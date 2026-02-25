<?php
use Micro\OpenvpnWebadmin\Core\Lang;

Lang::init();
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(substr(Lang::getCurrent(), 0, 2), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Lang::get('_SITE_TITLE') ?> - <?= Lang::get('_LOGIN') ?></title>
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/fontawesome-free-7.0.1-web/css/all.min.css">
    <link rel="stylesheet" href="?op=loginasset&amp;file=style.css">
</head>
<body class="login1-page">
<div class="login1-wrap">
    <div class="login1-card card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <h3 class="text-center mb-3"><i class="fa-solid fa-lock"></i> <?= Lang::get('_WELCOME') ?></h3>

            <?php if ($error): ?>
                <div class="alert alert-danger text-center py-2">
                    <?= match($error) {
                        'empty'   => Lang::get('_LOGIN_EMPTY'),
                        'invalid' => Lang::get('_LOGIN_INVALID'),
                        default   => Lang::get('_LOGIN_ERROR')
                    }; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="?op=checklogin">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\Micro\OpenvpnWebadmin\Core\Session::getCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="mb-3">
                    <label for="username" class="form-label"><?= Lang::get('_LOGIN_NAME') ?></label>
                    <input type="text" name="username" id="username" class="form-control" autofocus required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><?= Lang::get('_LOGIN_PASS') ?></label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-right-to-bracket"></i> <?= Lang::get('_LOGIN') ?>
                </button>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo _SITETOOLS ?>/jquery/jquery-min.js"></script>
<script src="<?php echo _SITETOOLS ?>/bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>
