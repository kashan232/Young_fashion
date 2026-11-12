@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* Premium Modal & UI Styles */
        .premium-card {
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 18px 25px;
        }

        .modal-header-variants {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white;
            padding: 18px 25px;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.25rem;
        }

        /* Large Modal Styling & Responsive Sizing */
        @media (min-width: 992px) {
            .modal-xl-custom {
                max-width: 1200px !important;
                width: 94% !important;
            }
        }

        .modal {
            overflow-y: auto !important;
        }

        .modal-dialog-scrollable {
            display: flex;
            max-height: calc(100% - 3rem);
            margin: 1.5rem auto;
        }

        .modal-dialog-scrollable .modal-content {
            max-height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .modal-dialog-scrollable .modal-header,
        .modal-dialog-scrollable .modal-footer {
            flex-shrink: 0;
        }

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            max-height: calc(85vh - 140px) !important;
            padding: 24px 28px;
            background: #f8fafc;
        }

        .info-label {
            font-size: 0.78rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 6px;
            display: block;
        }

        .big-input {
            font-size: 1.25rem;
            font-weight: 700;
            border-radius: 10px;
            padding: 10px 14px;
        }

        .details-card {
            background: white;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 15px;
            display: none;
        }

        .calc-box {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
        }

        .calc-number {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0369a1;
        }

        .variant-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .variant-table-container {
            max-height: 380px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            position: relative;
        }

        /* Smooth, visible scrollbars */
        .modal-dialog-scrollable .modal-body::-webkit-scrollbar,
        .variant-table-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .modal-dialog-scrollable .modal-body::-webkit-scrollbar-track,
        .variant-table-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb,
        .variant-table-container::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }

        .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover,
        .variant-table-container::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        .size-badge {
            font-size: 0.82rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            letter-spacing: 0.02em;
        }

        /* Select2 fixes for Modal */
        .select2-container {
            width: 100% !important;
            z-index: 9999;
        }

        .select2-dropdown {
            z-index: 9999;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            display: flex;
            align-items: center;
            border-color: #cbd5e1;
            border-radius: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-2">
        <div class="card premium-card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-warehouse me-2 text-success"></i> Warehouse Stock Inventory</h5>
                    <p class="text-muted mb-0 small">Real-time stock management with variant and size-level tracking</p>
                </div>
                @can('warehouse.stock.create')
                    <button onclick="openAddModal()" class="btn btn-success shadow-sm rounded-pill px-4 fw-bold">
                        <i class="fas fa-plus me-1"></i> Add Stock
                    </button>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="stockTable">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="text-uppercase small fw-bold">#</th>
                                <th class="text-uppercase small fw-bold">Warehouse</th>
                                <th class="text-uppercase small fw-bold">Product & Variants</th>
                                <th class="text-uppercase small fw-bold text-center">Total Box</th>
                                <th class="text-uppercase small fw-bold text-center">Total Pieces</th>
                                <th class="text-uppercase small fw-bold text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stocks as $stock)
                                <tr>
                                    <td>{{ $stocks->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <i class="fas fa-building text-muted me-1 small"></i>
                                            {{ $stock->warehouse->warehouse_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-start">
                                            @if ($stock->product && $stock->product->image)
                                                <img src="{{ asset('uploads/products/' . $stock->product->image) }}"
                                                    class="rounded me-2 shadow-sm"
                                                    style="width: 36px; height: 36px; object-fit: cover;">
                                            @else
                                                <div class="rounded me-2 bg-secondary d-flex align-items-center justify-content-center text-white shadow-sm"
                                                    style="width: 36px; height: 36px; font-size: 11px;">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark">{{ $stock->product->item_name ?? 'N/A' }}</div>
                                                <div class="small text-muted mb-1">Code: <b>{{ $stock->product->item_code ?? '--' }}</b></div>

                                                @if ($stock->has_variants)
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <span class="badge badge-light border text-primary rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                                            <i class="fas fa-tags me-1"></i> {{ $stock->variant_count }} Sizes/Variants
                                                        </span>
                                                        <button type="button" 
                                                            class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0 view-breakdown-btn fw-semibold"
                                                            style="font-size: 0.75rem; line-height: 1.6;"
                                                            data-product-id="{{ $stock->product_id }}"
                                                            data-warehouse-id="{{ $stock->warehouse_id }}"
                                                            data-product-name="{{ $stock->product->item_name }}"
                                                            data-warehouse-name="{{ $stock->warehouse->warehouse_name ?? 'Warehouse' }}">
                                                            <i class="fas fa-layer-group me-1"></i> Sizes Breakdown
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <span class="badge badge-info px-2 py-1">
                                            {{ $stock->quantity }} Boxes
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-primary fs-6">
                                        {{ number_format($stock->total_pieces) }} Pcs
                                    </td>
                                    <td class="text-end text-nowrap">
                                        @if ($stock->has_variants)
                                            <button type="button" 
                                                class="btn btn-sm btn-icon btn-outline-info rounded-circle view-breakdown-btn"
                                                title="View Size Breakdown"
                                                data-product-id="{{ $stock->product_id }}"
                                                data-warehouse-id="{{ $stock->warehouse_id }}"
                                                data-product-name="{{ $stock->product->item_name }}"
                                                data-warehouse-name="{{ $stock->warehouse->warehouse_name ?? 'Warehouse' }}">
                                                <i class="fas fa-list-ul"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('stock_transfers.create', ['warehouse_id' => $stock->warehouse_id, 'product_id' => $stock->product_id]) }}"
                                            class="btn btn-sm btn-icon btn-outline-success rounded-circle ms-1"
                                            title="Transfer Stock to another Warehouse or Shop">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>

                                        @can('warehouse.stock.edit')
                                            <button onclick="editStock({{ $stock->id }})"
                                                class="btn btn-sm btn-icon btn-outline-primary rounded-circle ms-1"
                                                title="Edit Stock Record">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan

                                        <form action="{{ route('warehouse_stocks.destroy', $stock->id) }}" method="POST"
                                            class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this stock record?');">
                                            @csrf @method('DELETE')
                                            @can('warehouse.stock.delete')
                                                <button type="submit"
                                                    class="btn btn-sm btn-icon btn-outline-danger rounded-circle ms-1"
                                                    title="Delete Stock Record">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endcan
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-box-open fa-2x mb-2 d-block text-secondary opacity-50"></i>
                                        No warehouse stock records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $stocks->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Variant Breakdown Modal (Professional View - XL Layout) -->
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
                                        <th class="small fw-bold text-end">Price</th>
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
                    <a id="vModalTransferBtn" href="#" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="fas fa-exchange-alt me-1"></i> Transfer This Product Stock
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Smart Modal (Add & Edit Stock - Extra Large 2-Column Dashboard Layout) -->
    <div class="modal fade" id="stockModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl modal-xl-custom modal-dialog-centered modal-dialog-scrollable" role="document">
            <form id="stockForm" class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                @csrf
                <input type="hidden" id="stock_id" name="stock_id">
                <input type="hidden" id="method_field" name="_method" value="POST">

                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title mb-0" id="modalTitle">Add Stock</h5>
                        <small class="text-white-50">Select warehouse and product to manage stock entries</small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem; background: transparent; border: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Left Column: Warehouse, Product, and Totals (Col 5) -->
                        <div class="col-lg-5 mb-3 mb-lg-0">
                            <!-- Destination Card -->
                            <div class="bg-white p-3 rounded-3 border mb-3">
                                <label class="info-label text-dark mb-2"><i class="fas fa-warehouse text-success me-1"></i> Destination Warehouse</label>
                                <select id="warehouse_id" name="warehouse_id" class="form-control mb-3" required>
                                    <option value="">Select Warehouse</option>
                                </select>

                                <label class="info-label text-dark mb-2"><i class="fas fa-box text-primary me-1"></i> Select Product</label>
                                <select id="product_id" name="product_id" class="form-control">
                                    <option value="">Select Product</option>
                                </select>

                                <!-- Product Details Card -->
                                <div id="product-details" class="details-card mt-3 mb-0">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 mr-3">
                                            <img id="prod-img" src=""
                                                style="width: 52px; height: 52px; border-radius: 8px; object-fit: cover; display: none;">
                                            <div id="prod-icon" class="text-secondary"><i class="fas fa-box fa-2x"></i></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-dark fs-6" id="prod-name">--</div>
                                            <div class="d-flex small text-muted gap-2 mt-1 flex-wrap">
                                                <span class="mr-2">Code: <b id="prod-code">--</b></span>
                                                <span class="mr-2">PPB: <b id="prod-ppb" class="text-primary">--</b></span>
                                                <div>Stock: <b id="prod-current" class="text-success">--</b></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Totals Calculation Card -->
                            <div class="bg-white p-3 rounded-3 border mb-3">
                                <label class="info-label text-dark mb-2"><i class="fas fa-calculator text-primary me-1"></i> Stock Quantity Summary</label>
                                
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <label class="small text-muted fw-semibold" id="qty-label">Total Pieces</label>
                                        <input type="number" name="quantity_input" id="quantity_input"
                                            class="form-control big-input" placeholder="0" min="0" disabled>
                                        <small class="text-muted d-block mt-1" id="qty-help" style="font-size: 0.75rem;">Enter pieces.</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="calc-box">
                                            <div class="text-uppercase small text-muted fw-bold mb-1">Total Boxes</div>
                                            <div class="calc-number" id="calc-result">0</div>
                                            <input type="hidden" name="total_pieces" id="total_pieces" value="0">
                                            <input type="hidden" name="total_box" id="total_box" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="bg-white p-3 rounded-3 border">
                                <label class="info-label text-dark mb-1"><i class="fas fa-sticky-note text-warning me-1"></i> Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>

                        <!-- Right Column: Variant & Size Matrix (Col 7) -->
                        <div class="col-lg-7">
                            <div class="variant-panel">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">
                                            <i class="fas fa-shoe-prints text-primary me-1"></i> Size & Variant Inventory Breakdown
                                        </h6>
                                        <small class="text-muted">Enter quantity to add for each specific size/color</small>
                                    </div>
                                    <span class="badge badge-primary px-2 py-1" id="variant-total-counter">0 Sizes</span>
                                </div>

                                <!-- When No Variants or No Product Selected -->
                                <div id="variant-placeholder" class="text-center py-5 my-auto text-muted">
                                    <i class="fas fa-layer-group fa-3x mb-3 text-secondary opacity-25"></i>
                                    <h6 class="fw-semibold">No Variant Matrix Needed</h6>
                                    <p class="small text-muted mb-0">Select a multi-size product (like shoes or garments) to enter stock per size, or enter direct quantity in the left panel.</p>
                                </div>

                                <!-- Variant Table Section -->
                                <div id="variant-section" style="display: none;" class="flex-grow-1">
                                    <div class="table-responsive variant-table-container">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="small fw-bold">Size</th>
                                                    <th class="small fw-bold">Color</th>
                                                    <th class="small fw-bold text-center">Current Stock</th>
                                                    <th class="small fw-bold text-center" style="width: 160px;">Quantity to Add</th>
                                                </tr>
                                            </thead>
                                            <tbody id="variant-tbody">
                                                <!-- Populated via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm" id="btn-save">
                        <i class="fas fa-check-circle me-1"></i> <span class="btn-text">Save Stock Entry</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <!-- Select2 -->
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>

    <script>
        let piecesPerBox = 0;
        let selectedProduct = null;
        let isEditMode = false;
        let productHasVariants = false;

        $(document).ready(function() {
            initSelect2();

            $('#stockModal').on('hidden.bs.modal', function() {
                resetForm();
            });

            $('#product_id').on('select2:select', function(e) {
                handleProductSelect(e.params.data);
            });

            $('#warehouse_id').on('change', function() {
                if (selectedProduct && $(this).val()) {
                    fetchCurrentStock($(this).val(), selectedProduct.id);
                }
            });

            $('#quantity_input').on('input', function() {
                if (!productHasVariants) {
                    calculateTotal();
                }
            });

            // Listen for variant inputs to auto-calculate total
            $(document).on('input', '.variant-qty-input', function() {
                calculateVariantsTotal();
            });

            // View Breakdown Modal trigger
            $(document).on('click', '.view-breakdown-btn', function() {
                let pId = $(this).data('product-id');
                let whId = $(this).data('warehouse-id');
                let pName = $(this).data('product-name');
                let whName = $(this).data('warehouse-name');

                openVariantsBreakdown(pId, whId, pName, whName);
            });
        });

        function initSelect2() {
            $('#warehouse_id').select2({
                dropdownParent: $('#stockModal'),
                placeholder: "Select Warehouse",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('warehouse_stock.search-warehouses') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data })
                }
            });

            $('#product_id').select2({
                dropdownParent: $('#stockModal'),
                placeholder: "Select Product",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('warehouse_stock.search-products') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data })
                }
            });
        }

        function openAddModal() {
            isEditMode = false;
            $('#modalTitle').text('Add Stock');
            $('#method_field').val('POST');
            $('#btn-save .btn-text').text('Add Stock');

            $('#stockModal').modal('show');
        }

        function editStock(id) {
            isEditMode = true;
            $('#modalTitle').text('Update Stock');
            $('#method_field').val('PUT');
            $('#btn-save .btn-text').text('Update Stock');
            $('#stock_id').val(id);

            let url = "{{ route('warehouse_stock.edit-data', ':id') }}".replace(':id', id);

            $.get(url, function(data) {
                let whOption = new Option(data.warehouse_name, data.warehouse_id, true, true);
                $('#warehouse_id').append(whOption).trigger('change');

                let option = new Option(data.product_name, data.product_id, true, true);
                $('#product_id').append(option).trigger('change');

                let mockData = {
                    id: data.product_id,
                    item_name: data.product_name,
                    item_code: data.product_code,
                    pieces_per_box: data.pieces_per_box,
                    has_variants: data.has_variants,
                    image: data.image
                };
                handleProductSelect(mockData);

                $('#quantity_input').val(data.total_pieces);
                $('#total_pieces').val(data.total_pieces);

                if (data.pieces_per_box > 0 && data.total_pieces > 0) {
                    let boxes = Math.floor(data.total_pieces / data.pieces_per_box);
                    let loose = data.total_pieces % data.pieces_per_box;
                    $('#calc-result').html(`<span class="text-primary">${boxes} Boxes</span>` + (loose > 0 ? ` <span class="text-muted small">+ ${loose} Loose</span>` : ''));
                    $('#total_box').val(boxes);
                } else {
                    $('#calc-result').text(data.total_pieces + ' Pcs');
                    $('#total_box').val(0);
                }

                $('#remarks').val(data.remarks);

                $('#stockModal').modal('show');
            }).fail(function() {
                Swal.fire('Error', 'Could not fetch stock data.', 'error');
            });
        }

        function handleProductSelect(data) {
            selectedProduct = data;
            piecesPerBox = parseInt(data.pieces_per_box) || 0;
            productHasVariants = data.has_variants || false;

            $('#product-details').fadeIn();
            $('#prod-name').text(data.item_name);
            $('#prod-code').text(data.item_code || '--');
            $('#prod-ppb').text(piecesPerBox > 0 ? piecesPerBox : 'N/A (Loose)');

            if (data.image) {
                $('#prod-img').attr('src', data.image).show();
                $('#prod-icon').hide();
            } else {
                $('#prod-img').hide();
                $('#prod-icon').show();
            }

            let whId = $('#warehouse_id').val();
            if (whId) {
                fetchCurrentStock(whId, data.id);
            } else {
                $('#prod-current').text('Select Warehouse First');
            }

            if (productHasVariants && whId) {
                loadVariantInputs(data.id, whId);
            } else {
                $('#variant-section').hide();
                $('#variant-placeholder').show();
                $('#quantity_input').prop('disabled', false).prop('readonly', false);
            }

            calculateTotal();
        }

        function loadVariantInputs(productId, warehouseId) {
            let url = "{{ route('warehouse_stock.product-variants', ':id') }}".replace(':id', productId);
            $.get(url, { warehouse_id: warehouseId }, function(res) {
                if (res.has_variants && res.variants.length > 0) {
                    productHasVariants = true;
                    $('#variant-total-counter').text(res.variants.length + ' Sizes');
                    let tbody = $('#variant-tbody');
                    tbody.empty();

                    res.variants.forEach(function(v) {
                        let stockBadge = v.current_stock > 0 
                            ? `<span class="badge badge-success px-2 py-1">${v.current_stock} Pcs</span>`
                            : `<span class="badge badge-danger px-2 py-1">0 Pcs</span>`;

                        let row = `
                            <tr>
                                <td><span class="badge badge-dark size-badge">Size ${v.size}</span></td>
                                <td><span class="badge badge-secondary border">${v.color}</span></td>
                                <td class="text-center">${stockBadge}</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" 
                                               class="form-control form-control-sm variant-qty-input fw-bold text-center" 
                                               name="variant_quantities[${v.variant_key}]" 
                                               min="0" 
                                               placeholder="0"
                                               data-stock="${v.current_stock}">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-light small">Pcs</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

                    $('#variant-placeholder').hide();
                    $('#variant-section').fadeIn();
                    $('#quantity_input').prop('disabled', false).prop('readonly', true);
                    $('#qty-help').text('Auto-calculated from sizes matrix on right.');
                } else {
                    $('#variant-section').hide();
                    $('#variant-placeholder').show();
                    $('#quantity_input').prop('disabled', false).prop('readonly', false);
                    $('#qty-help').text('Enter total quantity in pieces.');
                }
            });
        }

        function calculateVariantsTotal() {
            let total = 0;
            $('.variant-qty-input').each(function() {
                let val = parseFloat($(this).val()) || 0;
                total += val;
            });

            $('#quantity_input').val(total);
            $('#total_pieces').val(total);

            if (piecesPerBox > 0 && total > 0) {
                let boxes = Math.floor(total / piecesPerBox);
                let loose = total % piecesPerBox;
                $('#calc-result').html(`<span class="text-primary">${boxes} Boxes</span>` + (loose > 0 ? ` <span class="text-muted small">+ ${loose} Loose</span>` : ''));
                $('#total_box').val(boxes);
            } else {
                $('#calc-result').text(total + ' Pcs');
                $('#total_box').val(0);
            }
        }

        function fetchCurrentStock(warehouseId, productId) {
            $.get("{{ route('warehouse_stock.get-stock') }}", {
                warehouse_id: warehouseId,
                product_id: productId
            }, function(res) {
                let currentTotal = parseFloat(res.total_pieces) || 0;
                let displayStock = currentTotal + " Pcs";

                if (piecesPerBox > 0) {
                    let b = Math.floor(currentTotal / piecesPerBox);
                    let l = currentTotal % piecesPerBox;
                    displayStock = `${b} Boxes` + (l > 0 ? ` + ${l} Loose` : '');
                }
                $('#prod-current').text(displayStock);

                if (res.has_variants && res.variants.length > 0) {
                    productHasVariants = true;
                    loadVariantInputs(productId, warehouseId);
                } else {
                    productHasVariants = false;
                    $('#variant-section').hide();
                    $('#variant-placeholder').show();
                    $('#quantity_input').prop('disabled', false).prop('readonly', false);
                }
            });
        }

        function calculateTotal() {
            let inputQty = parseFloat($('#quantity_input').val()) || 0;
            $('#total_pieces').val(inputQty);

            if (piecesPerBox > 0 && inputQty > 0) {
                let boxes = Math.floor(inputQty / piecesPerBox);
                let loose = inputQty % piecesPerBox;
                $('#calc-result').html(`<span class="text-primary">${boxes} Boxes</span>` + (loose > 0 ? ` <span class="text-muted small">+ ${loose} Loose</span>` : ''));
                $('#total_box').val(boxes);
            } else {
                $('#calc-result').text(inputQty + ' Pcs');
                $('#total_box').val(0);
            }
        }

        function resetForm() {
            selectedProduct = null;
            piecesPerBox = 0;
            productHasVariants = false;

            $('#stockForm')[0].reset();
            $('#warehouse_id').val(null).trigger('change');
            $('#product_id').val(null).trigger('change');
            $('#product-details').hide();
            $('#variant-section').hide();
            $('#variant-placeholder').show();
            $('#variant-tbody').empty();
            $('#quantity_input').prop('disabled', true).prop('readonly', false);
            $('#calc-result').text('0');
            $('#stock_id').val('');
        }

        // View Variants Breakdown Modal
        function openVariantsBreakdown(productId, warehouseId, productName, warehouseName) {
            $('#vModalProductName').text(productName);
            $('#vModalWhName').text('Warehouse: ' + warehouseName);
            $('#vModalTitle').html(`<i class="fas fa-layer-group me-2"></i> ${productName} - Size Breakdown`);
            $('#vModalSubtitle').text(`Stock in ${warehouseName}`);

            $('#vModalLoader').show();
            $('#vModalContent').hide();

            $('#variantsModal').modal('show');

            let transferUrl = "{{ route('stock_transfers.create') }}?warehouse_id=" + warehouseId + "&product_id=" + productId;
            $('#vModalTransferBtn').attr('href', transferUrl);

            let url = "{{ route('warehouse_stock.product-variants', ':id') }}".replace(':id', productId);
            $.get(url, { warehouse_id: warehouseId }, function(res) {
                $('#vModalLoader').hide();
                $('#vModalContent').show();

                if (res.image) {
                    $('#vModalImg').attr('src', res.image).show();
                } else {
                    $('#vModalImg').hide();
                }

                $('#vModalTotalPieces').text((res.total_pieces || 0) + ' Pcs');

                let tbody = $('#vModalTableBody');
                tbody.empty();

                if (res.variants && res.variants.length > 0) {
                    res.variants.forEach(function(v) {
                        let stockBadge = v.current_stock > 0 
                            ? `<span class="badge badge-success px-3 py-1 fs-6">${v.current_stock} Pcs</span>`
                            : `<span class="badge badge-danger px-3 py-1">0 Pcs (Out of Stock)</span>`;

                        let row = `
                            <tr>
                                <td><span class="badge badge-dark size-badge">Size ${v.size}</span></td>
                                <td><span class="badge badge-secondary border">${v.color}</span></td>
                                <td class="fw-semibold text-dark">${v.name}</td>
                                <td><code>${v.barcode || '--'}</code></td>
                                <td class="text-center">${stockBadge}</td>
                                <td class="text-end fw-bold text-dark">Rs. ${parseFloat(v.price).toLocaleString()}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                } else {
                    tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">No variants configured for this product.</td></tr>');
                }
            }).fail(function() {
                $('#vModalLoader').html('<p class="text-danger py-4">Failed to load variant details.</p>');
            });
        }

        // Submit Logic
        $('#stockForm').on('submit', function(e) {
            e.preventDefault();

            let url = "{{ route('warehouse_stocks.store') }}";
            if (isEditMode) {
                let id = $('#stock_id').val();
                url = "{{ route('warehouse_stocks.update', ':id') }}".replace(':id', id);
            }

            let formData = $(this).serialize();
            let btn = $('#btn-save');
            btn.prop('disabled', true);

            $.ajax({
                url: url,
                method: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let msg = 'Error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire('Error', msg, 'error');
                    btn.prop('disabled', false);
                }
            });
        });
    </script>
@endsection
