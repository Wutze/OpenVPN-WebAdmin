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
/**
 * Modal und JavaScript für die erweiterte Dokumentation.
 *
 * Alle Spracheinträge werden serverseitig nach [HELP_DOC/<bereich>/:<dokument>]
 * durchsucht (Lang::getDocRefMap()). Das nachfolgende Skript blendet überall dort,
 * wo ein solcher Sprachtext im Dokument erscheint, ein klickbares 🛟-Symbol ein
 * und lädt beim Anklicken die zugehörige Markdown-Datei per AJAX in das Modal.
 * Zusätzlich können Elemente über die Attribute data-helpdoc-area und
 * data-helpdoc-doc direkt verdrahtet werden.
 */
use Micro\OpenvpnWebadmin\Util\Lang;
?>
<div class="modal fade" id="helpdoc-modal" tabindex="-1" aria-labelledby="helpdoc-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpdoc-modal-title"><?= htmlspecialchars(Lang::get('HELP_DOC_MODAL_TITLE'), ENT_QUOTES) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(Lang::get('HELP_DOC_CLOSE'), ENT_QUOTES, 'UTF-8') ?>"></button>
            </div>
            <div class="modal-body help-doc-content" id="helpdoc-modal-body"></div>
        </div>
    </div>
</div>
<script nonce="<?= htmlspecialchars(defined('_CSP_NONCE') ? _CSP_NONCE : '', ENT_QUOTES, 'UTF-8') ?>">
window.HelpDoc = (() => {
    const labels = <?= json_encode([
        'open' => Lang::get('HELP_DOC_OPEN'),
        'loading' => Lang::get('HELP_DOC_LOADING'),
        'unavailable' => Lang::get('HELP_DOC_NOT_AVAILABLE'),
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    // Sichtbarer Text → Dokumentationsverweis, aus allen Spracheinträgen
    // der aktiven Sprache mit [HELP_DOC/...]-Marker.
    const docRefs = <?= json_encode(Lang::getDocRefMap(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const refByText = new Map(docRefs.map((ref) => [ref.text, ref]));

    const modalBody = () => document.getElementById('helpdoc-modal-body');

    const createIcon = (ref) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'help-doc-icon';
        button.dataset.helpdocArea = ref.area;
        button.dataset.helpdocDoc = ref.doc;
        button.title = labels.open;
        button.setAttribute('aria-label', labels.open);
        button.textContent = '🛟';
        return button;
    };

    const showMessage = (message) => {
        const body = modalBody();
        const alert = document.createElement('div');
        alert.className = 'alert alert-warning mb-0';
        alert.textContent = message;
        body.innerHTML = '';
        body.appendChild(alert);
    };

    const open = (area, doc) => {
        const body = modalBody();
        const loading = document.createElement('p');
        loading.className = 'text-muted mb-0';
        loading.textContent = labels.loading;
        body.innerHTML = '';
        body.appendChild(loading);
        const modal = document.getElementById('helpdoc-modal');
        bootstrap.Modal.getOrCreateInstance(modal).show();

        fetch('/?op=helpdocshow&area=' + encodeURIComponent(area) + '&doc=' + encodeURIComponent(doc), {
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            credentials: 'same-origin'
        })
            .then((response) => response.json())
            .then((data) => {
                if (data && data.ok && typeof data.html === 'string') {
                    body.innerHTML = data.html;
                } else {
                    showMessage(data && data.message ? String(data.message) : labels.unavailable);
                }
            })
            .catch(() => showMessage(labels.unavailable));
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-helpdoc-area][data-helpdoc-doc]');
        if (!trigger) {
            return;
        }
        event.preventDefault();
        open(trigger.dataset.helpdocArea, trigger.dataset.helpdocDoc);
    });

    const hasIcon = (node) => {
        const next = node.nextSibling;
        return next !== null && next.nodeType === 1 && next.classList.contains('help-doc-icon');
    };

    // Hängt an jeden Textknoten, dessen Inhalt exakt einem Sprachtext mit
    // Dokumentationsverweis entspricht, das 🛟-Symbol an.
    const enhance = (root) => {
        if (refByText.size === 0) {
            return;
        }
        const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
            acceptNode: (node) => {
                if (!node.nodeValue || !refByText.has(node.nodeValue.trim())) {
                    return NodeFilter.FILTER_REJECT;
                }
                const parent = node.parentElement;
                if (!parent || parent.closest('script, style, #helpdoc-modal')) {
                    return NodeFilter.FILTER_REJECT;
                }
                return NodeFilter.FILTER_ACCEPT;
            }
        });
        const nodes = [];
        while (walker.nextNode()) {
            nodes.push(walker.currentNode);
        }
        nodes.forEach((node) => {
            if (hasIcon(node)) {
                return;
            }
            const ref = refByText.get(node.nodeValue.trim());
            node.parentElement.insertBefore(createIcon(ref), node.nextSibling);
        });
    };

    enhance(document.body);

    // Nachgeladene oder geänderte Inhalte (z. B. per textContent) nachrüsten
    new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.type === 'characterData') {
                if (mutation.target.parentElement) {
                    enhance(mutation.target.parentElement);
                }
                continue;
            }
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    if (!node.classList.contains('help-doc-icon')) {
                        enhance(node);
                    }
                } else if (node.nodeType === 3 && node.parentElement) {
                    enhance(node.parentElement);
                }
            });
        }
    }).observe(document.body, {childList: true, subtree: true, characterData: true});

    return {open, createIcon, enhance};
})();
</script>
