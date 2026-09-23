<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Signing out</title>
</head>
<body>
<p>Signing out...</p>
<script>
window.APP = {
    baseUrl: <?= json_encode(base_url()) ?>,
    loginUrl: <?= json_encode(site_url('login')) ?>
};
</script>
<script src="<?= base_url('assets/js/api.js') ?>"></script>
<script>
(async () => {
    try {
        await ApiClient.request('/api/logout', { method: 'POST' });
    } catch (_) {}

    ApiClient.clearToken();
    window.location.href = window.APP.loginUrl;
})();
</script>
</body>
</html>
