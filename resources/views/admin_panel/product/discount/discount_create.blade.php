@extends('admin_panel.layout.app')

@section('content')
<style>
    .discount-container {
        padding-bottom: 120px; /* Ensures table is never covered by bottom bar */
    }
    .discount-page-hdr {
        background: #fff;
        border-radius: 12px;
        padding: 18px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }
    .article-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        margin-bottom: 26px;
        overflow: hidden;
    }
    .article-card-header {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 22px;
    }
    .article-img-thumb {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
    }
    .quick-batch-bar {
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        border: 1.5px dashed #818cf8;
        border-radius: 12px;
        padding: 14px 20px;
        margin: 16px 20px 0 20px;
    }
    .batch-input-group .input-group-text {
        background: #ffffff;
        font-weight: 700;
        color: #4f46e5;
        border-color: #c7d2fe;
    }
    .batch-input-group .form-control {
        border-color: #c7d2fe;
        font-weight: 700;
    }
    .variant-table {
        margin-bottom: 0;
    }
    .variant-table th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.72rem;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 0.5px;
        border-top: none;
        border-bottom: 2px solid #e2e8f0;
        vertical-align: middle;
        padding: 12px 14px;
        white-space: nowrap;
    }
    .variant-table td {
        vertical-align: middle;
        padding: 10px 14px;
        font-size: 0.86rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .variant-calc-row:hover {
        background-color: #f8fafc !important;
    }
    .badge-size {
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-size: 0.78rem;
    }
    .badge-color {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
        font-size: 0.78rem;
    }
    .barcode-chip {
        font-family: monospace;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #334155;
    }
    .input-addon-symbol {
        font-size: 11px;
        font-weight: 700;
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #64748b;
    }
    .discount-val-inp {
        font-weight: 700;
        border-color: #cbd5e1;
        text-align: right;
    }
    .discount-val-inp:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
    .final-price-box {
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        color: #15803d;
        font-weight: 800;
        font-size: 0.95rem;
        padding: 6px 10px;
        border-radius: 8px;
        text-align: right;
        min-width: 110px;
        display: inline-block;
    }
    .strike-price-box {
        font-size: 10px;
        color: #94a3b8;
        text-decoration: line-through;
        display: block;
        margin-bottom: 2px;
    }
    .save-off-badge {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
        white-space: nowrap;
    }
    .sticky-bottom-bar {
        position: fixed;
        bottom: 16px;
        left: 280px; /* Accounts for sidebar on desktop */
        right: 24px;
        z-index: 1050;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border: 1.5px solid #cbd5e1;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.14);
        padding: 14px 24px;
        transition: all 0.2s ease;
    }
    @media (max-width: 991px) {
        .sticky-bottom-bar {
            left: 16px;
            right: 16px;
            bottom: 12px;
        }
    }
</style>

<div class="container-fluid px-3 px-md-4 py-3 discount-container">

    {{-- ── Top Navigation Bar ── --}}
    <div class="discount-page-hdr d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <span style="color:#4f46e5;"><i class="fas fa-tags"></i></span> 
                Footwear Product &amp; Variant Discount Setup
            </h4>
            <p class="text-muted small mb-0">
                Set discounts across all sizes &amp; colors at once using the <strong>Batch Tool</strong>, or adjust individual variants manually.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('discount.index') }}" class="btn btn-outline-secondary btn-sm fw-bold px-3" style="border-radius:8px;">
                <i class="fas fa-list me-1"></i> Discount Rules List
            </a>
            <a href="{{ route('product') }}" class="btn btn-light border btn-sm fw-bold px-3" style="border-radius:8px;">
                <i class="fas fa-arrow-left me-1"></i> Back to Catalog
            </a>
        </div>
    </div>

    @if (session()->has('warning'))
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-ban me-1"></i> Please check the following errors:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('discount.store') }}" method="POST" id="discountForm">
        @csrf

        @php $rowGlobalIdx = 0; @endphp

        @foreach($products as $pIdx => $product)
            @php
                $variants = $product->parsed_variants;
                $hasVariants = count($variants) > 0;
                $productPrice = (float) $product->price;
                $productDiscounts = $product->discounts ?? collect();
            @endphp

            <div class="article-card" id="article-card-{{ $product->id }}">
                {{-- ── Article Header ── --}}
                <div class="article-card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        @if($product->image)
                            <img src="{{ asset('uploads/products/' . $product->image) }}" class="article-img-thumb shadow-sm" alt="{{ $product->item_name }}">
                        @else
                            <div class="article-img-thumb d-flex align-items-center justify-content-center text-muted">
                                <i class="fas fa-shoe-prints fa-lg"></i>
                            </div>
                        @endif
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h5 class="mb-0 fw-bold text-dark">{{ $product->item_name }}</h5>
                                <span class="badge bg-dark">{{ $product->item_code }}</span>
                                @if(!empty($product->rack_shelf))
                                    <span class="badge bg-light text-success border border-success-subtle">
                                        <i class="fas fa-layer-group me-1"></i>{{ $product->rack_shelf }}
                                    </span>
                                @endif
                            </div>
                            <div class="small text-muted mt-1">
                                <strong>Brand:</strong> {{ $product->brand->name ?? 'Standard' }} &bull; 
                                <strong>Category:</strong> {{ $product->category_relation->name ?? '-' }} &bull; 
                                <strong>Base Price:</strong> <span class="fw-bold text-dark">Rs. {{ number_format($productPrice, 2) }}</span>
                                @if($hasVariants)
                                    &bull; <span class="badge bg-primary text-white">{{ count($variants) }} Variants / Sizes Available</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="badge bg-light text-secondary border px-3 py-2 fw-semibold">
                            <i class="fas fa-cubes me-1"></i> Total Stock: {{ $product->warehouse_stocks_sum_total_pieces ?? 0 }} Pairs
                        </span>
                    </div>
                </div>

                {{-- ── If Variants Exist: Quick Batch Tool ── --}}
                @if($hasVariants)
                <div class="quick-batch-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge text-white fw-bold px-2 py-1" style="background:#4f46e5; font-size:11px;">⚡ 1-CLICK BATCH TOOL</span>
                        <span class="small fw-bold text-dark">Apply to all {{ count($variants) }} variants:</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group input-group-sm batch-input-group" style="width: 145px;">
                            <input type="number" step="0.1" min="0" max="100" class="form-control batch-perc-inp" placeholder="0" title="Discount percentage">
                            <span class="input-group-text">% Off</span>
                        </div>
                        <span class="small text-muted fw-bold">OR</span>
                        <div class="input-group input-group-sm batch-input-group" style="width: 155px;">
                            <span class="input-group-text">PKR</span>
                            <input type="number" step="1" min="0" class="form-control batch-amt-inp" placeholder="0" title="Discount PKR amount">
                        </div>
                        <button type="button" class="btn btn-sm btn-primary fw-bold apply-batch-btn px-3" data-target="#table-variants-{{ $product->id }}" style="background:#4f46e5; border-color:#4f46e5; border-radius:6px;">
                            <i class="fas fa-check-double me-1"></i> Apply to All Variants
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary reset-batch-btn px-3" data-target="#table-variants-{{ $product->id }}" style="border-radius:6px;">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </div>
                @endif

                {{-- ── Variants / Product Rows Table ── --}}
                <div class="table-responsive p-3">
                    <table class="table align-middle variant-table" id="table-variants-{{ $product->id }}">
                        <thead>
                            <tr>
                                <th style="width: 35px;">#</th>
                                <th>Item / Variant</th>
                                <th style="width: 80px;" class="text-center">Size</th>
                                <th style="width: 90px;" class="text-center">Color</th>
                                <th style="width: 100px;">Barcode</th>
                                <th style="width: 110px;" class="text-end">Original Price</th>
                                <th style="width: 120px;" class="text-center">Discount %</th>
                                <th style="width: 130px;" class="text-center">Discount (PKR)</th>
                                <th style="width: 120px;" class="text-center">You Save</th>
                                <th style="width: 130px;" class="text-end">Final Price</th>
                                <th style="width: 130px;">Valid Date</th>
                                <th style="width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($hasVariants)
                                @foreach($variants as $vIdx => $variant)
                                    @php
                                        $vRowId = $variant['row_id'] ?? ('var_' . $vIdx);
                                        $vName = $variant['name'] ?? ($product->item_name . ' (' . ($variant['color'] ?? '') . ')');
                                        $vSize = $variant['size'] ?? '-';
                                        $vColor = $variant['color'] ?? '-';
                                        $vBarcode = $variant['barcode'] ?? $product->item_code;
                                        $vPrice = (float) ($variant['sale_price'] ?? $productPrice);

                                        // Existing discount lookup
                                        $existingDiscount = $productDiscounts->firstWhere('variant_row_id', $vRowId);
                                        if (!$existingDiscount && $vBarcode) {
                                            $existingDiscount = $productDiscounts->firstWhere('variant_barcode', $vBarcode);
                                        }

                                        $initPerc = old('discount_percentage.'.$rowGlobalIdx, $existingDiscount->discount_percentage ?? 0);
                                        $initAmt = old('discount_amount.'.$rowGlobalIdx, $existingDiscount->discount_amount ?? 0);
                                        $initTotal = old('total_discount.'.$rowGlobalIdx, $existingDiscount->total_discount ?? 0);
                                        $initFinal = old('final_price.'.$rowGlobalIdx, $existingDiscount->final_price ?? $vPrice);
                                        $initDate = old('date.'.$rowGlobalIdx, $existingDiscount ? \Carbon\Carbon::parse($existingDiscount->date)->format('Y-m-d') : now()->toDateString());
                                        $initStatus = old('status.'.$rowGlobalIdx, $existingDiscount->status ?? 1);
                                    @endphp
                                    <tr class="variant-calc-row">
                                        <input type="hidden" name="product_id[{{ $rowGlobalIdx }}]" value="{{ $product->id }}">
                                        <input type="hidden" name="variant_row_id[{{ $rowGlobalIdx }}]" value="{{ $vRowId }}">
                                        <input type="hidden" name="variant_name[{{ $rowGlobalIdx }}]" value="{{ $vName }}">
                                        <input type="hidden" name="variant_size[{{ $rowGlobalIdx }}]" value="{{ $vSize }}">
                                        <input type="hidden" name="variant_color[{{ $rowGlobalIdx }}]" value="{{ $vColor }}">
                                        <input type="hidden" name="variant_barcode[{{ $rowGlobalIdx }}]" value="{{ $vBarcode }}">
                                        <input type="hidden" name="actual_price[{{ $rowGlobalIdx }}]" class="actualPriceInp" value="{{ $vPrice }}">
                                        @if($existingDiscount)
                                            <input type="hidden" name="existing_discount_id[{{ $rowGlobalIdx }}]" value="{{ $existingDiscount->id }}">
                                        @endif

                                        <td class="text-muted small fw-bold">{{ $vIdx + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if(!empty($variant['images'][0]))
                                                    <img src="{{ asset('uploads/products/' . $variant['images'][0]) }}" style="width:34px; height:34px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0;">
                                                @endif
                                                <div>
                                                    <span class="fw-bold text-dark">{{ $vName }}</span>
                                                    @if($existingDiscount)
                                                        <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">Saved Rule</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-size">{{ $vSize }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-color">{{ $vColor }}</span>
                                        </td>
                                        <td>
                                            <span class="barcode-chip">{{ $vBarcode }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-dark">Rs. {{ number_format($vPrice, 2) }}</span>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.1" min="0" max="100" 
                                                       name="discount_percentage[{{ $rowGlobalIdx }}]" 
                                                       class="form-control form-control-sm discountPercentage discount-val-inp" 
                                                       value="{{ $initPerc > 0 ? $initPerc : '' }}" placeholder="0">
                                                <span class="input-group-text input-addon-symbol">%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text input-addon-symbol">Rs.</span>
                                                <input type="number" step="1" min="0" 
                                                       name="discount_amount[{{ $rowGlobalIdx }}]" 
                                                       class="form-control form-control-sm discountAmount discount-val-inp" 
                                                       value="{{ $initAmt > 0 ? $initAmt : '' }}" placeholder="0">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="total_discount[{{ $rowGlobalIdx }}]" class="totalDiscountInp" value="{{ $initTotal }}">
                                            <span class="save-off-badge totalDiscountBadge">
                                                Rs. {{ number_format($initTotal, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <input type="hidden" name="final_price[{{ $rowGlobalIdx }}]" class="finalPriceInp" value="{{ $initFinal }}">
                                            <div class="final-price-box">
                                                <span class="strike-price-box strikePriceBadge">Rs. {{ number_format($vPrice, 2) }}</span>
                                                <span class="finalPriceBadge">Rs. {{ number_format($initFinal, 2) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="date" 
                                                   name="date[{{ $rowGlobalIdx }}]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ $initDate }}">
                                        </td>
                                        <td>
                                            <select name="status[{{ $rowGlobalIdx }}]" class="form-select form-select-sm">
                                                <option value="1" {{ $initStatus == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $initStatus == 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @php $rowGlobalIdx++; @endphp
                                @endforeach
                            @else
                                {{-- ── Simple Product Without Variants ── --}}
                                @php
                                    $existingDiscount = $productDiscounts->firstWhere('variant_row_id', null);
                                    $initPerc = old('discount_percentage.'.$rowGlobalIdx, $existingDiscount->discount_percentage ?? 0);
                                    $initAmt = old('discount_amount.'.$rowGlobalIdx, $existingDiscount->discount_amount ?? 0);
                                    $initTotal = old('total_discount.'.$rowGlobalIdx, $existingDiscount->total_discount ?? 0);
                                    $initFinal = old('final_price.'.$rowGlobalIdx, $existingDiscount->final_price ?? $productPrice);
                                    $initDate = old('date.'.$rowGlobalIdx, $existingDiscount ? \Carbon\Carbon::parse($existingDiscount->date)->format('Y-m-d') : now()->toDateString());
                                    $initStatus = old('status.'.$rowGlobalIdx, $existingDiscount->status ?? 1);
                                @endphp
                                <tr class="variant-calc-row">
                                    <input type="hidden" name="product_id[{{ $rowGlobalIdx }}]" value="{{ $product->id }}">
                                    <input type="hidden" name="variant_row_id[{{ $rowGlobalIdx }}]" value="">
                                    <input type="hidden" name="variant_name[{{ $rowGlobalIdx }}]" value="{{ $product->item_name }}">
                                    <input type="hidden" name="variant_size[{{ $rowGlobalIdx }}]" value="-">
                                    <input type="hidden" name="variant_color[{{ $rowGlobalIdx }}]" value="-">
                                    <input type="hidden" name="variant_barcode[{{ $rowGlobalIdx }}]" value="{{ $product->item_code }}">
                                    <input type="hidden" name="actual_price[{{ $rowGlobalIdx }}]" class="actualPriceInp" value="{{ $productPrice }}">
                                    @if($existingDiscount)
                                        <input type="hidden" name="existing_discount_id[{{ $rowGlobalIdx }}]" value="{{ $existingDiscount->id }}">
                                    @endif

                                    <td class="text-muted small fw-bold">1</td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $product->item_name }}</span>
                                        <span class="badge bg-secondary ms-1">Base Article</span>
                                    </td>
                                    <td class="text-center"><span class="text-muted">-</span></td>
                                    <td class="text-center"><span class="text-muted">-</span></td>
                                    <td><span class="barcode-chip">{{ $product->item_code }}</span></td>
                                    <td class="text-end"><span class="fw-bold text-dark">Rs. {{ number_format($productPrice, 2) }}</span></td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.1" min="0" max="100" 
                                                   name="discount_percentage[{{ $rowGlobalIdx }}]" 
                                                   class="form-control form-control-sm discountPercentage discount-val-inp" 
                                                   value="{{ $initPerc > 0 ? $initPerc : '' }}" placeholder="0">
                                            <span class="input-group-text input-addon-symbol">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text input-addon-symbol">Rs.</span>
                                            <input type="number" step="1" min="0" 
                                                   name="discount_amount[{{ $rowGlobalIdx }}]" 
                                                   class="form-control form-control-sm discountAmount discount-val-inp" 
                                                   value="{{ $initAmt > 0 ? $initAmt : '' }}" placeholder="0">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" name="total_discount[{{ $rowGlobalIdx }}]" class="totalDiscountInp" value="{{ $initTotal }}">
                                        <span class="save-off-badge totalDiscountBadge">
                                            Rs. {{ number_format($initTotal, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <input type="hidden" name="final_price[{{ $rowGlobalIdx }}]" class="finalPriceInp" value="{{ $initFinal }}">
                                        <div class="final-price-box">
                                            <span class="strike-price-box strikePriceBadge">Rs. {{ number_format($productPrice, 2) }}</span>
                                            <span class="finalPriceBadge">Rs. {{ number_format($initFinal, 2) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="date" 
                                               name="date[{{ $rowGlobalIdx }}]" 
                                               class="form-control form-control-sm" 
                                               value="{{ $initDate }}">
                                    </td>
                                    <td>
                                        <select name="status[{{ $rowGlobalIdx }}]" class="form-select form-select-sm">
                                            <option value="1" {{ $initStatus == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $initStatus == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </td>
                                </tr>
                                @php $rowGlobalIdx++; @endphp
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        {{-- ── Bottom Action Sticky Bar ── --}}
        <div class="sticky-bottom-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary px-3 py-2 fw-semibold" style="font-size:13px; background:#4f46e5 !important;">
                    <i class="fas fa-boxes me-1"></i> {{ count($products) }} Product Article(s)
                </span>
                <span class="text-muted small fw-medium">
                    Enter either <strong>Discount %</strong> or <strong>PKR</strong> — the system automatically syncs both and calculates final prices.
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('product') }}" class="btn btn-outline-secondary fw-semibold px-4" style="border-radius:8px;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" style="background:#4f46e5; border-color:#4f46e5; border-radius:8px;">
                    <i class="fas fa-save me-1"></i> Save &amp; Apply Discounts
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@section('js')
<script>
$(document).ready(function(){

    function formatNumber(num) {
        return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Function to calculate and update a row's display
    function updateRowDisplay(row, actualPrice, totalDiscount) {
        const cappedDiscount = Math.min(Math.max(0, totalDiscount), actualPrice);
        const finalPrice = Math.max(0, actualPrice - cappedDiscount);

        row.find('.totalDiscountInp').val(cappedDiscount.toFixed(2));
        row.find('.totalDiscountBadge').text('Rs. ' + formatNumber(cappedDiscount));

        row.find('.finalPriceInp').val(finalPrice.toFixed(2));
        row.find('.finalPriceBadge').text('Rs. ' + formatNumber(finalPrice));

        if (cappedDiscount > 0) {
            row.find('.strikePriceBadge').show();
            row.find('.totalDiscountBadge').removeClass('bg-light text-muted').addClass('save-off-badge');
        } else {
            row.find('.strikePriceBadge').hide();
        }
    }

    // 1. User edits DISCOUNT % -> Auto compute PKR amount
    $(document).on('input', '.discountPercentage', function(){
        const row = $(this).closest('tr.variant-calc-row');
        const actualPrice = parseFloat(row.find('.actualPriceInp').val()) || 0;
        let perc = parseFloat($(this).val()) || 0;

        if (perc < 0) { perc = 0; $(this).val(0); }
        if (perc > 100) { perc = 100; $(this).val(100); }

        const calculatedAmt = (actualPrice * perc) / 100;

        // Auto-fill the PKR input so user sees the rupee value!
        if (perc > 0) {
            row.find('.discountAmount').val(calculatedAmt.toFixed(2));
        } else {
            row.find('.discountAmount').val('');
        }

        updateRowDisplay(row, actualPrice, calculatedAmt);
    });

    // 2. User edits DISCOUNT PKR -> Auto compute %
    $(document).on('input', '.discountAmount', function(){
        const row = $(this).closest('tr.variant-calc-row');
        const actualPrice = parseFloat(row.find('.actualPriceInp').val()) || 0;
        let amt = parseFloat($(this).val()) || 0;

        if (amt < 0) { amt = 0; $(this).val(0); }
        if (amt > actualPrice && actualPrice > 0) { 
            amt = actualPrice; 
            $(this).val(actualPrice); 
        }

        const calculatedPerc = actualPrice > 0 ? ((amt / actualPrice) * 100) : 0;

        // Auto-fill % input
        if (amt > 0) {
            row.find('.discountPercentage').val(calculatedPerc.toFixed(1));
        } else {
            row.find('.discountPercentage').val('');
        }

        updateRowDisplay(row, actualPrice, amt);
    });

    // 3. Batch Tool input sync (% to PKR or PKR to %)
    $(document).on('input', '.batch-perc-inp', function(){
        const card = $(this).closest('.article-card');
        const basePrice = parseFloat(card.find('.actualPriceInp').first().val()) || 0;
        let perc = parseFloat($(this).val()) || 0;
        if (perc > 100) { perc = 100; $(this).val(100); }
        if (perc > 0 && basePrice > 0) {
            card.find('.batch-amt-inp').val(((basePrice * perc) / 100).toFixed(0));
        } else {
            card.find('.batch-amt-inp').val('');
        }
    });

    $(document).on('input', '.batch-amt-inp', function(){
        const card = $(this).closest('.article-card');
        const basePrice = parseFloat(card.find('.actualPriceInp').first().val()) || 0;
        let amt = parseFloat($(this).val()) || 0;
        if (amt > 0 && basePrice > 0) {
            card.find('.batch-perc-inp').val(((amt / basePrice) * 100).toFixed(1));
        } else {
            card.find('.batch-perc-inp').val('');
        }
    });

    // 4. Batch Apply Button
    $(document).on('click', '.apply-batch-btn', function(){
        const targetTable = $(this).data('target');
        const card = $(this).closest('.article-card');
        const percVal = card.find('.batch-perc-inp').val();
        const amtVal = card.find('.batch-amt-inp').val();

        if (percVal === '' && amtVal === '') {
            return;
        }

        $(targetTable).find('tbody tr.variant-calc-row').each(function(){
            const row = $(this);
            const actualPrice = parseFloat(row.find('.actualPriceInp').val()) || 0;

            if (percVal !== '') {
                row.find('.discountPercentage').val(percVal);
                const computedAmt = (actualPrice * parseFloat(percVal)) / 100;
                row.find('.discountAmount').val(computedAmt.toFixed(2));
                updateRowDisplay(row, actualPrice, computedAmt);
            } else if (amtVal !== '') {
                row.find('.discountAmount').val(amtVal);
                const computedPerc = actualPrice > 0 ? (parseFloat(amtVal) / actualPrice) * 100 : 0;
                row.find('.discountPercentage').val(computedPerc.toFixed(1));
                updateRowDisplay(row, actualPrice, parseFloat(amtVal));
            }
        });
    });

    // 5. Reset Batch
    $(document).on('click', '.reset-batch-btn', function(){
        const targetTable = $(this).data('target');
        const card = $(this).closest('.article-card');
        card.find('.batch-perc-inp').val('');
        card.find('.batch-amt-inp').val('');

        $(targetTable).find('tbody tr.variant-calc-row').each(function(){
            const row = $(this);
            const actualPrice = parseFloat(row.find('.actualPriceInp').val()) || 0;
            row.find('.discountPercentage').val('');
            row.find('.discountAmount').val('');
            updateRowDisplay(row, actualPrice, 0);
        });
    });

    // Initial pass on load for existing values
    $('tr.variant-calc-row').each(function(){
        const row = $(this);
        const actualPrice = parseFloat(row.find('.actualPriceInp').val()) || 0;
        const total = parseFloat(row.find('.totalDiscountInp').val()) || 0;
        updateRowDisplay(row, actualPrice, total);
    });

});
</script>
@endsection
