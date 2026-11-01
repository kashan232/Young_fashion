@extends('admin_panel.layout.app')

@section('content')
<style>
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
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .article-card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
    }
    .article-img-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #fff;
    }
    .quick-batch-bar {
        background: #eef2ff;
        border: 1px dashed #a5b4fc;
        border-radius: 10px;
        padding: 12px 18px;
        margin: 14px 20px 0 20px;
    }
    .variant-table th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.72rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        border-top: none;
        vertical-align: middle;
        padding: 10px 12px;
    }
    .variant-table td {
        vertical-align: middle;
        padding: 8px 12px;
        font-size: 0.85rem;
    }
    .badge-size {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        font-size: 0.75rem;
    }
    .badge-color {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
        font-size: 0.75rem;
    }
    .barcode-chip {
        font-family: monospace;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 3px 6px;
        border-radius: 4px;
        font-size: 0.8rem;
        color: #334155;
    }
    .final-price-text {
        font-size: 0.95rem;
        font-weight: 800;
        color: #15803d;
    }
    .sticky-bottom-bar {
        position: sticky;
        bottom: 12px;
        z-index: 100;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(8px);
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        padding: 14px 24px;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-2">

    {{-- ── Top Navigation Bar ── --}}
    <div class="discount-page-hdr d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                <span style="color:#4f46e5;"><i class="fas fa-tags"></i></span> 
                Create &amp; Apply Discount to Products &amp; Variants
            </h4>
            <p class="text-muted small mb-0">
                Set footwear discounts for the entire article or specify individual discounts for each shoe size &amp; color variant.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('discount.index') }}" class="btn btn-outline-secondary btn-sm fw-bold px-3">
                <i class="fas fa-list me-1"></i> Discount Rules List
            </a>
            <a href="{{ route('product') }}" class="btn btn-light border btn-sm fw-bold px-3">
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
                            <img src="{{ asset('uploads/products/' . $product->image) }}" class="article-img-thumb" alt="{{ $product->item_name }}">
                        @else
                            <div class="article-img-thumb d-flex align-items-center justify-content-center text-muted">
                                <i class="fas fa-shoe-prints"></i>
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
                                <strong>Base Price:</strong> Rs. {{ number_format($productPrice, 2) }}
                                @if($hasVariants)
                                    &bull; <span class="badge bg-primary text-white">{{ count($variants) }} Variants / Sizes</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="badge bg-white text-secondary border px-3 py-2 fw-semibold">
                            <i class="fas fa-cubes me-1"></i> Stock: {{ $product->warehouse_stocks_sum_total_pieces ?? 0 }} Pcs
                        </span>
                    </div>
                </div>

                {{-- ── If Variants Exist: Quick Batch Tool ── --}}
                @if($hasVariants)
                <div class="quick-batch-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-indigo text-white fw-bold" style="background:#4f46e5;">⚡ BATCH TOOL</span>
                        <span class="small fw-bold text-dark">Apply same discount to all {{ count($variants) }} variants of this shoe:</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group input-group-sm" style="width: 140px;">
                            <input type="number" step="0.01" min="0" max="100" class="form-control batch-perc-inp" placeholder="0" title="Discount percentage">
                            <span class="input-group-text fw-bold">% Off</span>
                        </div>
                        <span class="small text-muted">or</span>
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <span class="input-group-text fw-bold">PKR</span>
                            <input type="number" step="0.01" min="0" class="form-control batch-amt-inp" placeholder="0" title="Discount PKR amount">
                        </div>
                        <button type="button" class="btn btn-sm btn-primary fw-bold apply-batch-btn" data-target="#table-variants-{{ $product->id }}" style="background:#4f46e5; border-color:#4f46e5;">
                            <i class="fas fa-check-double me-1"></i> Apply to All Variants
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary reset-batch-btn" data-target="#table-variants-{{ $product->id }}">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
                @endif

                {{-- ── Variants / Product Rows Table ── --}}
                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle variant-table mb-0" id="table-variants-{{ $product->id }}">
                        <thead>
                            <tr>
                                <th style="width: 35px;">#</th>
                                <th>Item / Variant</th>
                                <th style="width: 90px;">Size</th>
                                <th style="width: 100px;">Color</th>
                                <th style="width: 110px;">Barcode</th>
                                <th style="width: 110px;">Original Price</th>
                                <th style="width: 110px;">Discount %</th>
                                <th style="width: 120px;">Discount PKR</th>
                                <th style="width: 110px;">Total Off</th>
                                <th style="width: 120px;">Final Price</th>
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

                                        // Check if this variant already has a saved discount
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
                                                    @php $vImg = $variant['images'][0]; @endphp
                                                    <img src="{{ asset('uploads/products/' . $vImg) }}" style="width:32px; height:32px; object-fit:cover; border-radius:4px; border:1px solid #e2e8f0;">
                                                @endif
                                                <div>
                                                    <span class="fw-semibold text-dark">{{ $vName }}</span>
                                                    @if($existingDiscount)
                                                        <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">Has Existing Rule</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-size">{{ $vSize }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-color">{{ $vColor }}</span>
                                        </td>
                                        <td>
                                            <span class="barcode-chip">{{ $vBarcode }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-secondary">Rs. {{ number_format($vPrice, 2) }}</span>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" max="100" 
                                                   name="discount_percentage[{{ $rowGlobalIdx }}]" 
                                                   class="form-control form-control-sm discountPercentage" 
                                                   value="{{ $initPerc }}" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" 
                                                   name="discount_amount[{{ $rowGlobalIdx }}]" 
                                                   class="form-control form-control-sm discountAmount" 
                                                   value="{{ $initAmt }}" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   name="total_discount[{{ $rowGlobalIdx }}]" 
                                                   class="form-control form-control-sm totalDiscount bg-light text-danger fw-bold" 
                                                   value="{{ $initTotal }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   name="final_price[{{ $rowGlobalIdx }}]" 
                                                   class="form-control form-control-sm finalPrice bg-light final-price-text" 
                                                   value="{{ $initFinal }}" readonly>
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
                                        <span class="fw-semibold text-dark">{{ $product->item_name }}</span>
                                        <span class="badge bg-secondary ms-1">Base Article</span>
                                    </td>
                                    <td><span class="text-muted">-</span></td>
                                    <td><span class="text-muted">-</span></td>
                                    <td><span class="barcode-chip">{{ $product->item_code }}</span></td>
                                    <td><span class="fw-bold text-secondary">Rs. {{ number_format($productPrice, 2) }}</span></td>
                                    <td>
                                        <input type="number" step="0.01" min="0" max="100" 
                                               name="discount_percentage[{{ $rowGlobalIdx }}]" 
                                               class="form-control form-control-sm discountPercentage" 
                                               value="{{ $initPerc }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                               name="discount_amount[{{ $rowGlobalIdx }}]" 
                                               class="form-control form-control-sm discountAmount" 
                                               value="{{ $initAmt }}" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="total_discount[{{ $rowGlobalIdx }}]" 
                                               class="form-control form-control-sm totalDiscount bg-light text-danger fw-bold" 
                                               value="{{ $initTotal }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="final_price[{{ $rowGlobalIdx }}]" 
                                               class="form-control form-control-sm finalPrice bg-light final-price-text" 
                                               value="{{ $initFinal }}" readonly>
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
                <span class="badge bg-primary px-3 py-2 fw-semibold" style="font-size:13px;">
                    <i class="fas fa-boxes me-1"></i> {{ count($products) }} Product Articles
                </span>
                <span class="text-muted small fw-medium">
                    Adjust discounts individually or use the batch tool above. Only rows with discounts or active status will be saved.
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('product') }}" class="btn btn-outline-secondary fw-semibold px-4">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" style="background:#4f46e5; border-color:#4f46e5; border-radius:8px;">
                    <i class="fas fa-save me-1"></i> Save &amp; Apply Discounts
                </button>
            </div>
        </div>

    </form>
