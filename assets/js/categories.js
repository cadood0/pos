function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function showAlert(message, type) {
    const box = document.getElementById('categoryAlert');

    if (!box) {
        return;
    }

    box.className = 'alert alert-' + type;
    box.textContent = message;
}

function categoryUrl(path) {
    return window.APP.categoriesUrl.replace(/\/$/, '') + path;
}

async function loadCategories() {
    const tbody = document.getElementById('categoryRows');

    if (!tbody) {
        return;
    }

    try {
        const response = await ApiClient.request('/api/categories');
        const rows = response.data || [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-muted">No categories yet.</td></tr>';
            return;
        }

        tbody.innerHTML = rows.map(category => `
            <tr>
                <td>${category.id}</td>
                <td>${escapeHtml(category.name)}</td>
                <td>${category.products_count}</td>
                <td class="text-end">
                    <a href="${categoryUrl('/' + category.id)}"
                       class="btn btn-sm btn-outline-secondary">
                        View
                    </a>
                    <a href="${categoryUrl('/' + category.id + '/edit')}"
                       class="btn btn-sm btn-outline-dark">
                        Edit
                    </a>
                    <button
                        class="btn btn-sm btn-outline-danger js-delete-category"
                        data-id="${category.id}">
                        Delete
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to load categories.',
            'danger'
        );
    }
}

async function loadCategoryShow() {
    const box = document.getElementById('categoryShow');

    if (!box) {
        return;
    }

    try {
        const response = await ApiClient.request('/api/categories/' + box.dataset.id);
        const category = response.data;

        document.getElementById('categoryName').textContent = category.name;
        document.getElementById('categoryProducts').textContent = category.products_count;
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to load category.',
            'danger'
        );
    }
}

async function loadCategoryEdit() {
    const form = document.getElementById('categoryEditForm');

    if (!form) {
        return;
    }

    try {
        const response = await ApiClient.request('/api/categories/' + form.dataset.id);
        form.elements.name.value = response.data.name;
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to load category.',
            'danger'
        );
    }
}

document.addEventListener('submit', async (event) => {
    const createForm = event.target.closest('#categoryCreateForm');
    const editForm = event.target.closest('#categoryEditForm');

    if (!createForm && !editForm) {
        return;
    }

    event.preventDefault();

    const form = createForm || editForm;
    const name = form.elements.name.value;

    try {
        if (createForm) {
            await ApiClient.request('/api/categories', {
                method: 'POST',
                body: JSON.stringify({ name })
            });
        } else {
            await ApiClient.request('/api/categories/' + form.dataset.id, {
                method: 'PUT',
                body: JSON.stringify({ name })
            });
        }

        window.location.href = window.APP.categoriesUrl;
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Unable to save category.',
            'danger'
        );
    }
});

document.addEventListener('click', async (event) => {
    const button = event.target.closest('.js-delete-category');

    if (!button) {
        return;
    }

    if (!confirm('Delete this category?')) {
        return;
    }

    try {
        await ApiClient.request(
            '/api/categories/' + button.dataset.id,
            { method: 'DELETE' }
        );

        await loadCategories();
        showAlert('Category deleted.', 'success');
    } catch (error) {
        showAlert(
            (error.payload && error.payload.message) || 'Delete failed.',
            'danger'
        );
    }
});

document.addEventListener('DOMContentLoaded', () => {
    loadCategories();
    loadCategoryShow();
    loadCategoryEdit();
});
