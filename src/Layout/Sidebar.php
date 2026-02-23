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
<!-- Sidebar.php -->
<?php
$activeOp = $activeOp ?? 'dashboard';
$user = $user ?? null;
$isAdmin = \Micro\OpenvpnWebadmin\Core\Session::isAdmin();
$debugEnabled = !empty($debugEnabled);
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= htmlspecialchars(Url::op('dashboard'), ENT_QUOTES, 'UTF-8') ?>" class="brand-link">
        <span class="brand-text font-weight-light"><?= Lang::get('_BRAND_NAME') ?></span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                <li class="nav-item">
                    <a href="<?= htmlspecialchars(Url::op('dashboard'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link <?= $activeOp === 'dashboard' ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p><?= Lang::get('_MENU_DASHBOARD') ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= htmlspecialchars(Url::op('account'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link <?= $activeOp === 'account' ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-person-gear"></i>
                        <p><?= Lang::get('_MENU_ACCOUNT') ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= htmlspecialchars(Url::op('profiles'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link <?= $activeOp === 'profiles' ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-file-earmark-zip"></i>
                        <p><?= Lang::get('_MENU_PROFILES') ?></p>
                    </a>
                </li>

                <?php if ($isAdmin): ?>
                    <li class="nav-header"><?= Lang::get('_MENU_ADMIN') ?></li>
                    <li class="nav-item">
                        <a href="<?= htmlspecialchars(Url::op('users'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link <?= $activeOp === 'users' ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-people"></i>
                            <p><?= Lang::get('_MENU_USERS') ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= htmlspecialchars(Url::op('logs'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link <?= $activeOp === 'logs' ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-journal-text"></i>
                            <p><?= Lang::get('_MENU_LOGS') ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= htmlspecialchars(Url::op('config'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link <?= $activeOp === 'config' ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-sliders"></i>
                            <p><?= Lang::get('_MENU_CONFIG') ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= htmlspecialchars(Url::op('catls'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link <?= $activeOp === 'catls' ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-shield-lock"></i>
                            <p><?= Lang::get('_MENU_CATLS') ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= htmlspecialchars(Url::op('settings'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link <?= $activeOp === 'settings' ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-gear-wide-connected"></i>
                            <p><?= Lang::get('_MENU_SETTINGS') ?></p>
                        </a>
                    </li>
                    <?php if ($debugEnabled): ?>
                    <li class="nav-item">
                        <a href="<?= htmlspecialchars(Url::op('documentation'), ENT_QUOTES, 'UTF-8') ?>" class="nav-link nav-link-debug <?= $activeOp === 'documentation' ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-journal-code"></i>
                            <p><?= Lang::get('_MENU_DOCUMENTATION') ?></p>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
