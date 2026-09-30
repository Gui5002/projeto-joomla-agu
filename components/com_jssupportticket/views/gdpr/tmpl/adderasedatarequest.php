<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;

$conf   = Factory::getConfig();
$editor = Editor::getInstance($conf->get('editor'));

/*
HTMLHelper::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
HTMLHelper::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
*/
HTMLHelper::_('behavior.formvalidator');
$document = Factory::getDocument();
$document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
Text::script('Error file size too large');
Text::script('Error file extension mismatch');
?>
<div class="js-row js-null-margin jsst-wrapper-gdpr">
    <?php if($this->config['offline'] != '1'){ ?>
        <?php require_once JPATH_COMPONENT_SITE . '/views/header.php';
        $document = Factory::getDocument();
        $language = Factory::getLanguage();
        if($language->isRTL()){
        }?>

        <script language="javascript">
            function myValidate(f){
                if (document.formvalidator.isValid(f)){
                    f.check.value = '<?php if (JVERSION < 3) echo JUtility::getToken(); else echo Factory::getSession()->getFormToken(); ?>';//send token
                    return confirm("<?php echo Text::_('Are you sure to submit erase data request?', true); ?>");
                }else{
                    alert("<?php echo Text::_('Some values are not valid. Please review the form and try again.'); ?>");
                    return false;
                }
                jQuery('#submit_app_button').attr('disabled',true);
                f.submit();
                return true;
            }
        </script>
        <?php if($this->config['cur_location'] == 1){ ?>
            <div id="jsst-wrapper-top">
                <div id="jsst-wrapper-top-left">
                    <div id="jsst-breadcrunbs">
                        <ul>
                            <li>
                                <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" title="<?php echo htmlspecialchars(Text::_('Dashboard'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo Text::_('Dashboard'); ?>
                                </a>
                            </li>
                            <li>
                                <?php echo Text::_('Add').' '.Text::_('Erase Data Requests'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php
        if($this->user->getIsGuest()){
            messageslayout::getUserGuest($this->layoutname,$this->Itemid); //user guest
        }else{?>
            <div id="js-tk-formwrapper">
                <div class="js-ticket-top-search-wrp">
                    <div class="js-ticket-search-heading-main-wrp">
                        <div class="js-ticket-heading-left">
                            <?php echo Text::_("Export Your Data"); ?>
                        </div>
                        <div class="js-ticket-heading-right">
                            <a class="js-ticket-add-download-btn" href="index.php?option=com_jssupportticket&c=gdpr&task=exportusereraserequest&<?php echo Factory::getSession()->getFormToken(); ?>=1"><span class="js-ticket-add-img-wrp"></span><?php echo Text::_("Export"); ?></a>
                        </div>
                    </div>
                </div>
                <?php if(isset($this->erasedaatrequest) && is_numeric($this->erasedaatrequest->id)){ ?>
                    <div class="js-ticket-top-search-wrp second-style">
                        <div class="js-ticket-search-heading-main-wrp second-style">
                            <div class="js-ticket-heading-left">
                                <?php echo Text::_('You have submitted a request to remove your data.') ?>
                            </div>
                            <div class="js-ticket-heading-right">
                                <a class="js-ticket-add-download-btn" href="index.php?option=com_jssupportticket&c=gdpr&task=removeusereraserequest&id=<?php echo $this->erasedaatrequest->id; ?>&<?php echo Factory::getSession()->getFormToken(); ?>=1"><span class="js-ticket-add-img-wrp"></span><?php echo Text::_('To withdraw your data erasure request') ?></a>
                            </div>
                        </div>
                    </div>
                <?php }else{ ?>
                    <div class="js-ticket-top-search-wrp second-style">
                        <div class="js-ticket-search-heading-main-wrp second-style">
                            <div class="js-ticket-heading-left">
                                <?php echo Text::_("Request data removal from the system"); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <form action="index.php" method="post" name="adminForm" id="adminForm" class="jsticket_form jsst-gdpr-request-form" enctype="multipart/form-data">
                    <div class="jsst-gdpr-form-field">
                        <label class="jsst-gdpr-form-label" for="subject">
                            <span><?php echo Text::_("Subject"); ?></span>
                            <span class="jsst-required-mark" aria-hidden="true">*</span>
                        </label>
                        <div class="jsst-gdpr-form-control">
                            <input class="inputbox jsst-gdpr-subject-input required" type="text" name="subject" id="subject" size="40" maxlength="255" value="<?php echo isset($this->erasedaatrequest) ? $this->erasedaatrequest->subject : ''; ?>" required />
                        </div>
                    </div>
                    <div class="jsst-gdpr-form-field jsst-gdpr-editor-field">
                        <label class="jsst-gdpr-form-label" for="message">
                            <span><?php echo Text::_("Message"); ?></span>
                            <span class="jsst-required-mark" aria-hidden="true">*</span>
                        </label>
                        <div class="jsst-gdpr-form-control jsst-gdpr-editor-control">
                            <?php
                                echo $editor->display('message', isset($this->erasedaatrequest) ? $this->erasedaatrequest->message : '', '550', '300', '60', '20', array('class'=>'required')); ?>
                        </div>
                    </div>
                    <?php /*
                    <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null">
                        <div class="js-form-title"><label for="subject"><?php echo Text::_("Subject"); ?>&nbsp;<font color="red">*</font></label></div>
                        <div class="js-form-value"><input class="js-form-input-field required" type="text" name="subject" id="jsst-erasure-message" size="40" maxlength="255" value="<?php echo isset($this->erasedaatrequest) ? $this->erasedaatrequest->subject : ''; ?>" required/></div>
                    </div>
                    <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null">
                        <div class="js-form-title"><label for="message"><?php echo Text::_("Message"); ?>&nbsp;<font color="red">*</font></label></div>
                        <div class="js-form-value"><?php 
                        $conf   = Factory::getConfig();
                        $editor = Editor::getInstance($conf->get('editor'));
                        echo $editor->display('message', isset($this->erasedaatrequest) ? $this->erasedaatrequest->message : '', '550', '300', '60', '20', array('class'=>'required')); ?></div>
                    </div> */?>
                    <input type="hidden" name="view" value="gdpr" />
                    <input type="hidden" name="c" value="gdpr" />
                    <input type="hidden" name="layout" value="adderasedatarequest" />
                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    <input type="hidden" name="task" value="saveusereraserequest" />
                    <input type="hidden" name="check" value="" />
                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                    <input type="hidden" name="id" value="<?php if (isset($this->erasedaatrequest)) echo $this->erasedaatrequest->id; ?>" />
                    <?php echo HTMLHelper::_('form.token'); ?>

                    <div class="js-form-submit-btn-wrp">
                        <input class="js-save-button" type="submit" onclick="return myValidate(document.adminForm);"  name="submit_app" id="submit_app_button" value="<?php echo Text::_('Save'); ?>" />
                        <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" class="js-ticket-cancel-button"><?php echo Text::_('Cancel'); ?></a>
                    </div>
                </form>
            </div>
        <?php
        } ?>

    <?php }else{
        messagesLayout::getSystemOffline($this->config['title'],$this->config['offline_text']);
    } ?>
</div>
