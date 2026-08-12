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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
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

?>
<div class="js-row js-null-margin">
    <?php if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = Factory::getDocument();
    $document->addStyleSheet(Uri::root().'components/com_jssupportticket/include/css/inc.css/ticket-formticket.css', 'text/css');
    $language = Factory::getLanguage();
    $document->addStyleSheet(Uri::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(Uri::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    $per_ticket = true;
    $isstaffdisable = true;
    if($per_ticket){
        if($isstaffdisable){ ?>
            <?php
            HTMLHelper::_('behavior.formvalidator');
            //HTMLHelper::_('behavior.calendar');
            $document = Factory::getDocument();
            $document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
            Text::script('JS_ERROR_FILE_SIZE_TO_LARGE');
            Text::script('JS_ERROR_FILE_EXT_MISMATCH');
            ?>

        <div id="jsst-wrapper-top">
            <?php if($this->config['cur_location'] == 1){ ?>
                <div id="jsst-wrapper-top-left">
                    <div id="jsst-breadcrunbs">
                        <ul>
                            <li>
                                <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
                                    <?php echo Text::_('Dashboard'); ?>
                                </a>
                            </li>
                            <li>
                                <?php echo Text::_('Submit Ticket'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if($this->form_is_disabled == 0){ ?>
            <?php if(!empty($this->config['new_ticket_message'])){ ?>
                <div class="js-col-xs-12 js-col-md-12 js-ticket-form-instruction-message">
                    <?php echo $this->config['new_ticket_message']; ?>
                </div>
            <?php } ?>
        <?php } ?>
        <div id="js-tk-formwrapper">
            <?php if(isset($this->fieldsordering) && getJSTicketPHPFunctionsClass()->jsticket_count($this->fieldsordering) > 0){ ?>
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
                                    echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                }
                                $fieldcounter++;
                                $readonly = '';
                                if(isset($field->readonly) && $field->readonly == 1){
                                    $readonly = 'readonly';
                                }
                                ?>
                                <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                    <div class="js-form-title"><label for="email"><?php echo Text::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                    <div class="js-form-value"><input class="js-form-input-field required validate-email" type="text" name="email" id="email" size="40" maxlength="255" value="<?php if(isset($this->data['email'])) echo $this->data['email']; elseif (isset($this->email)) echo $this->email;  ?>" /></div>
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
                                    echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                }
                                $fieldcounter++;
                                ?>
                                <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null">
                                    <div class="js-form-title"><label for="name"><?php echo Text::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                    <div class="js-form-value"><input class="js-form-input-field required" type="text" name="name" id="name"size="40" maxlength="255" value="<?php if(isset($this->data['name'])) echo $this->data['name']; elseif (isset($this->name)) echo $this->name; ?>" /></div>
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
                                    echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
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
                                    echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
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
                                    echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
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
                        case 'priority':
                            if ($field->published == 1) {
                                if($fieldcounter % 2 == 0){
                                    if($fieldcounter != 0){
                                        echo '</div>';
                                    }
                                    echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
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
                                    echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                }
                                $fieldcounter++;
                                ?>
                                <div class="js-col-md-12 js-col-xs-12  js-margin-bottom js-padding-null">
                                    <div class="js-form-title"><label for="subject"><?php echo Text::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                    <div class="js-form-value"><input class="js-form-input-field required" type="text" name="subject" id="subject" size="40" maxlength="255" value="<?php if(isset($this->data['subject'])) echo $this->data['subject']; elseif (isset($this->editticket->subject)) echo $this->editticket->subject; ?>" /></div>
                                </div>
                                <?php
                            }
                            break;
                         case 'issuesummary':
                            if ($field->published == 1) {
                                if($fieldcounter != 0){
                                    echo '</div>';
                                }
                                 echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
                                }
                                $fieldcounter++;
                                ?>
                                <div class="js-col-md-12 js-col-xs-12 js-margin-bottom js-padding-null">
                                    <div class="js-form-title"><label for="issuesummary"><?php echo Text::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font></label></div>
                                    <div class="js-form-value">
                                    <?php
                                        if(isset($this->editticket)) $message = $this->editticket->message; else $message = '';
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
                                     echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';

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
                                            <div id="js-attachment-files" class="js-attachment-files">
                                                <span class="js-attachment-file-box">
                                                    <input type="file" class="js-form-input-field-attachment <?php echo $attachmentreq; ?>" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                                    <span class='js-attachment-remove'></span>
                                                </span>
                                            </div>
                                            <div id="js-attachment-option">
                                                <?php echo Text::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo Text::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></small>
                                            </div>
                                            <span id="js-attachment-add"><?php echo Text::_('Add more'); ?></span>
                                        </div>
                                    </div>
                                    <?php
                            }
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
                                            $fieldcounter = 0;
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';

                                    $fieldcounter++;
                                    echo getCustomFieldClass()->formCustomFields($field , $id , $params , $isadmin);
                                break;

                                case '50':
                                    if($fieldcounter % 2 == 0){
                                        if($fieldcounter != 0){
                                            echo '</div>';
                                        }
                                        echo '<div class="js-col-md-12 js-form-wrapper js-padding-null">';
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


                if($this->user->getIsGuest()){
                    if ($this->config['show_captcha_visitor_form_ticket'] == 1) { ?>
                        <div class="js-col-md-6 js-col-xs-12 js-margin-bottom js-padding-null js-captcha-wrp">
                            <div class="js-col-md-12 js-col-xs-12">
                                <div class="js-form-title js-captch-title">
                                    <label id="captchamsg" for="captcha"><?php echo Text::_('Captcha'); ?> <span style="color:red;">*</span></label>
                                </div>
                                <div class="js-form-value js-captcha-value">
                                    <?php
                                    if($this->config['captcha_selection'] == 1){
                                        $captcha = JCaptcha::getInstance('recaptcha', array('namespace' => 'dynamic_recaptcha_1' ));
                                       echo $captcha->display('recaptcha', 'recaptcha', 'required');
                                    }else{
                                        echo $this->captcha;
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }?>

                <div class="js-form-submit-btn-wrp">
                    <input type="submit" class="js-save-button" name="submit_app" id="submit_app_button" onclick="return validate_form(document.adminForm)" value="<?php echo Text::_('Submit Ticket'); ?>" />
                    <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" class="js-ticket-cancel-button"><?php echo Text::_('Cancel'); ?></a>
                    <?php
                        $link = "index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=" . $this->Itemid;

                    ?>
                </div>
                <input type="hidden" name="created" value="<?php echo $curdate; ?>" />
                <input type="hidden" name="view" value="ticket" />
                <input type="hidden" name="c" value="ticket" />
                <input type="hidden" name="layout" value="formticket" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="task" value="saveticket" />
                <input type="hidden" name="check" value="" />
                <input type="hidden" name="status" value="0" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="id" value="<?php if (isset($this->ticket)) echo $this->ticket->id; ?>" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
            <?php }else{
                messageslayout::getPermissionNotAllow();
            } ?>
            <div id="js-tk-copyright">
                <div class="js-tk-copyright-logo-wrapper">
                    <img src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a>
                </div>
                <div class="js-tk-copyright-desc-wrapper">
                    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="http://www.burujsolutions.com">Buruj Solutions</a>
                </div>
            </div>
        </div>
        <?php
        }else{
            messageslayout::getStaffDisable(); //staff disabled
        }
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
                        alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
                        return false;
                    }
                }
                f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo Factory::getSession()->getFormToken(); ?>';//send token
            } else {
                alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
                return false;
            }
            return true;
        }
        jQuery("#js-attachment-add").click(function () {
            var obj = this;
            var current_files = jQuery('input[name="filename[]"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            var append_text = "<span class='js-attachment-file-box'><input name='filename[]' class='js-form-input-field-attachment' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
            if (current_files < total_allow) {
                jQuery(".js-attachment-files").append(append_text);
            } else if ((current_files === total_allow) || (current_files > total_allow)) {
                alert("<?php echo Text::_('File upload limit exceed'); ?>");
                jQuery(obj).hide();
            }
        });

        jQuery(document).delegate(".js-attachment-remove", "click", function (e) {
            var current_files = jQuery('input[name="filename[]"]').length;
            if(current_files!=1)
                jQuery(this).parent().remove();
            var current_files = jQuery('input[name="filename[]"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            if (current_files < total_allow) {
                jQuery("#js-attachment-add").show();
            }
        });

        function isTinyMCE(){
            is_tinyMCE_active = false;
            if (typeof(tinyMCE) != "undefined") {
                //if(tinyMCE.editors.length > 0){
                    is_tinyMCE_active = true;
                //}
            }
            return is_tinyMCE_active;
        }
        function setUserLink() {
            jQuery("a.js-userpopup-link").each(function () {
                var anchor = jQuery(this);
                jQuery(anchor).click(function (e) {
                    var id = jQuery(this).attr('data-id');
                    var name = jQuery(this).html();
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
                    jQuery("div#userpopup").slideUp('slow', function () {
                        jQuery("div#userpopupblack").hide();
                    });
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
            jQuery("a#userpopup").click(function (e) {
                e.preventDefault();
                jQuery("div#userpopupblack").show();
                jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1",{},function(data){
                  if(data){
                    jQuery('div#records').html("");
                    jQuery('div#records').html(data);
                    setUserLink();
                  }
                });
                jQuery("div#userpopup").slideDown('slow');
            });
            jQuery("form#userpopupsearch").submit(function (e) {
                e.preventDefault();
                var name = jQuery("input#name").val();
                var username = jQuery("input#username").val();
                var emailaddress = jQuery("input#emailaddress").val();
                jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1",{name: name, emailaddress: emailaddress,username:username}, function (data) {
                    if (data) {
                        jQuery("div#records").html(data);
                        setUserLink();
                    }
                });//jquery closed
            });
            jQuery("span.close, div#userpopupblack").click(function (e) {
                jQuery("div#userpopup").slideUp('slow', function () {
                    jQuery("div#userpopupblack").hide();
                });
            });

            jQuery("div.popup-header-close-img").click(function (e) {
                jQuery("div#userpopup").slideUp('slow');
                setTimeout(function () {
                    jQuery("div#userpopupblack").hide();
                }, 700);
            });
            <?php if($this->form_is_disabled == 0){ ?>
                getUserRemainMaxtickets();
            <?php } ?>
        });

        function myValidate(f){
            if (document.formvalidator.isValid(f)){
                f.check.value = '<?php if (JVERSION < 3) echo JUtility::getToken(); else echo Factory::getSession()->getFormToken(); ?>';//send token
            }else{
                alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
                return false;
            }
            jQuery('#submit_app_button').attr('disabled',true);
            f.submit();
            return true;
        }

        function saveticket(formobj){
            var formObjdata = {};
            var inputs = jQuery('#adminForm').serializeArray();
            jQuery.each(inputs, function (i, input) {
                formObjdata[input.name] = input.value;
            });
            var xhr;
            try {
                xhr = new ActiveXObject('Msxml2.XMLHTTP');
            }
            catch (e) {
                try {
                    xhr = new ActiveXObject('Microsoft.XMLHTTP');
                }
                catch (e2) {
                    try {
                        xhr = new XMLHttpRequest();
                    }
                    catch (e3) {
                        xhr = false;
                    }
                }
            }
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    jQuery('#message_text').html(xhr.responseText);
                }
            }
            alert(xhr.readyState + " " + xhr.status);
            xhr.open("GET", "index.php?option=com_jssupportticket&c=ticket&task=saveticket&data=" + formobj, true);
            xhr.send(null);
        }
        function getDataForDepandantField(parentf, childf, type) {
            if (type == 1) {
                var val = jQuery("select#" + parentf).val();
            } else if (type == 2) {
                var val = jQuery("input[name=" + parentf + "]:checked").val();
            }
            console.log(val);
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
        }

    </script>
    

</div>
