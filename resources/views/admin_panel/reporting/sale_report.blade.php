@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Compact Sale Report Terminal Styling */
    .sale-report-container {
        padding: 10px 14px;
        background: #f1f5f9;
        min-height: calc(100vh - 75px);
    }

    /* Filter Form Spacing for Bootstrap 4 */
    .sale-filter-form {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px !important;
    }
    .sale-filter-group {
        display: flex;
        align-items: center;
        margin-right: 14px;
    }
    .sale-filter-label {
        margin-right: 8px !important;
        margin-bottom: 0 !important;
        white-space: nowrap;
        font-weight: 700;
        font-size: .78rem;
        color: #475569;
    }
    
    /* Single Horizontal Line Summary Metrics Bar */
    .summary-pill-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .stat-pill {
        flex: 1 1 0px;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .stat-pill .stat-label {
        font-size: .60rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 1px;
    }
    .stat-pill .stat-val {
        font-size: .88rem;
        font-weight: 800;
        line-height: 1.2;
    }

    /* Mobile Cards Styling */
    .mob-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .mob-metric-card {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 10px 6px;
        text-align: center;
        height: 100%;
    }
    .mob-metric-label {
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
    }
    .mob-metric-val {
        font-size: 14px;
        font-weight: 800;
    }

    /* Scrollable Table Wrapper with Sticky Header */
    .sale-table-wrap {
        height: calc(100vh - 250px);
        max-height: calc(100vh - 250px);
        min-height: 380px;
        overflow-y: auto;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
    }
    
    .sale-table-wrap::-webkit-scrollbar { width: 10px; height: 10px; }
    .sale-table-wrap::-webkit-scrollbar-track { background: #f1f5f9; }
    .sale-table-wrap::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 5px; }
    .sale-table-wrap::-webkit-scrollbar-thumb:hover { background: #64748b; }

    #saleReport {
        font-size: .78rem;
        margin-bottom: 0;
    }

    #saleReport thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #1e293b !important;
        color: #ffffff !important;
        font-size: .75rem;
        font-weight: 700;
        padding: 9px 10px;
        border-bottom: 2px solid #334155;
        white-space: nowrap;
    }

    /* Compact Returns Column */
    #saleReport th:nth-child(11),
    #saleReport td:nth-child(11) {
        max-width: 135px !important;
        width: 135px !important;
        font-size: .72rem;
        word-break: break-word;
        white-space: normal;
    }

    @media print {
        body { background: #ffffff !important; font-size: 11px; }
        .no-print, header, .sidebar, .navbar, footer { display: none !important; }
        .sale-report-container { padding: 0 !important; background: #fff !important; }
        .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; margin-bottom: 10px !important; }
        .sale-table-wrap { height: auto !important; max-height: none !important; overflow: visible !important; border: none !important; }
        #saleReport th, #saleReport td { border: 1px solid #cbd5e1 !important; }
    }
</style>

<div class="sale-report-container">
    
    {{-- DESKTOP FILTER HEADER BAR (d-none d-md-block) --}}
    <div class="card border-0 shadow-sm mb-2 d-none d-md-block no-print" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <form id="SaleFilterFormDesk" class="sale-filter-form">
                
                {{-- Title Badge --}}
                <div class="sale-filter-group" style="margin-right: 12px;">
                    <span class="fw-bold text-dark fs-6" style="letter-spacing: -0.2px; font-weight:700; white-space:nowrap;">
                        <i class="fas fa-chart-line text-primary" style="margin-right: 8px;"></i>Sale Report
                    </span>
                </div>

                {{-- Quick Date Preset --}}
                <div class="sale-filter-group" style="margin-right: 10px;">
                    <select id="quick_date_desk" class="form-select form-select-sm fw-bold quickDateInput" style="height: 36px; width: 125px; font-size: .78rem; border-radius: 6px;">
                        <option value="this_month" selected>This Month</option>
                        <option value="today">Today (Aaj)</option>
                        <option value="yesterday">Yesterday (Kal)</option>
                        <option value="last_7">Last 7 Days</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                {{-- Start Date --}}
                <div class="sale-filter-group" style="margin-right: 10px;">
                    <label for="start_date_desk" class="sale-filter-label">Start:</label>
                    <input type="date" name="start_date" id="start_date_desk" class="form-control form-control-sm fw-bold startDateInput" value="{{ date('Y-m-01') }}" style="height: 36px; width: 130px; font-size: .78rem; border-radius: 6px;">
                </div>

                {{-- End Date --}}
                <div class="sale-filter-group" style="margin-right: 10px;">
                    <label for="end_date_desk" class="sale-filter-label">End:</label>
                    <input type="date" name="end_date" id="end_date_desk" class="form-control form-control-sm fw-bold endDateInput" value="{{ date('Y-m-d') }}" style="height: 36px; width: 130px; font-size: .78rem; border-radius: 6px;">
                </div>

                {{-- Cashier / User --}}
                <div class="sale-filter-group" style="margin-right: 10px;">
                    <label for="user_id_desk" class="sale-filter-label">Cashier:</label>
                    <select name="user_id" id="user_id_desk" class="form-select form-select-sm fw-bold userIdInput" style="height: 36px; width: 125px; font-size: .78rem; border-radius: 6px;">
                        <option value="">All Cashiers</option>
                        @if(isset($users))
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Search Input --}}
                <div class="flex-grow-1" style="min-width: 170px; margin-right: 10px;">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; pointer-events: none;"></i>
                        <input type="text" class="form-control form-control-sm searchProductInput" placeholder="Search Product / Invoice / Customer…" style="height: 36px; font-size: .80rem; border-radius: 6px; padding-left: 34px;">
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <button type="button" class="btn btn-primary btn-sm px-2.5 px-2 fw-bold d-inline-flex align-items-center btnSearchTrigger" style="height: 36px; border-radius: 6px; font-size: .78rem;">
                        <i class="fas fa-filter me-1"></i> Search
                    </button>
                    <button type="button" class="btn btn-light border btn-sm px-2.5 px-2 fw-bold text-secondary d-inline-flex align-items-center btnResetTrigger" style="height: 36px; border-radius: 6px; font-size: .78rem;">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm px-2.5 px-2 fw-bold d-inline-flex align-items-center btnExportCsvTrigger" style="height: 36px; border-radius: 6px; font-size: .78rem;">
                        <i class="fas fa-file-csv me-1"></i> CSV
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-2.5 px-2 fw-bold d-inline-flex align-items-center btnPrintReport" style="height: 36px; border-radius: 6px; font-size: .78rem;">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MOBILE FILTER HEADER BAR (d-md-none With Top Margin) --}}
    <div class="card border-0 shadow-sm mb-3 no-print d-md-none mt-2" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form id="SaleFilterFormMob">
                <div class="row g-2">
                    <div class="col-12 mb-1">
                        <span class="fw-bold text-dark fs-6">
                            <i class="fas fa-chart-line text-primary me-2"></i>Sale Report
                        </span>
                    </div>

                    {{-- Quick Preset --}}
                    <div class="col-12 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Quick Preset</label>
                        <select id="quick_date_mob" class="form-select form-select-sm quickDateInput" style="font-size: 11px;">
                            <option value="this_month" selected>This Month</option>
                            <option value="today">Today (Aaj)</option>
                            <option value="yesterday">Yesterday (Kal)</option>
                            <option value="last_7">Last 7 Days</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Start Date</label>
                        <input type="date" name="start_date" id="start_date_mob" class="form-control form-control-sm startDateInput" value="{{ date('Y-m-01') }}" style="font-size: 11px;">
                    </div>
                    <div class="col-6 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">End Date</label>
                        <input type="date" name="end_date" id="end_date_mob" class="form-control form-control-sm endDateInput" value="{{ date('Y-m-d') }}" style="font-size: 11px;">
                    </div>
                    <div class="col-12 mb-1">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Cashier</label>
                        <select name="user_id" id="user_id_mob" class="form-select form-select-sm userIdInput" style="font-size: 11px;">
                            <option value="">All Cashiers</option>
                            @if(isset($users))
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="form-label mb-1 fw-bold text-secondary" style="font-size: 11px;">Search</label>
                        <input type="text" class="form-control form-control-sm searchProductInput" placeholder="Search Product / Invoice / Customer…" style="font-size: 11px;">
                    </div>
                    <div class="col-12 mb-1">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm btnSearchTrigger" style="font-size: 13px;">
                            <i class="fas fa-filter me-1"></i> Search
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-light border btn-sm flex-fill fw-bold text-secondary btnResetTrigger" style="font-size: 11px;">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm flex-fill fw-bold btnExportCsvTrigger" style="font-size: 11px;">
                                <i class="fas fa-file-csv me-1"></i> CSV
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm flex-fill fw-bold btnPrintReport" style="font-size: 11px;">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- DESKTOP SUMMARY PILL METRICS BAR (d-none d-md-block) --}}
    <div class="card border-0 shadow-sm mb-2 d-none d-md-block" style="border-radius: 10px; background: #ffffff;">
        <div class="card-body p-2">
            <div class="summary-pill-bar custom-scroll">
                
                <div class="stat-pill" style="background: #f8fafc; border-color: #cbd5e1;">
                    <div class="stat-label text-muted">Invoices</div>
                    <div class="stat-val text-dark" id="pillTotalInvoices">0</div>
                </div>

                <div class="stat-pill" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="stat-label text-info">Total Qty</div>
                    <div class="stat-val text-info" id="pillTotalQty">0 Pcs</div>
                </div>

                <div class="stat-pill" style="background: #f8fafc; border-color: #cbd5e1;">
                    <div class="stat-label text-secondary">Gross Sale</div>
                    <div class="stat-val text-secondary" id="pillGrossSale">Rs 0</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger">Returns</div>
                    <div class="stat-val text-danger" id="pillTotalReturns">Rs 0</div>
                </div>

                <div class="stat-pill" style="background: #f0fdf4; border-color: #86efac;">
                    <div class="stat-label text-success">Net Sale</div>
                    <div class="stat-val text-success" id="pillNetSale">Rs 0</div>
                </div>

                <div class="stat-pill" style="background: #eff6ff; border-color: #93c5fd;">
                    <div class="stat-label text-primary">Total Profit</div>
                    <div class="stat-val text-primary" id="pillGrossProfit">Rs 0</div>
                </div>

                <div class="stat-pill" style="background: #fffbeb; border-color: #fde047;">
                    <div class="stat-label" style="color: #b45309;">Expense</div>
                    <div class="stat-val" style="color: #d97706;" id="pillExpenses">Rs 0</div>
                </div>

                <div class="stat-pill" style="background: #ecfdf5; border-color: #34d399;">
                    <div class="stat-label" style="color: #047857;">Current Profit</div>
                    <div class="stat-val" style="color: #059669;" id="pillCurrentProfit">Rs 0</div>
                </div>

            </div>
        </div>
    </div>

    {{-- MOBILE SUMMARY METRIC GRID (2 Columns col-6 d-md-none) --}}
    <div class="row g-2 mb-3 d-md-none no-print px-1">
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-file-invoice text-muted me-1"></i>Invoices</span>
                <div class="mob-metric-val text-dark mt-1" id="mobPillTotalInvoices">0</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-box text-info me-1"></i>Total Qty</span>
                <div class="mob-metric-val text-info mt-1" id="mobPillTotalQty">0 Pcs</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-wallet text-secondary me-1"></i>Gross Sale</span>
                <div class="mob-metric-val text-secondary mt-1" id="mobPillGrossSale">Rs 0</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-undo text-danger me-1"></i>Returns</span>
                <div class="mob-metric-val text-danger mt-1" id="mobPillTotalReturns">Rs 0</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-check-circle text-success me-1"></i>Net Sale</span>
                <div class="mob-metric-val text-success mt-1" id="mobPillNetSale">Rs 0</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-chart-line text-primary me-1"></i>Total Profit</span>
                <div class="mob-metric-val text-primary mt-1" id="mobPillGrossProfit">Rs 0</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-minus-circle text-warning me-1"></i>Expense</span>
                <div class="mob-metric-val text-warning mt-1" id="mobPillExpenses">Rs 0</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-coins text-success me-1"></i>Current Profit</span>
                <div class="mob-metric-val text-success mt-1" id="mobPillCurrentProfit">Rs 0</div>
            </div>
        </div>
    </div>

    {{-- DESKTOP TABLE CONTAINER (d-none d-md-block) --}}
    <div class="card border-0 shadow-sm d-none d-md-block" style="border-radius: 8px;">
        <div class="card-body p-0">
            <div class="loader" style="display:none; text-align:center; padding: 20px;">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="small text-muted mt-2">Loading sales data…</div>
            </div>

            <div class="sale-table-wrap">
                <table class="table table-bordered table-hover align-middle" id="saleReport">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th style="width:130px;">Date &amp; Time</th>
                            <th style="width:110px;">Invoice</th>
                            <th style="width:110px;">Customer</th>
                            <th style="width:100px;">Cashier</th>
                            <th style="width:90px;">Ref</th>
                            <th>Products</th>
                            <th style="width:75px;">Qty</th>
                            <th style="width:90px;">Price</th>
                            <th style="width:95px;">Total</th>
                            <th style="width:95px;">Net</th>
                            <th style="width:135px;">Returns</th>
                        </tr>
                    </thead>
                    <tbody id="saleBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MOBILE SALE CARDS CONTAINER (d-md-none) --}}
    <div class="d-md-none" id="saleMobileContainer">
        <div class="text-center py-4 text-muted card border-0 shadow-sm rounded-3 bg-white">
            <div class="card-body py-4">
                <i class="fas fa-spinner fa-spin fa-2x mb-2 text-secondary"></i>
                <p class="mb-0 small fw-bold">Loading sales data…</p>
            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {

        let currentExpenses = 0;
        let currentCogs = 0;

        // Sync Date & Cashier Inputs between Desktop & Mobile
        $('.startDateInput').on('change', function() { $('.startDateInput').val($(this).val()); $('.quickDateInput').val('custom'); });
        $('.endDateInput').on('change', function() { $('.endDateInput').val($(this).val()); $('.quickDateInput').val('custom'); });
        $('.userIdInput').on('change', function() { $('.userIdInput').val($(this).val()); });
        $('.searchProductInput').on('input', function() { $('.searchProductInput').val($(this).val()); });

        function applyQuickDate(val) {
            let now = new Date();
            let pad = (n) => String(n).padStart(2, '0');
            let toYmd = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

            if (val === 'today') {
                let str = toYmd(now);
                $('.startDateInput').val(str);
                $('.endDateInput').val(str);
            } else if (val === 'yesterday') {
                let yest = new Date(now);
                yest.setDate(yest.getDate() - 1);
                let str = toYmd(yest);
                $('.startDateInput').val(str);
                $('.endDateInput').val(str);
            } else if (val === 'last_7') {
                let past = new Date(now);
                past.setDate(past.getDate() - 6);
                $('.startDateInput').val(toYmd(past));
                $('.endDateInput').val(toYmd(now));
            } else if (val === 'this_month') {
                let firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                $('.startDateInput').val(toYmd(firstDay));
                $('.endDateInput').val(toYmd(now));
            }
        }

        $('.quickDateInput').on('change', function() {
            let val = $(this).val();
            $('.quickDateInput').val(val);
            if (val !== 'custom') {
                applyQuickDate(val);
                $('.btnSearchTrigger').first().trigger('click');
            }
        });

        // Reset Handler
        $(document).on('click', '.btnResetTrigger', function() {
            $('.quickDateInput').val('this_month');
            applyQuickDate('this_month');
            $('.userIdInput').val('');
            $('.searchProductInput').val('');
            $('.btnSearchTrigger').first().trigger('click');
        });

        // Print Handler
        $(document).on('click', '.btnPrintReport', function() {
            window.print();
        });

        // Auto Search Trigger
        $(document).on('click', '.btnSearchTrigger', function() {
            let start  = $('#start_date_desk').val() || $('#start_date_mob').val();
            let end    = $('#end_date_desk').val() || $('#end_date_mob').val();
            let userId = $('#user_id_desk').val() || $('#user_id_mob').val();
            $('.searchProductInput').val('');

            $(".loader").show();
            $.ajax({
                url: "{{ route('report.sale.fetch') }}",
                type: "GET",
                data: {
                    start_date: start,
                    end_date: end,
                    user_id: userId
                },
                success: function(res) {
                    $(".loader").hide();

                    let salesData = Array.isArray(res) ? res : (res.sales || []);
                    let summary   = res.summary || {};
                    currentExpenses = parseFloat(summary.expenses) || 0;
                    currentCogs     = parseFloat(summary.cogs) || 0;

                    let html = "";
                    let mobHtml = "";
                    let grandQty = 0,
                        grandTotal = 0,
                        grandNet = 0,
                        grandReturn = 0;

                    salesData.forEach((s, i) => {
                        let invNo = s.invoice_no || ('INVSLE-' + s.id);
                        let products = s.product.split(',').join('<br>');
                        let qtyArr = s.qty.split(',');
                        let qtyPiecesArr = s.total_pieces ? s.total_pieces.split(',') : (s.qty_decimal ? s.qty_decimal.split(',') : qtyArr);
                        let price = s.per_price.split(',').join('<br>');
                        let total = s.per_total.split(',').join('<br>');

                        let rowQty = qtyPiecesArr.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                        grandQty += rowQty;

                        let rowTotal = s.per_total.split(',').reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                        grandTotal += parseFloat(rowTotal);
                        grandNet += parseFloat(s.total_net);

                        let returnHtml = "";
                        let returnTotal = 0;
                        if (s.returns && s.returns.length > 0) {
                            s.returns.forEach(r => {
                                returnHtml += `<span class="text-danger fw-semibold">${r.product} (${r.qty}) - ${r.per_total}</span><br>`;
                                returnTotal += parseFloat(r.per_total);
                            });
                        }
                        grandReturn += returnTotal;

                        // Desktop Row
                        html += `<tr data-qty="${rowQty}" data-total="${rowTotal}" data-net="${s.total_net}" data-return="${returnTotal}">
                            <td>${i+1}</td>
                            <td class="small text-nowrap">${s.created_at}</td>
                            <td class="font-monospace fw-bold text-primary">${invNo}</td>
                            <td>${s.customer_name ?? '-'}</td>
                            <td><span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-user-circle text-primary me-1"></i>${s.cashier || 'System'}</span></td>
                            <td>${s.reference ?? '-'}</td>
                            <td>${products}</td>
                            <td class="fw-semibold">${qtyArr.join('<br>')}</td>
                            <td>${price}</td>
                            <td>${total}</td>
                            <td class="fw-bold text-dark">${parseFloat(s.total_net).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                            <td>${returnHtml || '-'}</td>
                        </tr>`;

                        // Mobile Card
                        mobHtml += `
                        <div class="mob-card p-2.5 p-2 mb-2 mob-sale-card" data-search="${(s.product + ' ' + invNo + ' ' + (s.customer_name||'') + ' ' + (s.cashier||'') + ' ' + (s.reference||'')).toLowerCase()}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-light text-muted border" style="font-size: 10px;">#${i+1}</span>
                                    <span class="badge bg-light text-primary border font-monospace fw-bold" style="font-size: 11px;">${invNo}</span>
                                </div>
                                <small class="text-muted" style="font-size: 10.5px;">${s.created_at}</small>
                            </div>
                            <div class="mb-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <strong class="text-dark" style="font-size: 12.5px;">${s.customer_name ?? 'Walking Customer'}</strong>
                                    <span class="badge bg-light text-dark border" style="font-size: 10px;"><i class="fas fa-user-circle text-primary me-1"></i>${s.cashier || 'System'}</span>
                                </div>
                                <small class="text-muted" style="font-size: 10.5px;">Ref: ${s.reference ?? '-'}</small>
                            </div>
                            <div class="bg-light rounded p-2 mb-1" style="font-size: 11px;">
                                <span class="fw-semibold text-secondary d-block mb-1">Products:</span>
                                <div>${products}</div>
                            </div>
                            <div class="border-top pt-2">
                                <div class="row g-1 text-center" style="font-size: 11px;">
                                    <div class="col-4 border-end">
                                        <span class="text-muted d-block" style="font-size: 10px;">Qty</span>
                                        <strong class="text-dark">${rowQty}</strong>
                                    </div>
                                    <div class="col-4 border-end">
                                        <span class="text-muted d-block" style="font-size: 10px;">Gross</span>
                                        <strong class="text-dark">Rs ${parseFloat(rowTotal).toLocaleString(undefined, {minimumFractionDigits:2})}</strong>
                                    </div>
                                    <div class="col-4">
                                        <span class="text-muted d-block" style="font-size: 10px;">Net Sale</span>
                                        <strong class="text-success">Rs ${parseFloat(s.total_net).toLocaleString(undefined, {minimumFractionDigits:2})}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });

                    // Grand total row inside desktop table
                    html += `<tr class="fw-bold bg-light" id="grandTotalRow">
                        <td colspan="7" class="text-end">Grand Total:</td>
                        <td id="grandQty">${grandQty.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                        <td>-</td>
                        <td id="grandTotal">${grandTotal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                        <td id="grandNet">${grandNet.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                        <td id="grandReturn">${grandReturn.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                    </tr>`;

                    $('#saleBody').html(html);
                    $('#saleMobileContainer').html(mobHtml || '<div class="card border-0 shadow-sm rounded-3 text-center py-4 bg-white"><div class="card-body py-4 text-muted"><i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i><p class="small fw-bold mb-0">No Sales Data Found</p></div></div>');

                    updateSingleLineSummary(salesData.length, grandQty, grandTotal, grandReturn, grandNet);
                }
            });
        });

        // Function to update Summary Metrics (Desktop & Mobile)
        function updateSingleLineSummary(count, qty, gross, returns, net) {
            let grossProfit   = net - currentCogs;
            let currentProfit = grossProfit - currentExpenses;

            let formattedCount = count.toLocaleString();
            let formattedQty   = qty.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' Pcs';
            let formattedGross = 'Rs ' + gross.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            let formattedRet   = 'Rs ' + returns.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            let formattedNet   = 'Rs ' + net.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            let formattedGP    = 'Rs ' + grossProfit.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            let formattedExp   = 'Rs ' + currentExpenses.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
            let formattedNP    = 'Rs ' + currentProfit.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});

            // Desktop Pills
            $('#pillTotalInvoices').text(formattedCount);
            $('#pillTotalQty').text(formattedQty);
            $('#pillGrossSale').text(formattedGross);
            $('#pillTotalReturns').text(formattedRet);
            $('#pillNetSale').text(formattedNet);
            $('#pillGrossProfit').text(formattedGP);
            $('#pillExpenses').text(formattedExp);
            $('#pillCurrentProfit').text(formattedNP);

            // Mobile Cards
            $('#mobPillTotalInvoices').text(formattedCount);
            $('#mobPillTotalQty').text(formattedQty);
            $('#mobPillGrossSale').text(formattedGross);
            $('#mobPillTotalReturns').text(formattedRet);
            $('#mobPillNetSale').text(formattedNet);
            $('#mobPillGrossProfit').text(formattedGP);
            $('#mobPillExpenses').text(formattedExp);
            $('#mobPillCurrentProfit').text(formattedNP);
        }

        // Real-time Search Filter Handler
        $(document).on('input', '.searchProductInput', function() {
            let val = $(this).val().toLowerCase();

            // Desktop Table Filter
            $('#saleBody tr').each(function() {
                if ($(this).attr('id') === 'grandTotalRow') return;
                let text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(val) > -1);
            });

            // Mobile Card Filter
            $('.mob-sale-card').each(function() {
                let searchStr = $(this).attr('data-search') || $(this).text().toLowerCase();
                $(this).toggle(searchStr.indexOf(val) > -1);
            });
        });

        // Export CSV Handler
        $(document).on('click', '.btnExportCsvTrigger', function() {
            let csv = [];
            csv.push(['#', 'Date & Time', 'Invoice', 'Customer', 'Cashier', 'Reference', 'Products', 'Qty', 'Price', 'Total', 'Net Amount', 'Returns'].join(','));
            $('#saleBody tr').each(function() {
                if ($(this).attr('id') === 'grandTotalRow') return;
                let row = [];
                $(this).find('td').each(function() {
                    let text = $(this).text().trim().replace(/,/g, '').replace(/\n/g, ' ');
                    row.push('"' + text + '"');
                });
                if (row.length) csv.push(row.join(','));
            });
            let blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'Sale_Report.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Auto-load on page ready
        $('.btnSearchTrigger').first().trigger('click');

    });
</script>
@endsection