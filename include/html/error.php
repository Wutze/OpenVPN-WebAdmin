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
 * @version		1.0.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

 ?>
<body class="hold-transition login-page">

<div class="login-box">
  <div class="login-box-body">
    <div class="position-relative p-2 bg-gray" style="height: 180px">
      <div class="ribbon-wrapper ribbon-lg">
        <div class="ribbon bg-danger">
          ERROR
        </div>
      </div>
      Fehler <br>
      <small>Ein Fehler wurde stattgefunden.</small>
    </div>
    <a href="/" class="btn btn-info col-12" role="button"><?php echo GET_Lang::nachricht('_HOME'); ?></a>
  </div>
  <div class="col-12">
    <?php echo get_slogan(); ?>
  </div>
</div>

