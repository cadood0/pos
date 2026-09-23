<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Category</h1>
            <p class="text-muted mb-0">Category details.</p>
        </div>
        <a href="<?= site_url('categories') ?>" class="btn btn-outline-secondary">Back</a>
    </div>

    <div id="categoryAlert" class="alert d-none"></div>

    <div class="card shadow-sm" style="max-width: 480px;">
        <div class="card-body" id="categoryShow" data-id="<?= (int) $category_id ?>">
            <div class="mb-3">
                <div class="text-muted small">Name</div>
                <div id="categoryName" class="fs-5">—</div>
            </div>
            <div class="mb-3">
                <div class="text-muted small">Products</div>
                <div id="categoryProducts">—</div>
            </div>
            <a id="categoryEditLink" href="<?= site_url('categories/'.$category_id.'/edit') ?>" class="btn btn-dark">
                Edit
            </a>
        </div>
    </div>
</div>
