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

function isoDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return year + '-' + month + '-' + day;
}

function defaultRange() {
    const to = new Date();
    const from = new Date();
    from.setDate(from.getDate() - 29);

    return {
        from: isoDate(from),
        to: isoDate(to)
    };
}

function showReportError(message) {
    const box = document.getElementById('reportError');

    if (!box) {
        return;
    }

    box.className = 'alert alert-danger';
    box.textContent = message;
}

function hideReportError() {
    const box = document.getElementById('reportError');

    if (!box) {
        return;
    }

    box.className = 'alert alert-danger d-none';
    box.textContent = '';
}

function queryString() {
    const params = new URLSearchParams();
    const from = document.getElementById('from');
    const to = document.getElementById('to');

    if (from && from.value) {
        params.set('from', from.value);
    }

    if (to && to.value) {
        params.set('to', to.value);
    }

    const query = params.toString();

    return query ? '?' + query : '';
}

function renderSummary(data) {
    document.getElementById('salesCount').textContent = data.sales_count;
    document.getElementById('revenue').textContent = formatMoney(data.revenue);
    document.getElementById('itemsSold').textContent = data.items_sold;
    document.getElementById('profit').textContent = formatMoney(data.profit);
}

function renderSalesByDay(rows) {
    const tbody = document.getElementById('salesByDay');

    if (!tbody) {
        return;
    }

    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-muted">No sales in this range.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map((row) => `
        <tr>
            <td>${escapeHtml(row.date)}</td>
            <td>${row.sales_count}</td>
            <td>${formatMoney(row.revenue)}</td>
        </tr>
    `).join('');
}

function renderTopProducts(rows) {
    const tbody = document.getElementById('topProducts');

    if (!tbody) {
        return;
    }

    if (!rows.length) {
        tbody.innerHTML = '<tr><td class="text-muted">No products sold in this range.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map((row) => `
        <tr>
            <td>${escapeHtml(row.name)}</td>
            <td>${row.units_sold} sold</td>
            <td class="text-end">${formatMoney(row.revenue)}</td>
        </tr>
    `).join('');
}

function renderLowStock(rows) {
    const tbody = document.getElementById('lowStock');

    if (!tbody) {
        return;
    }

    if (!rows.length) {
        tbody.innerHTML = '<tr><td class="text-muted">No low-stock products.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map((row) => `
        <tr>
            <td>${escapeHtml(row.name)}</td>
            <td>${escapeHtml(row.barcode || '')}</td>
            <td class="text-end">${row.stock_qty}</td>
        </tr>
    `).join('');
}

async function loadReports() {
    hideReportError();

    const query = queryString();

    try {
        const [summary, salesByDay, topProducts, lowStock] = await Promise.all([
            ApiClient.request('/api/reports/summary' + query),
            ApiClient.request('/api/reports/sales-by-day' + query),
            ApiClient.request('/api/reports/top-products' + query),
            ApiClient.request('/api/reports/low-stock')
        ]);

        renderSummary(summary.data || {});
        renderSalesByDay(salesByDay.data || []);
        renderTopProducts(topProducts.data || []);
        renderLowStock(lowStock.data || []);
    } catch (error) {
        if (error.status === 403) {
            showReportError('Reports unavailable for your role.');
            return;
        }

        showReportError(
            (error.payload && error.payload.message) || 'Unable to load reports.'
        );
    }
}

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('#reportFilters');

    if (!form) {
        return;
    }

    event.preventDefault();
    await loadReports();
});

document.addEventListener('DOMContentLoaded', async () => {
    const range = defaultRange();
    const from = document.getElementById('from');
    const to = document.getElementById('to');

    if (from && !from.value) {
        from.value = range.from;
    }

    if (to && !to.value) {
        to.value = range.to;
    }

    await loadReports();
});
