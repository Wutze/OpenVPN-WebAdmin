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
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#">
                <i class="bi bi-list"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="?op=dashboard" class="nav-link"><?= Lang::get('_HOME') ?></a>
        </li>
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
