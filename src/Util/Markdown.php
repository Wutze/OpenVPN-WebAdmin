<?php
/**
 * this File is part of OpenBind-WebAdmin - (c) 2020/2026 OpenBind-WebAdmin
 *
 * NOTICE OF LICENSE
 *
 * OPEN-SOURCE-LIZENZ FÜR DIE EUROPÄISCHE UNION v. 1.2
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://eupl.eu/1.2/de/
 *
 * @author      Wutze
 * @copyright   2026 OpenBind-WebAdmin
 * @link        https://github.com/Wutze/OpenBind-WebAdmin
 * @see         Internal Documentation ~/doc/
 * @version     3.0.0
 */

declare(strict_types=1);

namespace Micro\OpenvpnWebadmin\Util;

/**
 * Minimaler, sicherer Markdown-Renderer für die eingebettete Dokumentation.
 * Sicherheitsmodell: Der komplette Eingabetext wird ZUERST HTML-escaped,
 * rohes HTML aus den Markdown-Dateien wird also niemals ausgegeben. Danach
 * werden nur die unterstützten Markdown-Elemente in HTML überführt:
 * Überschriften, Absätze, Listen, Codeblöcke, Inline-Code, Fett/Kursiv,
 * Links und Bilder.
 * Links sind auf die Schemes http, https und mailto beschränkt. Bild-Quellen
 * werden ausschließlich über den übergebenen Resolver in eine URL überführt;
 * ohne Resolver oder bei ungültiger Quelle wird das Bild verworfen.
 */
class Markdown
{
    /**
     * Rendert den unterstützten Markdown-Teilumfang zu escaped HTML für das Hilfedokumentationsmodal.
     * @param callable|null $imageResolver Resolver mit String-Quelle und erlaubter String-URL oder null; null verwirft Bilder.
     * @param string $markdown
     * @return string
     */
    public static function render(string $markdown, ?callable $imageResolver = null): string
    {
        $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);

        // Fenced Codeblöcke zuerst herauslösen, damit ihr Inhalt nicht
        // weiter interpretiert wird.
        $codeBlocks = [];
        $markdown = (string) preg_replace_callback(
            '/^```[^\n`]*\n(.*?)^```[ \t]*$/ms',
            static function (array $matches) use (&$codeBlocks): string {
                $codeBlocks[] = '<pre class="help-doc-code"><code>'
                    . htmlspecialchars(rtrim($matches[1], "\n"), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                    . '</code></pre>';

                return "\n\n\x00CB" . (count($codeBlocks) - 1) . "\x00\n\n";
            },
            $markdown
        );

        $html = [];
        foreach (preg_split('/\n{2,}/', trim($markdown)) ?: [] as $block) {
            // Codeblock-Platzhalter vor dem trim() prüfen: trim() würde die
            // \x00-Begrenzer entfernen.
            if (preg_match('/^\x00CB\d+\x00$/', $block) === 1) {
                $html[] = $block;
                continue;
            }
            $block = trim($block);
            if ($block === '') {
                continue;
            }
            $html[] = self::renderBlock($block, $imageResolver);
        }

        $result = implode("\n", $html);

        return (string) preg_replace_callback(
            '/\x00CB(\d+)\x00/',
            static fn(array $matches): string => $codeBlocks[(int) $matches[1]],
            $result
        );
    }

