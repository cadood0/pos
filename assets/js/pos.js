const cart = new Map();
const productCache = new Map();

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function formatMoney(value) {
    return Number(value).toFixed(2);
}

function showBox(id, message, type) {
    const box = document.getElementById(id);

    if (!box) {
        return;
    }

    box.className = 'alert alert-' + type + (id === 'checkoutAlert' ? ' mt-3' : '');
    box.textContent = message;
}

function showCheckoutAlert(message, type) {
    showBox('checkoutAlert', message, type);
}

function showSaleAlert(message, type) {
    showBox('saleAlert', message, type);
}

function saleUrl(path) {
    return window.APP.salesUrl.replace(/\/$/, '') + path;
}

function previewTotal() {
    let total = 0;

    cart.forEach((line) => {
        total += Number(line.sell_price) * Number(line.quantity);
    });

    return roundMoney(total);
}

function roundMoney(value) {
    return Math.round(Number(value) * 100) / 100;
}

function renderCart() {
    const box = document.getElementById('cartItems');
    const totalNode = document.getElementById('cartTotal');

    if (!box || !totalNode) {
        return;
    }

    if (!cart.size) {
        box.innerHTML = '<p class="text-muted mb-0">Cart is empty.</p>';
        totalNode.textContent = '0.00';
        return;
    }

    box.innerHTML = Array.from(cart.values()).map((line) => `
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="flex-grow-1">
                <div>${escapeHtml(line.name)}</div>
                <div class="small text-muted">${formatMoney(line.sell_price)} each</div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button type="button" class="btn btn-sm btn-outline-secondary js-qty" data-id="${line.id}" data-delta="-1">-</button>
                <span class="px-1">${line.quantity}</span>
                <button type="button" class="btn btn-sm btn-outline-secondary js-qty" data-id="${line.id}" data-delta="1">+</button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove" data-id="${line.id}">x</button>
        </div>
    `).join('');

    totalNode.textContent = formatMoney(previewTotal());
}

function addToCart(product) {
    const id = Number(product.id);
    const existing = cart.get(id);

    if (existing) {
        existing.quantity += 1;
        cart.set(id, existing);
    } else {
        cart.set(id, {
            id,
            name: product.name,
            sell_price: Number(product.sell_price),
            stock_qty: Number(product.stock_qty),
            quantity: 1
        });
    }

    renderCart();
}

function changeQty(id, delta) {
    const line = cart.get(Number(id));

    if (!line) {
        return;
    }

    line.quantity += delta;

    if (line.quantity < 1) {
        cart.delete(Number(id));
    } else {
        cart.set(Number(id), line);
    }

    renderCart();
}

function renderProducts(items) {
    const box = document.getElementById('productResults');

    if (!box) {
        return;
    }

    if (!items.length) {
        box.innerHTML = '<div class="col-12 text-muted">No products found.</div>';
        return;
    }

    items.forEach((product) => {
        productCache.set(Number(product.id), product);
    });

    box.innerHTML = items.map((product) => `
        <div class="col-md-6">
            <div class="border rounded p-3 h-100 bg-white">
                <div class="fw-semibold">${escapeHtml(product.name)}</div>
                <div class="small text-muted">${escapeHtml(product.barcode || 'No barcode')}</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span>${formatMoney(product.sell_price)}</span>
                    <span class="small text-muted">Stock ${product.stock_qty}</span>
                </div>
                <button
                    type="button"
                    class="btn btn-sm btn-dark w-100 mt-2 js-add-product"
                    data-id="${product.id}"
                    ${Number(product.stock_qty) < 1 ? 'disabled' : ''}>
                    Add
                </button>
            </div>
        </div>
    `).join('');
}

async function searchProducts() {
    const box = document.getElementById('productResults');
    const input = document.getElementById('productSearch');

    if (!box || !input) {
        return;
    }

    const params = new URLSearchParams();
    params.set('page', '1');

    if (input.value.trim()) {
        params.set('search', input.value.trim());
    }

    try {
        const response = await ApiClient.request('/api/products?' + params.toString());
        renderProducts((response.data && response.data.items) || []);
    } catch (error) {
        showCheckoutAlert(
            (error.payload && error.payload.message) || 'Unable to load products.',
            'danger'
        );
    }
}

function checkoutPayload() {
    return {
        items: Array.from(cart.values()).map((line) => ({
            product_id: line.id,
            quantity: line.quantity
        })),
        paid_amount: document.getElementById('paidAmount').value,
        payment_method: document.getElementById('paymentMethod').value,
        customer_id: null
    };
}

