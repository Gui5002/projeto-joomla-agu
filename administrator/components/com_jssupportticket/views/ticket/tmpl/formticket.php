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
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\HTML\HTMLHelper;

jimport('joomla.html.pane');
HTMLHelper::_('behavior.formvalidator');

/*
HTMLHelper::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
HTMLHelper::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
*/
$document = Factory::getDocument();
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
        jQuery('#submit_app_button').attr('disabled',true);
        return true;
    }
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
</script>
<div id="userpopupblack" style="display:none;"></div>
<div id="userpopup" style="display:none;">
    <form id="userpopupsearch">
        <div class="search-center">
            <div class="search-center-heading"><?php echo Text::_('Select user'); ?><span class="close"></span></div>
            <div class="js-col-md-12">
                <div class="js-col-xs-12 js-col-md-3 js-search-value">
                    <input type="text" name="username" id="username" placeholder="<?php echo Text::_('Username'); ?>" />
                </div>
                <div class="js-col-xs-12 js-col-md-3 js-search-value">
                    <input type="text" name="name" id="name" placeholder="<?php echo Text::_('Name'); ?>" />
                </div>
                <div class="js-col-xs-12 js-col-md-3 js-search-value">
                    <input type="text" name="emailaddress" id="emailaddress" placeholder="<?php echo Text::_('Email Address'); ?>"/>
                </div>
                <div class="js-col-xs-12 js-col-md-3 js-search-value-button">
                    <div class="js-button">
                        <input class="js-button-search" type="submit" value="<?php echo Text::_('Search'); ?>" />
                    </div>
                    <div class="js-button">
                        <input class="js-button-reset" type="submit" onclick="document.getElementById('name').value = '';document.getElementById('username').value = ''; document.getElementById('emailaddress').value = '';" value="<?php echo Text::_('Reset'); ?>" />
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div id="records">
        <div id="records-inner">
            <div class="js-staff-searc-desc">
                <?php echo Text::_('Use Search Feature To Select The User'); ?>
            </div>
        </div>
    </div>
</div>
<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo Text::_('Dashboard'); ?></a></li>
                        <li><?php echo Text::_('Submit Ticket'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo Text::_('Create Ticket'); ?></h1></div>
        <?php //var_dump(isset($this->editticket)); die; ?>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
            <?php if(getJSTicketPHPFunctionsClass()->jsticket_count($this->fieldsordering) > 0){ ?>
            <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <?php
            $count = getJSTicketPHPFunctionsClass()->jsticket_count($this->fieldsordering);
            $i = 0; // for userfield numbering
            foreach ($this->fieldsordering AS $field) { ?>
                <?php switch ($field->field) {
                        case 'users':
                            if ($field->published == 1) {  ?>
                                <div class="js-form-wrapper">
                                    <div class="js-title"><label for="email"><?php echo Text::_($field->fieldtitle); ?><?php if($field->required == 1) echo ' <span style="color:red;">*</span>'; ?></label></div>
                                    <div class="js-value">
                                        <?php if (isset($this->editticket->uid) && $this->editticket->uid!=0) {?>
                                            <div id="username-div"><input type="text" class="<?php if($field->required == 1) echo ' required'; ?>" value="<?php if(isset($this->data['username-text'])) echo $this->data['username-text']; else echo $this->editticket->name; ?>" id="username-text" name="username-text" readonly="readonly" /></div>
                                            <?php } else {
                                            ?>
                                            <div id="username-div"></div><input type="text" class="<?php if($field->required == 1) echo ' required'; ?>" value="<?php if(isset($this->data['username-text'])) echo $this->data['username-text']; ?>" id="username-text" name="username-text" readonly="readonly" /><a href="#" id="userpopup"><?php echo Text::_('Select User'); ?></a>
                                            <?php
                                        }
                                        ?>              
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
                                    <input class="inputbox required" type="text" name="name" id="name" size="40" maxlength="255" value="<?php if(isset($this->data['name'])) echo $this->data['name']; elseif (isset($this->editticket)) echo $this->editticket->name; ?>" />
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
                case 'issuesummary': ?>
                    <?php if ($field->published == 1) { ?>
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
                            <div id="js-attachment-files" class="js-attachment-files">
                                <span class="js-value-attachment-text">
                                    <input type="file" class="inputbox <?php echo $attachmentreq; ?>" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                    <span class='js-attachment-remove'></span>
                                </span>
                            </div>
                            <div id="js-attachment-option">
                                <span class="js-attachment-ins">
                                    <small><?php echo Text::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo Text::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></small>
                                </span>
                                <span id="js-attachment-add"><?php echo Text::_('Add Files'); ?></span>
                            </div>
                            <?php
                            if (!empty($this->attachments)) {
                                $ticketid = isset($this->editticket->id) ? $this->editticket->id : '' ;
                                foreach ($this->attachments AS $attachment) {
                                    echo '
                                        <div class="js_ticketattachment">' . $attachment->filename . ' ( ' . $attachment->filesize . ' ) ' . '<a href="index.php?option=com_jssupportticket&c=ticket&task=deleteattachment&id=' . $attachment->id . '&ticketid=' . $ticketid. '&'.Factory::getSession()->getFormToken().'=1">' . Text::_('Delete Attachment') . '</a></div>';
                                }
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
<?php }  // end of fieldsordering foreach  ?>

            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo Text::_('Save').' '.Text::_('Ticket'); ?>" /></div></div>

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
    <?php }
    ?>
    </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script type="text/javascript">
    function isTinyMCE(){
        is_tinyMCE_active = false;
        if (typeof(tinyMCE) != "undefined") {
            //if(tinyMCE.editors.length > 0){
                is_tinyMCE_active = true;
            //}
        }
        return is_tinyMCE_active;
    }

    jQuery("#js-attachment-add").click(function () {
        var obj = this;
        var current_files = jQuery('input[name="filename[]"]').length;
        var total_allow =<?php echo $this->config['noofattachment']; ?>;
        var append_text = "<span class='js-value-attachment-text'><input name='filename[]' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
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
    function updateuserlist(pagenum){
        var name = jQuery("input#name").val();
        var username = jQuery("input#username").val();
        var emailaddress = jQuery("input#emailaddress").val();
        jQuery.post("index.php?option=com_jssupportticket&c=jssupportticket&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1", {name:name,username:username,emailaddress:emailaddress,userlimit:pagenum}, function (data) {
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
            });
        });
    }

    jQuery(document).ready(function () {
        jQuery("a#userpopup").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopupblack").show();
            jQuery.post("index.php?option=com_jssupportticket&c=jssupportticket&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1",{},function(data){
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
            var emailaddress = jQuery("input#emailaddress").val();
            var username = jQuery("input#username").val();
            jQuery.post("index.php?option=com_jssupportticket&c=jssupportticket&task=getusersearchajax&<?php echo Factory::getSession()->getFormToken(); ?>=1",{name: name,username:username, emailaddress: emailaddress}, function (data) {
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
    });
		function deleteCutomUploadedFile (field1) {
			jQuery("input#"+field1).val(1);
			jQuery("span."+field1).hide();
			
		}        
</script>
