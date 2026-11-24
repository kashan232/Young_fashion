@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    .vcat-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        padding-bottom: 30px;
    }

    .vcat-header-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .vcat-title {
        font-weight: 800;
        font-size: 1.55rem;
        color: #0f172a;
        margin-bottom: 4px;
        letter-spacing: -0.02em;
    }

    .vcat-subtitle {
        font-size: 0.88rem;
        color: #64748b;
        margin-bottom: 0;
    }

    .btn-create-vcat {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        font-weight: 600;
        font-size: 0.88rem;
        border-radius: 10px;
        padding: 10px 20px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .btn-create-vcat:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #ffffff;
    }

    .vcat-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        height: 100%;
    }

    .vcat-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .vcat-stat-icon.primary {
        background: #eef2ff;
        color: #4f46e5;
    }

    .vcat-stat-icon.success {
        background: #ecfdf5;
        color: #10b981;
    }

    .vcat-stat-icon.warning {
        background: #fffbeb;
        color: #f59e0b;
    }

    .vcat-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
    }

    .vcat-table-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
    }

    .vcat-table-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0;
    }

    .vcat-table {
        margin-bottom: 0;
    }

    .vcat-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding: 14px 20px;
    }

    .vcat-table tbody td {
        padding: 14px 20px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .vcat-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .vcat-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        color: #ffffff;
        font-size: 0.82rem;
        transition: transform 0.15s ease, filter 0.15s ease;
        margin-right: 4px;
    }

    .vcat-action-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.1);
        color: #ffffff;
    }

    .vcat-btn-edit {
        background: #f59e0b;
    }

    .vcat-btn-delete {
        background: #ef4444;
    }

    .vcat-modal .modal-content {
        border-radius: 16px;
        border: none;
        overflow: hidden;
    }

    .vcat-modal .form-control {
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        font-size: 0.88rem;
        padding: 10px 14px;
    }

    .vcat-modal .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }
</style>

