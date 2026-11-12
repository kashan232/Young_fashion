@extends('admin_panel.layout.app')

@section('content')
    <style>
        .transfer-card {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .transfer-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 16px 16px 0 0;
        }

        .transfer-table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            background: #f1f5f9;
            padding: 12px 14px;
        }

        .transfer-table td {
            padding: 10px 14px;
            vertical-align: middle;
        }

        .size-badge {
            font-size: 0.85rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .pill-size {
            transition: all 0.2s ease;
            user-select: none;
        }

        .pill-size:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .modal-header-variants {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white;
            padding: 18px 25px;
        }

        @media (min-width: 992px) {
            .modal-xl-custom {
                max-width: 1100px !important;
                width: 92% !important;
            }
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-2">
        <div class="card transfer-card border-0 mb-4">
            <div class="transfer-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-white"><i class="fas fa-exchange-alt me-2 text-primary"></i> Transfer Warehouse Stock</h5>
                    <p class="text-white-50 mb-0 small">Transfer inventory between warehouses or to retail shop with live variant & size-level stock tracking</p>
                </div>
                <a href="{{ route('stock_transfers.index') }}" class="btn btn-outline-light rounded-pill btn-sm px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Transfer List
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('stock_transfers.store') }}" method="POST" id="transferForm">
                    @csrf

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Warehouse Selection Section -->
                    <div class="row g-3 mb-4 p-3 bg-light rounded-3 border align-items-center">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark small text-uppercase">
                                <i class="fas fa-warehouse text-danger me-1"></i> From Warehouse (Source)
                            </label>
                            <select name="from_warehouse_id" id="from_warehouse_id" class="form-select form-control" required>
                                <option value="">-- Select Source Warehouse --</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ (isset($selectedWarehouseId) && $selectedWarehouseId == $warehouse->id) ? 'selected' : '' }}>
                                        {{ $warehouse->warehouse_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Stock will be deducted from this warehouse.</small>
                        </div>

                        <div class="col-md-2 text-center d-flex flex-column justify-content-center align-items-center">
                            <div class="rounded-circle bg-white border shadow-sm p-2 my-2 text-muted">
                                <i class="fas fa-arrow-right fa-lg text-primary d-none d-md-inline"></i>
                                <i class="fas fa-arrow-down fa-lg text-primary d-inline d-md-none"></i>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark small text-uppercase mb-0">
                                    <i class="fas fa-store text-success me-1"></i> Destination
                                </label>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="to_shop" value="1" id="toShop" {{ !empty($toShop) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-primary" for="toShop">Transfer to Shop</label>
                                </div>
                            </div>
                            <div id="toWarehouseWrapper">
                                <select name="to_warehouse_id" id="to_warehouse_id" class="form-select form-control" {{ !empty($toShop) ? 'disabled' : '' }}>
                                    <option value="">-- Select Destination Warehouse --</option>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1" id="toWhHelp">
                                    @if (!empty($toShop))
                                        <i class="fas fa-check-circle text-success me-1"></i> Stock will be transferred directly to <strong>Main Shop</strong> (POS retail showroom stock).
                                    @else
                                        Stock will be credited to this warehouse.
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Products & Variants Table -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fas fa-boxes me-1 text-primary"></i> Transfer Items & Variant Sizes
                        </h6>
                        <span class="badge bg-light text-muted border" id="rowCounter">1 Item</span>
                    </div>

                    <div class="table-responsive rounded-3 border mb-3">
                        <table class="table table-hover align-middle transfer-table mb-0" id="product_table">
                            <thead>
                                <tr>
                                    <th style="width: 33%;">Product</th>
                                    <th style="width: 35%;">Size / Variant</th>
                                    <th style="width: 12%;" class="text-center">Avail. Stock</th>
                                    <th style="width: 15%;" class="text-center">Transfer Qty</th>
                                    <th style="width: 5%;" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="product_body">
                                <tr class="product_row">
                                    <td>
                                        <select name="product_id[]" class="form-control form-select product-select" required>
                                            <option value="">-- Select Product --</option>
                                            @php
                                                $inStock = $products->filter(fn($p) => $p->warehouse_pieces > 0);
                                                $outStock = $products->filter(fn($p) => $p->warehouse_pieces <= 0);
                                            @endphp
                                            @if ($inStock->isNotEmpty())
                                                <optgroup label="Available in this Warehouse">
                                                    @foreach ($inStock as $p)
                                                        <option value="{{ $p->id }}" {{ (isset($selectedProductId) && $selectedProductId == $p->id) ? 'selected' : '' }}>
                                                            {{ $p->item_name }} ({{ $p->item_code }}){{ $p->has_variants ? ' (Sizes Available)' : '' }} — [{{ $p->warehouse_pieces }} Pcs in WH]
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                            @if ($outStock->isNotEmpty())
                                                <optgroup label="Other Products (0 Stock in this WH)">
                                                    @foreach ($outStock as $p)
                                                        <option value="{{ $p->id }}" {{ (isset($selectedProductId) && $selectedProductId == $p->id) ? 'selected' : '' }} disabled style="color: #94a3b8;">
                                                            {{ $p->item_name }} ({{ $p->item_code }}){{ $p->has_variants ? ' (Sizes Available)' : '' }} — [0 Pcs in WH]
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <div class="variant-container">
                                            <div class="d-flex align-items-center gap-2">
                                                <select class="form-control form-select variant-select" {{ !empty($initialVariants) ? '' : 'disabled' }}>
                                                    @if (!empty($initialVariants))
                                                        <option value="">-- Choose Size / Variant --</option>
                                                        @foreach ($initialVariants as $v)
                                                            <option value="{{ $v['variant_key'] }}" data-stock="{{ $v['current_stock'] }}" data-size="{{ $v['size'] }}" data-color="{{ $v['color'] }}" {{ $v['current_stock'] <= 0 ? 'disabled style="color: #94a3b8;"' : '' }}>
                                                                Size: {{ $v['size'] }} | Color: {{ $v['color'] }} ({{ $v['current_stock'] }} Pcs in WH)
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="">Select Product First</option>
                                                    @endif
                                                </select>
                                                <button type="button" class="btn btn-sm btn-outline-primary text-nowrap rounded-pill px-2 py-1 btn-view-variants" style="{{ !empty($initialVariants) ? '' : 'display:none;' }} font-size: 0.78rem;" title="View all size stocks for this product in this warehouse">
                                                    <i class="fas fa-layer-group"></i> Breakdown
                                                </button>
                                            </div>
                                            @if (!empty($initialVariants))
                                                <div class="variant-pills-wrap mt-1 d-flex flex-wrap gap-1">
                                                    @foreach ($initialVariants as $v)
                                                        @if ($v['current_stock'] > 0)
                                                            <span class="badge badge-light border text-dark pill-size cursor-pointer me-1 mb-1 p-1" style="cursor:pointer; font-size: 0.76rem;" data-key="{{ $v['variant_key'] }}" title="Click to select Size {{ $v['size'] }}">
                                                                Size <b>{{ $v['size'] }}</b> ({{ $v['color'] }}): <span class="text-success fw-bold">{{ $v['current_stock'] }}</span>
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <input type="hidden" name="variant_key[]" class="input-variant-key">
                                        <input type="hidden" name="size[]" class="input-size">
                                        <input type="hidden" name="color[]" class="input-color">
                                    </td>
                                    <td class="text-center">
                                        <input type="text" class="form-control text-center fw-bold text-success stock-display" placeholder="--" readonly>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="quantity[]" class="form-control text-center fw-bold quantity-input" min="1" placeholder="0" required disabled>
                                        <div class="invalid-feedback text-danger small mt-1 error-msg" style="display:none;"></div>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle remove-row" title="Remove Item">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="add_row_btn">
                            <i class="fas fa-plus me-1"></i> Add Another Product / Size
                        </button>
                        <div class="fs-6 fw-bold text-dark">
                            Total Transfer Quantity: <span class="text-primary fs-5" id="totalTransferQty">0</span> Pcs
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase">Remarks / Reason</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes, dispatch invoice number, or reasons..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('stock_transfers.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm" id="submitTransferBtn">
                            <i class="fas fa-check-circle me-1"></i> Transfer Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Variant Breakdown Modal -->
    <div class="modal fade" id="variantsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-xl-custom modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                <div class="modal-header modal-header-variants d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title mb-0" id="vModalTitle">
                            <i class="fas fa-layer-group me-2"></i> Variant & Size Stock Breakdown
                        </h5>
                        <small class="opacity-75" id="vModalSubtitle">Warehouse details</small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem; background: transparent; border: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div id="vModalLoader" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 small">Loading live variant stock breakdown...</p>
                    </div>

                    <div id="vModalContent" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border mb-3 flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <img id="vModalImg" src="" class="rounded me-3 border mr-3" style="width: 52px; height: 52px; object-fit: cover; display: none;">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark fs-5" id="vModalProductName">--</h6>
                                    <span class="text-muted small" id="vModalWhName">--</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted text-uppercase fw-semibold">Total Warehouse Stock</div>
                                <div class="fs-4 fw-bold text-primary" id="vModalTotalPieces">0 Pcs</div>
                            </div>
                        </div>

                        <div class="table-responsive variant-table-container" style="max-height: 440px;">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="small fw-bold">Size</th>
                                        <th class="small fw-bold">Color</th>
                                        <th class="small fw-bold">Variant Title</th>
                                        <th class="small fw-bold">Barcode</th>
                                        <th class="small fw-bold text-center">Available Stock</th>
                                        <th class="small fw-bold text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="vModalTableBody">
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-3" id="btnFillAllSizes">
                        <i class="fas fa-plus-circle me-1"></i> Add All Available Sizes to Transfer
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let availableProducts = @json($products);
    let activeRowForModal = null;
    let currentModalVariants = [];
    let currentModalProductId = null;

    $(document).ready(function() {
        // Toggle Transfer to Shop
        $('#toShop').on('change', function() {
            if ($(this).is(':checked')) {
                $('#to_warehouse_id').val('').prop('disabled', true);
                $('#toWhHelp').html('<i class="fas fa-check-circle text-success me-1"></i> Stock will be transferred directly to <strong>Main Shop</strong> (POS retail showroom stock).').addClass('text-primary');
            } else {
                $('#to_warehouse_id').prop('disabled', false);
                $('#toWhHelp').text('Stock will be credited to this warehouse.').removeClass('text-primary');
            }
        });

        // Source Warehouse Change
        $('#from_warehouse_id').on('change', function() {
            let whId = $(this).val();
            if (!whId) {
                availableProducts = [];
                resetAllRows();
                return;
            }

            $.ajax({
                url: "{{ route('get.products.by.warehouse') }}",
                data: { warehouse_id: whId },
                success: function(products) {
                    availableProducts = products || [];
                    populateProductDropdowns();
                },
                error: function() {
                    Swal.fire('Error', 'Could not load products for the selected warehouse.', 'error');
                }
            });
        });

        // Add New Row
        $('#add_row_btn').on('click', function() {
            addNewRow();
        });

        // Remove Row
        $(document).on('click', '.remove-row', function() {
            if ($('#product_body tr').length > 1) {
                $(this).closest('tr').remove();
                updateRowCounter();
                calculateTotalQuantity();
            } else {
                let tr = $(this).closest('tr');
                tr.find('.product-select').val('');
                tr.find('.variant-select').html('<option value="">Select Product First</option>').prop('disabled', true);
                tr.find('.variant-pills-wrap').remove();
                tr.find('.btn-view-variants').hide();
                tr.find('.stock-display').val('');
                tr.find('.quantity-input').val('').prop('disabled', true);
                tr.find('.input-variant-key, .input-size, .input-color').val('');
                calculateTotalQuantity();
            }
        });

        // Product Selection Change
        $(document).on('change', '.product-select', function() {
            let row = $(this).closest('tr');
            let productId = $(this).val();
            let whId = $('#from_warehouse_id').val();

            if (!productId) {
                row.find('.variant-select').html('<option value="">Select Product First</option>').prop('disabled', true);
                row.find('.variant-pills-wrap').remove();
                row.find('.btn-view-variants').hide();
                row.find('.stock-display').val('');
                row.find('.quantity-input').val('').prop('disabled', true);
                row.find('.input-variant-key, .input-size, .input-color').val('');
                calculateTotalQuantity();
                return;
            }

            if (!whId) {
                if ($('#from_warehouse_id option').length > 1) {
                    $('#from_warehouse_id').prop('selectedIndex', 1);
                    whId = $('#from_warehouse_id').val();
                } else {
                    Swal.fire('Select Warehouse', 'Please select a source warehouse first.', 'warning');
                    return;
                }
            }

            let vSelect = row.find('.variant-select');
            vSelect.html('<option value="">Loading sizes & stock...</option>').prop('disabled', true);
            row.find('.variant-pills-wrap').remove();

            $.ajax({
                url: "{{ route('stock_transfers.product-variants') }}",
                data: { warehouse_id: whId, product_id: productId },
                success: function(res) {
                    vSelect.empty();
                    row.data('variantsData', res);

                    if (res.has_variants && res.variants && res.variants.length > 0) {
                        vSelect.prop('disabled', false);
                        vSelect.append('<option value="">-- Choose Size / Variant --</option>');

                        let pillsHtml = '<div class="variant-pills-wrap mt-1 d-flex flex-wrap gap-1">';
                        res.variants.forEach(function(v) {
                            let label = `Size: ${v.size} | Color: ${v.color} (${v.current_stock} Pcs in WH)`;
                            let opt = $(`<option value="${v.variant_key}" data-stock="${v.current_stock}" data-size="${v.size}" data-color="${v.color}">${label}</option>`);
                            opt.data('stock', v.current_stock);
                            opt.data('size', v.size);
                            opt.data('color', v.color);
                            if (v.current_stock <= 0) {
                                opt.attr('disabled', true).css('color', '#94a3b8');
                            }
                            vSelect.append(opt);

                            if (v.current_stock > 0) {
                                pillsHtml += `<span class="badge badge-light border text-dark pill-size me-1 mb-1 p-1" style="cursor:pointer; font-size: 0.76rem;" data-key="${v.variant_key}" title="Click to select Size ${v.size}">Size <b>${v.size}</b> (${v.color}): <span class="text-success fw-bold">${v.current_stock}</span></span>`;
                            }
                        });
                        pillsHtml += '</div>';
                        row.find('.variant-container').append(pillsHtml);

                        row.find('.btn-view-variants').show();
                        row.find('.stock-display').val('');
                        row.find('.quantity-input').val('').prop('disabled', true);
                    } else {
                        // Standard product without variants
                        vSelect.append('<option value="" data-stock="' + res.total_pieces + '" data-size="-" data-color="-">Standard Product (No Size/Variant)</option>');
                        vSelect.prop('disabled', true);
                        row.find('.btn-view-variants').hide();

                        row.find('.input-variant-key').val('');
                        row.find('.input-size').val('-');
                        row.find('.input-color').val('-');
                        row.find('.stock-display').val(res.total_pieces + ' Pcs');
                        row.find('.quantity-input').prop('disabled', false).attr('max', res.total_pieces).focus();
                    }
                },
                error: function(xhr) {
                    vSelect.html('<option value="">Error loading variants</option>');
                    Swal.fire('Error', 'Could not load variants for this product.', 'error');
                }
            });
        });

        // Click Size Pill to Select
        $(document).on('click', '.pill-size', function() {
            let row = $(this).closest('tr');
            let key = $(this).data('key');
            if (key) {
                row.find('.variant-select').val(key).trigger('change');
            }
        });

        // Variant Selection Change
        $(document).on('change', '.variant-select', function() {
            let row = $(this).closest('tr');
            let selectedOpt = $(this).find('option:selected');
            let stock = parseFloat(selectedOpt.data('stock') !== undefined ? selectedOpt.data('stock') : selectedOpt.attr('data-stock')) || 0;
            let size = selectedOpt.data('size') || selectedOpt.attr('data-size');
            let color = selectedOpt.data('color') || selectedOpt.attr('data-color');
            let vKey = $(this).val();

            if (vKey !== '') {
                row.find('.input-variant-key').val(vKey);
                row.find('.input-size').val(size || '-');
                row.find('.input-color').val(color || '-');
                row.find('.stock-display').val(stock + ' Pcs');
                row.find('.quantity-input').prop('disabled', false).attr('max', stock).focus();

                // Highlight active pill
                row.find('.pill-size').removeClass('badge-primary text-white').addClass('badge-light text-dark');
                row.find(`.pill-size[data-key="${vKey}"]`).removeClass('badge-light text-dark').addClass('badge-primary text-white');

                if (stock <= 0) {
                    row.find('.quantity-input').prop('disabled', true);
                    row.find('.error-msg').text('Selected variant is out of stock in this warehouse.').show();
                } else {
                    row.find('.error-msg').hide();
                }
            } else {
                row.find('.input-variant-key, .input-size, .input-color').val('');
                row.find('.stock-display').val('');
                row.find('.quantity-input').val('').prop('disabled', true);
                row.find('.error-msg').hide();
                row.find('.pill-size').removeClass('badge-primary text-white').addClass('badge-light text-dark');
            }
            calculateTotalQuantity();
        });

        // Quantity Input Change
        $(document).on('input', '.quantity-input', function() {
            let val = parseFloat($(this).val()) || 0;
            let max = parseFloat($(this).attr('max')) || 0;
            let errorDiv = $(this).siblings('.error-msg');

            if (val > max) {
                $(this).addClass('is-invalid');
                errorDiv.text(`Only ${max} available!`).show();
            } else if (val <= 0 && $(this).val() !== '') {
                $(this).addClass('is-invalid');
                errorDiv.text('Min 1 piece.').show();
            } else {
                $(this).removeClass('is-invalid');
                errorDiv.hide();
            }

            calculateTotalQuantity();
        });

        // Open Breakdown Modal from Row
        $(document).on('click', '.btn-view-variants', function() {
            activeRowForModal = $(this).closest('tr');
            let productId = activeRowForModal.find('.product-select').val();
            let whId = $('#from_warehouse_id').val();
            let whName = $('#from_warehouse_id option:selected').text();
            let prodName = activeRowForModal.find('.product-select option:selected').text();

            if (!productId || !whId) return;

            openBreakdownModal(productId, whId, prodName, whName);
        });

        // Fill All Sizes into Transfer from Breakdown Modal
        $('#btnFillAllSizes').on('click', function() {
            if (!currentModalVariants || currentModalVariants.length === 0 || !currentModalProductId) return;

            let availableVariants = currentModalVariants.filter(v => v.current_stock > 0);
            if (availableVariants.length === 0) {
                Swal.fire('No Stock', 'None of the sizes for this product have available stock.', 'warning');
                return;
            }

            // Populate the active row with the first available variant
            let firstV = availableVariants[0];
            if (activeRowForModal) {
                activeRowForModal.find('.product-select').val(currentModalProductId);
                activeRowForModal.find('.variant-select').val(firstV.variant_key).trigger('change');
            }

            // Create new rows for the remaining variants
            for (let i = 1; i < availableVariants.length; i++) {
                let v = availableVariants[i];
                let newRow = addNewRow();
                newRow.find('.product-select').val(currentModalProductId).trigger('change');
                setTimeout((function(targetRow, variantKey) {
                    return function() {
                        targetRow.find('.variant-select').val(variantKey).trigger('change');
                    };
                })(newRow, v.variant_key), 400);
            }

            $('#variantsModal').modal('hide');
        });

        // Form Submission Validation
        $('#transferForm').on('submit', function(e) {
            let fromWh = $('#from_warehouse_id').val();
            let toWh = $('#to_warehouse_id').val();
            let toShop = $('#toShop').is(':checked');

            if (!fromWh) {
                e.preventDefault();
                Swal.fire('Source Warehouse Required', 'Please select a source warehouse.', 'warning');
                return false;
            }

            if (!toShop && !toWh) {
                e.preventDefault();
                Swal.fire('Destination Required', 'Please select a destination warehouse or choose "Transfer to Shop".', 'warning');
                return false;
            }

            if (!toShop && fromWh && toWh && fromWh === toWh) {
                e.preventDefault();
                Swal.fire('Invalid Destination', 'Source and destination warehouses cannot be the same.', 'warning');
                return false;
            }

            let hasInvalid = false;
            let rowCount = 0;

            $('#product_body tr').each(function() {
                let pId = $(this).find('.product-select').val();
                if (!pId) return;

                rowCount++;
                let qtyInput = $(this).find('.quantity-input');
                let val = parseFloat(qtyInput.val()) || 0;
                let max = parseFloat(qtyInput.attr('max')) || 0;

                if (val <= 0 || (max > 0 && val > max)) {
                    hasInvalid = true;
                    qtyInput.addClass('is-invalid');
                }
            });

            if (rowCount === 0) {
                e.preventDefault();
                Swal.fire('No Products', 'Please add at least one product to transfer.', 'warning');
                return false;
            }

            if (hasInvalid) {
                e.preventDefault();
                Swal.fire('Check Quantities', 'Please enter valid transfer quantities for all selected items within available stock.', 'warning');
                return false;
            }
        });
    });

    function buildProductOptionsHtml(currentVal) {
        if (!availableProducts || availableProducts.length === 0) {
            return '<option value="">No stock in this warehouse</option>';
        }

        let inStockOptions = '';
        let outStockOptions = '';

        availableProducts.forEach(function(p) {
            let selected = (p.id == currentVal) ? 'selected' : '';
            let badge = p.has_variants ? ' (Sizes Available)' : '';
            let stockPieces = p.warehouse_pieces !== undefined ? p.warehouse_pieces : (p.total_pieces || 0);

            if (stockPieces > 0) {
                inStockOptions += `<option value="${p.id}" ${selected}>${p.item_name} (${p.item_code})${badge} — [${stockPieces} Pcs in WH]</option>`;
            } else {
                outStockOptions += `<option value="${p.id}" ${selected} disabled style="color: #94a3b8;">${p.item_name} (${p.item_code})${badge} — [0 Pcs in WH]</option>`;
            }
        });

        let html = '<option value="">-- Select Product --</option>';
        if (inStockOptions !== '') {
            html += `<optgroup label="Available in this Warehouse">${inStockOptions}</optgroup>`;
        }
        if (outStockOptions !== '') {
            html += `<optgroup label="Other Products (0 Stock in this WH)">${outStockOptions}</optgroup>`;
        }

        return html;
    }

    function populateProductDropdowns() {
        $('.product-select').each(function() {
            let currentVal = $(this).val();
            $(this).html(buildProductOptionsHtml(currentVal));
        });
    }

    function addNewRow() {
        let optionsHtml = buildProductOptionsHtml('');

        let newRow = $(`
            <tr class="product_row">
                <td>
                    <select name="product_id[]" class="form-control form-select product-select" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <div class="variant-container">
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-control form-select variant-select" disabled>
                                <option value="">Select Product First</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-primary text-nowrap rounded-pill px-2 py-1 btn-view-variants" style="display:none; font-size: 0.78rem;" title="View all size stocks for this product in this warehouse">
                                <i class="fas fa-layer-group"></i> Breakdown
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="variant_key[]" class="input-variant-key">
                    <input type="hidden" name="size[]" class="input-size">
                    <input type="hidden" name="color[]" class="input-color">
                </td>
                <td class="text-center">
                    <input type="text" class="form-control text-center fw-bold text-success stock-display" placeholder="--" readonly>
                </td>
                <td class="text-center">
                    <input type="number" name="quantity[]" class="form-control text-center fw-bold quantity-input" min="1" placeholder="0" required disabled>
                    <div class="invalid-feedback text-danger small mt-1 error-msg" style="display:none;"></div>
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle remove-row" title="Remove Item">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `);

        $('#product_body').append(newRow);
        updateRowCounter();
        return newRow;
    }

    function resetAllRows() {
        $('#product_body tr:not(:first)').remove();
        let firstRow = $('#product_body tr:first');
        firstRow.find('.product-select').html('<option value="">Select Source Warehouse First</option>');
        firstRow.find('.variant-select').html('<option value="">Select Product First</option>').prop('disabled', true);
        firstRow.find('.variant-pills-wrap').remove();
        firstRow.find('.btn-view-variants').hide();
        firstRow.find('.stock-display').val('');
        firstRow.find('.quantity-input').val('').prop('disabled', true);
        firstRow.find('.input-variant-key, .input-size, .input-color').val('');
        updateRowCounter();
        calculateTotalQuantity();
    }

    function updateRowCounter() {
        let count = $('#product_body tr').length;
        $('#rowCounter').text(count + (count === 1 ? ' Item' : ' Items'));
    }

    function calculateTotalQuantity() {
        let total = 0;
        $('.quantity-input').each(function() {
            let val = parseFloat($(this).val()) || 0;
            total += val;
        });
        $('#totalTransferQty').text(total.toLocaleString());
    }

    function openBreakdownModal(productId, warehouseId, productName, warehouseName) {
        currentModalProductId = productId;
        $('#vModalProductName').text(productName);
        $('#vModalWhName').text('Source Warehouse: ' + warehouseName);
        $('#vModalTitle').html(`<i class="fas fa-layer-group me-2"></i> ${productName} - Size Breakdown`);
        $('#vModalSubtitle').text(`Stock in ${warehouseName}`);

        $('#vModalLoader').show();
        $('#vModalContent').hide();
        $('#variantsModal').modal('show');

        $.ajax({
            url: "{{ route('stock_transfers.product-variants') }}",
            data: { warehouse_id: warehouseId, product_id: productId },
            success: function(res) {
                $('#vModalLoader').hide();
                $('#vModalContent').show();

                if (res.image) {
                    $('#vModalImg').attr('src', res.image).show();
                } else {
                    $('#vModalImg').hide();
                }

                $('#vModalTotalPieces').text((res.total_pieces || 0) + ' Pcs');
                currentModalVariants = res.variants || [];

                let tbody = $('#vModalTableBody');
                tbody.empty();

                if (res.variants && res.variants.length > 0) {
                    res.variants.forEach(function(v) {
                        let stockBadge = v.current_stock > 0 
                            ? `<span class="badge badge-success px-3 py-1 fs-6">${v.current_stock} Pcs</span>`
                            : `<span class="badge badge-danger px-3 py-1">0 Pcs (Out of Stock)</span>`;

                        let actionBtn = v.current_stock > 0
                            ? `<button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold btn-select-variant-from-modal" data-variant-key="${v.variant_key}">Select Size</button>`
                            : `<button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" disabled>Out of Stock</button>`;

                        let tr = `
                            <tr>
                                <td><span class="badge badge-dark size-badge">Size ${v.size}</span></td>
                                <td><span class="badge badge-secondary border">${v.color}</span></td>
                                <td class="fw-semibold text-dark">${v.name}</td>
                                <td><code>${v.barcode || '--'}</code></td>
                                <td class="text-center">${stockBadge}</td>
                                <td class="text-end">${actionBtn}</td>
                            </tr>
                        `;
                        tbody.append(tr);
                    });
                } else {
                    tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">No variants found for this product.</td></tr>');
                }
            },
            error: function() {
                $('#vModalLoader').html('<p class="text-danger py-4">Failed to load variant details.</p>');
            }
        });
    }

    // Select Size from Modal
    $(document).on('click', '.btn-select-variant-from-modal', function() {
        let vKey = $(this).data('variant-key');
        if (activeRowForModal && vKey) {
            activeRowForModal.find('.variant-select').val(vKey).trigger('change');
            $('#variantsModal').modal('hide');
        }
    });
</script>
@endpush
