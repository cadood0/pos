<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Login</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">
<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow-sm" style="max-width: 420px; width: 100%;">
        <div class="card-body p-4">
            <h1 class="h4 mb-1">Shop POS</h1>
            <p class="text-muted mb-4">Sign in to continue.</p>

            <div id="loginError" class="alert alert-danger d-none"></div>

            <form id="loginForm">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Device name</label>
                    <input
                        type="text"
                        name="device_name"
                        class="form-control"
                        value="pos-terminal"
                        maxlength="100"
                    >
                </div>

                <button class="btn btn-dark w-100" type="submit">
                    Sign in
                </button>
            </form>
        </div>
    </div>
</div>

<script>
window.APP = {
    baseUrl: <?= json_encode(base_url()) ?>,
    loginUrl: <?= json_encode(site_url('login')) ?>,
    dashboardUrl: <?= json_encode(site_url('dashboard')) ?>
};
</script>
<script src="<?= base_url('assets/js/api.js') ?>"></script>
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
</body>
</html>
