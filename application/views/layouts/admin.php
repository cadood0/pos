<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= html_escape(isset($title) ? $title : 'POS') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
<div class="app-shell">
    <?php $this->load->view('layouts/partials/sidebar'); ?>
    <div class="app-main">
        <?php $this->load->view('layouts/partials/navbar'); ?>
        <div id="appToast" class="alert alert-warning d-none mx-3 mt-3" role="status"></div>
        <div class="app-content">
            <?php $this->load->view($content); ?>
        </div>
    </div>
</div>
<?php $this->load->view('layouts/partials/scripts'); ?>
</body>
</html>
