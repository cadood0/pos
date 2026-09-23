document.addEventListener('DOMContentLoaded', async () => {
    const toast = document.getElementById('appToast');

    if (!ApiClient.getToken()) {
        window.location.href = window.APP.loginUrl;
        return;
    }

    try {
        const response = await ApiClient.request('/api/me');
        const user = response.data;

        const nameNode = document.getElementById('navUserName');

        if (nameNode && user && user.name) {
            nameNode.textContent = user.name;
        }
    } catch (error) {
        if (error.status === 401) {
            ApiClient.clearToken();
            window.location.href = window.APP.loginUrl;
            return;
        }

        if (toast) {
            toast.textContent = (error.payload && error.payload.message) || 'Unable to load your session.';
            toast.classList.remove('d-none');
        }
    }

    const logoutBtn = document.getElementById('logoutBtn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', async (event) => {
            event.preventDefault();

            try {
                await ApiClient.request('/api/logout', { method: 'POST' });
            } catch (_) {}

            ApiClient.clearToken();
            window.location.href = window.APP.loginUrl;
        });
    }
});
