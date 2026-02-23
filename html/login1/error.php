<?php
use Micro\OpenvpnWebadmin\Core\Lang;

Lang::init();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(substr(Lang::getCurrent(), 0, 2), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Lang::get('_SITE_TITLE') ?> - <?= Lang::get('_ERROR') ?></title>
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars(Url::op('loginasset', ['file' => 'style.css']), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="login1-page">
<div class="login1-wrap">
    <div class="login1-card card shadow-sm border-0">
        <div class="card-body p-4 p-md-5 text-center">
            <h2 class="mb-3"><?= Lang::get('_ERROR') ?></h2>
            <p class="text-muted mb-4"><?= Lang::get('_ERROR_PAGE_NOT_FOUND') ?></p>
            <a class="btn btn-primary" href="<?= htmlspecialchars(Url::op('login'), ENT_QUOTES, 'UTF-8') ?>"><?= Lang::get('_BACK_TO_LOGIN') ?></a>
        </div>
    </div>
</div>
</body>
</html>
