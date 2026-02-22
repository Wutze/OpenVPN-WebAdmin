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
//Debug::debug($name,$vars);
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

        return <<<HTML
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

    }
}
