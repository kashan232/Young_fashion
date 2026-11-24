@extends('admin_panel.layout.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .vendor-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        padding-bottom: 40px;
    }

    /* Page Header */
    .vendor-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        border-radius: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .vendor-title {
        font-weight: 800;
        font-size: 1.35rem;
        color: #0f172a;
        margin-bottom: 2px;
        letter-spacing: -0.02em;
    }
    .vendor-sub {
        font-size: 0.82rem;
        color: #64748b;
    }

    /* Action Buttons Hub */
    .vendor-action-hub {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-vendor-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        border: none;
        padding: 9px 18px;
        font-size: 0.86rem;
        font-weight: 600;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.22);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-vendor-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
    }
    .btn-vendor-sub {
        background: #ffffff;
        color: #475569 !important;
        border: 1.5px solid #cbd5e1;
        padding: 9px 16px;
        font-size: 0.84rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-vendor-sub:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }

    /* Stat Cards */
    .vendor-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }
    .vendor-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .vendor-stat-val {
        font-weight: 800;
        font-size: 1.25rem;
        color: #0f172a;
        line-height: 1.2;
    }
    .vendor-stat-lbl {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Card & Table */
    .vendor-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }
    .vendor-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .vendor-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .datanew {
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    .datanew thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }
    .datanew tbody td {
        padding: 14px 20px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .datanew tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Avatar Circle */
    .vendor-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #eef2ff;
        color: #4f46e5;
        font-weight: 800;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Mobile Cards View */
    .mobile-vendor-cards {
        display: none;
        padding: 14px;
        flex-direction: column;
        gap: 12px;
    }
    .vendor-mcard {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
    }
    .vendor-mcard-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .vendor-mcard-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f172a;
    }
    .vendor-mcard-details {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 0.82rem;
        color: #475569;
        padding: 8px 0;
    }
    .vendor-mcard-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #e2e8f0;
    }

    /* Responsive Breakpoints */
    @media (max-width: 768px) {
        .vendor-header {
            padding: 16px;
        }
        .vendor-action-hub {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .btn-vendor-primary, .btn-vendor-sub {
            width: 100%;
            justify-content: center;
            height: 40px;
            font-size: 0.78rem;
            padding: 6px;
        }
        .vendor-table-wrap {
            display: none !important;
        }
        .mobile-vendor-cards {
            display: flex;
        }
    }

    /* Status Toggle Pill */
    .status-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        border: 1px solid transparent;
        transition: all 0.15s ease;
        line-height: 1.2;
    }
    button.status-toggle-btn {
        cursor: pointer;
    }
    button.status-toggle-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
    }
    .status-toggle-btn.is-active {
        background-color: #ecfdf5;
        color: #059669;
        border-color: #a7f3d0;
    }
    .status-toggle-btn.is-deactive {
        background-color: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .is-active .status-dot {
        background-color: #10b981;
    }
    .is-deactive .status-dot {
        background-color: #ef4444;
    }

    /* iOS Switch for Action Column */
    .vendor-switch-wrapper {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        user-select: none;
        vertical-align: middle;
    }
    .vendor-switch {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 22px;
        margin-bottom: 0;
        cursor: pointer;
    }
    .vendor-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .vendor-switch-slider {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ef4444;
        transition: .25s ease-in-out;
        border-radius: 22px;
    }
    .vendor-switch-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: #ffffff;
        transition: .25s ease-in-out;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.25);
    }
    .vendor-switch input:checked + .vendor-switch-slider {
        background-color: #10b981;
    }
    .vendor-switch input:checked + .vendor-switch-slider:before {
        transform: translateX(20px);
    }
    .vendor-switch input:disabled + .vendor-switch-slider {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .vendor-switch-text {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        min-width: 46px;
        text-align: left;
    }
    .vendor-switch-text.text-active {
        color: #059669;
    }
    .vendor-switch-text.text-deactive {
        color: #dc2626;
    }

    /* Modal Form Alignment (Bootstrap 4) */
    .vendor-modal-form .form-group {
        margin-bottom: 16px;
    }
    .vendor-modal-form .form-group label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 6px;
    }
    .vendor-modal-form .form-control {
        display: block;
        width: 100%;
        height: 42px;
        padding: 8px 12px;
        font-size: 0.9rem;
        color: #0f172a;
        background-color: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        box-shadow: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .vendor-modal-form .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        outline: none;
    }
    .vendor-modal-form textarea.form-control {
        height: 90px;
        resize: vertical;
    }

    /* Quick Action Button Group */
    .vendor-action-group {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .qa-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.82rem;
        border: none;
        color: #ffffff !important;
        text-decoration: none !important;
        transition: all 0.15s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        cursor: pointer;
        padding: 0;
    }
    .qa-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.18);
        filter: brightness(1.08);
    }
    .qa-btn-profile {
        background: #6366f1; /* Indigo / Purple */
    }
    .qa-btn-edit {
        background: #3b82f6; /* Blue */
    }
    .qa-btn-delete {
        background: #ef4444; /* Red */
    }
    .qa-btn-ledger {
        background: #f59e0b; /* Amber / Yellow */
    }
    .qa-btn-payments {
        background: #10b981; /* Green */
    }

    /* Profile Modal Details Styling */
    .vprofile-avatar {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }
    .vprofile-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 0.88rem;
    }
    .vprofile-info-row:last-child {
        border-bottom: none;
    }
    .vprofile-info-label {
        font-weight: 600;
        color: #64748b;
    }
    .vprofile-info-value {
        font-weight: 700;
        color: #0f172a;
        text-align: right;
    }
