<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Receipt</h1>
            <p class="text-muted mb-0">Sale totals come from the server.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('sales/history') ?>" class="btn btn-outline-secondary">History</a>
            <a href="<?= site_url('pos') ?>" class="btn btn-dark">New sale</a>
        </div>
    </div>

    <div id="saleAlert" class="alert d-none"></div>

    <div class="card shadow-sm" style="max-width: 640px;" id="receiptCard" data-id="<?= (int) $sale_id ?>">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <div class="text-muted small">Invoice</div>
                    <div id="receiptInvoice" class="fs-5">—</div>
                </div>
                <div class="text-end">
                    <div class="text-muted small">Date</div>
                    <div id="receiptDate">—</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <div class="text-muted small">Cashier</div>
                    <div id="receiptCashier">—</div>
                </div>
                <div class="col-6">
                    <div class="text-muted small">Customer</div>
                    <div id="receiptCustomer">Walk-in</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th class="text-end">Line</th>
                        </tr>
                    </thead>
                    <tbody id="receiptItems"></tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between">
                <span>Total</span>
                <span id="receiptTotal">0.00</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Paid</span>
                <span id="receiptPaid">0.00</span>
            </div>
            <div class="d-flex justify-content-between fw-bold">
                <span>Change</span>
                <span id="receiptChange">0.00</span>
            </div>
            <div class="text-muted small mt-2">
                Payment: <span id="receiptMethod">—</span>
            </div>
        </div>
    </div>
</div>
