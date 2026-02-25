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

use Debug;

class Template
{
private string $title;
private string $content;
private array $data;

/**
 * Initialisiert die Klasse und setzt die benoetigten Startwerte.
 *
 * @param mixed $title Eingabewert fuer title.
 * @param mixed $content Eingabewert fuer content.
 * @param mixed $data Eingabewert fuer data.
 * @return mixed|null Rueckgabewert der Funktion.
 */
public function __construct(string $title = 'WebAdmin', string $content = '', array $data = [])
    {
        $this->title   = $title;
        $this->content = $content;
        $this->data    = $data;
    }

    /**
     * Lädt eine Layout-Datei aus /src/Layout/
     */
    private function loadPart(string $name, array $vars = []): string
    {
        $file = __DIR__ . '/../Layout/' . $name . '.php';
        if (!file_exists($file)) {
            return "<!-- Layout-Part {$name} nicht gefunden -->";
        }
        $debugVars = $vars;
        if (isset($debugVars['debugModal']) && is_array($debugVars['debugModal'])) {
            unset(
                $debugVars['debugModal']['debug_log_content'],
                $debugVars['debugModal']['exceptions_log_content']
            );
        }
        //Debug::debug("war was?", $name, $debugVars);
        // Variablen extrahieren
        extract($vars, EXTR_SKIP);

        ob_start();
        include $file;
        return ob_get_clean();
    }

    /**
     * Baut die komplette Seite zusammen
     */
    public function render(): string
    {
        //Debug::debug($this);
        $htmlHead = $this->loadPart('HtmlHead', ['title' => $this->title]);
        $header   = $this->loadPart('Header', $this->data);
        $sidebar  = $this->loadPart('Sidebar', $this->data);
        $footer   = $this->loadPart('Footer');
        $scripts  = $this->loadPart('Scripts', $this->data);
        $htmlfoot  = $this->loadPart('HtmlFoot');

        $html = <<<HTML
{$htmlHead}
    <div class="wrapper">
        {$header}
        {$sidebar}

    <!-- Inhalt -->
    <div class="content-wrapper">
        <section class="content p-3">
            {$this->content}
        </section>
    </div>

        {$footer}
    </div>
{$scripts}
{$htmlfoot}
HTML;

        if (defined('_URL_REWRITE') && _URL_REWRITE === true) {
            $html = $this->rewriteOpUrls($html);
        }

        return $html;
    }

    /**
     * Schreibt ?op=... Links in "sprechende" Pfade um.
     *
     * @param string $html
     * @return string
     */
    private function rewriteOpUrls(string $html): string
    {
        return (string)preg_replace_callback(
            '/\?op=([a-zA-Z0-9_-]+)((?:&(?:amp;)?[^"\'<\s]*)*)/',
            static function (array $m): string {
                $op = (string)$m[1];
                $tail = (string)($m[2] ?? '');

                $queryRaw = str_replace('&amp;', '&', ltrim($tail, '&'));
                $params = [];
                if ($queryRaw !== '') {
                    parse_str($queryRaw, $params);
                }

                if ($op === 'setlang' && isset($params['lang']) && is_string($params['lang']) && $params['lang'] !== '') {
                    $lang = rawurlencode($params['lang']);
                    unset($params['lang']);
                    $query = $params === [] ? '' : '?' . htmlspecialchars(http_build_query($params), ENT_QUOTES, 'UTF-8');
                    return '/setlang/' . $lang . $query;
                }

                if ($op === 'dashboard' || $op === 'main') {
                    return $params === [] ? '/' : '/?' . htmlspecialchars(http_build_query($params), ENT_QUOTES, 'UTF-8');
                }

                $query = $params === [] ? '' : '?' . htmlspecialchars(http_build_query($params), ENT_QUOTES, 'UTF-8');
                return '/' . rawurlencode($op) . $query;
            },
            $html
        );
    }
}
