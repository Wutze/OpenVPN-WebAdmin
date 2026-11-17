<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020/2026 OpenVPN-WebAdmin
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

namespace Micro\OpenvpnWebadmin\Templates;

class Profiles
{
    /**
     * Zeigt Betriebssystem-Downloads und fuer Admins die Benutzerauswahl.
     *
     * @param array<int, array{system: string, label: string}> $systems
     */
    public function index(array $systems, bool $isAdmin): string
    {
        $title = \Lang::get('_PROFILES_TITLE');
        $subtitle = \Lang::get('_PROFILES_SUBTITLE');
        $download = \Lang::get('_DOWNLOAD');
        $cards = '';
        foreach ($systems as $system) {
            $id = htmlspecialchars($system['system'], ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars($system['label'], ENT_QUOTES, 'UTF-8');
            $downloadUrl = htmlspecialchars(\Url::op('download', ['system' => $system['system']]), ENT_QUOTES, 'UTF-8');
            $cards .= <<<HTML
<div class="col-md-4">
  <div class="card h-100">
    <div class="card-body d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">{$label}</h5>
      <a class="btn btn-success js-profile-download" data-system="{$id}" href="{$downloadUrl}">{$download}</a>
    </div>
  </div>
</div>
HTML;
        }

        if ($cards === '') {
            $cards = '<div class="col-12"><div class="alert alert-warning">' .
                htmlspecialchars(\Lang::get('_MSG_NO_SYSTEMS'), ENT_QUOTES, 'UTF-8') .
                '</div></div>';
        }

        $userTable = '';
        if ($isAdmin && $systems !== []) {
            $userTitle = \Lang::get('_PROFILE_USERS_TITLE');
            $uidLabel = \Lang::get('_PROFILE_USERS_UID');
            $nameLabel = \Lang::get('_PROFILE_USERS_NAME');
            $actionLabel = \Lang::get('_PROFILE_USERS_ACTION');
            $selfLabel = \Lang::get('_PROFILE_USERS_SELF');
            $userTable = <<<HTML
<div class="card mt-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>{$userTitle}</span>
    <button class="btn btn-sm btn-outline-secondary" type="button" id="profileOwnUserBtn">{$selfLabel}</button>
  </div>
  <div class="card-body">
    <table class="table table-striped" id="profile-users-table">
      <thead><tr><th>{$uidLabel}</th><th>{$nameLabel}</th><th>{$actionLabel}</th></tr></thead>
      <tbody></tbody>
    </table>
    <div id="profileUserSelection" class="small text-muted"></div>
  </div>
</div>
HTML;
        }

        return <<<HTML
<h1>{$title}</h1>
<p class="text-muted">{$subtitle}</p>
<div class="row g-3" id="profile-downloads">{$cards}</div>
{$userTable}
HTML;
    }
}
