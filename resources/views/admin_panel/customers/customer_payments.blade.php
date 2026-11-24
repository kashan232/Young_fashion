@extends('admin_panel.layout.app')

@section('content')

<style>
    /* Premium ERP Styling for Customer Payments & Recoveries */
    .cpay-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #1e293b;
    }

    /* Page Header */
    .cpay-page-header {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .cpay-page-title {
        font-weight: 800;
        font-size: 22px;
        color: #0f172a;
        margin: 0 0 4px 0;
        letter-spacing: -0.3px;
    }
    .cpay-page-sub {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    /* Action Buttons */
    .btn-action-primary {
        background: #16a34a !important;
        border: 1px solid #15803d !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding: 9px 18px !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin-right: 10px !important;
        margin-bottom: 6px !important;
        text-decoration: none !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
    }
    .btn-action-primary:hover {
        background: #15803d !important;
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
        margin-right: 10px !important;
        margin-bottom: 6px !important;
        text-decoration: none !important;
        transition: all 0.15s ease-in-out !important;
    }
    .btn-action-secondary:hover {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }

    /* Stat Cards */
    .cpay-stat-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .cpay-stat-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #cbd5e1;
    }
    .cpay-stat-box.border-success::before { background: #16a34a; }
    .cpay-stat-box.border-primary::before { background: #2563eb; }
    .cpay-stat-box.border-warning::before { background: #f59e0b; }
    .cpay-stat-box.border-danger::before { background: #dc2626; }

    .cpay-stat-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 6px;
    }
    .cpay-stat-number {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    /* Filter Box Styling */
    .cpay-filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .cpay-filter-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 5px;
    }
    .cpay-filter-input {
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        font-size: 13px;
        height: 38px;
        color: #0f172a;
    }
    .cpay-filter-input:focus {
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
        outline: none;
    }
    .btn-cpay-filter {
        height: 38px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* Main Table Card */
    .cpay-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .cpay-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Modern Table */
    .cpay-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .cpay-table thead th {
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
    .cpay-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .cpay-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Badges */
    .badge-paid-amount {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-size: 13.5px;
        font-weight: 800;
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
    .badge-id {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
        font-weight: 700;
        font-size: 11px;
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
        margin-right: 6px !important;
        margin-bottom: 4px !important;
        text-decoration: none !important;
    }

    /* Modal Form */
    .cpay-modal .modal-content {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    .cpay-modal .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .cpay-modal .modal-body {
        padding: 20px;
    }
    .cpay-modal .modal-footer {
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

    /* Receipt Slip Styling */
    .receipt-slip-wrap {
        background: #ffffff;
        border: 1.5px dashed #cbd5e1;
        border-radius: 8px;
        padding: 24px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        #printableReceiptArea, #printableReceiptArea * {
            visibility: visible;
        }
        #printableReceiptArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid cpay-container">

            {{-- ── 1. Page Header ── --}}
            <div class="cpay-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h1 class="cpay-page-title"><i class="fas fa-hand-holding-usd text-success mr-2"></i>Customer Payments &amp; Recoveries</h1>
                    <p class="cpay-page-sub">Record market collections, receive credit recoveries, manage khata, and print payment vouchers</p>
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <button type="button" class="btn-action-primary mr-2 mb-2" id="btnRecordPayment" onclick="openPaymentModal()" data-toggle="modal" data-target="#paymentModal" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="fas fa-plus"></i> + Record Payment
                    </button>
                    <a href="{{ route('customers.ledger') }}" class="btn-action-secondary mr-2 mb-2">
                        <i class="fas fa-book text-primary"></i> Customer Ledger
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn-action-secondary mb-2">
                        <i class="fas fa-users text-secondary"></i> Customer List
                    </a>
                </div>
            </div>

            {{-- ── 2. Top Summary Stat Cards ── --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="cpay-stat-box border-success">
                        <div class="cpay-stat-label">Total Recoveries Received</div>
                        <div class="cpay-stat-number text-success">Rs. {{ number_format($totalReceivedAmount, 2) }}</div>
                        <div class="small text-muted mt-1">Total payments collected across all time</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="cpay-stat-box border-primary">
                        <div class="cpay-stat-label">Today's Collections</div>
                        <div class="cpay-stat-number text-primary">Rs. {{ number_format($todayRecoveries, 2) }}</div>
                        <div class="small text-muted mt-1">Recoveries collected on {{ date('d M Y') }}</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="cpay-stat-box border-warning">
                        <div class="cpay-stat-label">Total Transactions</div>
                        <div class="cpay-stat-number">{{ $totalPaymentsCount }}</div>
                        <div class="small text-muted mt-1">Payment receipts recorded</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="cpay-stat-box border-danger">
                        <div class="cpay-stat-label">Market Receivables (Baqaya)</div>
                        <div class="cpay-stat-number text-danger">Rs. {{ number_format($totalReceivables, 2) }}</div>
                        <div class="small text-muted mt-1">Total outstanding balance from customers</div>
                    </div>
                </div>
            </div>

            {{-- ── 3. Success Alert ── --}}
            @if (session()->has('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                    <i class="fas fa-check-circle fs-5 mr-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- ── 4. Filter Card ── --}}
            <div class="cpay-filter-card">
                <form method="GET" action="{{ route('customer.payments') }}">
                    <div class="row align-items-end g-3">
                        <div class="col-12 col-md-3">
                            <label class="cpay-filter-label"><i class="fas fa-user mr-1 text-success"></i> Customer</label>
                            <select name="customer_id" id="filter_customer_id" class="form-control form-select cpay-filter-input">
                                <option value="">All Customers</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->customer_name }} {{ !empty($c->phone) ? '('.$c->phone.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="cpay-filter-label"><i class="fas fa-wallet mr-1 text-success"></i> Payment Method</label>
                            <select name="payment_method" class="form-control form-select cpay-filter-input">
                                <option value="">All Payment Methods</option>
                                <option value="Cash in Hand" {{ request('payment_method') == 'Cash in Hand' ? 'selected' : '' }}>Cash in Hand</option>
                                <option value="Bank Transfer" {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer / Online</option>
                                <option value="Cheque" {{ request('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="EasyPaisa / JazzCash" {{ request('payment_method') == 'EasyPaisa / JazzCash' ? 'selected' : '' }}>EasyPaisa / JazzCash</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label class="cpay-filter-label"><i class="far fa-calendar-alt mr-1 text-success"></i> Start Date</label>
                            <input type="date" name="start_date" class="form-control cpay-filter-input" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label class="cpay-filter-label"><i class="far fa-calendar-alt mr-1 text-success"></i> End Date</label>
                            <input type="date" name="end_date" class="form-control cpay-filter-input" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                <button type="submit" class="btn btn-success btn-cpay-filter flex-fill shadow-sm font-weight-bold mr-1">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                @if(request()->hasAny(['customer_id', 'payment_method', 'start_date', 'end_date']))
                                    <a href="{{ route('customer.payments') }}" class="btn btn-outline-secondary btn-cpay-filter" title="Reset Filters">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ── 5. Main Payment Records Card ── --}}
            <div class="cpay-card">
                <div class="cpay-card-header">
                    <div class="font-weight-bold text-dark">
                        <i class="fas fa-list mr-1 text-muted"></i> Customer Payment &amp; Recovery Records
                    </div>
                    <div class="text-muted small">Showing {{ $payments->count() }} vouchers</div>
                </div>

                <div class="table-responsive">
                    <table class="table cpay-table datanew">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 70px;">Voucher #</th>
                                <th style="min-width: 110px;">Payment Date</th>
                                <th style="min-width: 220px;">Customer</th>
                                <th class="text-right" style="min-width: 140px;">Amount Received</th>
                                <th style="min-width: 140px;">Method</th>
                                <th style="min-width: 180px;">Remarks / Note</th>
                                <th style="min-width: 120px;">Received By</th>
                                <th class="text-right" style="width: 160px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $p)
                                <tr>
                                    {{-- Voucher ID --}}
                                    <td class="text-center">
                                        <span class="badge-id">#CP-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </td>

                                    {{-- Payment Date --}}
                                    <td>
                                        <span class="font-weight-bold text-dark text-nowrap">
                                            {{ \Carbon\Carbon::parse($p->payment_date)->format('d-M-Y') }}
                                        </span>
                                    </td>

                                    {{-- Customer --}}
                                    <td>
                                        <div class="font-weight-bold text-dark" style="font-size: 13.5px;">
                                            {{ $p->customer->customer_name ?? 'N/A' }}
                                            @if(!empty($p->customer->customer_name_urdu))
                                                <span class="text-muted small font-weight-normal ml-1">({{ $p->customer->customer_name_urdu }})</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">
                                            <span class="badge badge-light border">{{ $p->customer->customer_id ?? ('CUST-' . str_pad($p->customer_id, 4, '0', STR_PAD_LEFT)) }}</span>
                                            @if(!empty($p->customer->phone))
                                                <span class="ml-1"><i class="fas fa-phone-alt text-muted" style="font-size: 10px;"></i> {{ $p->customer->phone }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Amount Paid --}}
                                    <td class="text-right">
                                        <span class="badge-paid-amount">
                                            Rs. {{ number_format($p->amount, 2) }}
                                        </span>
                                    </td>

                                    {{-- Method --}}
                                    <td>
                                        <span class="badge-method">
                                            <i class="fas fa-money-check-alt text-muted mr-1"></i>{{ $p->payment_method ?: 'Cash in Hand' }}
                                        </span>
                                    </td>

                                    {{-- Note --}}
                                    <td>
                                        <span class="text-secondary small">
                                            {{ $p->note ?: '—' }}
                                        </span>
                                    </td>

                                    {{-- Received By --}}
                                    <td>
                                        <span class="small font-weight-semibold text-muted">
                                            <i class="fas fa-user-circle mr-1"></i>{{ $p->user->name ?? 'Admin' }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-right text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-info btn-tbl-action mr-1" 
                                            onclick='openReceiptModal({
                                                id: "{{ $p->id }}",
                                                voucher_no: "CP-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}",
                                                date: "{{ \Carbon\Carbon::parse($p->payment_date)->format('d-M-Y') }}",
                                                customer_name: "{{ addslashes($p->customer->customer_name ?? 'N/A') }}",
                                                customer_phone: "{{ $p->customer->phone ?? '' }}",
                                                customer_code: "{{ $p->customer->customer_id ?? ('CUST-' . str_pad($p->customer_id, 4, '0', STR_PAD_LEFT)) }}",
                                                amount: "{{ number_format($p->amount, 2) }}",
                                                method: "{{ addslashes($p->payment_method ?: 'Cash in Hand') }}",
                                                note: "{{ addslashes($p->note ?? '') }}",
                                                received_by: "{{ addslashes($p->user->name ?? 'Admin') }}"
                                            })' title="Print Payment Receipt Voucher">
                                            <i class="fas fa-print"></i> Slip
                                        </button>

                                        @if($p->customer_id)
                                            <a href="{{ route('customers.ledger', ['customer_id' => $p->customer_id]) }}" class="btn btn-sm btn-outline-primary btn-tbl-action mr-1" title="View Customer Ledger">
                                                <i class="fas fa-book"></i> Ledger
                                            </a>
                                        @endif

                                        <form action="{{ route('customer.payments.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment record? The customer receivable balance will be restored in ledger.');" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-tbl-action" title="Delete Payment">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="mb-2"><i class="fas fa-receipt fa-3x text-muted" style="opacity: 0.3;"></i></div>
                                        <div class="font-weight-bold" style="font-size: 16px;">No Customer Payments Recorded</div>
                                        <div class="small text-muted mt-1">Click "+ Record Payment" above to add customer recoveries.</div>
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

{{-- ── 6. Payment Modal ── --}}
<div class="modal fade cpay-modal" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('customer.payments.store') }}" method="POST" id="customerPaymentForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title font-weight-bold text-dark mb-0" id="paymentModalLabel">
                        <i class="fas fa-hand-holding-usd text-success mr-2"></i> Record Customer Payment &amp; Recovery
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" onclick="closePaymentModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    
                    {{-- Customer Select --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Select Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" id="modal_customer_id" class="form-control" required onchange="handleCustomerSelect(this)">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" data-balance="{{ $c->live_balance ?? 0 }}" data-name="{{ $c->customer_name }}">
                                    {{ $c->customer_name }} (Baqaya: Rs. {{ number_format($c->live_balance ?? 0, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Live Balance Info Box --}}
                    <div id="balanceBox" class="balance-box">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <div class="text-muted small font-weight-bold">Current Outstanding (Baqaya):</div>
                                <div id="balanceText" class="font-weight-bold text-danger fs-5" style="font-size: 18px;">Rs. 0.00</div>
                            </div>
                            <button type="button" id="btnPayFull" class="btn btn-sm btn-primary font-weight-bold px-2 py-1" onclick="payFullBalance()" style="font-size: 12px;">
                                <i class="fas fa-bolt mr-1"></i> Receive Full
                            </button>
                        </div>
                        <div id="projectedBox" style="display:none;" class="pt-2 border-top">
                            <span class="text-muted small">Projected Balance After Adjustment: </span>
                            <span id="projectedText" class="font-weight-bold text-dark">Rs. 0.00</span>
                        </div>
                    </div>

                    {{-- Payment Date & Nature --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="font-weight-bold text-dark small mb-1">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" id="modal_payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="font-weight-bold text-dark small mb-1">Adjustment Type <span class="text-danger">*</span></label>
                            <select name="adjustment_type" id="modal_adjustment_type" class="form-control" required onchange="calculateProjectedBalance()">
                                <option value="minus" selected>- Payment Received (Reduce Baqaya)</option>
                                <option value="plus">+ Debit Adjustment (Increase Outstanding)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Amount Paid --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Payment Amount (Rs.) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold bg-light">Rs.</span>
                            </div>
                            <input type="number" step="0.01" min="0.01" name="amount" id="modal_payment_amount" class="form-control form-control-lg font-weight-bold font-monospace" placeholder="0.00" required oninput="calculateProjectedBalance()">
                        </div>
                    </div>

                    {{-- Account & Payment Method --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="font-weight-bold text-dark small mb-1">Deposit To Account</label>
                            <select name="account_id" class="form-control">
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
                            <label class="font-weight-bold text-dark small mb-1">Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="Cash in Hand" selected>Cash in Hand</option>
                                <option value="Bank Transfer">Bank Transfer / Online</option>
                                <option value="Cheque">Cheque</option>
                                <option value="EasyPaisa / JazzCash">EasyPaisa / JazzCash</option>
                            </select>
                        </div>
                    </div>

                    {{-- Remarks / Note --}}
                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-dark small mb-1">Remarks / Receipt Reference</label>
                        <textarea name="note" id="modal_payment_note" rows="2" class="form-control" placeholder="e.g. Received cash via recovery officer (Book # 4, Slip # 105)"></textarea>
                    </div>

                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" onclick="closePaymentModal()">Cancel</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="fas fa-check mr-1"></i> Save Payment Voucher
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── 7. Printable Payment Receipt Modal ── --}}
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px;">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold text-dark" id="receiptModalLabel">
                    <i class="fas fa-receipt text-success mr-2"></i> Customer Payment Voucher
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="printableReceiptArea">
                <div class="receipt-slip-wrap">
                    <div class="text-center mb-3">
                        <h4 class="font-weight-bold text-dark mb-0">YOUNG FASHION</h4>
                        <p class="text-muted small mb-1">Premium Footwear &amp; Apparel ERP</p>
                        <span class="badge badge-success font-weight-bold px-3 py-1">OFFICIAL PAYMENT RECEIPT</span>
                    </div>

                    <hr class="my-2">

                    <div class="row small mb-3">
                        <div class="col-6">
                            <strong>Voucher #:</strong> <span id="recVoucherNo">—</span><br>
                            <strong>Date:</strong> <span id="recDate">—</span>
                        </div>
                        <div class="col-6 text-right">
                            <strong>Customer ID:</strong> <span id="recCustCode">—</span><br>
                            <strong>Received By:</strong> <span id="recStaff">—</span>
                        </div>
                    </div>

                    <div class="p-2 mb-3 bg-light rounded border">
                        <div class="small text-muted">Customer Name:</div>
                        <div class="font-weight-bold text-dark fs-5" id="recCustName">—</div>
                        <div class="small text-muted" id="recCustPhone"></div>
                    </div>

                    <table class="table table-sm table-bordered mb-3">
                        <tbody>
                            <tr>
                                <th class="bg-light" style="width: 40%;">Amount Received</th>
                                <td class="font-weight-bold text-success font-monospace" style="font-size: 16px;" id="recAmount">Rs. 0.00</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Payment Method</th>
                                <td id="recMethod">Cash in Hand</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Narration / Note</th>
                                <td id="recNote">—</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-end pt-4 mt-3 border-top text-center">
                        <div style="width: 40%;">
                            <div class="border-top pt-1 small text-muted font-weight-bold">Customer Signature</div>
                        </div>
                        <div style="width: 40%;">
                            <div class="border-top pt-1 small text-muted font-weight-bold">Authorized Signature</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary font-weight-bold" onclick="printReceiptSlip()">
                    <i class="fas fa-print mr-1"></i> Print Voucher
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentCustomerBalance = 0;

    function openPaymentModal() {
        $('#paymentModal').modal('show');
    }

    function closePaymentModal() {
        $('#paymentModal').modal('hide');
    }

    function handleCustomerSelect(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        if (!selectEl.value) {
            $('#balanceBox').slideUp();
            currentCustomerBalance = 0;
            return;
        }

        // Fast balance read from data attribute
        currentCustomerBalance = parseFloat(option.getAttribute('data-balance') || 0);
        updateBalanceBox(currentCustomerBalance);

        // Background AJAX sync to ensure exact balance
        $.ajax({
            url: '/customer/ledger/' + selectEl.value,
            method: 'GET',
            success: function(res) {
                if (res && res.closing_balance !== undefined) {
                    currentCustomerBalance = parseFloat(res.closing_balance);
                    updateBalanceBox(currentCustomerBalance);
                }
            }
        });
    }

    function updateBalanceBox(balance) {
        $('#balanceBox').slideDown();
        const formatted = 'Rs. ' + balance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (balance > 0) {
            $('#balanceText').html('<span class="text-danger">' + formatted + ' (Outstanding Receivable)</span>');
            $('#btnPayFull').show();
        } else if (balance < 0) {
            $('#balanceText').html('<span class="text-success">' + formatted.replace('-', '') + ' (Advance / Credit)</span>');
            $('#btnPayFull').hide();
        } else {
            $('#balanceText').html('<span class="text-muted">Rs. 0.00 (Settled Nil)</span>');
            $('#btnPayFull').hide();
        }
        calculateProjectedBalance();
    }

    function payFullBalance() {
        if (currentCustomerBalance > 0) {
            $('#modal_payment_amount').val(currentCustomerBalance.toFixed(2));
            calculateProjectedBalance();
        }
    }

    function calculateProjectedBalance() {
        const enteredAmount = parseFloat($('#modal_payment_amount').val()) || 0;
        const adjType = $('#modal_adjustment_type').val();

        if (enteredAmount <= 0) {
            $('#projectedBox').hide();
            return;
        }

        let projected = currentCustomerBalance;
        if (adjType === 'minus') {
            projected -= enteredAmount;
        } else {
            projected += enteredAmount;
        }

        $('#projectedBox').show();
        const formatted = 'Rs. ' + projected.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (projected > 0) {
            $('#projectedText').html('<span class="text-danger">' + formatted + ' (Remaining)</span>');
        } else if (projected === 0) {
            $('#projectedText').html('<span class="text-success">Rs. 0.00 (Fully Settled)</span>');
        } else {
            $('#projectedText').html('<span class="text-primary">' + formatted.replace('-', '') + ' (Advance)</span>');
        }
    }

    function openReceiptModal(data) {
        $('#recVoucherNo').text(data.voucher_no);
        $('#recDate').text(data.date);
        $('#recCustCode').text(data.customer_code);
        $('#recCustName').text(data.customer_name);
        $('#recCustPhone').text(data.customer_phone ? 'Phone: ' + data.customer_phone : '');
        $('#recAmount').text('Rs. ' + data.amount);
        $('#recMethod').text(data.method);
        $('#recNote').text(data.note || '—');
        $('#recStaff').text(data.received_by);

        $('#receiptModal').modal('show');
    }

    function printReceiptSlip() {
        window.print();
    }

    $(document).ready(function() {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('.datanew')) {
            $('.datanew').DataTable({
                "pageLength": 25,
                "ordering": true,
                "order": [[0, "desc"]]
            });
        }
    });
</script>
@endpush
