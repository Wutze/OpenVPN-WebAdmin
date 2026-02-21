<?php

namespace Micro\OpenvpnWebadmin\Templates;

class Config
{
    /**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
public function index(): string
    {
        $title = \Lang::get('_CONFIG_TITLE');
        $subtitle = \Lang::get('_CONFIG_SUBTITLE');
        $system = \Lang::get('_CONFIG_SYSTEM');
        $reload = \Lang::get('_CONFIG_RELOAD');
        $save = \Lang::get('_CONFIG_SAVE');
        $editor = \Lang::get('_CONFIG_EDITOR');
        $hint = \Lang::get('_CONFIG_REQUIRED_HINT');
        $compare = \Lang::get('_CONFIG_COMPARE');
        $diff = \Lang::get('_CONFIG_DIFF');
        $noDiff = \Lang::get('_CONFIG_NO_DIFF');
        $file = \Lang::get('_FILE');
        return <<<HTML
<h1>{$title}</h1>
<p class="text-muted">{$subtitle}</p>
<div id="configMessage" class="mb-3"></div>

<div class="card mb-3">
  <div class="card-body row g-3 align-items-end">
    <div class="col-md-8">
      <label class="form-label">{$system}</label>
      <div id="configSystemToggleGroup" class="btn-group w-100 flex-wrap" role="group" aria-label="{$system}"></div>
    </div>
    <div class="col-md-4 text-md-end">
      <button id="configReloadBtn" class="btn btn-outline-secondary">{$reload}</button>
      <button id="configSaveBtn" class="btn btn-primary">{$save}</button>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card h-100 config-editor-card">
      <div class="card-header">{$editor}</div>
      <div class="card-body ovpn-editor-body">
        <textarea id="ovpnEditor" class="form-control ovpn-editor" spellcheck="false"></textarea>
        <div class="form-text mt-2">{$hint}</div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card mb-3">
      <div class="card-header">{$compare}</div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0" id="configHistoryTable">
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
        <pre id="configDiffOutput" class="config-diff">{$noDiff}</pre>
      </div>
    </div>
  </div>
</div>
HTML;
    }
}
