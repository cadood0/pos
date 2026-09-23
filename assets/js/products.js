function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function showAlert(message, type) {
    const box = document.getElementById('productAlert');

    if (!box) {
        return;
    }

    box.className = 'alert alert-' + type;
    box.textContent = message;
}

function productUrl(path) {
    return window.APP.productsUrl.replace(/\/$/, '') + path;
}

function formatMoney(value) {
    return Number(value).toFixed(2);
}

function currentFilters() {
    const search = document.getElementById('search');
    const category = document.getElementById('categoryFilter');

    return {
        search: search ? search.value.trim() : '',
        category_id: category ? category.value : ''
    };
}

function productsQuery(page) {
    const filters = currentFilters();
    const params = new URLSearchParams();

    params.set('page', String(page));

    if (filters.search) {
        params.set('search', filters.search);
    }

    if (filters.category_id) {
        params.set('category_id', filters.category_id);
    }

    return params.toString();
}

async function fillCategoryOptions(select, selectedId) {
    if (!select) {
        return;
    }

    const response = await ApiClient.request('/api/categories');
    const rows = response.data || [];

    const currentFirst = select.querySelector('option[value=""]');
    select.innerHTML = '';

    if (currentFirst) {
        select.appendChild(currentFirst);
    } else {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Select category';
        select.appendChild(option);
    }

    rows.forEach((category) => {
        const option = document.createElement('option');
        option.value = String(category.id);
        option.textContent = category.name;
        select.appendChild(option);
    });

    if (selectedId !== undefined && selectedId !== null && selectedId !== '') {
        select.value = String(selectedId);
    }
}

async function loadCategoryFilter() {
    const select = document.getElementById('categoryFilter');

    if (!select) {
        return;
    }

    try {
        await fillCategoryOptions(select, select.value);
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to load categories.',
            'danger'
        );
    }
}

function renderPagination(meta) {
    const list = document.getElementById('pagination');

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
                <button type="button" class="page-link js-page" data-page="${page}">
                    ${page}
                </button>
            </li>
        `;
    }

    list.innerHTML = html;
}

async function loadProducts(page) {
    const tbody = document.getElementById('productRows');

    if (!tbody) {
        return;
    }

    try {
        const response = await ApiClient.request('/api/products?' + productsQuery(page));
        const rows = response.items || [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-muted">No products found.</td></tr>';
            renderPagination(response);
            return;
        }

        tbody.innerHTML = rows.map((product) => `
            <tr>
                <td>${escapeHtml(product.name)}</td>
                <td>${escapeHtml(product.barcode || '')}</td>
                <td>${escapeHtml(product.category && product.category.name)}</td>
                <td>${formatMoney(product.cost_price)}</td>
                <td>${formatMoney(product.sell_price)}</td>
                <td>${product.stock_qty}</td>
                <td class="text-end">
                    <a href="${productUrl('/' + product.id + '/edit')}"
                       class="btn btn-sm btn-outline-dark">
                        Edit
                    </a>
                    <button
                        class="btn btn-sm btn-outline-danger js-delete-product"
                        data-id="${product.id}">
                        Delete
                    </button>
                </td>
            </tr>
        `).join('');

        renderPagination(response);
        tbody.dataset.page = String(response.current_page || page);
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to load products.',
            'danger'
        );
    }
}

function collectProductPayload(form) {
    return {
        name: form.elements.name.value,
        barcode: form.elements.barcode.value,
        category_id: form.elements.category_id.value,
        cost_price: form.elements.cost_price.value,
        sell_price: form.elements.sell_price.value,
        stock_qty: form.elements.stock_qty.value
    };
}

function showCreatePriceHint(form) {
    const hint = form.querySelector('.js-price-hint');

    if (!hint) {
        return;
    }

    const cost = parseFloat(form.elements.cost_price.value);
    const sell = parseFloat(form.elements.sell_price.value);

    if (!Number.isNaN(cost) && !Number.isNaN(sell) && sell < cost) {
        hint.textContent = 'Sell price cannot be lower than cost price.';
        hint.classList.remove('d-none');
        return;
    }

    hint.textContent = '';
    hint.classList.add('d-none');
}

async function loadProductFormCategories(form, selectedId) {
    if (!form) {
        return;
    }

    await fillCategoryOptions(form.elements.category_id, selectedId);
}

async function loadProductEdit() {
    const form = document.getElementById('productEditForm');

    if (!form) {
        return;
    }

    try {
        const product = await ApiClient.request('/api/products/' + form.dataset.id);

        form.elements.name.value = product.name;
        form.elements.barcode.value = product.barcode || '';
        form.elements.cost_price.value = product.cost_price;
        form.elements.sell_price.value = product.sell_price;
        form.elements.stock_qty.value = product.stock_qty;

        await loadProductFormCategories(form, product.category && product.category.id);
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to load product.',
            'danger'
        );
    }
}

document.addEventListener('input', (event) => {
    const form = event.target.closest('#productCreateForm');

    if (!form) {
        return;
    }

    if (event.target.name === 'cost_price' || event.target.name === 'sell_price') {
        showCreatePriceHint(form);
    }
});

document.addEventListener('submit', async (event) => {
    const createForm = event.target.closest('#productCreateForm');
    const editForm = event.target.closest('#productEditForm');

    if (!createForm && !editForm) {
        return;
    }

    event.preventDefault();

    const form = createForm || editForm;

    if (createForm) {
        showCreatePriceHint(form);
    }

    try {
        if (createForm) {
            await ApiClient.request('/api/products', {
                method: 'POST',
                body: JSON.stringify(collectProductPayload(form))
            });
        } else {
            await ApiClient.request('/api/products/' + form.dataset.id, {
                method: 'PUT',
                body: JSON.stringify(collectProductPayload(form))
            });
        }

        window.location.href = window.APP.productsUrl;
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to save product.',
            'danger'
        );
    }
});

document.addEventListener('click', async (event) => {
    const pageButton = event.target.closest('.js-page');

    if (pageButton) {
        await loadProducts(Number(pageButton.dataset.page));
        return;
    }

    const apply = event.target.closest('#applyFilters');

    if (apply) {
        await loadProducts(1);
        return;
    }

    const button = event.target.closest('.js-delete-product');

    if (!button) {
        return;
    }

    if (!confirm('Delete this product?')) {
        return;
    }

    try {
        await ApiClient.request(
            '/api/products/' + button.dataset.id,
            { method: 'DELETE' }
        );

        const tbody = document.getElementById('productRows');
        const page = tbody && tbody.dataset.page ? Number(tbody.dataset.page) : 1;

        await loadProducts(page);
        showAlert('Product deleted successfully.', 'success');
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Delete failed.',
            'danger'
        );
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    await loadCategoryFilter();
    await loadProducts(1);

    const createForm = document.getElementById('productCreateForm');

    if (createForm) {
        try {
            await loadProductFormCategories(createForm);
        } catch (error) {
            showAlert(
                (error.payload && error.payload.message) || 'Unable to load categories.',
                'danger'
            );
        }
    }

    await loadProductEdit();
});
