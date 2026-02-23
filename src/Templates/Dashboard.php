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

class Dashboard
{
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
public function index(): string
    {
        $t = fn(string $key): string => \Lang::get($key);
        $url = fn(string $op): string => htmlspecialchars(\Url::op($op), ENT_QUOTES, 'UTF-8');
        $adminStats = '';
        if (\Micro\OpenvpnWebadmin\Core\Session::isAdmin()) {
            $adminStats = <<<HTML
  <div class="row g-3 mb-3" id="dashboardAdminStats">
    <div class="col-sm-6 col-lg-3">
      <div class="small-box bg-info">
        <div class="inner">
          <h3 id="statLoad">-</h3>
          <p>{$t('_DASH_STAT_LOAD')}</p>
        </div>
        <div class="icon"><i class="bi bi-speedometer2"></i></div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3 id="statDisk">-</h3>
          <p>{$t('_DASH_STAT_DISK')}</p>
        </div>
        <div class="icon"><i class="bi bi-hdd-network"></i></div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="small-box bg-success">
        <div class="inner">
          <h3 id="statUsers">-</h3>
          <p>{$t('_DASH_STAT_USERS')}</p>
        </div>
        <div class="icon"><i class="bi bi-people-fill"></i></div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3 id="statErrors">-</h3>
          <p>{$t('_DASH_STAT_ERRORS')}</p>
        </div>
        <div class="icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
      </div>
    </div>
  </div>
HTML;
        }
        return <<<HTML
<div class="container-fluid px-0">
  {$adminStats}
  <div class="row g-3">
    <div class="col-md-6 col-xl-3">
      <div class="card dashboard-card h-100">
        <div class="card-body">
          <h5 class="card-title">{$t('_DASH_ACCOUNT_TITLE')}</h5>
          <p class="card-text">{$t('_DASH_ACCOUNT_TEXT')}</p>
          <a href="{$url('account')}" class="btn btn-outline-primary btn-sm">{$t('_DASH_OPEN')}</a>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="card dashboard-card h-100">
        <div class="card-body">
          <h5 class="card-title">{$t('_DASH_PROFILES_TITLE')}</h5>
          <p class="card-text">{$t('_DASH_PROFILES_TEXT')}</p>
          <a href="{$url('profiles')}" class="btn btn-outline-primary btn-sm">{$t('_DASH_OPEN')}</a>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="card dashboard-card h-100">
        <div class="card-body">
          <h5 class="card-title">{$t('_DASH_USERS_TITLE')}</h5>
          <p class="card-text">{$t('_DASH_USERS_TEXT')}</p>
          <a href="{$url('users')}" class="btn btn-outline-primary btn-sm">{$t('_DASH_OPEN')}</a>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="card dashboard-card h-100">
        <div class="card-body">
          <h5 class="card-title">{$t('_DASH_LOGS_TITLE')}</h5>
          <p class="card-text">{$t('_DASH_LOGS_TEXT')}</p>
          <a href="{$url('logs')}" class="btn btn-outline-primary btn-sm">{$t('_DASH_OPEN')}</a>
        </div>
      </div>
    </div>
  </div>
</div>
HTML;
    }
}
