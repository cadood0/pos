<div class="container-fluid py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Edit category</h1>
        <p class="text-muted mb-0">Update this product category.</p>
    </div>

    <div id="categoryAlert" class="alert d-none"></div>

    <div class="card shadow-sm" style="max-width: 480px;">
        <div class="card-body">
            <form id="categoryEditForm" data-id="<?= (int) $category_id ?>">
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" maxlength="100" required>
                </div>

                <button type="submit" class="btn btn-dark">Save changes</button>
                <a href="<?= site_url('categories/'.$category_id) ?>" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
