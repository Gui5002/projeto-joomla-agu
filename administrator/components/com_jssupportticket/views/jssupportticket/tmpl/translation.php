<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

?>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-jssupportticket-translation">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'LANGUAGE TRANSLATIONS';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('Language Translations'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>

        <div id="jsstadmin-data-wrp" class="js-padding-all-null js-ticket-box-shadow">
            <div id="js-language-wrapper" class="jsa-translations-panel">

                <div class="jsa-translations-head">
                    <div class="jsa-translations-headtext">
                        <div class="jstopheading"><?php echo Text::_('GET JS TICKETS TRANSLATIONS'); ?></div>
                        <p class="jsa-translations-sub"><?php echo Text::_('Language files are downloaded from the JS Support Ticket translation server and verified before they are installed'); ?>.</p>
                    </div>
                    <button type="button" id="gettranslation" class="jsa-btn-primary">
                        <span class="jsa-btn-label"><?php echo Text::_('GET TRANSLATIONS'); ?></span>
                    </button>
                </div>

                <div id="js-emessage-wrapper" class="jsa-note jsa-note-error" style="display:none;">
                    <div id="jslang_em_text"></div>
                </div>
                <div id="js-emessage-wrapper_ok" class="jsa-note jsa-note-ok" style="display:none;">
                    <div id="jslang_em_text_ok"></div>
                </div>

                <div id="jsa-translations-skeleton" class="jsa-translations-skeleton" style="display:none;">
                    <span></span><span></span><span></span>
                </div>

                <div id="js_ddl" style="display:none;">
                    <div class="jsa-translations-tablewrap">
                        <table class="adminlist jsa-translations-table" id="jsa-translations-table">
                            <thead>
                                <tr>
                                    <th><?php echo Text::_('Language'); ?></th>
                                    <th class="center"><?php echo Text::_('Keys'); ?></th>
                                    <th class="center"><?php echo Text::_('Updated'); ?></th>
                                    <th class="center"><?php echo Text::_('Status'); ?></th>
                                    <th class="center"><?php echo Text::_('Actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="jsa-translations-body"></tbody>
                        </table>
                    </div>
                    <p class="jsa-translations-hint"><?php echo Text::_('When the Joomla language changes, the JS Support Ticket language changes automatically to match'); ?>.</p>
                </div>

            </div>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
<script type="text/javascript">
    var jsstToken = <?php echo json_encode(Factory::getSession()->getFormToken()); ?>;
    var jsaLang = {
        installed:    <?php echo json_encode(Text::_('Installed')); ?>,
        outdated:     <?php echo json_encode(Text::_('Update available')); ?>,
        notInstalled: <?php echo json_encode(Text::_('Not installed')); ?>,
        install:      <?php echo json_encode(Text::_('Install')); ?>,
        update:       <?php echo json_encode(Text::_('Update')); ?>,
        reinstall:    <?php echo json_encode(Text::_('Reinstall')); ?>,
        working:      <?php echo json_encode(Text::_('Working')); ?>,
        refresh:      <?php echo json_encode(Text::_('REFRESH LIST')); ?>,
        noResults:    <?php echo json_encode(Text::_('No Record Found')); ?>,
        noPack:       <?php echo json_encode(Text::_('Install the Joomla language pack to switch the site to this language')); ?>,
        colLanguage:  <?php echo json_encode(Text::_('Language')); ?>,
        colKeys:      <?php echo json_encode(Text::_('Keys')); ?>,
        colUpdated:   <?php echo json_encode(Text::_('Updated')); ?>,
        colStatus:    <?php echo json_encode(Text::_('Status')); ?>,
        badResponse:  <?php echo json_encode(Text::_('The update server returned an unexpected response.')); ?>
    };

    function jsstTokenPayload(extra) {
        var payload = extra || {};
        payload[jsstToken] = 1;
        return payload;
    }

    // Rows are built from JSON with jQuery text()/attr() only - the server never
    // supplies HTML, so a compromised CDN cannot inject markup into this page.
    function jsaRenderTranslations(languages) {
        var $body = jQuery('#jsa-translations-body').empty();

        if (!languages || !languages.length) {
            jQuery('<tr>').append(
                jQuery('<td colspan="5" class="jsa-translations-empty">').text(jsaLang.noResults)
            ).appendTo($body);
            return;
        }

        jQuery.each(languages, function (i, lang) {
            var state  = lang.installed ? (lang.current ? 'ok' : 'stale') : 'none';
            var status = state === 'ok' ? jsaLang.installed : (state === 'stale' ? jsaLang.outdated : jsaLang.notInstalled);
            var action = state === 'ok' ? jsaLang.reinstall : (state === 'stale' ? jsaLang.update : jsaLang.install);

            // name + code stacked, so the code stays readable next to a native name
            var $name = jQuery('<td>')
                .attr('data-jsa-label', jsaLang.colLanguage)
                .addClass('jsa-lang-cell')
                .append(jQuery('<span class="jsa-lang-name">').text(lang.name))
                .append(jQuery('<span class="jsa-lang-code">').text(lang.code));

            if (!lang.joomla) {
                $name.append(jQuery('<span class="jsa-lang-warn">').text(jsaLang.noPack));
            }

            var $btn = jQuery('<button type="button">')
                .addClass('jsa-install-translation')
                .addClass(state === 'none' ? 'is-primary' : 'is-quiet')
                .attr('data-code', lang.code)
                .text(action);

            jQuery('<tr>')
                .attr('data-code', lang.code)
                .append($name)
                .append(jQuery('<td class="center jsa-num">').attr('data-jsa-label', jsaLang.colKeys).text(lang.keys ? lang.keys : '-'))
                .append(jQuery('<td class="center jsa-num">').attr('data-jsa-label', jsaLang.colUpdated).text(lang.updated || '-'))
                .append(
                    jQuery('<td class="center">').attr('data-jsa-label', jsaLang.colStatus).append(
                        jQuery('<span class="jsa-pill">').addClass('jsa-pill-' + state).text(status)
                    )
                )
                .append(jQuery('<td class="center jsa-action-cell">').append($btn))
                .appendTo($body);
        });
    }

    function jsaShowError(msg) {
        jQuery('#js-emessage-wrapper div').text(msg || jsaLang.badResponse);
        jQuery('#js-emessage-wrapper').show();
    }

    function jsaLoadList(force, $btn) {
        jQuery('#js-emessage-wrapper').hide();
        if (!jQuery('#js_ddl').is(':visible')) {
            jQuery('#jsa-translations-skeleton').show();
        }
        if ($btn) { $btn.prop('disabled', true).addClass('is-busy'); }

        jQuery.post(
            "index.php?option=com_jssupportticket&c=jssupportticket&task=translationslist",
            jsstTokenPayload(force ? { refresh: 1 } : {}),
            function (data) {
                jQuery('#jsa-translations-skeleton').hide();
                if ($btn) { $btn.prop('disabled', false).removeClass('is-busy'); }
                if (!data || data.error) { jsaShowError(data && data.error); return; }

                jQuery('#js_ddl').show();
                // the button becomes a refresh control once the list is on screen
                jQuery('#gettranslation').find('.jsa-btn-label').text(jsaLang.refresh);
                jsaRenderTranslations(data.languages);
            },
            'json'
        ).fail(function () {
            jQuery('#jsa-translations-skeleton').hide();
            if ($btn) { $btn.prop('disabled', false).removeClass('is-busy'); }
            jsaShowError();
        });
    }

    jQuery(document).ready(function(){
        // Show the catalogue straight away - the old flow made the admin click
        // once before anything at all appeared on the page.
        jsaLoadList(false, null);

        jQuery('#gettranslation').click(function(){
            jsaLoadList(true, jQuery(this));
        });

        jQuery(document).on('click', '.jsa-install-translation', function () {
            var $btn = jQuery(this);
            var code = $btn.attr('data-code');
            if (!code || $btn.prop('disabled')) { return; }

            var previous = $btn.text();
            $btn.prop('disabled', true).addClass('is-busy').text(jsaLang.working);
            jQuery('#js-emessage-wrapper_ok').hide();
            jQuery('#js-emessage-wrapper').hide();

            jQuery.post(
                "index.php?option=com_jssupportticket&c=jssupportticket&task=translationsinstall",
                jsstTokenPayload({ code: code }),
                function (data) {
                    if (!data || data.error) {
                        $btn.prop('disabled', false).removeClass('is-busy').text(previous);
                        jsaShowError(data && data.error);
                        return;
                    }
                    jQuery('#jslang_em_text_ok').text(data.message);
                    jQuery('#js-emessage-wrapper_ok').slideDown();
                    // refresh so status/actions reflect what is now on disk
                    jQuery.post(
                        "index.php?option=com_jssupportticket&c=jssupportticket&task=translationslist",
                        jsstTokenPayload({}),
                        function (d) {
                            if (d && !d.error) { jsaRenderTranslations(d.languages); }
                            else { $btn.prop('disabled', false).removeClass('is-busy').text(previous); }
                        },
                        'json'
                    ).fail(function () {
                        $btn.prop('disabled', false).removeClass('is-busy').text(previous);
                    });
                },
                'json'
            ).fail(function () {
                $btn.prop('disabled', false).removeClass('is-busy').text(previous);
                jsaShowError();
            });
        });
    });
</script>
