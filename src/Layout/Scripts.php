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
    <!-- zusätzliche Scripte -->
    <!-- jQuery lokal -->
    <script src="<?php echo _SITETOOLS ?>/jquery/jquery-min.js"></script>
    <!-- Bootstrap lokal -->
    <script src="<?php echo _SITETOOLS ?>/bootstrap5/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE lokal -->
    <script src="<?php echo _SITETOOLS ?>/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
    <!-- Optional: bootstrap-table, datepicker lokal -->
    <script src="<?php echo _SITETOOLS ?>/bootstrap-table/dist/bootstrap-table.min.js"></script>
    <script src="<?php echo _SITETOOLS ?>/int/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script>
      window.I18N = <?= json_encode(Lang::getAll(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
      window.CSRF_TOKEN = <?= json_encode(\Micro\OpenvpnWebadmin\Core\Session::getCsrfToken(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    </script>
    <script src="/js/admin.js"></script>
