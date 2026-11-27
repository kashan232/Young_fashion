@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Standardized Sale Report Pattern Styling */
    .sale-report-container {
        padding: 10px 14px;
        background: #f1f5f9;
        min-height: calc(100vh - 75px);
    }
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

    /* Table Styling with Sticky Header */
    .sale-table-wrap {
        height: calc(100vh - 350px);
        max-height: calc(100vh - 350px);
        min-height: 300px;
        overflow-y: auto;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
    }
    .report-table {
        font-size: .78rem;
        margin-bottom: 0;
    }
    .report-table thead th {
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

    .account-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .account-row:last-child { border-bottom: none; }

    @media print {
        body { background: #ffffff !important; font-size: 11px; }
        .no-print, header, .sidebar, .navbar, footer { display: none !important; }
        .sale-report-container { padding: 0 !important; background: #fff !important; }
        .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; margin-bottom: 10px !important; }
    }
</style>

<div class="sale-report-container">

    {{-- DESKTOP FILTER HEADER CARD (d-none d-md-block Standard Pattern) --}}
    <div class="card border-0 shadow-sm mb-2 no-print d-none d-md-block" style="border-radius: 10px;">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                
                {{-- Left Title --}}
                <div class="d-flex align-items-center me-2">
                    <span class="fw-bold text-dark fs-6 text-nowrap" style="letter-spacing: -0.2px;">
                        <i class="fa-solid fa-crown text-warning me-2"></i>Executive Overview Report
                    </span>
                </div>

                {{-- Middle: Date Range Controls --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <select id="quickDatePresetDesk" class="form-select form-select-sm fw-bold quickPresetSelect" style="width: 140px; height: 32px; font-size: .78rem; border-radius: 6px;">
                        <option value="today" selected>Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="day_before">2 Days Ago</option>
                        <option value="last_7_days">Last 7 Days</option>
                        <option value="this_month">This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>

                    <div class="d-flex align-items-center gap-1">
                        <label for="start_date_desk" class="mb-0 text-muted fw-bold" style="font-size: .75rem;">From:</label>
                        <input type="date" name="start_date" id="start_date_desk" class="form-control form-control-sm fw-bold startDateInput" value="{{ date('Y-m-d') }}" style="height: 32px; width: 130px; font-size: .78rem; border-radius: 6px;">
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        <label for="end_date_desk" class="mb-0 text-muted fw-bold" style="font-size: .75rem;">To:</label>
                        <input type="date" name="end_date" id="end_date_desk" class="form-control form-control-sm fw-bold endDateInput" value="{{ date('Y-m-d') }}" style="height: 32px; width: 130px; font-size: .78rem; border-radius: 6px;">
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center ms-auto" style="gap: 8px !important;">
                    <button type="button" class="btn btn-primary btn-sm px-3 fw-bold d-inline-flex align-items-center btnFetchTrigger" style="height: 32px; border-radius: 6px; font-size: .78rem;">
                        <i class="fas fa-filter me-1"></i> Filter / Refresh
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold d-inline-flex align-items-center btnPrintReport" style="height: 32px; border-radius: 6px; font-size: .78rem;">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MOBILE FILTER HEADER CARD (d-md-none With Top Margin) --}}
    <div class="card border-0 shadow-sm mb-3 no-print d-md-none mt-2" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-12 mb-1 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark fs-6">
                        <i class="fa-solid fa-crown text-warning me-2"></i>Executive Report
                    </span>
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold btnPrintReport" style="font-size: 11px;">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
                <div class="col-12">
                    <select id="quickDatePresetMob" class="form-select form-select-sm fw-bold quickPresetSelect" style="font-size: 11px;">
                        <option value="today" selected>Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="day_before">2 Days Ago</option>
                        <option value="last_7_days">Last 7 Days</option>
                        <option value="this_month">This Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="small text-muted fw-bold mb-0" style="font-size:10px;">From Date</label>
                    <input type="date" name="start_date" id="start_date_mob" class="form-control form-control-sm startDateInput" value="{{ date('Y-m-d') }}" style="font-size: 11px;">
                </div>
                <div class="col-6">
                    <label class="small text-muted fw-bold mb-0" style="font-size:10px;">To Date</label>
                    <input type="date" name="end_date" id="end_date_mob" class="form-control form-control-sm endDateInput" value="{{ date('Y-m-d') }}" style="font-size: 11px;">
                </div>
                <div class="col-12 mt-2">
                    <button type="button" class="btn btn-primary w-100 btn-sm fw-bold btnFetchTrigger" style="font-size: 11px;">
                        <i class="fas fa-filter me-1"></i> Apply Filter / Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- DESKTOP SUMMARY METRIC PILL BAR (d-none d-md-block) --}}
    <div class="card border-0 shadow-sm mb-2 d-none d-md-block" style="border-radius: 10px; background: #ffffff;">
        <div class="card-body p-2">
            <div class="summary-pill-bar">
                
                <div class="stat-pill" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="stat-label text-primary" id="lblSalesPeriod">Sales (Today)</div>
                    <div class="stat-val text-primary" id="salesPeriod">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="stat-label text-info">Sales (This Month)</div>
                    <div class="stat-val text-info" id="salesMonth">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fffbeb; border-color: #fde047;">
                    <div class="stat-label" style="color: #b45309;" id="lblPurchasesPeriod">Purchases (Today)</div>
                    <div class="stat-val" style="color: #d97706;" id="purchasesPeriod">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger" id="lblExpensesPeriod">Expenses (Today)</div>
                    <div class="stat-val text-danger" id="expensesPeriod">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fef2f2; border-color: #fca5a5;">
                    <div class="stat-label text-danger">Receivables</div>
                    <div class="stat-val text-danger" id="totalReceivables">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #fffbeb; border-color: #fde047;">
                    <div class="stat-label" style="color: #b45309;">Payables</div>
                    <div class="stat-val" style="color: #d97706;" id="totalPayables">Rs 0.00</div>
                </div>

                <div class="stat-pill" style="background: #f0fdf4; border-color: #86efac;">
                    <div class="stat-label text-success">Liquidity Index</div>
                    <div class="stat-val text-success" id="liquidityRatio">1.0+</div>
                </div>

            </div>
        </div>
    </div>

    {{-- MOBILE SUMMARY METRIC GRID (2 Columns col-6 d-md-none) --}}
    <div class="row g-2 mb-3 d-md-none no-print px-1">
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-shopping-cart text-primary me-1"></i><span id="mobSalesLabel">Sales (Today)</span></span>
                <div class="mob-metric-val text-primary mt-1" id="mobSalesPeriod">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-truck-loading text-warning me-1"></i><span id="mobPurchasesLabel">Purchases</span></span>
                <div class="mob-metric-val text-warning mt-1" id="mobPurchasesPeriod">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-arrow-down text-danger me-1"></i>Receivables</span>
                <div class="mob-metric-val text-danger mt-1" id="mobTotalReceivables">Rs 0.00</div>
            </div>
        </div>
        <div class="col-6 mb-1">
            <div class="mob-metric-card">
                <span class="mob-metric-label"><i class="fas fa-arrow-up text-warning me-1"></i>Payables</span>
                <div class="mob-metric-val text-warning mt-1" id="mobTotalPayables">Rs 0.00</div>
            </div>
        </div>
    </div>

    {{-- Loader --}}
    <div id="loader" style="display:none; text-align:center; padding: 20px;">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="small text-muted mt-2">Syncing latest executive figures…</div>
    </div>

    {{-- Content Layout --}}
    <div class="row g-3 mb-3">
        
        {{-- Cash in Hand --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-light py-2 px-3">
                    <span class="fw-bold text-dark fs-6"><i class="fas fa-wallet text-success me-2"></i>Cash in Hand</span>
                </div>
                <div class="card-body p-3" id="cashList">
                    <div class="text-center py-3 text-muted small">Loading cash accounts…</div>
                </div>
            </div>
        </div>

        {{-- Bank Balances --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-light py-2 px-3">
                    <span class="fw-bold text-dark fs-6"><i class="fas fa-university text-info me-2"></i>Bank Balances</span>
                </div>
                <div class="card-body p-3" id="bankList">
                    <div class="text-center py-3 text-muted small">Loading bank accounts…</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Top 10 Customers --}}
    <div class="card border-0 shadow-sm mb-3 rounded-3 bg-white">
        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark fs-6">
                <i class="fas fa-crown text-warning me-2"></i>Top Customers by Profit 
                <span class="badge bg-primary text-white ms-1 fw-bold" id="topCustomersPeriodBadge" style="font-size:0.75rem;">Today</span>
            </span>
            <span class="badge bg-secondary text-white small" id="customerCount">0 customers</span>
        </div>
        <div class="card-body p-0">
            
            {{-- DESKTOP TABLE VIEW (d-none d-md-block) --}}
            <div class="d-none d-md-block">
                <div class="sale-table-wrap border-0 rounded-0">
                    <table class="table table-bordered table-hover align-middle mb-0 report-table" id="topCustomersTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">#</th>
                                <th>CUSTOMER NAME</th>
                                <th class="text-end" style="width: 140px;">REVENUE</th>
                                <th class="text-end" style="width: 150px;">BALANCE</th>
                                <th class="text-end" style="width: 150px;">NET PROFIT</th>
                            </tr>
                        </thead>
                        <tbody id="topCustomersList">
                            <tr><td colspan="5" class="text-center text-muted py-4 small">Fetch data to see top customers</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE CARDS CONTAINER (d-md-none) --}}
            <div class="d-md-none p-2" id="mobTopCustomersContainer">
            </div>

        </div>
    </div>

</div>
@endsection

@section('js')
<script>
function fmt(n) {
    let val = parseFloat(n || 0);
    let sign = val < 0 ? '-' : '';
    return sign + 'Rs ' + Math.abs(val).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function getActiveDateRange() {
    let start = $('#start_date_desk').val() || $('#start_date_mob').val();
    let end = $('#end_date_desk').val() || $('#end_date_mob').val();
    return { start: start, end: end };
}

function setQuickPreset(preset) {
    let today = new Date();
    let pad = (n) => String(n).padStart(2, '0');
    let fmtDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

    let start = fmtDate(today);
    let end = fmtDate(today);

    if (preset === 'today') {
        start = fmtDate(today);
        end = fmtDate(today);
    } else if (preset === 'yesterday') {
        let y = new Date();
        y.setDate(today.getDate() - 1);
        start = fmtDate(y);
        end = fmtDate(y);
    } else if (preset === 'day_before') {
        let db = new Date();
        db.setDate(today.getDate() - 2);
        start = fmtDate(db);
        end = fmtDate(db);
    } else if (preset === 'last_7_days') {
        let d7 = new Date();
        d7.setDate(today.getDate() - 6);
        start = fmtDate(d7);
        end = fmtDate(today);
    } else if (preset === 'this_month') {
        let firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        start = fmtDate(firstDay);
        end = fmtDate(today);
    }

    if (preset !== 'custom') {
        $('.startDateInput').val(start);
        $('.endDateInput').val(end);
    }
}

function fetchData() {
    $('#loader').show();
    let range = getActiveDateRange();

    $.ajax({
        url: "{{ route('report.executive.fetch') }}",
        type: "GET",
        data: {
            start_date: range.start,
            end_date: range.end
        },
        success: function(res) {
            $('#loader').hide();

            let label = res.filter ? res.filter.period_label : 'Today';

            // Dynamic Labels
            $('#lblSalesPeriod').text(`Sales (${label})`);
            $('#lblPurchasesPeriod').text(`Purchases (${label})`);
            $('#lblExpensesPeriod').text(`Expenses (${label})`);
            $('#topCustomersPeriodBadge').text(label);
            $('#mobSalesLabel').text(`Sales (${label})`);
            $('#mobPurchasesLabel').text(`Purchases (${label})`);

            // Sales
            let salesPeriodVal = res.sales.period !== undefined ? res.sales.period : res.sales.today;
            $('#salesPeriod, #mobSalesPeriod').text(fmt(salesPeriodVal));
            $('#salesMonth').text(fmt(res.sales.month));

            // Purchases
            let purchPeriodVal = res.purchases.period !== undefined ? res.purchases.period : res.purchases.today;
            $('#purchasesPeriod, #mobPurchasesPeriod').text(fmt(purchPeriodVal));

            // Expenses
            let expPeriodVal = res.expenses.period !== undefined ? res.expenses.period : res.expenses.today;
            $('#expensesPeriod').text(fmt(expPeriodVal));

            // Vitals
            $('#totalReceivables, #mobTotalReceivables').text(fmt(res.receivables));
            $('#totalPayables, #mobTotalPayables').text(fmt(res.payables));

            // Accounts
            let cashHtml = '';
            if(res.accounts && res.accounts.cash && res.accounts.cash.length > 0) {
                res.accounts.cash.forEach(acc => {
                    cashHtml += `
                    <div class="account-row">
                        <span class="fw-bold small text-dark"><i class="fas fa-money-bill-wave text-success me-2"></i>${acc.title}</span>
                        <span class="fw-bold small text-success">${fmt(acc.current_balance)}</span>
                    </div>`;
                });
            } else {
                cashHtml = '<div class="text-center py-3 text-muted small">No cash accounts found</div>';
            }
            $('#cashList').html(cashHtml);

            let bankHtml = '';
            if(res.accounts && res.accounts.bank && res.accounts.bank.length > 0) {
                res.accounts.bank.forEach(acc => {
                    bankHtml += `
                    <div class="account-row">
                        <span class="fw-bold small text-dark"><i class="fas fa-university text-info me-2"></i>${acc.title}</span>
                        <span class="fw-bold small text-info">${fmt(acc.current_balance)}</span>
                    </div>`;
                });
            } else {
                bankHtml = '<div class="text-center py-3 text-muted small">No bank accounts found</div>';
            }
            $('#bankList').html(bankHtml);

            // Top Customers
            let topCustHtml = '';
            let mobCustHtml = '';
            let custCount = res.top_customers ? res.top_customers.length : 0;
            $('#customerCount').text(custCount + ' customers');

            if(res.top_customers && res.top_customers.length > 0) {
                res.top_customers.forEach((c, i) => {
                    let profitColor = c.profit >= 0 ? '#059669' : '#dc2626';
                    let profitSign = c.profit < 0 ? '-' : '';

                    topCustHtml += `
                    <tr>
                        <td class="text-center fw-bold text-muted" style="font-size:12px;">${i+1}</td>
                        <td>
                            <div class="fw-bold text-dark" style="font-size:13px;">${c.name}</div>
                            <div class="text-muted small" style="font-size:10px;">ID: CUST-${c.id.toString().padStart(4, '0')}</div>
                        </td>
                        <td class="text-end fw-semibold" style="font-size:12px;">${fmt(c.revenue)}</td>
                        <td class="text-end text-danger fw-semibold" style="font-size:12px;">${fmt(c.balance)}</td>
                        <td class="text-end">
                            <span class="badge p-1.5 px-2" style="background: ${c.profit >= 0 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'}; color: ${profitColor}; font-size:12px; font-weight:700;">
                                ${profitSign}${fmt(Math.abs(c.profit))}
                            </span>
                        </td>
                    </tr>`;

                    mobCustHtml += `
                    <div class="mob-card p-2 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-light text-muted border" style="font-size:10px;">#${i+1}</span>
                                <strong class="text-dark" style="font-size:12.5px;">${c.name}</strong>
                            </div>
                            <span class="badge p-1 px-2" style="background:${c.profit >= 0 ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)'}; color:${profitColor}; font-size:11px; font-weight:700;">
                                ${profitSign}${fmt(Math.abs(c.profit))}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between text-muted" style="font-size:11px;">
                            <span>Revenue: ${fmt(c.revenue)}</span>
                            <span class="text-danger">Balance: ${fmt(c.balance)}</span>
                        </div>
                    </div>`;
                });
                $('#topCustomersList').html(topCustHtml);
                $('#mobTopCustomersContainer').html(mobCustHtml);
            } else {
                $('#topCustomersList').html('<tr><td colspan="5" class="text-center text-muted py-4 small">No customer profit data found for selected period</td></tr>');
                $('#mobTopCustomersContainer').html('<div class="text-center text-muted py-3 small">No customer profit data found for selected period</div>');
            }

            // Liquidity Index
            let totalAvailable = 0;
            if (res.accounts && res.accounts.cash) res.accounts.cash.forEach(a => totalAvailable += parseFloat(a.current_balance));
            if (res.accounts && res.accounts.bank) res.accounts.bank.forEach(a => totalAvailable += parseFloat(a.current_balance));
            
            let ratio = res.payables > 0 ? (totalAvailable / res.payables).toFixed(2) : '1.0+';
            $('#liquidityRatio').text(ratio);

        },
        error: function() {
            $('#loader').hide();
            alert('Failed to fetch executive data');
        }
    });
}

$(document).ready(function() {
    $('.quickPresetSelect').on('change', function() {
        let val = $(this).val();
        $('.quickPresetSelect').val(val);
        setQuickPreset(val);
        fetchData();
    });

    $('.startDateInput').on('change', function() {
        $('.startDateInput').val($(this).val());
        $('.quickPresetSelect').val('custom');
    });

    $('.endDateInput').on('change', function() {
        $('.endDateInput').val($(this).val());
        $('.quickPresetSelect').val('custom');
    });

    $('.btnFetchTrigger').click(fetchData);
    $('.btnPrintReport').click(() => window.print());
    fetchData();
});
</script>
@endsection
