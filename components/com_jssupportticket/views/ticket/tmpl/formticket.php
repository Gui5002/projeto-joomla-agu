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
$dash = '-';
$dateformat = $this->config['date_format'];
$firstdash = getJSTicketPHPFunctionsClass()->jsticket_strpos($dateformat, $dash, 0);
$firstvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, 0, $firstdash);
$firstdash = $firstdash + 1;
$seconddash = getJSTicketPHPFunctionsClass()->jsticket_strpos($dateformat, $dash, $firstdash);
$secondvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, $firstdash, $seconddash - $firstdash);
$seconddash = $seconddash + 1;
$thirdvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, $seconddash, getJSTicketPHPFunctionsClass()->jsticket_strlen($dateformat) - $seconddash);
$js_dateformat = '%' . $firstvalue . $dash . '%' . $secondvalue . $dash . '%' . $thirdvalue;

$per_ticket = true;
if($this->user->getIsGuest()){
    $per_ticket = false;
}
?>
<?php // Free edition has no staff module, so this form is always the user variant. ?>
<div class="js-row js-null-margin jsst-submit-ticket-page jsst-submit-ticket-user-page">
    <?php
if($this->config['offline'] != '1'){       
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = Factory::getDocument();
    $language = Factory::getLanguage();
    if($language->isRTL()){
    }?>
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
                            <?php echo Text::_('Submit Ticket'); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php
    if($per_ticket){
        ?>
            <?php
            HTMLHelper::_('behavior.formvalidator');
            /*
            HTMLHelper::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
            HTMLHelper::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
            HTMLHelper::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
            */
            $document = Factory::getDocument();
            $document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
            Text::script('JS_ERROR_FILE_SIZE_TO_LARGE');
            Text::script('JS_ERROR_FILE_EXT_MISMATCH');
            ?>
            <div id="userpopupblack" style="display:none;"></div>
                
        <?php if($this->form_is_disabled == 0){ ?>
            <?php if(!empty($this->config['new_ticket_message'])){ ?>
                <div class="js-col-xs-12 js-col-md-12 js-ticket-form-instruction-message">
                    <?php echo str_replace('The maximum remaining ticket(s) in a day is 100 .', 'You can submit up to 100 tickets per day.', $this->config['new_ticket_message']); ?>
                </div>
            <?php } ?>
        <?php } ?>
        <div id="js-tk-formwrapper" class="jsst-submit-ticket-form-shell js-ticket-add-form-wrapper">
            <?php if(isset($this->fieldsordering) && is_array($this->fieldsordering) && getJSTicketPHPFunctionsClass()->jsticket_count($this->fieldsordering) > 0){ ?>
                <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm" >
                    <?php 
                    $fieldcounter = 0;
                    $i = 0;
                    $j = 0;
                    foreach($this->fieldsordering AS $field) {
                        switch($field->field){
                            case 'email':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    $readonly = '';
                                    if(isset($field->readonly) && $field->readonly == 1){
                                        $readonly = 'readonly';
                                    }
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="email"><?php echo Text::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field required validate-email" <?php echo $readonly;?> type="text" name="email" id="email" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" size="40" maxlength="255" value="<?php if(isset($this->data['email'])) echo $this->data['email']; elseif (isset($this->editticket->email)) echo $this->editticket->email;elseif (isset($this->email)) echo $this->email; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'fullname':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="name"><?php echo Text::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field required" type="text" name="name" id="name"size="40" maxlength="255" value="<?php if(isset($this->data['name'])) echo $this->data['name']; elseif (isset($this->editticket->ticketname)) echo $this->editticket->ticketname; elseif (isset($this->name)) echo $this->name; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'phone':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="phone"><?php echo Text::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field <?php if($field->required == 1) echo ' required'; ?>" type="text" name="phone" id="phone" size="40" maxlength="255" value="<?php if(isset($this->data['phone'])) echo $this->data['phone']; else echo isset($this->editticket->phone) ? $this->editticket->phone : ''; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'phoneext':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="phoneext"><?php echo Text::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field <?php if($field->required == 1) echo ' required'; ?>" type="text" name="phoneext" id="phoneext" size="5" maxlength="255" value="<?php if(isset($this->data['phoneext'])) echo $this->data['phoneext']; else echo isset($this->editticket->phoneext) ? $this->editticket->phoneext : ''; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'department':
                                if ($field->published == 1){
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="departmentid"><?php echo Text::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                        <div class="js-form-value"><?php echo $this->lists['departments']; ?></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'helptopic':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="helptopicid"><?php echo Text::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                        <div class="js-form-value" id="helptopic"><?php echo $this->lists['helptopic']; ?></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'priority':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="priorityid"><?php echo Text::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                        <div class="js-form-value"><?php echo $this->lists['priorities']; ?></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'subject':
                                if ($field->published == 1) {
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                    <div class="js-col-md-12 js-col-xs-12  js-margin-bottom js-padding-null">
                                        <div class="js-form-title"><label for="subject"><?php echo Text::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                        <div class="js-form-value"><input class="js-form-input-field required" type="text" name="subject" id="subject" size="40" maxlength="255" value="<?php if(isset($this->data['subject'])) echo $this->subject; elseif (isset($this->editticket->subject)) echo $this->editticket->subject; ?>" /></div>
                                    </div>
                                    <?php
                                }
                                break;
                            case 'issuesummary':
                                if ($field->published == 1) {
                                    if($fieldcounter != 0){
                                        echo '</div>';
                                    }
                                     echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                    }
                                    $fieldcounter++;
                                    ?>
                                <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null">
                                    <div class="js-form-title"><label for="issuesummary"><?php echo Text::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                    <div class="js-form-value">
                                        <?php
                                            if(isset($this->editticket)) $message = $this->editticket->message; else $message = '';
                                            $conf   = Factory::getConfig();
                                            $editor = Editor::getInstance($conf->get('editor'));
                                            echo $editor->display('message', $message, '550', '300', '60', '20', false);
                                        ?></div>
                                </div>
                                <?php
                                break;
                            case 'attachments':
                                $flag = true;
                                if($flag){
                                    if ($field->published == 1) {
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                         echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                            
                                    }
                                        $fieldcounter++;
                                        ?>
                                        <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null js-attachment-wrp">
                                            <div class="js-form-title"><?php echo Text::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></div>
                                            <?php
                                            if(isset($this->attachments) && is_array($this->attachments) && getJSTicketPHPFunctionsClass()->jsticket_count($this->attachments) > 0){
                                                $attachmentreq = '';
                                            }else{
                                                $attachmentreq = $field->required == 1 ? 'required' : '';
                                            }
                                            ?>
                                            <div class="js-form-value js-attachment-files-wrp">
                                                <div id="js-attachment-files" class="js-attachment-files" data-jsst-max-attachments="<?php echo (int) $this->config['noofattachment']; ?>">
                                                    <span class="js-attachment-file-box">
                                                        <input type="file" class="js-form-input-field-attachment <?php echo $attachmentreq; ?>" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlength='30'/>
                                                        <span class='js-attachment-remove'></span>
                                                    </span>
                                                </div>
                                                <div id="js-attachment-option">
                                                    <?php echo Text::_('Maximum file size') . ': ' . $this->config['filesize']; ?> KB<br><?php echo Text::_('Allowed file types') . ': ' . $this->config['fileextension']; ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                }
                                break;
                            case 'status':
                                break;
                            default:
                                $params = NULL;
                                $id = NULL;
                                $isadmin = false;
                                if(isset($this->editticket)){
                                    $id = $this->editticket->id; 
                                    $params = $this->editticket->params; 
                                }else{
    								if(isset($this->custom_params))
    									$params = $this->custom_params;
    								else
    									$params = '';
                                }
                                switch ($field->size) {
                                    case '100':
                                        
                                            if($fieldcounter != 0){
                                                echo '</div>';
                                            }
                                            echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                       
                                        $fieldcounter++;
                                        echo getCustomFieldClass()->formCustomFields($field , $id , $params , $isadmin);
                                    break;
                                
                                    case '50':
                                        if($fieldcounter % 2 == 0){
                                            if($fieldcounter != 0){
                                                echo '</div>';
                                            }
                                            echo '<div class="js-col-md-12 jsst-form-row js-padding-null">';
                                        }
                                        $fieldcounter++;
                                        echo getCustomFieldClass()->formCustomFields($field , $id , $params , $isadmin );
                                    break;
                                }
                            break;
                        }
                    }

                    if($fieldcounter != 0){
                        echo '</div>';
                    }                


                    ?>

                    <div class="js-form-submit-btn-wrp">
                        <input type="submit" class="js-save-button" name="submit_app" id="submit_app_button" onclick="return validate_form(document.adminForm)" value="<?php echo Text::_('Submit Ticket'); ?>" />
                        <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" class="js-ticket-cancel-button"><?php echo Text::_('Cancel'); ?></a>
                        <?php
                            $link = "index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=" . $this->Itemid;

                        ?>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php if (isset($this->editticket)) echo $this->editticket->id; ?>" />
                    <input type="hidden" name="isoverdue" id="isoverdue" value="<?php if (isset($this->editticket)) echo $this->editticket->isoverdue; ?>" />
                    <input type="hidden" name="ticketid" id="ticketid" value="<?php if (isset($this->editticket)) echo $this->editticket->ticketid; ?>" />
                    <input type="hidden" name="uid" id="uid" value="<?php if (isset($this->data['uid'])) echo (int) $this->data['uid']; elseif (isset($this->editticket)) echo (int) $this->editticket->uid; elseif (isset($this->uid)) echo (int) $this->uid; ?>" />
                    <input type="hidden" name="c" id="c" value="ticket" />
                    <input type="hidden" name="task" id="task" value="saveticket" />
                    <input type="hidden" name="view" id="view" value="ticket" />
                    <input type="hidden" name="layout" id="layout" value="formticket" />
                    <input type="hidden" name="check" id="check" value="" />
                    <input type="hidden" name="option" id="option" value="<?php echo $this->option; ?>" />
                    <input type="hidden" name="created" id="created" value="<?php if (isset($this->editticket)) echo $this->editticket->created; else echo $curdate = date('Y-m-d H:i:s'); ?>"/>
                    <input type="hidden" name="Itemid" id="Itemid" value="<?php echo $this->Itemid; ?>" />
                    <input type="hidden" name="update" id="update" value="<?php if (isset($this->editticket)) echo $update = date('Y-m-d H:i:s'); ?>"/>
                    <?php echo HTMLHelper::_('form.token'); ?>
                </form>
            <?php }else{
                messageslayout::getPermissionNotAllow();
            } ?>
        </div>
        <?php
    }else{
        if($this->user->getIsGuest()){ // user is guest
            messageslayout::getUserGuest('formticket',$this->Itemid);
        }else{
            messageslayout::getPermissionNotAllow(); //permission not granted
        }
    }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>
<script type="text/javascript">
        function validate_form(f) {
            if (document.formvalidator.isValid(f)) {
                if(isTinyMCE()){
                    var issuesummary = tinyMCE.get('message').getContent();
                }else{
                    var issuesummary = jQuery('textarea#message').val();
                }
                if (typeof issuesummary !== 'undefined' && issuesummary !== null) {
                    if (issuesummary == '') {
                        alert("<?php echo Text::_('Some values are not valid. Please review the form and try again.'); ?>");
                        return false;
                    }
                }
                f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo Factory::getSession()->getFormToken(); ?>';//send token
            } else {
                alert("<?php echo Text::_('Some values are not valid. Please review the form and try again.'); ?>");
                return false;
            }
            return true;
        }
        function jsstSubmitTicketMaxAttachments(zone) {
            var max = parseInt(zone.attr('data-jsst-max-attachments'), 10);
            if (!max || max < 1) {
                max = <?php echo (int) $this->config['noofattachment']; ?>;
            }
            return max;
        }

        function jsstSubmitTicketAttachmentTemplate() {
            return "<span class='js-attachment-file-box'><input name='filename[]' class='js-form-input-field-attachment' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlength='30' /><span class='jsst-attachment-file-name'><?php echo Text::_('No file selected'); ?></span><span class='js-attachment-remove'></span></span>";
        }

        function jsstSubmitTicketSelectedAttachmentCount(zone) {
            var count = 0;
            zone.find('input[name="filename[]"]').each(function () {
                if (this.files && this.files.length) {
                    count++;
                }
            });
            return count;
        }

        function jsstEnsureSubmitTicketAttachmentLabel(input) {
            var row = jQuery(input).closest('.js-attachment-file-box');
            if (!row.children('.jsst-attachment-file-name').length) {
                jQuery(input).after('<span class="jsst-attachment-file-name"><?php echo Text::_('No file selected'); ?></span>');
            }
        }

        function jsstUpdateSubmitTicketAttachmentRow(input) {
            var row = jQuery(input).closest('.js-attachment-file-box');
            jsstEnsureSubmitTicketAttachmentLabel(input);
            var label = row.children('.jsst-attachment-file-name').first();
            if (input.files && input.files.length) {
                label.text(input.files[0].name);
                row.addClass('is-file-attached');
            } else {
                label.text("<?php echo Text::_('No file selected'); ?>");
                row.removeClass('is-file-attached');
            }
        }

        function jsstUpdateSubmitTicketAttachmentState(zone) {
            var max = jsstSubmitTicketMaxAttachments(zone);
            var selected = jsstSubmitTicketSelectedAttachmentCount(zone);
            var wrapper = zone.closest('.js-attachment-files-wrp');
            var option = wrapper.find('#js-attachment-option').first();
            zone.find('input[name="filename[]"]').each(function () {
                jsstUpdateSubmitTicketAttachmentRow(this);
            });
            if (option.length && !option.children('.jsst-attachment-counter').length) {
                option.prepend('<span class="jsst-attachment-counter"></span>');
            }
            option.children('.jsst-attachment-counter').text(selected + ' / ' + max + ' <?php echo Text::_('attachments selected'); ?>');
            wrapper.find('#js-attachment-add').toggle(selected < max);
        }

        function jsstGetSubmitTicketAttachmentInput(zone) {
            var max = jsstSubmitTicketMaxAttachments(zone);
            if (jsstSubmitTicketSelectedAttachmentCount(zone) >= max) {
                alert("<?php echo Text::_('File upload limit exceeded.'); ?>");
                return null;
            }
            var emptyInput = zone.find('input[name="filename[]"]').filter(function () {
                return !this.files || this.files.length === 0;
            }).first();
            if (!emptyInput.length) {
                zone.append(jsstSubmitTicketAttachmentTemplate());
                emptyInput = zone.find('input[name="filename[]"]').last();
                jsstEnsureSubmitTicketAttachmentLabel(emptyInput.get(0));
            }
            return emptyInput;
        }

        jQuery(document).on('click', '#js-attachment-add', function (event) {
            event.preventDefault();
            var zone = jQuery(this).closest('.js-attachment-files-wrp').find('.js-attachment-files').first();
            if (!zone.length) {
                return;
            }
            var input = jsstGetSubmitTicketAttachmentInput(zone);
            if (input) {
                input.trigger('click');
            }
        });

        jQuery(document).delegate('.js-attachment-remove', 'click', function (e) {
            e.preventDefault();
            var row = jQuery(this).closest('.js-attachment-file-box');
            var zone = row.closest('.js-attachment-files');
            if (zone.find('input[name="filename[]"]').length > 1) {
                row.remove();
            } else {
                var input = row.find('input[type="file"]').get(0);
                if (input) {
                    input.value = '';
                }
                row.removeClass('is-file-attached');
                row.children('.jsst-attachment-file-name').text("<?php echo Text::_('No file selected'); ?>");
            }
            jsstUpdateSubmitTicketAttachmentState(zone);
        });

        function jsstInitSubmitTicketDropzones() {
            var zones = jQuery('.jsst-submit-ticket-page .js-attachment-files');
            if (!zones.length) {
                return;
            }
            zones.each(function () {
                var zone = jQuery(this);
                jsstPrepareSubmitTicketDropzone(zone);
                jsstUpdateSubmitTicketAttachmentState(zone);
                if (zone.data('jsstDragDropReady')) {
                    return;
                }
                zone.data('jsstDragDropReady', true);
                zone.addClass('jsst-dragdrop-enabled');

                zone.on('change', 'input[type="file"]', function () {
                    jsstUpdateSubmitTicketAttachmentRow(this);
                    jsstUpdateSubmitTicketAttachmentState(zone);
                });

                zone.on('click', '.jsst-dropzone-message', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    var input = jsstGetSubmitTicketAttachmentInput(zone);
                    if (input) {
                        input.trigger('click');
                    }
                });

                zone.on('dragenter dragover', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    zone.addClass('is-dragover');
                    jsstSetSubmitTicketDropzoneText(zone, "<?php echo Text::_('Release files to attach'); ?>");
                });
                zone.on('dragleave dragend', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    zone.removeClass('is-dragover');
                    jsstSetSubmitTicketDropzoneText(zone, "<?php echo Text::_('Drag and drop files here'); ?>");
                });
                zone.on('drop', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    zone.removeClass('is-dragover');
                    jsstSetSubmitTicketDropzoneText(zone, "<?php echo Text::_('Drag and drop files here'); ?>");
                    var originalEvent = event.originalEvent || event;
                    var files = originalEvent.dataTransfer && originalEvent.dataTransfer.files ? originalEvent.dataTransfer.files : null;
                    if (!files || !files.length) {
                        return;
                    }
                    jsstAttachSubmitTicketDroppedFiles(zone, files);
                });
            });
        }

        function jsstPrepareSubmitTicketDropzone(zone) {
            if (!zone.children('.jsst-dropzone-message').length) {
                zone.prepend('<button type="button" class="jsst-dropzone-message"><span class="jsst-dropzone-icon">&#8682;</span><span class="jsst-dropzone-copy"><strong><?php echo Text::_('Drag and drop files here'); ?></strong><small><?php echo Text::_('or click to choose files'); ?></small></span></button>');
            }
        }

        function jsstSetSubmitTicketDropzoneText(zone, text) {
            var label = zone.children('.jsst-dropzone-message').find('strong').first();
            if (label.length) {
                label.text(text);
            }
        }

        function jsstSetSubmitTicketDroppedFile(input, file) {
            if (typeof DataTransfer === 'undefined') {
                alert("<?php echo Text::_('Your browser cannot attach dropped files. Click the upload area to choose files.'); ?>");
                return false;
            }
            var transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            jQuery(input).trigger('change');
            jsstUpdateSubmitTicketAttachmentRow(input);
            return true;
        }

        function jsstAttachSubmitTicketDroppedFiles(zone, files) {
            var max = jsstSubmitTicketMaxAttachments(zone);
            for (var i = 0; i < files.length; i++) {
                if (jsstSubmitTicketSelectedAttachmentCount(zone) >= max) {
                    alert("<?php echo Text::_('File upload limit exceeded.'); ?>");
                    break;
                }
                var emptyInput = jsstGetSubmitTicketAttachmentInput(zone);
                if (!emptyInput || !jsstSetSubmitTicketDroppedFile(emptyInput.get(0), files[i])) {
                    break;
                }
            }
            jsstUpdateSubmitTicketAttachmentState(zone);
        }

        jQuery(function () {
            jsstInitSubmitTicketDropzones();
        });

        function gethelptopicandpremade(help,pre,depid) {
            var link = 'index.php?option=com_jssupportticket&c=ticket&task=listhelptopicandpremade&<?php echo Factory::getSession()->getFormToken(); ?>=1';
            jQuery.post(link, {val: depid}, function (data) {
                if (data) {
                    helptopics = JSON.parse(data);
                    jQuery('div#'+help).html(helptopics.helptopic);
                    jQuery('div#'+pre).html(helptopics.premade);
                }
            });
        }



    function isTinyMCE(){
        is_tinyMCE_active = false;
        if (typeof(tinyMCE) != "undefined") {
            //if(tinyMCE.editors.length > 0){
                is_tinyMCE_active = true;
            //}
        }
        return is_tinyMCE_active;
    }

        function getpremade(src, id, append) {
            var link = 'index.php?option=com_jssupportticket&c=ticket&task=getpremadeforinternalnote&<?php echo Factory::getSession()->getFormToken(); ?>=1';
            jQuery.post(link, {val: id}, function (data) {
                if (data) {
                    if (append == true) {
                        if(isTinyMCE()){
                            var content = tinyMCE.get('message').getContent();
                        }else{
                            var content = jQuery('textarea#message').val();
                        }
                        content = content + data;
                        if(isTinyMCE()){
                            tinyMCE.get('message').execCommand('mceSetContent', false, content);
                        }else{
                            jQuery('textarea#message').val(content);
                        }

                    } else {
                        if(isTinyMCE()){
                            tinyMCE.get('message').execCommand('mceSetContent', false, data);
                        }else{
                            jQuery('textarea#message').val(content);
                        }
                    }
                }
            });
        }
        var jsstUserPopupScrollTop = 0;

        function jsstLockUserPopupScroll() {
            jsstUserPopupScrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
            jQuery('html, body').addClass('jsst-userpopup-open');
            jQuery('body').css({
                'top': '-' + jsstUserPopupScrollTop + 'px',
                'position': 'fixed',
                'width': '100%',
                'left': '0',
                'right': '0'
            });
        }

        function jsstUnlockUserPopupScroll() {
            jQuery('html, body').removeClass('jsst-userpopup-open');
            jQuery('body').css({
                'top': '',
                'position': '',
                'width': '',
                'left': '',
                'right': ''
            });
            if (jsstUserPopupScrollTop) {
                window.scrollTo(0, jsstUserPopupScrollTop);
            }
        }

        function jsstOpenUserPopup() {
            jsstLockUserPopupScroll();
            jQuery('div#userpopupblack').show();
            jQuery('div#userpopup').stop(true, true).slideDown('slow');
        }

        function jsstCloseUserPopup() {
            jQuery('div#userpopup').stop(true, true).slideUp('slow', function () {
                jQuery('div#userpopupblack').hide();
                jsstUnlockUserPopupScroll();
            });
        }

        function jsstPolishUserPopupRecords() {
            var popup = jQuery("div#userpopup");
            var columnWidths = ['80px', '260px', '240px', '180px'];
            popup.find("table").each(function () {
                var table = jQuery(this);
                table.addClass("jsst-user-popup-table jsst-user-popup-grid-table");
                table.css({
                    'width': '100%',
                    'min-width': '860px',
                    'border-collapse': 'collapse',
                    'table-layout': 'fixed'
                });
                table.find('thead, tbody').css({'display':'block','width':'100%'});
                if (!table.children("colgroup.jsst-user-popup-colgroup").length) {
                    table.prepend('<colgroup class="jsst-user-popup-colgroup"><col><col><col><col></colgroup>');
                }
                table.children("colgroup.jsst-user-popup-colgroup").children("col").each(function(index){
                    jQuery(this).css('width', columnWidths[index] || '180px');
                });
            });
            popup.find("table tr").each(function () {
                var row = jQuery(this);
                var cells = row.children("th,td");
                if (cells.length < 4) {
                    return;
                }
                row.addClass('jsst-user-popup-grid-row');
                row.css({
                    'display': 'grid',
                    'grid-template-columns': columnWidths.join(' '),
                    'column-gap': '44px',
                    'align-items': 'center',
                    'width': '100%',
                    'box-sizing': 'border-box'
                });
                cells.each(function (index) {
                    var cell = jQuery(this);
                    var width = columnWidths[index] || '180px';
                    cell.css({
                        'display': 'block',
                        'width': width,
                        'max-width': width,
                        'min-width': '0',
                        'box-sizing': 'border-box',
                        'overflow': 'hidden',
                        'vertical-align': 'middle',
                        'padding-left': index === 0 ? '0' : '0',
                        'padding-right': '0'
                    });
                    if (!cell.children(".jsst-popup-cell-inner").length) {
                        cell.wrapInner('<div class="jsst-popup-cell-inner"></div>');
                    }
                    var inner = cell.children(".jsst-popup-cell-inner");
                    inner.css({
                        'display': 'block',
                        'width': '100%',
                        'max-width': '100%',
                        'min-width': '0',
                        'overflow': 'hidden',
                        'text-overflow': 'ellipsis',
                        'white-space': index === 1 ? 'normal' : 'nowrap',
                        'word-break': index === 1 ? 'break-word' : 'normal',
                        'overflow-wrap': index === 1 ? 'anywhere' : 'normal',
                        'box-sizing': 'border-box'
                    });
                    var text = jQuery.trim(inner.text());
                    if (text) {
                        cell.attr('title', text);
                    }
                    if (index === 2) {
                        cell.addClass('jsst-popup-email-cell');
                        var emailText = jQuery.trim(inner.text());
                        var fullEmailText = emailText;
                        var possibleEmail = emailText.match(/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i);
                        if (possibleEmail && possibleEmail[0]) {
                            emailText = possibleEmail[0];
                        }
                        if (emailText.length > 22) {
                            emailText = emailText.substring(0, 19) + '…';
                        }
                        inner.text(emailText);
                        if (fullEmailText) {
                            cell.attr('title', fullEmailText);
                        }
                    }
                    if (index === 3) {
                        cell.addClass('jsst-popup-name-cell');
                        var nameText = jQuery.trim(inner.text());
                        if (!nameText) {
                            var link = cells.eq(1).find('a.js-userpopup-link').first();
                            if (link.length && link.attr('data-name')) {
                                nameText = link.attr('data-name');
                                inner.text(nameText);
                            }
                        }
                        if (nameText.length > 18) {
                            inner.text(nameText.substring(0, 16) + '…');
                        }
                    }
                });
            });
        }
        function jsstCleanUserPopupValue(cell, index) {
            var cleanCell = cell.clone();
            cleanCell.find('script,style').remove();
            var link = cell.find('a.js-userpopup-link').first();
            var text = '';
            if (index === 1 && link.length) {
                text = jQuery.trim(link.text());
            } else {
                text = jQuery.trim(cleanCell.text());
            }
            text = text.replace(/\s+/g, ' ');
            var labels = ['User ID', 'USER ID', 'Username', 'USERNAME', 'Email Address', 'EMAIL ADDRESS', 'Email', 'EMAIL', 'Name', 'NAME'];
            for (var i = 0; i < labels.length; i++) {
                var label = labels[i].replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                text = text.replace(new RegExp('^' + label + '\\s*:?\\s*', 'i'), '');
            }
            if (index === 2) {
                var possibleEmail = text.match(/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i);
                if (possibleEmail && possibleEmail[0]) {
                    text = possibleEmail[0];
                }
            }
            if (!text && index === 0) {
                var dataLink = cell.closest('.js-ticket-data-row,tr').find('a.js-userpopup-link').first();
                if (dataLink.length && dataLink.attr('data-id')) {
                    text = dataLink.attr('data-id');
                }
            }
            return text || '—';
        }

        function jsstBuildMobileUserPopupCards() {
            if (!window.matchMedia || !window.matchMedia('(max-width: 640px)').matches) {
                return;
            }
            var records = jQuery('div#userpopup div#records');
            if (!records.length || records.find('.jsst-user-popup-card-list').length) {
                return;
            }

            var rows = records.find('table tr').filter(function () {
                return jQuery(this).children('td').length >= 4;
            });
            if (!rows.length) {
                rows = records.find('.js-ticket-table-body > .js-ticket-data-row, .js-ticket-data-row').filter(function () {
                    return jQuery(this).children('.js-ticket-table-body-col,td').length >= 4;
                });
            }
            if (!rows.length) {
                return;
            }

            var pagination = records.find('.jsst_userpages').first().detach();
            var labels = ['User ID', 'Username', 'Email Address', 'Full Name'];
            var list = jQuery('<div class="jsst-user-popup-card-list"></div>');

            rows.each(function () {
                var row = jQuery(this);
                var cells = row.children('td');
                if (!cells.length) {
                    cells = row.children('.js-ticket-table-body-col');
                }
                if (cells.length < 4) {
                    return;
                }

                var card = jQuery('<div class="jsst-user-popup-card"></div>');
                cells.slice(0, 4).each(function (index) {
                    var cell = jQuery(this);
                    var item = jQuery('<div class="jsst-user-popup-card-row jsst-user-popup-card-row-' + index + '"></div>');
                    var label = jQuery('<span class="jsst-user-popup-card-label"></span>').text(labels[index]);
                    var value = jQuery('<span class="jsst-user-popup-card-value"></span>');

                    if (index === 1 && cell.find('a.js-userpopup-link').length) {
                        var userLink = cell.find('a.js-userpopup-link').first().clone(false, false);
                        userLink.text(jsstCleanUserPopupValue(cell, index));
                        value.append(userLink);
                    } else {
                        value.text(jsstCleanUserPopupValue(cell, index));
                    }

                    item.append(label).append(value);
                    card.append(item);
                });
                list.append(card);
            });

            records.empty().append(list);
            if (pagination.length) {
                records.append(pagination);
            }
        }

        function setUserLink() {
            jsstPolishUserPopupRecords();
            jsstBuildMobileUserPopupCards();
            jQuery("a.js-userpopup-link").each(function () {
                var anchor = jQuery(this);
                jQuery(anchor).click(function (e) {
                    var id = jQuery(this).attr('data-id');
                    var name = jQuery.trim(jQuery(this).text());
                    var email = jQuery(this).attr('data-email');
                    var displayname = jQuery(this).attr('data-name');
                    jQuery("input#username-text").val(name);
                    if(jQuery('input#name').val() == ''){
                        jQuery('input#name').val(displayname);
                    }
                    if(jQuery('input#email').val() == ''){
                        jQuery('input#email').val(email);
                    }
                    jQuery("input#uid").val(id);
                    jsstCloseUserPopup();
                    getUserRemainMaxtickets(id);
                });
            });
        }
        function updateuserlist(pagenum){
            jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1", {userlimit:pagenum}, function (data) {
                if(data){
                    jQuery("div#records").html("");
                    jQuery("div#records").html(data);
                    setUserLink();
                }
            });
        }
        jQuery(document).ready(function () {
            jQuery("a.jsst-open-user-popup").click(function (e) {
                e.preventDefault();
                jsstOpenUserPopup();
                jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1",{},function(data){
                  if(data){
                    jQuery('div#records').html("");
                    jQuery('div#records').html(data);
                    setUserLink();
                  }
                });
            });
            jQuery("form#userpopupsearch").submit(function (e) {
                e.preventDefault();
                var name = jQuery("input#jsst-popup-name").val();
                var username = jQuery("input#jsst-popup-username").val();
                var emailaddress = jQuery("input#jsst-popup-email").val();
                jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1",{name: name, emailaddress: emailaddress,username:username}, function (data) {
                    if (data) {
                        jQuery("div#records").html(data);
                        setUserLink();
                    }
                });//jquery closed
            });
            jQuery("span.close, div#userpopupblack, .popup-header-close-img").click(function (e) {
                e.preventDefault();
                jsstCloseUserPopup();
            });
            <?php if($this->form_is_disabled == 0){ ?>
                getUserRemainMaxtickets();
            <?php } ?>
        });
        function getDataForDepandantField(parentf, childf, type) {
            if (type == 1) {
                var val = jQuery("select#" + parentf).val();
            } else if (type == 2) {
                var val = jQuery("input[name=" + parentf + "]:checked").val();
            }
            jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=datafordepandantfield&<?php echo Factory::getSession()->getFormToken(); ?>=1', {fvalue: val, child: childf}, function (data) {
                if (data) {
                    console.log(data);
                    var d = jQuery.parseJSON(data);
                    jQuery("select#" + childf).replaceWith(d);
                }
            });
        }

        function deleteCutomUploadedFile (field1) {
            jQuery("input#"+field1).val(1);
            jQuery("span."+field1).hide();
            
        }     

        jQuery('#adminForm').submit(function() {
            jQuery('#submit_app_button').attr('disabled',true);
        });

        function getUserRemainMaxtickets(uid = 0){
            jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=getuserremainmaxticket&<?php echo Factory::getSession()->getFormToken(); ?>=1', {uid:uid}, function (data) {
                if (data) {
                    // message kept adding into the page. (handling multiple messages)
                    jQuery(".jsticket-remaining-ticket-wrap").slideUp();
                    jQuery("#js-tk-formwrapper").before(data);
                }
            });
        }

    </script>
</div>