async function completeSale() {
    const button = document.getElementById('completeSale');

    if (!cart.size) {
        showCheckoutAlert('Add at least one product to the cart.', 'danger');
        return;
    }

    button.disabled = true;

    try {
        const response = await ApiClient.request('/api/sales', {
            method: 'POST',
            body: JSON.stringify(checkoutPayload())
        });

        window.location.href = saleUrl('/' + response.data.id);
    } catch (error) {
        showCheckoutAlert(
            (error.payload && error.payload.message) || 'Unable to complete sale.',
            'danger'
        );
        button.disabled = false;
    }
}

function renderSalePagination(meta) {
    const list = document.getElementById('salePagination');

    if (!list) {
        return;
    }

    if (!meta || meta.last_page <= 1) {
        list.innerHTML = '';
        return;
    }

    let html = '';

    for (let page = 1; page <= meta.last_page; page++) {
        html += `
            <li class="page-item ${page === meta.current_page ? 'active' : ''}">
                <button type="button" class="page-link js-sale-page" data-page="${page}">
                    ${page}
                </button>
            </li>
        `;
    }

    list.innerHTML = html;
}

async function loadSaleHistory(page) {
    const tbody = document.getElementById('saleRows');

    if (!tbody) {
        return;
    }

    try {
        const response = await ApiClient.request('/api/sales?page=' + page);
        const payload = response.data || {};
        const rows = payload.items || [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-muted">No sales yet.</td></tr>';
            renderSalePagination(payload);
            return;
        }

        tbody.innerHTML = rows.map((sale) => `
            <tr>
                <td>${escapeHtml(sale.invoice_no)}</td>
                <td>${escapeHtml(sale.cashier)}</td>
                <td>${escapeHtml(sale.customer && sale.customer.name ? sale.customer.name : 'Walk-in')}</td>
                <td>${formatMoney(sale.total_amount)}</td>
                <td>${formatMoney(sale.paid_amount)}</td>
                <td>${escapeHtml(sale.payment_method)}</td>
                <td>${escapeHtml(sale.created_at)}</td>
                <td class="text-end">
                    <a href="${saleUrl('/' + sale.id)}" class="btn btn-sm btn-outline-dark">
                        Receipt
                    </a>
                </td>
            </tr>
        `).join('');

        renderSalePagination(payload);
    } catch (error) {
        showSaleAlert(
            (error.payload && error.payload.message) || 'Unable to load sales.',
            'danger'
        );
    }
}

async function loadReceipt() {
    const card = document.getElementById('receiptCard');

    if (!card) {
        return;
    }

    try {
        const response = await ApiClient.request('/api/sales/' + card.dataset.id);
        const sale = response.data || {};

        document.getElementById('receiptInvoice').textContent = sale.invoice_no;
        document.getElementById('receiptDate').textContent = sale.created_at;
        document.getElementById('receiptCashier').textContent = sale.cashier;
        document.getElementById('receiptCustomer').textContent =
            sale.customer && sale.customer.name ? sale.customer.name : 'Walk-in';
        document.getElementById('receiptTotal').textContent = formatMoney(sale.total_amount);
        document.getElementById('receiptPaid').textContent = formatMoney(sale.paid_amount);
        document.getElementById('receiptChange').textContent = formatMoney(sale.change);
        document.getElementById('receiptMethod').textContent = sale.payment_method;

        document.getElementById('receiptItems').innerHTML = (sale.items || []).map((item) => `
            <tr>
                <td>${escapeHtml(item.name)}</td>
                <td>${item.quantity}</td>
                <td>${formatMoney(item.unit_price)}</td>
                <td class="text-end">${formatMoney(item.line_total)}</td>
            </tr>
        `).join('');
    } catch (error) {
        showSaleAlert(
            (error.payload && error.payload.message) || 'Unable to load receipt.',
            'danger'
        );
    }
}

document.addEventListener('click', async (event) => {
    if (event.target.closest('#searchProducts')) {
        await searchProducts();
        return;
    }

    const addButton = event.target.closest('.js-add-product');

    if (addButton) {
        const product = productCache.get(Number(addButton.dataset.id));

        if (product) {
            addToCart(product);
        }

        return;
    }

    const qtyButton = event.target.closest('.js-qty');

    if (qtyButton) {
        changeQty(qtyButton.dataset.id, Number(qtyButton.dataset.delta));
        return;
    }

    const removeButton = event.target.closest('.js-remove');

    if (removeButton) {
        cart.delete(Number(removeButton.dataset.id));
        renderCart();
        return;
    }

    if (event.target.closest('#completeSale')) {
        await completeSale();
        return;
    }

    const pageButton = event.target.closest('.js-sale-page');

    if (pageButton) {
        await loadSaleHistory(Number(pageButton.dataset.page));
    }
});

document.addEventListener('keydown', async (event) => {
    if (event.key !== 'Enter' || !event.target.closest('#productSearch')) {
        return;
    }

    event.preventDefault();
    await searchProducts();
});

document.addEventListener('DOMContentLoaded', async () => {
    renderCart();
    await searchProducts();
    await loadSaleHistory(1);
    await loadReceipt();
});
