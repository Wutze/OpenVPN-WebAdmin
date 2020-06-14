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
		<title><?php echo _SITE_NAME; ?></title>
    <link rel="icon" type="image/png" href="images/favicon.png">
		<link rel="stylesheet" href="node_modules/admin-lte/dist/css/adminlte.min.css">
		<link rel="stylesheet" href="node_modules/admin-lte/plugins/fontawesome-free/css/all.css">
		<link rel="stylesheet" type="text/css" href="include/html/login/<?php echo _LOGINSITE; ?>/login.css">
</head>
<body>
<div class="container">
	<div class="d-flex justify-content-center h-100">
		<div class="card">
			<div class="card-header">
				<h3>Sign In</h3>
				<div class="d-flex justify-content-end social_icon">
					<span><i class="fab fa-instagram"></i></span>
					<span><a href="https://github.com/Wutze"><i class="fab fa-github"></i></a></span>
					<span><a href="https://twitter.de/huwutze"><i class="fab fa-twitter-square"></i></a></span>
				</div>
			</div>
			<div class="card-body">
			<form action="/" method="post">
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-user"></i></span>
						</div>
						<?php echo username(); ?>
						
					</div>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-key"></i></span>
						</div>
						<?php echo password(); ?>
					</div>
					<div class="row align-items-center remember">
					<?php echo hiddenfields()."\n"; ?>
					</div>
					<div class="form-group">
						<?php echo button('float-right'); ?>
					</div>
				</form>
			</div>
			<div class="card-footer">
				<div class="d-flex justify-content-center links">
					Don't have an account?<a href="#">Sign Up</a>
				</div>
				<div class="d-flex justify-content-center">
					<a href="#">Forgot your password?</a>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
