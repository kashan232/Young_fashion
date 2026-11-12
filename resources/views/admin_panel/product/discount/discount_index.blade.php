@extends('admin_panel.layout.app')

@section('content')
<style>
    .barcode-chip {
        font-family: monospace;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.78rem;
        color: #334155;
        display: inline-block;
    }
    .disc-summary-pill {
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 0.78rem;
        font-weight: 700;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-3">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span style="color:#4f46e5;"><i class="fas fa-tags"></i></span> Discount Products
                    <span class="disc-summary-pill">{{ $discounts->count() }} Products</span>
                </h5>
                <small class="text-muted">Manage active and scheduled product-level discount offers across your catalog</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('product') }}" class="btn btn-outline-secondary btn-sm fw-bold shadow-sm" style="border-radius: 6px;">
                    <i class="fas fa-boxes me-1"></i> Product Catalog
                </a>
                <a href="{{ route('discount.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm" style="background:#4f46e5; border-color:#4f46e5; border-radius: 6px;">
                    <i class="fas fa-plus me-1"></i> Create / Manage Discounts
                </a>
            </div>
        </div>

        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle text-success fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session()->has('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-circle text-warning fs-5"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($discounts->count() > 0)
            <div class="row g-2 mb-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="discountSearchInput" class="form-control" placeholder="Search product name, article code, brand, category...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="discountStatusFilter" class="form-select form-select-sm">
                        <option value="all">All Statuses</option>
                        <option value="1">Active Only</option>
                        <option value="0">Inactive Only</option>
                    </select>
                </div>
                <div class="col-md-4 text-md-end text-muted small">
                    Showing <strong id="visibleDiscountCount">{{ $discounts->count() }}</strong> of <strong>{{ $discounts->count() }}</strong> discounted product(s)
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle table-bordered mb-0" id="discountTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 45px;" class="text-center">#</th>
                            <th>Product Details</th>
                            <th style="width: 130px;">Article Code</th>
                            <th style="width: 120px;" class="text-end">Original Price</th>
                            <th style="width: 160px;">Discount Offer</th>
                            <th style="width: 125px;" class="text-end">Offer Price</th>
                            <th style="width: 105px;">Valid Date</th>
                            <th style="width: 95px;" class="text-center">Status</th>
                            <th style="width: 165px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($discounts as $key => $discount)
                            @php
                                $product = $discount->product;
                                $origPrice = (float) ($discount->actual_price ?: ($product->price ?? 0));
                                $searchBlob = strtolower(
                                    ($product->item_name ?? '') . ' ' .
                                    ($product->item_code ?? '') . ' ' .
                                    ($product->brand->name ?? '') . ' ' .
                                    ($product->category_relation->name ?? '')
                                );
                            @endphp
                            <tr class="discount-prod-row {{ $discount->status ? '' : 'table-light text-muted' }}"
                                data-search="{{ $searchBlob }}"
                                data-status="{{ $discount->status ? '1' : '0' }}">
                                <td class="text-center fw-bold text-muted">{{ $key + 1 }}</td>

                                {{-- Product Column --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($product && $product->image)
                                            <img src="{{ asset('uploads/products/'.$product->image) }}"
                                                 width="42" height="42" class="rounded border object-fit-cover flex-shrink-0">
                                        @else
                                            <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted flex-shrink-0" style="width:42px; height:42px;">
                                                <i class="fas fa-shoe-prints"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('products.show', $product->id) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $product->item_name ?? 'Product' }}
                                            </a>
                                            <div class="small text-muted">
                                                {{ $product->category_relation->name ?? 'General' }} &bull; {{ $product->brand->name ?? 'Standard' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Article Code --}}
                                <td>
                                    <span class="barcode-chip">
                                        <i class="fas fa-barcode me-1 text-muted"></i>{{ $product->item_code ?? '-' }}
                                    </span>
                                </td>

                                {{-- Original Price --}}
                                <td class="text-end">
                                    <span class="text-muted text-decoration-line-through">
                                        Rs. {{ number_format($origPrice, 2) }}
                                    </span>
                                </td>

                                {{-- Discount Column --}}
                                <td>
                                    @if($discount->discount_percentage > 0)
                                        <span class="badge text-white fw-bold px-2 py-1" style="background: #4f46e5; font-size: 12px;">
                                            {{ floatval($discount->discount_percentage) }}% Off
                                        </span>
                                    @elseif($discount->discount_amount > 0)
                                        <span class="badge bg-warning text-dark fw-bold px-2 py-1" style="font-size: 12px;">
                                            Flat Rs. {{ number_format($discount->discount_amount, 2) }} Off
                                        </span>
                                    @endif
                                    <div class="small text-danger fw-bold mt-1">
                                        Save: Rs. {{ number_format($discount->total_discount, 2) }}
                                    </div>
                                </td>

                                {{-- Final Offer Price --}}
                                <td class="text-end">
                                    <strong class="text-success fs-6">Rs. {{ number_format($discount->final_price, 2) }}</strong>
                                </td>

                                {{-- Date --}}
                                <td class="small">{{ \Carbon\Carbon::parse($discount->date)->format('d M Y') }}</td>

                                {{-- Status --}}
                                <td class="text-center">
                                    <form action="{{ route('discount.toggleStatus', $discount->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $discount->status ? 'btn-success' : 'btn-outline-secondary' }} px-2 py-1" style="font-size: 11px; border-radius: 6px;" title="Click to toggle status">
                                            {{ $discount->status ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>

                                {{-- Actions --}}
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('discount.create', ['products' => $product->id]) }}"
                                           class="btn btn-sm btn-outline-secondary px-2 py-1"
                                           title="Edit Product Discount"
                                           style="border-radius: 6px; font-size: 11px;">
                                           <i class="fas fa-pencil-alt"></i>
                                        </a>

                                        <a href="{{ route('discount.barcode', $discount->id) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary px-2 py-1"
                                           title="Print Discount Barcode Label"
                                           style="border-radius: 6px; font-size: 11px;">
                                           <i class="fas fa-barcode"></i> Label
                                        </a>

                                        <form action="{{ route('discount.destroy', $discount->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete the discount for this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" style="border-radius: 6px; font-size: 11px;" title="Delete Discount Offer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-tags fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h5>No Product Discounts Found</h5>
                                    <p class="small text-muted mb-3">You have not created any product discount rules yet.</p>
                                    <a href="{{ route('discount.create') }}" class="btn btn-primary btn-sm fw-bold">
                                        <i class="fas fa-plus me-1"></i> Create Your First Discount
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    function filterDiscountRows() {
        var q = ($('#discountSearchInput').val() || '').toLowerCase().trim();
        var st = $('#discountStatusFilter').val() || 'all';
        var visible = 0;

        $('#discountTable tbody tr.discount-prod-row').each(function() {
            var row = $(this);
            var text = (row.data('search') || '').toString();
            var status = (row.data('status') || '').toString();

            var matchSearch = !q || text.indexOf(q) !== -1;
            var matchStatus = (st === 'all') || (status === st);

            if (matchSearch && matchStatus) {
                row.show();
                visible++;
            } else {
                row.hide();
            }
        });

        $('#visibleDiscountCount').text(visible);
    }

    $('#discountSearchInput').on('input', filterDiscountRows);
    $('#discountStatusFilter').on('change', filterDiscountRows);
});
</script>
@endsection
