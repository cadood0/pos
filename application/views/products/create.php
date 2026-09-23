<div class="container-fluid py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Add product</h1>
        <p class="text-muted mb-0">Create a catalog item. Prices and stock are saved by the API.</p>
    </div>

    <div id="productAlert" class="alert d-none"></div>

    <div class="card shadow-sm" style="max-width: 560px;">
        <div class="card-body">
            <form id="productCreateForm">
                <?php $this->load->view('products/_form'); ?>

                <button type="submit" class="btn btn-dark">Save product</button>
                <a href="<?= site_url('products') ?>" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
