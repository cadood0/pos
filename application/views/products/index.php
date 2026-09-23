<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Products</h1>
            <p class="text-muted mb-0">Catalog, pricing and stock.</p>
        </div>
        <a href="<?= site_url('products/create') ?>" class="btn btn-dark">
            Add product
        </a>
    </div>

    <div id="productAlert" class="alert d-none"></div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-6">
                    <input id="search" class="form-control"
                           placeholder="Search name or exact barcode">
                </div>
                <div class="col-md-3">
                    <select id="categoryFilter" class="form-select">
                        <option value="">All categories</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="applyFilters" class="btn btn-outline-dark w-100">
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Barcode</th>
                        <th>Category</th>
                        <th>Cost</th>
                        <th>Sell</th>
                        <th>Stock</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="productRows"></tbody>
            </table>
        </div>
    </div>

    <nav class="mt-3">
        <ul id="pagination" class="pagination justify-content-center"></ul>
    </nav>
</div>
