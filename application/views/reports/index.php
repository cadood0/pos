<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Reports</h1>
            <p class="text-muted mb-0">Sales and stock performance.</p>
        </div>
    </div>

    <form id="reportFilters" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">From</label>
                    <input id="from" type="date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To</label>
                    <input id="to" type="date" class="form-control">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-dark w-100" type="submit">
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div id="reportError" class="alert alert-danger d-none"></div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="small text-muted">Sales</div>
                <div id="salesCount" class="fs-3 fw-bold">—</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="small text-muted">Revenue</div>
                <div id="revenue" class="fs-3 fw-bold">—</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="small text-muted">Items sold</div>
                <div id="itemsSold" class="fs-3 fw-bold">—</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="small text-muted">Profit</div>
                <div id="profit" class="fs-3 fw-bold">—</div>
            </div></div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">Sales by day</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sales</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody id="salesByDay"></tbody>
            </table>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Top Products</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody id="topProducts"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Low Stock</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody id="lowStock"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
