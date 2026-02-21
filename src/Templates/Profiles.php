<?php

namespace Micro\OpenvpnWebadmin\Templates;

class Profiles
{
    /**
     * Kurzbeschreibung Funktion index
     *
     * @return string
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