</div>

<script>
$(document).ready(function(){

    function calculateRow(row) {
        const actualPrice = parseFloat(row.find('.actualPriceInp').val()) || 0;
        let perc = parseFloat(row.find('.discountPercentage').val()) || 0;
        let amt  = parseFloat(row.find('.discountAmount').val()) || 0;

        if (perc < 0) { perc = 0; row.find('.discountPercentage').val(0); }
        if (perc > 100) { perc = 100; row.find('.discountPercentage').val(100); }
        if (amt < 0) { amt = 0; row.find('.discountAmount').val(0); }

        const percDiscount = (actualPrice * perc) / 100;
        let totalDiscount = percDiscount + amt;

        if (totalDiscount > actualPrice && actualPrice > 0) {
            row.find('.discountPercentage, .discountAmount').addClass('is-invalid');
        } else {
            row.find('.discountPercentage, .discountAmount').removeClass('is-invalid');
        }

        const cappedDiscount = Math.min(totalDiscount, actualPrice);
        const finalPrice = Math.max(0, actualPrice - cappedDiscount);

        row.find('.totalDiscount').val(cappedDiscount.toFixed(2));
        row.find('.finalPrice').val(finalPrice.toFixed(2));
    }

    // Input listener for any discount field
    $(document).on('input', '.discountPercentage, .discountAmount', function(){
        const row = $(this).closest('tr.variant-calc-row');
        calculateRow(row);
    });

    // Batch apply to all variants in an article card
    $(document).on('click', '.apply-batch-btn', function(){
        const targetTable = $(this).data('target');
        const card = $(this).closest('.article-card');
        const percVal = card.find('.batch-perc-inp').val();
        const amtVal = card.find('.batch-amt-inp').val();

        $(targetTable).find('tbody tr.variant-calc-row').each(function(){
            const row = $(this);
            if (percVal !== '') {
                row.find('.discountPercentage').val(percVal);
            }
            if (amtVal !== '') {
                row.find('.discountAmount').val(amtVal);
            }
            calculateRow(row);
        });
    });

    // Reset batch inputs
    $(document).on('click', '.reset-batch-btn', function(){
        const targetTable = $(this).data('target');
        const card = $(this).closest('.article-card');
        card.find('.batch-perc-inp').val('');
        card.find('.batch-amt-inp').val('');

        $(targetTable).find('tbody tr.variant-calc-row').each(function(){
            const row = $(this);
            row.find('.discountPercentage').val(0);
            row.find('.discountAmount').val(0);
            calculateRow(row);
        });
    });

    // Initial calculation on page load
    $('tr.variant-calc-row').each(function(){
        calculateRow($(this));
    });

});
</script>
@endsection
