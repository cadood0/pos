document.addEventListener('DOMContentLoaded', () => {
    if (ApiClient.getToken() && window.APP && window.APP.dashboardUrl) {
        window.location.href = window.APP.dashboardUrl;
        return;
    }

    const form = document.getElementById('loginForm');

    if (!form) return;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const errorBox = document.getElementById('loginError');

        errorBox.classList.add('d-none');
        errorBox.textContent = '';

        const body = Object.fromEntries(
            new FormData(form).entries()
        );

        try {
            const response = await ApiClient.request('/api/login', {
                method: 'POST',
                body: JSON.stringify(body)
            });

            ApiClient.setToken(response.data.token);

            window.location.href = window.APP.dashboardUrl;
        } catch (error) {
            errorBox.textContent =
                (error.payload && error.payload.message) || 'Unable to sign in.';

            errorBox.classList.remove('d-none');
        }
    });
});
