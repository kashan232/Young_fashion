@extends('admin_panel.layout.app')

@section('content')
    <style>
        .transfer-card {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .size-badge {
            font-size: 0.8rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-2">
        <div class="card transfer-card border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-exchange-alt me-2 text-primary"></i> Stock Transfer Audit Log</h5>
                    <p class="text-muted mb-0 small">History of all stock transfers between warehouses and retail shop</p>
                </div>
                @can('stock.transfer.create')
                    <a href="{{ route('stock_transfers.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                        <i class="fas fa-plus me-1"></i> New Transfer
                    </a>
                @endcan
            </div>

            @if (session('error'))
                <div class="alert alert-danger mx-4 mt-3 rounded-3">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success mx-4 mt-3 rounded-3">{{ session('success') }}</div>
            @endif

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="transferTable">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="small fw-bold">Date & Time</th>
                                <th class="small fw-bold">From Warehouse</th>
                                <th class="small fw-bold">Destination</th>
                                <th class="small fw-bold">Product</th>
                                <th class="small fw-bold">Size / Variant</th>
                                <th class="small fw-bold text-center">Transferred Qty</th>
                                <th class="small fw-bold">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transfers as $transfer)
                                <tr>
                                    <td class="small text-muted text-nowrap">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $transfer->created_at ? $transfer->created_at->format('d M, Y h:i A') : '--' }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            <i class="fas fa-warehouse text-danger me-1 small"></i>
                                            {{ $transfer->fromWarehouse->warehouse_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($transfer->to_shop)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="fas fa-store me-1"></i> Retail Shop
                                            </span>
                                        @else
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                                <i class="fas fa-warehouse me-1"></i>
                                                {{ $transfer->toWarehouse ? $transfer->toWarehouse->warehouse_name : '-' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $transfer->product->item_name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $transfer->product->item_code ?? '' }}</div>
                                    </td>
                                    <td>
                                        @if ($transfer->size && $transfer->size !== '-')
                                            <span class="badge bg-dark size-badge">Size {{ $transfer->size }}</span>
                                        @endif
                                        @if ($transfer->color && $transfer->color !== '-')
                                            <span class="badge bg-secondary-subtle text-dark border ms-1">{{ $transfer->color }}</span>
                                        @endif
                                        @if ((!$transfer->size || $transfer->size === '-') && (!$transfer->color || $transfer->color === '-'))
                                            <span class="text-muted small">Standard (No Size)</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-primary fs-6 text-nowrap">
                                        {{ number_format($transfer->quantity) }} Pcs
                                    </td>
                                    <td class="small text-muted">
                                        {{ $transfer->remarks ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-box-open fa-2x mb-2 d-block text-secondary opacity-50"></i>
                                        No stock transfers recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($transfers, 'links'))
                    <div class="mt-4">
                        {{ $transfers->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
