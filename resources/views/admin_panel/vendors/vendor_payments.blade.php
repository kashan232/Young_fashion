@extends('admin_panel.layout.app')

@section('content')

<style>
    /* Premium ERP Styling for Vendor Payments */
    .vpay-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #1e293b;
    }

    /* Page Header */
    .vpay-page-header {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .vpay-page-title {
        font-weight: 800;
        font-size: 22px;
        color: #0f172a;
        margin: 0 0 4px 0;
        letter-spacing: -0.3px;
    }
    .vpay-page-sub {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    /* Action Buttons */
    .btn-action-primary {
        background: #2563eb !important;
        border: 1px solid #1d4ed8 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding: 9px 18px !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
    }
    .btn-action-primary:hover {
        background: #1d4ed8 !important;
        transform: translateY(-1px);
        color: #ffffff !important;
    }

    .btn-action-secondary {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #334155 !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding: 9px 16px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        transition: all 0.15s ease-in-out !important;
    }
    .btn-action-secondary:hover {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }

    /* Stat Cards */
    .vpay-stat-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .vpay-stat-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 6px;
    }
    .vpay-stat-number {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    /* Main Table Card */
    .vpay-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .vpay-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Modern Table */
    .vpay-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .vpay-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #cbd5e1;
        padding: 12px 16px;
        white-space: nowrap;
    }
    .vpay-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .vpay-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Badges */
    .badge-paid-amount {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-size: 13px;
        font-weight: 700;
        font-family: monospace;
        padding: 5px 12px;
        border-radius: 6px;
        display: inline-block;
    }

    .badge-method {
        background-color: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
    }

    .badge-type-payment {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-block;
    }

    .badge-id {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
        font-weight: 700;
        font-size: 12px;
        font-family: monospace;
        padding: 3px 8px;
        border-radius: 4px;
    }

    /* Action Buttons in Table */
    .btn-tbl-action {
        padding: 5px 10px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        text-decoration: none !important;
    }

    /* Modal Form */
    .vpay-modal .modal-content {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    .vpay-modal .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .vpay-modal .modal-body {
        padding: 20px;
    }
    .vpay-modal .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 14px 20px;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .balance-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        display: none;
    }
    .modal-header .close {
        background: none;
        border: none;
        font-size: 26px;
        line-height: 1;
        color: #64748b;
        cursor: pointer;
        padding: 0;
        margin: -4px 0 0 0;
    }
    .modal-header .close:hover {
        color: #0f172a;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid vpay-container">

            {{-- ── 1. Page Header ── --}}
            <div class="vpay-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h1 class="vpay-page-title">Vendor Payments</h1>
                    <p class="vpay-page-sub">Manage supplier payment records, accounts payable disbursements, and vendor khata</p>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn-action-primary" id="btnMakePayment" onclick="openPaymentModal()" data-toggle="modal" data-target="#paymentModal" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="fas fa-plus"></i> Make Payment
                    </button>
                    <a href="{{ route('vendors-ledger') }}" class="btn-action-secondary">
                        <i class="fas fa-book"></i> Vendor Ledger
                    </a>
                    <a href="{{ route('Purchase.home') }}" class="btn-action-secondary">
                        <i class="fas fa-arrow-left"></i> Purchases
                    </a>
                </div>
            </div>

            {{-- ── 2. Top Summary Stat Cards ── --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="vpay-stat-box">
                        <div class="vpay-stat-label">Total Payments Disbursed</div>
                        <div class="vpay-stat-number text-success">Rs. {{ number_format($totalPaidAmount, 2) }}</div>
                        <div class="small text-muted mt-1">Paid to suppliers across all records</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="vpay-stat-box">
                        <div class="vpay-stat-label">Total Transactions</div>
                        <div class="vpay-stat-number">{{ $totalPaymentsCount }}</div>
                        <div class="small text-muted mt-1">Disbursement vouchers recorded</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="vpay-stat-box">
                        <div class="vpay-stat-label">Total Outstanding Payables</div>
                        <div class="vpay-stat-number text-danger">Rs. {{ number_format($totalPayables, 2) }}</div>
                        <div class="small text-muted mt-1">Total credit balance owed to vendors</div>
                    </div>
                </div>
            </div>

            {{-- ── 3. Success Alert ── --}}
            @if (session()->has('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                    <i class="fas fa-check-circle fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- ── 4. Main Payment Records Card ── --}}
            <div class="vpay-card">
                <div class="vpay-card-header">
                    <div class="fw-bold text-dark fs-6">
                        <i class="fas fa-list me-1 text-muted"></i> All Vendor Payment Records
                    </div>
                    <div class="text-muted small">Showing {{ $payments->count() }} entries</div>
                </div>

                <div class="table-responsive">
                    <table class="table vpay-table datanew">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 70px;">ID</th>
                                <th style="min-width: 120px;">Payment Date</th>
                                <th style="min-width: 220px;">Vendor / Supplier</th>
                                <th class="text-end" style="min-width: 140px;">Amount Paid</th>
                                <th style="min-width: 130px;">Method / Account</th>
                                <th style="min-width: 130px;">Type</th>
                                <th style="min-width: 200px;">Remarks / Note</th>
                                <th class="text-end" style="width: 140px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $pay)
                                <tr>
                                    {{-- ID --}}
                                    <td class="text-center">
                                        <span class="badge-id">#{{ $pay->id }}</span>
                                    </td>

                                    {{-- Payment Date --}}
                                    <td>
                                        <span class="fw-semibold text-dark text-nowrap">
                                            {{ \Carbon\Carbon::parse($pay->payment_date)->format('d-M-Y') }}
                                        </span>
                                    </td>

                                    {{-- Vendor --}}
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 13.5px;">
                                            {{ $pay->vendor->name ?? 'N/A' }}
                                        </div>
                                        @if(!empty($pay->vendor->phone))
                                            <div class="text-muted small">
                                                <i class="fas fa-phone-alt me-1 text-muted" style="font-size: 10px;"></i>{{ $pay->vendor->phone }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Amount Paid --}}
                                    <td class="text-end">
                                        <span class="badge-paid-amount">
                                            Rs. {{ number_format($pay->amount, 2) }}
                                        </span>
                                    </td>

                                    {{-- Method / Account --}}
                                    <td>
                                        <span class="badge-method">
                                            {{ $pay->payment_method ?: 'Cash in Hand' }}
                                        </span>
                                    </td>

                                    {{-- Type --}}
                                    <td>
                                        <span class="badge-type-payment">
                                            Payment Out
                                        </span>
                                    </td>

                                    {{-- Remarks --}}
                                    <td>
                                        <span class="text-secondary small">
                                            {{ $pay->note ?: '—' }}
                                        </span>
                                    </td>

                                    {{-- Action --}}
                                    <td class="text-end text-nowrap">
                                        @if($pay->vendor_id)
                                            <a href="{{ route('vendor.ledger', $pay->vendor_id) }}" class="btn btn-sm btn-outline-primary btn-tbl-action me-1" title="View Vendor Ledger">
                                                <i class="fas fa-book"></i> Ledger
                                            </a>
                                        @endif
                                        <form action="{{ route('vendor.payments.destroy', $pay->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment record? The ledger balance will be restored.');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-tbl-action" title="Delete Payment">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="mb-2"><i class="fas fa-receipt fa-3x text-muted opacity-50"></i></div>
                                        <div class="fw-bold fs-6">No Vendor Payments Recorded</div>
                                        <div class="small text-muted mt-1">Click "Make Payment" above to record a new supplier payment.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── 5. Payment Modal ── --}}
<div class="modal fade vpay-modal" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('vendor.payments.store') }}" method="POST" id="vendorPaymentForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-dark mb-0" id="paymentModalLabel">
                        <i class="fas fa-money-bill-wave text-primary me-2"></i> Record Vendor Payment
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" onclick="closePaymentModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    
                    {{-- Vendor Select --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 13px;">Select Vendor / Supplier <span class="text-danger">*</span></label>
                        <select name="vendor_id" id="vendor_id" class="form-control form-select" required>
                            <option value="">-- Choose Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" data-balance="{{ $vendor->live_balance ?? 0 }}">
                                    {{ $vendor->name }} (Current: Rs. {{ number_format($vendor->live_balance ?? 0, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Live Balance Info Box --}}
                    <div id="balanceBox" class="balance-box">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold">Current Outstanding Balance:</div>
                                <div id="balanceText" class="fw-bold text-danger fs-5">Rs. 0.00 (Payable)</div>
                            </div>
                            <button type="button" id="btnPayFull" class="btn btn-sm btn-primary fw-bold px-2 py-1" style="font-size: 12px;">
                                <i class="fas fa-bolt me-1"></i> Pay Full Balance
                            </button>
                        </div>
                    </div>

                    {{-- Payment Date & Nature --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Payment Type <span class="text-danger">*</span></label>
                            <select name="adjustment_type" class="form-control form-select" required>
                                <option value="minus" selected>Vendor Payment (Reduces Balance)</option>
                                <option value="plus">Refund / Adjustment (Increases Balance)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Amount Paid --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 13px;">Payment Amount (Rs.) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text fw-bold bg-light">Rs.</span>
                            </div>
                            <input type="number" step="0.01" min="0.01" name="amount" id="payment_amount" class="form-control form-control-lg fw-bold font-monospace" placeholder="0.00" required>
                        </div>
                    </div>

                    {{-- Paid From Account & Payment Method --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Payment Account</label>
                            <select name="account_id" class="form-control form-select">
                                @foreach($accounts as $acc)
                                    @if(in_array($acc->id, [1, 2]) || str_contains(strtolower($acc->title), 'cash') || str_contains(strtolower($acc->title), 'bank'))
                                        <option value="{{ $acc->id }}" {{ str_contains(strtolower($acc->title), 'cash') ? 'selected' : '' }}>
                                            {{ $acc->title }} ({{ $acc->account_code }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Payment Method</label>
                            <select name="payment_method" class="form-control form-select">
                                <option value="Cash in Hand" selected>Cash in Hand</option>
                                <option value="Bank Transfer">Bank Transfer / Online</option>
                                <option value="Cheque">Cheque</option>
                                <option value="EasyPaisa / JazzCash">EasyPaisa / JazzCash</option>
                            </select>
                        </div>
                    </div>

                    {{-- Remarks / Note --}}
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark" style="font-size: 12.5px;">Remarks / Note</label>
                        <textarea name="note" id="payment_note" rows="2" class="form-control" placeholder="e.g. Paid cash against purchase PUR-001 or weekly settlement"></textarea>
                    </div>

                </div>

                <div class="modal-footer d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-secondary px-3" data-dismiss="modal" data-bs-dismiss="modal" onclick="closePaymentModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="fas fa-check-circle me-1"></i> Submit Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var currentVendorBalance = 0;

    function openPaymentModal() {
        clearPaymentForm();
        if (window.jQuery) {
            $('#paymentModal').modal('show');
        } else {
            var m = document.getElementById('paymentModal');
            if (m) {
                m.style.display = 'block';
                m.classList.add('show');
            }
        }
    }

    function closePaymentModal() {
        if (window.jQuery) {
            $('#paymentModal').modal('hide');
        } else {
            var m = document.getElementById('paymentModal');
            if (m) {
                m.style.display = 'none';
                m.classList.remove('show');
            }
        }
    }

    function clearPaymentForm() {
        var form = document.getElementById('vendorPaymentForm');
        if (form) form.reset();
        var dateInput = document.getElementById('payment_date');
        if (dateInput) dateInput.value = "{{ date('Y-m-d') }}";
        var bBox = document.getElementById('balanceBox');
        if (bBox) bBox.style.display = 'none';
        currentVendorBalance = 0;
    }

    // Direct event listener to button to ensure clicking ALWAYS triggers modal
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('btnMakePayment');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openPaymentModal();
            });
        }

        // Vendor Change Listener
        var vendorSelect = document.getElementById('vendor_id');
        if (vendorSelect) {
            vendorSelect.addEventListener('change', function () {
                var vId = this.value;
                var bBox = document.getElementById('balanceBox');
                var bText = document.getElementById('balanceText');
                var btnPay = document.getElementById('btnPayFull');

                if (vId) {
                    if (window.jQuery) {
                        $.ajax({
                            url: '/get-vendor-balance/' + vId,
                            type: 'GET',
                            success: function (res) {
                                currentVendorBalance = parseFloat(res.closing_balance || 0);
                                if (bText) bText.innerText = res.formatted;
                                if (bBox) bBox.style.display = 'block';
                                if (btnPay) {
                                    btnPay.style.display = currentVendorBalance > 0 ? 'inline-block' : 'none';
                                }
                            },
                            error: function () {
                                var selectedOpt = vendorSelect.options[vendorSelect.selectedIndex];
                                var fallback = parseFloat(selectedOpt.getAttribute('data-balance') || 0);
                                currentVendorBalance = fallback;
                                if (bText) bText.innerText = 'Rs. ' + fallback.toFixed(2);
                                if (bBox) bBox.style.display = 'block';
                            }
                        });
                    }
                } else {
                    if (bBox) bBox.style.display = 'none';
                    currentVendorBalance = 0;
                }
            });
        }

        // Pay Full Balance click
        var btnPay = document.getElementById('btnPayFull');
        if (btnPay) {
            btnPay.addEventListener('click', function () {
                if (currentVendorBalance > 0) {
                    var amtInput = document.getElementById('payment_amount');
                    if (amtInput) amtInput.value = currentVendorBalance.toFixed(2);
                }
            });
        }
    });
</script>

@endsection
