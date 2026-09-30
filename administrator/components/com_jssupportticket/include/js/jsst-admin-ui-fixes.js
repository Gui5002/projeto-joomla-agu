(function () {
    'use strict';

    var emailStyleId = 'jsst-email-template-editor-content-style';
    var scanTimer = null;

    function styleEmailEditorFrame(frame) {
        if (!frame || frame.dataset.jsstEmailStyleReady === '1') {
            return;
        }

        try {
            var frameDocument = frame.contentDocument || (frame.contentWindow && frame.contentWindow.document);
            if (!frameDocument || !frameDocument.head || !frameDocument.body) {
                return;
            }

            if (!frameDocument.getElementById(emailStyleId)) {
                var style = frameDocument.createElement('style');
                style.id = emailStyleId;
                style.textContent = [
                    'html { background: #f1f5f9; }',
                    'body, body.mce-content-body { background: #ffffff; color: #334155; color-scheme: light; }',
                    'body.mce-content-body p, body.mce-content-body li, body.mce-content-body td,',
                    'body.mce-content-body th, body.mce-content-body span, body.mce-content-body a,',
                    'body.mce-content-body strong, body.mce-content-body em { color: inherit; }',
                    'body.mce-content-body img { max-width: 100%; height: auto; }'
                ].join('\n');
                frameDocument.head.appendChild(style);
            }

            frameDocument.documentElement.style.colorScheme = 'light';
            frameDocument.body.style.colorScheme = 'light';
            frame.dataset.jsstEmailStyleReady = '1';
        } catch (error) {
            // Do not block the page if a third-party editor uses a cross-origin frame.
        }
    }

    function scanEmailEditorFrames() {
        var page = document.querySelector('.jsst-email-template-v86');
        if (!page) {
            return;
        }

        page.querySelectorAll('iframe[id$="_ifr"], .tox-edit-area iframe, .js-editor-tinymce iframe').forEach(function (frame) {
            styleEmailEditorFrame(frame);

            if (frame.dataset.jsstEmailLoadBound !== '1') {
                frame.addEventListener('load', function () {
                    frame.dataset.jsstEmailStyleReady = '0';
                    styleEmailEditorFrame(frame);
                });
                frame.dataset.jsstEmailLoadBound = '1';
            }
        });
    }

    function getCalendarAction(button) {
        var action = button.getAttribute('data-action') || button.getAttribute('data-task') || '';
        var label = (button.textContent || '').trim();
        var value = (action || label).toLowerCase();

        if (value.indexOf('clear') !== -1) {
            return 'clear';
        }
        if (value.indexOf('today') !== -1) {
            return 'today';
        }
        // Joomla's own attribute for the third action is data-action="exit",
        // which takes precedence over the "Close" label below it - so matching
        // only on 'close' left that button untagged and unstyled.
        if (value.indexOf('close') !== -1 || value.indexOf('exit') !== -1) {
            return 'close';
        }
        return '';
    }

    function tagReportCalendars() {
        if (!document.querySelector('.jsst-screen-report-v73')) {
            return;
        }

        document.querySelectorAll('.js-calendar').forEach(function (calendar) {
            calendar.classList.add('jsst-report-calendar-popup');

            var actionButtons = Array.prototype.filter.call(calendar.querySelectorAll('button'), function (button) {
                var action = getCalendarAction(button);
                if (action) {
                    button.classList.add('jsst-calendar-action-button', 'jsst-calendar-action-' + action);
                    return true;
                }
                return false;
            });

            if (actionButtons.length < 2) {
                var fallbackGroup = Array.prototype.find.call(
                    calendar.querySelectorAll('.buttons-wrapper, .calendar-buttons, .calendar-footer, .btn-group'),
                    function (group) {
                        var directButtons = Array.prototype.filter.call(group.children, function (child) {
                            return child.tagName === 'BUTTON' || child.matches('.btn');
                        });
                        return directButtons.length === 3;
                    }
                );

                if (fallbackGroup) {
                    actionButtons = Array.prototype.filter.call(fallbackGroup.children, function (child) {
                        return child.tagName === 'BUTTON' || child.matches('.btn');
                    });
                    actionButtons.forEach(function (button, index) {
                        button.classList.add('jsst-calendar-action-button');
                        if (index === actionButtons.length - 1) {
                            button.classList.add('jsst-calendar-action-close');
                        }
                    });
                    fallbackGroup.classList.add('jsst-calendar-actions');
                    return;
                }
            }

            if (actionButtons.length < 2) {
                return;
            }

            var commonParent = actionButtons[0].parentElement;
            var sameParent = actionButtons.every(function (button) {
                return button.parentElement === commonParent;
            });

            if (sameParent && commonParent) {
                commonParent.classList.add('jsst-calendar-actions');
            }
        });
    }

    /**
     * Stamp each listing cell with its column heading.
     *
     * Below ~600px the listing tables used to be given
     * `overflow-x:auto;white-space:nowrap`, which just traded an overflowing
     * table for a horizontally scrolling one. To stack each row as a labelled
     * card instead, the CSS needs the heading text available per cell, and
     * `::before{content:attr(...)}` is the only way to get it there. Doing it
     * here keeps all 21 listing views untouched.
     */
    function labelListingCells() {
        document.querySelectorAll('.jsst-screen-list table#js-table').forEach(function (table) {
            var headings = Array.prototype.map.call(
                table.querySelectorAll('thead th'),
                function (th) {
                    // ignore a checkbox/ordering header - its label adds nothing
                    return th.querySelector('input, select') ? '' : (th.textContent || '').trim();
                }
            );

            if (!headings.length) {
                return;
            }

            table.querySelectorAll('tbody tr').forEach(function (row) {
                // "No Record Found" and other colspan rows are not a column grid
                var spans = Array.prototype.some.call(row.cells, function (cell) {
                    return cell.colSpan > 1;
                });

                Array.prototype.forEach.call(row.cells, function (cell, index) {
                    var label = spans ? '' : (headings[index] || '');
                    if (cell.getAttribute('data-jsst-label') !== label) {
                        cell.setAttribute('data-jsst-label', label);
                    }
                });
            });
        });
    }

    function scanUiFixes() {
        scanEmailEditorFrames();
        tagReportCalendars();
        labelListingCells();
    }

    function queueScan() {
        if (scanTimer !== null) {
            return;
        }
        scanTimer = window.setTimeout(function () {
            scanTimer = null;
            scanUiFixes();
        }, 30);
    }

    function start() {
        scanUiFixes();

        var observer = new MutationObserver(queueScan);
        observer.observe(document.documentElement, {
            childList: true,
            subtree: true
        });

        // TinyMCE can initialise after Joomla's DOM ready event without adding
        // the iframe immediately. A short bounded retry covers that path.
        var retries = 0;
        var retryTimer = window.setInterval(function () {
            scanUiFixes();
            retries += 1;
            if (retries >= 30) {
                window.clearInterval(retryTimer);
            }
        }, 250);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }
})();
