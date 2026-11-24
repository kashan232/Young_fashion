@extends('admin_panel.layout.app')

@section('content')

<style>
    /* Premium ERP Styling for Zones */
    .zone-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: #1e293b;
    }

    /* Page Header */
    .zone-page-header {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .zone-page-title {
        font-weight: 800;
        font-size: 22px;
        color: #0f172a;
        margin: 0 0 4px 0;
        letter-spacing: -0.3px;
    }
    .zone-page-sub {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    /* Action Buttons */
    .btn-action-primary {
        background: #2563eb !important;
        border: 1px solid #1d4ed8 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding: 9px 18px !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin-right: 10px !important;
        margin-bottom: 6px !important;
        text-decoration: none !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
    }
    .btn-action-primary:hover {
        background: #1d4ed8 !important;
        transform: translateY(-1px);
        color: #ffffff !important;
    }

    .btn-action-secondary {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #334155 !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding: 9px 16px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin-right: 10px !important;
        margin-bottom: 6px !important;
        text-decoration: none !important;
        transition: all 0.15s ease-in-out !important;
    }
    .btn-action-secondary:hover {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
    }

    /* Stat Cards */
    .zone-stat-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .zone-stat-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #cbd5e1;
    }
    .zone-stat-box.border-primary::before { background: #2563eb; }
    .zone-stat-box.border-success::before { background: #16a34a; }
    .zone-stat-box.border-warning::before { background: #f59e0b; }

    .zone-stat-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 6px;
    }
    .zone-stat-number {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    /* Main Table Card */
    .zone-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .zone-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Modern Table */
    .zone-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .zone-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #cbd5e1;
        padding: 12px 16px;
        white-space: nowrap;
    }
    .zone-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .zone-table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Badges */
    .badge-id {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
        font-weight: 700;
        font-size: 11px;
        font-family: monospace;
        padding: 3px 8px;
        border-radius: 4px;
    }

    .badge-cust-count {
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* Action Buttons in Table */
    .btn-tbl-action {
        padding: 5px 11px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
        margin-right: 6px !important;
        margin-bottom: 4px !important;
        text-decoration: none !important;
    }

    /* Modal Form */
    .zone-modal .modal-content {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    .zone-modal .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .zone-modal .modal-body {
        padding: 20px;
    }
    .zone-modal .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 14px 20px;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .modal-header .close {
        background: none;
        border: none;
        font-size: 26px;
        line-height: 1;
        color: #64748b;
        cursor: pointer;
        padding: 0;
        margin: -4px 0 0 0;
    }
    .modal-header .close:hover {
        color: #0f172a;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid zone-container">

            {{-- ── 1. Page Header ── --}}
            <div class="zone-page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h1 class="zone-page-title"><i class="fas fa-map-marked-alt text-primary mr-2"></i>Delivery &amp; Sales Zones</h1>
                    <p class="zone-page-sub">Manage territory sectors, delivery dispatch areas, and customer regional allocations</p>
                </div>

                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    @can('zones.create')
                        <button type="button" class="btn-action-primary mr-2 mb-2" id="btnCreateZone" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> + Add Zone
                        </button>
                    @endcan
                    <a href="{{ route('customers.index') }}" class="btn-action-secondary mr-2 mb-2">
                        <i class="fas fa-users text-primary"></i> Customers Directory
                    </a>
                    <a href="{{ route('sales.officer.index') }}" class="btn-action-secondary mb-2">
                        <i class="fas fa-user-tie text-secondary"></i> Sales Officers
                    </a>
                </div>
            </div>

            {{-- ── 2. Top Summary Stat Cards ── --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-4">
                    <div class="zone-stat-box border-primary">
                        <div class="zone-stat-label">Total Distribution Zones</div>
                        <div class="zone-stat-number text-primary">{{ $totalZones }}</div>
                        <div class="small text-muted mt-1">Configured delivery territories</div>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <div class="zone-stat-box border-success">
                        <div class="zone-stat-label">Assigned Customers</div>
                        <div class="zone-stat-number text-success">{{ $totalMappedCustomers }}</div>
                        <div class="small text-muted mt-1">Customers mapped to specific zones</div>
                    </div>
                </div>

                <div class="col-12 col-sm-4">
                    <div class="zone-stat-box border-warning">
                        <div class="zone-stat-label">Top Territory</div>
                        <div class="zone-stat-number text-warning" style="font-size: 20px;">{{ $topZoneName }}</div>
                        <div class="small text-muted mt-1">Territory with largest customer base</div>
                    </div>
                </div>
            </div>

            {{-- ── 3. Main Data Card ── --}}
            <div class="zone-card">
                <div class="zone-card-header">
                    <div class="font-weight-bold text-dark">
                        <i class="fas fa-list mr-1 text-muted"></i> All Configured Zones
                    </div>
                    <div class="text-muted small">Showing {{ $zones->count() }} territories</div>
                </div>

                <div class="table-responsive">
                    <table class="table zone-table datanew" id="zoneDatatable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">ID</th>
                                <th style="min-width: 240px;">Zone / Territory Name</th>
                                <th style="min-width: 160px;">Linked Customers</th>
                                <th style="min-width: 140px;">Created Date</th>
                                <th class="text-right" style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($zones as $zone)
                                <tr id="row-{{ $zone->id }}">
                                    {{-- ID --}}
                                    <td class="text-center">
                                        <span class="badge-id">#ZN-{{ str_pad($zone->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    </td>

                                    {{-- Zone Name --}}
                                    <td>
                                        <div class="font-weight-bold text-dark" style="font-size: 14.5px;">
                                            <i class="fas fa-map-marker-alt text-danger mr-2"></i>{{ $zone->zone }}
                                        </div>
                                    </td>

                                    {{-- Linked Customers --}}
                                    <td>
                                        @if(($zone->customer_count ?? 0) > 0)
                                            <a href="{{ route('customers.index', ['search' => $zone->zone]) }}" class="badge-cust-count" title="Filter customers in this zone">
                                                <i class="fas fa-users"></i> {{ $zone->customer_count }} Customers
                                            </a>
                                        @else
                                            <span class="text-muted small font-weight-semibold">
                                                <i class="fas fa-user-slash text-muted mr-1"></i>0 Customers
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Date --}}
                                    <td>
                                        <span class="small font-weight-semibold text-muted">
                                            {{ $zone->created_at ? $zone->created_at->format('d-M-Y') : '—' }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-right text-nowrap">
                                        @can('zones.edit')
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-tbl-action" onclick="openEditModal({{ $zone->id }}, '{{ addslashes($zone->zone) }}')" title="Edit Zone">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        @endcan

                                        <a href="{{ route('customers.index', ['search' => $zone->zone]) }}" class="btn btn-sm btn-outline-info btn-tbl-action" title="View Customers">
                                            <i class="fas fa-users"></i>
                                        </a>

                                        @can('zones.delete')
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-tbl-action" onclick="confirmDeleteZone({{ $zone->id }}, '{{ addslashes($zone->zone) }}')" title="Delete Zone">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <div class="mb-2"><i class="fas fa-map-marked-alt fa-3x text-muted" style="opacity: 0.3;"></i></div>
                                        <div class="font-weight-bold" style="font-size: 16px;">No Zones Configured Yet</div>
                                        <div class="small text-muted mt-1">Click "+ Add Zone" above to add your first delivery sector or market territory.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── 4. Unified Zone Modal (Create & Edit) ── --}}
<div class="modal fade zone-modal" id="zoneModal" tabindex="-1" role="dialog" aria-labelledby="zoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="zoneForm" action="{{ route('zone.store') }}" method="POST">
            @csrf
            <input type="hidden" name="edit_id" id="modal_edit_id" value="">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title font-weight-bold text-dark mb-0" id="zoneModalLabel">
                        <i class="fas fa-map-marker-alt text-primary mr-2"></i><span id="zoneModalTitleText">Add New Zone</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeZoneModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Zone / Sector Name <span class="text-danger">*</span></label>
                        <input type="text" name="zone" id="modal_zone_input" class="form-control form-control-lg font-weight-bold" placeholder="e.g. Saddar, Tariq Road, Lahore Central, Clifton" required autofocus>
                        <small class="text-muted mt-1 d-block">Used for grouping customer orders, delivery runs, and sales routes.</small>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeZoneModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4" id="btnSaveZone">
                        <i class="fas fa-save mr-1"></i> <span id="btnSaveZoneText">Save Zone</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openCreateModal() {
        $('#modal_edit_id').val('');
        $('#modal_zone_input').val('');
        $('#zoneModalTitleText').text('Add New Zone');
        $('#btnSaveZoneText').text('Save Zone');
        $('#zoneModal').modal('show');
        setTimeout(() => $('#modal_zone_input').focus(), 300);
    }

    function openEditModal(id, currentName) {
        $('#modal_edit_id').val(id);
        $('#modal_zone_input').val(currentName);
        $('#zoneModalTitleText').text('Edit Zone');
        $('#btnSaveZoneText').text('Update Zone');
        $('#zoneModal').modal('show');
        setTimeout(() => $('#modal_zone_input').focus(), 300);
    }

    function closeZoneModal() {
        $('#zoneModal').modal('hide');
    }

    // Handle AJAX Form Submit
    $('#zoneForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const submitBtn = $('#btnSaveZone');
        const origText = $('#btnSaveZoneText').text();

        submitBtn.prop('disabled', true);
        $('#btnSaveZoneText').text('Saving...');

        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                closeZoneModal();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message || 'Zone saved successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    location.reload();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false);
                $('#btnSaveZoneText').text(origText);
                let msg = 'Failed to save zone. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', msg, 'error');
                } else {
                    alert(msg);
                }
            }
        });
    });

    // Handle Delete Confirmation
    function confirmDeleteZone(id, zoneName) {
        const deleteUrl = "{{ url('zones/delete') }}/" + id;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Zone?',
                text: 'Are you sure you want to delete zone "' + zoneName + '"? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'GET',
                        success: function(res) {
                            $('#row-' + id).fadeOut(300, function() {
                                $(this).remove();
                            });
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Zone "' + zoneName + '" has been deleted.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire('Error', 'Could not delete zone. Please try again.', 'error');
                        }
                    });
                }
            });
        } else {
            if (confirm('Are you sure you want to delete zone "' + zoneName + '"?')) {
                window.location.href = deleteUrl;
            }
        }
    }

    $(document).ready(function() {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#zoneDatatable')) {
            $('#zoneDatatable').DataTable({
                pageLength: 15,
                ordering: true,
                order: [[0, 'desc']]
            });
        }
    });
</script>
@endpush
