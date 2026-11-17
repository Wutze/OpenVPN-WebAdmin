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

namespace Micro\OpenvpnWebadmin\Core;

class Url
{
/**
 * Ermittelt den normalisierten URL-Präfix der Anwendung aus `_APP_BASE_PATH`.
 *
 * @return string Leerstring für nicht gesetzten/root-Präfix, sonst führender Slash ohne Abschlussslash.
 */
private static function basePath(): string
    {
        if (!defined('_APP_BASE_PATH')) {
            return '';
        }
        $base = (string)_APP_BASE_PATH;
        if ($base === '' || $base === '/') {
            return '';
        }
        return '/' . trim($base, '/');
    }

/**
 * Ergänzt einen relativen oder absoluten Pfad um den konfigurierten Anwendungspräfix.
 *
 * @param string $path Pfad, dessen führende Slashes entfernt und durch genau einen ersetzt werden.
 * @return string Zusammengesetzter interner Anwendungspfad.
 */
private static function appPath(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $base = self::basePath();
        return $base . $path;
    }

    /**
     * Baut eine interne URL für eine `op`-Route, je nach Konfiguration als Rewrite- oder Query-URL.
     *
     * @param string $op Routenkennung; leer wird zu `main`.
     * @param array<string,mixed> $params Query-Parameter; im Rewrite-Modus wird `lang` bei `setlang` Teil des Pfads.
     * @return string URL inklusive App-Präfix und gegebenenfalls kodierter Query.
     */
    public static function op(string $op, array $params = []): string
    {
        $op = trim($op);
        if ($op === '') {
            $op = 'main';
        }

        if (defined('_URL_REWRITE') && _URL_REWRITE === true) {
            if ($op === 'main' || $op === 'dashboard') {
                return $params === [] ? self::appPath('/') : self::appPath('/?' . http_build_query($params));
            }

            if ($op === 'setlang' && isset($params['lang']) && is_string($params['lang']) && $params['lang'] !== '') {
                $lang = rawurlencode($params['lang']);
                unset($params['lang']);
                return self::appPath('/setlang/' . $lang) . ($params === [] ? '' : '?' . http_build_query($params));
            }

            return self::appPath('/' . rawurlencode($op)) . ($params === [] ? '' : '?' . http_build_query($params));
        }

        return self::appPath('/?' . http_build_query(array_merge(['op' => $op], $params)));
    }

/**
 * Normalisiert einen internen URL-Wert und erhält absolute URLs unverändert.
 *
 * @param string $url Absolute URL, `?op=`-Query, Querystring oder Pfad.
 * @return string Absolute URLs unverändert; interne Werte erhalten den App-Präfix und `?op=` wird über `op()` gebaut.
 */
public static function normalizeInternal(string $url): string
    {
        if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $url)) {
            return $url;
        }

        if (str_starts_with($url, '?op=')) {
            parse_str(ltrim($url, '?'), $params);
            $op = (string)($params['op'] ?? 'main');
            unset($params['op']);
            return self::op($op, $params);
        }

        if (str_starts_with($url, '?')) {
            return self::appPath('/?' . ltrim($url, '?'));
        }

        if (str_starts_with($url, '/')) {
            $base = self::basePath();
            if ($base !== '' && ($url === $base || str_starts_with($url, $base . '/'))) {
                return $url;
            }
            return self::appPath($url);
        }

        return self::appPath('/' . ltrim($url, '/'));
    }
}
