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
		<link rel="stylesheet" href="node_modules/admin-lte/dist/css/adminlte.min.css">
		<link rel="stylesheet" href="node_modules/admin-lte/plugins/fontawesome-free/css/all.min.css">		
		<script src="node_modules/jquery/dist/jquery.min.js"></script>
	  <link rel="stylesheet" type="text/css" href="include/html/login/<?php echo _LOGINSITE; ?>/login.css">
</head>
<!--Coded with love by Mutiullah Samim-->
	<body>
		<div class="container h-100">
			<div class="d-flex justify-content-center h-100">
				<div class="user_card">
					<div class="d-flex justify-content-center">
						<div class="brand_logo_container">
							<img src="images/ovpn-original.png" class="brand_logo" alt="Logo">
						</div>
					</div>
					<div class="d-flex justify-content-center form_container">
						<form>
							<div class="input-group mb-3">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-user"></i></span>
								</div>
								<?php echo username(); ?>
							</div>
							<div class="input-group mb-2">
								<div class="input-group-append">
									<span class="input-group-text"><i class="fas fa-key"></i></span>
								</div>
								<?php echo password(); ?>
							</div>
							<div class="form-group">
								<div class="custom-control custom-checkbox">
								<?php echo hiddenfields()."\n"; ?>
								</div>
							</div>
								<div class="d-flex justify-content-center mt-3 login_container">
								<?php echo button(''); ?>
						</div>
						</form>
					</div>
			
					<div class="mt-4">
						<div class="d-flex justify-content-center links">
							Don't have an account? <a href="#" class="ml-2">Sign Up</a>
						</div>
						<div class="d-flex justify-content-center links">
							<a href="#">Forgot your password?</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>