<?php

namespace Micro\OpenvpnWebadmin\Templates;

class Settings
{
    /**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
public function index(): string
    {
        $title = \Lang::get('_SETTINGS_TITLE');
        $subtitle = \Lang::get('_SETTINGS_SUBTITLE');
        $source = \Lang::get('_SETTINGS_SOURCE');
        $savePath = \Lang::get('_SETTINGS_SAVE_PATH');
        $reload = \Lang::get('_SETTINGS_RELOAD');
        $save = \Lang::get('_SETTINGS_SAVE');
        $editor = \Lang::get('_SETTINGS_EDITOR');
        $history = \Lang::get('_SETTINGS_HISTORY');
        $diff = \Lang::get('_CONFIG_DIFF');
        $noDiff = \Lang::get('_CONFIG_NO_DIFF');
        $file = \Lang::get('_FILE');
        return <<<HTML
<h1>{$title}</h1>
<p class="text-muted">{$subtitle}</p>

<div class="card mb-3">
  <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <div><strong>{$source}:</strong> <span id="settingsSourceInfo">-</span></div>
      <div><strong>{$savePath}:</strong> <span id="settingsSavePath">-</span></div>
    </div>
    <div>
      <button id="settingsReloadBtn" class="btn btn-outline-secondary">{$reload}</button>
      <button id="settingsSaveBtn" class="btn btn-primary">{$save}</button>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header">{$editor}</div>
      <div class="card-body">
        <textarea id="settingsEditor" class="form-control ovpn-editor" spellcheck="false"></textarea>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card mb-3">
      <div class="card-header">{$history}</div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0" id="settingsHistoryTable">
          <thead>
            <tr>
              <th>{$file}</th>
              <th>+/-</th>
              <th></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header">{$diff}</div>
      <div class="card-body">
        <pre id="settingsDiffOutput" class="config-diff">{$noDiff}</pre>
      </div>
    </div>
  </div>
</div>

<div id="settingsMessage" class="mt-3"></div>
HTML;
    }
}
