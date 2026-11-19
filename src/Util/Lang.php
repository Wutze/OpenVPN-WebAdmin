<?php
/**
 * this File is part of OpenVPN-WebAdmin - (c) 2020/2026 OpenVPN-WebAdmin
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

declare(strict_types=1);

namespace Micro\OpenvpnWebadmin\Util;

/**
 * Mehrsprachigkeit.
 * Sprachdateien liegen als Lang/<locale>/lang.php im Projektverzeichnis
 * und liefern ein assoziatives Array ('_KEY' => 'Text').
 * Die Anwendung ermittelt die aktive Sprache und übergibt den Locale-Code an init().
 */
class Lang
{
    private const LANG_DIR = __DIR__ . '/../Lang';

    /**
     * Marker für erweiterte Dokumentation am Ende eines Spracheintrags:
     * [HELP_DOC/<bereich>/:<dokument>] → doc/<bereich>/<dokument>.md
     * Bereich und Dokument sind streng auf Buchstaben, Ziffern, _ und - begrenzt,
     * damit keine Pfadangaben möglich sind.
     */
    private const HELP_DOC_PATTERN = '/\[HELP_DOC\/([A-Za-z0-9_-]{1,64})\/:([A-Za-z0-9_-]{1,64})\]/';

    private static array $messages = [];
    private static string $currentLang = 'de_DE';
    private static bool $initialized = false;

    /**
     * Initialisiert die Sprache (Session muss vorher gestartet sein).
     * @param ?string $lang
     * @return void
     */
    public static function init(?string $lang = null): void
    {
        self::$initialized = true;

        self::$currentLang = self::sanitizeLangCode($lang ?? 'de_DE');
        self::loadMessages(self::$currentLang);
    }

