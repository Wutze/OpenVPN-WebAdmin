<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020 OpenVPN-WebAdmin
 *
 * NOTICE OF LICENSE
 *
 * GNU AFFERO GENERAL PUBLIC LICENSE V3
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.en.html
 *
 * @fork Original Idea and parts in this script from: https://github.com/Chocobozzz/OpenVPN-Admin
 *
 * @author    Wutze
 * @copyright 2020 OpenVPN-WebAdmin
 * @link			https://github.com/Wutze/OpenVPN-WebAdmin
 * @see				Internal Documentation ~/doc/
 * @version		1.3.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');

include (REAL_BASE_DIR.'/include/html/login/login.functions.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title><?php echo _SITE_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="node_modules/admin-lte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="node_modules/admin-lte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="css/index.css" type="text/css" />
  </head>
  <body class="hold-transition login-page">
  <?php echo noscript(); ?>
    <div class="login-box">
      <div class="login-logo">
        <a href="/"><img src="images/logo.png" />&nbsp;<?php echo _SITE_NAME; ?></a>
      </div>
      <div class="login-box-body">
        <p class="login-box-msg"><?php echo GET_Lang::nachricht("_LOGIN_DATA") ?></p>
        <form action="/" method="post">
          <div class="input-group mb-3">
            <?php echo username()."\n"; ?>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <?php echo password()."\n"; ?>
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
              </div>
            </div>
            <div class="col-4">
              <?php echo button('btn-primary btn-block'); ?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>