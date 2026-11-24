@extends('admin_panel.layout.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .customer-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        padding-bottom: 40px;
    }

    /* Page Header */
    .customer-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        border-radius: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .customer-title {
        font-weight: 800;
        font-size: 1.35rem;
        color: #0f172a;
        margin-bottom: 2px;
        letter-spacing: -0.02em;
    }
    .customer-sub {
        font-size: 0.82rem;
        color: #64748b;
    }

    /* Action Buttons Hub */
    .customer-action-hub {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-cust-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border: none;
        padding: 9px 18px;
        font-size: 0.86rem;
        font-weight: 600;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.22);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-cust-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
    }
    .btn-cust-sub {
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
    .btn-cust-sub:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a !important;
    }

    /* Stat Cards */
    .customer-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
        transition: transform 0.2s;
    }
    .customer-stat-card:hover {
        transform: translateY(-2px);
    }
    .customer-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .customer-stat-val {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .customer-stat-lbl {
        font-size: 0.76rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* Filter Card */
    .customer-filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }

    /* Table Container */
    .customer-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
    }

    .table-customer {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 8px !important;
        margin-top: 10px !important;
    }
    .table-customer thead th {
        border: none !important;
        background: #f8fafc !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 0.74rem !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 14px !important;
    }
    .table-customer tbody tr {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        transition: all 0.15s ease;
    }
    .table-customer tbody tr:hover {
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .table-customer tbody td {
        border: none !important;
        border-top: 1px solid #f1f5f9 !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 12px 14px !important;
        vertical-align: middle !important;
        font-size: 0.85rem;
    }
    .table-customer tbody td:first-child {
        border-left: 1px solid #f1f5f9 !important;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }
    .table-customer tbody td:last-child {
        border-right: 1px solid #f1f5f9 !important;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    /* Badges & Pills */
    .cust-badge-id {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 0.74rem;
        padding: 4px 8px;
        border-radius: 6px;
        font-family: monospace;
        border: 1px solid #e2e8f0;
    }
    .cust-badge-type {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 600;
        font-size: 0.74rem;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
    }
    .cust-badge-zone {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.72rem;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }
    .cust-badge-balance-danger {
        background: #fef2f2;
        color: #dc2626;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #fecaca;
    }
    .cust-badge-balance-success {
        background: #f0fdf4;
        color: #16a34a;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #bbf7d0;
    }
    .cust-badge-balance-zero {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.82rem;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    /* Action Buttons in Table */
    .cust-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 0.82rem;
        transition: all 0.15s ease;
        margin: 0 2px;
        cursor: pointer;
        text-decoration: none !important;
    }
    .cust-action-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .cust-action-btn.btn-view:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
    .cust-action-btn.btn-edit:hover {
        background: #fefce8;
        color: #ca8a04;
        border-color: #fef08a;
    }
    .cust-action-btn.btn-ledger:hover {
        background: #f0fdf4;
        color: #16a34a;
        border-color: #bbf7d0;
    }
    .cust-action-btn.btn-delete:hover {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    /* WhatsApp Button */
    .btn-whatsapp {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #25d366;
        color: #ffffff !important;
        border-radius: 6px;
        padding: 2px 7px;
        font-size: 0.74rem;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.15s;
    }
    .btn-whatsapp:hover {
        background: #128c7e;
        transform: scale(1.04);
    }

    /* Status Pill Toggle */
    .status-toggle-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        text-decoration: none !important;
    }
    .status-active {
        background: #ecfdf5;
        color: #059669;
        border-color: #a7f3d0;
    }
    .status-active:hover {
        background: #d1fae5;
        color: #047857;
    }
    .status-inactive {
        background: #f1f5f9;
        color: #64748b;
        border-color: #cbd5e1;
    }
    .status-inactive:hover {
        background: #e2e8f0;
        color: #334155;
    }

    /* Modals Modern Refinement */
    .cust-modal-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        padding: 16px 22px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .cust-modal-header .close {
        color: #ffffff !important;
        opacity: 0.8;
        font-size: 1.6rem;
        text-shadow: none;
        outline: none;
    }
    .cust-modal-header .close:hover {
        opacity: 1;
    }
    .cust-form-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 4px;
    }
    .cust-form-control {
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 7px 12px !important;
        font-size: 0.88rem !important;
        font-weight: 500 !important;
        color: #0f172a !important;
    }
    .cust-form-control:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        outline: none !important;
    }

    /* Profile Modal Details Grid */
    .profile-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.86rem;
    }
    .profile-info-row:last-child {
        border-bottom: none;
    }
    .profile-info-label {
        font-weight: 600;
        color: #64748b;
    }
    .profile-info-value {
        font-weight: 700;
        color: #0f172a;
        text-align: right;
    }

    /* Modal stacking & z-index failsafe */
    .modal {
        z-index: 1060 !important;
    }
    .modal-backdrop {
        z-index: 1050 !important;
    }
