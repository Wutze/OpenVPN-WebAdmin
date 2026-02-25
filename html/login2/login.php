<?php
use Micro\OpenvpnWebadmin\Core\Lang;

Lang::init();
$error = $_GET['error'] ?? '';
$cssVersion = (string) (is_file(__DIR__ . '/login.css') ? filemtime(__DIR__ . '/login.css') : time());
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(substr(Lang::getCurrent(), 0, 2), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Lang::get('_SITE_TITLE') ?> - <?= Lang::get('_LOGIN') ?></title>
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/fontawesome-free-7.0.1-web/css/all.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars(Url::op('loginasset', ['file' => 'login.css', 'v' => $cssVersion]), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-center h-100">
        <div class="card">
            <div class="card-header">
                <h3><?= Lang::get('_LOGIN') ?></h3>
                <div class="d-flex justify-content-end social_icon">
                    <span><i class="fab fa-instagram"></i></span>
                    <span><a href="https://github.com/Wutze"><i class="fab fa-github"></i></a></span>
                    <span><a href="https://twitter.de/huwutze"><i class="fab fa-twitter-square"></i></a></span>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger m-3 mb-0 text-center py-2">
                    <?= match($error) {
                        'empty'   => Lang::get('_LOGIN_EMPTY'),
                        'invalid' => Lang::get('_LOGIN_INVALID'),
                        default   => Lang::get('_LOGIN_ERROR')
                    }; ?>
                </div>
            <?php endif; ?>

            <div class="card-body">
                <form method="post" action="<?= htmlspecialchars(Url::op('checklogin'), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\Micro\OpenvpnWebadmin\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="username" id="username" required autofocus placeholder="<?= Lang::get('_LOGIN_NAME') ?>">
                    </div>
                    <div class="input-group form-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="password" class="form-control" name="password" id="password" required placeholder="<?= Lang::get('_LOGIN_PASS') ?>">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn login_btn float-right"><?= Lang::get('_LOGIN') ?></button>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-center links">
                    <?= Lang::get('_SITE_TITLE') ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
