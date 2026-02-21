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

use Micro\OpenvpnWebadmin\Templates\User;
use Micro\OpenvpnWebadmin\Templates\Template;

/**
 * Summary of UserController
 */
class UserController
{
    /**
     * Kurzbeschreibung Funktion index
     *
     * @return void
     */
public function index(): void
    {
        $content = (new User())->index();

        $tpl = new Template("OpenVPN WebAdmin – Benutzer", $content, [
            'user' => $_SESSION['user'] ?? null,
        ]);

        echo $tpl->render();
    }
}