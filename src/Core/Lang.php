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

namespace Micro\OpenvpnWebadmin\Core;

class Lang
{
    private static array $messages = [];
    private static string $currentLang = 'de_DE';

    /**
     * Initialisiert die Sprache (Session muss vorher gestartet sein)
     */
    public static function init(?string $lang = null): void
    {
        // Sprache aus Parameter, Session oder Config-Fallback
        $requestedLang = $lang ?? ($_SESSION['lang'] ?? (defined('DEFAULT_LANG') ? DEFAULT_LANG : 'de_DE'));
        self::$currentLang = self::sanitizeLangCode((string)$requestedLang);

        // Pfad zur Sprachdatei
        $langRoot = realpath(REAL_BASE_DIR . '/../src/Lang');
        $langFile = ($langRoot ?: (REAL_BASE_DIR . '/../src/Lang')) . '/' . self::$currentLang . '/lang.php';
        if (file_exists($langFile)) {
            $msgs = include $langFile; // erwartet: return $message;
            // falls das Sprachfile $message benutzt aber nicht returned, fangen wir beides ab
            if (is_array($msgs)) {
                self::$messages = $msgs;
            } elseif (isset($message) && is_array($message)) {
                self::$messages = $message;
            }
        }

        // Fallback, wenn nichts geladen wurde
        if (empty(self::$messages)) {
            self::$messages = ['_LANGUAGE_ERROR' => 'Language file missing'];
        }
    }

    /**
     * Einzeltext holen
     */
    public static function get(string $key, string $default = ''): string
    {
        return self::$messages[$key] ?? ($default !== '' ? $default : "LANG ERROR ($key)");
    }

    /**
     * Komplettes Message-Array (für Templates / Backwards-Compatibility)
     */
    public static function getAll(): array
    {
        return self::$messages;
    }

    /**
     * Aktuelle Sprache
     */
    public static function getCurrent(): string
    {
        return self::$currentLang;
    }

    /**
     * Sprache setzen und neu laden
     */
    public static function setCurrent(string $lang): void
    {
        $safe = self::sanitizeLangCode($lang);
        self::init($safe);
        // Option: auch in Session speichern
        $_SESSION['lang'] = $safe;
    }

    // List all available language codes by scanning /src/Lang/*/lang.php
    /**
     * Kurzbeschreibung Funktion getAvailableLanguages
     *
     * @return array
     */
public static function getAvailableLanguages(): array
    {
        $langRoot = REAL_BASE_DIR . '/../src/Lang';
        if (!is_dir($langRoot)) {
            return [self::$currentLang];
        }

        $entries = scandir($langRoot) ?: [];
        $codes = [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $langFile = $langRoot . '/' . $entry . '/lang.php';
            if (is_file($langFile)) {
                $codes[] = $entry;
            }
        }

        sort($codes);
        if ($codes === []) {
            $codes[] = self::$currentLang;
        }

        return $codes;
    }

    /**
     * Liefert Label + Flagge für Sprach-Auswahl.
     */
    public static function getLanguageMeta(string $code): array
    {
        if (!preg_match('/^([a-z]{2})_([A-Z]{2})$/', $code, $matches)) {
            return ['label' => $code, 'flag' => '🏳️'];
        }

        $langCode = strtolower($matches[1]);
        $countryCode = strtoupper($matches[2]);

        return [
            'label' => self::buildLocaleLabel($langCode, $countryCode),
            'flag' => self::countryCodeToFlag($countryCode),
        ];
    }

    /**
     * Kurzbeschreibung Funktion buildLocaleLabel
     *
     * @param mixed $langCode
     * @param mixed $countryCode
     * @return string
     */
private static function buildLocaleLabel(string $langCode, string $countryCode): string
    {
        if (class_exists('\\Locale')) {
            $locale = $langCode . '_' . $countryCode;
            $language = \Locale::getDisplayLanguage($locale, $locale);
            $region = \Locale::getDisplayRegion($locale, $locale);

            if (is_string($language) && $language !== '' && strcasecmp($language, $langCode) !== 0) {
                if (is_string($region) && $region !== '' && strcasecmp($region, $countryCode) !== 0) {
                    return $language . ' (' . $region . ')';
                }
                return $language;
            }
        }

        return strtoupper($langCode) . ' (' . $countryCode . ')';
    }

    /**
     * Kurzbeschreibung Funktion countryCodeToFlag
     *
     * @param mixed $countryCode
     * @return string
     */
private static function countryCodeToFlag(string $countryCode): string
    {
        if (!preg_match('/^[A-Z]{2}$/', $countryCode)) {
            return '🏳️';
        }

        $first = 127397 + ord($countryCode[0]);
        $second = 127397 + ord($countryCode[1]);

        return html_entity_decode('&#' . $first . ';&#' . $second . ';', ENT_NOQUOTES, 'UTF-8');
    }

    /**
     * Kurzbeschreibung Funktion sanitizeLangCode
     *
     * @param mixed $lang
     * @return string
     */
private static function sanitizeLangCode(string $lang): string
    {
        $lang = trim($lang);
        if (!preg_match('/^[a-z]{2}_[A-Z]{2}$/', $lang)) {
            return 'de_DE';
        }

        $available = self::getAvailableLanguages();
        if (!in_array($lang, $available, true)) {
            return 'de_DE';
        }

        return $lang;
    }
}
