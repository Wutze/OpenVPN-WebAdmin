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

class Account
{
    /**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
public function index(): string
    {
        $title = \Lang::get('_ACCOUNT_TITLE');
        $subtitle = \Lang::get('_ACCOUNT_SUBTITLE');
        $currentPw = \Lang::get('_ACCOUNT_CURRENT_PASSWORD');
        $newPw = \Lang::get('_ACCOUNT_NEW_PASSWORD');
        $repeatPw = \Lang::get('_ACCOUNT_NEW_PASSWORD_REPEAT');
        $save = \Lang::get('_ACCOUNT_SAVE_PASSWORD');
        return <<<HTML
<h1>{$title}</h1>
<p class="text-muted">{$subtitle}</p>

<div class="card account-card" style="max-width:720px;">
  <div class="card-body">
    <form id="accountPasswordForm" class="row g-3">
      <div class="col-12">
        <label for="currentPassword" class="form-label">{$currentPw}</label>
        <input type="password" class="form-control" id="currentPassword" required>
      </div>
      <div class="col-md-6">
        <label for="newPassword" class="form-label">{$newPw}</label>
        <input type="password" class="form-control" id="newPassword" required>
      </div>
      <div class="col-md-6">
        <label for="confirmPassword" class="form-label">{$repeatPw}</label>
        <input type="password" class="form-control" id="confirmPassword" required>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary">{$save}</button>
      </div>
    </form>
    <div id="accountMessage" class="mt-3"></div>
  </div>
</div>
HTML;
    }
}
