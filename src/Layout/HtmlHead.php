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
?>
<!-- HtmlHead.php -->
<!DOCTYPE html>
<html lang="<?= Lang::get('_LANG_CODE') ?>">
<head>
    <meta charset="UTF-8">
    <title><?= Lang::get('_SITE_TITLE') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/bootstrap5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/bootstrap-table/dist/bootstrap-table.min.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/int/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo _SITETOOLS ?>/int/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="/css/index.css">
    <script src="<?php echo _SITETOOLS ?>/jquery/jquery-min.js"></script>
    <script src="<?php echo _SITETOOLS ?>/bootstrap-table/dist/bootstrap-table.min.js"></script>
    <script src="<?php echo _SITETOOLS ?>/int/flatpickr/dist/flatpickr.min.js"></script>
    <script src="<?php echo _SITETOOLS ?>/int/flatpickr/dist/l10n/de.js"></script>

</head>
<body class="hold-transition sidebar-mini">
    <?php \Micro\OpenvpnWebadmin\Core\Debug::render(); ?>