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

use Micro\OpenvpnWebadmin\Util\Lang;

/**
 * Erstellt View-Markup für Verweise auf die ergänzende interne Dokumentation.
 * Das globale JavaScript in views/layout/helpdoc.php lädt das Dokument anhand
 * der data-helpdoc-* Attribute in das Modal.
 */
class HelpDoc
{
    /**
     * Rendert ein escaped 🛟-Modal-Element aus einem area/doc-Verweis oder einen Leerstring.
     * @param array{area: string, doc: string}|null $ref
     * @return string
     */
    public static function icon(?array $ref): string
    {
        if ($ref === null) {
            return '';
        }

        $label = Lang::get('HELP_DOC_OPEN');

        return ' <button type="button" class="help-doc-icon"'
            . ' data-helpdoc-area="' . htmlspecialchars($ref['area'], ENT_QUOTES) . '"'
            . ' data-helpdoc-doc="' . htmlspecialchars($ref['doc'], ENT_QUOTES) . '"'
            . ' title="' . htmlspecialchars($label, ENT_QUOTES) . '"'
            . ' aria-label="' . htmlspecialchars($label, ENT_QUOTES) . '">🛟</button>';
    }

    /**
     * Ermittelt den Dokumentverweis eines Sprachschlüssels und rendert dessen Hilfesymbol.
     * @param string $key
     * @return string
     */
    public static function iconFor(string $key): string
    {
        return self::icon(Lang::getDocRef($key));
    }
}
