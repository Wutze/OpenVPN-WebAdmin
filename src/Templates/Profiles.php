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

namespace Micro\OpenvpnWebadmin\Templates;

class Profiles
{
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
public function index(): string
    {
        $title = \Lang::get('_PROFILES_TITLE');
        $subtitle = \Lang::get('_PROFILES_SUBTITLE');
        $cardTitle = \Lang::get('_PROFILES_CARD_TITLE');
        $refresh = \Lang::get('_REFRESH');
        $colSystem = \Lang::get('_PROFILES_COL_SYSTEM');
        $colFiles = \Lang::get('_PROFILES_COL_FILES');
        $colZip = \Lang::get('_PROFILES_COL_ZIP');
        $colLast = \Lang::get('_PROFILES_COL_LAST_BUILD');
        $colActions = \Lang::get('_PROFILES_COL_ACTIONS');
        return <<<HTML
<h1>{$title}</h1>
<p class="text-muted">{$subtitle}</p>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>{$cardTitle}</span>
    <button type="button" class="btn btn-sm btn-outline-primary" id="refreshProfilesBtn">{$refresh}</button>
  </div>
  <div class="card-body p-0">
    <table class="table table-striped mb-0" id="profiles-table">
      <thead>
        <tr>
          <th>{$colSystem}</th>
          <th>{$colFiles}</th>
          <th>{$colZip}</th>
          <th>{$colLast}</th>
          <th>{$colActions}</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<div id="profilesMessage" class="mt-3"></div>
HTML;
    }
}
