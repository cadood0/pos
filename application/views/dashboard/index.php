<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">
                Overview of your shop.
            </p>
        </div>

        <a href="<?= site_url('pos') ?>" class="btn btn-dark">
            Open POS
        </a>
    </div>

    <div id="dashboardError" class="alert alert-danger d-none"></div>

    <div id="kpiRow" class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Sales</div>
                    <div id="salesCount" class="fs-3 fw-bold">—</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Revenue</div>
                    <div id="revenue" class="fs-3 fw-bold">—</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Items sold</div>
                    <div id="itemsSold" class="fs-3 fw-bold">—</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Profit</div>
                    <div id="profit" class="fs-3 fw-bold">—</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            Quick actions
        </div>

        <div class="card-body">
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= site_url('categories') ?>"
                   class="btn btn-outline-dark">
                    Categories
                </a>

                <a href="<?= site_url('products') ?>"
                   class="btn btn-outline-dark">
                    Products
                </a>

                <a href="<?= site_url('pos') ?>"
                   class="btn btn-dark">
                    New Sale
                </a>

                <a href="<?= site_url('reports') ?>"
                   class="btn btn-outline-dark">
                    Reports
                </a>
            </div>
        </div>
    </div>
</div>
