document.addEventListener('DOMContentLoaded', async () => {
    const errorBox = document.getElementById('dashboardError');
    const kpiRow = document.getElementById('kpiRow');

    if (!errorBox) {
        return;
    }

    try {
        const response = await ApiClient.request(
            '/api/reports/summary'
        );

        const data = response.data;

        document.getElementById('salesCount').textContent =
            data.sales_count;

        document.getElementById('revenue').textContent =
            Number(data.revenue).toFixed(2);

        document.getElementById('itemsSold').textContent =
            data.items_sold;

        document.getElementById('profit').textContent =
            Number(data.profit).toFixed(2);

    } catch (error) {
        if (error.status === 403) {
            if (kpiRow) {
                kpiRow.classList.add('d-none');
            }

            errorBox.textContent = 'Reports unavailable for your role.';
            errorBox.classList.remove('d-none');
            errorBox.classList.remove('alert-danger');
            errorBox.classList.add('alert-secondary');
            return;
        }

        errorBox.textContent =
            (error.payload && error.payload.message) ||
            'Dashboard data could not be loaded.';

        errorBox.classList.remove('d-none');
    }
});
