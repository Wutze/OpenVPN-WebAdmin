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
                return $params === [] ? '/' : '/?' . http_build_query($params);
            }

            if ($op === 'setlang' && isset($params['lang']) && is_string($params['lang']) && $params['lang'] !== '') {
                $lang = rawurlencode($params['lang']);
                unset($params['lang']);
                return '/setlang/' . $lang . ($params === [] ? '' : '?' . http_build_query($params));
            }

            return '/' . rawurlencode($op) . ($params === [] ? '' : '?' . http_build_query($params));
        }

        return '/?' . http_build_query(array_merge(['op' => $op], $params));
    }
}