</style>

<div class="vendor-page container-fluid px-3 px-md-4 pt-3">
    
    {{-- Header Row --}}
    <div class="vendor-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <div>
            <h3 class="vendor-title"><i class="fas fa-truck-loading text-primary mr-2"></i>Vendor Management</h3>
            <div class="vendor-sub">Manage suppliers, opening balances, ledgers and payments</div>
        </div>
        <div class="vendor-action-hub mt-3 mt-md-0">
            @can('vendors.create')
                <button type="button" class="btn-vendor-primary" id="btnAddVendor">
                    <i class="fas fa-plus"></i> Add Vendor
                </button>
            @endcan
            <a href="{{ url('vendors-ledger') }}" class="btn-vendor-sub">
                <i class="fas fa-book mr-1"></i> Ledger
            </a>
            <a href="{{ route('vendor.payments') }}" class="btn-vendor-sub">
                <i class="fas fa-money-check-alt mr-1"></i> Payments
            </a>
            <a href="{{ url('vendor/bilties') }}" class="btn-vendor-sub">
                <i class="fas fa-shipping-fast mr-1"></i> Bilty
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
            <div class="vendor-stat-card">
                <div class="vendor-stat-icon"><i class="fas fa-store"></i></div>
                <div>
                    <div class="vendor-stat-val">{{ count($vendors) }}</div>
                    <div class="vendor-stat-lbl">Total Vendors</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
            <div class="vendor-stat-card">
                <div class="vendor-stat-icon" style="background: #ecfdf5; color: #059669;"><i class="fas fa-user-check"></i></div>
                <div>
                    <div class="vendor-stat-val" id="activeVendorsCount">{{ $activeVendorsCount ?? 0 }}</div>
                    <div class="vendor-stat-lbl">Active Vendors</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
            <div class="vendor-stat-card" style="border-left: 4px solid #ef4444;">
                <div class="vendor-stat-icon" style="background: #fef2f2; color: #dc2626;"><i class="fas fa-calendar-times"></i></div>
                <div>
                    <div class="vendor-stat-val text-danger">Rs. {{ number_format((float) ($dueThisWeekAmount ?? 0), 2) }}</div>
                    <div class="vendor-stat-lbl font-weight-bold text-danger">
                        DUE PAYMENTS <span class="badge badge-danger ml-1" style="font-size: 0.68rem;">{{ $dueThisWeekCount ?? 0 }} this week</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="vendor-stat-card">
                <div class="vendor-stat-icon" style="background: #fff7ed; color: #ea580c;"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <div class="vendor-stat-val">Rs. {{ number_format((float) ($totalPayables ?? 0), 2) }}</div>
                    <div class="vendor-stat-lbl">Total Payables</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="vendor-card">
        <div class="vendor-card-header">
            <div class="fw-bold text-dark"><i class="fas fa-list me-1 text-muted"></i> All Vendors</div>
            <div class="text-muted small">Showing {{ count($vendors) }} entries</div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mx-4 mt-3 mb-0">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Desktop Table View --}}
        <div class="vendor-table-wrap p-3">
            <table class="table table-hover align-middle datanew">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th class="text-start">Vendor Name</th>
                        <th class="text-start">Category</th>
                        <th class="text-start">Contact Phone</th>
                        <th class="text-start">Status</th>
                        <th class="text-start">Balance</th>
                        <th class="text-start">Payment Due</th>
                        <th class="text-start">Address</th>
                        <th class="text-start">Date</th>
                        <th class="text-end pe-4" style="width: 250px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vendors as $key => $v)
                        @php
                            $isActive = (int) ($v->status ?? 1) === 1;
                            $liveBalance = (float) ($v->live_balance ?? 0);
                        @endphp
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="vendor-avatar">
                                        {{ strtoupper(substr($v->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold text-dark">{{ $v->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if ($v->category)
                                    <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1">{{ $v->category }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($v->phone)
                                    <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-phone-alt me-1 text-muted"></i>{{ $v->phone }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @can('vendors.edit')
                                    <button type="button"
                                        class="status-toggle-btn {{ $isActive ? 'is-active' : 'is-deactive' }}"
                                        data-id="{{ $v->id }}"
                                        title="Click to toggle Active / Deactive">
                                        <span class="status-dot"></span>
                                        <span class="status-text">{{ $isActive ? 'Active' : 'Deactive' }}</span>
                                    </button>
                                @else
                                    <span class="status-toggle-btn {{ $isActive ? 'is-active' : 'is-deactive' }}">
                                        <span class="status-dot"></span>
                                        <span class="status-text">{{ $isActive ? 'Active' : 'Deactive' }}</span>
                                    </span>
                                @endcan
                            </td>
                            <td class="fw-bold {{ $liveBalance > 0 ? 'text-danger' : 'text-success' }}">
                                Rs. {{ number_format($liveBalance, 2) }}
                                @if (!empty($v->credit_limit) && $v->credit_limit > 0)
                                    <small class="text-muted d-block font-weight-normal" style="font-size: 0.72rem;">
                                        Limit: Rs. {{ number_format($v->credit_limit, 0) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if (!empty($v->payment_due_date) || !empty($v->payment_day))
                                    @php
                                        $isDueThisWeek = !empty($v->is_due_this_week) && $liveBalance > 0;
                                    @endphp
                                    <div class="d-flex flex-column" style="gap: 2px;">
                                        @if (!empty($v->payment_due_date))
                                            <span class="badge {{ $isDueThisWeek ? 'badge-danger' : 'badge-light border' }} text-nowrap px-2 py-1" style="font-size: 0.76rem;">
                                                <i class="far fa-calendar-alt mr-1"></i>{{ \Carbon\Carbon::parse($v->payment_due_date)->format('d M, Y') }}
                                            </span>
                                        @endif
                                        @if (!empty($v->payment_day))
                                            <span class="badge badge-info text-nowrap px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="fas fa-redo-alt mr-1"></i>Every {{ $v->payment_day }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-secondary small">{{ Str::limit($v->address, 35) ?: '—' }}</td>
                            <td class="text-secondary small text-nowrap">
                                {{ $v->created_at ? $v->created_at->format('d M, Y') : '—' }}
                            </td>
                            <td class="text-end pe-4 text-nowrap">
                                <div class="d-inline-flex align-items-center" style="gap: 8px;">
                                    {{-- Active / Deactive Switch in Action --}}
                                    @can('vendors.edit')
                                        <div class="vendor-switch-wrapper mr-1" title="Toggle Active / Deactive">
                                            <label class="vendor-switch">
                                                <input type="checkbox"
                                                    class="vendor-status-toggle"
                                                    data-id="{{ $v->id }}"
                                                    {{ $isActive ? 'checked' : '' }}>
                                                <span class="vendor-switch-slider"></span>
                                            </label>
                                            <span class="vendor-switch-text {{ $isActive ? 'text-active' : 'text-deactive' }}">
                                                {{ $isActive ? 'Active' : 'Deactive' }}
                                            </span>
                                        </div>
                                    @endcan

                                    {{-- Quick Action Short Keys (1: View Profile, 2: Edit, 3: Delete, 4: Ledger, 5: Payments) --}}
                                    <div class="vendor-action-group">
                                        {{-- 1. View Profile --}}
                                        <button type="button" class="qa-btn qa-btn-profile btn-view-profile"
                                            data-id="{{ $v->id }}"
                                            data-name="{{ $v->name }}"
                                            data-category="{{ $v->category ?? '—' }}"
                                            data-phone="{{ $v->phone ?? '—' }}"
                                            data-status="{{ $isActive ? 'Active' : 'Deactive' }}"
                                            data-balance="Rs. {{ number_format($liveBalance, 2) }}"
                                            data-due="{{ !empty($v->payment_due_date) ? \Carbon\Carbon::parse($v->payment_due_date)->format('d M, Y') : (!empty($v->payment_day) ? 'Every '.$v->payment_day : '—') }}"
                                            data-address="{{ $v->address ?? '—' }}"
                                            data-credit-limit="{{ (!empty($v->credit_limit) && $v->credit_limit > 0) ? 'Rs. ' . number_format($v->credit_limit, 2) : '—' }}"
                                            data-payment-terms="{{ $v->payment_terms ?: '—' }}"
                                            data-bank-name="{{ $v->bank_name ?: '' }}"
                                            data-account-title="{{ $v->account_title ?: '' }}"
                                            data-account-number="{{ $v->account_number ?: '' }}"
                                            data-bank-details="{{ $v->bank_details ?: '' }}"
                                            data-date="{{ $v->created_at ? $v->created_at->format('d M, Y') : '—' }}"
                                            data-ledger-url="{{ route('vendor.ledger', $v->id) }}"
                                            data-payments-url="{{ route('vendor.payments') }}?vendor_id={{ $v->id }}"
                                            title="View Profile">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        {{-- 2. Edit --}}
                                        @can('vendors.edit')
                                            <button type="button" class="qa-btn qa-btn-edit edit-btn" data-id="{{ $v->id }}" title="Edit Vendor">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        @endcan

                                        {{-- 3. Delete --}}
                                        @can('vendors.delete')
                                            <button type="button" class="qa-btn qa-btn-delete delete-btn"
                                                data-id="{{ $v->id }}"
                                                data-url="{{ url('vendor/delete/' . $v->id) }}"
                                                data-msg="Are you sure you want to delete this vendor?"
                                                data-method="get"
                                                onclick="logoutAndDeleteFunction(this)"
                                                title="Delete Vendor">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endcan

                                        {{-- 4. View Vendor Ledger Details --}}
                                        <a href="{{ route('vendor.ledger', $v->id) }}" class="qa-btn qa-btn-ledger" title="View Vendor Ledger Details">
                                            <i class="fas fa-book-open"></i>
                                        </a>

                                        {{-- 5. View Payments History --}}
                                        <a href="{{ route('vendor.payments') }}?vendor_id={{ $v->id }}" class="qa-btn qa-btn-payments" title="View Payments History">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <!-- Hidden inputs for reliable JS edit -->
                            <input type="hidden" class="v-name" value="{{ $v->name }}">
                            <input type="hidden" class="v-category" value="{{ $v->category }}">
                            <input type="hidden" class="v-status" value="{{ $isActive ? 1 : 0 }}">
                            <input type="hidden" class="v-phone" value="{{ $v->phone }}">
                            <input type="hidden" class="v-balance" value="{{ $v->opening_balance }}">
                            <input type="hidden" class="v-due-date" value="{{ $v->payment_due_date }}">
                            <input type="hidden" class="v-due-day" value="{{ $v->payment_day }}">
                            <input type="hidden" class="v-credit-limit" value="{{ $v->credit_limit }}">
                            <input type="hidden" class="v-payment-terms" value="{{ $v->payment_terms }}">
                            <input type="hidden" class="v-bank-name" value="{{ $v->bank_name }}">
                            <input type="hidden" class="v-account-title" value="{{ $v->account_title }}">
                            <input type="hidden" class="v-account-number" value="{{ $v->account_number }}">
                            <input type="hidden" class="v-bank-details" value="{{ $v->bank_details }}">
                            <input type="hidden" class="v-address" value="{{ $v->address }}">
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards View (< 768px) --}}
        <div class="mobile-vendor-cards">
            @foreach ($vendors as $key => $v)
                @php
                    $isActive = (int) ($v->status ?? 1) === 1;
                    $liveBalance = (float) ($v->live_balance ?? 0);
                @endphp
                <div class="vendor-mcard">
                    <div class="vendor-mcard-hdr">
                        <div class="d-flex align-items-center gap-2">
                            <div class="vendor-avatar">
                                {{ strtoupper(substr($v->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="vendor-mcard-title">{{ $v->name }}</div>
                                @if ($v->category)
                                    <span class="badge bg-light text-primary border border-primary-subtle">{{ $v->category }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            @can('vendors.edit')
                                <button type="button"
                                    class="status-toggle-btn mr-1 {{ $isActive ? 'is-active' : 'is-deactive' }}"
                                    data-id="{{ $v->id }}"
                                    title="Click to toggle Active / Deactive">
                                    <span class="status-dot"></span>
                                    <span class="status-text">{{ $isActive ? 'Active' : 'Deactive' }}</span>
                                </button>
                            @else
                                <span class="status-toggle-btn mr-1 {{ $isActive ? 'is-active' : 'is-deactive' }}">
                                    <span class="status-dot"></span>
                                    <span class="status-text">{{ $isActive ? 'Active' : 'Deactive' }}</span>
                                </span>
                            @endcan
                            <span class="badge bg-light text-dark border">#{{ $key + 1 }}</span>
                        </div>
                    </div>

                    <div class="vendor-mcard-details">
                        @if ($v->phone)
                            <div><i class="fas fa-phone me-2 text-primary"></i> <strong>Phone:</strong> {{ $v->phone }}</div>
                        @endif
                        <div><i class="fas fa-wallet me-2 {{ $liveBalance > 0 ? 'text-danger' : 'text-success' }}"></i> <strong>Balance:</strong> <span class="{{ $liveBalance > 0 ? 'text-danger' : 'text-success' }} fw-bold">Rs. {{ number_format($liveBalance, 2) }}</span> @if(!empty($v->credit_limit) && $v->credit_limit > 0) <small class="text-muted ms-1">(Limit: Rs. {{ number_format($v->credit_limit, 0) }})</small> @endif</div>
                        @if (!empty($v->payment_due_date) || !empty($v->payment_day))
                            <div><i class="far fa-clock me-2 text-danger"></i> <strong>Due Payment:</strong> 
                                @if (!empty($v->payment_due_date))
                                    <span class="badge {{ (!empty($v->is_due_this_week) && $liveBalance > 0) ? 'badge-danger' : 'badge-light border' }}">{{ \Carbon\Carbon::parse($v->payment_due_date)->format('d M, Y') }}</span>
                                @endif
                                @if (!empty($v->payment_day))
                                    <span class="badge badge-info">Every {{ $v->payment_day }}</span>
                                @endif
                            </div>
                        @endif
                        @if ($v->address)
                            <div><i class="fas fa-map-marker-alt me-2 text-muted"></i> <strong>Address:</strong> {{ Str::limit($v->address, 50) }}</div>
                        @endif
                        <div><i class="fas fa-calendar-alt me-2 text-muted"></i> <strong>Date:</strong> {{ $v->created_at ? $v->created_at->format('d M, Y') : '—' }}</div>
                    </div>

                    <div class="vendor-mcard-actions">
                        @can('vendors.edit')
                            <div class="d-flex align-items-center justify-content-center p-2 mb-2 bg-light rounded" style="grid-column: 1 / -1;">
                                <div class="vendor-switch-wrapper">
                                    <label class="vendor-switch mb-0">
                                        <input type="checkbox"
                                            class="vendor-status-toggle"
                                            data-id="{{ $v->id }}"
                                            {{ $isActive ? 'checked' : '' }}>
                                        <span class="vendor-switch-slider"></span>
                                    </label>
                                    <span class="vendor-switch-text {{ $isActive ? 'text-active' : 'text-deactive' }}">
                                        {{ $isActive ? 'Active' : 'Deactive' }}
                                    </span>
                                </div>
                            </div>
                        @endcan

                        <div class="d-flex align-items-center justify-content-center" style="grid-column: 1 / -1;">
                            <div class="vendor-action-group">
                                {{-- 1. View Profile --}}
                                <button type="button" class="qa-btn qa-btn-profile btn-view-profile"
                                    data-id="{{ $v->id }}"
                                    data-name="{{ $v->name }}"
                                    data-category="{{ $v->category ?? '—' }}"
                                    data-phone="{{ $v->phone ?? '—' }}"
                                    data-status="{{ $isActive ? 'Active' : 'Deactive' }}"
                                    data-balance="Rs. {{ number_format($liveBalance, 2) }}"
                                    data-due="{{ !empty($v->payment_due_date) ? \Carbon\Carbon::parse($v->payment_due_date)->format('d M, Y') : (!empty($v->payment_day) ? 'Every '.$v->payment_day : '—') }}"
                                    data-address="{{ $v->address ?? '—' }}"
                                    data-credit-limit="{{ (!empty($v->credit_limit) && $v->credit_limit > 0) ? 'Rs. ' . number_format($v->credit_limit, 2) : '—' }}"
                                    data-payment-terms="{{ $v->payment_terms ?: '—' }}"
                                    data-bank-name="{{ $v->bank_name ?: '' }}"
                                    data-account-title="{{ $v->account_title ?: '' }}"
                                    data-account-number="{{ $v->account_number ?: '' }}"
                                    data-bank-details="{{ $v->bank_details ?: '' }}"
                                    data-date="{{ $v->created_at ? $v->created_at->format('d M, Y') : '—' }}"
                                    data-ledger-url="{{ route('vendor.ledger', $v->id) }}"
                                    data-payments-url="{{ route('vendor.payments') }}?vendor_id={{ $v->id }}"
                                    title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </button>

                                {{-- 2. Edit --}}
                                @can('vendors.edit')
                                    <button type="button" class="qa-btn qa-btn-edit edit-btn" data-id="{{ $v->id }}" title="Edit Vendor">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                @endcan

                                {{-- 3. Delete --}}
                                @can('vendors.delete')
                                    <button type="button" class="qa-btn qa-btn-delete delete-btn"
                                        data-id="{{ $v->id }}"
                                        data-url="{{ url('vendor/delete/' . $v->id) }}"
                                        data-msg="Are you sure you want to delete this vendor?"
                                        data-method="get"
                                        onclick="logoutAndDeleteFunction(this)"
                                        title="Delete Vendor">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan

                                {{-- 4. View Vendor Ledger Details --}}
                                <a href="{{ route('vendor.ledger', $v->id) }}" class="qa-btn qa-btn-ledger" title="View Vendor Ledger Details">
                                    <i class="fas fa-book-open"></i>
                                </a>

                                {{-- 5. View Payments History --}}
                                <a href="{{ route('vendor.payments') }}?vendor_id={{ $v->id }}" class="qa-btn qa-btn-payments" title="View Payments History">
                                    <i class="fas fa-money-bill-wave"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden inputs for mobile JS edit -->
                    <input type="hidden" class="v-name" value="{{ $v->name }}">
                    <input type="hidden" class="v-category" value="{{ $v->category }}">
                    <input type="hidden" class="v-status" value="{{ $isActive ? 1 : 0 }}">
                    <input type="hidden" class="v-phone" value="{{ $v->phone }}">
                    <input type="hidden" class="v-balance" value="{{ $v->opening_balance }}">
                    <input type="hidden" class="v-due-date" value="{{ $v->payment_due_date }}">
                    <input type="hidden" class="v-due-day" value="{{ $v->payment_day }}">
                    <input type="hidden" class="v-credit-limit" value="{{ $v->credit_limit }}">
                    <input type="hidden" class="v-payment-terms" value="{{ $v->payment_terms }}">
                    <input type="hidden" class="v-bank-name" value="{{ $v->bank_name }}">
                    <input type="hidden" class="v-account-title" value="{{ $v->account_title }}">
                    <input type="hidden" class="v-account-number" value="{{ $v->account_number }}">
                    <input type="hidden" class="v-bank-details" value="{{ $v->bank_details }}">
                    <input type="hidden" class="v-address" value="{{ $v->address }}">
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Add/Edit Vendor Modal --}}
<div class="modal fade" id="vendorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <form action="{{ url('vendor/store') }}" method="POST" id="vendorForm" class="vendor-modal-form">
                @csrf
                <input type="hidden" id="vendor_id" name="id">

                <div class="modal-header bg-white px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="modal-title font-weight-bold text-dark mb-0" id="modalTitle"><i class="fas fa-truck text-primary mr-2"></i>New Vendor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.4rem; line-height: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div class="form-group">
                        <label for="vname">Vendor Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="vname" placeholder="Enter vendor name..." required />
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label for="vcategory" class="mb-0">Vendor Category</label>
                                    <a href="{{ route('vendor_categories.index') }}" target="_blank" class="text-primary font-weight-bold" style="font-size: 0.76rem;" title="Manage categories">
                                        <i class="fas fa-cog mr-1"></i>Manage
                                    </a>
                                </div>
                                <select class="form-control" name="category" id="vcategory">
                                    <option value="">Select category...</option>
                                    @if(isset($vendorCategories) && $vendorCategories->count() > 0)
                                        @foreach($vendorCategories as $cat)
                                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="Loafer / Manufacturer">Loafer / Manufacturer</option>
                                        <option value="Sneakers / Supplier">Sneakers / Supplier</option>
                                        <option value="Manufacturer">Manufacturer</option>
                                        <option value="Distributor">Distributor</option>
                                        <option value="Wholesaler">Wholesaler</option>
                                        <option value="Supplier">Supplier</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vstatus">Vendor Status <span class="text-danger">*</span></label>
                                <select class="form-control" name="status" id="vstatus" required>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vphone">Phone Number</label>
                                <input type="text" class="form-control" name="phone" id="vphone" placeholder="Phone number..." />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="opening_balance">Opening Balance <span class="text-danger">*</span></label>
                                <input type="number" step="any" class="form-control" name="opening_balance" id="opening_balance" placeholder="0.00" required />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vpayment_due_date">Payment Due Date <small class="text-muted">(Specific Date)</small></label>
                                <input type="date" class="form-control" name="payment_due_date" id="vpayment_due_date" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vpayment_day">Payment Day <small class="text-muted">(Weekly Day)</small></label>
                                <select class="form-control" name="payment_day" id="vpayment_day">
                                    <option value="">No fixed day...</option>
                                    <option value="Monday">Every Monday</option>
                                    <option value="Tuesday">Every Tuesday</option>
                                    <option value="Wednesday">Every Wednesday</option>
                                    <option value="Thursday">Every Thursday</option>
                                    <option value="Friday">Every Friday</option>
                                    <option value="Saturday">Every Saturday</option>
                                    <option value="Sunday">Every Sunday</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vcredit_limit">Credit Limit (Rs.)</label>
                                <input type="number" step="any" min="0" class="form-control" name="credit_limit" id="vcredit_limit" placeholder="e.g. 100000" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vpayment_terms">Payment Terms</label>
                                <input type="text" class="form-control" name="payment_terms" id="vpayment_terms" list="paymentTermsList" placeholder="e.g. 30 days credit" />
                                <datalist id="paymentTermsList">
                                    <option value="Cash / Immediate">
                                    <option value="7 days credit">
                                    <option value="15 days credit">
                                    <option value="30 days credit">
                                    <option value="45 days credit">
                                    <option value="60 days credit">
                                    <option value="90 days credit">
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Account Details Section -->
                    <div class="p-3 mb-3 rounded" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-university text-primary mr-2" style="font-size: 0.95rem;"></i>
                            <span class="font-weight-bold text-dark" style="font-size: 0.88rem;">Bank Account Details</span>
                            <small class="text-muted ml-2">(Optional)</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="vbank_name" style="font-size: 0.8rem;">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name" id="vbank_name" placeholder="e.g. Meezan Bank, HBL, MCB..." />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label for="vaccount_title" style="font-size: 0.8rem;">Account Title</label>
                                    <input type="text" class="form-control" name="account_title" id="vaccount_title" placeholder="e.g. Title / Account Holder..." />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="vaccount_number" style="font-size: 0.8rem;">Account / IBAN Number</label>
                                    <input type="text" class="form-control" name="account_number" id="vaccount_number" placeholder="e.g. 01020304050 or PK..." />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="vbank_details" style="font-size: 0.8rem;">Branch / Additional Info</label>
                                    <input type="text" class="form-control" name="bank_details" id="vbank_details" placeholder="e.g. Main Branch, Branch Code..." />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="vaddress">Full Address</label>
                        <textarea class="form-control" name="address" id="vaddress" placeholder="Enter address details..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4 font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    @canany(['vendors.create', 'vendors.edit'])
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold shadow-sm" style="border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none;">
                            <i class="fas fa-check mr-1"></i> Save Vendor
                        </button>
                    @endcanany
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Vendor Profile View Modal --}}
<div class="modal fade" id="vendorProfileModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-white px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="modal-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-id-card text-primary mr-2"></i>Vendor Profile
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.4rem; line-height: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="vprofile-avatar" id="vpAvatar">V</div>
                    <div>
                        <h5 class="font-weight-bold text-dark mb-1" id="vpName">Vendor Name</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-light border" id="vpCategory">Supplier</span>
                            <span class="badge" id="vpStatus">Active</span>
                        </div>
                    </div>
                </div>

                <div class="vprofile-info-row">
                    <span class="vprofile-info-label"><i class="fas fa-phone mr-2 text-primary"></i> Contact Phone:</span>
                    <span class="vprofile-info-value" id="vpPhone">—</span>
                </div>
                <div class="vprofile-info-row">
                    <span class="vprofile-info-label"><i class="fas fa-wallet mr-2 text-danger"></i> Outstanding Balance:</span>
                    <span class="vprofile-info-value text-danger" id="vpBalance">Rs. 0.00</span>
                </div>
                <div class="vprofile-info-row">
                    <span class="vprofile-info-label"><i class="far fa-calendar-alt mr-2 text-warning"></i> Payment Schedule:</span>
                    <span class="vprofile-info-value" id="vpDue">—</span>
                </div>
                <div class="vprofile-info-row">
                    <span class="vprofile-info-label"><i class="fas fa-shield-alt mr-2 text-info"></i> Credit Limit:</span>
                    <span class="vprofile-info-value font-weight-bold text-dark" id="vpCreditLimit">—</span>
                </div>
                <div class="vprofile-info-row">
                    <span class="vprofile-info-label"><i class="fas fa-file-contract mr-2 text-primary"></i> Payment Terms:</span>
                    <span class="vprofile-info-value font-weight-bold text-dark" id="vpPaymentTerms">—</span>
                </div>
                <div class="vprofile-info-row">
                    <span class="vprofile-info-label"><i class="fas fa-map-marker-alt mr-2 text-muted"></i> Address:</span>
                    <span class="vprofile-info-value" id="vpAddress" style="max-width: 60%;">—</span>
                </div>
                <div class="vprofile-info-row">
                    <span class="vprofile-info-label"><i class="fas fa-calendar-check mr-2 text-muted"></i> Joined Date:</span>
                    <span class="vprofile-info-value" id="vpDate">—</span>
                </div>

                <!-- Bank Accounts Details in Profile -->
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="font-weight-bold text-dark" style="font-size: 0.86rem;">
                            <i class="fas fa-university text-success mr-2"></i> Bank Account Details
                        </span>
                        <span class="badge badge-light border text-muted" id="vpBankStatusBadge" style="font-size: 0.72rem;">Available</span>
                    </div>
                    <div id="vpBankDetailsContainer" class="p-3 rounded bg-light" style="border: 1px solid #e2e8f0; font-size: 0.85rem;">
                        <div class="vprofile-info-row py-1 border-0">
                            <span class="vprofile-info-label text-muted" style="font-size: 0.8rem;">Bank Name:</span>
                            <span class="vprofile-info-value text-dark" id="vpBankName">—</span>
                        </div>
                        <div class="vprofile-info-row py-1 border-0">
                            <span class="vprofile-info-label text-muted" style="font-size: 0.8rem;">Account Title:</span>
                            <span class="vprofile-info-value text-dark" id="vpAccountTitle">—</span>
                        </div>
                        <div class="vprofile-info-row py-1 border-0">
                            <span class="vprofile-info-label text-muted" style="font-size: 0.8rem;">Account / IBAN:</span>
                            <span class="vprofile-info-value text-primary font-weight-bold" id="vpAccountNumber">—</span>
                        </div>
                        <div class="vprofile-info-row py-1 border-0" id="vpBankBranchRow">
                            <span class="vprofile-info-label text-muted" style="font-size: 0.8rem;">Branch / Details:</span>
                            <span class="vprofile-info-value text-dark" id="vpBankDetails">—</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-3 border-top d-flex align-items-center justify-content-between">
                <div>
                    <a href="#" id="vpLedgerBtn" class="btn btn-warning btn-sm font-weight-bold mr-1" style="border-radius: 8px;">
                        <i class="fas fa-book-open mr-1"></i> Ledger
                    </a>
                    <a href="#" id="vpPaymentsBtn" class="btn btn-success btn-sm font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-money-bill-wave mr-1"></i> Payments
                    </a>
                </div>
                <button type="button" class="btn btn-secondary px-4 font-weight-bold btn-sm" data-dismiss="modal" style="border-radius: 8px;">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('.datanew')) {
            $('.datanew').DataTable().destroy();
        }
        $('.datanew').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "search": "",
                "searchPlaceholder": "Search vendors..."
            },
            "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });

        // Open Modal for Create
        $('#btnAddVendor').click(function() {
            $('#vendorForm')[0].reset();
            $('#vendor_id').val('');
            $('#vcategory').val('');
            $('#vstatus').val('1');
            $('#vpayment_due_date').val('');
            $('#vpayment_day').val('');
            $('#vcredit_limit').val('');
            $('#vpayment_terms').val('');
            $('#vbank_name').val('');
            $('#vaccount_title').val('');
            $('#vaccount_number').val('');
            $('#vbank_details').val('');
            $('#modalTitle').html('<i class="fas fa-truck text-primary mr-2"></i>New Vendor');
            $('#opening_balance').prop('readonly', false);
            $('#vendorModal').modal('show');
        });

        // Edit Vendor
        $(document).on('click', '.edit-btn', function() {
            const $container = $(this).closest('tr, .vendor-mcard');
            const id = $(this).data('id');

            const name = $container.find('.v-name').val();
            const category = $container.find('.v-category').val();
            const status = $container.find('.v-status').val();
            const phone = $container.find('.v-phone').val();
            const balance = $container.find('.v-balance').val();
            const dueDate = $container.find('.v-due-date').val();
            const dueDay = $container.find('.v-due-day').val();
            const creditLimit = $container.find('.v-credit-limit').val();
            const paymentTerms = $container.find('.v-payment-terms').val();
            const bankName = $container.find('.v-bank-name').val();
            const accountTitle = $container.find('.v-account-title').val();
            const accountNumber = $container.find('.v-account-number').val();
            const bankDetails = $container.find('.v-bank-details').val();
            const address = $container.find('.v-address').val();

            $('#vendor_id').val(id);
            $('#vname').val(name);
            $('#vcategory').val(category || '');
            $('#vstatus').val(status !== undefined && status !== '' ? status : '1');
            $('#vphone').val(phone);
            $('#opening_balance').val(balance).prop('readonly', true);
            $('#vpayment_due_date').val(dueDate || '');
            $('#vpayment_day').val(dueDay || '');
            $('#vcredit_limit').val(creditLimit || '');
            $('#vpayment_terms').val(paymentTerms || '');
            $('#vbank_name').val(bankName || '');
            $('#vaccount_title').val(accountTitle || '');
            $('#vaccount_number').val(accountNumber || '');
            $('#vbank_details').val(bankDetails || '');
            $('#vaddress').val(address);

            $('#modalTitle').html('<i class="fas fa-edit text-primary mr-2"></i>Edit Vendor');
            $('#vendorModal').modal('show');
        });

        // View Vendor Profile Modal
        $(document).on('click', '.btn-view-profile', function() {
            const name = $(this).data('name');
            const category = $(this).data('category');
            const phone = $(this).data('phone');
            const status = $(this).data('status');
            const balance = $(this).data('balance');
            const due = $(this).data('due');
            const creditLimit = $(this).data('credit-limit');
            const paymentTerms = $(this).data('payment-terms');
            const bankName = $(this).data('bank-name');
            const accountTitle = $(this).data('account-title');
            const accountNumber = $(this).data('account-number');
            const bankDetails = $(this).data('bank-details');
            const address = $(this).data('address');
            const date = $(this).data('date');
            const ledgerUrl = $(this).data('ledger-url');
            const paymentsUrl = $(this).data('payments-url');

            $('#vpAvatar').text((name || 'V').substring(0, 1).toUpperCase());
            $('#vpName').text(name || '—');
            $('#vpCategory').text(category || '—');
            $('#vpPhone').text(phone || '—');
            $('#vpStatus').text(status)
                .removeClass('badge-success badge-secondary')
                .addClass(status === 'Active' ? 'badge-success' : 'badge-secondary');
            $('#vpBalance').text(balance || 'Rs. 0.00');
            $('#vpDue').text(due || '—');
            $('#vpCreditLimit').text(creditLimit || '—');
            $('#vpPaymentTerms').text(paymentTerms || '—');
            $('#vpAddress').text(address || '—');
            $('#vpDate').text(date || '—');
            $('#vpLedgerBtn').attr('href', ledgerUrl || '#');
            $('#vpPaymentsBtn').attr('href', paymentsUrl || '#');

            const hasBank = bankName || accountTitle || accountNumber || bankDetails;
            if (hasBank) {
                $('#vpBankStatusBadge').text('Saved').removeClass('badge-secondary').addClass('badge-success');
                $('#vpBankName').text(bankName || '—');
                $('#vpAccountTitle').text(accountTitle || '—');
                $('#vpAccountNumber').text(accountNumber || '—');
                if (bankDetails) {
                    $('#vpBankDetails').text(bankDetails);
                    $('#vpBankBranchRow').show();
                } else {
                    $('#vpBankBranchRow').hide();
                }
            } else {
                $('#vpBankStatusBadge').text('Not Set').removeClass('badge-success').addClass('badge-secondary');
                $('#vpBankName').text('—');
                $('#vpAccountTitle').text('—');
                $('#vpAccountNumber').text('—');
                $('#vpBankBranchRow').hide();
            }

            $('#vendorProfileModal').modal('show');
        });

        // Helper to update vendor status across all UI components
        function syncVendorStatusUI(id, isActive, activeCount) {
            // 1. Status column pill
            const $statusBtns = $('button.status-toggle-btn[data-id="' + id + '"], span.status-toggle-btn[data-id="' + id + '"]');
            $statusBtns.each(function() {
                const $b = $(this);
                $b.toggleClass('is-active', isActive)
                  .toggleClass('is-deactive', !isActive);
                $b.find('.status-text').text(isActive ? 'Active' : 'Deactive');
            });

            // 2. Action column switch toggles
            const $switches = $('input.vendor-status-toggle[data-id="' + id + '"]');
            $switches.each(function() {
                const $sw = $(this);
                $sw.prop('checked', isActive);
                const $wrapper = $sw.closest('.vendor-switch-wrapper');
                const $txt = $wrapper.find('.vendor-switch-text');
                $txt.text(isActive ? 'Active' : 'Deactive')
                    .toggleClass('text-active', isActive)
                    .toggleClass('text-deactive', !isActive);
            });

            // 3. Hidden input for edit modal
            $('tr, .vendor-mcard').each(function() {
                const $row = $(this);
                if ($row.find('.edit-btn[data-id="' + id + '"]').length || $row.find('input[data-id="' + id + '"]').length) {
                    $row.find('.v-status').val(isActive ? '1' : '0');
                }
            });

            // 4. Update top counter
            if (activeCount !== undefined) {
                $('#activeVendorsCount').text(activeCount);
            }
        }

        // Shared AJAX toggle function
        function toggleVendorStatusAjax(id, onDone) {
            $.ajax({
                url: "{{ url('vendor/toggle-status') }}/" + id,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res && res.success) {
                        const isActive = parseInt(res.status, 10) === 1;
                        syncVendorStatusUI(id, isActive, res.active_count);
                    } else {
                        alert('Could not update status.');
                    }
                },
                error: function() {
                    alert('Failed to update vendor status.');
                    if (typeof onDone === 'function') onDone(false);
                },
                complete: function() {
                    if (typeof onDone === 'function') onDone(true);
                }
            });
        }

        // Action Column: Toggle Switch Change
        $(document).on('change', 'input.vendor-status-toggle', function() {
            const $checkbox = $(this);
            const id = $checkbox.data('id');
            const targetState = $checkbox.is(':checked');

            $checkbox.prop('disabled', true);
            toggleVendorStatusAjax(id, function(success) {
                $checkbox.prop('disabled', false);
                if (success === false) {
                    // Revert checkbox if ajax failed
                    $checkbox.prop('checked', !targetState);
                }
            });
        });

        // Status Column: Quick Status Pill Toggle
        $(document).on('click', 'button.status-toggle-btn', function() {
            const $btn = $(this);
            const id = $btn.data('id');
            if ($btn.prop('disabled')) return;

            $btn.prop('disabled', true);
            toggleVendorStatusAjax(id, function() {
                $btn.prop('disabled', false);
            });
        });
    });
</script>
@endsection
