<?php
use Micro\OpenvpnWebadmin\Core\Lang;

Lang::init();
$cssVersion = (string) (is_file(__DIR__ . '/login.css') ? filemtime(__DIR__ . '/login.css') : time());
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(substr(Lang::getCurrent(), 0, 2), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Lang::get('_SITE_TITLE') ?> - <?= Lang::get('_ERROR') ?></title>
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/fontawesome-free-7.0.1-web/css/all.min.css">
    <link rel="stylesheet" href="?op=loginasset&amp;file=login.css&amp;v=<?= htmlspecialchars($cssVersion, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-center h-100">
        <div class="card error-msg">
            <div class="card-header">
                <div class="d-flex justify-content-end social_icon">
                    <span><i class="fab fa-instagram"></i></span>
                    <span><a href="https://github.com/Wutze"><i class="fab fa-github"></i></a></span>
                    <span><a href="https://twitter.de/huwutze"><i class="fab fa-twitter-square"></i></a></span>
                </div>
            </div>
            <div class="card-body text-center">
                <p class="login-box-msg"><?= Lang::get('_ERROR') ?></p>
                <p class="login-box-msg"><a href="?op=login"><?= Lang::get('_BACK_TO_LOGIN') ?></a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
