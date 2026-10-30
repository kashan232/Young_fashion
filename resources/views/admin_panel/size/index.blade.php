@extends('admin_panel.layout.app')
@section('content')

@if (session('success'))
    <script>
    $('.modal').on('hide.bs.modal', function () {
        if (document.activeElement) {
            document.activeElement.blur();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#4f46e5'
        });
    });
    </script>
@endif

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    .size-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        padding-bottom: 40px;
    }

    /* Page Header */
    .size-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        border-radius: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .size-title {
        font-weight: 800;
        font-size: 1.35rem;
        color: #0f172a;
        margin-bottom: 2px;
        letter-spacing: -0.02em;
    }
    .size-sub {
        font-size: 0.82rem;
        color: #64748b;
    }

    /* Stat Cards */
    .size-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }
    .size-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .size-stat-val {
        font-weight: 800;
        font-size: 1.25rem;
        color: #0f172a;
        line-height: 1.2;
    }
    .size-stat-lbl {
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Primary Gradient Button */
    .btn-size-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        border: none;
        padding: 10px 20px;
        font-size: 0.86rem;
        font-weight: 600;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.22);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-size-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
    }

    /* Card & Table */
    .size-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }
    .size-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .size-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    #default-datatable {
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    #default-datatable thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }
    #default-datatable tbody td {
        padding: 14px 20px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }
    #default-datatable tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Badges */
    .size-id-badge {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 3px 8px;
        border-radius: 6px;
        font-family: monospace;
    }
    .size-value-badge {
        background: #eef2ff;
        color: #4338ca;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 4px 12px;
        border-radius: 8px;
        display: inline-block;
        border: 1px solid #c7d2fe;
    }

    /* Mobile Cards View */
    .mobile-size-cards {
        display: none;
        padding: 14px;
        flex-direction: column;
        gap: 12px;
    }
    .size-mcard {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
    }
    .size-mcard-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .size-mcard-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #0f172a;
    }
    .size-mcard-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px dashed #e2e8f0;
    }

    /* Responsive Breakpoints */
    @media (max-width: 768px) {
        .size-card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            padding: 16px;
        }
        .btn-size-primary {
            width: 100%;
            justify-content: center;
            height: 42px;
        }
        .size-table-wrap {
            display: none !important;
        }
        .mobile-size-cards {
            display: flex;
        }
    }
</style>

