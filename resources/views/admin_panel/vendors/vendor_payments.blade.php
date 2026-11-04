@extends('admin_panel.layout.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .vpay-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        padding-bottom: 40px;
    }

    /* Page Header */
    .vpay-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 22px 28px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .vpay-hdr-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #ecfdf5;
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        border: 1px solid #a7f3d0;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.08);
    }
    .vpay-title {
        font-weight: 800;
        font-size: 1.45rem;
        color: #0f172a;
        margin-bottom: 3px;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .vpay-sub {
        font-size: 0.86rem;
        color: #64748b;
    }

    /* Buttons */
    .btn-pay-vendor {
        background: #059669;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 10px 20px;
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .btn-pay-vendor:hover {
        background: #047857;
        box-shadow: 0 6px 16px rgba(5, 150, 105, 0.35);
        transform: translateY(-1px);
    }

    .btn-vpay-secondary {
        background: #ffffff;
        color: #475569 !important;
        border: 1.5px solid #cbd5e1;
        padding: 9px 16px;
        font-size: 0.86rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-vpay-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }

    /* Stat Cards */
    .vpay-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 22px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
        transition: transform 0.15s ease;
    }
    .vpay-stat-card:hover {
        transform: translateY(-2px);
    }
    .vpay-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .vpay-stat-val {
        font-weight: 800;
        font-size: 1.3rem;
        color: #0f172a;
        line-height: 1.2;
    }
    .vpay-stat-lbl {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        margin-top: 2px;
    }

    /* Card & Table */
    .vpay-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }
    .vpay-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .vpay-table-wrap {
        overflow-x: auto;
        padding: 18px 24px;
    }
    .datanew thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 18px;
    }
    .datanew tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .datanew tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Vendor Avatar */
    .vendor-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 800;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 16px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .modal-header {
        border-bottom: 1px solid #e2e8f0;
        padding: 18px 24px;
        background: #f8fafc;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }
    .modal-body {
        padding: 24px;
    }
    .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 16px 24px;
        background: #f8fafc;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }

    .balance-preview-box {
        background: #eff6ff;
        border: 1.5px dashed #93c5fd;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 16px;
        display: none;
    }
</style>

<div class="vpay-page container-fluid px-3 px-md-4 py-4">

    {{-- Top Header Row --}}
    <div class="vpay-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="vpay-hdr-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <h3 class="vpay-title">Vendor Payments (Supplier Disbursements)</h3>
                <div class="vpay-sub">Record payments made to suppliers, clear payables, and keep vendor ledger balanced</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button class="btn-pay-vendor" data-bs-toggle="modal" data-bs-target="#paymentModal" onclick="clearPaymentForm()">
                <i class="fas fa-plus-circle"></i> Pay Vendor (Nayi Payment)
            </button>
            <a href="{{ route('vendors-ledger') }}" class="btn-vpay-secondary">
                <i class="fas fa-book text-primary"></i> Vendors Ledger
            </a>
            <a href="{{ route('Purchase.home') }}" class="btn-vpay-secondary">
                <i class="fas fa-arrow-left"></i> Purchases
            </a>
        </div>
    </div>

    {{-- Stat Cards Row --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Paid --}}
        <div class="col-12 col-md-4">
            <div class="vpay-stat-card">
                <div class="vpay-stat-icon" style="background: #ecfdf5; color: #059669;">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <div class="vpay-stat-val text-success">Rs. {{ number_format($totalPaidAmount, 2) }}</div>
                    <div class="vpay-stat-lbl">Total Payments Disbursed</div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Transactions --}}
        <div class="col-12 col-md-4">
            <div class="vpay-stat-card">
                <div class="vpay-stat-icon" style="background: #eef2ff; color: #4f46e5;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="vpay-stat-val">{{ $totalPaymentsCount }}</div>
                    <div class="vpay-stat-lbl">Total Payment Records</div>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Outstanding Payables --}}
        <div class="col-12 col-md-4">
            <div class="vpay-stat-card">
                <div class="vpay-stat-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-scale-unbalanced"></i>
                </div>
                <div>
                    <div class="vpay-stat-val text-danger">Rs. {{ number_format($totalPayables, 2) }}</div>
                    <div class="vpay-stat-lbl">Total Outstanding Payables (Cr)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4 shadow-sm">
            <i class="fas fa-check-circle fs-5"></i>
            <strong>Success!</strong> {{ session('success') }}
        </div>
    @endif

    {{-- Table Card --}}
    <div class="vpay-card">
        <div class="vpay-card-header">
            <div class="fw-bold text-dark fs-6">
                <i class="fas fa-list me-1.5 text-muted"></i> All Vendor Payment Records
            </div>
            <div class="text-muted small">Showing {{ $payments->count() }} entries</div>
        </div>

        <div class="vpay-table-wrap">
            <table class="table table-hover align-middle datanew">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">ID</th>
                        <th style="min-width: 120px;">Payment Date</th>
                        <th style="min-width: 220px;">Vendor / Supplier</th>
                        <th class="text-end" style="min-width: 150px;">Amount Paid</th>
                        <th style="min-width: 130px;">Method / Account</th>
                        <th style="min-width: 140px;">Type</th>
                        <th style="min-width: 200px;">Note / Narration</th>
                        <th class="text-end" style="width: 130px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $key => $pay)
                        @php
                            $vInitial = strtoupper(substr($pay->vendor->name ?? 'V', 0, 1));
                        @endphp
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                    #{{ $pay->id }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1.5 font-monospace text-secondary" style="font-size: 0.85rem;">
                                    <i class="fas fa-calendar-day text-muted"></i>
                                    <strong>{{ \Carbon\Carbon::parse($pay->payment_date)->format('d-M-Y') }}</strong>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="vendor-avatar">{{ $vInitial }}</div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                                            {{ $pay->vendor->name ?? 'N/A' }}
                                        </div>
                                        @if(!empty($pay->vendor->phone))
                                            <div class="text-muted small">
                                                <i class="fas fa-phone-alt me-1 text-muted" style="font-size: 0.72rem;"></i>{{ $pay->vendor->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 font-monospace fs-6 fw-bold">
                                    Rs. {{ number_format($pay->amount, 2) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fas fa-wallet text-secondary me-1"></i>{{ $pay->payment_method ?: 'Cash' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                    <i class="fas fa-arrow-down me-1"></i>Vendor Paid
                                </span>
                            </td>
                            <td class="text-secondary small">
                                {{ $pay->note ?: '—' }}
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @if($pay->vendor_id)
                                        <a href="{{ route('vendor.ledger', $pay->vendor_id) }}" class="btn btn-sm btn-outline-primary px-2 py-1" title="View Vendor Ledger">
                                            <i class="fas fa-book"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('vendor.payments.destroy', $pay->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment record? Ledger balance will be restored.');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Delete Payment">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-receipt fa-3x text-muted opacity-50"></i></div>
                                <div class="fw-bold fs-6">No Vendor Payments Recorded Yet</div>
                                <div class="small text-muted mt-1">Click "Pay Vendor" above to record a payment made to a supplier.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ── Payment Modal ── -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('vendor.payments.store') }}" method="POST" id="vendorPaymentForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="paymentModalLabel">
                        <i class="fas fa-money-bill-transfer text-success"></i> Record Vendor Payment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    
                    {{-- 1. Vendor Selection --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.88rem;">Select Vendor / Supplier <span class="text-danger">*</span></label>
                        <select name="vendor_id" id="vendor_id" class="form-select" required>
                            <option value="">-- Choose Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" data-balance="{{ $vendor->live_balance ?? 0 }}">
                                    {{ $vendor->name }} (Current: Rs. {{ number_format($vendor->live_balance ?? 0, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dynamic Balance Info Box --}}
                    <div id="balanceBox" class="balance-preview-box">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small fw-semibold">Current Outstanding Balance:</div>
                                <div id="balanceText" class="fw-black text-danger fs-5">Rs. 0.00 (Payable)</div>
                            </div>
                            <button type="button" id="btnPayFull" class="btn btn-sm btn-primary fw-bold px-2.5 py-1" style="font-size: 0.78rem;">
                                <i class="fas fa-bolt me-1"></i> Pay Full Balance
                            </button>
                        </div>
                    </div>

                    {{-- 2. Payment Date & Type --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Payment Nature <span class="text-danger">*</span></label>
                            <select name="adjustment_type" class="form-select" required>
                                <option value="minus" selected>Vendor Payment (Balance Kam Hoga)</option>
                                <option value="plus">Refund / Adjustment (Balance Barhega)</option>
                            </select>
                        </div>
                    </div>

                    {{-- 3. Amount Paid --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.88rem;">Amount Paid (Rs.) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold bg-light">Rs.</span>
                            <input type="number" step="0.01" min="0.01" name="amount" id="payment_amount" class="form-control form-control-lg fw-bold font-monospace" placeholder="0.00" required>
                        </div>
                    </div>

                    {{-- 4. Disbursed From (Account) & Method --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Paid From Account</label>
                            <select name="account_id" class="form-select">
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
                            <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="Cash" selected>Cash in Hand</option>
                                <option value="Bank Transfer">Bank Transfer / Online</option>
                                <option value="Cheque">Cheque</option>
                                <option value="EasyPaisa / JazzCash">EasyPaisa / JazzCash</option>
                            </select>
                        </div>
                    </div>

                    {{-- 5. Note / Narration --}}
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Remarks / Narration</label>
                        <textarea name="note" id="payment_note" rows="2" class="form-control" placeholder="e.g. Paid cash against purchase PUR-001 or weekly settlement"></textarea>
                    </div>

                </div>

                <div class="modal-footer d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fas fa-check-circle me-1"></i> Save &amp; Disburse Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentVendorBalance = 0;

    function clearPaymentForm() {
        document.getElementById('vendorPaymentForm').reset();
        document.getElementById('payment_date').value = "{{ date('Y-m-d') }}";
        $('#balanceBox').hide();
        currentVendorBalance = 0;
    }

    $(document).ready(function () {
        $('#vendor_id').on('change', function () {
            const vendorId = $(this).val();
            if (vendorId) {
                // Fetch dynamic balance via ajax
                $.ajax({
                    url: '/get-vendor-balance/' + vendorId,
                    type: 'GET',
                    success: function (res) {
                        currentVendorBalance = parseFloat(res.closing_balance || 0);
                        $('#balanceText').text(res.formatted);
                        if (currentVendorBalance > 0) {
                            $('#balanceText').removeClass('text-success').addClass('text-danger');
                            $('#btnPayFull').show();
                        } else {
                            $('#balanceText').removeClass('text-danger').addClass('text-success');
                            $('#btnPayFull').hide();
                        }
                        $('#balanceBox').slideDown();
                    },
                    error: function () {
                        const fallbackBal = parseFloat($('#vendor_id option:selected').data('balance') || 0);
                        currentVendorBalance = fallbackBal;
                        $('#balanceText').text('Rs. ' + fallbackBal.toFixed(2));
                        $('#balanceBox').slideDown();
                    }
                });
            } else {
                $('#balanceBox').slideUp();
                currentVendorBalance = 0;
            }
        });

        $('#btnPayFull').on('click', function() {
            if (currentVendorBalance > 0) {
                $('#payment_amount').val(currentVendorBalance.toFixed(2));
            }
        });
    });
</script>
@endpush
