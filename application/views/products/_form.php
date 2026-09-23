<div class="mb-3">
    <label class="form-label" for="name">Name</label>
    <input type="text" id="name" name="name" class="form-control" maxlength="255" required>
</div>

<div class="mb-3">
    <label class="form-label" for="barcode">Barcode</label>
    <input type="text" id="barcode" name="barcode" class="form-control" maxlength="100">
</div>

<div class="mb-3">
    <label class="form-label" for="category_id">Category</label>
    <select id="category_id" name="category_id" class="form-select" required>
        <option value="">Select category</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label" for="cost_price">Cost price</label>
    <input type="number" id="cost_price" name="cost_price" class="form-control" min="0" step="0.01" required>
</div>

<div class="mb-3">
    <label class="form-label" for="sell_price">Sell price</label>
    <input type="number" id="sell_price" name="sell_price" class="form-control" min="0" step="0.01" required>
    <div class="form-text text-danger d-none js-price-hint"></div>
</div>

<div class="mb-3">
    <label class="form-label" for="stock_qty">Stock quantity</label>
    <input type="number" id="stock_qty" name="stock_qty" class="form-control" min="0" step="1" required>
</div>
