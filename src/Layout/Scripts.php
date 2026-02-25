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
    <script nonce="<?= htmlspecialchars(defined('_CSP_NONCE') ? _CSP_NONCE : '', ENT_QUOTES, 'UTF-8') ?>">
      window.I18N = <?= json_encode(Lang::getAll(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
      window.CSRF_TOKEN = <?= json_encode(\Micro\OpenvpnWebadmin\Core\Session::getCsrfToken(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
      window.URL_REWRITE = <?= json_encode(defined('_URL_REWRITE') && _URL_REWRITE === true, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
      window.APP_BASE_PATH = <?= json_encode(defined('_APP_BASE_PATH') ? (string)_APP_BASE_PATH : '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

      (function () {
        if (!window.URL_REWRITE) return;

        function mapOpUrl(raw) {
          if (typeof raw !== 'string' || raw.indexOf('?op=') !== 0) return raw;
          var query = raw.slice(1);
          var params = new URLSearchParams(query);
          var op = params.get('op');
          if (!op) return raw;
          params.delete('op');

          if (op === 'setlang' && params.has('lang')) {
            var lang = params.get('lang') || '';
            params.delete('lang');
            var tailLang = params.toString();
            return (window.APP_BASE_PATH || '') + '/setlang/' + encodeURIComponent(lang) + (tailLang ? '?' + tailLang : '');
          }

          if (op === 'dashboard' || op === 'main') {
            var rootTail = params.toString();
            return (window.APP_BASE_PATH || '') + '/' + (rootTail ? '?' + rootTail : '');
          }

          var tail = params.toString();
          return (window.APP_BASE_PATH || '') + '/' + encodeURIComponent(op) + (tail ? '?' + tail : '');
        }

        function rewriteStaticLinks() {
          document.querySelectorAll('a[href^=\"?op=\"]').forEach(function (a) {
            a.setAttribute('href', mapOpUrl(a.getAttribute('href') || ''));
          });
          document.querySelectorAll('form[action^=\"?op=\"]').forEach(function (form) {
            form.setAttribute('action', mapOpUrl(form.getAttribute('action') || ''));
          });
        }

        document.addEventListener('click', function (ev) {
          var el = ev.target;
          if (!(el instanceof Element)) return;
          var a = el.closest('a[href^=\"?op=\"]');
          if (!a) return;
          a.setAttribute('href', mapOpUrl(a.getAttribute('href') || ''));
        }, true);

        document.addEventListener('submit', function (ev) {
          var form = ev.target;
          if (!(form instanceof HTMLFormElement)) return;
          var action = form.getAttribute('action') || '';
          if (action.indexOf('?op=') !== 0) return;
          form.setAttribute('action', mapOpUrl(action));
        }, true);

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', rewriteStaticLinks);
        } else {
          rewriteStaticLinks();
        }
      })();
    </script>
    <script src="<?= htmlspecialchars((defined('_APP_BASE_PATH') ? _APP_BASE_PATH : '') . '/js/user.js', ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php if (\Micro\OpenvpnWebadmin\Core\Session::isAdmin()): ?>
    <script src="<?= htmlspecialchars((defined('_APP_BASE_PATH') ? _APP_BASE_PATH : '') . '/js/admin.js', ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php endif; ?>