    /**
     * Einzeltext holen.
     * Ein eventuell vorhandener [HELP_DOC/...]-Marker wird aus dem sichtbaren
     * Text entfernt; die Referenz selbst liefert Lang::getDocRef().
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function get(string $key, string $default = ''): string
    {
        self::ensureInitialized();

        $value = self::$messages[$key] ?? ($default !== '' ? $default : $key);

        return self::stripHelpDocMarkers($value);
    }

    /**
     * Dokumentationsverweis eines Spracheintrags ([HELP_DOC/<bereich>/:<dokument>]).
     * @return array{area: string, doc: string}|null null, wenn kein gültiger Marker vorhanden ist
     * @param string $key
     */
    public static function getDocRef(string $key): ?array
    {
        self::ensureInitialized();

        $value = self::$messages[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (preg_match(self::HELP_DOC_PATTERN, $value, $matches) !== 1) {
            return null;
        }

        return ['area' => $matches[1], 'doc' => $matches[2]];
    }

    /**
     * Alle Dokumentationsverweise der aktiven Sprache als Liste
     * (sichtbarer Text → Bereich/Dokument). Dient der clientseitigen
     * Anbindung des 🛟-Symbols an jede Stelle, an der ein markierter
     * Sprachtext ausgegeben wird.
     * @return array<int, array{text: string, area: string, doc: string}>
     */
    public static function getDocRefMap(): array
    {
        self::ensureInitialized();

        $map = [];
        foreach (self::$messages as $value) {
            if (!is_string($value)) {
                continue;
            }
            if (preg_match(self::HELP_DOC_PATTERN, $value, $matches) !== 1) {
                continue;
            }
            $text = self::stripHelpDocMarkers($value);
            if ($text === '') {
                continue;
            }
            $map[] = ['text' => $text, 'area' => $matches[1], 'doc' => $matches[2]];
        }

        return $map;
    }

    /**
     * Komplettes Message-Array.
     * @return array
     */
    public static function getAll(): array
    {
        self::ensureInitialized();

        $messages = [];
        foreach (self::$messages as $key => $value) {
            $messages[$key] = is_string($value) ? self::stripHelpDocMarkers($value) : $value;
        }

        return $messages;
    }

    /**
     * Aktuelle Sprache.
     * @return string
     */
    public static function getCurrent(): string
    {
        self::ensureInitialized();

        return self::$currentLang;
    }

    /**
     * Sprache setzen, in Session und Cookie speichern und neu laden.
     * @param string $lang
     * @return void
     */
    public static function setCurrent(string $lang): void
    {
        $safe = self::sanitizeLangCode($lang);
        self::init($safe);
    }

    /**
     * Alle verfügbaren Sprachcodes anhand der Ordner Lang/<locale>/lang.php.
     * @return array
     */
    public static function getAvailableLanguages(): array
    {
        $entries = is_dir(self::LANG_DIR) ? (scandir(self::LANG_DIR) ?: []) : [];
        $codes = [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (preg_match('/^[a-z]{2}_[A-Z]{2}$/', $entry) !== 1) {
                continue;
            }
            if (is_file(self::LANG_DIR . '/' . $entry . '/lang.php')) {
                $codes[] = $entry;
            }
        }

        sort($codes);

        return $codes === [] ? ['de_DE'] : $codes;
    }

    /**
     * Liefert Label + Flagge für die Sprachauswahl.
     * Die Flagge ist ein SVG aus public/img/flags/ (Dateiname = Ländercode
     * klein, z. B. de.svg); ohne passende Datei ein generisches Icon.
     * @param string $code
     * @return array
     */
    public static function getLanguageMeta(string $code): array
    {
        if (!preg_match('/^([a-z]{2})_([A-Z]{2})$/', $code, $matches)) {
            return ['label' => $code, 'flag' => '<i class="fas fa-flag"></i>'];
        }

        $langCode = strtolower($matches[1]);
        $countryCode = strtoupper($matches[2]);

        return [
            'label' => self::buildLocaleLabel($langCode, $countryCode),
            'flag' => self::flagIcon($countryCode),
        ];
    }

    /**
     * Lädt die Sprachwahl einmalig, wenn ein Aufrufer vor init() erstmals einen Text abfragt.
     * @return void
     */
    private static function ensureInitialized(): void
    {
        if (!self::$initialized) {
            self::init();
        }
    }

    /**
     * Entfernt gültige [HELP_DOC/...]-Marker aus einem sichtbaren Text.
     * @param string $text
     * @return string
     */
    private static function stripHelpDocMarkers(string $text): string
    {
        if (!str_contains($text, '[HELP_DOC/')) {
            return $text;
        }

        return rtrim((string) preg_replace(self::HELP_DOC_PATTERN, '', $text));
    }

    /**
     * Lädt das Nachrichtenarray der Sprache und fällt bei fehlender/leerer Datei auf DEFAULT_LANG zurück.
     * @param string $lang
     * @return void
     */
    private static function loadMessages(string $lang): void
    {
        self::$messages = [];

        $file = self::LANG_DIR . '/' . $lang . '/lang.php';
        if (is_file($file)) {
            $msgs = include $file;
            if (is_array($msgs)) {
                self::$messages = $msgs;
            }
        }

        // Fallback auf DEFAULT_LANG, wenn die Datei fehlt oder leer ist
        if (self::$messages === []) {
            $default = self::sanitizeAvailable('de_DE');
            if ($default !== $lang) {
                $fallbackFile = self::LANG_DIR . '/' . $default . '/lang.php';
                if (is_file($fallbackFile)) {
                    $msgs = include $fallbackFile;
                    if (is_array($msgs)) {
                        self::$messages = $msgs;
                    }
                }
            }
        }
    }

    /**
     * Baut mit der Intl-Erweiterung einen lokalisierten Sprachnamen, sonst ein stabiles Code-Label.
     * @param string $langCode
     * @param string $countryCode
     * @return string
     */
    private static function buildLocaleLabel(string $langCode, string $countryCode): string
    {
        if (class_exists('\\Locale')) {
            $locale = $langCode . '_' . $countryCode;
            $language = \Locale::getDisplayLanguage($locale, $locale);
            /**
             * $region ist eigentlich nicht notwendig, da wir nur die Sprache anzeigen wollen.
             */
            //$region = \Locale::getDisplayRegion($locale, $locale);

            if (is_string($language) && $language !== '' && strcasecmp($language, $langCode) !== 0) {
                //if (is_string($region) && $region !== '' && strcasecmp($region, $countryCode) !== 0) {
                    //return $language . ' (' . $region . ')';
                //}

                return $language;
            }
        }

        return strtoupper($langCode) . ' (' . $countryCode . ')';
    }

    /**
     * Liefert das vorhandene Länderflaggen-SVG oder ein Unicode-Flaggenzeichen.
     * @param string $countryCode
     * @return string
     */
    private static function flagIcon(string $countryCode): string
    {
        $file = strtolower($countryCode) . '.svg';
        if (is_file(__DIR__ . '/../../public/img/flags/' . $file)) {
            return '<img src="/img/flags/' . $file . '" alt="' . $countryCode . '" class="lang-flag">';
        }

        if (class_exists('IntlChar')) {
            $first = \IntlChar::chr(127397 + ord($countryCode[0]));
            $second = \IntlChar::chr(127397 + ord($countryCode[1]));
            if (is_string($first) && is_string($second)) {
                return '<span class="lang-flag-emoji" role="img" aria-label="' . $countryCode . '">'
                    . $first . $second . '</span>';
            }
        }

        return '<span class="lang-flag-emoji" role="img" aria-label="' . $countryCode . '">'
            . '&#' . (127397 + ord($countryCode[0])) . ';&#' . (127397 + ord($countryCode[1])) . ';</span>';
    }

    /**
     * Normalisiert Bindestriche und verwirft Codes außerhalb des Locale-Formats.
     * @param string $lang
     * @return string
     */
    private static function sanitizeLangCode(string $lang): string
    {
        $lang = str_replace('-', '_', trim($lang));
        if (preg_match('/^[a-z]{2}_[A-Z]{2}$/', $lang) !== 1) {
            return self::sanitizeAvailable('de_DE');
        }

        return self::sanitizeAvailable($lang);
    }

    /**
     * Ersetzt nicht installierte Sprachen durch DEFAULT_LANG oder die erste verfügbare Sprache.
     * @param string $lang
     * @return string
     */
    private static function sanitizeAvailable(string $lang): string
    {
        $available = self::getAvailableLanguages();
        if (in_array($lang, $available, true)) {
            return $lang;
        }

        $default = 'de_DE';
        if (in_array($default, $available, true)) {
            return $default;
        }

        return $available[0] ?? 'de_DE';
    }
}
