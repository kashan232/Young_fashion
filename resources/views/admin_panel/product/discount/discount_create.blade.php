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
        margin-bottom: 20px;
        overflow: hidden;
    }

    .view-card-header {
        padding: 14px 20px;
        background: #ffffff;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
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

    .batch-tool-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 18px;
        margin: 16px 20px 0 20px;
    }

    .discount-table {
        min-width: 1120px;
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
        padding: 12px 12px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .discount-table tbody td {
        vertical-align: middle;
        padding: 11px 12px;
        font-size: 13px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }

    .discount-table tbody tr:hover td {
        background: #f8fafc !important;
    }

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
                    <a href="{{ route('discount.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; color: #475569; background: #ffffff;" title="Back to Discount Products">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Product Discount Setup</h3>
                            <span class="badge bg-primary text-white px-2 py-1 shadow-sm" style="font-size: 12px; font-weight: 600;">
                                <i class="fas fa-percentage me-1"></i> Products Only
                            </span>
                        </div>
                        <div class="text-muted small mt-1">
                            Apply discounts directly on products (not variants). Filter, select products, or enter % / PKR per product.
                        </div>
                    </div>
                </div>

                {{-- Header Actions --}}
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('discount.index') }}" class="btn btn-outline-secondary btn-action-hdr">
                        <i class="fas fa-list"></i> Discount Products
                    </a>
                    <a href="{{ route('product') }}" class="btn btn-light border btn-action-hdr">
                        <i class="fas fa-boxes"></i> Catalog
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

            {{-- ── 2. Server-side Search & Filter Bar (Optimized for 1,000+ Products) ── --}}
            <div class="view-card">
                <div class="p-3 bg-white">
                    <form method="GET" action="{{ route('discount.create') }}" class="row g-2 align-items-end">
                        @if(request('products'))
                            <input type="hidden" name="products" value="{{ request('products') }}">
                        @endif
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">Search Product</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or article code...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-1">Category</label>
                            <select name="category_id" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-1">Brand</label>
                            <select name="brand_id" class="form-select form-select-sm">
                                <option value="">All Brands</option>
                                @foreach($brands as $br)
                                    <option value="{{ $br->id }}" {{ request('brand_id') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-1">Discount Status</label>
                            <select name="discount_filter" class="form-select form-select-sm">
                                <option value="">All Products</option>
                                <option value="with_discount" {{ request('discount_filter') === 'with_discount' ? 'selected' : '' }}>Already Discounted</option>
                                <option value="without_discount" {{ request('discount_filter') === 'without_discount' ? 'selected' : '' }}>No Discount Yet</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <select name="per_page" class="form-select form-select-sm" style="max-width: 75px;" title="Rows per page">
                                @foreach([25, 50, 100] as $pp)
                                    <option value="{{ $pp }}" {{ (int)request('per_page', 50) === $pp ? 'selected' : '' }}>{{ $pp }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm fw-bold flex-fill" style="background: #4f46e5; border-color: #4f46e5;">
                                Filter
                            </button>
                            <a href="{{ route('discount.create') }}" class="btn btn-light border btn-sm" title="Reset Filters">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── 3. Product-Only Discount Form ── --}}
            <form action="{{ route('discount.store') }}" method="POST" id="discountForm">
                @csrf

                <div class="view-card">
                    <div class="view-card-header">
                        <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="fas fa-boxes text-primary me-2"></i>Products List
                            <span class="badge bg-light text-secondary border ms-2">
                                Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                            </span>
                        </span>
                        <span class="small text-muted" id="selectedCountBadge">0 product(s) selected</span>
                    </div>

                    {{-- Batch Tool Panel --}}
                    <div class="batch-tool-panel d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-white fw-bold px-2 py-2" style="background: #4f46e5; font-size: 11.5px;">
                                <i class="fas fa-bolt me-1"></i> BATCH TOOL
                            </span>
                            <span class="fw-bold text-dark small">
                                Apply discount to checked products (or all listed on this page):
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="input-group input-group-sm" style="width: 135px;">
                                <input type="number" step="0.1" min="0" max="100" class="form-control batch-perc-inp" placeholder="0">
                                <span class="input-group-text fw-bold">% Off</span>
                            </div>
                            <span class="small fw-bold text-muted">OR</span>
                            <div class="input-group input-group-sm" style="width: 145px;">
                                <span class="input-group-text fw-bold">Rs.</span>
                                <input type="number" step="1" min="0" class="form-control batch-amt-inp" placeholder="0">
                            </div>
                            <button type="button" class="btn btn-sm btn-primary fw-bold apply-batch-btn px-3" data-mode="selected" style="background: #4f46e5; border-color: #4f46e5; border-radius: 6px;">
                                <i class="fas fa-check me-1"></i> Apply Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold apply-batch-btn px-3" data-mode="all" style="border-radius: 6px;">
                                <i class="fas fa-check-double me-1"></i> Apply Page
                            </button>
                            <button type="button" class="btn btn-sm btn-light border reset-batch-btn px-2" style="border-radius: 6px;">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table align-middle discount-table" id="productDiscountTable">
                                <thead>
                                    <tr>
                                        <th style="width: 38px;" class="text-center">
                                            <input type="checkbox" id="selectAllProducts" class="form-check-input" title="Select all products on this page">
                                        </th>
                                        <th style="width: 45px;" class="text-center">#</th>
                                        <th>Product Name &amp; Details</th>
                                        <th style="width: 120px;">Article Code</th>
                                        <th style="width: 90px;" class="text-center">Stock</th>
                                        <th style="width: 120px;" class="text-end">Original Price</th>
                                        <th style="width: 130px;" class="text-center">Discount %</th>
                                        <th style="width: 140px;" class="text-center">Discount (PKR)</th>
                                        <th style="width: 125px;" class="text-center">You Save</th>
                                        <th style="width: 135px;" class="text-end">Final Offer Price</th>
                                        <th style="width: 130px;">Valid Date</th>
                                        <th style="width: 100px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $pIdx => $product)
                                        @php
                                            $productPrice = (float) ($product->sale_price_per_piece ?: $product->sale_price_per_box ?: 0);
                                            if ($productPrice <= 0 && $product->size_mode === 'by_size' && $product->height > 0 && $product->width > 0) {
                                                $m2 = ($product->height * $product->width) / 10000;
                                                $productPrice = $m2 * (float) $product->price_per_m2;
                                            }
                                            if ($productPrice <= 0) {
                                                $variants = $product->parsed_variants;
                                                if (!empty($variants) && isset($variants[0]['sale_price'])) {
                                                    $productPrice = (float) $variants[0]['sale_price'];
                                                }
                                            }

                                            $productDiscounts = $product->discounts ?? collect();
                                            $existingDiscount = $productDiscounts->firstWhere('variant_row_id', null) ?? $productDiscounts->first();

                                            $initPerc = old('discount_percentage.' . $pIdx, $existingDiscount->discount_percentage ?? 0);
                                            $initAmt  = old('discount_amount.' . $pIdx, $existingDiscount->discount_amount ?? 0);

                                            if ($initPerc > 0) {
                                                $initTotal = round($productPrice * $initPerc / 100, 2);
                                                $initAmt = $initTotal;
                                            } elseif ($initAmt > 0) {
                                                $initTotal = min($productPrice, $initAmt);
                                                $initPerc = $productPrice > 0 ? round(($initTotal / $productPrice) * 100, 2) : 0;
                                            } else {
                                                $initTotal = 0;
                                            }

                                            $initFinal = max(0, $productPrice - $initTotal);
                                            $initDate = old('date.' . $pIdx, $existingDiscount ? \Carbon\Carbon::parse($existingDiscount->date)->format('Y-m-d') : now()->toDateString());
                                            $initStatus = old('status.' . $pIdx, $existingDiscount->status ?? 1);
                                            $stockPieces = (float) ($product->warehouse_stocks_sum_total_pieces ?? 0);
                                        @endphp
                                        <tr class="variant-calc-row">
                                            <input type="hidden" name="product_id[{{ $pIdx }}]" class="row-input" value="{{ $product->id }}">
                                            <input type="hidden" name="actual_price[{{ $pIdx }}]" class="actualPriceInp row-input" value="{{ $productPrice }}">
                                            @if($existingDiscount)
                                                <input type="hidden" name="existing_discount_id[{{ $pIdx }}]" class="existingDiscountIdInp row-input" value="{{ $existingDiscount->id }}">
                                            @endif

                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input product-row-check">
                                            </td>

                                            <td class="text-center text-muted fw-bold">
                                                {{ ($products->firstItem() ?? 1) + $pIdx }}
                                            </td>

                                            {{-- Product Name & Details --}}
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($product->image)
                                                        <img src="{{ asset('uploads/products/' . $product->image) }}" style="width: 38px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; flex-shrink: 0;" alt="{{ $product->item_name }}">
                                                    @else
                                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted flex-shrink-0" style="width: 38px; height: 38px;">
                                                            <i class="fas fa-shoe-prints"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 13px;">{{ $product->item_name }}</div>
                                                        <div class="text-muted small">
                                                            {{ $product->category_relation->name ?? 'General' }} &bull; {{ $product->brand->name ?? 'Standard' }}
                                                            @if($existingDiscount)
                                                                <span class="badge bg-warning text-dark ms-1" style="font-size: 10px;">Has Discount</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Article Code --}}
                                            <td>
                                                <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 12px;">
                                                    {{ $product->item_code ?? '-' }}
                                                </span>
                                            </td>

                                            {{-- Stock --}}
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">{{ number_format($stockPieces, 0) }}</span>
                                            </td>

                                            {{-- Original Price --}}
                                            <td class="text-end">
                                                <span class="fw-bold text-dark">Rs. {{ number_format($productPrice, 2) }}</span>
                                            </td>

                                            {{-- Discount % Input --}}
                                            <td>
                                                <div class="input-group input-group-sm cell-input-grp mx-auto">
                                                    <input type="number" step="0.1" min="0" max="100"
                                                           name="discount_percentage[{{ $pIdx }}]"
                                                           class="form-control discountPercentage row-input"
                                                           value="{{ $initPerc > 0 ? $initPerc : '' }}" placeholder="0">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>

                                            {{-- Discount (PKR) Input --}}
                                            <td>
                                                <div class="input-group input-group-sm cell-input-grp mx-auto">
                                                    <span class="input-group-text">Rs.</span>
                                                    <input type="number" step="1" min="0"
                                                           name="discount_amount[{{ $pIdx }}]"
                                                           class="form-control discountAmount row-input"
                                                           value="{{ $initAmt > 0 ? $initAmt : '' }}" placeholder="0">
                                                </div>
                                            </td>

                                            {{-- You Save --}}
                                            <td class="text-center">
                                                <input type="hidden" name="total_discount[{{ $pIdx }}]" class="totalDiscountInp row-input" value="{{ $initTotal }}">
                                                <span class="badge-you-save totalDiscountBadge">
                                                    Rs. {{ number_format($initTotal, 2) }}
                                                </span>
                                            </td>

                                            {{-- Final Offer Price --}}
                                            <td class="text-end">
                                                <input type="hidden" name="final_price[{{ $pIdx }}]" class="finalPriceInp row-input" value="{{ $initFinal }}">
                                                <div class="badge-final-price">
                                                    <span class="strike-price strikePriceBadge">Rs. {{ number_format($productPrice, 2) }}</span>
                                                    <span class="finalPriceBadge">Rs. {{ number_format($initFinal, 2) }}</span>
                                                </div>
                                            </td>

                                            {{-- Valid Date --}}
                                            <td>
                                                <input type="date"
                                                       name="date[{{ $pIdx }}]"
                                                       class="form-control form-control-sm row-input"
                                                       value="{{ $initDate }}" style="font-size: 12px;">
                                            </td>

                                            {{-- Status --}}
                                            <td>
                                                <select name="status[{{ $pIdx }}]" class="form-select form-select-sm row-input" style="font-size: 12px;">
                                                    <option value="1" {{ $initStatus == 1 ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ $initStatus == 0 ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center py-5 text-muted">
                                                <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                                                <div>No products match the selected filters.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($products->hasPages())
                        <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="small text-muted">
                                Page {{ $products->currentPage() }} of {{ $products->lastPage() }}
                            </div>
                            <div>
                                {{ $products->links() }}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ── 4. Bottom Action Card ── --}}
                <div class="view-card p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-3 py-2 text-white fw-bold" style="background: var(--primary) !important; font-size: 13px;">
                            <i class="fas fa-check-circle me-1"></i> Ready to Apply
                        </span>
                        <span class="text-muted small fw-semibold">
                            Only products with a discount entered (or updated existing discounts) will be saved.
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('discount.index') }}" class="btn btn-outline-secondary btn-action-hdr">
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

    function updateSelectedCount() {
        var count = $('.product-row-check:checked').length;
        $('#selectedCountBadge').text(count + ' product(s) selected');
    }

    $('#selectAllProducts').on('change', function() {
        $('.product-row-check').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });

    $(document).on('change', '.product-row-check', function() {
        updateSelectedCount();
        var total = $('.product-row-check').length;
        var checked = $('.product-row-check:checked').length;
        $('#selectAllProducts').prop('checked', total > 0 && checked === total);
    });

    function updateRowDisplay(row, actualPrice, totalDiscount) {
        const cappedDiscount = Math.min(Math.max(0, totalDiscount), actualPrice);
        const finalPrice = Math.max(0, actualPrice - cappedDiscount);

        row.find('.totalDiscountInp').val(cappedDiscount.toFixed(2));
        row.find('.totalDiscountBadge').text('Rs. ' + formatNumber(cappedDiscount));

        row.find('.finalPriceInp').val(finalPrice.toFixed(2));
        row.find('.finalPriceBadge').text('Rs. ' + formatNumber(finalPrice));

        if (cappedDiscount > 0) {
            row.find('.strikePriceBadge').show();
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
        row.find('.discountAmount').val(perc > 0 ? calculatedAmt.toFixed(2) : '');
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
        row.find('.discountPercentage').val(amt > 0 ? calculatedPerc.toFixed(1) : '');
        updateRowDisplay(row, actualPrice, amt);
    });

    // 3. Batch Tool mutually exclusive typing
    $(document).on('input', '.batch-perc-inp', function(){
        if ($(this).val() !== '') $('.batch-amt-inp').val('');
    });
    $(document).on('input', '.batch-amt-inp', function(){
        if ($(this).val() !== '') $('.batch-perc-inp').val('');
    });

    // 4. Batch Apply Button (Selected or All on page)
    $(document).on('click', '.apply-batch-btn', function(){
        const mode = $(this).data('mode');
        const percVal = $('.batch-perc-inp').val();
        const amtVal = $('.batch-amt-inp').val();

        if (percVal === '' && amtVal === '') {
            Swal.fire('Enter Discount', 'Please enter a Discount % or Rs. amount in the batch tool first.', 'info');
            return;
        }

        let targetRows = $('tr.variant-calc-row');
        if (mode === 'selected') {
            targetRows = targetRows.filter(function() {
                return $(this).find('.product-row-check').is(':checked');
            });
            if (targetRows.length === 0) {
                Swal.fire('No Products Selected', 'Please check at least one product row, or click "Apply Page".', 'info');
                return;
            }
        }

        targetRows.each(function(){
            const row = $(this);
            const actualPrice = parseFloat(row.find('.actualPriceInp').val()) || 0;

            if (percVal !== '') {
                const p = Math.min(100, Math.max(0, parseFloat(percVal) || 0));
                row.find('.discountPercentage').val(p);
                const computedAmt = (actualPrice * p) / 100;
                row.find('.discountAmount').val(computedAmt.toFixed(2));
                updateRowDisplay(row, actualPrice, computedAmt);
            } else if (amtVal !== '') {
                let a = Math.max(0, parseFloat(amtVal) || 0);
                if (actualPrice > 0 && a > actualPrice) a = actualPrice;
                row.find('.discountAmount').val(a);
                const computedPerc = actualPrice > 0 ? (a / actualPrice) * 100 : 0;
                row.find('.discountPercentage').val(computedPerc.toFixed(1));
                updateRowDisplay(row, actualPrice, a);
            }
        });
    });

    // 5. Reset Batch
    $(document).on('click', '.reset-batch-btn', function(){
        $('.batch-perc-inp').val('');
        $('.batch-amt-inp').val('');

        let targetRows = $('tr.variant-calc-row');
        const checked = $('.product-row-check:checked').length;
        if (checked > 0) {
            targetRows = targetRows.filter(function() {
                return $(this).find('.product-row-check').is(':checked');
            });
        }

        targetRows.each(function(){
            const row = $(this);
            const actualPrice = parseFloat(row.find('.actualPriceInp').val()) || 0;
            row.find('.discountPercentage').val('');
            row.find('.discountAmount').val('');
            updateRowDisplay(row, actualPrice, 0);
        });
    });

    // 6. Prevent hitting PHP max_input_vars: skip empty non-existing rows before submit
    $('#discountForm').on('submit', function() {
        $('tr.variant-calc-row').each(function() {
            const row = $(this);
            const perc = parseFloat(row.find('.discountPercentage').val()) || 0;
            const amt = parseFloat(row.find('.discountAmount').val()) || 0;
            const hasExisting = row.find('.existingDiscountIdInp').length > 0;

            if (perc <= 0 && amt <= 0 && !hasExisting) {
                row.find('.row-input').prop('disabled', true);
            }
        });
    });

    // Initial pass on load
    $('tr.variant-calc-row').each(function(){
        const row = $(this);
        const actualPrice = parseFloat(row.find('.actualPriceInp').val()) || 0;
        const total = parseFloat(row.find('.totalDiscountInp').val()) || 0;
        updateRowDisplay(row, actualPrice, total);
    });

});
</script>
@endsection
