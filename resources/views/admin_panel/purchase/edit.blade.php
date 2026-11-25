@extends('admin_panel.layout.app')

@section('content')
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
    
    <style>
        /* 💎 PREMIUM MODERN ERP THEME FOR PURCHASE ENTRY & EDIT 💎 */
        body {
            background-color: #f8fafc;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overflow-x: hidden !important;
        }
        
        .main-container {
            border: 2px solid #475569 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05) !important;
            background-color: #ffffff !important;
            padding: 20px !important;
            font-size: .85rem;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .card-panel {
            background-color: #f8fafc !important;
            border: 2px solid #cbd5e1 !important;
            border-radius: 10px !important;
            padding: 18px !important;
            height: 100%;
            transition: all 0.2s;
        }
        
        .card-panel:hover {
            border-color: #94a3b8 !important;
        }
        
        .section-title {
            font-weight: 800 !important;
            text-transform: uppercase;
            font-size: 0.8rem !important;
            letter-spacing: 1px !important;
            color: #1e293b !important;
            margin-bottom: 14px !important;
            border-left: 4px solid #2563eb !important;
            padding-left: 10px !important;
        }
        
        .form-control,
        .form-select,
        .select2-container--default .select2-selection--single {
            border: 2px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 6px 10px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            background-color: #ffffff !important;
            transition: all 0.2s ease-in-out !important;
            height: auto !important;
            font-size: 0.85rem !important;
        }
        
        .form-control:focus,
        .form-select:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            outline: none !important;
        }
        
        .input-readonly {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            cursor: not-allowed !important;
        }
        
        .btn-action-primary {
            background-color: #2563eb !important;
            border: 2px solid #1d4ed8 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 10px 24px !important;
            transition: all 0.2s;
            font-size: 0.88rem !important;
        }
        .btn-action-primary:hover {
            background-color: #1d4ed8 !important;
            transform: translateY(-1px);
            color: #ffffff !important;
        }
        
        .btn-action-secondary {
            background-color: #ffffff !important;
            border: 2px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            padding: 10px 20px !important;
            transition: all 0.2s;
            font-size: 0.88rem !important;
        }
        .btn-action-secondary:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
        }

        /* ⚡ FLAT BORDERLESS GRID INPUTS FOR THE ITEMS TABLE ⚡ */
        .sales-table {
            border-collapse: collapse !important;
            margin-bottom: 0 !important;
            width: 100% !important;
        }

        .sales-table thead th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            font-size: 11px !important;
            letter-spacing: 0.5px;
            padding: 12px 10px !important;
            border: 1px solid #cbd5e1 !important;
            border-bottom: 2px solid #64748b !important;
            vertical-align: middle !important;
            text-align: center;
        }

        .sales-table thead th.col-product {
            text-align: left !important;
            padding-left: 14px !important;
        }

        .sales-table tbody td {
            border: 1px solid #cbd5e1 !important;
            padding: 4px 6px !important;
            background-color: #ffffff;
            vertical-align: middle !important;
        }

        .sales-table tbody .form-control,
        .sales-table tbody .form-select {
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 6px !important;
            height: 38px !important;
            padding: 4px 8px !important;
            font-size: 0.85rem !important;
            box-shadow: none !important;
        }

        .sales-table tbody .form-control:focus,
        .sales-table tbody .form-select:focus {
            border-color: #2563eb !important;
            background-color: #f8fafc !important;
        }

        .sales-table tbody .input-readonly {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #334155 !important;
            font-weight: 700 !important;
        }

        .sales-table tbody .select2-container--default .select2-selection--single {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 6px !important;
            height: 38px !important;
            display: flex;
            align-items: center;
        }

        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: 10px !important;
            padding-right: 20px !important;
            font-size: 0.82rem !important;
            color: #1e293b !important;
            font-weight: 600 !important;
            text-align: left !important;
        }

        .sales-table tbody .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            right: 6px !important;
        }
        
        .sales-table tfoot td {
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-top: 2px solid #94a3b8 !important;
            padding: 8px 10px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
        }
        
        .sales-table tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        .btn-edit-variants {
            font-size: 0.84rem !important;
            padding: 6px 14px !important;
            font-weight: 700 !important;
            border-radius: 7px !important;
            white-space: nowrap;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: all 0.15s;
        }
        .btn-view-variants {
            font-size: 0.84rem !important;
            padding: 6px 10px !important;
            border-radius: 7px !important;
            transition: all 0.15s;
        }

        .shadow-xs {
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        /* Vendor inv photo preview */
        .photo-thumb-wrap {
            position: relative;
            display: inline-block;
        }
        .photo-thumb-img {
            width: 36px;
            height: 36px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #3b82f6;
            cursor: pointer;
            transition: transform 0.15s;
        }
        .photo-thumb-img:hover {
            transform: scale(1.1);
        }

        /* 💎 VARIANTS & SIZES MODAL DELIGHT STYLING & PADDING 💎 */
        #productVariantsModal .modal-dialog {
            max-width: 980px !important;
            width: 95% !important;
            margin: 1.5rem auto !important;
        }

        #productVariantsModal .modal-content {
            border-radius: 12px !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            overflow: hidden !important;
        }

        #productVariantsModal .modal-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
            padding: 14px 20px !important;
            border-bottom: 2px solid #334155 !important;
        }

        #productVariantsModal .modal-body {
            padding: 20px 24px !important;
            background-color: #f8fafc !important;
        }

        .modal-close-btn {
            cursor: pointer !important;
            transition: all 0.15s ease-in-out;
        }
        .modal-close-btn:hover {
            opacity: 1 !important;
            transform: scale(1.08);
        }

        .pvm-presets-card {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 14px 18px !important;
            margin-bottom: 18px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .pvm-preset-btn {
            padding: 8px 18px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            transition: all 0.15s ease-in-out;
            margin: 2px !important;
        }
        .pvm-preset-btn:hover {
            transform: translateY(-1px);
        }

        .pvm-bulk-group .form-control {
            border: 1.5px solid #cbd5e1 !important;
            height: 34px !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            border-radius: 6px 0 0 6px !important;
        }
        .pvm-bulk-group .btn {
            height: 34px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            border: 1.5px solid #cbd5e1 !important;
            border-left: none !important;
            border-radius: 0 6px 6px 0 !important;
            padding: 0 12px !important;
        }

        .pvm-table-wrap {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            max-height: 380px;
            overflow-y: auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .pvm-table {
            margin-bottom: 0 !important;
            border-collapse: collapse !important;
            width: 100%;
        }

        .pvm-table thead th {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 12px 14px !important;
            border-bottom: 2px solid #cbd5e1 !important;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .pvm-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.15s;
        }
        .pvm-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .pvm-table tbody td {
            padding: 8px 12px !important;
            vertical-align: middle !important;
        }

        .pvm-input {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 6px !important;
            height: 36px !important;
            padding: 5px 10px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            background-color: #ffffff !important;
            transition: all 0.15s;
        }
        .pvm-input:focus {
            border-color: #2563eb !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
            outline: none !important;
        }

        .pvm-qty-cell {
            background-color: #eff6ff !important;
        }
        .pvm-qty-input {
            border-color: #93c5fd !important;
            color: #1d4ed8 !important;
            font-size: 14px !important;
            font-weight: 800 !important;
        }
        .pvm-qty-input:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2) !important;
        }

        #productVariantsModal .modal-footer {
            padding: 16px 24px !important;
            background-color: #f8fafc !important;
            border-top: 1.5px solid #e2e8f0 !important;
        }

        .pvm-btn-cancel {
            padding: 9px 24px !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            background-color: #ffffff !important;
            border: 2px solid #cbd5e1 !important;
            color: #475569 !important;
            transition: all 0.15s;
        }
        .pvm-btn-cancel:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
        }

        .pvm-btn-save {
            padding: 9px 28px !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            background-color: #2563eb !important;
            border: 2px solid #1d4ed8 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.25) !important;
            transition: all 0.15s;
        }
        .pvm-btn-save:hover {
            background-color: #1d4ed8 !important;
            transform: translateY(-1px);
        }

        .pvm-trash-btn {
            padding: 6px 11px !important;
            border-radius: 6px !important;
            font-size: 12px !important;
        }
    </style>

    <div class="container-fluid py-2 px-1">
        <div class="main-container bg-white border shadow-sm mx-auto p-3 rounded-3">

            <div id="alertBox" class="alert d-none mb-3" role="alert"></div>

            <form id="purchaseForm" action="{{ route('purchase.update', $purchase->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @method('PUT')
                <input type="hidden" id="action" name="action" value="{{ $purchase->status_purchase == 'draft' ? 'save_only' : 'approved' }}">

                {{-- TOP HEADER & INVOICE / VENDOR CARD --}}
                <div class="card-panel shadow-sm mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('Purchase.home') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <h4 class="header-text text-dark fw-bold mb-0 ms-2">Edit Purchase #{{ $purchase->invoice_no }}</h4>
                            @if ($purchase->status_purchase == 'draft')
                                <span class="badge bg-warning text-dark border border-warning ms-2">Pending / Draft</span>
                            @elseif ($purchase->status_purchase == 'Returned')
                                <span class="badge bg-danger text-white border border-danger ms-2">Returned</span>
                            @else
                                <span class="badge bg-success border border-success ms-2">Received</span>
                            @endif
                        </div>
                        <div>
                            <span class="badge bg-light text-secondary border px-3 py-2 fs-6 fw-semibold" id="entryDate">
                                Date: {{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') : date('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">System No.</label>
                            <input type="text" class="form-control input-readonly" name="invoice_no" value="{{ $purchase->invoice_no }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">Vendor Inv#</label>
                            <input type="text" class="form-control" name="purchase_order_no" value="{{ $purchase->purchase_order_no }}" placeholder="Manual Ref">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1 text-muted small">Select Vendor</label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="flex-grow-1">
                                    <select class="form-select select2" id="vendorSelect" name="vendor_id">
                                        <option value="" disabled>Select Vendor</option>
                                        @foreach ($Vendor as $v)
                                            <option value="{{ $v->id }}" data-phone="{{ $v->phone }}" data-address="{{ $v->address }}" {{ $v->id == $purchase->vendor_id ? 'selected' : '' }}>
                                                {{ $v->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addVendorModal" style="padding: 0.38rem 0.75rem;" title="Add New Vendor">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">Date</label>
                            <input type="text" name="purchase_date" class="form-control datepicker-custom" value="{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : date('Y-m-d') }}">
                        </div>
                        
                        {{-- VENDOR INVOICE PHOTO UPLOAD --}}
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1 text-muted small">
                                <i class="bi bi-camera-fill text-primary me-1"></i> Vendor Inv Photo
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" class="form-control form-control-sm" name="vendor_inv_photo" id="vendorInvPhoto" accept="image/*" onchange="previewVendorPhoto(this)">
                                <div id="vendorPhotoThumbWrap" class="photo-thumb-wrap {{ $purchase->vendor_inv_photo ? '' : 'd-none' }}">
                                    <img id="vendorPhotoThumb" src="{{ $purchase->vendor_inv_photo ? asset($purchase->vendor_inv_photo) : '#' }}" alt="Bill Preview" class="photo-thumb-img" title="Click to view full photo" onclick="showFullPhotoModal()">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label class="form-label fw-bold mb-1 text-muted small">Vendor Bill No# / Remarks</label>
                            <input type="text" class="form-control" name="note" id="remarks" value="{{ $purchase->note }}" placeholder="Vendor Bill No# or optional remarks...">
                        </div>
                    </div>

                    <!-- TOP VENDOR DETAILS & HISTORY STRIP -->
                    <div id="vendorInfoCard" class="mt-3 p-2 border rounded-3 bg-light {{ $purchase->vendor ? '' : 'd-none' }}">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 px-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-telephone-fill text-primary"></i>
                                <span class="fw-bold text-muted small">Mobile:</span>
                                <span class="fw-semibold text-dark small" id="vi_mobile">{{ $purchase->vendor->phone ?? '—' }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-primary"></i>
                                <span class="fw-bold text-muted small">Address:</span>
                                <span class="fw-semibold text-dark small" id="vi_address">{{ $purchase->vendor->address ?? '—' }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger-subtle text-danger border border-danger fs-6 px-3 py-1">
                                    Previous Balance: Rs. <span id="vi_prev_bal">0.00</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="warehouse_id" value="{{ $purchase->warehouse_id ?? ($Warehouse->first()->id ?? 1) }}">
                </div>

                {{-- PURCHASE ITEMS (CLEAN FOOTWEAR/FASHION TABLE) --}}
                <div class="card-panel shadow-sm p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mb-0">Purchase Items</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-success px-3 py-2 shadow-sm fw-bold" data-toggle="modal" data-target="#quickAddProductModal" data-dismiss="modal" style="border-radius: 8px; font-size: 0.85rem;">
                                <i class="bi bi-plus-circle me-1"></i>+ Quick Add New Product
                            </button>
                            <button type="button" class="btn btn-primary px-3 py-2 shadow-sm fw-bold" id="btnAdd" style="border-radius: 8px; font-size: 0.85rem;">
                                <i class="bi bi-plus-lg me-1"></i>+ Add Product Row
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive border rounded-3 bg-white">
                        <table class="table table-bordered sales-table mb-0" id="purchaseTable">
                            <thead>
                                <tr>
                                    <th style="width: 45px; text-align: center;">#</th>
                                    <th class="col-product" style="min-width: 280px;">Product / Shoe Article</th>
                                    <th style="min-width: 175px; text-align: center;">Variants, Sizes &amp; Qty</th>
                                    <th style="width: 75px; text-align: center;">Unit</th>
                                    <th style="width: 95px; text-align: center;">Total Qty</th>
                                    <th style="width: 125px; text-align: right;">Cost Price</th>
                                    <th style="width: 80px; text-align: right;">Disc %</th>
                                    <th style="width: 95px; text-align: right;">Disc Amt</th>
                                    <th style="width: 125px; text-align: right;">Amount</th>
                                    <th style="width: 45px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseTableBody">
                                <!-- Rows injected via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" class="text-end fw-bold text-muted">Total Amount:</td>
                                    <td class="text-end fw-bold fs-6 text-dark"><span id="totalAmount">0.00</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Totals + Summary --}}
                <div class="row g-3 mt-1">
                    <div class="col-lg-7">
                        <div class="card-panel shadow-sm">
                            <div class="section-title mb-3">Payment / Receipt Voucher</div>
                            <div id="paymentWrapper" class="border rounded p-3 bg-light mb-3">
                                <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                                    <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                                        <option value="" selected disabled>Select Account</option>
                                        @foreach ($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:140px">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPayment">
                                        <i class="bi bi-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="me-2 fw-bold text-muted">Total Paid:</span>
                                <span class="fw-bold fs-6 text-success" id="totalPaid">{{ number_format($purchase->paid_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="bg-white shadow-sm rounded-3 p-3 h-100 border">
                            <div class="section-title mb-3">Summary</div>
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-muted fw-medium">Total Qty (Pieces)</div>
                                    <div class="col-5 text-end"><span id="tQty" class="fw-bold">0</span></div>
                                </div>
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-muted fw-medium">Sub-Total</div>
                                    <div class="col-5 text-end fw-bold"><span id="tSub">0.00</span></div>
                                </div>
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-muted fw-medium">Bill Discount</div>
                                    <div class="col-5 text-end d-flex gap-1">
                                        <input type="number" class="form-control text-end form-control-sm" id="billDiscountPct" placeholder="%" style="width: 70px;" step="0.01">
                                        <input type="number" class="form-control text-end form-control-sm" id="billDiscount" value="{{ $purchase->discount ?: ($purchase->additional_discount ?: 0) }}" step="0.01">
                                        <input type="hidden" name="discount" id="discountInput" value="{{ $purchase->discount ?: ($purchase->additional_discount ?: 0) }}">
                                    </div>
                                </div>
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-muted fw-medium">Extra Cost</div>
                                    <div class="col-5 text-end">
                                        <input type="number" class="form-control text-end form-control-sm" name="extra_cost" id="extraCost" value="{{ $purchase->extra_cost ?: 0 }}">
                                    </div>
                                </div>
                                <div class="row py-1 align-items-center">
                                    <div class="col-7 text-danger fw-medium">Previous Balance</div>
                                    <div class="col-5 text-end text-danger fw-bold"><span id="tPrev">0.00</span></div>
                                </div>
                                <hr class="my-2 border-secondary">
                                <div class="row py-2">
                                    <div class="col-6 fw-bold fs-5 text-primary">Current Bill</div>
                                    <div class="col-6 text-end fw-bold fs-5 text-primary"><span id="tPayable">{{ number_format($purchase->net_amount, 2) }}</span></div>
                                </div>
                                <div class="row py-2 bg-warning-subtle rounded-2">
                                    <div class="col-6 fw-bold fs-5 text-dark">Total Payable</div>
                                    <div class="col-6 text-end fw-bold fs-5 text-dark"><span id="tTotalPayable">{{ number_format($purchase->due_amount, 2) }}</span></div>
                                </div>
                                <input type="hidden" name="net_amount" id="netAmountInput" value="{{ $purchase->net_amount }}">
                                <input type="hidden" name="subtotal" id="subtotalInput" value="{{ $purchase->subtotal }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap gap-3 justify-content-end p-3 mt-3 border-top bg-light rounded-bottom">
                    <button type="button" class="btn btn-action-secondary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                    @if ($purchase->status_purchase == 'draft')
                        <button type="button" class="btn btn-action-primary bg-info border-info text-white" id="btnSaveOnly">
                            <i class="bi bi-save me-1"></i> Save Draft
                        </button>
                        <button type="button" class="btn btn-action-primary bg-success border-success text-white" id="btnConfirm">
                            <i class="bi bi-check-circle me-1"></i> Confirm Purchase
                        </button>
                    @else
                        <button type="button" class="btn btn-action-primary bg-primary border-primary text-white" id="btnUpdatePurchase">
                            <i class="bi bi-check2-circle me-1"></i> Update Purchase
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 2. PRODUCT VARIANTS & SIZES MODAL (The 📝 Edit Modal) --}}
    {{-- ======================================================== --}}
    <div class="modal fade" id="productVariantsModal" tabindex="-1" aria-labelledby="productVariantsModalLabel" aria-hidden="true" data-backdrop="static" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-width: 980px; width: 95%;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary fs-6"><i class="bi bi-boxes"></i></span>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="productVariantsModalLabel">
                            Variants &amp; Sizes: <span id="pvm_product_title" class="text-warning">—</span>
                        </h5>
                        <span class="badge bg-secondary ms-2" id="pvm_sku_badge">SKU: —</span>
                    </div>
                    <button type="button" class="close text-white modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:1.8rem; line-height:1; opacity:0.85; cursor:pointer;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3 bg-light">
                    <!-- Batch & Expiry (FEFO) Details Strip -->
                    <div class="card p-2.5 mb-2.5 border bg-white shadow-xs" style="border-radius: 8px;">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1 text-muted">
                                    <i class="bi bi-calendar-check text-primary me-1"></i> Manufacturing Date (Mfg)
                                </label>
                                <input type="date" class="form-control form-control-sm pvm-input" id="pvm_mfg_date">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1 text-muted">
                                    <i class="bi bi-calendar-x text-danger me-1"></i> Expiry Date (FEFO Target)
                                </label>
                                <input type="date" class="form-control form-control-sm pvm-input border-danger" id="pvm_exp_date" title="Earliest expiring products will be prioritized for sale first (FEFO)">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold mb-1 text-muted">
                                    <i class="bi bi-tag text-secondary me-1"></i> Batch / Lot / Description
                                </label>
                                <input type="text" class="form-control form-control-sm pvm-input" id="pvm_description" placeholder="e.g. Lot # 01, Fresh Import">
                            </div>
                        </div>
                    </div>

                    <!-- Quick Presets & Bulk Fill Strip -->
                    <div class="card pvm-presets-card">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="fw-bold small text-muted me-1"><i class="bi bi-lightning-charge-fill text-warning"></i> Quick Sizes:</span>
                                <button type="button" class="btn btn-sm btn-outline-primary pvm-preset-btn" onclick="applySizePreset('shoes_39_45')">
                                    + Shoe 39–45
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary pvm-preset-btn" onclick="applySizePreset('shoes_40_45')">
                                    + Shoe 40–45
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary pvm-preset-btn" onclick="applySizePreset('clothing_s_xxl')">
                                    + Clothing S–XXL
                                </button>
                                <button type="button" class="btn btn-sm btn-success pvm-preset-btn" onclick="addNewVariantRowModal()">
                                    <i class="bi bi-plus-circle me-1"></i>+ Add Size / Color
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold small text-muted"><i class="bi bi-sliders text-info me-1"></i>Bulk Fill:</span>
                                <div class="input-group input-group-sm pvm-bulk-group" style="width: 140px;">
                                    <input type="number" class="form-control text-center" id="bulkQtyVal" placeholder="Qty">
                                    <button class="btn btn-outline-secondary" type="button" onclick="applyBulkQty()">All</button>
                                </div>
                                <div class="input-group input-group-sm pvm-bulk-group" style="width: 155px;">
                                    <input type="number" class="form-control text-center" id="bulkPriceVal" placeholder="Cost Price">
                                    <button class="btn btn-outline-secondary" type="button" onclick="applyBulkPurchasePrice()">All</button>
                                </div>
                                <div class="input-group input-group-sm pvm-bulk-group" style="width: 155px;">
                                    <input type="number" class="form-control text-center" id="bulkSalePriceVal" placeholder="Sale Price">
                                    <button class="btn btn-outline-secondary" type="button" onclick="applyBulkSalePrice()">All</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Variants Table -->
                    <div class="pvm-table-wrap">
                        <table class="table pvm-table" id="pvm_table">
                            <thead class="sticky-top">
                                <tr>
                                    <th style="width: 110px; padding-left: 12px;">Size</th>
                                    <th style="width: 110px;">Color</th>
                                    <th style="width: 140px;">Barcode</th>
                                    <th style="width: 120px;" class="text-center">Current Stock</th>
                                    <th style="width: 120px;" class="text-center pvm-qty-cell text-primary">Purchase Qty</th>
                                    <th style="width: 120px;" class="text-end">Cost Price</th>
                                    <th style="width: 120px;" class="text-end">Sale Price</th>
                                    <th style="width: 120px;" class="text-end">Subtotal</th>
                                    <th style="width: 45px;" class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="pvm_tbody">
                                <!-- Injected by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top d-flex justify-content-between py-2 px-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="small fw-semibold text-muted">Total Sizes: <strong id="pvm_tot_sizes" class="text-dark">0</strong></span>
                        <span class="small fw-semibold text-muted">Total Qty: <strong id="pvm_tot_qty" class="text-primary fs-6">0 Pcs</strong></span>
                        <span class="small fw-semibold text-muted">Total Cost: <strong id="pvm_tot_amt" class="text-success fs-6">Rs. 0.00</strong></span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn pvm-btn-cancel modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="button" class="btn pvm-btn-save" id="btnSaveVariantsModal">
                            <i class="bi bi-check2-circle me-1"></i> Save &amp; Apply to Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 3. QUICK VIEW BREAKDOWN MODAL (The 🔍 Quick Preview) --}}
    {{-- ======================================================== --}}
    <div class="modal fade" id="variantsPreviewQuickModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <h6 class="modal-title mb-0 fw-bold"><i class="bi bi-search text-primary me-1"></i> Size Breakdown: <span id="vpq_title"></span></h6>
                    <button type="button" class="close text-dark modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:1.6rem; line-height:1; opacity:0.8; cursor:pointer;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <div id="vpq_content"></div>
                </div>
                <div class="modal-footer py-1 px-3 bg-light border-top-0">
                    <button type="button" class="btn btn-secondary btn-sm px-3 modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 4. VENDOR PHOTO FULL PREVIEW MODAL --}}
    {{-- ======================================================== --}}
    <div class="modal fade" id="vendorPhotoFullModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="modal-title mb-0"><i class="bi bi-image me-1"></i> Vendor Invoice Photo Preview</h6>
                    <button type="button" class="close text-white modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:1.8rem; line-height:1; opacity:0.85; cursor:pointer;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-2 text-center bg-dark">
                    <img id="vendorPhotoFullImg" src="{{ $purchase->vendor_inv_photo ? asset($purchase->vendor_inv_photo) : '#' }}" alt="Vendor Invoice Full" style="max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 6px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Product Modal -->
    <div class="modal fade" id="quickAddProductModal" tabindex="-1" aria-labelledby="quickAddProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="quickAddProductModalLabel">⚡ Quick Add New Shoe Article</h5>
                    <button type="button" class="close text-dark modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:1.6rem; line-height:1; opacity:0.8; cursor:pointer;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="quickNewProductForm">
                    <div class="modal-body pt-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Article / Product Name *</label>
                            <input type="text" class="form-control" id="qnp_name" required placeholder="e.g. Oxford Derby Leather Shoe">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Category</label>
                                <select class="form-select" id="qnp_category">
                                    <option value="" selected>No Category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Brand</label>
                                <select class="form-select" id="qnp_brand">
                                    <option value="" selected>No Brand</option>
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Default Cost Price</label>
                                <input type="number" step="0.01" class="form-control" id="qnp_cost" value="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Default Sale Price</label>
                                <input type="number" step="0.01" class="form-control" id="qnp_sale" value="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="form-check form-switch p-2 bg-light rounded border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="qnp_gen_shoe_sizes" checked>
                            <label class="form-check-label fw-bold small text-dark" for="qnp_gen_shoe_sizes">
                                Pre-generate Shoe Sizes (39, 40, 41, 42, 43, 44, 45)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-secondary modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-arrow-right-circle me-1"></i> Create &amp; Configure Sizes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Add Vendor Modal -->
    <div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0 pb-2 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="addVendorModalLabel">Add New Vendor</h5>
                    <button type="button" class="close text-dark modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:1.6rem; line-height:1; opacity:0.8; cursor:pointer;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="quickAddVendorForm">
                    @csrf
                    <div class="modal-body pt-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Vendor Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="Enter vendor name">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Phone Number</label>
                                <input type="text" class="form-control" name="phone" placeholder="Optional">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Opening Balance</label>
                                <input type="number" step="0.01" class="form-control" name="opening_balance" value="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-muted">Address</label>
                            <textarea class="form-control" name="address" rows="2" placeholder="Optional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-secondary modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnQuickSaveVendor">Save Vendor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>

    <script>
        let rowVariantsMap = {};
        let activeModalRowId = null;
        let rowCounter = 0;

        // Photo Preview Functions
        function previewVendorPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#vendorPhotoThumb').attr('src', e.target.result);
                    $('#vendorPhotoFullImg').attr('src', e.target.result);
                    $('#vendorPhotoThumbWrap').removeClass('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function showFullPhotoModal() {
            $('#vendorPhotoFullModal').modal('show');
        }

        // Universal modal close handler
        $(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"], .modal-close-btn, .close, .btn-close', function(e) {
            e.preventDefault();
            const $modal = $(this).closest('.modal');
            if ($modal.length) {
                $modal.modal('hide');
            }
        });

        $(document).ready(function() {
            // Init Select2
            $('.select2').select2({
                width: '100%'
            });

            // Vendor Select Logic
            $('#vendorSelect').on('change', function() {
                const vendorId = $(this).val();
                if (!vendorId) {
                    $('#vendorInfoCard').addClass('d-none');
                    return;
                }

                $.get(`/vendor/${vendorId}/ledger-json`, function(data) {
                    $('#vi_mobile').text(data.vendor.phone || '—');
                    $('#vi_address').text(data.vendor.address || '—');
                    $('#vi_prev_bal').text(parseFloat(data.current_balance).toFixed(2));
                    $('#vendorInfoCard').removeClass('d-none');

                    $('#tPrev').text(parseFloat(data.current_balance).toFixed(2));
                    recalcAll();
                });
            });

            // Load vendor ledger balance initially if vendor is selected
            const initialVendorId = $('#vendorSelect').val();
            if (initialVendorId) {
                $.get(`/vendor/${initialVendorId}/ledger-json`, function(data) {
                    $('#vi_prev_bal').text(parseFloat(data.current_balance).toFixed(2));
                    $('#tPrev').text(parseFloat(data.current_balance).toFixed(2));
                    recalcAll();
                });
            }

            // PRELOAD EXISTING PURCHASE ITEMS
            const preloadedItems = @json($groupedItems ?? []);
            if (preloadedItems && preloadedItems.length > 0) {
                preloadedItems.forEach(item => {
                    const rowId = 'row_' + (++rowCounter);
                    addCustomRow(rowId, {
                        is_new: 0,
                        product_id: item.product_id,
                        product_name: item.product_name,
                        product_code: item.product_code,
                        unit: item.unit || 'Pcs',
                        total_qty: item.total_qty,
                        cost_price: item.price,
                        discount_percent: item.discount_percent,
                        discount_amount: item.discount_amount,
                        line_total: item.line_total,
                        mfg_date: item.mfg_date,
                        exp_date: item.exp_date,
                        description: item.description,
                        variants: item.variants || []
                    });
                });
                recalcAll();
            } else {
                addBlankRow();
            }

            // Add Row Button
            $('#btnAdd').click(function() {
                addBlankRow();
            });

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                if ($('#purchaseTableBody tr').length > 1) {
                    const rowId = $(this).closest('tr').data('row-id');
                    delete rowVariantsMap[rowId];
                    $(this).closest('tr').remove();
                    updateRowNumbers();
                    recalcAll();
                }
            });

            // Input changes -> recalc row and invoice
            $('#purchaseTableBody').on('input', '.main-qty-input, .price, .item-disc-percent', function() {
                recalcRow($(this).closest('tr'));
                recalcAll();
            });

            // Summary Inputs
            $('#billDiscount, #billDiscountPct, #extraCost').on('input', function() {
                recalcAll();
            });

            function normalizeDiscountInput() {
                let totalInlineDiscount = 0;
                $('#purchaseTableBody tr').each(function() {
                    const rowDiscAmt = parseFloat($(this).find('.item-disc-amt').val()) || 0;
                    totalInlineDiscount += rowDiscAmt;
                });

                let billDiscVal = parseFloat($('#billDiscount').val());
                if (isNaN(billDiscVal) || billDiscVal < totalInlineDiscount) {
                    $('#billDiscount').val(totalInlineDiscount.toFixed(2));
                }
                recalcAll();
            }

            $('#billDiscount, #billDiscountPct').on('blur', function() {
                normalizeDiscountInput();
            });

            // Payment Row Add
            $('#btnAddPayment').click(function() {
                const html = `
                    <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                        <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                            <option value="" selected disabled>Select Account</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                            @endforeach
                        </select>
                        <input type="number" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:140px">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>`;
                $('#paymentWrapper').append(html);
            });

            $(document).on('click', '.remove-payment', function() {
                $(this).closest('.payment-row').remove();
                calcTotalPaid();
            });

            $(document).on('input', '.payment-amount', function() {
                calcTotalPaid();
            });

            function calcTotalPaid() {
                let total = 0;
                $('.payment-amount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#totalPaid').text(total.toFixed(2));
                recalcAll();
            }

            // AJAX SUBMIT FUNCTION
            function submitPurchaseForm(actionStatus, $btn, btnOrigHtml) {
                normalizeDiscountInput();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

                $('#action').val(actionStatus);

                const formElem = document.getElementById('purchaseForm');
                const formData = new FormData(formElem);

                $.ajax({
                    url: "{{ route('purchase.update', $purchase->id) }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message || 'Purchase updated successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect_url || "{{ route('Purchase.home') }}";
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(btnOrigHtml);
                        let msg = 'Something went wrong.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            msg += '\n' + errors;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }

            // Save Draft Click
            $('#btnSaveOnly').click(function(e) {
                e.preventDefault();
                submitPurchaseForm('save_only', $(this), '<i class="bi bi-save me-1"></i> Save Draft');
            });

            // Confirm Purchase Click
            $('#btnConfirm').click(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Confirm Purchase?',
                    text: "This will approve the purchase, add stock, and update accounts.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Confirm it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitPurchaseForm('approved', $('#btnConfirm'), '<i class="bi bi-check-circle me-1"></i> Confirm Purchase');
                    }
                });
            });

            // Update Purchase Click (For already approved purchase)
            $('#btnUpdatePurchase').click(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Update Purchase?',
                    text: "Changes to quantities or prices will update stock movements and accounts.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitPurchaseForm('approved', $('#btnUpdatePurchase'), '<i class="bi bi-check2-circle me-1"></i> Update Purchase');
                    }
                });
            });

            // Quick Add Vendor
            $('#quickAddVendorForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnQuickSaveVendor');
                let originalText = $btn.text();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

                $.ajax({
                    url: "{{ route('vendors.store.ajax') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $btn.prop('disabled', false).text(originalText);
                        let vendorId = response.vendor ? response.vendor.id : null;
                        let vendorName = response.vendor ? response.vendor.name : $('#quickAddVendorForm input[name="name"]').val();

                        Swal.fire({
                            icon: 'success',
                            title: 'Vendor Added',
                            text: 'The vendor has been created successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#addVendorModal').modal('hide');
                            $('#quickAddVendorForm')[0].reset();
                            if (vendorId) {
                                let newOption = new Option(vendorName, vendorId, false, true);
                                $('#vendorSelect').append(newOption).trigger('change');
                            }
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).text(originalText);
                        let msg = 'Error adding vendor.';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Quick Add New Product Handler
            $('#quickNewProductForm').on('submit', function(e) {
                e.preventDefault();
                const name = $('#qnp_name').val().trim();
                const catId = $('#qnp_category').val();
                const brandId = $('#qnp_brand').val();
                const cost = parseFloat($('#qnp_cost').val()) || 0;
                const sale = parseFloat($('#qnp_sale').val()) || 0;
                const genShoeSizes = $('#qnp_gen_shoe_sizes').is(':checked');

                if (!name) return;

                $('#quickAddProductModal').modal('hide');
                $('#quickNewProductForm')[0].reset();

                const rowId = 'row_' + (++rowCounter);
                const $row = addCustomRow(rowId, {
                    is_new: 1,
                    product_id: '',
                    product_name: name,
                    category_id: catId,
                    brand_id: brandId,
                    cost_price: cost,
                    sale_price: sale
                });

                let defaultVariants = [];
                if (genShoeSizes) {
                    ['39', '40', '41', '42', '43', '44', '45'].forEach((sz) => {
                        defaultVariants.push({
                            size: sz,
                            color: '-',
                            barcode: '',
                            current_stock: 0,
                            qty: 0,
                            purchase_price: cost,
                            sale_price: sale
                        });
                    });
                } else {
                    defaultVariants.push({
                        size: 'Standard',
                        color: '-',
                        barcode: '',
                        current_stock: 0,
                        qty: 0,
                        purchase_price: cost,
                        sale_price: sale
                    });
                }

                rowVariantsMap[rowId] = defaultVariants;
                updateRowVariantDisplay(rowId);
                openVariantsModal(rowId);
            });
        });

        function addBlankRow() {
            const rowId = 'row_' + (++rowCounter);
            addCustomRow(rowId);
        }

        function updateRowNumbers() {
            $('#purchaseTableBody tr').each(function(idx) {
                $(this).find('.row-number').text(idx + 1);
            });
        }

        function addCustomRow(rowId, initialData = null) {
            const rowNum = $('#purchaseTableBody tr').length + 1;
            const isNew = initialData && initialData.is_new ? 1 : 0;
            const prodId = initialData && initialData.product_id ? initialData.product_id : '';
            const prodName = initialData ? (initialData.product_name || '') : '';
            const prodCode = initialData ? (initialData.product_code || '') : '';
            const catId = initialData ? (initialData.category_id || '') : '';
            const brandId = initialData ? (initialData.brand_id || '') : '';
            const costPrice = initialData ? (initialData.cost_price || 0) : 0;
            const totalQty = initialData ? (initialData.total_qty || 0) : 0;
            const discPct = initialData ? (initialData.discount_percent || 0) : 0;
            const discAmt = initialData ? (initialData.discount_amount || 0) : 0;
            const lineTot = initialData ? (initialData.line_total || 0) : 0;
            const unitVal = initialData && initialData.unit ? initialData.unit : 'Pcs';

            const html = `
            <tr id="${rowId}" data-row-id="${rowId}">
                <td class="text-center align-middle fw-bold text-muted row-number">${rowNum}</td>
                <td class="col-product">
                    ${isNew ? `
                        <div class="px-2 py-1.5">
                            <span class="badge bg-success me-1">NEW</span>
                            <strong class="text-dark">${prodName}</strong>
                            <input type="hidden" name="product_id[]" value="">
                        </div>
                    ` : `
                        <select class="form-select product-select2" name="product_id[]">
                            ${prodId ? `<option value="${prodId}" selected>${prodName} ${prodCode ? '(' + prodCode + ')' : ''}</option>` : ''}
                        </select>
                    `}
                    <input type="hidden" name="is_new[]" class="hidden-is-new" value="${isNew}">
                    <input type="hidden" name="new_product_name[]" class="hidden-new-name" value="${prodName}">
                    <input type="hidden" name="new_category_id[]" class="hidden-new-cat" value="${catId}">
                    <input type="hidden" name="new_brand_id[]" class="hidden-new-brand" value="${brandId}">
                    <input type="hidden" name="product_variants_json[]" class="hidden-variants-json" value="">
                    <input type="hidden" name="color[]" class="hidden-variant-data" value="">
                    <input type="hidden" name="size_mode[]" class="hidden-size-mode" value="pieces">
                    <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="1">
                    <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="0">
                    <input type="hidden" name="mfg_date[]" class="hidden-mfg-date" value="${initialData && initialData.mfg_date ? initialData.mfg_date : ''}">
                    <input type="hidden" name="exp_date[]" class="hidden-exp-date" value="${initialData && initialData.exp_date ? initialData.exp_date : ''}">
                    <input type="hidden" name="description[]" class="hidden-item-desc" value="${initialData && initialData.description ? initialData.description : ''}">
                    <div class="row-batch-pill-wrap px-2 py-0.5 ${initialData && (initialData.exp_date || initialData.mfg_date) ? '' : 'd-none'}">
                        <span class="badge bg-warning-subtle text-dark border batch-pill" style="font-size: 10.5px;">
                            <i class="bi bi-calendar-event text-danger me-1"></i><span class="batch-pill-text">${initialData && initialData.exp_date ? 'Exp: ' + initialData.exp_date : (initialData && initialData.mfg_date ? 'Mfg: ' + initialData.mfg_date : '')}</span>
                        </span>
                    </div>
                </td>
                <td class="text-center align-middle">
                    <div class="d-flex align-items-center justify-content-center gap-1.5">
                        <button type="button" class="btn btn-sm btn-outline-warning btn-edit-variants" onclick="openVariantsModal('${rowId}')" title="Adjust variants, sizes & quantities">
                            <i class="bi bi-pencil-square me-1"></i><span class="variants-badge-text">Set Sizes (0)</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-view-variants" onclick="previewVariantsQuick('${rowId}')" title="Quick size breakdown">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </td>
                <td class="text-center align-middle">
                    <span class="badge bg-light text-dark border">${unitVal}</span>
                    <input type="hidden" name="unit[]" class="unit-input-val" value="${unitVal}">
                </td>
                <td>
                    <input type="number" step="any" min="0" name="qty[]" class="form-control text-center main-qty-input fw-bold text-primary" value="${totalQty}" placeholder="Qty">
                </td>
                <td>
                    <input type="number" step="0.01" name="price[]" class="form-control text-end price fw-semibold" value="${costPrice}">
                </td>
                <td>
                    <input type="number" step="0.01" name="item_discount[]" class="form-control text-end item-disc-percent" value="${discPct}">
                </td>
                <td>
                    <input type="number" class="form-control text-end input-readonly item-disc-amt" value="${discAmt.toFixed ? discAmt.toFixed(2) : discAmt}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control text-end input-readonly row-total fw-bold text-dark" value="${lineTot.toFixed ? lineTot.toFixed(2) : lineTot}" readonly>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-x-lg"></i></button>
                </td>
            </tr>
            `;

            const $row = $(html);
            $('#purchaseTableBody').append($row);

            if (prodName) {
                $row.data('product_title', prodName);
                $row.data('product_code', prodCode);
            }

            // Set initial variants if preloaded
            if (initialData && initialData.variants && initialData.variants.length > 0) {
                rowVariantsMap[rowId] = initialData.variants;
                $row.find('.hidden-variants-json').val(JSON.stringify(initialData.variants));
                updateRowVariantDisplay(rowId);
            }

            if (!isNew) {
                initProductSelect2($row.find('.product-select2'), rowId);
            }

            return $row;
        }

        function initProductSelect2($el, rowId) {
            $el.select2({
                placeholder: 'Search Existing Product (Name / SKU / Barcode)',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route('purchase.search_master_products') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term || '' };
                    },
                    processResults: function(data) {
                        return {
                            results: (data || []).map(p => ({
                                id: p.id,
                                text: `${p.item_name} (${p.item_code})`,
                                product: p
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });

            $el.on('select2:select', function(e) {
                const prod = e.params.data.product;
                const $row = $('#' + rowId);

                $row.find('.price').val(prod.purchase_price || 0);
                $row.data('product_title', prod.item_name);
                $row.data('product_code', prod.item_code);

                $.get(`/purchase/product-variants/${prod.id}`, function(res) {
                    let vars = res.variants || [];
                    if (vars.length === 0) {
                        vars = [{
                            size: 'Standard',
                            color: '-',
                            barcode: prod.barcode_path || '',
                            current_stock: 0,
                            qty: 0,
                            purchase_price: prod.purchase_price || 0,
                            sale_price: prod.sale_price || 0
                        }];
                    } else {
                        vars.forEach(v => {
                            v.qty = 0;
                            if (!v.purchase_price) v.purchase_price = prod.purchase_price || 0;
                            if (!v.sale_price) v.sale_price = prod.sale_price || 0;
                        });
                    }

                    rowVariantsMap[rowId] = vars;
                    updateRowVariantDisplay(rowId);
                    openVariantsModal(rowId);
                });
            });
        }

        // ==========================================
        // VARIANTS MODAL LOGIC (📝 Button)
        // ==========================================
        function openVariantsModal(rowId) {
            activeModalRowId = rowId;
            const $row = $('#' + rowId);
            const isNew = $row.find('.hidden-is-new').val() == '1';
            const prodSelect = $row.find('.product-select2');
            const prodId = prodSelect.val();

            if (!isNew && !prodId) {
                Swal.fire({
                    icon: 'info',
                    title: 'Pehle Product Select Karein',
                    text: 'Baraye meharbani pehle product choose / search karein taake uski sizes aur stock load ho sakein.',
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Select Product'
                }).then(() => {
                    prodSelect.select2('open');
                });
                return;
            }

            let title = isNew ? $row.find('.hidden-new-name').val() : ($row.find('.product-select2 option:selected').text() || $row.data('product_title') || 'Product');
            let sku = $row.data('product_code') || (isNew ? 'New Article' : '—');

            $('#pvm_product_title').text(title);
            $('#pvm_sku_badge').text('SKU: ' + sku);

            $('#pvm_mfg_date').val($row.find('.hidden-mfg-date').val() || '');
            $('#pvm_exp_date').val($row.find('.hidden-exp-date').val() || '');
            $('#pvm_description').val($row.find('.hidden-item-desc').val() || '');

            let variants = rowVariantsMap[rowId] || [];

            if (variants.length === 0) {
                const defaultCost = parseFloat($row.find('.price').val()) || 0;
                variants = [
                    { size: '39', color: '-', barcode: '', current_stock: 0, qty: 0, purchase_price: defaultCost, sale_price: 0 },
                    { size: '40', color: '-', barcode: '', current_stock: 0, qty: 0, purchase_price: defaultCost, sale_price: 0 },
                    { size: '41', color: '-', barcode: '', current_stock: 0, qty: 0, purchase_price: defaultCost, sale_price: 0 },
                    { size: '42', color: '-', barcode: '', current_stock: 0, qty: 0, purchase_price: defaultCost, sale_price: 0 },
                    { size: '43', color: '-', barcode: '', current_stock: 0, qty: 0, purchase_price: defaultCost, sale_price: 0 },
                    { size: '44', color: '-', barcode: '', current_stock: 0, qty: 0, purchase_price: defaultCost, sale_price: 0 },
                    { size: '45', color: '-', barcode: '', current_stock: 0, qty: 0, purchase_price: defaultCost, sale_price: 0 }
                ];
                rowVariantsMap[rowId] = variants;
            }

            renderVariantsTableModal(variants);
            $('#productVariantsModal').modal('show');
        }

        function renderVariantsTableModal(variants) {
            const $tbody = $('#pvm_tbody');
            $tbody.empty();

            variants.forEach((v, index) => {
                const qty = parseFloat(v.qty) || 0;
                const cost = parseFloat(v.purchase_price) || 0;
                const sub = (qty * cost).toFixed(2);
                const curStock = v.current_stock !== undefined ? v.current_stock : 0;
                const stockBadgeClass = curStock > 0 ? 'bg-success text-white' : 'bg-light text-muted border';

                const tr = `
                <tr data-var-index="${index}">
                    <td style="padding-left: 12px; width: 110px;">
                        <input type="text" class="pvm-input text-center fw-bold var-size-input" value="${v.size || '-'}" placeholder="Size">
                    </td>
                    <td style="width: 110px;">
                        <input type="text" class="pvm-input text-center var-color-input" value="${v.color || '-'}" placeholder="Color">
                    </td>
                    <td style="width: 140px;">
                        <input type="text" class="pvm-input text-center font-monospace var-barcode-input" value="${v.barcode || ''}" placeholder="Barcode">
                    </td>
                    <td class="text-center" style="width: 120px;">
                        <span class="badge ${stockBadgeClass} px-2.5 py-1.5">${curStock} In-Stock</span>
                    </td>
                    <td class="pvm-qty-cell text-center" style="width: 120px;">
                        <input type="number" step="any" min="0" class="pvm-input pvm-qty-input text-center fw-bold var-qty-input" value="${qty}" placeholder="0">
                    </td>
                    <td style="width: 120px;">
                        <input type="number" step="0.01" min="0" class="pvm-input text-end fw-semibold var-cost-input" value="${cost}">
                    </td>
                    <td style="width: 120px;">
                        <input type="number" step="0.01" min="0" class="pvm-input text-end var-sale-input" value="${v.sale_price || 0}">
                    </td>
                    <td class="text-end fw-bold text-dark pe-3 var-subtotal-cell" style="width: 120px; font-size: 13px;">
                        ${sub}
                    </td>
                    <td class="text-center" style="width: 45px;">
                        <button type="button" class="btn btn-sm btn-outline-danger pvm-trash-btn" onclick="removeVariantRowModal(${index})" title="Delete size">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
                $tbody.append(tr);
            });

            recalcModalSummary();
        }

        $('#pvm_tbody').on('input', '.var-qty-input, .var-cost-input, .var-size-input, .var-color-input, .var-sale-input', function() {
            const $tr = $(this).closest('tr');
            const qty = parseFloat($tr.find('.var-qty-input').val()) || 0;
            const cost = parseFloat($tr.find('.var-cost-input').val()) || 0;
            $tr.find('.var-subtotal-cell').text((qty * cost).toFixed(2));
            recalcModalSummary();
        });

        function recalcModalSummary() {
            let totalSizes = 0;
            let totalQty = 0;
            let totalAmt = 0;

            $('#pvm_tbody tr').each(function() {
                totalSizes++;
                const qty = parseFloat($(this).find('.var-qty-input').val()) || 0;
                const cost = parseFloat($(this).find('.var-cost-input').val()) || 0;
                totalQty += qty;
                totalAmt += (qty * cost);
            });

            $('#pvm_tot_sizes').text(totalSizes);
            $('#pvm_tot_qty').text(totalQty + ' Pcs');
            $('#pvm_tot_amt').text('Rs. ' + totalAmt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }

        function addNewVariantRowModal() {
            if (!activeModalRowId) return;
            const cost = parseFloat($('#bulkPriceVal').val()) || (parseFloat($('#' + activeModalRowId).find('.price').val()) || 0);
            const sale = parseFloat($('#bulkSalePriceVal').val()) || 0;

            const newVar = {
                size: '',
                color: '-',
                barcode: '',
                current_stock: 0,
                qty: 0,
                purchase_price: cost,
                sale_price: sale
            };

            const variants = collectModalVariantsData();
            variants.push(newVar);
            renderVariantsTableModal(variants);
        }

        function removeVariantRowModal(index) {
            let variants = collectModalVariantsData();
            if (variants.length > 1) {
                variants.splice(index, 1);
                renderVariantsTableModal(variants);
            } else {
                alert('At least one size / variant must remain.');
            }
        }

        function applySizePreset(presetKey) {
            const existing = collectModalVariantsData();
            const cost = parseFloat($('#bulkPriceVal').val()) || (parseFloat($('#' + activeModalRowId).find('.price').val()) || 0);
            const sale = parseFloat($('#bulkSalePriceVal').val()) || 0;

            let targetSizes = [];
            if (presetKey === 'shoes_39_45') {
                targetSizes = ['39', '40', '41', '42', '43', '44', '45'];
            } else if (presetKey === 'shoes_40_45') {
                targetSizes = ['40', '41', '42', '43', '44', '45'];
            } else if (presetKey === 'clothing_s_xxl') {
                targetSizes = ['S', 'M', 'L', 'XL', 'XXL'];
            }

            targetSizes.forEach(sz => {
                const exists = existing.find(e => (e.size || '').toString().toLowerCase() === sz.toLowerCase());
                if (!exists) {
                    existing.push({
                        size: sz,
                        color: '-',
                        barcode: '',
                        current_stock: 0,
                        qty: 0,
                        purchase_price: cost,
                        sale_price: sale
                    });
                }
            });

            renderVariantsTableModal(existing);
        }

        function applyBulkQty() {
            const val = parseFloat($('#bulkQtyVal').val());
            if (isNaN(val)) return;
            $('#pvm_tbody .var-qty-input').val(val).trigger('input');
        }

        function applyBulkPurchasePrice() {
            const val = parseFloat($('#bulkPriceVal').val());
            if (isNaN(val)) return;
            $('#pvm_tbody .var-cost-input').val(val).trigger('input');
        }

        function applyBulkSalePrice() {
            const val = parseFloat($('#bulkSalePriceVal').val());
            if (isNaN(val)) return;
            $('#pvm_tbody .var-sale-input').val(val).trigger('input');
        }

        function collectModalVariantsData() {
            const list = [];
            $('#pvm_tbody tr').each(function() {
                list.push({
                    size: $(this).find('.var-size-input').val().trim() || '-',
                    color: $(this).find('.var-color-input').val().trim() || '-',
                    barcode: $(this).find('.var-barcode-input').val().trim() || '',
                    current_stock: $(this).find('.badge').text().replace(/[^0-9.]/g, '') || 0,
                    qty: parseFloat($(this).find('.var-qty-input').val()) || 0,
                    purchase_price: parseFloat($(this).find('.var-cost-input').val()) || 0,
                    sale_price: parseFloat($(this).find('.var-sale-input').val()) || 0
                });
            });
            return list;
        }

        // SAVE & APPLY VARIANTS TO ROW
        $('#btnSaveVariantsModal').click(function() {
            if (!activeModalRowId) return;

            const variants = collectModalVariantsData();
            rowVariantsMap[activeModalRowId] = variants;

            let totalQty = 0;
            let totalAmount = 0;
            let avgCost = 0;

            variants.forEach(v => {
                totalQty += (parseFloat(v.qty) || 0);
                totalAmount += ((parseFloat(v.qty) || 0) * (parseFloat(v.purchase_price) || 0));
            });

            if (totalQty > 0) {
                avgCost = (totalAmount / totalQty);
            } else if (variants.length > 0) {
                avgCost = parseFloat(variants[0].purchase_price) || 0;
            }

            const $row = $('#' + activeModalRowId);

            $row.find('.main-qty-input').val(totalQty);
            $row.find('.price').val(avgCost.toFixed(2));
            $row.find('.hidden-variants-json').val(JSON.stringify(variants));

            const mfg = $('#pvm_mfg_date').val() || '';
            const exp = $('#pvm_exp_date').val() || '';
            const desc = $('#pvm_description').val() || '';

            $row.find('.hidden-mfg-date').val(mfg);
            $row.find('.hidden-exp-date').val(exp);
            $row.find('.hidden-item-desc').val(desc);

            const $pillWrap = $row.find('.row-batch-pill-wrap');
            const $pillText = $row.find('.batch-pill-text');

            if (exp || mfg) {
                $pillWrap.removeClass('d-none');
                let pillLabel = '';
                if (exp) pillLabel += 'Exp: ' + exp;
                if (mfg) pillLabel += (pillLabel ? ' | ' : '') + 'Mfg: ' + mfg;
                if (desc) pillLabel += ' (' + desc + ')';
                $pillText.text(pillLabel);
            } else {
                $pillWrap.addClass('d-none');
            }

            updateRowVariantDisplay(activeModalRowId);
            recalcRow($row);
            recalcAll();

            $('#productVariantsModal').modal('hide');
        });

        function updateRowVariantDisplay(rowId) {
            const $row = $('#' + rowId);
            const variants = rowVariantsMap[rowId] || [];
            let totalQty = 0;
            let activeSizes = 0;

            variants.forEach(v => {
                const q = parseFloat(v.qty) || 0;
                totalQty += q;
                if (q > 0) activeSizes++;
            });

            const $btn = $row.find('.btn-edit-variants');
            if (totalQty > 0) {
                $btn.removeClass('btn-outline-warning').addClass('btn-primary');
                $btn.find('.variants-badge-text').text(`${variants.length} Sizes (${totalQty} Pcs)`);
            } else {
                $btn.removeClass('btn-primary').addClass('btn-outline-warning');
                $btn.find('.variants-badge-text').text(`Set Sizes (${variants.length})`);
            }
        }

        // Quick Preview Breakdown (🔍 Button)
        function previewVariantsQuick(rowId) {
            const $row = $('#' + rowId);
            const variants = rowVariantsMap[rowId] || [];
            const isNew = $row.find('.hidden-is-new').val() == '1';
            const title = isNew ? $row.find('.hidden-new-name').val() : ($row.find('.product-select2 option:selected').text() || $row.data('product_title') || 'Product');

            $('#vpq_title').text(title);

            let html = `
            <div class="d-flex flex-wrap gap-2 justify-content-center">
            `;

            if (variants.length === 0) {
                html += `<div class="text-muted p-3">No sizes configured yet. Click 📝 to set sizes.</div>`;
            } else {
                variants.forEach(v => {
                    const q = parseFloat(v.qty) || 0;
                    const badgeClass = q > 0 ? 'bg-primary text-white' : 'bg-light text-muted border';
                    html += `
                        <div class="border rounded p-2 text-center" style="min-width: 80px;">
                            <div class="small fw-bold text-dark">${v.size || '-'}</div>
                            <div class="badge ${badgeClass} fs-7 mt-1">${q} Pcs</div>
                            <div class="text-muted" style="font-size: 10px;">Rs. ${v.purchase_price || 0}</div>
                        </div>
                    `;
                });
            }

            html += `</div>`;
            $('#vpq_content').html(html);

            $('#variantsPreviewQuickModal').modal('show');
        }

        // Recalculate row
        function recalcRow($row) {
            const qty = parseFloat($row.find('.main-qty-input').val()) || 0;
            const price = parseFloat($row.find('.price').val()) || 0;
            const discPct = parseFloat($row.find('.item-disc-percent').val()) || 0;

            const gross = qty * price;
            const discAmt = gross * (discPct / 100);
            const lineTotal = Math.max(0, gross - discAmt);

            $row.find('.item-disc-amt').val(discAmt.toFixed(2));
            $row.find('.row-total').val(lineTotal.toFixed(2));
        }

        // Recalculate whole invoice
        function recalcAll() {
            let totalQty = 0;
            let subtotal = 0;
            let totalInlineDiscount = 0;

            $('#purchaseTableBody tr').each(function() {
                const qty = parseFloat($(this).find('.main-qty-input').val()) || 0;
                const total = parseFloat($(this).find('.row-total').val()) || 0;
                const rowDiscAmt = parseFloat($(this).find('.item-disc-amt').val()) || 0;

                totalQty += qty;
                subtotal += total;
                totalInlineDiscount += rowDiscAmt;
            });

            const grossSubtotal = subtotal + totalInlineDiscount;

            $('#tQty').text(totalQty);
            $('#tSub').text(subtotal.toFixed(2));
            $('#subtotalInput').val(subtotal.toFixed(2));

            let additionalDiscount = parseFloat($('#discountInput').val()) || 0;
            let billDiscVal = parseFloat($('#billDiscount').val());

            if ($(document.activeElement).is('#billDiscount') || $(document.activeElement).is('#billDiscountPct')) {
                if ($(document.activeElement).is('#billDiscountPct')) {
                    const pct = parseFloat($('#billDiscountPct').val()) || 0;
                    billDiscVal = grossSubtotal * (pct / 100);
                    $('#billDiscount').val(billDiscVal.toFixed(2));
                }
                if (!isNaN(billDiscVal)) {
                    additionalDiscount = Math.max(0, billDiscVal - totalInlineDiscount);
                } else {
                    additionalDiscount = 0;
                }
            } else {
                billDiscVal = totalInlineDiscount + additionalDiscount;
                $('#billDiscount').val(billDiscVal.toFixed(2));
            }
            
            const pct = grossSubtotal > 0 ? (billDiscVal / grossSubtotal) * 100 : 0;
            $('#billDiscountPct').val(pct.toFixed(2));
            $('#discountInput').val(additionalDiscount.toFixed(2));

            const extraCost = parseFloat($('#extraCost').val()) || 0;
            const net = subtotal - additionalDiscount + extraCost;

            $('#tPayable').text(net.toFixed(2));
            $('#netAmountInput').val(net.toFixed(2));
            $('#totalAmount').text(subtotal.toFixed(2));

            const prevBal = parseFloat($('#tPrev').text()) || 0;
            const totalPaid = parseFloat($('#totalPaid').text()) || 0;
            const totalPayable = (prevBal + net) - totalPaid;
            
            $('#tTotalPayable').text(totalPayable.toFixed(2));
        }
    </script>
@endsection
