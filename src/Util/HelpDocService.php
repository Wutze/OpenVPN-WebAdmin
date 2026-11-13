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
 * Lädt erweiterte Dokumentation aus doc/<sprache>/<bereich>/<dokument>.md.
 * - Die Sprache kommt aus Lang::getCurrent(); existiert die Datei dort nicht,
 * wird automatisch auf de_DE zurückgefallen.
 * - Bereich und Dokument werden gegen ein festes Format validiert; sämtliche
 * Zugriffe bleiben über einen realpath-Check innerhalb von doc/.
 */
class HelpDocService
{
    private const DOC_DIR = __DIR__ . '/../../doc';
    private const FALLBACK_LANG = 'de_DE';
    private const NAME_PATTERN = '/\A[A-Za-z0-9_-]{1,64}\z/';
    private const IMAGE_PATH_PATTERN = '/\A[A-Za-z0-9._-]+(?:\/[A-Za-z0-9._-]+)*\z/';

    private const IMAGE_MIME_TYPES = [
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
    ];

    /**
     * Dokument laden; Fallback auf de_DE, wenn es in der aktiven Sprache fehlt.
     * @return array{area: string, doc: string, lang: string, markdown: string}|null
     * null, wenn weder die Übersetzung noch die deutsche Datei existiert
     * oder die Angaben ungültig sind
     * @param string $area
     * @param string $doc
     */
    public function load(string $area, string $doc): ?array
    {
        if (!$this->isValidName($area) || !$this->isValidName($doc)) {
            return null;
        }

        $lang = Lang::getCurrent();
        $path = $this->docPath($lang, $area, $doc);

        if ($path === null) {
            $lang = self::FALLBACK_LANG;
            $path = $this->docPath($lang, $area, $doc);
        }

        if ($path === null) {
            return null;
        }

        $markdown = @file_get_contents($path);
        if ($markdown === false) {
            return null;
        }

        return ['area' => $area, 'doc' => $doc, 'lang' => $lang, 'markdown' => $markdown];
    }

    /**
     * Bild relativ zum Verzeichnis eines Dokuments auflösen.
     * @return array{path: string, mime: string}|null
     * @param string $lang
     * @param string $area
     * @param string $file
     */
    public function resolveImage(string $lang, string $area, string $file): ?array
    {
        if (!in_array($lang, Lang::getAvailableLanguages(), true)) {
            return null;
        }

        if (!$this->isValidName($area)) {
            return null;
        }

        // Relativpfad: einzelne Segmente, kein ".." o. ä.
        if (preg_match(self::IMAGE_PATH_PATTERN, $file) !== 1 || str_contains($file, '..')) {
            return null;
        }

        $mime = self::IMAGE_MIME_TYPES[strtolower(pathinfo($file, PATHINFO_EXTENSION))] ?? null;
        if ($mime === null) {
            return null;
        }

        $path = $this->containedPath(self::DOC_DIR . '/' . $lang . '/' . $area . '/' . $file);
        if ($path === null || !is_file($path)) {
            return null;
        }

        return ['path' => $path, 'mime' => $mime];
    }

    /**
     * Pfad zu einem Dokument, wenn es existiert und innerhalb von doc/ liegt.
     * @param string $lang
     * @param string $area
     * @param string $doc
     * @return ?string
     */
    private function docPath(string $lang, string $area, string $doc): ?string
    {
        if (!in_array($lang, Lang::getAvailableLanguages(), true)) {
            return null;
        }

        $path = $this->containedPath(self::DOC_DIR . '/' . $lang . '/' . $area . '/' . $doc . '.md');

        return $path !== null && is_file($path) ? $path : null;
    }

    /**
     * Auflösen und prüfen, dass der Pfad innerhalb von doc/ bleibt.
     * @param string $path
     * @return ?string
     */
    private function containedPath(string $path): ?string
    {
        $base = realpath(self::DOC_DIR);
        $real = realpath($path);

        if ($base === false || $real === false) {
            return null;
        }

        return str_starts_with($real, $base . DIRECTORY_SEPARATOR) ? $real : null;
    }

    /**
     * Beschränkt Dokument- und Bereichsnamen auf sichere ASCII-Zeichen ohne Pfadtrenner.
     * @param string $name
     * @return bool
     */
    private function isValidName(string $name): bool
    {
        return preg_match(self::NAME_PATTERN, $name) === 1;
    }
}
