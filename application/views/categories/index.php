<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Categories</h1>
            <p class="text-muted mb-0">Manage product categories.</p>
        </div>

        <a href="<?= site_url('categories/create') ?>" class="btn btn-dark">
            Add category
        </a>
    </div>

    <div id="categoryAlert" class="alert d-none"></div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Products</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="categoryRows"></tbody>
            </table>
        </div>
    </div>
</div>
