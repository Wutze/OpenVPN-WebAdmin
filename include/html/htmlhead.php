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

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />

    <title><?php echo _SITE_NAME; ?></title>

    <link rel="stylesheet" href="node_modules/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css" type="text/css" />
    <link rel="stylesheet" href="node_modules/bootstrap-table/dist/bootstrap-table.min.css" type="text/css" />
    <link rel="stylesheet" href="node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" type="text/css" />
    <link rel="stylesheet" href="node_modules/bootstrap-table/dist/extensions/filter-control/bootstrap-table-filter-control.css" type="text/css" />
    <link rel="stylesheet" href="css/index.css" type="text/css" />
    <link rel="stylesheet" href="node_modules/admin-lte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="node_modules/admin-lte/plugins/fontawesome-free/css/all.min.css">
    <link rel="icon" type="image/png" href="images/favicon.png">

    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="node_modules/admin-lte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="node_modules/admin-lte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="node_modules/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <script src="node_modules/ionicons/dist/ionicons.js"></script>
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/popper.js/dist/umd/popper.min.js"></script>
  </head>
