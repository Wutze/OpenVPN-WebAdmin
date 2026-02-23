<?php
use Micro\OpenvpnWebadmin\Core\Lang;

Lang::init();
$error = $_GET['error'] ?? '';
$cssVersion = (string) (is_file(__DIR__ . '/login.css') ? filemtime(__DIR__ . '/login.css') : time());
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(substr(Lang::getCurrent(), 0, 2), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="utf-8" />
    <title><?= Lang::get('_SITE_TITLE') ?> - <?= Lang::get('_LOGIN') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/fontawesome-free-7.0.1-web/css/all.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars(Url::op('loginasset', ['file' => 'login.css', 'v' => $cssVersion]), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<div class="container h-100">
    <div class="d-flex justify-content-center h-100">
        <div class="user_card">
            <div class="d-flex justify-content-center">
                <div class="brand_logo_container">
                    <div class="brand_logo d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center form_container">
                <form method="post" action="<?= htmlspecialchars(Url::op('checklogin'), ENT_QUOTES, 'UTF-8') ?>" class="w-100">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\Micro\OpenvpnWebadmin\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-1 px-2 mb-2 text-center">
                            <?= match($error) {
                                'empty'   => Lang::get('_LOGIN_EMPTY'),
                                'invalid' => Lang::get('_LOGIN_INVALID'),
                                default   => Lang::get('_LOGIN_ERROR')
                            }; ?>
                        </div>
                    <?php endif; ?>

                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="username" id="username" required autofocus placeholder="<?= Lang::get('_LOGIN_NAME') ?>">
                    </div>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input type="password" class="form-control" name="password" id="password" required placeholder="<?= Lang::get('_LOGIN_PASS') ?>">
                    </div>

                    <div class="d-flex justify-content-center mt-3 login_container">
                        <button type="submit" class="btn login_btn"><?= Lang::get('_LOGIN') ?></button>
                    </div>
                </form>
            </div>

            <div class="mt-4">
                <div class="d-flex justify-content-center links">
                    <?= Lang::get('_SITE_TITLE') ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
