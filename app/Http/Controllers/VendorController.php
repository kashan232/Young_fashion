<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorLedger;
use App\Models\VendorPayment;
use App\Models\VendorBilty;
use App\Models\Purchase;
use App\Models\Account;
use App\Models\VoucherMaster;
use App\Services\BalanceService;
use App\Services\VoucherService;

class VendorController extends Controller
{
    // Show all vendors
    public function index() {
        $vendors = Vendor::all();
        return view('admin_panel.vendors.index', compact('vendors'));
    }

    // Store or update vendor information
    public function store(Request $request)
    {
        if ($request->id) {
            // Update existing vendor (prevent balance update)
            Vendor::findOrFail($request->id)->update($request->except('opening_balance'));
        } else {
            // Create a new vendor and ledger entry
            $vendor = Vendor::create($request->all());

            // Create ledger entry
            VendorLedger::create([
                'vendor_id' => $vendor->id,
                'admin_or_user_id' => Auth::id(),
                'opening_balance' => $request->opening_balance ?? 0,
                'closing_balance' => $request->opening_balance ?? 0,
                'previous_balance' => $request->previous_balance ?? 0,
            ]);

            // ✅ Record Journal Entry for Opening Balance (Accounting)
            if ($request->opening_balance > 0) {
                try {
                    $balanceService = app(\App\Services\BalanceService::class);
                    $journalService = app(\App\Services\JournalEntryService::class);
                    $apId = $balanceService->getAccountsPayableId();

                    $journalService->recordEntry(
                        $vendor,
                        $apId,
                        0, // Debit
                        $request->opening_balance, // Credit (Liability)
                        "Opening Balance",
                        now()->format('Y-m-d'),
                        $vendor
                    );
                } catch (\Exception $e) {
                    \Log::error("Vendor Opening Balance Journal Error: " . $e->getMessage());
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'vendor' => $vendor ?? Vendor::find($request->id),
                'message' => 'Saved Successfully'
            ]);
        }

        return back()->with('success', 'Saved Successfully');
    }

    // Soft delete vendor and related ledger entry
    public function delete($id) {
    // Find the vendor by id, along with the related ledger entry using the 'ledger' relationship
    $vendor = Vendor::with('ledger')->findOrFail($id);

    // The vendor's ledger will be automatically deleted due to cascading delete
    $vendor->delete(); // Soft delete vendor

    return back()->with('success', 'Deleted Successfully');
}


    // Show vendor ledger
    public function vendors_ledger()
    {
        if (Auth::check()) {
            // Get all ledgers
            $VendorLedgers = VendorLedger::with('vendor')->get();
            
            // Recalculate balances from Journal Entries
            $balanceService = app(\App\Services\BalanceService::class);
            
            foreach ($VendorLedgers as $ledger) {
                // Calculate actual closing balance from journal entries
                // Note: BalanceService::getVendorBalance returns positive for Credit (Payable)
                $ledger->formatted_closing_balance = $balanceService->getVendorBalance($ledger->vendor_id);
            }
            
            return view('admin_panel.vendors.vendors_ledger', compact('VendorLedgers'));
        } else {
            return redirect()->back();
        }
    }

    // Show all vendor payments
    public function vendor_payments()
    {
        $payments = VendorPayment::with('vendor')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        $vendors = Vendor::all();
        $balanceService = app(BalanceService::class);

        foreach ($vendors as $v) {
            $v->live_balance = $balanceService->getVendorBalance($v->id);
        }

        // Available payment accounts (Cash, Bank)
        $accounts = Account::where('is_active', 1)->orWhereNull('is_active')->get();

        $totalPaidAmount = (float) $payments->sum('amount');
        $totalPaymentsCount = $payments->count();
        $totalPayables = 0;
        foreach ($vendors as $v) {
            if ($v->live_balance > 0) {
                $totalPayables += $v->live_balance;
            }
        }

        return view('admin_panel.vendors.vendor_payments', compact(
            'payments',
            'vendors',
            'accounts',
            'totalPaidAmount',
            'totalPaymentsCount',
            'totalPayables'
        ));
    }

