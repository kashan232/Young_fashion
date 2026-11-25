@extends('admin_panel.layout.app')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid">

                <div class="page-header row">
                    <div class="page-title col-lg-6">
                        <h4>Warehouse List</h4>
                        <h6>Manage Warehouses</h6>
                    </div>
                    <div class="page-btn d-flex justify-content-end col-lg-6">
                        @can('warehouse.create')
                            <button class="btn btn-outline-primary mb-2" id="btnAddWarehouse" data-toggle="modal" data-target="#warehouseModal" data-bs-toggle="modal" data-bs-target="#warehouseModal"
                                onclick="clearWarehouse()">Add Warehouse</button>
                        @endcan
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        {{-- @if (session()->has('success'))
                            <div class="alert alert-success"><strong>Success!</strong> {{ session('success') }}</div>
                        @endif --}}

                        <table class="table datanew">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Created By</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($warehouses as $key => $w)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $w->user->name ?? 'Admin' }}</td>
                                        <td>{{ $w->warehouse_name }}</td>
                                        <td>{{ $w->location }}</td>
                                        <td>{{ $w->remarks }}</td>
                                        <td>
                                            @can('warehouse.edit')
                                                <button class="btn btn-primary btn-sm edit-warehouse-btn"
                                                    data-id="{{ $w->id }}" data-name="{{ $w->warehouse_name }}"
                                                    data-location="{{ $w->location }}" data-remarks="{{ $w->remarks }}"
                                                    data-toggle="modal" data-target="#warehouseModal"
                                                    data-bs-toggle="modal" data-bs-target="#warehouseModal">
                                                    Edit
                                                </button>
                                            @endcan
                                            @can('warehouse.delete')
                                                <button class="btn btn-danger btn-sm delete-btn"
                                                    data-url="{{ url('warehouse/delete/' . $w->id) }}"
                                                    data-msg="Are you sure you want to delete this warehouse?" data-method="get"
                                                    onclick="logoutAndDeleteFunction(this)">
                                                    Delete
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="warehouseModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="{{ url('warehouse/store') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="warehouse_id">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold" id="warehouseModalTitle">Add Warehouse</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background:none; border:none; font-size:1.6rem; cursor:pointer;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Warehouse Name *</label>
                            <input class="form-control" name="warehouse_name" id="warehouse_name" placeholder="Enter warehouse name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Location / Address</label>
                            <input class="form-control" name="location" id="location" placeholder="e.g. Shaibazar, Hyderabad">
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-muted">Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                        @canany(['warehouse.create', 'warehouse.edit'])
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Save</button>
                        @endcanany
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
            });
        </script>
    @endif
    <script>
        function clearWarehouse() {
            $('#warehouse_id').val('');
            $('#warehouse_name').val('');
            $('#location').val('');
            $('#remarks').val('');
            $('#warehouseModalTitle').text('Add Warehouse');
        }

        $('#btnAddWarehouse').on('click', function(e) {
            e.preventDefault();
            clearWarehouse();
            $('#warehouseModal').modal('show');
        });

        // Handle Edit button click
        $(document).on('click', '.edit-warehouse-btn', function(e) {
            e.preventDefault();
            $('#warehouse_id').val($(this).data('id'));
            $('#warehouse_name').val($(this).data('name'));
            $('#location').val($(this).data('location'));
            $('#remarks').val($(this).data('remarks'));
            $('#warehouseModalTitle').text('Edit Warehouse');
            $('#warehouseModal').modal('show');
        });

        $('.datanew').DataTable();
    </script>
@endsection
