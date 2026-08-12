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

<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li>
                            <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard">
                                <?php echo Text::_('Dashboard'); ?>
                            </a>
                        </li>
                        <li>
                            <?php echo Text::_('Language Translations'); ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="jsstadmin-wrapper-top-right">
                <div id="jsstadmin-config-btn">
                    <a title="Configuration" href="index.php?option=com_jssupportticket&c=config&layout=config">
                        <img alt="Configuration" src="components/com_jssupportticket/include/images/config.png">
                    </a>
                </div>
                <div id="jsstadmin-vers-txt">
                    <?php echo Text::_('Version').Text::_(' : '); ?>
                    <span class="jsstadmin-ver">
                        <?php $version = str_split($this->version);
                        $version = implode('.', $version);
                        echo $version; ?>
                    </span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo Text::_('Language Translations'); ?></h4>
        </div>
        <div id="black_wrapper_translation"></div>
        <div id="jstran_loading">
            <img src="components/com_jssupportticket/include/images/spinning-wheel.gif" />
        </div>

        <div id="jsstadmin-data-wrp" class="js-padding-all-null js-ticket-box-shadow">
            <div id="js-language-wrapper">
                <div class="jstopheading"><?php echo Text::_('Get JS Tickets Translations');?></div>
                <div id="gettranslation" class="gettranslation"><img style="width:18px; height:auto;" src="components/com_jssupportticket/include/images/download-icon.png" /><?php echo Text::_('Get Translations');?></div>
                <div id="js_ddl">
                    <span class="title"><?php echo Text::_('Select Translation');?>:</span>
                    <span class="combo" id="js_combo"></span>
                    <span class="button" id="jsdownloadbutton"><img style="width:14px; height:auto;" src="components/com_jssupportticket/include/images/download-icon.png" /><?php echo Text::_('Download');?></span>
                    <div id="jscodeinputbox" class="js-some-disc"></div>
                    <div class="js-some-disc"><img style="width:18px; height:auto;" src="components/com_jssupportticket/include/images/info-icon.png" /><?php echo Text::_('When Joomla language change to ro, JS Jobs language will auto change to ro');?></div>
                </div>
                <div id="js-emessage-wrapper">
                    <img src="components/com_jssupportticket/include/images/c_error.png" />
                    <div id="jslang_em_text"></div>
                </div>
                <div id="js-emessage-wrapper_ok">
                    <img src="components/com_jssupportticket/include/images/saved.png" />
                    <div id="jslang_em_text_ok"></div>
                </div>
            </div>
            <div id="js-lang-toserver" class="jsticket-lang-btm-btn">
                <div class="js-col-xs-12 js-col-md-8 col"><a class="anc one" href="https://www.transifex.com/joom-sky/js-support-ticket" target="_blank"><img src="components/com_jssupportticket/include/images/translation-icon.png" /><?php echo Text::_('Contribute In Translation');?></a></div>
            </div>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#gettranslation').click(function(){
            jsShowLoading();
            jQuery.post("index.php?option=com_jssupportticket&c=jssupportticket&task=getlisttranslations&<?php echo Factory::getSession()->getFormToken(); ?>=1",{}, function (data) {
                if (data) {
                    jsHideLoading();
                    data = JSON.parse(data);
                    if(data['error']){
                        jQuery('#js-emessage-wrapper div').html(data['error']);
                        jQuery('#js-emessage-wrapper').show();
                    }else{
                        jQuery('#js-emessage-wrapper').hide();
                        jQuery('#gettranslation').hide();
                        jQuery('div#js_ddl').show();
                        jQuery('span#js_combo').html(data['data']);
                    }
                }
            });
        });
        
        jQuery(document).on('change', 'select#translations' ,function() {
            var lang_name = jQuery( this ).val();
            if(lang_name != ''){
                jQuery('#js-emessage-wrapper_ok').hide();
                jsShowLoading();
                jQuery.post("index.php?option=com_jssupportticket&c=jssupportticket&task=validateandshowdownloadfilename&<?php echo Factory::getSession()->getFormToken(); ?>=1",{ langname:lang_name}, function (data) {
                    if (data) {
                        jsHideLoading();
                        data = JSON.parse(data);
                        if(data['error']){
                            jQuery('#js-emessage-wrapper div').html(data['error']);
                            jQuery('#js-emessage-wrapper').show();
                            jQuery('#jscodeinputbox').slideUp('400' , 'swing' , function(){
                                jQuery('input#languagecode').val("");
                            });
                        }else{
                            jQuery('#js-emessage-wrapper').hide();
                            jQuery('#jscodeinputbox').html(data['path']+'/ '+data['input']);
                            jQuery('#jscodeinputbox').slideDown();
                        }
                    }
                });
            }
        });

        jQuery('#jsdownloadbutton').click(function(){
            jQuery('#js-emessage-wrapper_ok').hide();
            var lang_name = jQuery('#translations').val();
            var file_name = jQuery('#languagecode').val();
            if(lang_name != '' && file_name != ''){
                jsShowLoading();
                jQuery.post("index.php?option=com_jssupportticket&c=jssupportticket&task=getlanguagetranslation&<?php echo Factory::getSession()->getFormToken(); ?>=1",{ langname:lang_name , filename: file_name}, function (data) {
                    if (data) {
                        jsHideLoading();
                        data = JSON.parse(data);
                        if(data['error']){
                            jQuery('#js-emessage-wrapper div').html(data['error']);
                            jQuery('#js-emessage-wrapper').show();
                        }else{
                            jQuery('#js-emessage-wrapper').hide();
                            jQuery('#js-emessage-wrapper_ok div').html(data['data']);
                            jQuery('#js-emessage-wrapper_ok').slideDown();
                        }
                    }
                });
            }
        });
    });
    
    function jsShowLoading(){
        jQuery('div#black_wrapper_translation').show();
        jQuery('div#jstran_loading').show();
    }    

    function jsHideLoading(){
        jQuery('div#black_wrapper_translation').hide();
        jQuery('div#jstran_loading').hide();
    }
</script>
