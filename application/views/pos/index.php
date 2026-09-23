<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">POS</h1>
            <p class="text-muted mb-0">Search products, build a cart, and check out.</p>
        </div>
        <a href="<?= site_url('sales/history') ?>" class="btn btn-outline-dark">
            Sale history
        </a>
    </div>

    <div class="row g-4">

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input id="productSearch"
                               class="form-control"
                               placeholder="Search product or barcode">
                        <button id="searchProducts"
                                class="btn btn-dark">
                            Search
                        </button>
                    </div>

                    <div id="productResults" class="row g-2"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Current Sale</div>
                <div class="card-body">
                    <div id="cartItems"></div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total</span>
                        <span id="cartTotal" class="fw-bold">0.00</span>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Paid amount</label>
                        <input id="paidAmount"
                               type="number"
                               min="0"
                               step="0.01"
                               class="form-control">
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Payment method</label>
                        <select id="paymentMethod" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="mobile">Mobile</option>
                        </select>
                    </div>

                    <button id="completeSale"
                            class="btn btn-dark w-100 mt-4">
                        Complete Sale
                    </button>

                    <div id="checkoutAlert"
                         class="alert d-none mt-3"></div>
                </div>
            </div>
        </div>

    </div>
</div>
