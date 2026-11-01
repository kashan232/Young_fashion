@extends('admin_panel.layout.app')

@section('content')
<style>
    .badge-size {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 700;
        padding: 3px 6px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        font-size: 0.72rem;
    }
    .badge-color {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 600;
        padding: 3px 6px;
        border-radius: 4px;
        border: 1px solid #bfdbfe;
        font-size: 0.72rem;
    }
    .barcode-chip {
        font-family: monospace;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 3px 6px;
        border-radius: 4px;
        font-size: 0.78rem;
        color: #334155;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-2">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span style="color:#4f46e5;"><i class="fas fa-tags"></i></span> Product &amp; Variant Discounts
                </h5>
                <small class="text-muted">Manage all active and scheduled discount offers for shoe articles and specific size/color variants</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('product') }}" class="btn btn-outline-secondary btn-sm fw-bold shadow-sm" style="border-radius: 6px;">
                    <i class="fas fa-boxes me-1"></i> Product Catalog
                </a>
                <a href="{{ route('discount.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm" style="background:#4f46e5; border-color:#4f46e5; border-radius: 6px;">
                    <i class="fas fa-plus me-1"></i> Create Discount
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

            <div class="table-responsive">
                <table class="table table-hover align-middle table-bordered" id="discountTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Article &amp; Variant Details</th>
                            <th style="width: 100px;">Size / Color</th>
                            <th style="width: 110px;">Barcode</th>
                            <th style="width: 110px;">Original Price</th>
                            <th style="width: 150px;">Discount Offer</th>
                            <th style="width: 110px;">Offer Price</th>
                            <th style="width: 100px;">Date</th>
                            <th style="width: 90px;">Status</th>
                            <th style="width: 130px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($discounts as $key => $discount)
                            @php
                                $product = $discount->product;
                            @endphp
                            <tr class="{{ $discount->status ? '' : 'table-light text-muted' }}">
                                <td class="text-center fw-bold text-muted">{{ $key + 1 }}</td>

                                {{-- Product & Variant Column --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($product && $product->image)
                                            <img src="{{ asset('uploads/products/'.$product->image) }}" 
                                                 width="42" height="42" class="rounded border object-fit-cover">
                                        @else
                                            <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted" style="width:42px; height:42px;">
                                                <i class="fas fa-shoe-prints"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong class="text-dark">{{ $product->item_name ?? 'Product' }}</strong>
                                            @if($discount->variant_name && $discount->variant_name !== ($product->item_name ?? ''))
                                                <div class="text-primary small fw-semibold">
                                                    <i class="fas fa-level-down-alt me-1"></i>{{ $discount->variant_name }}
                                                </div>
                                            @endif
                                            <div class="small text-muted">
                                                Code: {{ $product->item_code ?? '-' }} &bull; Brand: {{ $product->brand->name ?? 'Standard' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Size / Color --}}
                                <td>
                                    @if($discount->variant_size && $discount->variant_size !== '-')
                                        <span class="badge-size">Size: {{ $discount->variant_size }}</span>
                                    @endif
                                    @if($discount->variant_color && $discount->variant_color !== '-')
                                        <span class="badge-color mt-1 d-inline-block">{{ $discount->variant_color }}</span>
                                    @endif
                                    @if(!$discount->variant_size && !$discount->variant_color)
                                        <span class="badge bg-secondary-subtle text-secondary border" style="font-size:10px;">All Variants</span>
                                    @endif
                                </td>

                                {{-- Barcode --}}
                                <td>
                                    <span class="barcode-chip">
                                        <i class="fas fa-barcode me-1 text-muted"></i>{{ $discount->variant_barcode ?: ($product->item_code ?? '-') }}
                                    </span>
                                </td>

                                {{-- Original Price --}}
                                <td>
                                    <span class="text-muted text-decoration-line-through">
                                        Rs. {{ number_format($discount->actual_price, 2) }}
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
                                <td>
                                    <strong class="text-success fs-6">Rs. {{ number_format($discount->final_price, 2) }}</strong>
                                </td>

                                {{-- Date --}}
                                <td class="small">{{ \Carbon\Carbon::parse($discount->date)->format('d M Y') }}</td>

                                {{-- Status --}}
                                <td>
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
                                        <a href="{{ route('discount.barcode', $discount->id) }}" 
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary px-2 py-1" 
                                           title="Print Discount Barcode Label"
                                           style="border-radius: 6px; font-size: 11px;">
                                           <i class="fas fa-barcode"></i> Label
                                        </a>

                                        <form action="{{ route('discount.destroy', $discount->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this discount offer?');">
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
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-tags fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h5>No Discounts Found</h5>
                                    <p class="small text-muted mb-3">You have not created any discount rules for products or variants yet.</p>
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
