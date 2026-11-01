@extends('admin_panel.layout.app')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}">

<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --primary-light: #eef2ff;
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
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
        flex-wrap: gap;
    }

    .field-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        height: 100%;
    }

    .field-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 4px;
        letter-spacing: 0.3px;
    }

    .field-value {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .var-photo-preview-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.18s ease;
    }
    .var-photo-preview-btn:hover {
        border-color: var(--primary);
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.2);
    }
    .var-thumb-mini {
        width: 34px;
        height: 34px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
    }

    .view-color-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12.5px;
        font-weight: 600;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .view-color-chip:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    .view-color-chip.active {
        background: var(--primary) !important;
        color: #ffffff !important;
        border-color: var(--primary) !important;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
    }
    .view-color-chip.active .badge {
        background: rgba(255,255,255,0.25) !important;
        color: #ffffff !important;
    }
    .view-color-chip[data-color="discounted"].active {
        background: #dc2626 !important;
        color: #ffffff !important;
        border-color: #dc2626 !important;
        box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25) !important;
    }
    .variant-data-row.variant-promo-active {
        background-color: #fffdf5 !important;
    }
    .variant-data-row.variant-promo-active:hover {
        background-color: #fef9e7 !important;
    }

    .btn-action-hdr {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.15s ease;
        text-decoration: none !important;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid px-4 py-4">
            @php
                $discountedVariants = collect($variants)->filter(function($item) {
                    return !empty($item['active_discount']);
                });
                $discountCount = $discountedVariants->count();
            @endphp

            {{-- ── Top Navigation & Action Header ── --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('product') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; color: #475569;" title="Back to Products">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ $product->item_name }}</h3>
                            @if($product->rack_shelf)
                                <span class="badge bg-success text-white px-2 py-1 shadow-sm" style="font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-layer-group me-1"></i>{{ $product->rack_shelf }}
                                </span>
                            @endif
                            @if($product->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                            @endif
                            @if($discountCount > 0)
                                <span class="badge bg-danger text-white px-2 py-1 shadow-sm" style="font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-tag me-1"></i>On Discount ({{ $discountCount }} Variants)
                                </span>
                            @endif
                        </div>
                        <div class="text-muted small mt-1">
                            <span>Code: <strong>{{ $product->item_code ?? '-' }}</strong></span>
                            <span class="mx-2">•</span>
                            <span>Category: <strong>{{ $product->category_relation->name ?? 'Footwear' }}</strong></span>
                            <span class="mx-2">•</span>
                            <span>Brand: <strong>{{ $product->brand->name ?? 'Young Fashion' }}</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('product') }}" class="btn btn-outline-secondary btn-action-hdr">
                        <i class="fas fa-list"></i> Product Catalog
                    </a>
                    <a href="{{ route('discount.create') }}?products={{ $product->id }}" class="btn btn-outline-primary btn-action-hdr" style="color: #4f46e5; border-color: #c7d2fe; background: #f5f3ff;" title="Apply discount to this article and variants">
                        <i class="fas fa-tags"></i> Apply Discount
                    </a>
                    <a href="{{ route('generate-barcode-image', $product->id) }}" class="btn btn-outline-dark btn-action-hdr">
                        <i class="fas fa-barcode"></i> Print Barcode
                    </a>
                    @if (auth()->check() && (auth()->user()->can('products.edit') || auth()->user()->email === 'admin@admin.com'))
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-action-hdr" style="background: var(--primary); border-color: var(--primary);">
                            <i class="fas fa-pencil-alt"></i> Edit Product
                        </a>
                    @endif
                </div>
            </div>

            {{-- ── 1. Product Identity & Details Card ── --}}
            <div class="view-card">
                <div class="view-card-header">
                    <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">
                        <i class="fas fa-tag text-primary me-2"></i>Product Identity &amp; Details
                    </span>
                    <span class="badge bg-light text-secondary border px-3 py-1">
                        Article Code: <strong>{{ $product->item_code ?? '-' }}</strong>
                    </span>
                </div>
                <div class="p-4 bg-white">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-9">
                            <div class="row g-3">
                                <div class="col-sm-6 col-md-4">
                                    <div class="field-box">
                                        <div class="field-label">Product / Article Name</div>
                                        <div class="field-value text-truncate" title="{{ $product->item_name }}">{{ $product->item_name }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="field-box">
                                        <div class="field-label">Rack / Shelf Location</div>
                                        <div class="field-value text-success">
                                            <i class="fas fa-layer-group me-1"></i>{{ $product->rack_shelf ?? 'Not Assigned' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="field-box">
                                        <div class="field-label">Category &amp; Sub Category</div>
                                        <div class="field-value text-truncate">
                                            {{ $product->category_relation->name ?? 'Footwear' }}
                                            @if($product->sub_category_relation)
                                                / {{ $product->sub_category_relation->name }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="field-box">
                                        <div class="field-label">Brand</div>
                                        <div class="field-value">{{ $product->brand->name ?? 'Young Fashion' }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="field-box">
                                        <div class="field-label">Base Unit &amp; Size Mode</div>
                                        <div class="field-value">
                                            {{ $product->unit->name ?? 'Pcs' }}
                                            @if($product->size_mode)
                                                <span class="text-muted font-normal">({{ ucwords(str_replace('_', ' ', $product->size_mode)) }})</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="field-box" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                                        <div class="field-label" style="color: #166534 !important;">Overall Inventory Stock</div>
                                        <div class="field-value fs-5" style="color: #15803d !important;">
                                            {{ $totalStock ?? $stockDisplay ?? 0 }} Pairs Available
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Product Main Image --}}
                        <div class="col-lg-3 text-center">
                            @php
                                $mainImg = $product->image ? (str_starts_with($product->image, 'http') ? $product->image : (str_starts_with($product->image, '/') ? $product->image : '/uploads/products/' . $product->image)) : '/uploads/products/placeholder.png';
                            @endphp
                            <div class="p-2 border rounded-3 bg-light shadow-sm d-inline-block position-relative" style="width: 140px; height: 140px; cursor: pointer;" id="mainProductPhotoFrame" data-src="{{ $mainImg }}">
                                <img src="{{ $mainImg }}" class="rounded w-100 h-100" style="object-fit: contain;">
                                <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 text-white" style="font-size: 10px;">
                                    <i class="fas fa-search-plus"></i> Zoom
                                </span>
                            </div>
                            <small class="text-muted d-block mt-2 font-semibold">Article Main Photo</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Promotional Discount Live Banner (If any variant is discounted) ── --}}
            @if($discountCount > 0)
                <div class="card border-0 mb-4 shadow-sm" style="background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%); border-left: 5px solid #4f46e5 !important; border-radius: 12px;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 44px; height: 44px; font-size: 20px; background: #4f46e5;">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                    Active Promotional Discount is Live!
                                    <span class="badge bg-danger text-white px-2 py-0.5" style="font-size: 11px;">PROMO ACTIVE</span>
                                </div>
                                <div class="small text-muted mt-0.5">
                                    <strong class="text-primary">{{ $discountCount }}</strong> of {{ count($variants) }} shoe variants currently have discounted customer sale prices.
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary bg-white fw-bold px-3 py-1.5 filter-discount-trigger shadow-sm" style="border-radius: 8px;">
                                <i class="fas fa-filter text-primary me-1"></i> Filter Discounted Variants ({{ $discountCount }})
                            </button>
                            <a href="{{ route('discount.create') }}?products={{ $product->id }}" class="btn btn-sm btn-primary fw-bold px-3 py-1.5 shadow-sm" style="background: #4f46e5; border-color: #4f46e5; border-radius: 8px;">
                                <i class="fas fa-edit me-1"></i> Modify Discounts
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── 2. Product Variants & Units Card (Exact Match to Create Page) ── --}}
            <div class="view-card">
                <div class="view-card-header flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-dark fs-5">
                            <i class="fas fa-boxes text-primary me-2"></i>Product Variants &amp; Units
                        </span>
                        <span class="badge bg-primary text-white px-3 py-1 fs-6 ms-2">
                            Overall Stock: {{ $totalStock ?? $stockDisplay ?? 0 }}
                        </span>
                    </div>

                    {{-- Search & Color Filter Controls --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group" style="width: 260px;">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" id="variantSearchInput" placeholder="Search size, color, barcode...">
                        </div>
                        <div class="d-flex gap-1 flex-wrap" id="variantColorFilters">
                            <button type="button" class="view-color-chip active color-filter-btn" data-color="all">
                                <span>All Variants</span> <span class="badge bg-light text-dark ms-1">{{ count($variants) }}</span>
                            </button>
                            @if($discountCount > 0)
                                <button type="button" class="view-color-chip color-filter-btn" data-color="discounted" style="border-color: #fca5a5; color: #dc2626; background: #fff1f2;">
                                    <i class="fas fa-tag text-danger me-1"></i>
                                    <span>On Discount</span> <span class="badge bg-danger text-white ms-1">{{ $discountCount }}</span>
                                </button>
                            @endif
                            @php
                                $colorCounts = [];
                                foreach ($variants as $v) {
                                    $c = (!empty($v['color']) && $v['color'] !== '-') ? trim($v['color']) : 'General';
                                    $colorCounts[$c] = ($colorCounts[$c] ?? 0) + 1;
                                }
                            @endphp
                            @foreach ($colorCounts as $cName => $cnt)
                                <button type="button" class="view-color-chip color-filter-btn" data-color="{{ $cName }}">
                                    <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                    <span>{{ $cName }}</span> <span class="badge bg-light text-dark ms-1">{{ $cnt }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- The Full-Width Variants Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="showVariantsTable" style="font-size: 13px;">
                        <thead class="table-light text-muted text-uppercase" style="font-size: 11.5px; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4 py-3 text-center" style="width: 50px;">#</th>
                                <th class="py-3 text-start" style="min-width: 200px;">Variant Name</th>
                                <th class="py-3 text-center" style="width: 100px;">Size</th>
                                <th class="py-3 text-center" style="width: 110px;">Color</th>
                                <th class="py-3 text-center" style="width: 160px; min-width: 140px;"><i class="fas fa-camera text-primary me-1"></i>Photos</th>
                                <th class="py-3 text-center" style="width: 80px;">Unit</th>
                                <th class="py-3 text-center" style="width: 140px;">Available Stock</th>
                                <th class="py-3 text-end" style="min-width: 165px; width: 175px;">Sale / Offer Price</th>
                                <th class="py-3 text-end" style="width: 120px;">Wholesale</th>
                                <th class="py-3 text-end" style="width: 120px;">Purch Price</th>
                                <th class="py-3 text-center" style="width: 80px;">Alert</th>
                                <th class="pe-4 py-3 text-center" style="width: 130px;">Barcode</th>
                            </tr>
                        </thead>
                        <tbody id="showVariantsTbody">
                            @forelse($variants as $idx => $v)
                                @php
                                    $vName = $v['name'] ?? 'Shoe Variant';
                                    $vSize = $v['size'] ?? '-';
                                    $vColor = (!empty($v['color']) && $v['color'] !== '-') ? trim($v['color']) : 'General';
                                    $vUnit = $v['unit'] ?? ($product->unit->name ?? 'Pcs');
                                    $vStock = (float)($v['available_stock'] ?? ($v['stock'] ?? 0));
                                    $vSale = (float)($v['sale_price'] ?? 0);
                                    $vWholesale = (float)($v['wholesale_price'] ?? 0);
                                    $vPurch = (float)($v['purch_price'] ?? 0);
                                    $vAlert = $v['alert'] ?? 0;
                                    $vBarcode = $v['barcode'] ?? '-';
                                    $vPhotos = $v['resolved_photos'] ?? [];
                                @endphp
                                <tr class="variant-data-row {{ !empty($v['active_discount']) ? 'variant-promo-active' : '' }}" data-has-discount="{{ !empty($v['active_discount']) ? '1' : '0' }}" data-color="{{ $vColor }}" data-search="{{ strtolower("$vName $vSize $vColor $vBarcode") }}">
                                    <td class="ps-4 py-3 text-center text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td class="py-3 text-start fw-bold text-dark">{{ $vName }}</td>
                                    <td class="py-3 text-center"><span class="badge bg-light text-dark border px-2 py-1 fs-6">{{ $vSize }}</span></td>
                                    <td class="py-3 text-center"><span class="badge bg-secondary text-white px-2 py-1">{{ $vColor }}</span></td>
                                    <td class="py-3 text-center">
                                        @if(count($vPhotos) > 0)
                                            @php
                                                $firstPhoto = $vPhotos[0];
                                                $firstPhotoUrl = str_starts_with($firstPhoto, 'http') ? $firstPhoto : (str_starts_with($firstPhoto, '/') ? $firstPhoto : '/uploads/products/' . $firstPhoto);
                                            @endphp
                                            <div class="var-photo-preview-btn open-gallery-trigger" 
                                                 data-title="{{ $vName }} ({{ $vColor }} - Size {{ $vSize }}) - Photos" 
                                                 data-photos="{{ json_encode(array_values($vPhotos)) }}" 
                                                 title="Click to view all {{ count($vPhotos) }} photos">
                                                <img src="{{ $firstPhotoUrl }}" class="var-thumb-mini">
                                                <span class="badge bg-primary text-white" style="font-size: 11px;">
                                                    <i class="fas fa-camera me-1"></i>{{ count($vPhotos) }} Pics
                                                </span>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted border py-1 px-2" style="font-size: 11px;">
                                                <i class="fas fa-camera-slash me-1"></i>0 Pics
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-center text-muted">{{ $vUnit }}</td>
                                    <td class="py-3 text-center">
                                        @if($vStock > 5)
                                            <span class="badge bg-success text-white px-2 py-1"><i class="fas fa-check-circle me-1"></i>{{ $vStock }} In Stock</span>
                                        @elseif($vStock > 0)
                                            <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-exclamation-triangle me-1"></i>{{ $vStock }} Low Stock</span>
                                        @else
                                            <span class="badge bg-danger text-white px-2 py-1"><i class="fas fa-times-circle me-1"></i>Out of Stock</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-end" style="min-width: 155px;">
                                        @if(!empty($v['active_discount']))
                                            @php
                                                $vDisc = $v['active_discount'];
                                                $discPerc = floatval($vDisc->discount_percentage);
                                                $totalOff = floatval($vDisc->total_discount);
                                                $finalPrice = floatval($vDisc->final_price);
                                                if ($finalPrice <= 0) {
                                                    $finalPrice = max(0, $vSale - $totalOff);
                                                }
                                            @endphp
                                            <div class="d-flex flex-column align-items-end">
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    <span class="badge bg-danger text-white fw-bold px-1.5 py-0.5" style="font-size: 10px; letter-spacing: 0.3px;">
                                                        <i class="fas fa-tag me-0.5"></i>{{ $discPerc > 0 ? rtrim(rtrim(number_format($discPerc, 2), '0'), '.') . '% Off' : 'Rs. ' . number_format($totalOff) . ' Off' }}
                                                    </span>
                                                    <span class="text-decoration-line-through text-muted small" style="font-size: 11px;">
                                                        Rs. {{ number_format($vSale) }}
                                                    </span>
                                                </div>
                                                <div class="fw-bold text-success fs-6" style="line-height: 1.1;">
                                                    Rs. {{ number_format($finalPrice) }}
                                                </div>
                                                <div class="text-danger fw-semibold mt-0.5" style="font-size: 10.5px;">
                                                    Save Rs. {{ number_format($totalOff) }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="fw-bold text-dark fs-6" style="line-height: 1.1;">Rs. {{ number_format($vSale) }}</div>
                                            <span class="badge bg-light text-muted border mt-1" style="font-size: 10px; font-weight: 500;">Regular Price</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-end text-dark">Rs. {{ number_format($vWholesale) }}</td>
                                    <td class="py-3 text-end text-muted">Rs. {{ number_format($vPurch) }}</td>
                                    <td class="py-3 text-center text-muted">{{ $vAlert }}</td>
                                    <td class="pe-4 py-3 text-center"><code class="bg-light px-2 py-1 rounded border text-dark">{{ $vBarcode }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">No variants registered for this product.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer Summary --}}
                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center text-muted small flex-wrap gap-2">
                    <span id="variantShowingCounter">Showing all {{ count($variants) }} variants</span>
                    <span class="fst-italic"><i class="fas fa-info-circle me-1 text-primary"></i>Click on any variant's photo button to view high-resolution multi-angle photos.</span>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── Variant Photos Lightbox Modal ── --}}
<div id="showVariantGalleryModal" class="modal fade" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2 px-3 d-flex align-items-center justify-content-between">
                <h6 class="modal-title mb-0 d-flex align-items-center gap-2" id="galleryModalTitle" style="font-size: 14px;">
                    <i class="fas fa-images text-warning"></i>
                    <span>Variant Photos</span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" style="background:none; border:none; color:#fff; font-size:1.2rem; cursor:pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-3 text-center" style="background: #0f172a;">
                <!-- Main High-Res Shoe Image Preview -->
                <div class="position-relative d-flex align-items-center justify-content-center rounded shadow-sm mb-3" style="min-height: 420px; max-height: 520px; overflow: hidden; background: #1e293b;">
                    <img id="galleryMainImage" src="" style="max-height: 500px; max-width: 100%; object-fit: contain; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.4));" class="rounded">
                    
                    <button type="button" class="btn btn-dark btn-sm position-absolute start-0 top-50 translate-middle-y ms-3 rounded-circle shadow" id="galleryPrevBtn" style="width: 44px; height: 44px; opacity: 0.9;">
                        <i class="fas fa-chevron-left fs-6"></i>
                    </button>
                    <button type="button" class="btn btn-dark btn-sm position-absolute end-0 top-50 translate-middle-y me-3 rounded-circle shadow" id="galleryNextBtn" style="width: 44px; height: 44px; opacity: 0.9;">
                        <i class="fas fa-chevron-right fs-6"></i>
                    </button>
                    
                    <span class="position-absolute bottom-0 start-50 translate-middle-x mb-2 badge bg-black bg-opacity-75 text-white px-3 py-1 fs-6" id="galleryCounter" style="border-radius: 20px;">
                        Photo 1 of 1
                    </span>
                </div>
                <!-- Thumbnails strip -->
                <div class="d-flex gap-2 justify-content-center flex-wrap p-2 rounded border border-secondary" id="galleryThumbsStrip" style="max-height: 95px; overflow-x: auto; background: #1e293b;">
                    <!-- Thumbnails rendered here -->
                </div>
            </div>
            <div class="modal-footer py-2 px-3 bg-white justify-content-between">
                <div class="text-muted small">
                    <i class="fas fa-mouse-pointer text-primary me-1"></i>Click any thumbnail or use left/right arrow buttons to switch angle
                </div>
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" data-dismiss="modal">Close Gallery</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    function formatImgUrl(img) {
        if (!img) return '/uploads/products/placeholder.png';
        if (img.startsWith('http://') || img.startsWith('https://')) return img;
        if (img.startsWith('/')) return img;
        if (img.startsWith('uploads/')) return '/' + img;
        return '/uploads/products/' + img;
    }

    // Lightbox State
    let currentPhotos = [];
    let currentIdx = 0;

    function openGallery(title, photos, startIdx = 0) {
        if (!photos || photos.length === 0) return;
        currentPhotos = photos.map(formatImgUrl);
        currentIdx = startIdx;

        $('#galleryModalTitle').html(`<i class="fas fa-images text-warning me-2"></i>${title}`);
        updateGallery();
        $('#showVariantGalleryModal').modal('show');
    }

    function updateGallery() {
        if (currentPhotos.length === 0) return;
        const currentSrc = currentPhotos[currentIdx];
        $('#galleryMainImage').attr('src', currentSrc);
        $('#galleryCounter').text(`Photo ${currentIdx + 1} of ${currentPhotos.length}`);

        const strip = $('#galleryThumbsStrip');
        strip.empty();

        currentPhotos.forEach((src, idx) => {
            const thumb = $(`
                <div class="border rounded overflow-hidden ${idx === currentIdx ? 'border-primary border-2 shadow' : 'border-secondary'}" style="width: 52px; height: 52px; cursor: pointer;">
                    <img src="${src}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            `);
            thumb.on('click', function() {
                currentIdx = idx;
                updateGallery();
            });
            strip.append(thumb);
        });
    }

    $('#galleryPrevBtn').on('click', function() {
        if (currentPhotos.length <= 1) return;
        currentIdx = (currentIdx - 1 + currentPhotos.length) % currentPhotos.length;
        updateGallery();
    });

    $('#galleryNextBtn').on('click', function() {
        if (currentPhotos.length <= 1) return;
        currentIdx = (currentIdx + 1) % currentPhotos.length;
        updateGallery();
    });

    $(document).on('keydown', function(e) {
        if ($('#showVariantGalleryModal').hasClass('show')) {
            if (e.key === 'ArrowLeft') $('#galleryPrevBtn').click();
            if (e.key === 'ArrowRight') $('#galleryNextBtn').click();
        }
    });

    // Trigger gallery on clicking variant photos button
    $('.open-gallery-trigger').on('click', function() {
        const title = $(this).data('title');
        const rawPhotos = $(this).data('photos');
        let photos = [];
        try {
            photos = typeof rawPhotos === 'string' ? JSON.parse(rawPhotos) : rawPhotos;
        } catch (e) {
            photos = rawPhotos;
        }
        openGallery(title, photos, 0);
    });

    // Article Main Photo Zoom
    $('#mainProductPhotoFrame').on('click', function() {
        const src = $(this).data('src');
        if (src) {
            openGallery('{{ $product->item_name }} - Article Image', [src], 0);
        }
    });

    // Filter & Search Logic
    let activeColor = 'all';

    function filterTable() {
        const search = ($('#variantSearchInput').val() || '').toLowerCase().trim();
        let visibleCount = 0;
        let totalCount = $('.variant-data-row').length;

        $('.variant-data-row').each(function() {
            const rowColor = $(this).data('color');
            const searchData = $(this).data('search') || '';

            const hasDiscount = $(this).data('has-discount') == 1;

            let matchesColor = false;
            if (activeColor === 'all') {
                matchesColor = true;
            } else if (activeColor === 'discounted') {
                matchesColor = hasDiscount;
            } else {
                matchesColor = (rowColor === activeColor);
            }
            let matchesSearch = (search === '' || searchData.includes(search));

            if (matchesColor && matchesSearch) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        $('#variantShowingCounter').text(`Showing ${visibleCount} of ${totalCount} variants`);
    }

    $('.color-filter-btn').on('click', function() {
        $('.color-filter-btn').removeClass('active');
        $(this).addClass('active');
        activeColor = $(this).data('color');
        filterTable();
    });

    $('.filter-discount-trigger').on('click', function() {
        $('.color-filter-btn[data-color="discounted"]').click();
        $('html, body').animate({
            scrollTop: $("#showVariantsTable").offset().top - 120
        }, 300);
    });

    $('#variantSearchInput').on('input', function() {
        filterTable();
    });
});
</script>
@endsection
