@extends('admin_panel.layout.app')
@section('content')

@if (session('success'))
    <script>
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

    .color-page {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        padding-bottom: 40px;
    }
    .color-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        border-radius: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .color-title {
        font-weight: 800;
        font-size: 1.35rem;
        color: #0f172a;
        margin-bottom: 2px;
        letter-spacing: -0.02em;
    }
    .color-sub {
        font-size: 0.82rem;
        color: #64748b;
    }
    .color-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .color-stat-icon {
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
    .btn-color-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        border: none;
        padding: 10px 20px;
        font-size: 0.86rem;
        font-weight: 600;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.22);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .color-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .color-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfc;
    }
    .color-id-badge {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.78rem;
    }
    .color-value-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eef2ff;
        color: #3730a3;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        border: 1px solid #c7d2fe;
    }
    .color-swatch-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 1px solid rgba(15, 23, 42, 0.25);
        display: inline-block;
    }
    #default-datatable thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 13px 16px;
        border-bottom: 1px solid #e2e8f0;
    }
    #default-datatable tbody td {
        padding: 13px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
</style>

<div class="container-fluid px-3 px-md-4 pt-3 color-page">

    <div class="color-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="color-title"><i class="fas fa-palette text-primary me-2"></i>Variants &amp; Color</div>
            <div class="color-sub">Manage reusable variant colors (e.g. Black, Brown, Red, Tan) for product creation</div>
        </div>
        @canany(['products.create', 'products.edit'])
            <button type="button" class="btn btn-color-primary" id="reset" data-toggle="modal" data-target="#colorModal">
                <i class="fas fa-plus"></i> Add Variant / Color
            </button>
        @endcanany
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="color-stat-card">
                <div class="color-stat-icon"><i class="fas fa-palette"></i></div>
                <div>
                    <div class="fw-bold fs-4 text-dark">{{ count($colors) }}</div>
                    <div class="text-muted small fw-semibold">Total Variants &amp; Colors</div>
                </div>
            </div>
        </div>
    </div>

    <div class="color-card">
        <div class="color-card-header">
            <div class="fw-bold text-dark"><i class="fas fa-list me-1 text-muted"></i> All Variants &amp; Colors</div>
            <div class="text-muted small">Showing {{ count($colors) }} entries</div>
        </div>

        <div class="table-responsive">
            <table id="default-datatable" class="table mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 90px;">Id</th>
                        <th class="text-start">Variant / Color Name</th>
                        <th class="text-start">Color Code / Hex</th>
                        <th class="text-end pe-4" style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($colors as $colorItem)
                        <tr>
                            <td class="text-center id"><span class="color-id-badge">#{{ $colorItem->id }}</span></td>
                            <td class="text-start name fw-semibold text-dark">
                                <span class="color-value-badge">
                                    @if(!empty($colorItem->color_code) && str_starts_with($colorItem->color_code, '#'))
                                        <span class="color-swatch-dot" style="background: {{ $colorItem->color_code }};"></span>
                                    @endif
                                    <span class="color-name-text">{{ $colorItem->name }}</span>
                                </span>
                            </td>
                            <td class="text-start code text-muted">
                                {{ $colorItem->color_code ?? '-' }}
                            </td>
                            <td class="text-end pe-4">
                                @include('admin_panel.partials.action_buttons', [
                                    'editRoute' => route('store.Color'),
                                    'deleteRoute' => route('delete.Color', $colorItem->id),
                                    'editIsLink' => false,
                                    'permissions' => [
                                        'edit' => 'products.edit',
                                        'delete' => 'products.delete',
                                    ],
                                    'dataId' => $colorItem->id,
                                ])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Add/Edit Variant & Color Modal --}}
<div class="modal fade" id="colorModal" tabindex="-1" aria-labelledby="colorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-white px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark" id="colorModalLabel"><i class="fas fa-palette text-primary me-2"></i><span id="modalTitleText">Add Variant / Color</span></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="myform" action="{{ route('store.Color') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="edit_id" id="id" />
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-dark small">Variant / Color Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control px-3 py-2" id="name" placeholder="e.g. Black, Brown, Red, Tan, Navy Blue" required style="border-radius: 10px; border: 1.5px solid #cbd5e1;" />
                    </div>
                    <div class="mb-3">
                        <label for="color_code" class="form-label fw-semibold text-dark small">Color Code / Hex (Optional)</label>
                        <input type="text" name="color_code" class="form-control px-3 py-2" id="color_code" placeholder="e.g. #000000 or BLK" style="border-radius: 10px; border: 1.5px solid #cbd5e1;" />
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-semibold" data-dismiss="modal" style="border-radius: 8px;">Close</button>
                    @canany(['products.create', 'products.edit'])
                        <button type="submit" class="btn btn-primary px-4 fw-bold save-btn" style="border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border: none;">
                            <i class="fas fa-check me-1"></i> Save Variant &amp; Color
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
        var tr = $(this).closest("tr");
        var id = tr.find(".id").text().replace('#', '').trim();
        var name = tr.find(".color-name-text").text().trim();
        var code = tr.find(".code").text().trim();
        if (code === '-') code = '';

        $('#id').val(id);
        $('#name').val(name);
        $('#color_code').val(code);
        $('#modalTitleText').text('Edit Variant & Color');
        $("#colorModal").modal("show");
    });

    $('#reset').on('click', function() {
        $('#id').val('');
        $('#name').val('');
        $('#color_code').val('');
        $('#modalTitleText').text('Add Variant / Color');
    });

    $(document).ready(function() {
        $('#default-datatable').DataTable({
            "pageLength": 15,
            "lengthMenu": [5, 10, 15, 25, 50, 100],
            "order": [[0, 'desc']],
            "language": {
                "search": "Search Color:",
                "lengthMenu": "Show _MENU_ entries"
            }
        });
    });
</script>
@endsection
