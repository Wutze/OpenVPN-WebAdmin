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

class Url
{
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

    private static function appPath(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        $base = self::basePath();
        return $base . $path;
    }

    /**
     * Baut interne App-URLs fuer op-Routen.
     *
     * @param string $op
     * @param array<string,mixed> $params
     * @return string
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
            return self::appPath($url);
        }

        return self::appPath('/' . ltrim($url, '/'));
    }
}
