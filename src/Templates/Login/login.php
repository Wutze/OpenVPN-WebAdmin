<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020/2025 OpenVPN-WebAdmin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * @author      Wutze
 * @copyright   2025 OpenVPN-WebAdmin
 * @link        https://github.com/Wutze/OpenVPN-WebAdmin
 * @see         Internal Documentation ~/doc/
 * @version     2.0.0
 */

use Micro\OpenvpnWebadmin\Core\Lang;

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?= substr(Lang::getCurrent(), 0, 2) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= Lang::get('_SITE_TITLE') ?> - <?= Lang::get('_LOGIN') ?></title>
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/fontawesome-free-7.0.1-web/css/all.min.css">
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh;">
    <?php \Micro\OpenvpnWebadmin\Core\Debug::render(); ?>
<div class="card shadow-sm p-4" style="min-width: 360px;">
    
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

<script src="<?php echo _SITETOOLS ?>/jquery/jquery-min.js"></script>
<script src="<?php echo _SITETOOLS ?>/bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>