    // Store vendor payment and update ledger
    public function store_vendor_payment(Request $request)
    {
        $request->validate([
            'vendor_id'       => 'required|exists:vendors,id',
            'payment_date'    => 'required|date',
            'amount'          => 'required|numeric|min:0.01',
            'payment_method'  => 'nullable|string',
            'account_id'      => 'nullable|exists:accounts,id',
            'note'            => 'nullable|string',
            'adjustment_type' => 'required|in:plus,minus',
        ]);

        $vendor = Vendor::findOrFail($request->vendor_id);
        $amount = (float) $request->amount;
        $isPayment = ($request->adjustment_type === 'minus');

        // 1. Save vendor payment record
        $payment = VendorPayment::create([
            'vendor_id'        => $vendor->id,
            'admin_or_user_id' => Auth::id(),
            'payment_date'     => $request->payment_date,
            'amount'           => $amount,
            'payment_method'   => $request->payment_method ?? 'Cash',
            'note'             => $request->note,
        ]);

        // 2. Update legacy vendor ledger
        $ledger = VendorLedger::where('vendor_id', $vendor->id)->first();
        if ($ledger) {
            $multiplier = $isPayment ? -1 : 1;
            $ledger->closing_balance += ($multiplier * $amount);
            $ledger->save();
        }

        // 3. Post to General Ledger via VoucherService
        try {
            $balanceService = app(BalanceService::class);
            $apId = $balanceService->getAccountsPayableId();
            $cashAccountId = $request->account_id ?: $balanceService->getCashAccountId();

            if ($isPayment) {
                // Payment to vendor (reduces vendor payable)
                $lines = [
                    [
                        'account_id' => $apId,
                        'debit'      => $amount,
                        'credit'     => 0,
                        'narration'  => $request->note ?: "Payment to vendor: {$vendor->name}",
                    ],
                    [
                        'account_id' => $cashAccountId,
                        'debit'      => 0,
                        'credit'     => $amount,
                        'narration'  => "Cash/Bank paid to {$vendor->name} (" . ($request->payment_method ?? 'Cash') . ")",
                    ]
                ];
            } else {
                // Refund from vendor
                $lines = [
                    [
                        'account_id' => $cashAccountId,
                        'debit'      => $amount,
                        'credit'     => 0,
                        'narration'  => "Refund received from {$vendor->name}",
                    ],
                    [
                        'account_id' => $apId,
                        'debit'      => 0,
                        'credit'     => $amount,
                        'narration'  => $request->note ?: "Adjustment from vendor: {$vendor->name}",
                    ]
                ];
            }

            app(VoucherService::class)->createVoucher([
                'voucher_type' => 'payment',
                'date'         => $request->payment_date,
                'status'       => VoucherMaster::STATUS_POSTED,
                'party_type'   => Vendor::class,
                'party_id'     => $vendor->id,
                'remarks'      => $request->note ?: ("Vendor Payment: {$vendor->name} [Payment #{$payment->id}]"),
            ], $lines, Auth::id());

        } catch (\Exception $e) {
            \Log::error('Vendor Payment Voucher sync error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Vendor payment of Rs. ' . number_format($amount, 2) . ' recorded successfully.');
    }

    // Delete vendor payment and revert ledger
    public function destroy_vendor_payment($id)
    {
        $payment = VendorPayment::findOrFail($id);
        $vendorId = $payment->vendor_id;
        $amount = (float) $payment->amount;

        $ledger = VendorLedger::where('vendor_id', $vendorId)->first();
        if ($ledger) {
            $ledger->closing_balance += $amount;
            $ledger->save();
        }

        $payment->delete();

        return redirect()->back()->with('success', 'Vendor payment deleted and ledger updated successfully.');
    }

    // Show all vendor bilties
    public function vendor_bilties()
    {
        $bilties = VendorBilty::with(['vendor', 'purchase'])->orderByDesc('id')->get();
        $vendors = Vendor::all();
        $purchases = Purchase::all();
        return view('admin_panel.vendors.vendor_bilties', compact('bilties', 'vendors', 'purchases'));
    }

    // Store vendor bilty information
    public function store_vendor_bilty(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_id' => 'nullable|exists:purchases,id',
            'bilty_no' => 'nullable|string',
            'vehicle_no' => 'nullable|string',
            'transporter_name' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        VendorBilty::create($request->all());

        return back()->with('success', 'Vendor bilty saved successfully.');
    }

    // Get vendor balance by vendor id
    public function getVendorBalance($id)
    {
        $vendor = Vendor::find($id);
        if (!$vendor) {
            return response()->json(['closing_balance' => 0, 'formatted' => 'Rs. 0.00', 'vendor_name' => '']);
        }

        $balanceService = app(BalanceService::class);
        $closingBalance = (float) $balanceService->getVendorBalance((int) $id);

        return response()->json([
            'closing_balance' => $closingBalance,
            'formatted'       => 'Rs. ' . number_format(abs($closingBalance), 2) . ($closingBalance >= 0 ? ' (Payable / Cr)' : ' (Advance / Dr)'),
            'vendor_name'     => $vendor->name,
            'phone'           => $vendor->phone ?? 'N/A',
            'address'         => $vendor->address ?? 'N/A',
        ]);
    }

    /**
     * Show vendor ledger (journal-based)
     */
    public function ledger($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        
        // Get date range from request or default to current month
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $balanceService = app(\App\Services\BalanceService::class);
        $ledgerData = $balanceService->getVendorLedger($vendorId, $startDate, $endDate);
        
        return view('admin_panel.vendors.ledger', $ledgerData);
    }
    /**
     * Get vendor ledger as JSON (AJAX)
     */
    public function getVendorLedgerJson($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $balanceService = app(\App\Services\BalanceService::class);
        
        // Default range: last 30 days
        $startDate = request('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));
        
        $ledgerData = $balanceService->getVendorLedger($vendorId, $startDate, $endDate);
        
        // Add current balance to response
        $ledgerData['current_balance'] = $balanceService->getVendorBalance($vendorId);
        
        return response()->json($ledgerData);
    }
}
