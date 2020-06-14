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

?>
<!-- Start HTML-Code -->
<body class="hold-transition sidebar-mini layout-boxed">
<div class="wrapper">
  <!-- Navbar top -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
<?php
/** Navbar Top left */
include("include/html/content/main-navbar-left.php");
/** Navbar Top right */
include("include/html/content/main-navbar-right.php");
?>
  </nav>
  <!-- /.navbar top -->
<?php
/** Sidebar left */
include("include/html/content/main-sidebar.php");
/** Maincontent all */
include("include/html/content/main-content.php");
?>

