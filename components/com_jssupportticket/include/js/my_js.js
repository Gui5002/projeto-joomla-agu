function suretodelete(id) {
    var currentRow = jQuery('div#tk-row-' + id);
    currentRow.children().hide();
    currentRow.children('div.js-suredelete').show();
}

function canceldelete(id) {
    var currentRow = jQuery('div#tk-row-' + id);
    currentRow.children().show();
    currentRow.children('div.js-suredelete').hide();
}

function yesdeletethisrecord(id, cname, mname, jsession) {
    var currentRow = jQuery('div#tk-row-' + id);
    var tempdiv = currentRow.children('div.js-suredelete');
    var link = 'index.php?option=com_jssupportticket&c=' + encodeURIComponent(cname) +
        '&task=' + encodeURIComponent(mname) + '&' + encodeURIComponent(jsession) + '=1';
    var hostname = jQuery('input#joomlinkforjs').val() || '';
    var original = tempdiv.html();

    jQuery.post(link, { rowid: id })
        .done(function (data) {
            var response;
            try {
                response = typeof data === 'string' ? JSON.parse(data) : data;
            } catch (error) {
                response = null;
            }

            if (!Array.isArray(response) || response.length < 2) {
                currentRow.children().show();
                tempdiv.hide().html(original);
                return;
            }

            tempdiv.html(response[1]);
            if (Number(response[0]) === 1) {
                tempdiv.prepend('<img class="warning-icon" alt="" aria-hidden="true" src="' + hostname + 'components/com_jssupportticket/include/images/successful.png" />');
                return;
            }

            tempdiv.prepend('<img class="warning-icon" alt="" aria-hidden="true" src="' + hostname + 'components/com_jssupportticket/include/images/not_allow.png" />');
            window.setTimeout(function () {
                currentRow.children().show();
                tempdiv.hide().html(original);
            }, 3000);
        })
        .fail(function () {
            currentRow.children().show();
            tempdiv.hide().html(original);
        });
}
