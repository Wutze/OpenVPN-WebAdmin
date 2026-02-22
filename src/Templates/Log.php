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

class Log
{
/**
 * Fuehrt index entsprechend der internen Logik aus.
 *
 * @return string Rueckgabe als Text.
 */
public function index(): string
    {
        $table = include __DIR__ . "/LogTable.php";
        $title = \Lang::get('_LOGS_TITLE');
        $subtitle = \Lang::get('_LOGS_SUBTITLE');
        return <<<HTML
<h1>{$title}</h1>
<p class="text-muted">{$subtitle}</p>
{$table}
HTML;
    }
}
