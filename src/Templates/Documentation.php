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

class Documentation
{
    /**
     * Rendert die Funktionsdokumentation aus doc/functions.md als durchsuchbare Akkordeon-Ansicht.
     *
     * @return string
     */
    public function index(): string
    {
        $title = \Lang::get('_DOC_TITLE');
        $subtitle = \Lang::get('_DOC_SUBTITLE');
        $searchPlaceholder = \Lang::get('_DOC_SEARCH_PLACEHOLDER');
        $noResult = \Lang::get('_DOC_NO_RESULTS');
        $fileLabel = \Lang::get('_FILE');
        $lineLabel = \Lang::get('_DOC_LINE');
        $returnLabel = \Lang::get('_DOC_RETURN');

        $parsed = $this->parseFunctionsReference();
        $error = $parsed['error'];
        $files = $parsed['files'];

        if ($error !== '') {
            $safe = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
            return <<<HTML
<h1>{$title}</h1>
<p class="text-muted">{$subtitle}</p>
<div class="alert alert-danger">{$safe}</div>
HTML;
        }

        $cards = [];
        $index = 0;
        foreach ($files as $file => $functions) {
            $fileId = 'docFile' . $index;
            $headingId = 'docHeading' . $index;
            $collapseId = 'docCollapse' . $index;
            $isOpen = $index === 0 ? 'show' : '';
            $expanded = $index === 0 ? 'true' : 'false';
            $collapsedClass = $index === 0 ? '' : 'collapsed';
            $safeFile = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
            $safeFileAttr = htmlspecialchars(strtolower($file), ENT_QUOTES, 'UTF-8');

            $functionsHtml = [];
            foreach ($functions as $entry) {
                $signature = htmlspecialchars((string)($entry['signature'] ?? ''), ENT_QUOTES, 'UTF-8');
                $line = htmlspecialchars((string)($entry['line'] ?? '-'), ENT_QUOTES, 'UTF-8');
                $return = htmlspecialchars((string)($entry['return'] ?? '-'), ENT_QUOTES, 'UTF-8');
                $docblock = htmlspecialchars((string)($entry['docblock'] ?? ''), ENT_QUOTES, 'UTF-8');
                $searchBlob = strtolower(
                    implode(
                        ' ',
                        [
                            (string)($entry['signature'] ?? ''),
                            (string)($entry['line'] ?? ''),
                            (string)($entry['return'] ?? ''),
                            (string)($entry['docblock'] ?? ''),
                            $file,
                        ]
                    )
                );
                $safeSearchBlob = htmlspecialchars($searchBlob, ENT_QUOTES, 'UTF-8');

                $functionsHtml[] = <<<HTML
<div class="doc-function border rounded p-3 mb-3" data-doc-search="{$safeSearchBlob}">
  <h6 class="mb-2"><code>{$signature}</code></h6>
  <div class="small text-muted mb-2">
    {$fileLabel}: <code>{$safeFile}</code> |
    {$lineLabel}: <strong>{$line}</strong> |
    {$returnLabel}: <code>{$return}</code>
  </div>
  <pre class="doc-pre mb-0">{$docblock}</pre>
</div>
HTML;
            }

            $functionCount = count($functions);
            $cards[] = <<<HTML
<div class="accordion-item doc-file-item" id="{$fileId}" data-doc-file="{$safeFileAttr}">
  <h2 class="accordion-header" id="{$headingId}">
    <button class="accordion-button {$collapsedClass}" type="button" data-bs-toggle="collapse" data-bs-target="#{$collapseId}" aria-expanded="{$expanded}" aria-controls="{$collapseId}">
      <code>{$safeFile}</code>
      <span class="badge bg-secondary ms-2">{$functionCount}</span>
    </button>
  </h2>
  <div id="{$collapseId}" class="accordion-collapse collapse {$isOpen}" aria-labelledby="{$headingId}" data-bs-parent="#docsAccordion">
    <div class="accordion-body pt-3">
      {$this->joinHtml($functionsHtml)}
    </div>
  </div>
</div>
HTML;

            $index++;
        }

        $emptyHintClass = count($files) > 0 ? 'd-none' : '';
        return <<<HTML
<h1>{$title}</h1>
<p class="text-muted">{$subtitle}</p>

<div class="card mb-3">
  <div class="card-body">
    <input id="docsSearchInput" type="search" class="form-control" placeholder="{$searchPlaceholder}" aria-label="{$searchPlaceholder}">
  </div>
</div>

<div id="docsNoResult" class="alert alert-warning {$emptyHintClass}">{$noResult}</div>

<div class="accordion" id="docsAccordion">
  {$this->joinHtml($cards)}
</div>
HTML;
    }

    /**
     * Parsed doc/functions.md into file -> function entries.
     *
     * @return array{error:string,files:array<string,array<int,array<string,string>>>}
     */
    private function parseFunctionsReference(): array
    {
        $baseDir = defined('_PROJECT_BASE_DIR') ? _PROJECT_BASE_DIR : dirname(__DIR__, 2);
        $path = $baseDir . '/doc/functions.md';

        if (!is_file($path) || !is_readable($path)) {
            return ['error' => 'doc/functions.md konnte nicht gelesen werden.', 'files' => []];
        }

        $content = @file_get_contents($path);
        if (!is_string($content) || $content === '') {
            return ['error' => 'doc/functions.md ist leer oder unlesbar.', 'files' => []];
        }

        $lines = preg_split('/\R/', $content) ?: [];
        $files = [];
        $currentFile = '';
        $currentFunction = null;
        $inCodeBlock = false;
        $docBuffer = [];

        $flushFunction = static function () use (&$files, &$currentFile, &$currentFunction, &$docBuffer): void {
            if (!is_array($currentFunction) || $currentFile === '') {
                return;
            }
            $currentFunction['docblock'] = trim(implode("\n", $docBuffer));
            if (!isset($files[$currentFile])) {
                $files[$currentFile] = [];
            }
            $files[$currentFile][] = $currentFunction;
            $currentFunction = null;
            $docBuffer = [];
        };

        foreach ($lines as $line) {
            if (preg_match('/^##\s+`(.+)`$/', $line, $m)) {
                $flushFunction();
                $currentFile = trim($m[1]);
                if (!isset($files[$currentFile])) {
                    $files[$currentFile] = [];
                }
                $inCodeBlock = false;
                continue;
            }

            if (preg_match('/^###\s+`(.+)`$/', $line, $m)) {
                $flushFunction();
                $currentFunction = [
                    'signature' => trim($m[1]),
                    'line' => '-',
                    'return' => '-',
                    'docblock' => '',
                ];
                $inCodeBlock = false;
                continue;
            }

            if (!is_array($currentFunction)) {
                continue;
            }

            if (preg_match('/^- Zeile:\s*(.+)$/', $line, $m)) {
                $currentFunction['line'] = trim($m[1]);
                continue;
            }

            if (preg_match('/^- Rueckgabe:\s*`(.+)`$/', $line, $m)) {
                $currentFunction['return'] = trim($m[1]);
                continue;
            }

            if (trim($line) === '```') {
                $inCodeBlock = !$inCodeBlock;
                continue;
            }

            if ($inCodeBlock) {
                $docBuffer[] = $line;
            }
        }

        $flushFunction();

        return ['error' => '', 'files' => $files];
    }

    /**
     * Joins HTML fragments without additional separators.
     *
     * @param array<int,string> $parts
     * @return string
     */
    private function joinHtml(array $parts): string
    {
        return implode("\n", $parts);
    }
}