<div class="vcat-page container-fluid py-4 px-md-4">
    {{-- Header Card --}}
    <div class="vcat-header-card d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="vcat-title">
                <i class="fas fa-tags text-primary mr-2"></i>Vendor Categories
            </h1>
            <p class="vcat-subtitle">
                Classify vendors (e.g. <strong>Loafer / Manufacturer</strong>, <strong>Sneakers / Supplier</strong>) for efficient procurement and reporting.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('vendor') }}" class="btn btn-outline-secondary font-weight-bold mr-2" style="border-radius: 10px; height: 42px; display: inline-flex; align-items: center;">
                <i class="fas fa-truck mr-1"></i> Back to Vendors
            </a>
            @canany(['vendors.create', 'vendors.edit'])
                <button type="button" class="btn-create-vcat" id="btnOpenCreateCat">
                    <i class="fas fa-plus"></i> New Vendor Category
                </button>
            @endcanany
        </div>
    </div>

    {{-- Stats Row --}}
    @php
        $totalCats = $categories->count();
        $activeCats = $categories->where('status', 1)->count();
        $totalAssigned = $categories->sum('vendors_count');
    @endphp
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="vcat-stat-card">
                <div class="vcat-stat-icon primary">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <div class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Categories</div>
                    <div class="font-weight-bold text-dark" style="font-size: 1.45rem;">{{ $totalCats }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="vcat-stat-card">
                <div class="vcat-stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Active Categories</div>
                    <div class="font-weight-bold text-success" style="font-size: 1.45rem;">{{ $activeCats }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="vcat-stat-card">
                <div class="vcat-stat-icon warning">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Vendors Assigned</div>
                    <div class="font-weight-bold text-warning" style="font-size: 1.45rem;">{{ $totalAssigned }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="vcat-table-card">
        <div class="vcat-table-header">
            <h5 class="vcat-table-title">Vendor Category List</h5>
            <span class="badge badge-light border text-muted px-2 py-1">{{ $totalCats }} Total</span>
        </div>
        <div class="table-responsive">
            <table class="table vcat-table datatable-categories">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th class="text-center" style="width: 160px;">Assigned Vendors</th>
                        <th class="text-center" style="width: 120px;">Status</th>
                        <th class="text-right pr-4" style="width: 120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $index => $cat)
                        <tr>
                            <td class="text-center font-weight-bold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-light border px-2 py-1 font-weight-bold text-dark mr-2" style="font-size: 0.88rem;">
                                        <i class="fas fa-tag text-primary mr-1"></i> {{ $cat->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-muted" style="max-width: 300px;">
                                {{ $cat->description ?: '—' }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $cat->vendors_count > 0 ? 'badge-primary' : 'badge-light border text-muted' }} px-2 py-1 font-weight-bold">
                                    {{ $cat->vendors_count }} {{ Str::plural('vendor', $cat->vendors_count) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($cat->status == 1)
                                    <span class="badge badge-success px-2 py-1">Active</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Deactive</span>
                                @endif
                            </td>
                            <td class="text-right pr-4 text-nowrap">
                                @can('vendors.edit')
                                    <button type="button" class="vcat-action-btn vcat-btn-edit btn-edit-cat"
                                        data-id="{{ $cat->id }}"
                                        data-name="{{ $cat->name }}"
                                        data-description="{{ $cat->description }}"
                                        data-status="{{ $cat->status }}"
                                        title="Edit Category">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                @endcan

                                @can('vendors.delete')
                                    <button type="button" class="vcat-action-btn vcat-btn-delete"
                                        data-id="{{ $cat->id }}"
                                        data-url="{{ route('vendor_categories.delete', $cat->id) }}"
                                        data-msg="Are you sure you want to delete category '{{ $cat->name }}'?"
                                        onclick="logoutAndDeleteFunction(this)"
                                        title="Delete Category">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No vendor categories found. Click "+ New Vendor Category" to add one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add / Edit Category Modal --}}
<div class="modal fade vcat-modal" id="categoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <form action="{{ route('vendor_categories.store') }}" method="POST" id="categoryForm">
                @csrf
                <input type="hidden" name="id" id="cat_id">

                <div class="modal-header bg-white px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="modal-title font-weight-bold text-dark mb-0" id="catModalTitle">
                        <i class="fas fa-tags text-primary mr-2"></i>New Vendor Category
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.4rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="cat_name" class="font-weight-bold text-dark" style="font-size: 0.85rem;">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="name" id="cat_name"
                            placeholder="e.g. Loafer / Manufacturer, Sneakers / Supplier..." required>
                        <small class="text-muted">Enter a descriptive category name for your vendors.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="cat_status" class="font-weight-bold text-dark" style="font-size: 0.85rem;">Status</label>
                        <select class="form-control" name="status" id="cat_status">
                            <option value="1">Active</option>
                            <option value="0">Deactive</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label for="cat_description" class="font-weight-bold text-dark" style="font-size: 0.85rem;">Description</label>
                        <textarea class="form-control" name="description" id="cat_description" rows="3"
                            placeholder="Optional notes or details about this vendor category..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4 font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" style="border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none;">
                        <i class="fas fa-check mr-1"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('.datatable-categories')) {
            $('.datatable-categories').DataTable().destroy();
        }
        $('.datatable-categories').DataTable({
            "pageLength": 10,
            "order": [],
            "language": {
                "search": "",
                "searchPlaceholder": "Search categories..."
            }
        });

        // Open create modal
        $('#btnOpenCreateCat').click(function() {
            $('#categoryForm')[0].reset();
            $('#cat_id').val('');
            $('#cat_status').val('1');
            $('#catModalTitle').html('<i class="fas fa-tags text-primary mr-2"></i>New Vendor Category');
            $('#categoryModal').modal('show');
        });

        // Open edit modal
        $(document).on('click', '.btn-edit-cat', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const description = $(this).data('description');
            const status = $(this).data('status');

            $('#cat_id').val(id);
            $('#cat_name').val(name);
            $('#cat_description').val(description || '');
            $('#cat_status').val(status !== undefined && status !== '' ? status : '1');
            $('#catModalTitle').html('<i class="fas fa-edit text-primary mr-2"></i>Edit Vendor Category');
            $('#categoryModal').modal('show');
        });
    });
</script>
@endsection
