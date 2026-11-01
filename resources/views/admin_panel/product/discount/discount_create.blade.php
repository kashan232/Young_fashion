@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --border-color: #e2e8f0;
        --bg-body: #f8fafc;
        --text-main: #0f172a;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-body);
        color: var(--text-main);
    }

    .view-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .view-card-header {
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .field-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        height: 100%;
    }

    .field-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
        letter-spacing: 0.4px;
    }

    .field-value {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .btn-action-hdr {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.15s ease;
        text-decoration: none !important;
    }

    /* Batch Tool Bar */
    .batch-tool-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 18px;
        margin: 16px 20px 0 20px;
    }

    /* Table styling aligned with show.blade.php */
    .discount-table {
        min-width: 1240px;
        margin-bottom: 0;
    }

    .discount-table thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 11.5px;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 0.5px;
        border-top: 1px solid #e2e8f0;
        border-bottom: 2px solid #cbd5e1;
        padding: 12px 14px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .discount-table tbody td {
        vertical-align: middle;
        padding: 12px 14px;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }

    .discount-table tbody tr:hover td {
        background: #f8fafc !important;
    }

    /* Input Cells */
    .cell-input-grp {
        width: 120px;
    }
    .cell-input-grp .input-group-text {
        background: #f8fafc;
        border-color: #cbd5e1;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        padding: 4px 8px;
    }
    .cell-input-grp .form-control {
        font-size: 13px;
        font-weight: 700;
        border-color: #cbd5e1;
        text-align: right;
        padding: 6px 8px;
    }
    .cell-input-grp .form-control:focus {
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

    .badge-final-price {
        display: inline-block;
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        color: #15803d;
        font-weight: 800;
        font-size: 13.5px;
        padding: 6px 12px;
        border-radius: 8px;
        text-align: right;
        min-width: 115px;
        white-space: nowrap;
    }
    .strike-price {
        font-size: 10px;
        color: #94a3b8;
        text-decoration: line-through;
        display: block;
        line-height: 1.1;
        margin-bottom: 2px;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid px-4 py-4">

            {{-- ── 1. Top Navigation & Action Header ── --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('product') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; color: #475569; background: #ffffff;" title="Back to Products">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Discount &amp; Promotion Setup</h3>
                            <span class="badge bg-primary text-white px-2 py-1 shadow-sm" style="font-size: 12px; font-weight: 600;">
                                <i class="fas fa-percentage me-1"></i> Special Offers
                            </span>
                        </div>
                        <div class="text-muted small mt-1">
                            Set discounts for individual shoe sizes &amp; colors, or apply a batch discount to all variants at once.
                        </div>
                    </div>
                </div>

                {{-- Header Actions --}}
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('discount.index') }}" class="btn btn-outline-secondary btn-action-hdr">
                        <i class="fas fa-list"></i> Discount Rules List
                    </a>
                    <a href="{{ route('product') }}" class="btn btn-light border btn-action-hdr">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" form="discountForm" class="btn btn-primary btn-action-hdr shadow-sm" style="background: var(--primary); border-color: var(--primary);">
                        <i class="fas fa-save"></i> Save &amp; Apply Discounts
                    </button>
                </div>
            </div>

            @if (session()->has('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle fs-5"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
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

                    {{-- ── 2. Shoe Article Information Card (Exact show.blade.php layout) ── --}}
                    <div class="view-card mb-4">
                        <div class="view-card-header">
                            <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="fas fa-shoe-prints text-primary me-2"></i>Shoe Article Information
                            </span>
                            <span class="badge bg-light text-secondary border px-3 py-1">
                                Article Code: <strong>{{ $product->item_code ?? '-' }}</strong>
                            </span>
                        </div>
                        <div class="p-4 bg-white">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-auto text-center">
                                    @if($product->image)
                                        <img src="{{ asset('uploads/products/' . $product->image) }}" style="width: 76px; height: 76px; object-fit: cover; border-radius: 12px; border: 1.5px solid #cbd5e1;" alt="{{ $product->item_name }}">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light border rounded-3 text-muted" style="width: 76px; height: 76px;">
                                            <i class="fas fa-shoe-prints fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col">
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="field-box">
                                                <div class="field-label">Article / Product Name</div>
                                                <div class="field-value text-truncate" title="{{ $product->item_name }}">{{ $product->item_name }}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="field-box">
                                                <div class="field-label">Brand &amp; Category</div>
                                                <div class="field-value">{{ $product->brand->name ?? 'Standard' }} &bull; {{ $product->category_relation->name ?? 'Casual Shoes' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="field-box">
                                                <div class="field-label">Rack / Shelf Location</div>
                                                <div class="field-value text-success">
                                                    <i class="fas fa-layer-group me-1"></i>{{ $product->rack_shelf ?? 'Not Assigned' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="field-box" style="background: #f0fdf4; border-color: #bbf7d0;">
                                                <div class="field-label" style="color: #166534;">Original Price &amp; Stock</div>
                                                <div class="field-value fw-bold" style="color: #15803d; font-size: 15px;">
                                                    Rs. {{ number_format($productPrice, 2) }}
                                                    <span class="text-muted small fw-normal">({{ $product->warehouse_stocks_sum_total_pieces ?? 0 }} Pairs)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── 3. Batch Tool Panel ── --}}
                        @if($hasVariants)
                        <div class="batch-tool-panel d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge text-white fw-bold px-2 py-2" style="background: #4f46e5; font-size: 11.5px;">
                                    <i class="fas fa-bolt me-1"></i> 1-CLICK BATCH TOOL
                                </span>
                                <span class="fw-bold text-dark small">
                                    Set discount for all {{ count($variants) }} variants at once:
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="input-group input-group-sm" style="width: 140px;">
                                    <input type="number" step="0.1" min="0" max="100" class="form-control batch-perc-inp" placeholder="0">
                                    <span class="input-group-text fw-bold">% Off</span>
                                </div>
                                <span class="small fw-bold text-muted">OR</span>
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <span class="input-group-text fw-bold">Rs.</span>
                                    <input type="number" step="1" min="0" class="form-control batch-amt-inp" placeholder="0">
                                </div>
                                <button type="button" class="btn btn-sm btn-primary fw-bold apply-batch-btn px-3" data-target="#table-variants-{{ $product->id }}" style="background: #4f46e5; border-color: #4f46e5; border-radius: 6px;">
                                    <i class="fas fa-check-double me-1"></i> Apply to All Variants
                                </button>
                                <button type="button" class="btn btn-sm btn-light border reset-batch-btn px-2" data-target="#table-variants-{{ $product->id }}" style="border-radius: 6px;">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                        </div>
                        @endif

                        {{-- ── 4. Variants Table (Spacious & Clean) ── --}}
                        <div class="p-3">
                            <div class="table-responsive">
                                <table class="table align-middle discount-table" id="table-variants-{{ $product->id }}">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">#</th>
                                            <th>Shoe Variant Name</th>
                                            <th style="width: 80px;" class="text-center">Size</th>
                                            <th style="width: 90px;" class="text-center">Color</th>
                                            <th style="width: 110px;" class="text-center">Barcode</th>
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
                                                    $vColor = (!empty($variant['color']) && $variant['color'] !== '-') ? trim($variant['color']) : 'General';
                                                    $vBarcode = $variant['barcode'] ?? $product->item_code;
                                                    $vPrice = (float) ($variant['sale_price'] ?? $productPrice);
                                                    $vPhotos = $variant['resolved_photos'] ?? (!empty($variant['images']) ? $variant['images'] : []);
                                                    $vImg = !empty($vPhotos[0]) ? $vPhotos[0] : $product->image;

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
                                                    
                                                    {{-- Variant Name with thumbnail --}}
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if($vImg)
                                                                <img src="{{ asset('uploads/products/' . $vImg) }}" style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; flex-shrink: 0;" alt="{{ $vName }}">
                                                            @else
                                                                <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="width: 36px; height: 36px; flex-shrink: 0;">
                                                                    <i class="fas fa-shoe-prints"></i>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div class="fw-bold text-dark" style="font-size: 13px; white-space: nowrap;">{{ $vName }}</div>
                                                                @if($existingDiscount)
                                                                    <span class="badge bg-warning text-dark mt-1" style="font-size: 10px;">Active Promotion</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>

                                                    {{-- Size --}}
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark border px-2 py-1 fs-6">{{ $vSize }}</span>
                                                    </td>

                                                    {{-- Color --}}
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary text-white px-2 py-1">{{ $vColor }}</span>
                                                    </td>

                                                    {{-- Barcode --}}
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 12px;">
                                                            {{ $vBarcode }}
                                                        </span>
                                                    </td>

                                                    {{-- Original Price --}}
                                                    <td class="text-end">
                                                        <span class="fw-bold text-dark">Rs. {{ number_format($vPrice, 2) }}</span>
                                                    </td>

                                                    {{-- Discount % Input --}}
                                                    <td>
                                                        <div class="input-group input-group-sm cell-input-grp mx-auto">
                                                            <input type="number" step="0.1" min="0" max="100" 
                                                                   name="discount_percentage[{{ $rowGlobalIdx }}]" 
                                                                   class="form-control discountPercentage" 
                                                                   value="{{ $initPerc > 0 ? $initPerc : '' }}" placeholder="0">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </td>

                                                    {{-- Discount (PKR) Input --}}
                                                    <td>
                                                        <div class="input-group input-group-sm cell-input-grp mx-auto">
                                                            <span class="input-group-text">Rs.</span>
                                                            <input type="number" step="1" min="0" 
                                                                   name="discount_amount[{{ $rowGlobalIdx }}]" 
                                                                   class="form-control discountAmount" 
                                                                   value="{{ $initAmt > 0 ? $initAmt : '' }}" placeholder="0">
                                                        </div>
                                                    </td>

                                                    {{-- You Save --}}
                                                    <td class="text-center">
                                                        <input type="hidden" name="total_discount[{{ $rowGlobalIdx }}]" class="totalDiscountInp" value="{{ $initTotal }}">
                                                        <span class="badge-you-save totalDiscountBadge">
                                                            Rs. {{ number_format($initTotal, 2) }}
                                                        </span>
                                                    </td>

                                                    {{-- Final Offer Price --}}
                                                    <td class="text-end">
                                                        <input type="hidden" name="final_price[{{ $rowGlobalIdx }}]" class="finalPriceInp" value="{{ $initFinal }}">
                                                        <div class="badge-final-price">
                                                            <span class="strike-price strikePriceBadge">Rs. {{ number_format($vPrice, 2) }}</span>
                                                            <span class="finalPriceBadge">Rs. {{ number_format($initFinal, 2) }}</span>
                                                        </div>
                                                    </td>

                                                    {{-- Valid Date --}}
                                                    <td>
                                                        <input type="date" 
                                                               name="date[{{ $rowGlobalIdx }}]" 
                                                               class="form-control form-control-sm" 
                                                               value="{{ $initDate }}" style="font-size: 12px;">
                                                    </td>

                                                    {{-- Status --}}
                                                    <td>
                                                        <select name="status[{{ $rowGlobalIdx }}]" class="form-select form-select-sm" style="font-size: 12px;">
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
                                                    <span class="fw-bold text-dark">{{ $product->item_name }}</span>
                                                    <span class="badge bg-secondary ms-1">Base Article</span>
                                                </td>
                                                <td class="text-center"><span class="text-muted">-</span></td>
                                                <td class="text-center"><span class="text-muted">-</span></td>
                                                <td class="text-center"><span class="badge bg-light text-dark border font-monospace">{{ $product->item_code }}</span></td>
                                                <td class="text-end"><span class="fw-bold text-dark">Rs. {{ number_format($productPrice, 2) }}</span></td>
                                                <td>
                                                    <div class="input-group input-group-sm cell-input-grp mx-auto">
                                                        <input type="number" step="0.1" min="0" max="100" 
                                                               name="discount_percentage[{{ $rowGlobalIdx }}]" 
                                                               class="form-control discountPercentage" 
                                                               value="{{ $initPerc > 0 ? $initPerc : '' }}" placeholder="0">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm cell-input-grp mx-auto">
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
                                                    <div class="badge-final-price">
                                                        <span class="strike-price strikePriceBadge">Rs. {{ number_format($productPrice, 2) }}</span>
                                                        <span class="finalPriceBadge">Rs. {{ number_format($initFinal, 2) }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="date" 
                                                           name="date[{{ $rowGlobalIdx }}]" 
                                                           class="form-control form-control-sm" 
                                                           value="{{ $initDate }}" style="font-size: 12px;">
                                                </td>
                                                <td>
                                                    <select name="status[{{ $rowGlobalIdx }}]" class="form-select form-select-sm" style="font-size: 12px;">
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

                {{-- ── 5. Bottom Action Card (Clean, Normal Flow) ── --}}
                <div class="view-card p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-3 py-2 text-white fw-bold" style="background: var(--primary) !important; font-size: 13px;">
                            <i class="fas fa-check-circle me-1"></i> Ready to Apply
                        </span>
                        <span class="text-muted small fw-semibold">
                            Enter either <strong>Discount %</strong> or <strong>PKR</strong> — system automatically calculates total savings and final offer price.
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('product') }}" class="btn btn-outline-secondary btn-action-hdr">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-action-hdr shadow-sm" style="background: var(--primary); border-color: var(--primary);">
                            <i class="fas fa-save"></i> Save &amp; Apply Discounts
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
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
        const card = $(this).closest('.view-card');
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
        const card = $(this).closest('.view-card');
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
        const card = $(this).closest('.view-card');
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
        const card = $(this).closest('.view-card');
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
