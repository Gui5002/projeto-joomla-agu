(function (window, document, $) {
    'use strict';

    if (!$) {
        return;
    }

    function readText($zone, name, fallback) {
        var value = $zone.attr('data-' + name);
        return value !== undefined && value !== '' ? value : fallback;
    }

    function getMaximum($zone) {
        var maximum = parseInt($zone.attr('data-max-files'), 10);
        return Number.isFinite(maximum) && maximum > 0 ? maximum : 1;
    }

    function isSingleFileZone($zone) {
        return $zone.attr('data-single-file') === '1';
    }

    function findRow($input) {
        return $input.closest('.js-value-text, .js-value-attachment-text, .js-attachment-file-box, .jsst-attachment-input-row');
    }

    function ensureFileName($row, $zone) {
        var $name = $row.children('.jsst-attachment-file-name');
        if (!$name.length) {
            $name = $('<span class="jsst-attachment-file-name" aria-live="polite"></span>');
            var $remove = $row.children('.js-attachment-remove').first();
            if ($remove.length) {
                $name.insertBefore($remove);
            } else {
                $row.append($name);
            }
        }
        if (!$name.text()) {
            $name.text(readText($zone, 'empty-label', 'No file selected'));
        }
        return $name;
    }

    function updateInputRow(input) {
        var $input = $(input);
        var $zone = $input.closest('.jsst-admin-attachment-zone');
        var $row = findRow($input);
        if (!$row.length || !$zone.length) {
            return;
        }

        $row.addClass('jsst-attachment-input-row');
        var $name = ensureFileName($row, $zone);
        var files = input.files;
        var names = [];
        if (files && files.length) {
            for (var i = 0; i < files.length; i += 1) {
                names.push(files[i].name);
            }
        }

        var hasFile = names.length > 0;
        $name.text(hasFile ? names.join(', ') : readText($zone, 'empty-label', 'No file selected'));
        $row.toggleClass('is-file-attached', hasFile);
        $row.attr('aria-hidden', hasFile ? 'false' : 'true');
    }

    function prepareRow($row, $zone) {
        if (!$row.length) {
            return;
        }
        $row.addClass('jsst-attachment-input-row');
        var $remove = $row.children('.js-attachment-remove').first();
        if ($remove.length) {
            $remove.attr({
                role: 'button',
                tabindex: '0',
                'aria-label': readText($zone, 'remove-label', 'Remove file')
            });
        }
        var input = $row.find('input[type="file"]').first().get(0);
        if (input) {
            updateInputRow(input);
        }
    }

    function prepareZone($zone) {
        if ($zone.data('jsstAttachmentReady')) {
            return;
        }

        $zone.data('jsstAttachmentReady', true);
        $zone.addClass('jsst-admin-attachment-zone jsst-attachment-enhanced');

        if (!$zone.children('.jsst-dropzone-message').length) {
            var label = readText($zone, 'drop-label', 'Drag and drop files here');
            var subLabel = readText($zone, 'browse-label', 'or click to browse files');
            var $message = $('<button type="button" class="jsst-dropzone-message"></button>');
            $message.append('<span class="jsst-dropzone-icon" aria-hidden="true">⇧</span>');
            var $copy = $('<span class="jsst-dropzone-copy"></span>');
            $copy.append($('<strong></strong>').text(label));
            $copy.append($('<small></small>').text(subLabel));
            $message.append($copy);
            $zone.prepend($message);
        }

        $zone.find('input[type="file"]').each(function () {
            prepareRow(findRow($(this)), $zone);
        });
    }

    function cloneEmptyInput($zone) {
        var $template = $zone.find('input[type="file"]').first();
        if (!$template.length) {
            return $();
        }

        var $templateRow = findRow($template);
        var $row = $templateRow.clone(false, false);
        var $input = $row.find('input[type="file"]').first();

        $input.val('');
        $input.removeAttr('id');
        $input.removeClass('invalid');
        $input.removeClass('required');
        $row.removeClass('is-file-attached');
        $row.attr('aria-hidden', 'true');
        $row.children('.jsst-attachment-file-name').text(readText($zone, 'empty-label', 'No file selected'));
        $zone.append($row);
        prepareRow($row, $zone);
        return $input;
    }

    function getEmptyInput($zone) {
        return $zone.find('input[type="file"]').filter(function () {
            return !this.files || this.files.length === 0;
        }).first();
    }

    function openPicker($zone, forceNew) {
        var $input = forceNew ? $() : getEmptyInput($zone);
        var current = $zone.find('input[type="file"]').length;
        var maximum = getMaximum($zone);

        if (!$input.length && !isSingleFileZone($zone) && current < maximum) {
            $input = cloneEmptyInput($zone);
        }

        if (!$input.length) {
            $input = $zone.find('input[type="file"]').first();
        }

        if ($input.length) {
            $input.get(0).click();
        }
    }

    function setInputFile(input, file, $zone) {
        if (typeof window.DataTransfer === 'undefined') {
            window.alert(readText($zone, 'browse-required-message', 'Please use the file browser to attach files.'));
            return false;
        }

        var transfer = new window.DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
        $(input).trigger('change');
        return true;
    }

    function addDroppedFiles($zone, files) {
        var maximum = getMaximum($zone);
        var single = isSingleFileZone($zone);

        if (single && files.length > 1) {
            window.alert(readText($zone, 'single-message', 'Only one file can be attached here.'));
        }

        var limit = single ? 1 : files.length;
        for (var i = 0; i < limit; i += 1) {
            var $input = getEmptyInput($zone);
            var count = $zone.find('input[type="file"]').length;

            if (!$input.length && !single && count < maximum) {
                $input = cloneEmptyInput($zone);
            }

            if (!$input.length) {
                if (single) {
                    $input = $zone.find('input[type="file"]').first();
                } else {
                    window.alert(readText($zone, 'limit-message', 'File upload limit exceeded.'));
                    break;
                }
            }

            if (!$input.length || !setInputFile($input.get(0), files[i], $zone)) {
                break;
            }
        }
    }

    function resetOrRemoveRow($remove) {
        var $zone = $remove.closest('.jsst-admin-attachment-zone');
        var $row = $remove.closest('.jsst-attachment-input-row');
        var rows = $zone.find('.jsst-attachment-input-row').length;
        var $input = $row.find('input[type="file"]').first();

        if (rows > 1 && !$input.hasClass('required')) {
            $row.remove();
        } else {
            if ($input.length) {
                $input.val('');
                updateInputRow($input.get(0));
            }
        }

        $zone.closest('.js-value').find('.jsst-attachment-add').prop('hidden', false).show();
    }

    function initialise(context) {
        $(context || document).find('[data-jsst-dropzone="1"]').each(function () {
            prepareZone($(this));
        });
    }

    $(document)
        .on('change', '.jsst-admin-attachment-zone input[type="file"]', function () {
            updateInputRow(this);
        })
        .on('click', '.jsst-admin-attachment-zone .jsst-dropzone-message', function (event) {
            event.preventDefault();
            openPicker($(this).closest('.jsst-admin-attachment-zone'), false);
        })
        .on('click', '.jsst-attachment-add', function (event) {
            event.preventDefault();
            var $zone = $(this).closest('.js-value').find('.jsst-admin-attachment-zone').first();
            if (!$zone.length) {
                return;
            }
            var current = $zone.find('input[type="file"]').length;
            if (current >= getMaximum($zone)) {
                window.alert(readText($zone, 'limit-message', 'File upload limit exceeded.'));
                return;
            }
            openPicker($zone, false);
        })
        .on('click', '.jsst-admin-attachment-zone .js-attachment-remove', function (event) {
            event.preventDefault();
            resetOrRemoveRow($(this));
        })
        .on('keydown', '.jsst-admin-attachment-zone .js-attachment-remove', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                resetOrRemoveRow($(this));
            }
        })
        .on('dragenter dragover', '.jsst-admin-attachment-zone', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var $zone = $(this);
            $zone.addClass('is-dragover');
            $zone.children('.jsst-dropzone-message').find('strong').first().text(readText($zone, 'release-label', 'Release files to attach'));
        })
        .on('dragleave dragend', '.jsst-admin-attachment-zone', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var $zone = $(this);
            $zone.removeClass('is-dragover');
            $zone.children('.jsst-dropzone-message').find('strong').first().text(readText($zone, 'drop-label', 'Drag and drop files here'));
        })
        .on('drop', '.jsst-admin-attachment-zone', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var $zone = $(this);
            $zone.removeClass('is-dragover');
            $zone.children('.jsst-dropzone-message').find('strong').first().text(readText($zone, 'drop-label', 'Drag and drop files here'));
            var original = event.originalEvent || event;
            var files = original.dataTransfer && original.dataTransfer.files ? original.dataTransfer.files : null;
            if (files && files.length) {
                addDroppedFiles($zone, files);
            }
        });

    $(function () {
        initialise(document);
    });

    window.jsstInitAdminAttachmentDropzones = initialise;
}(window, document, window.jQuery));
