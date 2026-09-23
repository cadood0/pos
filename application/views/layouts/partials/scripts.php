<script>
window.APP = {
    baseUrl: <?= json_encode(base_url()) ?>,
    loginUrl: <?= json_encode(site_url('login')) ?>,
    dashboardUrl: <?= json_encode(site_url('dashboard')) ?>,
    logoutUrl: <?= json_encode(site_url('logout-ui')) ?>
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/api.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
