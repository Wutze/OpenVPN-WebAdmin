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

$t = static fn(string $k): string => \Lang::get($k);

return <<<HTML
<div class="card mb-3">
  <div class="card-header">{$t('_USERS_CREATE_TITLE')}</div>
  <div class="card-body">
    <form id="createUserForm" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label for="createUsername" class="form-label">{$t('_USERS_USERNAME')}</label>
        <input id="createUsername" name="username" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label for="createPassword" class="form-label">{$t('_USERS_START_PASSWORD')}</label>
        <input id="createPassword" name="password" type="password" class="form-control" required>
      </div>
      <div class="col-md-2">
        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" id="createIsAdmin" name="is_admin" value="1">
          <label class="form-check-label" for="createIsAdmin">{$t('_USERS_IS_ADMIN')}</label>
        </div>
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100" type="submit">{$t('_USERS_CREATE_BUTTON')}</button>
      </div>
    </form>
  </div>
</div>

<table id="user-table" class="table table-striped"
       data-url="?op=data&select=user"
       data-side-pagination="server"
       data-search="true"
       data-striped="true"
       data-pagination="true"
       data-detail-view="true"
       data-detail-formatter="userDetailFormatter">
  <thead>
    <tr>
      <th data-field="uname">{$t('_USERS_COL_NAME')}</th>
      <th data-field="gname">{$t('_USERS_COL_GROUP')}</th>
      <th data-field="user_enable" data-formatter="ledFormatter">{$t('_USERS_COL_ACTIVE')}</th>
      <th data-field="user_online" data-formatter="onlineFormatter">{$t('_USERS_COL_ONLINE')}</th>
      <th data-field="user_start_date">{$t('_USERS_COL_FROM')}</th>
      <th data-field="user_end_date">{$t('_USERS_COL_TO')}</th>
    </tr>
  </thead>
</table>

<div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userDetailModalLabel">{$t('_USERS_DETAIL_TITLE')}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="userDetailTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabOverview" type="button" role="tab">{$t('_USERS_TAB_OVERVIEW')}</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabLimits" type="button" role="tab">{$t('_USERS_TAB_LIMITS')}</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabNetwork" type="button" role="tab">{$t('_USERS_TAB_NETWORK')}</button>
          </li>
        </ul>

        <div class="tab-content pt-3">
          <div class="tab-pane fade show active" id="tabOverview" role="tabpanel">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">{$t('_USERS_USERNAME')}</label>
                <input type="text" id="detailUsername" class="form-control" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">{$t('_USERS_ONLINE_STATUS')}</label>
                <input type="text" id="detailOnline" class="form-control" readonly>
              </div>
              <div class="col-md-6">
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" id="detailIsAdmin">
                  <label class="form-check-label" for="detailIsAdmin">{$t('_USERS_IS_ADMIN')}</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" id="detailEnabled">
                  <label class="form-check-label" for="detailEnabled">{$t('_USERS_ACTIVE_CHECK')}</label>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">{$t('_USERS_PASSWORD_OPTIONAL')}</label>
                <input type="password" id="detailPassword" class="form-control" placeholder="{$t('_USERS_PASSWORD_PLACEHOLDER')}">
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="tabLimits" role="tabpanel">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">{$t('_USERS_VALID_FROM')}</label>
                <input type="date" id="detailStartDate" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">{$t('_USERS_VALID_TO')}</label>
                <input type="date" id="detailEndDate" class="form-control">
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="tabNetwork" role="tabpanel">
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="detailFixedIpEnabled">
              <label class="form-check-label" for="detailFixedIpEnabled">{$t('_USERS_FIXED_IP_ENABLE')}</label>
            </div>
            <div>
              <label class="form-label">{$t('_USERS_FIXED_IP')}</label>
              <input type="text" id="detailFixedIp" class="form-control" inputmode="numeric" placeholder="{$t('_USERS_FIXED_IP_PLACEHOLDER')}">
              <div class="invalid-feedback">{$t('_API_FIXED_IP_INVALID')}</div>
            </div>
            <div class="form-text">{$t('_USERS_NETWORK_HINT')}</div>
          </div>
        </div>

        <div id="userDetailMessage" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-danger me-auto" id="deleteUserDetailBtn">{$t('_USERS_DELETE')}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{$t('_CLOSE')}</button>
        <button type="button" class="btn btn-primary" id="saveUserDetailBtn">{$t('_SAVE')}</button>
      </div>
    </div>
  </div>
</div>
HTML;
