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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;

jimport('joomla.html.pane');
HTMLHelper::_('behavior.formvalidator');
/*
HTMLHelper::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
HTMLHelper::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
*/
$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/jsst-admin-workspace-v2.css?v=61');
$document->addScript('components/com_jssupportticket/include/js/file/file_validate.js');
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

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'saveticket' || task == 'saveticketandnew' || task == 'saveticketsave') {
                returnvalue = validate_form(document.adminForm);
            } else
                returnvalue = true;
            if (returnvalue) {
                Joomla.submitform(task);
                return true;
            } else
                return false;
        }
    }

    function validate_form(f)
    {
        if (document.formvalidator.isValid(f)) {
            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo Factory::getSession()->getFormToken(); ?>';//send token
        } else {
            alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
        return true;
    }

        /*
    function validate_duedate(){
        var date_start_make = new Array();
        var split_start_value = new Array();
        var start_string = document.getElementById("ticket_duedate").value;
            var format_type = document.getElementById("js_dateformat").value;
            var current_date = document.getElementById("current_date").value;
            if (format_type == 'd-m-Y') {
                split_start_value = start_string.split('-');

                date_start_make['year'] = split_start_value[2];
                date_start_make['month'] = split_start_value[1];
                date_start_make['day'] = split_start_value[0];


            } else if (format_type == 'm-d-Y') {
                split_start_value = start_string.split('-');
                date_start_make['year'] = split_start_value[2];
                date_start_make['month'] = split_start_value[0];
                date_start_make['day'] = split_start_value[1];


            } else if (format_type == 'Y-m-d') {

                split_start_value = start_string.split('-');

                date_start_make['year'] = split_start_value[0];
                date_start_make['month'] = split_start_value[1];
                date_start_make['day'] = split_start_value[2];


            }

            var duedate = new Date(date_start_make['year'], date_start_make['month'] - 1, date_start_make['day']);
             console.log(duedate);
             console.log(current_date);


        return false;
    }
*/
</script>
<div id="userpopupblack" style="display:none;"></div>
<div id="userpopup" style="display:none;">
    <div class="">
        <form id="userpopupsearch">
            <div class="search-center">
                <div class="search-center-heading">
                    <span><?php echo Text::_('Select User'); ?></span>
                    <button type="button" class="close jsst-userpicker-close" aria-label="<?php echo htmlspecialchars(Text::_('Close'), ENT_QUOTES, 'UTF-8'); ?>"><span aria-hidden="true"></span></button>
                </div>
                <div class="js-col-md-12 jsst-userpicker-filter-grid">
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="username" id="userpopup-username" placeholder="<?php echo Text::_('Username'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="name" id="userpopup-name" placeholder="<?php echo Text::_('Name'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="emailaddress" id="userpopup-emailaddress" placeholder="<?php echo Text::_('Email address'); ?>"/>
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value-button">
                        <button class="js-button-search" type="submit"><?php echo Text::_('Search'); ?></button>
                        <button class="js-button-reset" type="button" data-jsst-userpicker-reset="1"><?php echo Text::_('Reset'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="records">
        <div id="records-inner">
            <div class="js-staff-searc-desc">
                <?php echo Text::_('Search for a user and select the account for this ticket.'); ?>
            </div>
        </div>
    </div>
</div>
<div id="js-tk-admin-wrapper" class="jsst-admin-form jsst-admin-form-ticket-v21">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'Create Ticket';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label' => 'Submit Ticket', 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
            <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <?php
            $count = getJSTicketPHPFunctionsClass()->jsticket_count($this->fieldsordering);
            $i = 0; // for userfield numbering
                if($count>0){
            foreach ($this->fieldsordering AS $field) { ?>
                <?php switch ($field->field) {
                        case 'users':
                            if ($field->published == 1) {  ?>
                                <div class="js-form-wrapper">
                                    <div class="js-title"><label for="email"><?php echo Text::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                    <div class="js-value jsst-user-select-control">
                                        <?php $selected_username = isset($this->data['username-text']) ? $this->data['username-text'] : ((isset($this->editticket->uid) && $this->editticket->uid != 0) ? $this->editticket->name : ''); ?>
                                        <div id="username-div" class="jsst-user-select-input"><input type="text" class="<?php if($field->required == 1) echo ' required'; ?>" value="<?php echo htmlspecialchars((string) $selected_username, ENT_QUOTES, 'UTF-8'); ?>" id="username-text" name="username-text" readonly="readonly" /></div>
                                        <?php if (!isset($this->editticket->uid) || $this->editticket->uid == 0) { ?>
                                            <a href="#" class="jsst-userpopup-trigger"><?php echo Text::_('Select User'); ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php
                            }
                        break;
                    case 'email': ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                            <div class="js-title">
                                    <label for="email"><?php echo Text::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                            </div>
                            <div class="js-value">
                                <input class="inputbox required validate-email" type="text" id="email" name="email" size="40" maxlength="255" value="<?php if(isset($this->data['email'])) echo $this->data['email']; elseif (isset($this->editticket)) echo $this->editticket->email; ?>" />
                            </div>
                        </div>
                        <?php
                    } ?>
                    <?php break;
                    case 'fullname':
                        ?>
                    <?php if ($field->published == 1) { ?>
                            <div class="js-form-wrapper">
                                <div class="js-title">
                                    <label for="name"><?php echo Text::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                                </div>
                                <div class="js-value">
                                    <input class="inputbox required" type="text" name="name" id="name" size="40" maxlength="255" value="<?php if(isset($this->data['name'])) echo $this->data['name']; elseif (isset($this->editticket)) echo $this->editticket->ticketname; ?>" />
                                </div>
                            </div>
                        <?php } ?>
                        <?php break;
                    case 'phone':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                                <div class="js-title">
                                    <label for="phone"><?php echo Text::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                                </div>
                                <div class="js-value">
                                    <input class="inputbox <?php if($field->required == 1) echo ' required'; ?>" type="text" name="phone" id="phone" size="40" maxlength="255" value="<?php if(isset($this->data['phone'])) echo $this->data['phone']; elseif (isset($this->editticket)) echo $this->editticket->phone; ?>" />
                                </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'phoneext':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                                <div class="js-title">
                                    <label for="phoneext"><?php echo Text::_($field->fieldtitle); ?>:&nbsp;</label>
                                </div>
                                <div class="js-value">
                                    <input class="inputbox" type="text" name="phoneext" id="phoneext" size="5" maxlength="255" value="<?php if(isset($this->data['phoneext'])) echo $this->data['phoneext']; elseif (isset($this->editticket)) echo $this->editticket->phoneext; ?>" />
                                </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'department':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                            <div class="js-title">
                                <label for="departmentid"><?php echo Text::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                            </div>
                            <div class="js-value js-export-row-alue">
                                <?php echo $this->lists['departments']; ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'helptopic':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                            <div class="js-title">
                                <label for="helptopicid"><?php echo Text::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                            </div>
                            <div class="js-value js-export-row-alue select-field-null-margin" id="helptopic">
                                <?php echo $this->lists['helptopic']; ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'priority':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                            <div class="js-title">
                                <label for="priorityid"><?php echo Text::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                            </div>
                            <div class="js-value js-export-row-alue select-field-null-margin">
                                <?php echo $this->lists['priorities']; ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php break;
                    case 'subject':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper">
                        <div class="js-title">
                            <label for="subject"><?php echo Text::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                        </div>
                        <div class="js-value">
                            <input style="width:100%" class="inputbox required" type="text" name="subject" id="subject" size="40" maxlength="255" value="<?php if(isset($this->data['subject'])) echo $this->data['subject']; elseif (isset($this->editticket)) echo $this->editticket->subject; ?>" />
                        </div>
                        </div>
                            <?php } ?>
                                <?php break;
                    case 'premade':
                                ?>
                        <?php if ($field->published == 1) { ?>
                            <?php //if (!isset($this->editticket)) { ?>
                              <div class="js-form-wrapper">
                                <div class="js-title">
                                    <label for="premadeid"><?php echo Text::_($field->fieldtitle); ?>:&nbsp;</label>
                                </div>
                                <div class="js-value" id="premades">
                                    <?php echo $this->lists['premade']; ?>
                                </div>
                        </div>
                            <?php //} ?>
                        <?php } ?>
                    <?php break; ?> <?php
                case 'issuesummary': ?>
                    <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper fullwidth">
                        <div class="js-title"><?php echo Text::_('Canned Response'); ?>
                            </div>
                            <div class="js-value"><div class="js-form-append">
                            <input type="checkbox" name="append" id ="append" /><label for="append"><?php echo Text::_('Append'); ?></label></div></div>
                        </div>
                        <div class="js-form-wrapper fullwidth">
                            <div class="js-title">
                                <label for="message"><?php echo Text::_($field->fieldtitle); ?>:&nbsp;<font color="red">*</font></label>
                            </div>
                            <div class="js-value">
                            <?php
                                if(isset($this->editticket)) $message = $this->editticket->message; else $message = '';
                                $conf   = Factory::getConfig();
                                $editor = Editor::getInstance($conf->get('editor'));
                                echo $editor->display('message', $message, '', '300', '60', '20', false);
                            ?>
                        </div>
                                </div>
                        <?php } ?>
                    <?php break; ?> <?php

                case 'attachments': ?>
                    <?php if ($field->published == 1) { ?>
                    <div class="js-form-wrapper fullwidth">
                    <div class="js-title">
                        <label for="attachment"><?php echo Text::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                        </div>
                        <?php 
                        if(isset($this->attachments) && getJSTicketPHPFunctionsClass()->jsticket_count($this->attachments) > 0){
                            $attachmentreq = '';
                        }else{
                            $attachmentreq = $field->required == 1 ? 'required' : '';
                        }
                        ?>
                        <div class="js-value">
                            <div class="js-attachment-files jsst-admin-attachment-zone"
                                 data-jsst-dropzone="1"
                                 data-max-files="<?php echo (int) $this->config['noofattachment']; ?>"
                                 data-drop-label="<?php echo htmlspecialchars(Text::_('Drag and drop files here'), ENT_QUOTES, 'UTF-8'); ?>"
                                 data-release-label="<?php echo htmlspecialchars(Text::_('Release files to attach'), ENT_QUOTES, 'UTF-8'); ?>"
                                 data-browse-label="<?php echo htmlspecialchars(Text::_('or click to browse files'), ENT_QUOTES, 'UTF-8'); ?>"
                                 data-empty-label="<?php echo htmlspecialchars(Text::_('No file selected'), ENT_QUOTES, 'UTF-8'); ?>"
                                 data-remove-label="<?php echo htmlspecialchars(Text::_('Remove file'), ENT_QUOTES, 'UTF-8'); ?>"
                                 data-limit-message="<?php echo htmlspecialchars(Text::_('File upload limit exceed'), ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="js-value-attachment-text jsst-attachment-input-row">
                                    <input type="file" class="inputbox <?php echo $attachmentreq; ?>" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlength="30"/>
                                    <span class="jsst-attachment-file-name" aria-live="polite"><?php echo Text::_('No file selected'); ?></span>
                                    <span class="js-attachment-remove" role="button" tabindex="0" aria-label="<?php echo htmlspecialchars(Text::_('Remove file'), ENT_QUOTES, 'UTF-8'); ?>"></span>
                                </span>
                            </div>
                        <div class="js-attachment-option">
                            <span class="js-attachment-ins">
                                <small><?php echo Text::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo Text::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></small>
                            </span>
                            <button type="button" id="js-attachment-add" class="jsst-attachment-add"><?php echo Text::_('Add Files'); ?></button>
                        </div>
                            <?php
                            if (!empty($this->attachments)) {
                                $ticketid = isset($this->editticket->id) ? (int) $this->editticket->id : 0;
                                echo '<div class="jsst-existing-attachments" aria-label="' . htmlspecialchars(Text::_('Existing attachments'), ENT_QUOTES, 'UTF-8') . '">';
                                foreach ($this->attachments AS $attachment) {
                                    $attachment_name = htmlspecialchars((string) $attachment->filename, ENT_QUOTES, 'UTF-8');
                                    $attachment_size_kb = max(0, (float) $attachment->filesize);
                                    $attachment_size = $attachment_size_kb >= 1024
                                        ? number_format($attachment_size_kb / 1024, 2) . ' MB'
                                        : number_format($attachment_size_kb, 2) . ' KB';
                                    $delete_url = 'index.php?option=com_jssupportticket&c=ticket&task=deleteattachment&id=' . (int) $attachment->id . '&ticketid=' . $ticketid . '&' . Factory::getSession()->getFormToken() . '=1';
                                    echo '<div class="jsst-existing-attachment">'
                                        . '<span class="jsst-existing-attachment-icon" aria-hidden="true"></span>'
                                        . '<span class="jsst-existing-attachment-meta"><strong>' . $attachment_name . '</strong><small>' . $attachment_size . '</small></span>'
                                        . '<a class="jsst-existing-attachment-delete" href="' . htmlspecialchars($delete_url, ENT_QUOTES, 'UTF-8') . '">' . Text::_('Delete Attachment') . '</a>'
                                        . '</div>';
                                }
                                echo '</div>';
                            }
                                ?>

                        </div>
                        </div>
                                    <?php } ?>
                                    <?php break;
                    case 'status':
                        ?>
                        <?php if ($field->published == 1) { ?>
                        <div class="js-form-wrapper fullwidth">
                        <div class="js-title">
                            <label for="active"> <?php echo Text::_($field->fieldtitle); ?>:<?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label>
                        </div>
                        <div class="js-value-radio-btn">
                            <div class="jsst-formfield-status-radio-button-wrap">
                            <input type="radio" id="open" value="0" name="status"<?php if (isset($this->editticket)) {if ($this->editticket->status == 0) echo "checked=''"; }else{ echo "checked=''";} ?> /><label for="open"><?php echo Text::_('Open'); ?></label>
                            </div>
                            <div class="jsst-formfield-status-radio-button-wrap">
                            <input type="radio" id="close" value="4" name="status"<?php if (isset($this->editticket)) {if ($this->editticket->status == 4) echo "checked=''"; } ?> /><label for="close"><?php echo Text::_('Close'); ?></label>
                            </div>
                            <div class="jsst-formfield-status-radio-button-wrap">
                            <input type="radio" id="waitinadminreply" value="1" name="status"<?php if (isset($this->editticket)) {if ($this->editticket->status == 1) echo "checked=''"; } ?> /><label for="waitinadminreply"><?php echo Text::_('Waiting for admin/staff reply'); ?></label>
                            </div>
                            <div class="jsst-formfield-status-radio-button-wrap">
                            <input type="radio" id="waitincustomerreply" value="3" name="status"<?php if (isset($this->editticket)) {if ($this->editticket->status == 3) echo "checked=''"; } ?> /><label for="waitincustomerreply"><?php echo Text::_('Waiting for customer reply'); ?></label></div>
                        </div>
                                </div>
                            <?php } ?>
                            <?php
                            break;
                    default:
                        $params = NULL;
                        $id = NULL;
                        $isadmin = true;
                        $j = 0;
                        if(isset($this->editticket)){
                            $id = $this->editticket->id; 
                            $params = $this->editticket->params; 
                        }
                        echo getCustomFieldClass()->formCustomFields($field , $id , $params ,$isadmin );
                        break;
            }
                ?>
            <?php }  // end of fieldsordering foreach 

        }else{
            messageslayout::getPermissionNotAllow(); //permission not granted
        }?>
                
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo Text::_('Submit Ticket'); ?>" /></div></div>

                <input type="hidden" name="id" id="id" value="<?php if (isset($this->editticket)) echo $this->editticket->id; ?>" />
                <input type="hidden" name="isoverdue" id="isoverdue" value="<?php if (isset($this->editticket)) echo $this->editticket->isoverdue; ?>" />
                <input type="hidden" name="ticketid" id="ticketid" value="<?php if (isset($this->editticket)) echo $this->editticket->ticketid; ?>" />
                <input type="hidden" name="c" id="c" value="ticket" />
                <input type="hidden" name="task" id="task" value="saveticket" />
                <input type="hidden" name="uid" id="uid" value="<?php if(isset($this->editticket)) echo $this->editticket->uid; ?>" />
                <input type="hidden" name="view" id="view" value="ticket" />
                <input type="hidden" name="layout" id="layout" value="formticket" />
                <input type="hidden" name="check" id="check" value="" />
                <input type="hidden" name="option" id="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="created" id="created" value="<?php if (isset($this->editticket)) echo $this->editticket->created; else echo $curdate = date('Y-m-d H:i:s'); ?>"/>
                <input type="hidden" name="update" id="update" value="<?php if (isset($this->editticket)) echo $update = date('Y-m-d H:i:s'); ?>"/>
                <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
<script type="text/javascript">
    function gethelptopicandpremade(src,src1, val) {
        jQuery('div#'+src).html("Loading...");
        jQuery('div#'+src1).html("Loading...");
        jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=listhelptopicandpremade&<?php echo Factory::getSession()->getFormToken(); ?>=1",{val:val},function(data){
            if(data){
                var obj = eval("(" + data + ")");
                jQuery('div#'+src).html(obj.helptopic); //retuen value
                jQuery('div#'+src1).html(obj.premade); //retuen value
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

    function getpremade(src, val, append) {
        jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getpremadeforinternalnote&<?php echo Factory::getSession()->getFormToken(); ?>=1",{val:val},function(data){
            if(data){
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
                        jQuery('textarea#message').val(data);
                    }
                }
            }
        });
    }

    // Attachment selection, drag-and-drop and remove actions are handled by
    // include/js/jsst-admin-attachments.js for all admin attachment zones.
    function updateuserlist(pagenum){
        var name = jQuery("input#userpopup-name").val();
        var username = jQuery("input#userpopup-username").val();
        var emailaddress = jQuery("input#userpopup-emailaddress").val();
        jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1", {name:name,username:username,emailaddress:emailaddress,userlimit:pagenum}, function (data) {
            if(data){
                jQuery("div#records").html("");
                jQuery("div#records").html(data);
                setUserLink();
            }
        });
    }
    function setUserLink() {
        jQuery("a.js-userpopup-link").each(function () {
            var anchor = jQuery(this);
            jQuery(anchor).click(function (e) {
                e.preventDefault();
                var id = jQuery(this).attr('data-id');
                var name = jQuery(this).text();
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
            });
        });
    }
        jQuery(document).ready(function () {
            jQuery("a.jsst-userpopup-trigger").click(function (e) {
                e.preventDefault();
                jQuery("div#userpopupblack").show();
                jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1",{},function(data){
                  if(data){
                    jQuery('div#records').html("");
                    jQuery('div#records').html(data);
                    setUserLink();
                  }
                });
                jQuery("div#userpopup").slideDown('slow');
            });
            jQuery("[data-jsst-userpicker-reset=\"1\"]").on("click", function () {
                jQuery("#userpopup-username, #userpopup-name, #userpopup-emailaddress").val("");
                updateuserlist(0);
            });
            jQuery("form#userpopupsearch").submit(function (e) {
                e.preventDefault();
                var name = jQuery("input#userpopup-name").val();
                var username = jQuery("input#userpopup-username").val();
                var emailaddress = jQuery("input#userpopup-emailaddress").val();
                jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1",{name: name, emailaddress: emailaddress,username:username}, function (data) {
                    if (data) {
                        jQuery("div#records").html(data);
                        setUserLink();
                    }
                });//jquery closed
            });
            jQuery(".jsst-userpicker-close, div#userpopupblack").click(function (e) {
                jQuery("div#userpopup").slideUp('slow', function () {
                    jQuery("div#userpopupblack").hide();
                });

            });
        });
		function getDataForDepandantField(parentf, childf, type) {
			if (type == 1) {
				var val = jQuery("select#" + parentf).val();
			} else if (type == 2) {
				var val = jQuery("input[name=" + parentf + "]:checked").val();
			}
			jQuery.post('index.php?option=com_jssupportticket&c=userfields&task=datafordepandantfield&<?php echo Factory::getSession()->getFormToken(); ?>=1', {fvalue: val, child: childf}, function (data) {
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
</script>
