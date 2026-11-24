<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Inwardgatepass;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Stock;
use App\Models\Vendor;
use App\Models\VendorLedger;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /** Keep stocks table in sync for a (branch,warehouse,product) */
    /** Keep warehouse_stocks table in sync for a (warehouse,product) */
    private function upsertStocks(int $productId, float $qtyPiecesDelta, int $branchId, int $warehouseId): void
    {
        // We ignore $branchId as WarehouseStock is warehouse-specific
        $stock = \App\Models\WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        // Get Product Master Data for Box Calculation
        $product = Product::find($productId);
        $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;

        if ($stock) {
            // ✅ Add new pieces directly to existing balance to prevent precision/sync loss
            $stock->total_pieces += $qtyPiecesDelta;
            
            // Recalculate Boxes for display only
            $stock->quantity = $stock->total_pieces / $ppb;
            
            $stock->save();
        } else {
            \App\Models\WarehouseStock::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'total_pieces' => $qtyPiecesDelta,
                'quantity' => $qtyPiecesDelta / $ppb, // Initial Box Qty
                'price' => 0, // Should be fetched from Product or Purchase Item? 
            ]);
        }
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['branch', 'warehouse', 'vendor', 'items', 'returns']);

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status_purchase', $request->status);
        }

        // Apply Date Filters
        if ($request->filled('from_date')) {
            $query->whereDate('purchase_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('purchase_date', '<=', $request->to_date);
        }

        // Apply Mobile Number Filter
        if ($request->filled('mobile_no')) {
            $query->whereHas('vendor', function ($q) use ($request) {
                $q->where('mobile', 'like', "%{$request->mobile_no}%");
            });
        }

        // Apply Bill No (Invoice No / ID) Filter
        if ($request->filled('bill_no')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_no', 'like', "%{$request->bill_no}%")
                  ->orWhere('id', 'like', "%{$request->bill_no}%");
            });
        }

        // Apply M.Bill # (Note) Filter
        if ($request->filled('reference')) {
            $query->where('note', 'like', "%{$request->reference}%");
        }

        // Apply Vendor Filter
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Order By
        $orderBy = $request->input('order_by', 'purchase_date');
        if ($orderBy === 'invoice_no') {
            $query->orderBy('invoice_no', 'desc');
        } else {
            $query->orderBy('purchase_date', 'desc');
        }

        $Purchase = $query->get();
        
        // Calculate updated amounts after returns
        $Purchase->each(function ($purchase) {
            // Calculate total returned amount for this purchase
            $totalReturned = $purchase->returns->sum('net_amount');
            
            // Store calculated values as attributes
            $purchase->total_returned = $totalReturned;
            $purchase->updated_net_amount = max(0, $purchase->net_amount - $totalReturned);
            $purchase->updated_due_amount = max(0, $purchase->due_amount - $totalReturned);
            
            // Check if fully returned
            $purchase->is_fully_returned = $purchase->net_amount > 0 && $totalReturned >= $purchase->net_amount;
            $purchase->has_partial_return = $totalReturned > 0 && $totalReturned < $purchase->net_amount;
        });

        // If AJAX request, return only table rows partial
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin_panel.purchase.partials.purchase_table_body', compact('Purchase'))->render()
            ]);
        }

        // Load all vendors for filter dropdown
        $vendors = Vendor::orderBy('name')->get();

        return view('admin_panel.purchase.index', compact('Purchase', 'vendors'));
    }

    public function addBill($gatepassId)
    {
        // Fetch the gatepass along with its related items and products
        $gatepass = InwardGatepass::with('items.product')->findOrFail($gatepassId);

        // Pass the gatepass data to the view
        return view('admin_panel.inward.add_bill', compact('gatepass'));
    }

    public function add_purchase()
    {
        // $userId = Auth::id();
        $Purchase = Purchase::get();
        $Vendor = Vendor::get();
        $Warehouse = Warehouse::get();
        // Filter accounts to only show Cash (1) and Bank (2) heads to prevent logic errors
        $accounts = \App\Models\Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)->orderBy('title')->get();

        $lastInvoice = Purchase::latest('id')->value('invoice_no');
        $nextInvoice = $lastInvoice
            ? 'PUR-'.str_pad(((int) preg_replace('/[^0-9]/', '', $lastInvoice)) + 1, 3, '0', STR_PAD_LEFT)
            : 'PUR-001';

        // Categories & Brands for Quick Add & Variant generation
        $categories = \App\Models\Category::orderBy('name')->get();
        $brands = \App\Models\Brand::orderBy('name')->get();

        // Return new V2 view
        return view('admin_panel.purchase.add_purchase_v2', compact('Vendor', 'Warehouse', 'Purchase', 'accounts', 'nextInvoice', 'categories', 'brands'));
    }

    public function searchMasterProducts(Request $request)
    {
        $term = trim($request->get('q', ''));
        if (empty($term)) {
            return response()->json([]);
        }

        $products = Product::with(['category_relation', 'brand', 'unit'])
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('item_name', 'like', "%{$term}%")
                  ->orWhere('item_code', 'like', "%{$term}%")
                  ->orWhere('barcode_path', 'like', "%{$term}%")
                  ->orWhere('color', 'like', "%{$term}%");
            })
            ->limit(25)
            ->get();

        $results = $products->map(function ($p) {
            $variants = [];
            if ($p->color) {
                try {
                    $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                    if (is_array($parsed)) {
                        $variants = $parsed;
                    }
                } catch (\Exception $e) {}
            }

            return [
                'id' => $p->id,
                'item_name' => $p->item_name,
                'item_code' => $p->item_code,
                'barcode_path' => $p->barcode_path,
                'category_name' => $p->category_relation->name ?? '',
                'brand_name' => $p->brand->name ?? '',
                'unit_name' => $p->unit->name ?? 'Pcs',
                'purchase_price' => (float) ($p->purchase_price_per_piece ?? 0),
                'sale_price' => (float) ($p->sale_price_per_piece ?? 0),
                'variants_count' => count($variants),
                'variants' => $variants,
            ];
        });

        return response()->json($results);
    }

    public function getProductVariants($id)
    {
        $product = Product::with(['category_relation', 'brand', 'unit'])->findOrFail($id);

        $variants = [];
        if ($product->color) {
            try {
                $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                if (is_array($parsed)) {
                    $variants = $parsed;
                }
            } catch (\Exception $e) {}
        }

        // Calculate variant stock
        $purchasesList = DB::table('purchase_items as pi')
            ->join('purchases as pur', 'pur.id', '=', 'pi.purchase_id')
            ->where('pi.product_id', $product->id)
            ->whereIn('pur.status_purchase', ['approved', 'Returned', 'Partial'])
            ->select('pi.qty as total_pieces', 'pi.color')
            ->get();

        $purchaseReturnsList = DB::table('purchase_return_items as pri')
            ->where('pri.product_id', $product->id)
            ->select('pri.qty', 'pri.color')
            ->get();

        $salesList = DB::table('sale_items')
            ->where('product_id', $product->id)
            ->select('total_pieces', 'color')
            ->get();

        $webSalesList = DB::table('ecommerce_order_items as eoi')
            ->join('ecommerce_orders as eo', 'eo.id', '=', 'eoi.ecommerce_order_id')
            ->where('eoi.product_id', $product->id)
            ->where('eo.is_stock_deducted', 1)
            ->select('eoi.quantity as total_pieces', 'eoi.color', 'eoi.size')
            ->get();

        $salesListArray = $salesList->toArray();
        foreach ($webSalesList as $wItem) {
            $salesListArray[] = (object) [
                'total_pieces' => $wItem->total_pieces,
                'color' => json_encode([
                    'color' => $wItem->color ?: '-',
                    'size' => $wItem->size ?: '-'
                ])
            ];
        }
        $salesList = collect($salesListArray);

        $returnsList = DB::table('sale_return_items as sri')
            ->where('sri.product_id', $product->id)
            ->select('sri.qty', 'sri.color')
            ->get();

        $adjList = DB::table('stock_movements')
            ->where('product_id', $product->id)
            ->where('type', 'adjustment')
            ->whereNotIn('ref_type', ['INIT'])
            ->select('qty', 'note')
            ->get();

        foreach ($variants as &$v) {
            $purchased = 0;
            foreach ($purchasesList as $pItem) {
                if ($this->matchVariantHelper($pItem->color, $v)) {
                    $purchased += (float) $pItem->total_pieces;
                }
            }
            $pReturned = 0;
            foreach ($purchaseReturnsList as $prItem) {
                if ($this->matchVariantHelper($prItem->color, $v)) {
                    $pReturned += (float) $prItem->qty;
                }
            }
            $sold = 0;
            foreach ($salesList as $sItem) {
                if ($this->matchVariantHelper($sItem->color, $v)) {
                    $sold += (float) $sItem->total_pieces;
                }
            }
            $returnedQty = 0;
            foreach ($returnsList as $rItem) {
                if ($this->matchVariantHelper($rItem->color, $v)) {
                    $returnedQty += (float) $rItem->qty;
                }
            }
            $adjustments = 0;
            foreach ($adjList as $adjItem) {
                if ($this->matchAdjustmentHelper($adjItem->note, $v)) {
                    $adjustments += (float) $adjItem->qty;
                }
            }

            $initial = (float) ($v['stock'] ?? 0);
            $v['current_stock'] = max(0, $initial + $purchased - $pReturned - $sold + $returnedQty + $adjustments);
            if (!isset($v['purch_price']) || (float)$v['purch_price'] <= 0) {
                $v['purch_price'] = (float) ($product->purchase_price_per_piece ?? 0);
            }
            if (!isset($v['sale_price']) || (float)$v['sale_price'] <= 0) {
                $v['sale_price'] = (float) ($product->sale_price_per_piece ?? 0);
            }
        }
        unset($v);

        return response()->json([
            'product' => [
                'id' => $product->id,
                'item_name' => $product->item_name,
                'item_code' => $product->item_code,
                'category_name' => $product->category_relation->name ?? '',
                'brand_name' => $product->brand->name ?? '',
                'unit_name' => $product->unit->name ?? 'Pcs',
                'purchase_price' => (float) ($product->purchase_price_per_piece ?? 0),
                'sale_price' => (float) ($product->sale_price_per_piece ?? 0),
            ],
            'variants' => $variants
        ]);
    }

    private function matchVariantHelper($itemColor, $variant)
    {
        if (empty($itemColor)) return false;
        $decoded = base64_decode($itemColor, true);
        $vData = ($decoded !== false) ? json_decode($decoded, true) : null;
        if (empty($vData)) {
            $vData = json_decode($itemColor, true);
        }
        if (!is_array($vData)) {
            return strcasecmp(trim($itemColor), trim($variant['color'] ?? '')) === 0;
        }

        $vColor = strtolower(trim($variant['color'] ?? '-'));
        $vSize = strtolower(trim($variant['size'] ?? '-'));

        $itemColorVal = strtolower(trim($vData['color'] ?? ($vData['color_val'] ?? '-')));
        $itemSizeVal = strtolower(trim($vData['size'] ?? ($vData['size_val'] ?? '-')));

        $colorMatches = ($vColor === $itemColorVal) || ($vColor === '-') || ($itemColorVal === '-');
        $sizeMatches = ($vSize === $itemSizeVal) || ($vSize === '-') || ($itemSizeVal === '-');

        if ($vColor !== '-' && $itemColorVal !== '-') {
            return $colorMatches && $sizeMatches;
        }
        return $sizeMatches;
    }

    private function matchAdjustmentHelper($note, $variant)
    {
        if (empty($note)) return false;
        $size = trim($variant['size'] ?? '');
        $color = trim($variant['color'] ?? '');
        if ($size && $size !== '-') {
            if (stripos($note, "Size: {$size}") === false && stripos($note, "Size:{$size}") === false) {
                return false;
            }
        }
        if ($color && $color !== '-') {
            if (stripos($note, "Color: {$color}") === false && stripos($note, "Color:{$color}") === false) {
                return false;
            }
        }
        return true;
    }
    public function quickCreate()
    {
        $Vendor = Vendor::get();
        $Warehouse = Warehouse::get();
        $accounts = \App\Models\Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)->orderBy('title')->get();
        
        $categories = \App\Models\Category::get();

        $balanceService = app(\App\Services\BalanceService::class);
        $apId = $balanceService->getAccountsPayableId();
        $vendorBalances = \App\Models\JournalEntry::where('party_type', \App\Models\Vendor::class)
            ->where('account_id', $apId)
            ->selectRaw('party_id, COALESCE(SUM(credit) - SUM(debit), 0) as balance')
            ->groupBy('party_id')
            ->pluck('balance', 'party_id')
            ->toArray();

        return view('admin_panel.purchase.quick_create', compact('Vendor', 'Warehouse', 'accounts', 'categories', 'vendorBalances'));
    }

    public function quickSearchProducts(Request $request)
    {
        $term = trim($request->get('q', ''));
        if (empty($term)) {
            return response()->json([]);
        }

        $products = Product::with(['category_relation', 'sub_category_relation'])
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('item_name', 'like', "%{$term}%")
                  ->orWhere('item_code', 'like', "%{$term}%")
                  ->orWhere('barcode_path', 'like', "%{$term}%");
            })
            ->limit(20)
            ->get();

        $results = $products->map(function ($p) use ($term) {
            $variants = [];
            if ($p->color) {
                try {
                    $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                    if (is_array($parsed)) {
                        $variants = $parsed;
                    }
                } catch (\Exception $e) {}
            }

            $isExactMatch = strtolower(trim($p->item_name)) === strtolower($term) || strtolower(trim($p->item_code)) === strtolower($term);

            return [
                'id' => $p->id,
                'item_code' => $p->item_code,
                'item_name' => $p->item_name,
                'category_id' => $p->category_id,
                'category_name' => $p->category_relation->name ?? '',
                'sub_category_id' => $p->sub_category_id,
                'sub_category_name' => $p->sub_category_relation->name ?? '',
                'purchase_price' => (float) ($p->purchase_price_per_piece ?? 0),
                'sale_price' => (float) ($p->sale_price_per_piece ?? 0),
                'variants' => $variants,
                'is_exact' => $isExactMatch,
            ];
        });

        return response()->json($results);
    }

    public function quickStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'vendor_id' => 'nullable',
                'new_vendor_name' => 'nullable|string',
                'opening_balance' => 'nullable|numeric',
                'product_id' => 'nullable|exists:products,id',
                'category_id' => 'required',
                'sub_category_id' => 'nullable',
                'base_product_name' => 'required|string',
                'payment_account_id' => 'nullable|exists:accounts,id',
                'payment_amount' => 'nullable|numeric|min:0',
                
                'variant_size' => 'required|array|min:1',
                'variant_color' => 'required|array|min:1',
                'variant_barcode' => 'nullable|array',
                'qty' => 'required|array|min:1',
                'qty.*' => 'required|numeric|min:0',
                'purchase_price' => 'required|array',
                'purchase_price.*' => 'required|numeric|min:0',
                'sale_price' => 'required|array',
                'sale_price.*' => 'required|numeric|min:0',
            ]);

            $hasPositiveQty = false;
            foreach ($validated['qty'] as $q) {
                if ((float)$q > 0) {
                    $hasPositiveQty = true;
                    break;
                }
            }

            if (!$hasPositiveQty) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please enter a quantity greater than 0 for at least one variant.',
                        'errors' => ['qty' => ['At least one variant must have quantity greater than 0.']]
                    ], 422);
                }
                return back()->withErrors(['qty' => 'At least one variant must have quantity greater than 0.'])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors(), 'message' => 'Validation Error'], 422);
            }
            throw $e;
        }

        $purchase = DB::transaction(function () use ($validated, $request) {
            // 1. Resolve Vendor
            $vendorId = $validated['vendor_id'] ?? null;
            if (!$vendorId && !empty($validated['new_vendor_name'])) {
                $vendor = Vendor::create([
                    'name' => $validated['new_vendor_name'],
                    'status' => 1
                ]);
                $vendorId = $vendor->id;

                if (!empty($validated['opening_balance']) && $validated['opening_balance'] != 0) {
                    \App\Models\VendorLedger::create([
                        'vendor_id' => $vendorId,
                        'admin_or_user_id' => auth()->id() ?? 1,
                        'previous_balance' => 0,
                        'opening_balance' => 0,
                        'closing_balance' => (float)$validated['opening_balance']
                    ]);
                }
            }

            // 2. Create Header
            $lastInvoice = Purchase::latest('id')->value('invoice_no');
            $nextInvoice = $lastInvoice
                ? 'PUR-'.str_pad(((int) preg_replace('/[^0-9]/', '', $lastInvoice)) + 1, 3, '0', STR_PAD_LEFT)
                : 'PUR-001';

            $purchase = Purchase::create([
                'branch_id' => 1,
                'warehouse_id' => 1, // Default warehouse
                'vendor_id' => $vendorId,
                'purchase_date' => now(),
                'invoice_no' => $nextInvoice,
                'note' => 'Quick Purchase: ' . $validated['base_product_name'],
                'subtotal' => 0,
                'discount' => 0,
                'extra_cost' => 0,
                'net_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'status_purchase' => 'approved',
            ]);

            $subtotal = 0;
            $baseName = $validated['base_product_name'];
            $sizes = $request->variant_size;
            $colors = $request->variant_color;
            $barcodes = $request->variant_barcode ?? [];
            $qtys = $validated['qty'];
            $pPrices = $validated['purchase_price'];
            $sPrices = $validated['sale_price'];

            // 3. Build Catalog Variants (All defined variants in table to persist to product catalog)
            $catalogVariants = [];
            foreach ($sizes as $i => $sizeStr) {
                $sizeVal = trim($sizeStr ?? '');
                $colorVal = trim($colors[$i] ?? '');
                if ($sizeVal === '' && $colorVal === '') continue;

                $barcodeVal = trim($barcodes[$i] ?? '');
                if (empty($barcodeVal)) {
                    $barcodeVal = (string) rand(100000, 999999);
                }

                $catalogVariants[] = [
                    'name' => $baseName, // Ensure name is set for POS display
                    'size' => $sizeVal,
                    'color' => $colorVal,
                    'sale_price' => (float) ($sPrices[$i] ?? 0),
                    'wholesale_price' => (float) ($sPrices[$i] ?? 0),
                    'purch_price' => (float) ($pPrices[$i] ?? 0),
                    'weight_per_piece' => 0,
                    'stock' => 0,
                    'barcode' => $barcodeVal,
                    'conv_factor' => 1,
                    'is_base_variant' => ($i === 0 ? 1 : 0),
                    'unit' => 'Pcs',
                ];
            }

            // 3. Resolve Product (Existing vs New)
            if (!empty($validated['product_id'])) {
                $product = Product::findOrFail($validated['product_id']);

                // Merge variants into existing product
                $existingVariants = [];
                if ($product->color) {
                    $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                    if (is_array($parsed)) {
                        $existingVariants = $parsed;
                    }
                }

                foreach ($catalogVariants as $inVar) {
                    $matched = false;
                    foreach ($existingVariants as &$exVar) {
                        $exSize = $exVar['size'] ?? '';
                        $exColor = $exVar['color'] ?? '';
                        if (strcasecmp(trim($exSize), trim($inVar['size'])) === 0 && strcasecmp(trim($exColor), trim($inVar['color'])) === 0) {
                            $exVar['sale_price'] = $inVar['sale_price'];
                            $exVar['purch_price'] = $inVar['purch_price'];
                            if (!empty($inVar['barcode'])) {
                                $exVar['barcode'] = $inVar['barcode'];
                            }
                            $matched = true;
                            break;
                        }
                    }
                    unset($exVar);

                    if (!$matched) {
                        $existingVariants[] = $inVar;
                    }
                }

                $product->color = json_encode(array_values($existingVariants));
                if (!empty($validated['category_id'])) {
                    $product->category_id = $validated['category_id'];
                }
                if (!empty($validated['sub_category_id'])) {
                    $product->sub_category_id = $validated['sub_category_id'];
                }
                $product->save();
            } else {
                // Create ONE master Product
                $lastProduct = Product::orderBy('id', 'desc')->first();
                $nextCode = $lastProduct ? ('ITEM-'.str_pad($lastProduct->id + 1, 4, '0', STR_PAD_LEFT)) : 'ITEM-0001';
                $masterBarcode = (!empty($catalogVariants) && !empty($catalogVariants[0]['barcode']))
                    ? $catalogVariants[0]['barcode']
                    : (string) rand(100000000000, 999999999999);

                $product = Product::create([
                    'item_name' => $baseName,
                    'category_id' => $validated['category_id'] ?? null,
                    'sub_category_id' => $validated['sub_category_id'] ?? null,
                    'color' => json_encode($catalogVariants), // Store all defined variants in catalog!
                    'item_code' => $nextCode,
                    'barcode_path' => $masterBarcode,
                    'purchase_price_per_piece' => (float) ($pPrices[0] ?? 0),
                    'sale_price_per_piece' => (float) ($sPrices[0] ?? 0),
                    'size_mode' => 'pieces',
                    'total_m2' => 0,
                    'is_active' => 1
                ]);
            }

            // 4. Create Purchase Items linking to the single master Product
            foreach ($sizes as $i => $sizeStr) {
                $qty = (float) $qtys[$i];
                $pPrice = (float) $pPrices[$i];
                if ($qty <= 0) continue;

                $colorStr = $colors[$i];
                $barcodeStr = $barcodes[$i] ?? ($catalogVariants[$i]['barcode'] ?? '');
                $lineTotal = $qty * $pPrice;

                // Create Purchase Item
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id, // All point to the same master product
                    'price' => $pPrice,
                    'qty' => $qty,
                    'line_total' => $lineTotal,
                    // Store variant info in color field for the PurchaseItem as JSON
                    'color' => json_encode([
                        'size' => $sizeStr,
                        'color' => $colorStr,
                        'barcode' => $barcodeStr
                    ]), 
                    'size_mode' => 'pieces',
                    'pieces_per_box' => 1,
                ]);

                $subtotal += $lineTotal;
            }

            $purchase->update([
                'subtotal' => $subtotal,
                'net_amount' => $subtotal,
                'due_amount' => $subtotal,
            ]);

            // 4. Approve (Stock + Ledgers)
            $purchase->load('items');
            $this->approvePurchase($purchase);

            // 5. Payment
            if (!empty($validated['payment_account_id']) && $validated['payment_amount'] > 0) {
                try {
                    $transactionService = app(\App\Services\TransactionService::class);
                    $transactionService->createPaymentForPurchase(
                        $purchase,
                        [$validated['payment_account_id']],
                        [$validated['payment_amount']]
                    );
                } catch (\Exception $e) {}
            }

            return $purchase;
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Quick Purchase saved successfully!',
                'redirect_url' => route('Purchase.home'),
            ]);
        }

        return redirect()->route('Purchase.home')->with('success', 'Quick Purchase saved successfully!');
    }

    private function approvePurchase(Purchase $purchase)
    {
        // 1. Stock Movements & Warehouse Stock
        // We need to re-iterate items because we need product_id and qty
        // But the previous logic used $validated arrays which might process duplicates or specific logic.
        // However, since the PurchaseItems are already saved in DB for 'draft' or 'new',
        // we should rely on the SAVED items for approval to ensure consistency.

        $branchId = $purchase->branch_id;
        $warehouseId = $purchase->warehouse_id;

        // Check for Gatepass link (if linked, no stock movement needed usually, logic from store method)
        $hasGatepass = \App\Models\InwardGatepass::where('purchase_id', $purchase->id)->exists();

        if (! $hasGatepass) {
            $movRows = [];
            foreach ($purchase->items as $item) {
                // Determine conversion factor and unit for weight products
                $convFactor = 1;
                $unit = strtolower($item->unit ?? '');
                if (!empty($item->color)) {
                    $itemColor = $item->color;
                    $b64Decoded = base64_decode($itemColor, true);
                    $json = $b64Decoded !== false ? json_decode($b64Decoded, true) : null;
                    if (!is_array($json)) {
                        $json = json_decode($itemColor, true);
                    }
                    if (is_array($json)) {
                        if (isset($json['conv_factor']) && (float)$json['conv_factor'] > 0) {
                            $convFactor = (float) $json['conv_factor'];
                        }
                        if (isset($json['unit'])) {
                            $unit = strtolower($json['unit']);
                        }
                    }
                }

                if ($unit === 'gm' || $unit === 'g' || $unit === 'gram' || $unit === 'grams') {
                    $baseQty = ((float) $item->qty) / 1000.0;
                } else {
                    $baseQty = ((float) $item->qty) * $convFactor;
                }

                // movements (+)
                $movRows[] = [
                    'product_id' => $item->product_id,
                    'type' => 'in',
                    'qty' => $baseQty,
                    'ref_type' => 'PURCHASE',
                    'ref_id' => $purchase->id,
                    'note' => 'Purchase Confirmed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // stocks
                $this->upsertStocks($item->product_id, +$baseQty, $branchId, $warehouseId);
            }

            if (! empty($movRows)) {
                DB::table('stock_movements')->insert($movRows);
            }
        }

        // 2. Vendor Ledger
        $netAmount = $purchase->net_amount;
        $prevClosing = \App\Models\VendorLedger::where('vendor_id', $purchase->vendor_id)
            ->value('closing_balance') ?? 0;

        \App\Models\VendorLedger::updateOrCreate(
            ['vendor_id' => $purchase->vendor_id],
            [
                'vendor_id' => $purchase->vendor_id,
                'admin_or_user_id' => auth()->id(),
                'previous_balance' => $prevClosing,
                'opening_balance' => $prevClosing,
                'closing_balance' => $prevClosing + $netAmount,
            ]
        );

        // 3. Accounting
        try {
            $transactionService = app(\App\Services\TransactionService::class);

            // A. Create Purchase Voucher
            $transactionService->createPurchaseVoucher($purchase);

            // B. Record Payment (This part is tricky if payments were passed solely in Request)
            // If payments were saved in a temp table or if we re-collect them, good.
            // BUT: distinct feature request "Confirm Purchase" usually implies later approval.
            // If payments were part of the initial 'store' request, they are lost if we didn't save them.
            // The user said "confirm purchase... don't create vouchers... just save...".
            // So if 'Draft', we did NOT run accounting. The payment inputs were ignored?
            // If we want to support payments on Confirm, we would need to store them or ask again.
            // For now, we will Assume no immediate payments on 'Draft -> Confirm' via separate button,
            // UNLESS we are in the immediate 'store' flow where Request data is available.

            // To handle both cases (immediate approve vs later approve), we can pass optional payment data.
            // But strict signature: approvePurchase(Purchase $purchase, array $paymentData = [])

        } catch (\Exception $e) {
            \Log::error('Purchase Accounting Error: '.$e->getMessage());
        }
    }

    public function confirm($id)
    {
        DB::transaction(function () use ($id) {
            $purchase = Purchase::with('items')->findOrFail($id);
            $oldNetAmount = $purchase->net_amount;

            if ($purchase->status_purchase !== 'draft') {
                return; // already approved or invalid state
            }

            // Run approval logic
            $this->approvePurchase($purchase);

            // Update status
            $purchase->update(['status_purchase' => 'approved']);
        });

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Purchase confirmed successfully.',
                'invoice_url' => route('purchase.invoice', $id),
                'redirect_url' => route('Purchase.home'),
            ]);
        }

        return redirect()->back()->with('success', 'Purchase confirmed successfully.');
    }

    public function bulkAdditionalDiscount(Request $request)
    {
        $request->validate([
            'purchase_ids' => 'required|array',
            'purchase_ids.*' => 'exists:purchases,id',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $purchaseIds = $request->purchase_ids;
        $percentage = (float)$request->discount_percentage;

        try {
            DB::transaction(function () use ($purchaseIds, $percentage) {
                foreach ($purchaseIds as $id) {
                    $purchase = Purchase::findOrFail($id);
                    
                    // Original net amount is the current net_amount + current additional_discount
                    $originalNet = (float)$purchase->net_amount + (float)$purchase->additional_discount;
                    
                    // Calculate absolute discount based on user-supplied percentage
                    $newAdditionalDiscount = round($originalNet * ($percentage / 100), 2);
                    
                    $oldAdditionalDiscount = (float)$purchase->additional_discount;
                    $diff = $newAdditionalDiscount - $oldAdditionalDiscount;

                    if ($diff != 0) {
                        // Update purchase record
                        $purchase->additional_discount = $newAdditionalDiscount;
                        $purchase->net_amount = max(0, $purchase->net_amount - $diff);
                        $purchase->due_amount = max(0, $purchase->due_amount - $diff);
                        $purchase->save();

                        // If approved, update ledger & journal entries
                        if ($purchase->status_purchase === 'approved') {
                            // 1. Adjust legacy VendorLedger closing balance
                            $vendorLedger = \App\Models\VendorLedger::where('vendor_id', $purchase->vendor_id)->first();
                            if ($vendorLedger) {
                                $vendorLedger->closing_balance -= $diff;
                                $vendorLedger->save();
                            }

                            // 2. Adjust Journal Vouchers
                            $voucher = \App\Models\VoucherMaster::where('remarks', "Purchase Voucher #{$purchase->invoice_no}")->first();
                            if ($voucher) {
                                $voucher->total_amount = max(0, $voucher->total_amount - $diff);
                                $voucher->save();

                                $balanceService = app(\App\Services\BalanceService::class);
                                $expenseAccountId = $balanceService->getPurchaseExpenseId();
                                $apAccountId = $balanceService->getAccountsPayableId();

                                // Update debit for expense
                                \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                                    ->where('source_id', $voucher->id)
                                    ->where('account_id', $expenseAccountId)
                                    ->update(['debit' => $purchase->net_amount]);

                                // Update credit for accounts payable
                                \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                                    ->where('source_id', $voucher->id)
                                    ->where('account_id', $apAccountId)
                                    ->update(['credit' => $purchase->net_amount]);
                            }
                        }
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Additional discount percentage applied successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $gatepassId = null)
    {
        // (A) Gatepass fetch if provided
        $gatepass = null;
        if ($gatepassId) {
            $gatepass = \App\Models\InwardGatepass::with('purchase')->findOrFail($gatepassId);
            if ($gatepass->purchase) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Gatepass already has a bill.'], 422);
                }

                return back()->with('error', 'This gatepass already has an associated bill.');
            }
        }

        // (B) Validation
        try {
            $validated = $request->validate([
                'invoice_no' => 'nullable|string',
                'vendor_id' => 'required|exists:vendors,id',
                'purchase_date' => 'nullable|date',
                'branch_id' => 'nullable|exists:branches,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'note' => 'nullable|string',
                'vendor_inv_photo' => 'nullable|file|image|max:15360',
                'discount' => 'nullable|numeric|min:0',
                'extra_cost' => 'nullable|numeric|min:0',
                'product_id' => 'nullable|array',
                'product_id.*' => 'nullable',
                'qty' => 'nullable|array',
                'qty.*' => 'nullable|numeric',
                'price' => 'nullable|array',
                'price.*' => 'nullable|numeric',
                'price_per_carton' => 'nullable|array',
                'price_per_carton.*' => 'nullable|numeric|min:0',
                'unit' => 'nullable|array',
                'unit.*' => 'nullable|string',
                'item_discount' => 'nullable|array',
                'item_discount.*' => 'nullable|numeric|min:0',
                'mfg_date' => 'nullable|array',
                'mfg_date.*' => 'nullable|date',
                'exp_date' => 'nullable|array',
                'exp_date.*' => 'nullable|date',
                'description' => 'nullable|array',
                'description.*' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'active' => false, 'errors' => $e->errors(), 'message' => 'Validation Error'], 422);
            }
            throw $e;
        }

        // Upload vendor invoice photo if provided
        $vendorInvPhotoPath = null;
        if ($request->hasFile('vendor_inv_photo')) {
            $photoFile = $request->file('vendor_inv_photo');
            if ($photoFile->isValid()) {
                $ext = $photoFile->getClientOriginalExtension() ?: 'jpg';
                $photoName = 'vendor_inv_' . time() . '_' . uniqid() . '.' . $ext;
                $photoFile->move(public_path('uploads/purchases'), $photoName);
                $vendorInvPhotoPath = 'uploads/purchases/' . $photoName;
            }
        }

        // Wrap in transaction... to allow returning $purchase outside
        $purchase = DB::transaction(function () use ($validated, $request, $gatepass, $vendorInvPhotoPath) {

            // invoice number
            $lastInvoice = Purchase::latest('id')->value('invoice_no');
            $nextInvoice = $lastInvoice
                ? 'PUR-'.str_pad(((int) preg_replace('/[^0-9]/', '', $lastInvoice)) + 1, 3, '0', STR_PAD_LEFT)
                : 'PUR-001';

            $branchId = (int) ($validated['branch_id'] ?? 1);
            $warehouseId = (int) $validated['warehouse_id'];

            // Status Logic
            $status = ($request->action === 'save_only') ? 'draft' : 'approved';

            // create header
            $purchase = Purchase::create([
                'branch_id' => $branchId,
                'warehouse_id' => $warehouseId,
                'vendor_id' => $validated['vendor_id'] ?? null,
                'purchase_date' => $validated['purchase_date'] ?? now(),
                'invoice_no' => $validated['invoice_no'] ?? $nextInvoice,
                'note' => $validated['note'] ?? null,
                'vendor_inv_photo' => $vendorInvPhotoPath,
                'subtotal' => 0,
                'discount' => 0,
                'extra_cost' => 0,
                'net_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'status_purchase' => $status,
            ]);

            $subtotal = 0;
            $pids = $request->product_id ?? [];
            $qtys = $request->qty ?? [];
            $prices = $request->price ?? [];
            $cartonPrices = $request->price_per_carton ?? [];
            $units = $request->unit ?? [];
            $itemDiscs = $request->item_discount ?? [];

            // Multi-product & Variant fields
            $isNews = $request->is_new ?? [];
            $newProductNames = $request->new_product_name ?? [];
            $newCategoryIds = $request->new_category_id ?? [];
            $newBrandIds = $request->new_brand_id ?? [];
            $variantsJsons = $request->product_variants_json ?? [];

            // Snapshot fields
            $sizeModes = $request->size_mode ?? [];
            $ppbs = $request->pieces_per_box ?? [];
            $ppm2 = $request->pieces_per_m2 ?? [];
            $boxesQtys = $request->boxes_qty ?? [];
            $looseQtys = $request->loose_qty ?? [];
            $lengths = $request->length ?? [];
            $widths = $request->width ?? [];

            foreach ($pids as $i => $pidRaw) {
                $isNew = !empty($isNews[$i]) && ($isNews[$i] == '1' || $isNews[$i] === true);
                $variantsJson = $variantsJsons[$i] ?? null;
                $variantsList = [];
                if (!empty($variantsJson)) {
                    $decoded = is_string($variantsJson) ? json_decode($variantsJson, true) : $variantsJson;
                    if (is_array($decoded)) {
                        $variantsList = $decoded;
                    }
                }

                $product = null;

                // 1. Resolve Product (New vs Existing)
                if ($isNew) {
                    $prodName = trim($newProductNames[$i] ?? '');
                    if (empty($prodName)) continue;

                    $lastProduct = Product::orderBy('id', 'desc')->first();
                    $nextCode = $lastProduct ? ('ITEM-'.str_pad($lastProduct->id + 1, 4, '0', STR_PAD_LEFT)) : 'ITEM-0001';
                    $firstBarcode = !empty($variantsList[0]['barcode']) ? $variantsList[0]['barcode'] : (string) rand(100000000000, 999999999999);

                    $catalogVariants = [];
                    foreach ($variantsList as $vIdx => $v) {
                        $catalogVariants[] = [
                            'name' => $prodName,
                            'size' => $v['size'] ?? '-',
                            'color' => $v['color'] ?? '-',
                            'sale_price' => (float) ($v['sale_price'] ?? 0),
                            'wholesale_price' => (float) ($v['sale_price'] ?? 0),
                            'purch_price' => (float) ($v['purchase_price'] ?? 0),
                            'weight_per_piece' => 0,
                            'stock' => 0,
                            'barcode' => !empty($v['barcode']) ? $v['barcode'] : (string) rand(100000, 999999),
                            'conv_factor' => 1,
                            'is_base_variant' => ($vIdx === 0 ? 1 : 0),
                            'unit' => 'Pcs',
                        ];
                    }

                    $pPricePiece = (float) ($prices[$i] ?? ($variantsList[0]['purchase_price'] ?? 0));
                    $sPricePiece = (float) ($variantsList[0]['sale_price'] ?? 0);

                    $product = Product::create([
                        'item_name' => $prodName,
                        'category_id' => !empty($newCategoryIds[$i]) ? $newCategoryIds[$i] : null,
                        'brand_id' => !empty($newBrandIds[$i]) ? $newBrandIds[$i] : null,
                        'color' => json_encode($catalogVariants),
                        'item_code' => $nextCode,
                        'barcode_path' => $firstBarcode,
                        'purchase_price_per_piece' => $pPricePiece,
                        'sale_price_per_piece' => $sPricePiece,
                        'size_mode' => 'pieces',
                        'pieces_per_box' => 1,
                        'total_m2' => 0,
                        'is_active' => 1,
                    ]);
                } else {
                    $pid = (int) ($pidRaw ?? 0);
                    if (!$pid) continue;
                    $product = Product::find($pid);
                    if (!$product) continue;
                }

                // 2. If Variants Details are Configured in Modal
                if (!empty($variantsList) && is_array($variantsList)) {
                    // If existing product, update/merge variants in product catalog
                    if (!$isNew) {
                        $existingVariants = [];
                        if ($product->color) {
                            $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                            if (is_array($parsed)) $existingVariants = $parsed;
                        }
                        foreach ($variantsList as $inVar) {
                            $matched = false;
                            foreach ($existingVariants as &$exVar) {
                                $exSize = $exVar['size'] ?? '';
                                $exColor = $exVar['color'] ?? '';
                                if (strcasecmp(trim($exSize), trim($inVar['size'] ?? '-')) === 0 && strcasecmp(trim($exColor), trim($inVar['color'] ?? '-')) === 0) {
                                    if (isset($inVar['sale_price']) && (float)$inVar['sale_price'] > 0) {
                                        $exVar['sale_price'] = (float)$inVar['sale_price'];
                                    }
                                    if (isset($inVar['purchase_price']) && (float)$inVar['purchase_price'] > 0) {
                                        $exVar['purch_price'] = (float)$inVar['purchase_price'];
                                    }
                                    if (!empty($inVar['barcode']) && empty($exVar['barcode'])) {
                                        $exVar['barcode'] = $inVar['barcode'];
                                    }
                                    $matched = true;
                                    break;
                                }
                            }
                            unset($exVar);

                            if (!$matched) {
                                $existingVariants[] = [
                                    'name' => $product->item_name,
                                    'size' => $inVar['size'] ?? '-',
                                    'color' => $inVar['color'] ?? '-',
                                    'sale_price' => (float) ($inVar['sale_price'] ?? 0),
                                    'wholesale_price' => (float) ($inVar['sale_price'] ?? 0),
                                    'purch_price' => (float) ($inVar['purchase_price'] ?? 0),
                                    'weight_per_piece' => 0,
                                    'stock' => 0,
                                    'barcode' => !empty($inVar['barcode']) ? $inVar['barcode'] : (string) rand(100000, 999999),
                                    'conv_factor' => 1,
                                    'is_base_variant' => 0,
                                    'unit' => 'Pcs',
                                ];
                            }
                        }
                        $product->color = json_encode(array_values($existingVariants));
                        $product->save();
                    }

                    // Create PurchaseItem for each variant with Qty > 0
                    $discPercent = (float) ($itemDiscs[$i] ?? 0);
                    $hasPurchasedVariant = false;

                    foreach ($variantsList as $vItem) {
                        $vQty = (float) ($vItem['qty'] ?? 0);
                        if ($vQty <= 0) continue;

                        $hasPurchasedVariant = true;
                        $vPrice = (float) ($vItem['purchase_price'] ?? ($prices[$i] ?? 0));
                        $vGross = $vQty * $vPrice;
                        $vDiscAmt = $vGross * ($discPercent / 100);
                        $vLineTotal = $vGross - $vDiscAmt;

                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product->id,
                            'unit' => 'Pcs',
                            'price' => $vPrice,
                            'item_discount' => $vDiscAmt,
                            'qty' => $vQty,
                            'line_total' => $vLineTotal,
                            'color' => json_encode([
                                'size' => $vItem['size'] ?? '-',
                                'color' => $vItem['color'] ?? '-',
                                'barcode' => $vItem['barcode'] ?? '',
                                'sale_price' => (float) ($vItem['sale_price'] ?? 0),
                            ]),
                            'size_mode' => 'pieces',
                            'pieces_per_box' => 1,
                            'description' => $request->description[$i] ?? null,
                        ]);

                        $subtotal += $vLineTotal;
                    }

                    if ($hasPurchasedVariant) {
                        continue; // Done with this product row
                    }
                }

                // 3. Fallback: Standard Single Line Purchase Item
                $qty = (float) ($qtys[$i] ?? 0);
                $price = (float) ($prices[$i] ?? 0);
                if ($qty <= 0 || $price < 0) {
                    continue;
                }

                $discPercent = (float) ($itemDiscs[$i] ?? 0);
                $unit = $units[$i] ?? null;

                $curSizeMode = $sizeModes[$i] ?? null;
                $curPPM2 = (float) ($ppm2[$i] ?? 0);

                if ($curSizeMode === 'by_size') {
                    $grossTotal = $curPPM2 * $qty * $price;
                } else {
                    $grossTotal = $qty * $price;
                }

                $discAmount = $grossTotal * ($discPercent / 100);
                $lineTotal = $grossTotal - $discAmount;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'unit' => $unit,
                    'price' => $price,
                    'item_discount' => $discAmount,
                    'qty' => $qty,
                    'line_total' => $lineTotal,
                    'color' => $request->color[$i] ?? null,
                    'size_mode' => $sizeModes[$i] ?? null,
                    'pieces_per_box' => $ppbs[$i] ?? 1,
                    'pieces_per_m2' => $ppm2[$i] ?? 0,
                    'boxes_qty' => $boxesQtys[$i] ?? 0,
                    'loose_qty' => $looseQtys[$i] ?? 0,
                    'length' => $lengths[$i] ?? null,
                    'width' => $widths[$i] ?? null,
                    'mfg_date' => !empty($request->mfg_date[$i]) ? $request->mfg_date[$i] : null,
                    'exp_date' => !empty($request->exp_date[$i]) ? $request->exp_date[$i] : null,
                    'description' => $request->description[$i] ?? null,
                ]);

                $subtotal += $lineTotal;
            }

            // totals
            $discount = (float) ($request->discount ?? 0);
            $extraCost = (float) ($request->extra_cost ?? 0);
            $netAmount = ($subtotal - $discount) + $extraCost;

            $purchase->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'additional_discount' => $discount,
                'extra_cost' => $extraCost,
                'net_amount' => $netAmount,
                'due_amount' => $netAmount,
            ]);

            // If NOT draft, run full approval
            if ($status === 'approved') {
                $purchase->load('items'); // Load items for approval logic logic

                $this->approvePurchase($purchase); // Basic Stock + Ledger + Voucher

                // B. Record Payment (Only available in immediate Request)
                try {
                    $transactionService = app(\App\Services\TransactionService::class);
                    $paymentAccountIds = $request->input('payment_account_id', []);
                    $paymentAmounts = $request->input('payment_amount', []);

                    if (! empty(array_filter($paymentAccountIds))) {
                        $transactionService->createPaymentForPurchase(
                            $purchase,
                            $paymentAccountIds,
                            $paymentAmounts
                        );
                    }
                } catch (\Exception $e) { /* Logged already */
                }
            }

            // link gatepass -> purchase (and keep status)
            if ($gatepass) {
                $gatepass->purchase_id = $purchase->id;
                $gatepass->status = 'linked';
                $gatepass->save();
            }

            return $purchase;
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Purchase saved successfully.',
                'invoice_url' => route('purchase.invoice', $purchase->id),
                'redirect_url' => route('Purchase.home'),
            ]);
        }

        return redirect()->route('Purchase.home')->with('success', 'Purchase saved successfully.');
    }

    // public function store(Request $request)
    // {
    //     // ✅ Validation
    //     $validated = $request->validate([
    //         'invoice_no'     => 'nullable|string',
    //         'vendor_id'      => 'nullable|exists:vendors,id',
    //         'purchase_date'  => 'nullable|date',
    //         'warehouse_id'   => 'nullable|exists:warehouses,id',
    //         'note'           => 'nullable|string',
    //         'discount'       => 'nullable|numeric|min:0',
    //         'extra_cost'     => 'nullable|numeric|min:0',

    //         // Purchase Items
    //         'product_id'       => 'nullable|array',
    //         'product_id.*'     => 'nullable|exists:products,id',
    //         'qty'              => 'nullable|array',
    //         'qty.*'            => 'nullable|numeric|min:1',
    //         'price'            => 'nullable|array',
    //         'price.*'          => 'nullable|numeric|min:0',
    //         'unit'             => 'nullable|array',
    //         'unit.*'           => 'nullable|string',
    //         'item_discount'    => 'nullable|array',
    //         'item_discount.*'  => 'nullable|numeric|min:0',
    //     ]);

    //     DB::transaction(function () use ($validated, $request) {

    //         // 🧾 Generate Next Invoice No
    //         $lastInvoice = Purchase::latest()->value('invoice_no');
    //         $nextInvoice = $lastInvoice
    //             ? 'INV-' . str_pad(((int) filter_var($lastInvoice, FILTER_SANITIZE_NUMBER_INT)) + 1, 5, '0', STR_PAD_LEFT)
    //             : 'INV-00001';

    //         // ✍️ Create Purchase with temporary values
    //         $purchase = Purchase::create([
    //             'branch_id'     => auth()->user()->id,
    //             'warehouse_id'  => $validated['warehouse_id'],
    //             'vendor_id'     => $validated['vendor_id'] ?? null,
    //             'purchase_date' => $validated['purchase_date'] ?? now(),
    //             'invoice_no'    => $validated['invoice_no'] ?? $nextInvoice,
    //             'note'          => $validated['note'] ?? null,
    //             'subtotal'      => 0,
    //             'discount'      => 0,
    //             'extra_cost'    => 0,
    //             'net_amount'    => 0,
    //             'paid_amount'   => 0,
    //             'due_amount'    => 0,
    //         ]);

    //         $subtotal = 0;

    //         // 🧾 Purchase Items
    //         $productIds = $validated['product_id'] ?? [];
    //         foreach ($productIds as $index => $productId) {
    //             $qty   = $validated['qty'][$index] ?? null;
    //             $price = $validated['price'][$index] ?? null;

    //             if (empty($productId) || empty($qty) || empty($price)) {
    //                 continue;
    //             }

    //             $disc = $validated['item_discount'][$index] ?? 0; // ✅ Correct name
    //             $unit = $validated['unit'][$index] ?? null;

    //             $lineTotal = ($price * $qty) - $disc;

    //             PurchaseItem::create([
    //                 'purchase_id'   => $purchase->id,
    //                 'product_id'    => $productId,
    //                 'unit'          => $unit,
    //                 'price'         => $price,
    //                 'item_discount' => $disc,
    //                 'qty'           => $qty,
    //                 'line_total'    => $lineTotal,
    //             ]);

    //             $subtotal += $lineTotal;

    //             // 📦 Update Stock
    //             $stock = Stock::where('branch_id', auth()->user()->id)
    //                 ->where('warehouse_id', $validated['warehouse_id'])
    //                 ->where('product_id', $productId)
    //                 ->first();

    //             if ($stock) {
    //                 $stock->qty += $qty;
    //                 $stock->save();
    //             } else {
    //                 Stock::create([
    //                     'branch_id'     => auth()->user()->id,
    //                     'warehouse_id'  => $validated['warehouse_id'],
    //                     'product_id'    => $productId,
    //                     'qty'           => $qty,
    //                 ]);
    //             }
    //         }

    //         // 💵 Final Calculations (use values from request safely)
    //         $discount   = $request->discount ?? 0;
    //         $extraCost  = $request->extra_cost ?? 0;
    //         $netAmount  = ($subtotal - $discount) + $extraCost;

    //         $purchase->update([
    //             'subtotal'    => $subtotal,
    //             'discount'    => $discount,
    //             'extra_cost'  => $extraCost,
    //             'net_amount'  => $netAmount,
    //             'due_amount'  => $netAmount,
    //         ]);

    //         // 📘 Vendor Ledger Update
    //         $previousBalance = VendorLedger::where('vendor_id', $validated['vendor_id'])
    //             ->value('closing_balance') ?? 0;

    //         $newClosingBalance = $previousBalance + $netAmount;

    //         VendorLedger::updateOrCreate(
    //             ['vendor_id' => $validated['vendor_id']],
    //             [
    //                 'vendor_id'         => $validated['vendor_id'],
    //                 'admin_or_user_id'  => auth()->id(),
    //                 'previous_balance'  => $subtotal,
    //                 'closing_balance'   => $newClosingBalance,
    //             ]
    //         );
    //     });

    //     return back()->with('success', 'Purchase saved successfully!');
    // }

    // public function store(Request $request)
    // {

    //         $validated = $request->validate([
    //             'invoice_no'     => 'nullable|string',
    //             'vendor_id'      => 'nullable|exists:vendors,id',
    //             // 'branch_id'      => 'required|exists:branches,id',
    //             'purchase_date'  => 'nullable|date',
    //             'warehouse_id'   => 'nullable|exists:warehouses,id',
    //             'note'           => 'nullable|string',
    //     'discount'       => 'nullable|numeric|min:0',
    //     'extra_cost'     => 'nullable|numeric|min:0',

    //             // Purchase Items
    //             'product_id'     => 'nullable|array',
    //             'product_id.*'   => 'nullable|exists:products,id',
    //             'qty'            => 'nullable|array',
    //             'qty.*'          => 'nullable|numeric|min:1',
    //             'price'          => 'nullable|array',
    //             'price.*'        => 'nullable|numeric|min:0',
    //             'unit'           => 'nullable|array',
    //             'unit.*'         => 'nullable|string',
    //             'item_discount'  => 'nullable|array',
    //             'item_discount.*'=> 'nullable|numeric|min:0',
    //         ]);
    // DB::transaction(function () use ($validated) {

    //     $lastInvoice = Purchase::latest()->value('invoice_no');

    //     $nextInvoice = $lastInvoice
    //         ? 'INV-' . str_pad(((int) filter_var($lastInvoice, FILTER_SANITIZE_NUMBER_INT)) + 1, 5, '0', STR_PAD_LEFT)
    //         : 'INV-00001';

    //     // 1️⃣ Create purchase
    //     $purchase = Purchase::create([
    //         'branch_id'     => Auth()->user()->id,
    //         'warehouse_id'  => $validated['warehouse_id'],
    //         'vendor_id'     => $validated['vendor_id'] ?? null,
    //         'purchase_date' => $validated['purchase_date'] ?? now(),
    //         'invoice_no'    => $validated['invoice_no'] ?? $nextInvoice,
    //         'note'          => $validated['note'] ?? null,
    //         'subtotal'      => $validated['subtotal'] ?? 0,
    //         'discount'      => $validated['discount'] ?? 0,
    //         'extra_cost'    => $validated['extra_cost'] ?? 0,
    //         'net_amount'    => $validated['net_amount'] ?? 0,
    //         'paid_amount'   => 0,
    //         'due_amount'    => 0,

    //     ]);

    //     $subtotal = 0;

    //     // 2️⃣ Loop & filter rows
    //     $productIds = $validated['product_id'] ?? [];
    //     foreach ($productIds as $index => $productId) {
    //         $qty   = $validated['qty'][$index] ?? null;
    //         $price = $validated['price'][$index] ?? null;

    //         // Skip row if any essential field is empty
    //         if (empty($productId) || empty($qty) || empty($price)) {
    //             continue;
    //         }

    //         $disc = $validated['item_disc'][$index] ?? 0;
    //         $unit = $validated['unit'][$index] ?? null;

    //         $lineTotal = ($price * $qty) - $disc;

    //         // Save item
    //         PurchaseItem::create([
    //             'purchase_id'   => $purchase->id,
    //             'product_id'    => $productId,
    //             'unit'          => $unit,
    //             'price'         => $price,
    //             'item_discount' => $disc,
    //             'qty'           => $qty,
    //             'line_total'    => $lineTotal,
    //         ]);

    //         $subtotal += $lineTotal;

    //         // 3️⃣ Update stock
    //         $stock = Stock::where('branch_id', Auth()->user()->id)
    //             ->where('warehouse_id', $validated['warehouse_id'])
    //             ->where('product_id', $productId)
    //             ->first();

    //         if ($stock) {
    //             $stock->qty += $qty;
    //             $stock->save();
    //         } else {
    //             Stock::create([
    //                 'branch_id'     => Auth()->user()->id,
    //                 'warehouse_id'  => $validated['warehouse_id'],
    //                 'product_id'    => $productId,
    //                 'qty'           => $qty,
    //             ]);
    //         }
    //     }

    //     // 4️⃣ Update totals
    //     $purchase->update([
    //         'subtotal'    => $subtotal,
    //         'net_amount'  => $subtotal,
    //         'due_amount'  => $subtotal,
    //     ]);

    //     // 5️⃣ Vendor ledger
    //     $previousBalance = VendorLedger::where('vendor_id', $validated['vendor_id'])
    //         ->value('closing_balance') ?? 0;

    //     $newClosingBalance = $previousBalance + $subtotal;

    //     VendorLedger::updateOrCreate(
    //         ['vendor_id' => $validated['vendor_id']],
    //         [
    //             'vendor_id' => $validated['vendor_id'],
    //             'admin_or_user_id' => Auth::id(),
    //             'previous_balance' => $subtotal,
    //             'closing_balance' => $newClosingBalance,
    //         ]
    //     );

    // });

    // // DB::transaction(function () use ($validated) {

    // // $lastInvoice = Purchase::latest()->value('invoice_no');

    // // // Agar last invoice mila to +1 karo, warna start karo INV-00001
    // // $nextInvoice = $lastInvoice
    // //     ? 'INV-' . str_pad(((int) filter_var($lastInvoice, FILTER_SANITIZE_NUMBER_INT)) + 1, 5, '0', STR_PAD_LEFT)
    // //     : 'INV-00001';

    // //     // 1️⃣ Save main Purchase
    // //     $purchase = Purchase::create([

    // //         'branch_id'     => Auth()->user()->id,
    // //         'warehouse_id'  => $validated['warehouse_id'],
    // //         'vendor_id'     => $validated['vendor_id'] ?? null,
    // //         'purchase_date' => $validated['purchase_date'] ?? now(),
    // //         'invoice_no'    => $validated['invoice_no'] ?? $nextInvoice,
    // //         'note'          => $validated['note'] ?? null,
    // //         'subtotal'      => 0,
    // //         'discount'      => 0,
    // //         'extra_cost'    => 0,
    // //         'net_amount'    => 0,
    // //         'paid_amount'   => 0,
    // //         'due_amount'    => 0,
    // //     ]);

    // //     $subtotal = 0;

    // //     // 2️⃣ Loop purchase items
    // //     foreach ($validated['product_id'] as $index => $productId) {
    // //         $qty     = $validated['qty'][$index];
    // //         $price   = $validated['price'][$index];
    // //         $disc    = $validated['item_discount'][$index] ?? 0;
    // //         $lineTotal = ($price * $qty) - $disc;

    // //         // Save purchase item
    // //         PurchaseItem::create([
    // //             'purchase_id'   => $purchase->id,
    // //             'product_id'    => $productId,
    // //             'unit'          => $validated['unit'][$index] ?? null,
    // //             'price'         => $price,
    // //             'item_discount' => $disc,
    // //             'qty'           => $qty,
    // //             'line_total'    => $lineTotal,
    // //         ]);

    // //         $subtotal += $lineTotal;

    // //         // 3️⃣ Update stock
    // //         $stock = Stock::where('branch_id',  Auth()->user()->id,)
    // //             ->where('warehouse_id', $validated['warehouse_id'])
    // //             ->where('product_id', $productId)
    // //             ->first();

    // //         if ($stock) {
    // //             $stock->qty += $qty;
    // //             $stock->save();
    // //         } else {
    // //             Stock::create([
    // //                 'branch_id'     => Auth()->user()->id,
    // //                 'warehouse_id'  => $validated['warehouse_id'],
    // //                 'product_id'    => $productId,
    // //                 'qty'           => $qty,
    // //             ]);
    // //         }
    // //     }

    // //     // 4️⃣ Update totals in purchase
    // //     $purchase->update([
    // //         'subtotal'    => $subtotal,
    // //         'net_amount'  => $subtotal,
    // //         'due_amount'  => $subtotal,
    // //     ]);

    // //     $previousBalance = VendorLedger::where('vendor_id', $validated['vendor_id'])
    // //         ->value('closing_balance') ?? 0; // If no previous balance, start from 0
    // //     // Calculate new balances

    // //     $newPreviousBalance = $subtotal;

    // //     $newClosingBalance = $previousBalance + $subtotal;
    // //     $userId = Auth::id();

    // //     // Update or create distributor ledger
    // //     VendorLedger::updateOrCreate(
    // //         ['vendor_id' => $validated['vendor_id']],
    // //         [
    // //             'vendor_id' => $validated['vendor_id'],
    // //             'admin_or_user_id' => $userId,
    // //             'previous_balance' => $newPreviousBalance,
    // //             'closing_balance' => $newClosingBalance,
    // //         ]
    // //     );

    // });

    //     return redirect()->back()->with('success', 'Purchase saved successfully!');
    // }

    public function edit($id)
    {
        $purchase = Purchase::with('items.product')->findOrFail($id);

        $Vendor = Vendor::all();
        $Warehouse = Warehouse::all();
        // Filter accounts to only show Cash (1) and Bank (2) heads
        $accounts = \App\Models\Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)->orderBy('title')->get();

        return view('admin_panel.purchase.edit', compact('purchase', 'Vendor', 'Warehouse', 'accounts'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'invoice_no' => 'nullable|string',
            'vendor_id' => 'nullable|exists:vendors,id',
            'purchase_date' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'note' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'extra_cost' => 'nullable|numeric|min:0',

            'product_id' => 'array',
            'product_id.*' => 'nullable|exists:products,id',
            'qty' => 'array',
            'qty.*' => 'nullable|required_with:product_id.*|numeric|min:0',
            'price' => 'array',
            'price.*' => 'nullable|required_with:product_id.*|numeric|min:0',
            'unit' => 'array',
            'unit.*' => 'nullable|required_with:product_id.*|string',
            'item_discount' => 'nullable|array',
            'item_discount.*' => 'nullable|numeric|min:0',
            'mfg_date' => 'nullable|array',
            'mfg_date.*' => 'nullable|date',
            'exp_date' => 'nullable|array',
            'exp_date.*' => 'nullable|date',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request, $id) {
            $purchase = Purchase::with('items')->findOrFail($id);
            $oldNetAmount = $purchase->net_amount;

            $branchId = (int) ($validated['branch_id'] ?? $purchase->branch_id ?? 1);
            $warehouseId = (int) ($validated['warehouse_id'] ?? $purchase->warehouse_id);

            // Map old totals per product for Stock Delta Logic
            $oldMap = $purchase->items->groupBy('product_id')->map(fn ($g) => (float) $g->sum('qty'));

            // Delete old items
            $purchase->items()->delete();

            $subtotal = 0;
            $newMap = collect();

            // Arrays from request
            $pids = $validated['product_id'] ?? [];
            $qtys = $validated['qty'] ?? [];
            $prices = $validated['price'] ?? [];
            $units = $validated['unit'] ?? [];
            $itemDiscs = $validated['item_discount'] ?? [];

            // Snapshot fields (Raw Request)
            $sizeModes = $request->size_mode ?? [];
            $ppbs = $request->pieces_per_box ?? [];
            $ppm2 = $request->pieces_per_m2 ?? [];
            $boxesQtys = $request->boxes_qty ?? [];
            $looseQtys = $request->loose_qty ?? [];
            $lengths = $request->length ?? [];
            $widths = $request->width ?? [];

            foreach ($pids as $i => $pid) {
                $pid = (int) ($pid ?? 0);
                $qty = (float) ($qtys[$i] ?? 0);
                $price = (float) ($prices[$i] ?? 0);

                if (! $pid || $qty <= 0) {
                    continue;
                }

                $discPercent = (float) ($itemDiscs[$i] ?? 0);
                $unit = $units[$i] ?? null;

                // --- Calculation Logic (Matches store()) ---
                $curSizeMode = $sizeModes[$i] ?? null;
                $curPPM2 = (float) ($ppm2[$i] ?? 0);

                if ($curSizeMode === 'by_size') {
                    // price is per m2. Gross = TotalPieces * m2_per_piece * price_per_m2
                    $grossTotal = $curPPM2 * $qty * $price;
                } else {
                    // price is always treated as per-piece for purchase entry
                    $grossTotal = $qty * $price;
                }

                // Calculate absolute discount from percentage
                $discAmount = $grossTotal * ($discPercent / 100);
                $lineTotal = $grossTotal - $discAmount;
                // ------------------------------------------

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $pid,
                    'unit' => $unit,
                    'price' => $price,
                    'item_discount' => $discAmount, // Store calculated amount
                    'qty' => $qty,
                    'line_total' => $lineTotal,
                    'color' => $request->color[$i] ?? null,

                    // Snapshots
                    'size_mode' => $sizeModes[$i] ?? null,
                    'pieces_per_box' => $ppbs[$i] ?? 1,
                    'pieces_per_m2' => $ppm2[$i] ?? 0,
                    'boxes_qty' => $boxesQtys[$i] ?? 0,
                    'loose_qty' => $looseQtys[$i] ?? 0,
                    'length' => $lengths[$i] ?? null,
                    'width' => $widths[$i] ?? null,
                    'mfg_date' => !empty($request->mfg_date[$i]) ? $request->mfg_date[$i] : null,
                    'exp_date' => !empty($request->exp_date[$i]) ? $request->exp_date[$i] : null,
                    'description' => $request->description[$i] ?? null,
                ]);

                $subtotal += $lineTotal;
                $newMap[$pid] = ($newMap[$pid] ?? 0) + $qty;
            }

            // header update
            $purchase->update([
                'vendor_id' => $validated['vendor_id'] ?? $purchase->vendor_id,
                'branch_id' => $branchId,
                'warehouse_id' => $warehouseId,
                'purchase_date' => $validated['purchase_date'] ?? $purchase->purchase_date,
                'invoice_no' => $validated['invoice_no'] ?? $purchase->invoice_no,
                'note' => $validated['note'] ?? $purchase->note,
            ]);

            // totals
            $discount = (float) ($request->discount ?? 0);
            $extraCost = (float) ($request->extra_cost ?? 0);
            $netAmount = ($subtotal - $discount) + $extraCost;

            $purchase->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'additional_discount' => $discount,
                'extra_cost' => $extraCost,
                'net_amount' => $netAmount,
                'due_amount' => $netAmount, // Assuming fully due? Or should we subtract paid?
                // Paid amount is separate (transactions).
                // The 'due_amount' usually tracks how much is left.
                // If we paid partial, tracking logic might need paid_amount check.
                // But standard approach here: due = net - paid
            ]);

            // Recalculate Due based on net - paid
            $paid = $purchase->paid_amount;
            $purchase->update(['due_amount' => $netAmount - $paid]);

            // If this purchase is linked to a gatepass => NO stock changes here
            $isLinkedToGatepass = \App\Models\InwardGatepass::where('purchase_id', $purchase->id)->exists();

            if (! $isLinkedToGatepass) {
                // deltas for movements + stocks
                $movs = [];
                $now = now();
                $all = $oldMap->keys()->merge($newMap->keys())->unique();
                foreach ($all as $pid) {
                    $oldQ = (float) ($oldMap[$pid] ?? 0);
                    $newQ = (float) ($newMap[$pid] ?? 0);
                    $delta = $newQ - $oldQ;
                    if ($delta == 0) {
                        continue;
                    }

                    $type = $delta > 0 ? 'in' : 'out';
                    $qty = abs($delta);

                    $movs[] = [
                        'product_id' => (int) $pid,
                        'type' => $type,
                        'qty' => $qty,
                        'ref_type' => 'PURCHASE_EDIT',
                        'ref_id' => $purchase->id,
                        'note' => 'Purchase edit delta',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $this->upsertStocks((int) $pid, ($type === 'in' ? +$qty : -$qty), $branchId, $warehouseId);
                }
                if (! empty($movs)) {
                    DB::table('stock_movements')->insert($movs);
                }
            }

            $diff = $netAmount - $oldNetAmount;

            // Update Vendor Ledger accurately with diff
            $vendorLedger = \App\Models\VendorLedger::where('vendor_id', $purchase->vendor_id)->first();
            if ($vendorLedger) {
                $vendorLedger->closing_balance += $diff;
                $vendorLedger->save();
            } else {
                \App\Models\VendorLedger::create([
                    'vendor_id' => $purchase->vendor_id,
                    'admin_or_user_id' => auth()->id(),
                    'previous_balance' => 0,
                    'opening_balance' => 0,
                    'closing_balance' => $netAmount,
                ]);
            }

            // Adjust Journal Vouchers correctly
            $voucher = \App\Models\VoucherMaster::where('remarks', "Purchase Voucher #{$purchase->invoice_no}")->first();
            if ($voucher) {
                $voucher->total_amount = max(0, $voucher->total_amount + $diff);
                $voucher->save();

                $balanceService = app(\App\Services\BalanceService::class);
                $expenseAccountId = $balanceService->getPurchaseExpenseId();
                $apAccountId = $balanceService->getAccountsPayableId();

                \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                    ->where('source_id', $voucher->id)
                    ->where('account_id', $expenseAccountId)
                    ->update(['debit' => $purchase->net_amount]);

                \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                    ->where('source_id', $voucher->id)
                    ->where('account_id', $apAccountId)
                    ->update(['credit' => $purchase->net_amount]);
            }
        });

        return redirect()->route('Purchase.home')->with('success', 'Purchase updated successfully!');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $purchase = Purchase::with('items')->findOrFail($id);
            $oldNetAmount = $purchase->net_amount;

            $branchId = (int) ($purchase->branch_id ?? 1);
            $warehouseId = (int) ($purchase->warehouse_id);

            // linked to gatepass? then NO stock changes
            $isLinkedToGatepass = \App\Models\InwardGatepass::where('purchase_id', $purchase->id)->exists();

            if (! $isLinkedToGatepass) {
                $movs = [];
                $now = now();

                foreach ($purchase->items as $it) {
                    $pid = (int) $it->product_id;
                    $qty = (float) $it->qty;

                    $movs[] = [
                        'product_id' => $pid,
                        'type' => 'out',
                        'qty' => $qty,
                        'ref_type' => 'PURCHASE_DELETE',
                        'ref_id' => $purchase->id,
                        'note' => 'Delete purchase (reverse)',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    // stocks rollback
                    $this->upsertStocks($pid, -$qty, $branchId, $warehouseId);
                }

                if (! empty($movs)) {
                    DB::table('stock_movements')->insert($movs);
                }
            }

            $purchase->items()->delete();
            $purchase->delete();
        });

        return redirect()->back()->with('success', 'Purchase deleted successfully.');
    }

    public function Invoice($id)
    {
        $purchase = Purchase::with(['items.product', 'vendor', 'warehouse'])->findOrFail($id);
        
        $balanceService = app(\App\Services\BalanceService::class);
        $vendor_balance = $balanceService->getVendorBalance($purchase->vendor_id);

        $previousBalance = 0;
        if ($purchase->vendor_id) {
            $voucher = \App\Models\VoucherMaster::where('remarks', "Purchase Voucher #{$purchase->invoice_no}")
                ->orderByRaw("ABS(TIMESTAMPDIFF(SECOND, created_at, '{$purchase->created_at}'))")
                ->first();
            $journalEntry = null;
            if ($voucher) {
                $journalEntry = \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                    ->where('source_id', $voucher->id)
                    ->where('party_type', \App\Models\Vendor::class)
                    ->where('party_id', $purchase->vendor_id)
                    ->first();
            }

            if ($journalEntry) {
                $previousBalance = \App\Models\JournalEntry::where('party_type', \App\Models\Vendor::class)
                    ->where('party_id', $purchase->vendor_id)
                    ->where('id', '<', $journalEntry->id)
                    ->sum(\Illuminate\Support\Facades\DB::raw('credit - debit'));
            } else {
                $previousBalance = $balanceService->getVendorBalanceBeforeDate($purchase->vendor_id, $purchase->created_at->format('Y-m-d H:i:s'));
            }
        }
        $currentBalance = $previousBalance + $purchase->net_amount - $purchase->paid_amount;

        return view('admin_panel.purchase.Invoice', compact('purchase', 'vendor_balance', 'previousBalance', 'currentBalance'));
    }

    public function receipt($id)
    {
        $purchase = Purchase::with(['items.product', 'vendor'])->findOrFail($id);

        $balanceService = app(\App\Services\BalanceService::class);
        $previousBalance = 0;
        if ($purchase->vendor_id) {
            $voucher = \App\Models\VoucherMaster::where('remarks', "Purchase Voucher #{$purchase->invoice_no}")
                ->orderByRaw("ABS(TIMESTAMPDIFF(SECOND, created_at, '{$purchase->created_at}'))")
                ->first();
            $journalEntry = null;
            if ($voucher) {
                $journalEntry = \App\Models\JournalEntry::where('source_type', \App\Models\VoucherMaster::class)
                    ->where('source_id', $voucher->id)
                    ->where('party_type', \App\Models\Vendor::class)
                    ->where('party_id', $purchase->vendor_id)
                    ->first();
            }

            if ($journalEntry) {
                $previousBalance = \App\Models\JournalEntry::where('party_type', \App\Models\Vendor::class)
                    ->where('party_id', $purchase->vendor_id)
                    ->where('id', '<', $journalEntry->id)
                    ->sum(\Illuminate\Support\Facades\DB::raw('credit - debit'));
            } else {
                $previousBalance = $balanceService->getVendorBalanceBeforeDate($purchase->vendor_id, $purchase->created_at->format('Y-m-d H:i:s'));
            }
        }
        $currentBalance = $previousBalance + $purchase->net_amount - $purchase->paid_amount;

        return view('admin_panel.purchase.receipt', compact('purchase', 'previousBalance', 'currentBalance'));
    }

    // purchase_reutun

    public function showReturnForm($id)
    {
        $purchase = Purchase::with(['vendor', 'warehouse', 'items.product'])->findOrFail($id);
        $accounts = \App\Models\Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)->orderBy('title')->get();
        // Identify max returnable qty: Purchase Qty - Already Returned Qty
        // 1. Get all previous returns for this purchase
        $pastReturns = \App\Models\PurchaseReturn::where('purchase_id', $id)
            ->with('items')
            ->get();
        
        $returnedQtyMap = [];
        foreach ($pastReturns as $pr) {
            foreach ($pr->items as $prItem) {
                $key = $prItem->product_id . '_' . ($prItem->color ?? '');
                if (!isset($returnedQtyMap[$key])) {
                    $returnedQtyMap[$key] = 0;
                }
                $returnedQtyMap[$key] += $prItem->qty;
            }
        }
        
        $purchaseItems = [];
        $hasReturnableItems = false;
        
        foreach ($purchase->items as $item) {
            $key = $item->product_id . '_' . ($item->color ?? '');
            $alreadyReturned = $returnedQtyMap[$key] ?? 0;
            $remaining = max(0, $item->qty - $alreadyReturned);
            
            if ($remaining > 0) {
                 $hasReturnableItems = true;
            }
            
            $itemName = optional($item->product)->item_name ?? 'Unknown Product';
            $colorStr = '';
            if (!empty($item->color)) {
                $decoded = base64_decode($item->color, true);
                if ($decoded !== false && is_string($decoded) && str_starts_with(trim($decoded), '{')) {
                    $parsed = json_decode($decoded, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                        $parts = [];
                        if (!empty($parsed['size']) && $parsed['size'] !== '-') $parts[] = $parsed['size'];
                        if (!empty($parsed['color']) && $parsed['color'] !== '-') $parts[] = $parsed['color'];
                        $colorStr = implode(' | ', $parts);
                    } else {
                        $colorStr = $item->color;
                    }
                } else {
                    $colorStr = $item->color;
                }
            }
            if ($colorStr) {
                $itemName .= ' (' . $colorStr . ')';
            }
            
            $purchaseItems[] = [
                'product_id' => $item->product_id,
                'item_name' => $itemName,
                'brand' => optional(optional($item->product)->brand)->name ?? '',
                'item_code' => optional($item->product)->item_code ?? '',
                // Fix: Prioritize Product Master PPB to ensure correct Frontend Box calculation
                'pieces_per_box' => (optional($item->product)->pieces_per_box > 0) ? $item->product->pieces_per_box : ($item->pieces_per_box ?? 1),
                'size_mode' => $item->size_mode ?? optional($item->product)->size_mode ?? 'by_pieces',
                'pieces_per_m2' => $item->pieces_per_m2 ?? optional($item->product)->pieces_per_m2 ?? 0,
                'price' => $item->price,
                
                // Qty Logic for Partial Return
                'original_qty' => $item->qty,
                'returned_qty' => $alreadyReturned,
                'qty' => $remaining, // Current Limit
                
                'unit' => $item->unit ?? 'pc',
                'discount' => $item->item_discount,
                'color' => $item->color,
            ];
        }
        
        // Block access if fully returned
        if (!$hasReturnableItems && $purchase->status_purchase == 'Returned') {
             // Or if total remaining is 0. 
             // Logic: If status is 'Returned', maybe it was fully returned? 
             // But partial return also sets status to 'Returned' in current logic (we might want to change that to 'Partial' later).
             // For now, relies on calculated remaining quantity.
             return redirect()->route('purchase.return.index')->with('error', 'This purchase has clearly been fully returned already.');
        }

        return view('admin_panel.purchase.purchase_return.create', compact('purchase', 'accounts', 'purchaseItems'));
    }

    // store return
    public function storeReturn(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => 'nullable|exists:purchases,id',
            'vendor_id' => 'required|exists:vendors,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'return_reason' => 'nullable|string|max:255',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array', // Pieces (Total Pieces)
            'qty.*' => 'required|numeric|min:0', // Allow 0 if partial
            'price' => 'required|array',
            'payment_account_id' => 'nullable|array',
            'payment_amount' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // 1. Generate Return Invoice #
            $lastReturn = PurchaseReturn::latest()->first();
            $nextInvoice = 'PRTN-'.str_pad(optional($lastReturn)->id + 1 ?? 1, 5, '0', STR_PAD_LEFT);

            // 2. Create Purchase Return Record
            $purchase = $request->purchase_id ? Purchase::find($request->purchase_id) : null;
            $remarks = $request->return_reason;
            if ($purchase) {
                $remarks .= ' (Ref Invoice: '.$purchase->invoice_no.')';
            }

            $return = PurchaseReturn::create([
                'purchase_id' => $purchase ? $purchase->id : null, 
                'vendor_id' => $validated['vendor_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'return_invoice' => $nextInvoice,
                'return_date' => $validated['return_date'],
                'return_reason' => $validated['return_reason'],
                'remarks' => $remarks,
                'bill_amount' => 0, // calculated below
                'item_discount' => 0,
                'extra_discount' => $request->extra_discount ?? 0,
                'net_amount' => 0,
                'paid' => 0,
                'balance' => 0,
            ]);

            $subtotal = 0;
            $totalItemDiscount = 0;
            $movements = [];
            $now = now();

            // 3. Process Items & Stock
            foreach ($validated['product_id'] as $index => $productId) {
                $qty = (float) ($validated['qty'][$index] ?? 0); // Pieces
                $price = (float) ($validated['price'][$index] ?? 0);

                if ($qty <= 0) {
                    continue;
                }

                // Find original item to get snapshots
                $origItem = \App\Models\PurchaseItem::where('purchase_id', $purchase->id ?? 0)
                    ->where('product_id', $productId)
                    ->first();

                // Fallback to Product defaults if no original item
                $product = Product::find($productId);
                $ppb = $origItem ? ($origItem->pieces_per_box ?? 1) : ($product->pieces_per_box ?? 1);
                $sizeMode = $origItem ? ($origItem->size_mode ?? 'by_pieces') : ($product->size_mode ?? 'by_pieces');
                
                // Fallback to m2_of_box if pieces_per_m2 is 0 or missing in old data
                $ppm2 = $origItem && $origItem->pieces_per_m2 > 0 ? $origItem->pieces_per_m2 : ($product->m2_of_box ?? 0);

                // Calculate Line Total Logic (Same as Purchase Store)
                if ($sizeMode === 'by_size') {
                    // price is per m2. Gross = TotalPieces * m2_per_piece * price_per_m2
                    $lineTotal = round($ppm2 * $qty * $price, 2);
                } elseif ($sizeMode === 'by_cartons' || $sizeMode === 'by_carton') {
                    $lineTotal = $qty * $price;
                } else {
                    $lineTotal = $qty * $price;
                }

                $itemDisc = 0;

                PurchaseReturnItem::create([
                    'purchase_return_id' => $return->id,
                    'product_id' => $productId,
                    'qty' => $qty, // Pieces
                    'price' => $price,
                    'item_discount' => $itemDisc,
                    'unit' => 'pc', // Default
                    'line_total' => $lineTotal,
                    'color' => $request->color[$index] ?? null,
                ]);

                // Check for weight product conversion factor
                $stockQty = $qty;
                $colorField = $request->color[$index] ?? null;
                if (!empty($colorField)) {
                    try {
                        $b64Decoded = base64_decode($colorField, true);
                        $variantData = $b64Decoded !== false ? json_decode($b64Decoded, true) : null;
                        if (!is_array($variantData)) {
                            $variantData = is_string($colorField) ? json_decode($colorField, true) : $colorField;
                        }
                        if (is_array($variantData) && isset($variantData['conv_factor'])) {
                            $factor = (float)$variantData['conv_factor'];
                            if ($factor > 0) {
                                $stockQty = $stockQty * $factor;
                            }
                        }
                    } catch (\Exception $e) {}
                }

                // Update Stock (DECREMENT)
                $stock = WarehouseStock::where('warehouse_id', $validated['warehouse_id'])
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $product = Product::find($productId); // Ensure fresher product data
                    $ppbVal = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
                    
                    // Use total_pieces as primary source of truth to avoid losing loose pieces, fallback only if zero
                    $currentTotalPieces = $stock->total_pieces;
                    if ($currentTotalPieces == 0 && $stock->quantity > 0) {
                        $currentTotalPieces = $stock->quantity * $ppbVal;
                    }
                    
                    // Subtract Return Qty (Pieces)
                    $newTotalPieces = max(0, $currentTotalPieces - $stockQty);
                    
                    $stock->total_pieces = $newTotalPieces;
                    $stock->quantity = $newTotalPieces / $ppbVal; // Convert back to Boxes
                    $stock->save();
                } else {
                     // Should technically not happen for return, but handle gracefully
                     $product = Product::find($productId);
                     $ppbVal = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
                     
                     WarehouseStock::create([
                        'warehouse_id' => $validated['warehouse_id'],
                        'product_id' => $productId,
                        'total_pieces' => -$stockQty, // Negative stock?
                        'quantity' => -$stockQty / $ppbVal,
                        'price' => 0
                     ]);
                }

                // Prepare Stock Movement
                $movements[] = [
                    'product_id' => $productId,
                    'type' => 'out', // Return OUT to vendor
                    'qty' => $stockQty,
                    'ref_type' => 'PURCHASE_RETURN',
                    'ref_id' => $return->id,
                    'note' => "Purchase Return",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $subtotal += $lineTotal;
                $totalItemDiscount += $itemDisc;
            }

            // Bulk Insert Movements
            if (! empty($movements)) {
                DB::table('stock_movements')->insert($movements);
            }

            $netAmount = ($subtotal - $totalItemDiscount) - ($request->extra_discount ?? 0);

            // 4. Handle Refund Payment
            $totalPaid = 0;
            if (! empty($request->payment_account_id)) {
                $voucherService = app(\App\Services\VoucherService::class);
                $apId = app(\App\Services\BalanceService::class)->getAccountsPayableId();

                foreach ($request->payment_account_id as $idx => $accId) {
                    $amt = (float) ($request->payment_amount[$idx] ?? 0);
                    if ($accId && $amt > 0) {
                        $totalPaid += $amt;
                        
                        // Create Receipt Voucher via Service (Cash In)
                        $voucherData = [
                            'voucher_type' => \App\Models\VoucherMaster::TYPE_RECEIPT,
                            'date' => $validated['return_date'],
                            'status' => \App\Models\VoucherMaster::STATUS_POSTED,
                            'party_type' => \App\Models\Vendor::class,
                            'party_id' => $validated['vendor_id'],
                            'remarks' => "Refund for Return #{$nextInvoice}",
                        ];

                        $lines = [
                            [
                                'account_id' => $accId, 
                                'debit' => $amt,
                                'credit' => 0,
                                'narration' => 'Cash Refund Received'
                            ],
                            [
                                'account_id' => $apId,
                                'debit' => 0,
                                'credit' => $amt,
                                'narration' => 'Refund from Vendor'
                            ]
                        ];
                        
                        $voucherService->createVoucher($voucherData, $lines, auth()->id());
                    }
                }
            }

            $return->update([
                'bill_amount' => $subtotal,
                'item_discount' => $totalItemDiscount,
                'net_amount' => $netAmount,
                'paid' => $totalPaid,
                'balance' => $netAmount - $totalPaid,
            ]);

            // Update Purchase Status (only if full return)
            if ($purchase) {
                $totalBought = $purchase->items->sum('qty');
                $totalReturned = \App\Models\PurchaseReturnItem::join('purchase_returns', 'purchase_returns.id', '=', 'purchase_return_items.purchase_return_id')
                    ->where('purchase_returns.purchase_id', $purchase->id)
                    ->sum('purchase_return_items.qty');
                
                if ($totalReturned >= $totalBought) {
                    $purchase->update(['status_purchase' => 'Returned']);
                }
            }

            // 5. Update Vendor Ledger & Accounting
            // A. Create General Ledger Voucher for Return (Debit Vendor, Credit Purchase Return)
            $transactionService = app(\App\Services\TransactionService::class);
            if (method_exists($transactionService, 'createPurchaseReturnVoucher')) {
                 $transactionService->createPurchaseReturnVoucher($return);
            }

            // B. Update Legacy Vendor Ledger
            // Logic: Return reduces Payable (Debit Vendor).
            // Refund increases Payable back (Credit Vendor) - effectively clearing the Debit.
            
            $balanceChange = -($netAmount - $totalPaid); // Reduces payable

            // Using VendorLedger table manual update for legacy views
            $ledger = \App\Models\VendorLedger::where('vendor_id', $validated['vendor_id'])->latest()->first();
            $currentClosing = $ledger ? $ledger->closing_balance : 0; // Current closing
            
            // Note: VendorLedger table structure does not support transaction history (no date/desc columns),
            // so we treat it as a Balance Snapshot.
            \App\Models\VendorLedger::updateOrCreate(
                ['vendor_id' => $validated['vendor_id']],
                [
                    'admin_or_user_id' => auth()->id(),
                    'opening_balance' => $ledger ? $ledger->opening_balance : 0, 
                    'previous_balance' => $currentClosing,
                    'closing_balance' => $currentClosing + $balanceChange,
                ]
            );

            DB::commit();

            return redirect()->route('purchase.return.index')->with('success', 'Purchase return processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing return: '.$e->getMessage());
        }
    }

    public function purchaseReturnIndex()
    {
        $returns = \App\Models\PurchaseReturn::with(['vendor', 'warehouse', 'purchase'])->latest()->get();
        
        // Calculate updated financial details for each return
        $returns->each(function ($return) {
            if ($return->purchase) {
                $purchase = $return->purchase;
                
                // Original Purchase Amounts
                $return->original_net_amount = $purchase->net_amount;
                $return->original_paid_amount = $purchase->paid_amount;
                $return->original_due_amount = $purchase->due_amount;
                
                // Calculate total returns for this purchase
                $totalReturned = \App\Models\PurchaseReturn::where('purchase_id', $purchase->id)
                    ->sum('net_amount');
                
                // New amounts after return(s)
                $return->new_net_amount = max(0, $purchase->net_amount - $totalReturned);
                $return->new_due_amount = max(0, $purchase->due_amount - $return->net_amount);
                $return->total_returned = $totalReturned;
            }
        });

        return view('admin_panel.purchase.purchase_return.index', compact('returns'));
    }

    public function viewReturn($id)
    {
        $return = \App\Models\PurchaseReturn::with(['vendor', 'warehouse', 'items.product'])->findOrFail($id);
        return view('admin_panel.purchase.purchase_return.show', compact('return'));
    }
}
