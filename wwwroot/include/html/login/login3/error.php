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
 * @version		1.2.0
 * @todo			new issues report here please https://github.com/Wutze/OpenVPN-WebAdmin/issues
 */

(stripos($_SERVER['PHP_SELF'], basename(__FILE__)) === false) or die('access denied?');


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title><?php echo _SITE_NAME; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/png" href="images/favicon.png">
		<link rel="stylesheet" href="node_modules/admin-lte/dist/css/adminlte.min.css">
		<link rel="stylesheet" href="node_modules/admin-lte/plugins/fontawesome-free/css/all.min.css">		
		<script src="node_modules/jquery/dist/jquery.min.js"></script>
	  <link rel="stylesheet" type="text/css" href="include/html/login/<?php echo _LOGINSITE; ?>/login.css">
</head>
<!--Coded with love by Mutiullah Samim-->
	<body>
		<div class="container h-100">
			<div class="d-flex justify-content-center h-100">
				<div class="user_card error-msg">
					<div class="d-flex justify-content-center">
						<div class="brand_logo_container">
							<img src="images/ovpn-original.png" class="brand_logo" alt="Logo">
						</div>
					</div>
					<div class="justify-content-center form_container">
            <div class="">
              <p class="login-box-msg"><?php echo GET_Lang::nachricht("_ERROR") ?></p>
            </div>
            <div class="">
              <p class="login-box-msg"><a href="/"><?php echo GET_Lang::nachricht("_HOME") ?></a></p>
            </div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>