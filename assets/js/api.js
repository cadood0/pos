const ApiClient = {
    getToken() {
        return localStorage.getItem('access_token');
    },

    setToken(token) {
        localStorage.setItem('access_token', token);
    },

    clearToken() {
        localStorage.removeItem('access_token');
    },

    baseUrl() {
        const configured = window.APP && window.APP.baseUrl
            ? window.APP.baseUrl
            : '/';

        return configured.replace(/\/$/, '');
    },

    url(path) {
        if (/^https?:\/\//i.test(path)) {
            return path;
        }

        return this.baseUrl() + (path.charAt(0) === '/' ? path : '/' + path);
    },

    async request(url, options = {}) {
        const headers = {
            'Accept': 'application/json',
            ...(options.headers || {})
        };

        const token = this.getToken();

        if (token) {
            headers.Authorization = `Bearer ${token}`;
        }

        if (options.body && !(options.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
        }

        const response = await fetch(this.url(url), {
            ...options,
            headers
        });

        let payload = null;

        try {
            payload = await response.json();
        } catch (_) {
            payload = null;
        }

        if (!response.ok) {
            const error = new Error(
                (payload && payload.message) || 'Request failed.'
            );

            error.status = response.status;
            error.payload = payload;

            throw error;
        }

        return payload;
    }
};
