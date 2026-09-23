<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Sale history</h1>
            <p class="text-muted mb-0">Completed invoices. Sales cannot be edited or voided.</p>
        </div>
        <a href="<?= site_url('pos') ?>" class="btn btn-dark">
            New sale
        </a>
    </div>

    <div id="saleAlert" class="alert d-none"></div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Cashier</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="saleRows"></tbody>
            </table>
        </div>
    </div>

    <nav class="mt-3">
        <ul id="salePagination" class="pagination justify-content-center"></ul>
    </nav>
</div>
