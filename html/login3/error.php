<?php
use Micro\OpenvpnWebadmin\Core\Lang;

Lang::init();
$cssVersion = (string) (is_file(__DIR__ . '/login.css') ? filemtime(__DIR__ . '/login.css') : time());
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(substr(Lang::getCurrent(), 0, 2), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="utf-8" />
    <title><?= Lang::get('_SITE_TITLE') ?> - <?= Lang::get('_ERROR') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/fontawesome-free-7.0.1-web/css/all.min.css">
    <link rel="stylesheet" href="?op=loginasset&amp;file=login.css&amp;v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="container h-100">
    <div class="d-flex justify-content-center h-100">
        <div class="user_card error-msg">
            <div class="d-flex justify-content-center">
                <div class="brand_logo_container">
                    <div class="brand_logo d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>
            <div class="justify-content-center form_container text-center">
                <p class="login-box-msg"><?= Lang::get('_ERROR') ?></p>
                <p class="login-box-msg"><a href="?op=login"><?= Lang::get('_BACK_TO_LOGIN') ?></a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