<div class="size-page container-fluid px-3 px-md-4 pt-3">
    
    {{-- Header Row --}}
    <div class="size-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <h3 class="size-title"><i class="fas fa-ruler-combined text-primary me-2"></i>Shoe Size Management</h3>
            <div class="size-sub">Manage footwear sizes for all shoe categories (39, 40, 41, 42, 43, 44, 45...)</div>
        </div>
        @can('sizes.create')
            <button type="button" class="btn-size-primary" data-toggle="modal" data-target="#sizeModal" id="reset">
                <i class="fas fa-plus"></i> Create Size
            </button>
        @endcan
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="size-stat-card">
                <div class="size-stat-icon"><i class="fas fa-shoe-prints"></i></div>
                <div>
                    <div class="size-stat-val">{{ count($sizes) }}</div>
                    <div class="size-stat-lbl">Total Shoe Sizes</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="size-card">
        <div class="size-card-header">
            <div class="fw-bold text-dark"><i class="fas fa-list me-1 text-muted"></i> All Sizes</div>
            <div class="text-muted small">Showing {{ count($sizes) }} entries</div>
        </div>

        {{-- Desktop Table View --}}
        <div class="size-table-wrap">
            <table id="default-datatable" class="table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 90px;">Id</th>
                        <th class="text-start">Size Value / Name</th>
                        <th class="text-start">Code / Label</th>
                        <th class="text-end pe-4" style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sizes as $sizeItem)
                        <tr>
                            <td class="text-center id"><span class="size-id-badge">#{{ $sizeItem->id }}</span></td>
                            <td class="text-start name fw-semibold text-dark">
                                <span class="size-value-badge">{{ $sizeItem->name }}</span>
                            </td>
                            <td class="text-start code text-muted">
                                {{ $sizeItem->size_code ?? '-' }}
                            </td>
                            <td class="text-end pe-4">
                                @include('admin_panel.partials.action_buttons', [
                                    'editRoute' => route('store.Size'),
                                    'deleteRoute' => route('delete.Size', $sizeItem->id),
                                    'editIsLink' => false,
                                    'permissions' => [
                                        'edit' => 'sizes.edit',
                                        'delete' => 'sizes.delete',
                                    ],
                                    'dataId' => $sizeItem->id,
                                ])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards View (< 768px) --}}
        <div class="mobile-size-cards">
            @foreach ($sizes as $sizeItem)
                <div class="size-mcard">
                    <div class="size-mcard-hdr">
                        <span class="size-id-badge">#{{ $sizeItem->id }}</span>
                        <span class="badge bg-light text-dark border">Footwear Size</span>
                    </div>
                    <div class="size-mcard-title mb-1 name">
                        <span class="size-value-badge">{{ $sizeItem->name }}</span>
                    </div>
                    @if($sizeItem->size_code)
                        <div class="text-muted small code mb-2">Code: {{ $sizeItem->size_code }}</div>
                    @endif
                    <div class="size-mcard-actions">
                        @include('admin_panel.partials.action_buttons', [
                            'editRoute' => route('store.Size'),
                            'deleteRoute' => route('delete.Size', $sizeItem->id),
                            'editIsLink' => false,
                            'permissions' => [
                                'edit' => 'sizes.edit',
                                'delete' => 'sizes.delete',
                            ],
                            'dataId' => $sizeItem->id,
                        ])
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Add/Edit Size Modal --}}
<div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-white px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark" id="sizeModalLabel"><i class="fas fa-ruler-combined text-primary me-2"></i><span id="modalTitleText">Add Shoe Size</span></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="myform" action="{{ route('store.Size') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="edit_id" id="id" />
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-dark small">Size Name / Value <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control px-3 py-2" id="name" placeholder="e.g. 40, 41, 42, 6 UK, 7 US" required style="border-radius: 10px; border: 1.5px solid #cbd5e1;" />
                        <small class="text-muted">Enter size value, e.g. 39, 40, 41, 42, 43, 44, 45, etc.</small>
                    </div>
                    <div class="mb-3">
                        <label for="size_code" class="form-label fw-semibold text-dark small">Size Code / Label (Optional)</label>
                        <input type="text" name="size_code" class="form-control px-3 py-2" id="size_code" placeholder="e.g. EU-40, UK-7" style="border-radius: 10px; border: 1.5px solid #cbd5e1;" />
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-semibold" data-dismiss="modal" style="border-radius: 8px;">Close</button>
                    @canany(['sizes.create', 'sizes.edit'])
                        <button type="submit" class="btn btn-primary px-4 fw-bold save-btn" style="border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none;">
                            <i class="fas fa-check me-1"></i> Save Size
                        </button>
                    @endcanany
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // Fix ARIA focus warning on modal close
    $('.modal').on('hide.bs.modal', function () {
        if (document.activeElement) {
            document.activeElement.blur();
        }
    });

    $(document).on('submit', '.myform', function(e) {
        e.preventDefault();
        var formdata = new FormData(this);
        var url = $(this).attr('action');
        var method = $(this).attr('method');
        $(this).find(':submit').attr('disabled', true);
        myAjax(url, formdata, method);
    });

    $(document).on('click', '.edit-btn', function() {
        var tr = $(this).closest("tr, .size-mcard");
        var id = tr.find(".id, .size-id-badge").text().replace('#', '').trim();
        var name = tr.find(".name, .size-value-badge").text().trim();
        var code = tr.find(".code").text().replace('Code:', '').trim();
        if (code === '-') code = '';
        
        $('#id').val(id);
        $('#name').val(name);
        $('#size_code').val(code);
        $('#modalTitleText').text('Edit Shoe Size');
        $("#sizeModal").modal("show");
    });

    $('#reset').on('click', function() {
        $('#id').val('');
        $('#name').val('');
        $('#size_code').val('');
        $('#modalTitleText').text('Add Shoe Size');
    });

    $(document).ready(function() {
        $('#default-datatable').DataTable({
            "pageLength": 15,
            "lengthMenu": [5, 10, 15, 25, 50, 100],
            "order": [[0, 'desc']],
            "language": {
                "search": "Search Size:",
                "lengthMenu": "Show _MENU_ entries"
            }
        });
    });
</script>
@endsection
