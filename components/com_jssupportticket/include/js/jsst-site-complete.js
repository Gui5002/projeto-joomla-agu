(function (window, document) {
    'use strict';

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
        } else {
            callback();
        }
    }

    function setAccessibleClose(element, label) {
        if (!element) return;
        element.setAttribute('role', 'button');
        element.setAttribute('tabindex', '0');
        element.setAttribute('aria-label', label || 'Close');
        element.setAttribute('title', label || 'Close');
        element.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                element.click();
            }
        });
    }

    function normalizeIconActions(root) {
        root.querySelectorAll('a.js-tk-button, a.js-ticket-action-button, .js-ticket-table-body-col a').forEach(function (link) {
            var image = link.querySelector('img');
            var label = link.getAttribute('aria-label') ||
                (image && (image.getAttribute('alt') || image.getAttribute('title'))) ||
                link.getAttribute('title');

            if (!label && image) {
                var source = (image.getAttribute('src') || '').toLowerCase();
                if (source.indexOf('edit') !== -1) label = 'Edit';
                else if (source.indexOf('delete') !== -1 || source.indexOf('remove') !== -1) label = 'Delete';
                else if (source.indexOf('download') !== -1) label = 'Download';
                else if (source.indexOf('permission') !== -1) label = 'Permissions';
                else if (source.indexOf('view') !== -1) label = 'View';
            }
            if (!label) {
                label = (link.textContent || '').trim().replace(/\s+/g, ' ');
            }
            if (label) {
                link.setAttribute('aria-label', label);
                link.setAttribute('title', label);
                if (image && !image.hasAttribute('alt')) image.setAttribute('alt', '');
            }

            // Several legacy action anchors have only onclick handlers. Make them
            // reachable and operable without changing their existing controller code.
            var href = link.getAttribute('href');
            if (!href || href === '#') {
                link.setAttribute('role', 'button');
                link.setAttribute('tabindex', '0');
                if (!link.dataset.jsstKeyboardAction) {
                    link.dataset.jsstKeyboardAction = '1';
                    link.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            link.click();
                        }
                    });
                }
            }
        });
    }

    function normalizeEmptyImages(root) {
        root.querySelectorAll('img').forEach(function (image) {
            image.addEventListener('error', function () {
                var holder = image.closest('.jsst-cp-empty-icon, .jsst-empty-icon, .js-ticket-category-download-logo');
                if (holder) {
                    image.style.display = 'none';
                    holder.classList.add('jsst-image-fallback');
                    holder.setAttribute('aria-hidden', 'true');
                }
            }, { once: true });
        });
    }

    function improveDeleteDialogs(root) {
        root.querySelectorAll('.js-suredelete').forEach(function (dialog) {
            dialog.setAttribute('role', 'alertdialog');
            dialog.setAttribute('aria-live', 'polite');
            dialog.querySelectorAll('a').forEach(function (button) {
                button.setAttribute('role', 'button');
                button.setAttribute('tabindex', '0');
                button.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        button.click();
                    }
                });
            });
        });
    }

    function improveLegacyActionAnchors(root) {
        root.querySelectorAll('a[onclick]:not([href])').forEach(function (link) {
            link.setAttribute('role', 'button');
            link.setAttribute('tabindex', '0');
            if (link.dataset.jsstKeyboardAction) return;
            link.dataset.jsstKeyboardAction = '1';
            link.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    link.click();
                }
            });
        });
    }

    function improveAttachmentControls(root) {
        root.querySelectorAll('.tk_attachments_addform, .tk_attachment_remove').forEach(function (control) {
            control.setAttribute('role', 'button');
            control.setAttribute('tabindex', '0');
            if (!control.getAttribute('aria-label')) {
                control.setAttribute('aria-label', control.classList.contains('tk_attachment_remove') ? 'Remove attachment' : 'Add another attachment');
            }
            if (control.dataset.jsstKeyboardAction) return;
            control.dataset.jsstKeyboardAction = '1';
            control.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    control.click();
                }
            });
        });
    }

    function enhanceDynamicContent(root) {
        root.querySelectorAll('.popup-header-close-img, .close-history, .jsst-dialog-close, #js-ticket-popup-close-button')
            .forEach(function (element) { setAccessibleClose(element, 'Close'); });
        normalizeIconActions(root);
        normalizeEmptyImages(root);
        improveDeleteDialogs(root);
        improveLegacyActionAnchors(root);
        improveAttachmentControls(root);
    }

    ready(function () {
        var root = document.getElementById('jsst-site');
        if (!root) return;

        document.body.classList.add('jsst-site-active');

        // Joomla templates often place the component beside modules. Respond to the
        // component's real width rather than only to the browser viewport.
        function updateContainerClasses() {
            var width = root.getBoundingClientRect().width;
            root.classList.toggle('jsst-cq-compact', width <= 1180);
            root.classList.toggle('jsst-cq-tablet', width <= 980);
            root.classList.toggle('jsst-cq-narrow', width <= 760);
            root.classList.toggle('jsst-cq-mobile', width <= 520);
        }

        updateContainerClasses();
        if ('ResizeObserver' in window) {
            var sizeObserver = new ResizeObserver(function () {
                window.requestAnimationFrame(updateContainerClasses);
            });
            sizeObserver.observe(root);
        } else {
            window.addEventListener('resize', updateContainerClasses, { passive: true });
        }

        enhanceDynamicContent(root);

        root.addEventListener('click', function (event) {
            var legacyAction = event.target.closest('a[href="#"]');
            if (legacyAction && root.contains(legacyAction)) {
                event.preventDefault();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;
            var openPopup = root.querySelector('#js-ticket-main-popup:not([style*=\"display:none\"]), .js-ticket-main-popup:not([style*=\"display:none\"]), .jsst-modal[aria-hidden=\"false\"]');
            if (!openPopup) return;
            var close = openPopup.querySelector('#js-ticket-popup-close-button, .popup-header-close-img, .jsst-dialog-close, .close-history, [data-jsst-close]');
            if (close) close.click();
        });

        // Mark the active Joomla pagination item consistently across Joomla versions.
        root.querySelectorAll('#jl_pagination_pageslink li').forEach(function (item) {
            if (item.classList.contains('active') || item.querySelector('[aria-current="page"]')) {
                item.classList.add('is-current');
            }
        });

        // AJAX popups and ticket threads are inserted after initial load.
        if ('MutationObserver' in window) {
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType === 1) enhanceDynamicContent(node);
                    });
                });
            });
            observer.observe(root, { childList: true, subtree: true });
        }
    });
}(window, document));
