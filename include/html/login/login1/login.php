<?php
/**
 * this File is part of OpenVPN-Admin - (c) 2020 OpenVPN-Admin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * Original Script from: https://github.com/Chocobozzz/OpenVPN-Admin
 *
 * @fork      https://github.com/Wutze/OpenVPN-Admin
 * @author    Wutze
 * @copyright 2020 OpenVPN-Admin
 * @license   https://www.gnu.org/licenses/agpl-3.0.en.html
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

include (REAL_BASE_DIR.'/include/html/login/login.functions.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title><?php echo _SITE_NAME; ?></title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="css/index.css" type="text/css" />
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="node_modules/admin-lte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="node_modules/admin-lte/dist/css/adminlte.min.css">
    <script src="node_modules/ionicons/dist/ionicons.js"></script>
  </head>
  <body class="hold-transition login-page">

    <div class="login-box">
    <div class="login-logo">

        <a href="/"><img src="./images/logo.png" />&nbsp;<?php echo _SITE_NAME; ?></a>
    </div>

    <div class="login-box-body">
      <p class="login-box-msg"><?php echo GET_Lang::nachricht("_LOGIN_DATA") ?></p>

      <form action="/" method="post">
        <div class="input-group mb-3">
          <?php echo username(); ?>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <?php echo password(); ?>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
              <?php echo hiddenfields()."\n"; ?>
                <label for="remember">
                  <?php echo GET_Lang::nachricht('_LOGIN_SAVE')."\n"; ?>
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
            <?php echo button('btn-primary btn-block'); ?>
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
    </div>
  </body>
</html>