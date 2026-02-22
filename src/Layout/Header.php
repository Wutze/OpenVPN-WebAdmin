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
<!-- Header.php -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light px-3">
    <?php $debugModal = $debugModal ?? null; ?>
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#">
                <i class="bi bi-list"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="?op=dashboard" class="nav-link"><?= Lang::get('_HOME') ?></a>
        </li>
        <?php if (is_array($debugModal) && !empty($debugModal['enabled'])): ?>
        <li class="nav-item d-none d-sm-inline-block">
            <button class="btn btn-outline-warning btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#debugModal">
                <i class="bi bi-bug"></i> Debug
            </button>
        </li>
        <?php endif; ?>
    </ul>

    <ul class="navbar-nav ms-auto">
        <?php
        $currentLang = $currentLang ?? Lang::getCurrent();
        $availableLangs = $availableLangs ?? [Lang::getCurrent()];
        $meta = Lang::getLanguageMeta($currentLang);
        ?>
        <li class="nav-item dropdown me-2">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <?= htmlspecialchars($meta['flag'], ENT_QUOTES, 'UTF-8') ?>
                <?= htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8') ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <?php foreach ($availableLangs as $langCode): ?>
                    <?php $langMeta = Lang::getLanguageMeta($langCode); ?>
                    <li>
                        <a class="dropdown-item<?= $langCode === $currentLang ? ' active' : '' ?>"
                           href="?op=setlang&amp;lang=<?= urlencode($langCode) ?>">
                            <?= htmlspecialchars($langMeta['flag'], ENT_QUOTES, 'UTF-8') ?>
                            <?= htmlspecialchars($langMeta['label'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li class="nav-item me-3 d-flex align-items-center">
            <span class="small text-muted">
                <?= htmlspecialchars((string)($user['name'] ?? Lang::get('_UNKNOWN_USER')), ENT_QUOTES, 'UTF-8') ?>
                (<?= htmlspecialchars((string)(($user['role'] ?? '') !== '' ? $user['role'] : Lang::get('_ROLE_USER')), ENT_QUOTES, 'UTF-8') ?>)
            </span>
        </li>
        <li class="nav-item">
            <button id="logoutBtn" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> <?= Lang::get('_LOGOUT_LABEL') ?>
            </button>
        </li>
    </ul>
</nav>

<?php if (is_array($debugModal) && !empty($debugModal['enabled'])): ?>
<?php
$debugLogContent = (string)($debugModal['debug_log_content'] ?? '');
$exceptionsLogContent = (string)($debugModal['exceptions_log_content'] ?? '');
$runtimeDebugHtml = \Micro\OpenvpnWebadmin\Core\Debug::getBufferedOutput();
$goRequestVars = $debugModal['go_request_vars'] ?? [];
if (!is_array($goRequestVars)) {
    $goRequestVars = [];
}
?>
<div class="modal fade" id="debugModal" tabindex="-1" aria-labelledby="debugModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="debugModalLabel">Debugging</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 debug-modal-grid" id="debugModalGrid">
                    <?php if ($runtimeDebugHtml !== ''): ?>
                    <div class="col-12">
                        <div class="card mb-0 debug-modal-card" id="debugCardRuntime">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Runtime Debug (Debug::debug)</span>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary js-debug-maximize" type="button" data-target-card="debugCardRuntime" aria-label="Maximize">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#runtimeDebugCollapse" aria-expanded="true" aria-controls="runtimeDebugCollapse">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="runtimeDebugCollapse" class="collapse show">
                                <div class="card-body debug-runtime-body">
                                    <?= $runtimeDebugHtml ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-lg-4">
                        <div class="card mb-0 debug-modal-card" id="debugCardLog">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>debug.log</span>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary js-debug-maximize" type="button" data-target-card="debugCardLog" aria-label="Maximize">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#debugLogCollapse" aria-expanded="false" aria-controls="debugLogCollapse">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="debugLogCollapse" class="collapse">
                                <div class="card-body">
                                    <pre class="debug-modal-pre mb-0"><?= htmlspecialchars($debugLogContent, ENT_QUOTES, 'UTF-8') ?></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card mb-0 debug-modal-card" id="debugCardExceptions">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>exceptions.log</span>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary js-debug-maximize" type="button" data-target-card="debugCardExceptions" aria-label="Maximize">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#exceptionsLogCollapse" aria-expanded="false" aria-controls="exceptionsLogCollapse">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="exceptionsLogCollapse" class="collapse">
                                <div class="card-body">
                                    <pre class="debug-modal-pre mb-0"><?= htmlspecialchars($exceptionsLogContent, ENT_QUOTES, 'UTF-8') ?></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card mb-0 debug-modal-card" id="debugCardGoRequest">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>GoRequest Variablen</span>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary js-debug-maximize" type="button" data-target-card="debugCardGoRequest" aria-label="Maximize">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#goRequestVarsCollapse" aria-expanded="false" aria-controls="goRequestVarsCollapse">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="goRequestVarsCollapse" class="collapse">
                                <div class="card-body">
                                    <pre class="debug-modal-pre mb-0"><?= htmlspecialchars(print_r($goRequestVars, true), ENT_QUOTES, 'UTF-8') ?></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
