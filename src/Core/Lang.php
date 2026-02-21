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
        self::$currentLang = $lang ?? ($_SESSION['lang'] ?? (defined('DEFAULT_LANG') ? DEFAULT_LANG : 'de_DE'));

        // Pfad zur Sprachdatei
        $langFile = REAL_BASE_DIR . '/../src/Lang/' . self::$currentLang . '/lang.php';
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
        self::init($lang);
        // Option: auch in Session speichern
        $_SESSION['lang'] = $lang;
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
        return match ($code) {
            'de_DE' => ['label' => 'Deutsch', 'flag' => '🇩🇪'],
            'en_EN' => ['label' => 'English', 'flag' => '🇬🇧'],
            default => ['label' => $code, 'flag' => '🏳️'],
        };
    }
}