</style>

<div class="customer-page container-fluid px-3 px-md-4 pt-3">

    {{-- 1. PAGE HEADER ROW --}}
    <div class="customer-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <div>
            <h3 class="customer-title"><i class="fas fa-users text-primary mr-2"></i>Customer Management</h3>
            <div class="customer-sub">Manage customer accounts, credit limits, balances, ledgers and payments</div>
        </div>
        <div class="customer-action-hub mt-3 mt-md-0">
            @can('customers.create')
                <button type="button" class="btn-cust-primary" id="btnQuickAddCustomer" onclick="openQuickAddCustomer()" data-toggle="modal" data-target="#customerQuickModal" data-bs-toggle="modal" data-bs-target="#customerQuickModal">
                    <i class="fas fa-user-plus"></i> Add Customer
                </button>
                <a href="{{ route('customers.create') }}" class="btn-cust-sub" title="Open Full Detailed Form">
                    <i class="fas fa-external-link-alt mr-1"></i> Full Form
                </a>
            @endcan
            @can('customers.view')
                <a href="{{ route('customers.ledger') }}" class="btn-cust-sub">
                    <i class="fas fa-book mr-1"></i> Ledger
                </a>
                <a href="{{ route('customer.payments') }}" class="btn-cust-sub">
                    <i class="fas fa-money-check-alt mr-1"></i> Payments
                </a>
                <a href="{{ route('customers.inactive') }}" class="btn-cust-sub">
                    <i class="fas fa-user-slash mr-1"></i> Inactive
                </a>
            @endcan
        </div>
    </div>

    {{-- 2. KPI OVERVIEW STAT CARDS --}}
    <div class="row mb-3">
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="customer-stat-card">
                <div class="customer-stat-icon"><i class="fas fa-users"></i></div>
                <div>
                    <div class="customer-stat-val" id="statTotal">{{ $totalCustomers }}</div>
                    <div class="customer-stat-lbl">Total Customers</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="customer-stat-card">
                <div class="customer-stat-icon" style="background: #ecfdf5; color: #059669;"><i class="fas fa-user-check"></i></div>
                <div>
                    <div class="customer-stat-val" id="statActive">{{ $activeCustomers }}</div>
                    <div class="customer-stat-lbl">Active Customers</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="customer-stat-card" style="border-left: 4px solid #ef4444;">
                <div class="customer-stat-icon" style="background: #fef2f2; color: #dc2626;"><i class="fas fa-hand-holding-usd"></i></div>
                <div>
                    <div class="customer-stat-val text-danger" id="statReceivables">Rs. {{ number_format($totalReceivable, 2) }}</div>
                    <div class="customer-stat-lbl">Total Market Receivables</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 mb-3">
            <div class="customer-stat-card">
                <div class="customer-stat-icon" style="background: #f1f5f9; color: #64748b;"><i class="fas fa-user-times"></i></div>
                <div>
                    <div class="customer-stat-val text-muted" id="statInactive">{{ $inactiveCustomers }}</div>
                    <div class="customer-stat-lbl">Inactive Accounts</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. FILTER BAR --}}
    <div class="customer-filter-card">
        <div class="row align-items-center g-2">
            <div class="col-md-4 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 text-muted" style="border-radius: 8px 0 0 8px;"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="customerSearchInput" class="form-control border-left-0" placeholder="Search by name, code, phone, zone..." style="border-radius: 0 8px 8px 0;">
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <select id="statusFilter" class="form-control cust-form-control">
                    <option value="">Status: All Status</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <select id="typeFilter" class="form-control cust-form-control">
                    <option value="">Type: All Customer Types</option>
                    <option value="Main Customer">Main Customer</option>
                    <option value="Walk-in">Walk-in</option>
                    <option value="Wholesaler">Wholesaler</option>
                    <option value="Retailer">Retailer</option>
                </select>
            </div>
            <div class="col-md-2 text-right">
                <button type="button" class="btn btn-light border w-100 font-weight-bold" id="btnResetFilters" style="border-radius: 8px; font-size: 0.84rem;">
                    <i class="fas fa-redo-alt mr-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- 4. CUSTOMERS DATA TABLE --}}
    <div class="customer-table-card">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-customer" id="customerTable">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th style="min-width: 110px;">Customer ID</th>
                        <th style="min-width: 220px;">Customer Info</th>
                        <th style="min-width: 170px;">Contact &amp; Phone</th>
                        <th style="min-width: 120px;">Type</th>
                        <th style="min-width: 120px;">Credit Limit</th>
                        <th style="min-width: 130px;" class="text-right">Balance</th>
                        <th style="min-width: 95px;" class="text-center">Status</th>
                        <th style="min-width: 85px;" class="text-center">Source</th>
                        <th style="min-width: 140px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $index => $c)
                        @php
                            $bal = (float) ($c->previous_balance ?? 0);
                            $cleanPhone = preg_replace('/[^0-9]/', '', $c->mobile ?? '');
                            if (strlen($cleanPhone) == 11 && str_starts_with($cleanPhone, '03')) {
                                $waPhone = '92' . substr($cleanPhone, 1);
                            } else {
                                $waPhone = $cleanPhone;
                            }
                        @endphp
                        <tr data-status="{{ strtolower($c->status ?? 'active') }}" data-type="{{ $c->customer_type ?? '' }}">
                            <td class="text-center text-muted font-weight-bold">{{ $index + 1 }}</td>
                            <td>
                                <span class="cust-badge-id">{{ $c->customer_id ?? ('CUST-' . str_pad($c->id, 4, '0', STR_PAD_LEFT)) }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark" style="font-size: 0.92rem;">
                                    {{ $c->customer_name }}
                                </div>
                                @if(!empty($c->customer_name_ur))
                                    <div class="text-muted small" style="font-family: 'Noto Nastaliq Urdu', serif; font-size: 0.78rem;">
                                        {{ $c->customer_name_ur }}
                                    </div>
                                @endif
                                @if(!empty($c->zone))
                                    <span class="cust-badge-zone mt-1 d-inline-block">
                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i>{{ $c->zone }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($c->mobile))
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="font-weight-semibold text-dark">{{ $c->mobile }}</span>
                                        @if(!empty($waPhone))
                                            <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="btn-whatsapp" title="Send WhatsApp Message">
                                                <i class="fab fa-whatsapp"></i> Chat
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif

                                @if(!empty($c->contact_person))
                                    <div class="text-muted small">
                                        <i class="fas fa-user-tie text-secondary mr-1"></i>{{ $c->contact_person }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="cust-badge-type">{{ $c->customer_type ?? 'Main Customer' }}</span>
                            </td>
                            <td>
                                @if(empty($c->balance_range) || $c->balance_range == 0)
                                    <span class="badge badge-light border text-muted">Unlimited</span>
                                @else
                                    <span class="font-weight-bold text-dark">Rs. {{ number_format($c->balance_range, 0) }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($bal > 0)
                                    <span class="cust-badge-balance-danger">
                                        Rs. {{ number_format($bal, 2) }}
                                        <small class="d-block text-danger" style="font-size: 10px;">Receivable</small>
                                    </span>
                                @elseif($bal < 0)
                                    <span class="cust-badge-balance-success">
                                        Rs. {{ number_format(abs($bal), 2) }}
                                        <small class="d-block text-success" style="font-size: 10px;">Advance / Credit</small>
                                    </span>
                                @else
                                    <span class="cust-badge-balance-zero">Rs. 0.00</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0)" class="status-toggle-pill {{ $c->status === 'active' ? 'status-active' : 'status-inactive' }} btn-toggle-status" data-id="{{ $c->id }}" data-status="{{ $c->status }}" title="Click to toggle status">
                                    <i class="fas {{ $c->status === 'active' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    <span>{{ ucfirst($c->status ?? 'active') }}</span>
                                </a>
                            </td>
                            <td class="text-center">
                                @php $source = $c->source ?? 'Manual'; @endphp
                                @if($source === 'Website')
                                    <span class="badge badge-success px-2 py-1" style="border-radius: 6px;">Website</span>
                                @elseif($source === 'Both')
                                    <span class="badge badge-info px-2 py-1" style="border-radius: 6px;">Both</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1" style="border-radius: 6px;">Manual</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    {{-- View Profile --}}
                                    <button type="button" class="cust-action-btn btn-view btn-view-profile" onclick="openCustomerProfile({{ $c->id }})" data-id="{{ $c->id }}" data-toggle="modal" data-target="#customerProfileModal" data-bs-toggle="modal" data-bs-target="#customerProfileModal" title="View Customer Profile &amp; Summary">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- Edit --}}
                                    @can('customers.edit')
                                        <a href="{{ route('customers.edit', $c->id) }}" class="cust-action-btn btn-edit" title="Edit Customer Details">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    @endcan

                                    {{-- Ledger --}}
                                    @can('customers.view')
                                        <a href="{{ route('customers.ledger', ['customer_id' => $c->id]) }}" class="cust-action-btn btn-ledger" title="View Customer Ledger">
                                            <i class="fas fa-book-open"></i>
                                        </a>
                                    @endcan

                                    {{-- Delete --}}
                                    @can('customers.delete')
                                        <button type="button" class="cust-action-btn btn-delete btn-delete-customer" data-id="{{ $c->id }}" data-name="{{ $c->customer_name }}" title="Delete Customer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="fas fa-user-friends fa-3x text-light"></i></div>
                                <h6 class="font-weight-bold text-dark">No Customers Found</h6>
                                <p class="text-muted small mb-3">You haven't added any customers yet. Add your first customer to get started.</p>
                                @can('customers.create')
                                    <button type="button" class="btn btn-primary btn-sm px-3 font-weight-bold" id="btnEmptyAdd" onclick="openQuickAddCustomer()" data-toggle="modal" data-target="#customerQuickModal" data-bs-toggle="modal" data-bs-target="#customerQuickModal">
                                        <i class="fas fa-plus mr-1"></i> + Add First Customer
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 5. QUICK ADD NEW CUSTOMER MODAL --}}
{{-- ======================================================== --}}
<div class="modal fade" id="customerQuickModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header cust-modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold text-white mb-0">
                    <i class="fas fa-user-plus text-primary mr-2"></i>Quick Add New Customer
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; cursor:pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickCustomerForm" autocomplete="off">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        <div class="col-md-4 mb-3">
                            <label class="cust-form-label">Customer ID</label>
                            <input type="text" class="form-control cust-form-control bg-white font-weight-bold text-primary" name="customer_id" id="qc_customer_id" value="{{ $latestId }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="cust-form-label">Customer Type <span class="text-danger">*</span></label>
                            <select class="form-control cust-form-control" name="customer_type" id="qc_customer_type" required>
                                <option value="Main Customer" selected>Main Customer</option>
                                <option value="Walk-in">Walk-in</option>
                                <option value="Wholesaler">Wholesaler</option>
                                <option value="Retailer">Retailer</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="cust-form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control cust-form-control" name="status" id="qc_status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="cust-form-label">Customer Name (English) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control cust-form-control" name="customer_name" id="qc_customer_name" required placeholder="e.g. Ali Shoes Store, Tariq Khan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="cust-form-label">Customer Name (Urdu - Optional)</label>
                            <input type="text" class="form-control cust-form-control text-right" dir="rtl" name="customer_name_ur" id="qc_customer_name_ur" placeholder="مثلاً: علی شوز اسٹور">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="cust-form-label">Mobile Number</label>
                            <input type="text" class="form-control cust-form-control" name="mobile" id="qc_mobile" placeholder="0300-1234567">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="cust-form-label">Zone / City</label>
                            <select class="form-control cust-form-control" name="zone" id="qc_zone">
                                <option value="">Select Zone / City</option>
                                @foreach($zones as $z)
                                    <option value="{{ $z->zone }}">{{ $z->zone }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="cust-form-label">Contact Person</label>
                            <input type="text" class="form-control cust-form-control" name="contact_person" id="qc_contact_person" placeholder="Manager / Owner Name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="cust-form-label">Opening Balance (Rs.)</label>
                            <input type="number" step="0.01" class="form-control cust-form-control text-right font-weight-bold" name="opening_balance" id="qc_opening_balance" value="0.00">
                            <small class="text-muted">Previous balance at the time of creation</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="cust-form-label">Credit Limit / Range (Rs.)</label>
                            <input type="number" step="0.01" class="form-control cust-form-control text-right font-weight-bold" name="balance_range" id="qc_balance_range" value="0.00">
                            <small class="text-muted">Enter 0 for Unlimited Credit Limit</small>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="cust-form-label">Full Address / Location</label>
                            <textarea class="form-control cust-form-control" name="address" id="qc_address" rows="2" placeholder="Shop #, Market, Street address..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white px-4 py-3 border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('customers.create') }}" class="btn btn-link text-primary font-weight-bold p-0" style="font-size: 0.84rem;">
                        <i class="fas fa-sliders-h mr-1"></i> Need More Fields (CNIC, Filer, Email)? Open Full Form
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary px-3 py-2 font-weight-bold" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold" id="btnSaveCustomer" style="border-radius: 8px;">
                            <i class="fas fa-check-circle mr-1"></i> Save Customer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 6. CUSTOMER PROFILE & SUMMARY MODAL --}}
{{-- ======================================================== --}}
<div class="modal fade" id="customerProfileModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header cust-modal-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-primary px-2 py-1"><i class="fas fa-user-circle"></i></span>
                    <h5 class="modal-title font-weight-bold text-white mb-0" id="cp_title">Customer Profile</h5>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; cursor:pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <h4 class="font-weight-bold text-dark mb-1" id="cp_name">—</h4>
                    <div class="text-muted small mb-2" id="cp_name_ur" style="font-family: 'Noto Nastaliq Urdu', serif;">—</div>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge badge-light border font-monospace" id="cp_id_badge">CUST-0000</span>
                        <span class="badge badge-info" id="cp_type_badge">Main Customer</span>
                        <span class="badge badge-success" id="cp_status_badge">Active</span>
                    </div>
                </div>

                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="fas fa-phone-alt text-primary mr-2"></i> Mobile Number:</span>
                    <span class="profile-info-value" id="cp_mobile">—</span>
                </div>
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="fas fa-user-tie text-secondary mr-2"></i> Contact Person:</span>
                    <span class="profile-info-value" id="cp_contact_person">—</span>
                </div>
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="fas fa-map-marker-alt text-danger mr-2"></i> Zone / City:</span>
                    <span class="profile-info-value" id="cp_zone">—</span>
                </div>
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="fas fa-id-card text-muted mr-2"></i> CNIC &amp; Filer:</span>
                    <span class="profile-info-value" id="cp_cnic_filer">—</span>
                </div>
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="fas fa-shield-alt text-info mr-2"></i> Credit Limit:</span>
                    <span class="profile-info-value" id="cp_credit_limit">—</span>
                </div>
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="fas fa-wallet text-danger mr-2"></i> Current Balance:</span>
                    <span class="profile-info-value" id="cp_balance">—</span>
                </div>
                <div class="profile-info-row">
                    <span class="profile-info-label"><i class="fas fa-map-signs text-muted mr-2"></i> Address:</span>
                    <span class="profile-info-value text-break" id="cp_address" style="max-width: 60%;">—</span>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between align-items-center">
                <div>
                    <a href="#" id="cp_ledger_btn" class="btn btn-warning btn-sm font-weight-bold mr-1" style="border-radius: 8px;">
                        <i class="fas fa-book-open mr-1"></i> Ledger
                    </a>
                    <a href="#" id="cp_payment_btn" class="btn btn-success btn-sm font-weight-bold" style="border-radius: 8px;">
                        <i class="fas fa-money-bill-wave mr-1"></i> Payment
                    </a>
                </div>
                <button type="button" class="btn btn-secondary btn-sm px-4 font-weight-bold" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // Global Open Functions (can never be blocked by DataTable/document.ready errors)
    function openQuickAddCustomer() {
        if ($('#quickCustomerForm').length) {
            $('#quickCustomerForm')[0].reset();
        }
        $('#qc_customer_id').val('{{ $latestId }}');
        $('#customerQuickModal').modal('show');
    }

    function openCustomerProfile(id) {
        if (!id) return;
        $('#customerProfileModal').modal('show');

        // Fetch detail from JSON endpoint
        $.get('/customers/' + id + '/json', function(res) {
            $('#cp_name').text(res.customer_name || '—');
            $('#cp_name_ur').text(res.customer_name_ur || '');
            $('#cp_id_badge').text(res.customer_id || 'CUST-' + res.id);
            $('#cp_type_badge').text(res.customer_type || 'Main Customer');
            $('#cp_status_badge').text((res.status || 'active').toUpperCase())
                .removeClass('badge-success badge-secondary')
                .addClass(res.status === 'active' ? 'badge-success' : 'badge-secondary');

            // Contact
            if (res.mobile) {
                let waPhone = res.mobile.replace(/[^0-9]/g, '');
                if (waPhone.length === 11 && waPhone.startsWith('03')) {
                    waPhone = '92' + waPhone.substring(1);
                }
                $('#cp_mobile').html(`
                    <span class="mr-2">${res.mobile}</span>
                    <a href="https://wa.me/${waPhone}" target="_blank" class="btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> Chat
                    </a>
                `);
            } else {
                $('#cp_mobile').text('—');
            }

            $('#cp_contact_person').text(res.contact_person || '—');
            $('#cp_zone').text(res.zone || '—');
            
            let cnicFiler = (res.cnic || 'No CNIC') + ' (' + (res.filer_type || 'Non-Filer') + ')';
            $('#cp_cnic_filer').text(cnicFiler);

            let limit = parseFloat(res.balance_range) || 0;
            $('#cp_credit_limit').text(limit > 0 ? 'Rs. ' + limit.toLocaleString() : 'Unlimited');

            let bal = parseFloat(res.previous_balance) || 0;
            if (bal > 0) {
                $('#cp_balance').html(`<span class="text-danger font-weight-bold">Rs. ${bal.toLocaleString(undefined, {minimumFractionDigits: 2})} (Receivable)</span>`);
            } else if (bal < 0) {
                $('#cp_balance').html(`<span class="text-success font-weight-bold">Rs. ${Math.abs(bal).toLocaleString(undefined, {minimumFractionDigits: 2})} (Advance / Credit)</span>`);
            } else {
                $('#cp_balance').html(`<span class="text-muted">Rs. 0.00</span>`);
            }

            $('#cp_address').text(res.address || '—');

            // Update Action Buttons
            $('#cp_ledger_btn').attr('href', `/customers/ledger?customer_id=${res.id}`);
            $('#cp_payment_btn').attr('href', `{{ route('customer.payments') }}`);
        }).fail(function() {
            console.warn('Profile JSON endpoint not reachable, modal shown with default state.');
        });
    }

    $(document).ready(function() {
        // Ensure modals are appended directly to body so no container overflows/z-index traps them
        $('#customerQuickModal').appendTo('body');
        $('#customerProfileModal').appendTo('body');

        // Universal modal close handler (Bootstrap 4 & 5 failsafe)
        $(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"], .close', function(e) {
            e.preventDefault();
            $(this).closest('.modal').modal('hide');
        });

        // Initialize DataTable safely
        let customerTable = null;
        try {
            if ($.fn.DataTable) {
                if ($.fn.DataTable.isDataTable('#customerTable')) {
                    $('#customerTable').DataTable().destroy();
                }

                customerTable = $('#customerTable').DataTable({
                    "pageLength": 15,
                    "order": [],
                    "language": {
                        "search": "",
                        "searchPlaceholder": "Filter records..."
                    },
                    "dom": "<'row mb-2'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'>>" +
                           "<'row'<'col-sm-12'tr>>" +
                           "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
                });
            }
        } catch (dtError) {
            console.error('DataTable init warning:', dtError);
        }

        // Custom Live Search
        $('#customerSearchInput').on('keyup', function() {
            if (customerTable) customerTable.search(this.value).draw();
        });

        // Custom Status Filter
        $('#statusFilter').on('change', function() {
            if (!customerTable) return;
            const val = $(this).val();
            if (!val) {
                customerTable.column(7).search('').draw();
            } else {
                customerTable.column(7).search(val, true, false).draw();
            }
        });

        // Custom Type Filter
        $('#typeFilter').on('change', function() {
            if (!customerTable) return;
            const val = $(this).val();
            if (!val) {
                customerTable.column(4).search('').draw();
            } else {
                customerTable.column(4).search(val).draw();
            }
        });

        // Reset Filters Button
        $('#btnResetFilters').click(function() {
            $('#customerSearchInput').val('');
            $('#statusFilter').val('');
            $('#typeFilter').val('');
            if (customerTable) customerTable.search('').columns().search('').draw();
        });

        // Open Quick Add Modal Click Fallback
        $('#btnQuickAddCustomer, #btnEmptyAdd').click(function(e) {
            e.preventDefault();
            openQuickAddCustomer();
        });

        // Submit Quick Customer Form via AJAX
        $('#quickCustomerForm').on('submit', function(e) {
            e.preventDefault();
            let $btn = $('#btnSaveCustomer');
            let origHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Saving...');

            $.ajax({
                url: "{{ route('customers.store') }}",
                method: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    $btn.prop('disabled', false).html(origHtml);
                    $('#customerQuickModal').modal('hide');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Customer Added!',
                            text: 'Customer account has been successfully created.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    let msg = 'Could not save customer.';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: msg
                        });
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        // View Customer Profile Modal Click Fallback
        $(document).on('click', '.btn-view-profile', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            openCustomerProfile(id);
        });

        // Toggle Status via AJAX
        $(document).on('click', '.btn-toggle-status', function(e) {
            e.preventDefault();
            const $this = $(this);
            const id = $this.data('id');
            if (!id) return;

            $.ajax({
                url: `/customers/toggle-status/${id}`,
                method: "GET",
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(res) {
                    if (res.status === 'active') {
                        $this.removeClass('status-inactive').addClass('status-active');
                        $this.find('i').removeClass('fa-times-circle').addClass('fa-check-circle');
                        $this.find('span').text('Active');
                        $this.data('status', 'active');
                        $this.closest('tr').attr('data-status', 'active');
                    } else {
                        $this.removeClass('status-active').addClass('status-inactive');
                        $this.find('i').removeClass('fa-check-circle').addClass('fa-times-circle');
                        $this.find('span').text('Inactive');
                        $this.data('status', 'inactive');
                        $this.closest('tr').attr('data-status', 'inactive');
                    }

                    // Toast
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    Toast.fire({
                        icon: 'success',
                        title: res.message || 'Status updated'
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update customer status.', 'error');
                }
            });
        });

        // Delete Customer with Confirmation
        $(document).on('click', '.btn-delete-customer', function() {
            const id = $(this).data('id');
            const name = $(this).data('name') || 'this customer';

            Swal.fire({
                title: 'Delete Customer?',
                text: `Are you sure you want to delete ${name}? This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/customers/delete/${id}`,
                        method: "GET",
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Customer has been deleted.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete customer.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
