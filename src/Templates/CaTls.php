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

class CaTls
{
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
public function index(): string
    {
        $title = \Lang::get('_CATLS_TITLE');
        $subtitle = \Lang::get('_CATLS_SUBTITLE');
        $caLabel = \Lang::get('_CATLS_CA_LABEL');
        $tlsLabel = \Lang::get('_CATLS_TLS_LABEL');
        $reload = \Lang::get('_CATLS_RELOAD');
        $save = \Lang::get('_CATLS_SAVE');
        $caPathLabel = \Lang::get('_CATLS_CA_PATH');
        $tlsPathLabel = \Lang::get('_CATLS_TLS_PATH');

        return <<<HTML
<h1>{$title}</h1>
<p class="text-muted">{$subtitle}</p>
<div id="catlsMessage" class="mb-3"></div>

<div class="card mb-3">
  <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <div><strong>{$caPathLabel}:</strong> <span id="catlsCaPath">-</span></div>
      <div><strong>{$tlsPathLabel}:</strong> <span id="catlsTlsPath">-</span></div>
    </div>
    <div>
      <button id="catlsReloadBtn" class="btn btn-outline-secondary">{$reload}</button>
      <button id="catlsSaveBtn" class="btn btn-primary">{$save}</button>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">{$caLabel}</div>
      <div class="card-body">
        <textarea id="catlsCaEditor" class="form-control ovpn-editor" spellcheck="false"></textarea>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">{$tlsLabel}</div>
      <div class="card-body">
        <textarea id="catlsTlsEditor" class="form-control ovpn-editor" spellcheck="false"></textarea>
      </div>
    </div>
  </div>
</div>
HTML;
    }
}