    /**
     * Rendert einen Codeblock-Platzhalter, eine Überschrift, Liste oder einen Absatz.
     * @param string $block
     * @param ?callable $imageResolver
     * @return string
     */
    private static function renderBlock(string $block, ?callable $imageResolver): string
    {
        // Platzhalter eines vollständigen Codeblocks
        if (preg_match('/^\x00CB\d+\x00$/', $block) === 1) {
            return $block;
        }

        // Überschrift
        if (preg_match('/^(#{1,6})[ \t]+(.+)$/s', $block, $matches) === 1) {
            $level = strlen($matches[1]);

            return '<h' . $level . '>' . self::renderInline(trim($matches[2]), $imageResolver) . '</h' . $level . '>';
        }

        $lines = explode("\n", $block);

        // Listen: Aufzählung oder nummeriert; Folgezeilen werden dem
        // vorherigen Eintrag zugeschlagen.
        if (preg_match('/^\s*[-*][ \t]+/', $lines[0]) === 1) {
            return self::renderList('ul', self::listItems($lines, '/^\s*[-*][ \t]+/'), $imageResolver);
        }

        if (preg_match('/^\s*\d+[.)][ \t]+/', $lines[0]) === 1) {
            return self::renderList('ol', self::listItems($lines, '/^\s*\d+[.)][ \t]+/'), $imageResolver);
        }

        // Normaler Absatz
        return '<p>' . self::renderInline(implode("\n", $lines), $imageResolver) . '</p>';
    }

    /**
     * Zerlegt Listenzeilen, hängt eingerückte Folgezeilen an den vorherigen Eintrag.
     * @param string[] $lines Zeilen eines Markdown-Blocks.
     * @return string[] Bereinigte Listeneinträge.
     * @param string $markerPattern
     */
    private static function listItems(array $lines, string $markerPattern): array
    {
        $items = [];
        foreach ($lines as $line) {
            if (preg_match($markerPattern, $line) === 1) {
                $items[] = (string) preg_replace($markerPattern, '', $line);
            } elseif ($items !== []) {
                $items[count($items) - 1] .= "\n" . trim($line);
            }
        }

        return $items;
    }

    /**
     * Erzeugt eine HTML-Liste aus zuvor erkannten Einträgen und escaped Inline-Markup.
     * @param string $tag
     * @param array $items
     * @param ?callable $imageResolver
     * @return string
     */
    private static function renderList(string $tag, array $items, ?callable $imageResolver): string
    {
        $html = '<' . $tag . '>';
        foreach ($items as $item) {
            $html .= '<li>' . self::renderInline($item, $imageResolver) . '</li>';
        }

        return $html . '</' . $tag . '>';
    }

    /**
     * Escaped Text zuerst und wandelt danach nur unterstützte Inline-Syntax in HTML um.
     * @param string $text
     * @param ?callable $imageResolver
     * @return string
     */
    private static function renderInline(string $text, ?callable $imageResolver): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Inline-Code
        $escaped = (string) preg_replace('/`([^`\n]+)`/', '<code>$1</code>', $escaped);

        // Fett und Kursiv
        $escaped = (string) preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $escaped);
        $escaped = (string) preg_replace('/\*([^*\n]+)\*/', '<em>$1</em>', $escaped);

        // Bilder (vor Links behandeln)
        $escaped = (string) preg_replace_callback(
            '/!\[([^\]]*)\]\(\s*([^\s)]+)(?:[ \t]+&quot;(.*?)&quot;)?\s*\)/',
            static function (array $matches) use ($imageResolver): string {
                $src = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $url = $imageResolver !== null ? $imageResolver($src) : null;
                if (!is_string($url) || $url === '') {
                    return $matches[1]; // Alt-Text als Klartext belassen
                }

                return '<img src="' . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                    . '" alt="' . $matches[1] . '" loading="lazy">';
            },
            $escaped
        );

        // Links: nur http(s) und mailto zulassen
        return (string) preg_replace_callback(
            '/\[([^\]]+)\]\(\s*([^\s)]+)(?:[ \t]+&quot;(.*?)&quot;)?\s*\)/',
            static function (array $matches): string {
                $url = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if (preg_match('#\A(https?://|mailto:)#i', $url) !== 1) {
                    return $matches[1]; // Linktext ohne Verweis ausgeben
                }

                return '<a href="' . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                    . '" target="_blank" rel="noopener noreferrer">' . $matches[1] . '</a>';
            },
            $escaped
        );
    }
}
