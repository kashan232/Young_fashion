@extends('admin_panel.layout.app')

@section('content')
<style>
    /* ── Main Container ── */
    .discount-wrapper {
        padding: 16px 20px 40px 20px;
        background: #f8fafc;
        min-height: calc(100vh - 120px);
    }

    /* ── Top Header ── */
    .discount-page-hdr {
        background: #ffffff;
        border-radius: 12px;
        padding: 18px 24px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 22px;
    }

    /* ── Article Card ── */
    .article-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        margin-bottom: 28px;
        overflow: hidden;
    }

    .article-card-header {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 20px 24px;
    }

    .article-main-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1;
        background: #f1f5f9;
        flex-shrink: 0;
    }

    .article-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .meta-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }

    /* ── Batch Tool ── */
    .quick-batch-box {
        background: #f8fafc;
        border-left: 4px solid #4f46e5;
        border-top: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 20px;
        margin: 16px 24px 0 24px;
    }

    .batch-input-grp {
        width: 140px;
    }
    .batch-input-grp .input-group-text {
        background: #ffffff;
        font-weight: 700;
        color: #4f46e5;
        border-color: #cbd5e1;
        font-size: 12px;
    }
    .batch-input-grp .form-control {
        border-color: #cbd5e1;
        font-weight: 700;
        font-size: 13px;
    }

    /* ── Variants Table ── */
    .table-container-card {
        padding: 16px 24px 24px 24px;
    }

    .variant-table {
        min-width: 1260px;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .variant-table thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 0.6px;
        border-top: 1px solid #e2e8f0;
        border-bottom: 2px solid #cbd5e1;
        padding: 12px 14px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .variant-table tbody td {
        vertical-align: middle;
        padding: 12px 14px;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }

    .variant-calc-row:hover tbody td,
    .variant-calc-row:hover td {
        background: #f8fafc !important;
    }

    .v-thumb-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        flex-shrink: 0;
        background: #f8fafc;
    }

    .pill-size {
        display: inline-block;
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-size: 12px;
    }

    .pill-color {
        display: inline-block;
        background: #eff6ff;
        color: #1e40af;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
        font-size: 12px;
    }

    .pill-barcode {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
    }

    .input-grp-cell {
        width: 115px;
    }
    .input-grp-cell .input-group-text {
        background: #f8fafc;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        border-color: #cbd5e1;
        padding: 4px 8px;
    }
    .input-grp-cell .form-control {
        font-size: 13px;
        font-weight: 700;
        border-color: #cbd5e1;
        text-align: right;
        padding: 6px 8px;
    }
    .input-grp-cell .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .badge-you-save {
        display: inline-block;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        font-weight: 700;
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .box-final-price {
        display: inline-block;
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        color: #15803d;
        font-weight: 800;
        font-size: 13px;
        padding: 6px 12px;
        border-radius: 8px;
        text-align: right;
        min-width: 115px;
        white-space: nowrap;
    }
    .strike-orig-text {
        font-size: 10px;
        color: #94a3b8;
        text-decoration: line-through;
        display: block;
        line-height: 1.1;
        margin-bottom: 2px;
    }

    /* ── Bottom Save Card (Normal Flow, No Overlay!) ── */
    .bottom-save-card {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 18px 24px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        margin-top: 10px;
    }
</style>

<div class="discount-wrapper">

    {{-- ── Form Header ── --}}
    <form action="{{ route('discount.store') }}" method="POST" id="discountForm">
        @csrf

        <div class="discount-page-hdr d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                    <span style="color:#4f46e5;"><i class="fas fa-tags"></i></span> 
                    Footwear Product &amp; Variant Discount Setup
                </h4>
                <p class="text-muted small mb-0">
                    Set discounts for individual shoe sizes and colors, or apply a batch discount to all variants at once.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('product') }}" class="btn btn-light border btn-sm fw-bold px-3 py-2" style="border-radius:8px;">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <a href="{{ route('discount.index') }}" class="btn btn-outline-secondary btn-sm fw-bold px-3 py-2" style="border-radius:8px;">
                    <i class="fas fa-list me-1"></i> Discount Rules List
                </a>
                <button type="submit" class="btn btn-primary btn-sm fw-bold px-4 py-2 shadow-sm" style="background:#4f46e5; border-color:#4f46e5; border-radius:8px; font-size:13px;">
                    <i class="fas fa-save me-1"></i> Save &amp; Apply Discounts
                </button>
            </div>
        </div>

        @if (session()->has('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="fas fa-exclamation-triangle fs-5"></i>
                <div>{{ session('warning') }}</div>
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
                            <img src="{{ asset('uploads/products/' . $product->image) }}" class="article-main-thumb shadow-sm" alt="{{ $product->item_name }}">
                        @else
                            <div class="article-main-thumb d-flex align-items-center justify-content-center text-muted">
                                <i class="fas fa-shoe-prints fa-lg"></i>
                            </div>
                        @endif
                        <div>
                            <div class="article-title">{{ $product->item_name }}</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-dark px-2 py-1" style="font-size:11px;">Code: {{ $product->item_code }}</span>
                                <span class="meta-chip"><i class="fas fa-trademark me-1 text-primary"></i>{{ $product->brand->name ?? 'Standard' }}</span>
                                <span class="meta-chip"><i class="fas fa-list me-1 text-secondary"></i>{{ $product->category_relation->name ?? 'Casual Shoes' }}</span>
                                @if(!empty($product->rack_shelf))
                                    <span class="meta-chip" style="background:#f0fdf4; color:#15803d; border-color:#bbf7d0;">
                                        <i class="fas fa-layer-group me-1"></i>{{ $product->rack_shelf }}
                                    </span>
                                @endif
                                @if($hasVariants)
                                    <span class="badge px-2 py-1 text-white" style="background:#4f46e5; font-size:11px;">
                                        {{ count($variants) }} Shoe Variants
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="small text-muted fw-bold text-uppercase" style="letter-spacing:0.5px;">Original Base Price</div>
                        <div class="fw-bold fs-5 text-dark">Rs. {{ number_format($productPrice, 2) }}</div>
                        <div class="small text-muted">
                            <i class="fas fa-cubes me-1"></i> Stock: <strong>{{ $product->warehouse_stocks_sum_total_pieces ?? 0 }} Pairs</strong>
                        </div>
                    </div>
                </div>

                {{-- ── Batch Tool: 1-Click All Variants ── --}}
                @if($hasVariants)
                <div class="quick-batch-box d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2 py-1 text-white fw-bold" style="background:#4f46e5 !important; font-size:11px;">
                            ⚡ BATCH TOOL
                        </span>
                        <span class="fw-bold text-dark" style="font-size:13px;">
                            Set discount for all {{ count($variants) }} variants of this shoe at once:
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group input-group-sm batch-input-grp">
                            <input type="number" step="0.1" min="0" max="100" class="form-control batch-perc-inp" placeholder="0">
                            <span class="input-group-text">% Off</span>
                        </div>
                        <span class="small fw-bold text-muted">OR</span>
                        <div class="input-group input-group-sm batch-input-grp">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" step="1" min="0" class="form-control batch-amt-inp" placeholder="0">
                        </div>
                        <button type="button" class="btn btn-sm btn-primary fw-bold apply-batch-btn px-3" data-target="#table-variants-{{ $product->id }}" style="background:#4f46e5; border-color:#4f46e5; border-radius:6px;">
                            <i class="fas fa-check-double me-1"></i> Apply to All Variants
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary reset-batch-btn px-2" data-target="#table-variants-{{ $product->id }}" title="Reset All Variants" style="border-radius:6px;">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
                @endif

                {{-- ── Table Container ── --}}
                <div class="table-container-card">
                    <div class="table-responsive">
                        <table class="table align-middle variant-table" id="table-variants-{{ $product->id }}">
                            <thead>
                                <tr>
                                    <th style="width: 35px;" class="text-center">#</th>
                                    <th style="width: 50px;">Photo</th>
                                    <th>Shoe Variant Name</th>
                                    <th style="width: 80px;" class="text-center">Size</th>
                                    <th style="width: 90px;" class="text-center">Color</th>
                                    <th style="width: 110px;">Barcode</th>
                                    <th style="width: 120px;" class="text-end">Original Price</th>
                                    <th style="width: 125px;" class="text-center">Discount %</th>
                                    <th style="width: 135px;" class="text-center">Discount (PKR)</th>
                                    <th style="width: 125px;" class="text-center">You Save</th>
                                    <th style="width: 135px;" class="text-end">Final Offer Price</th>
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
                                            $vImage = !empty($variant['images'][0]) ? $variant['images'][0] : $product->image;

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

                                            <td class="text-center text-muted fw-bold">{{ $vIdx + 1 }}</td>
                                            
                                            {{-- Variant Photo --}}
                                            <td>
                                                @if($vImage)
                                                    <img src="{{ asset('uploads/products/' . $vImage) }}" class="v-thumb-img" alt="{{ $vName }}">
                                                @else
                                                    <div class="v-thumb-img d-flex align-items-center justify-content-center text-muted">
                                                        <i class="fas fa-shoe-prints"></i>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Variant Name --}}
                                            <td>
                                                <div class="fw-bold text-dark" style="font-size:13px; white-space:nowrap;">{{ $vName }}</div>
                                                @if($existingDiscount)
                                                    <span class="badge bg-warning text-dark mt-1" style="font-size:10px;">Saved Promotion</span>
                                                @endif
                                            </td>

                                            {{-- Size --}}
                                            <td class="text-center">
                                                <span class="pill-size">{{ $vSize }}</span>
                                            </td>

                                            {{-- Color --}}
                                            <td class="text-center">
                                                <span class="pill-color">{{ $vColor }}</span>
                                            </td>

                                            {{-- Barcode --}}
                                            <td>
                                                <span class="pill-barcode">
                                                    <i class="fas fa-barcode me-1 text-muted"></i>{{ $vBarcode }}
                                                </span>
                                            </td>

                                            {{-- Original Price --}}
                                            <td class="text-end">
                                                <span class="fw-bold text-dark">Rs. {{ number_format($vPrice, 2) }}</span>
                                            </td>

                                            {{-- Discount % Input --}}
                                            <td>
                                                <div class="input-group input-group-sm input-grp-cell mx-auto">
                                                    <input type="number" step="0.1" min="0" max="100" 
                                                           name="discount_percentage[{{ $rowGlobalIdx }}]" 
                                                           class="form-control discountPercentage" 
                                                           value="{{ $initPerc > 0 ? $initPerc : '' }}" placeholder="0">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>

                                            {{-- Discount (PKR) Input --}}
                                            <td>
                                                <div class="input-group input-group-sm input-grp-cell mx-auto">
                                                    <span class="input-group-text">Rs.</span>
                                                    <input type="number" step="1" min="0" 
                                                           name="discount_amount[{{ $rowGlobalIdx }}]" 
                                                           class="form-control discountAmount" 
                                                           value="{{ $initAmt > 0 ? $initAmt : '' }}" placeholder="0">
                                                </div>
                                            </td>

                                            {{-- You Save (Total Discount Off) --}}
                                            <td class="text-center">
                                                <input type="hidden" name="total_discount[{{ $rowGlobalIdx }}]" class="totalDiscountInp" value="{{ $initTotal }}">
                                                <span class="badge-you-save totalDiscountBadge">
                                                    Rs. {{ number_format($initTotal, 2) }}
                                                </span>
                                            </td>

                                            {{-- Final Offer Price --}}
                                            <td class="text-end">
                                                <input type="hidden" name="final_price[{{ $rowGlobalIdx }}]" class="finalPriceInp" value="{{ $initFinal }}">
                                                <div class="box-final-price">
                                                    <span class="strike-orig-text strikePriceBadge">Rs. {{ number_format($vPrice, 2) }}</span>
                                                    <span class="finalPriceBadge">Rs. {{ number_format($initFinal, 2) }}</span>
                                                </div>
                                            </td>

                                            {{-- Valid Date --}}
                                            <td>
                                                <input type="date" 
                                                       name="date[{{ $rowGlobalIdx }}]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $initDate }}" style="font-size:12px;">
                                            </td>

                                            {{-- Status --}}
                                            <td>
                                                <select name="status[{{ $rowGlobalIdx }}]" class="form-select form-select-sm" style="font-size:12px;">
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

                                        <td class="text-center text-muted fw-bold">1</td>
                                        <td>
                                            @if($product->image)
                                                <img src="{{ asset('uploads/products/' . $product->image) }}" class="v-thumb-img" alt="{{ $product->item_name }}">
                                            @else
                                                <div class="v-thumb-img d-flex align-items-center justify-content-center text-muted">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $product->item_name }}</span>
                                            <span class="badge bg-secondary ms-1">Base Article</span>
                                        </td>
                                        <td class="text-center"><span class="text-muted">-</span></td>
                                        <td class="text-center"><span class="text-muted">-</span></td>
                                        <td><span class="pill-barcode">{{ $product->item_code }}</span></td>
                                        <td class="text-end"><span class="fw-bold text-dark">Rs. {{ number_format($productPrice, 2) }}</span></td>
                                        <td>
                                            <div class="input-group input-group-sm input-grp-cell mx-auto">
                                                <input type="number" step="0.1" min="0" max="100" 
                                                       name="discount_percentage[{{ $rowGlobalIdx }}]" 
                                                       class="form-control discountPercentage" 
                                                       value="{{ $initPerc > 0 ? $initPerc : '' }}" placeholder="0">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm input-grp-cell mx-auto">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="number" step="1" min="0" 
                                                       name="discount_amount[{{ $rowGlobalIdx }}]" 
                                                       class="form-control discountAmount" 
                                                       value="{{ $initAmt > 0 ? $initAmt : '' }}" placeholder="0">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="total_discount[{{ $rowGlobalIdx }}]" class="totalDiscountInp" value="{{ $initTotal }}">
                                            <span class="badge-you-save totalDiscountBadge">
                                                Rs. {{ number_format($initTotal, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <input type="hidden" name="final_price[{{ $rowGlobalIdx }}]" class="finalPriceInp" value="{{ $initFinal }}">
                                            <div class="box-final-price">
                                                <span class="strike-orig-text strikePriceBadge">Rs. {{ number_format($productPrice, 2) }}</span>
                                                <span class="finalPriceBadge">Rs. {{ number_format($initFinal, 2) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="date" 
                                                   name="date[{{ $rowGlobalIdx }}]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ $initDate }}" style="font-size:12px;">
                                        </td>
                                        <td>
                                            <select name="status[{{ $rowGlobalIdx }}]" class="form-select form-select-sm" style="font-size:12px;">
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
            </div>
        @endforeach

        {{-- ── Bottom Save Action Card (Normal Flow, No Overlay!) ── --}}
        <div class="bottom-save-card d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge px-3 py-2 text-white fw-bold" style="background:#4f46e5; font-size:13px;">
                    <i class="fas fa-check-circle me-1"></i> Ready to Apply
                </span>
                <span class="text-muted small fw-semibold">
                    Enter either <strong>Discount %</strong> or <strong>PKR</strong> — system calculates total savings and final offer price automatically.
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('product') }}" class="btn btn-outline-secondary fw-semibold px-4 py-2" style="border-radius:8px;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" style="background:#4f46e5; border-color:#4f46e5; border-radius:8px;">
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
            row.find('.totalDiscountBadge').show();
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
