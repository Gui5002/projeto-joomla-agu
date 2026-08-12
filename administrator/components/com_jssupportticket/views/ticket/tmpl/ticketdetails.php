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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$conf   = Factory::getConfig();
$editor = Editor::getInstance($conf->get('editor'));

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('behavior.formvalidator');

$document = Factory::getDocument();
$document->addScript('components/com_jssupportticket/include/js/jquery_idTabs.js');
$document->addScript('components/com_jssupportticket/include/js/file/file_validate.js');
Text::script('Error file size too large');
Text::script('Error file extension mismatch');
?>
<script type="text/javascript">
    function validate_form(f)
    {
        if (document.formvalidator.isValid(f)) {
            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken();
                else echo Factory::getSession()->getFormToken(); ?>';//send token
        } else {
            alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
        document.adminForm.submit();
    }
    function updateticketlist(pagenum,ticketid){
        jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo Factory::getSession()->getFormToken(); ?>=1", {ticketid:ticketid,ticketlimit:pagenum}, function (data) {
            if(data){
                var d = JSON.parse(data);
                if(d['status'] == 1){
                    jQuery("div#popup-record-data").show();
                    jQuery("div#popup-record-data").html("");
                    jQuery("div#popup-record-data").html(d['data']);
                }
            }
        });
    }
    //moreDetailDiv
    jQuery(document).ready(function(){
        jQuery("a#chng-prority").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopupforchangepriority").slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery("div#userpopupblack, span.close-history").click(function (e) {
            jQuery("div#userpopupforchangepriority").slideUp('slow');
            setTimeout(function () {
                jQuery('div#userpopupblack').hide();
            }, 700);
        });
        jQuery('a[href="#"]').click(function(e){
            e.preventDefault();
        });
        //ATTACHMENTS
        jQuery("#js-attachment-add").click(function () {
            var obj = this;
            var current_files = jQuery('input[type="file"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            var append_text = "<span class='js-value-text'><input name='filename[]' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
            if (current_files < total_allow) {
                jQuery(".js-attachment-files").append(append_text);
            } else if ((current_files === total_allow) || (current_files > total_allow)) {
                alert("<?php echo Text::_('File upload limit exceed'); ?>");
                jQuery(obj).hide();
            }
        });
        jQuery(document).delegate(".js-attachment-remove", "click", function (e) {
            var current_files = jQuery('input[type="file"]').length;
            if(current_files!=1)
                jQuery(this).parent().remove();
            var current_files = jQuery('input[type="file"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            if (current_files < total_allow) {
                jQuery("#js-attachment-add").show();
            }
        });
        jQuery(document).delegate("#ticketidcopybtn", "click", function(){
            var temp = jQuery("<input>");
            jQuery("body").append(temp);
            temp.val(jQuery("#ticketidcopybtn").attr("data-ticket-hash-id")).select();
            document.execCommand("copy");
            temp.remove();
            jQuery("#ticketidcopybtn").text(jQuery("#ticketidcopybtn").attr('success'));
        });
    });

    function formField(){
        jQuery('div#jsjob_installer_waiting_div').show();
        jQuery("#name").val("");
        jQuery("#email").val("");
        jQuery("#ticketpopupsearch").submit();
    }
</script>
<div id="popup-record-data" style="display:inline-block;"></div>
<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div class="jsst-popup-background" style="display:none;" ></div>
    <div id="js-tk-cparea">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo Text::_('Dashboard'); ?></a></li>
                        <li><a href="index.php?option=com_jssupportticket&c=ticket&layout=tickets" title="Dashboard"><?php echo Text::_('Tickets'); ?></a></li>
                        <li><?php echo Text::_('Ticket Detail'); ?></li>
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
                    <?php echo Text::_('Version').Text::_(' :'); ?>
                    <span class="jsstadmin-ver">
                        <?php $version = str_split($this->version);
                        $version = implode('.', $version);
                        echo $version; ?>
                    </span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo $this->ticketdetail->subject; ?></h4>
        </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <div id="js-tk-ticket-detail">
                <div class="js-tkt-det-left">
                    <div class="js-tkt-det-cnt js-tkt-det-info-wrp" id="">
                        <div class="js-tkt-det-user">
                            <?php if($this->ticketdetail->status != 5){ ?>
                            <div class="js-tkt-det-user-image">
                                <img class="requester-image" src="components/com_jssupportticket/include/images/user.png">
                            </div>
                            <div class="js-tkt-det-user-cnt">
                                <div class="js-tkt-det-user-data name">
                                    <?php echo $this->ticketdetail->name; ?>
                                </div>
                                <div class="js-tkt-det-user-data email">
                                    <?php echo $this->ticketdetail->email; ?>
                                </div>
                                <div class="js-tkt-det-user-data number">
                                    <?php if ($this->ticketdetail->phone) { ?>
                                        <?php echo $this->ticketdetail->phone;
                                         ?>
                                    <?php } ?>  
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="js-tkt-det-other-tkt">
                            <a href="index.php?option=com_jssupportticket&c=ticket&layout=tickets&uid=<?php echo $this->ticketdetail->uid ?>" class="js-tkt-det-other-tkt-btn">
                                <?php echo Text::_('View').' '.Text::_('all tickets').' '.Text::_('by').' '; ?>
                                <?php echo $this->ticketdetail->name; ?>
                            </a>
                        </div>
                        <div class="js-tkt-det-tkt-msg">
                            <p><?php echo $this->ticketdetail->message; ?></p>
                        </div>
                        <div class="js-tkt-det-actn-btn-wrp">
                            <?php if($this->ticketdetail->status != 5){ ?>
                                <?php $link = 'index.php?option='.$this->option.'&c=ticket&task=addnewticket&cid[]='.$this->ticketdetail->id; ?>
                                <a class="js-detal-alinks" href="<?php echo $link; ?>">
                                    <img title="<?php echo Text::_('Edit Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/edit.png">
                                    <span>
                                        <?php echo Text::_('Edit Ticket'); ?>
                                    </span>
                                </a>
                                <a class="js-detal-alinks" href="#" onclick="actioncall('<?php if ($this->ticketdetail->status == 4) echo 8; else echo 3; ?>')">
                                    <?php if ($this->ticketdetail->status != 4){?>
                                        <img title="<?php echo Text::_('Close Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/close.png">
                                        <span>
                                            <?php echo Text::_('Close Ticket'); ?>
                                        </span>
                                    <?php }else{?>
                                        <img title="<?php echo Text::_('Reopen Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/reopen.png">
                                        <span>
                                            <?php echo Text::_('Reopen Ticket'); ?>
                                        </span>
                                    <?php } ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="js-tk-subheading">
                        <?php echo Text::_('Ticket Thread'); ?>
                    </div>
                    <div id="js-ticket-threads">
                        <div class="js-tk-pic">
                            <img src="<?php echo Uri::root(); ?>components/com_jssupportticket/include/images/user.png" />
                        </div>
                        <div class="js-tk-message">
                            <div class="js-ticket-thread-data">
                                <span class="js-ticket-thread-person">
                                    <?php echo $this->ticketdetail->name; ?>
                                </span>
                            </div>
                            <div class="js-ticket-thread-data note-msg">
                                <?php echo $this->ticketdetail->message; ?>
                                <?php
                                if (isset($this->ticketattachment[0]->filename)) {
                                    foreach ($this->ticketattachment as $attachment) {
                                        echo '<div class="js_ticketattachment">';
                                            $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&' . Factory::getSession()->getFormToken() . '=1';
                                            echo "<img src='components/com_jssupportticket/include/images/clip.png'><a target='_blank' href=" . $path . ">"
                                            . $attachment->filename . "&nbsp(" . getJSTicketPHPFunctionsClass()->jsticket_round($attachment->filesize, 2) . " KB)" . "</a>";
                                        echo "</div>";
                                    }
                                } ?>
                            </div>
                            <div class="js-ticket-thread-cnt-btm">
                                <div class="js-ticket-thread-date">
                                    <?php $replyby = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($this->ticketdetail->created),"l F d, Y, H:i:s"); echo ' ( '. $replyby.' )'; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php 
                    jimport('joomla.filter.output');
                    for ($i = 0, $n = getJSTicketPHPFunctionsClass()->jsticket_count($this->ticketreplies); $i < $n; $i++) {
                    $row = & $this->ticketreplies[$i];
                    ?>
                    <div id="js-ticket-threads">
                        <div class="js-tk-pic">
                            <img src="components/com_jssupportticket/include/images/user.png"/>
                        </div>
                        <div class="js-tk-message">
                            <div class="js-ticket-thread-data">
                                <span class="js-ticket-thread-person">
                                    <?php if($row->name) echo $row->name; else echo $this->ticketdetail->name; ?>
                                </span>
                            </div>
                            <div class="js-ticket-thread-data note-msg">
                                <?php echo $row->message; ?>
                                <?php
								$reply_created = $row->created;
                                $count = $row->count;
                                if ($count >= 1) {
                                    $outdex = $i + $count;
                                    for ($j = $i; $j < $outdex; $j++) {
                                        if ($row->filename && $row->filename <> '') {
                                            echo '<div class="js_ticketattachment">';
                                            if($row->filename && $row->filename <> ''){
                                                $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$row->attachmentid.'&' . Factory::getSession()->getFormToken() . '=1';
                                                echo "<img src='components/com_jssupportticket/include/images/clip.png'><a target='_blank' href=" . $path . ">"
                                                . $row->filename . "&nbsp(" . getJSTicketPHPFunctionsClass()->jsticket_round($row->filesize, 2) . " KB)" . "</a>";
                                            }
                                        echo "</div>";
                                 }
                                        $row = & $this->ticketreplies[$j + 1];
                                    }
                                    $i = $outdex - 1;
                                } ?>
                            </div>
                            <div class="js-ticket-thread-cnt-btm">
                                <div class="js-ticket-thread-date">
                                    <span class="timedate">
                                        <!-- <?php //$replyby = date("l F d, Y, h:i:s", getJSTicketPHPFunctionsClass()->jsticket_strtotime($row->created)); echo ' ( '. $replyby.' )'; ?> -->
                                        <?php
                                        if ($reply_created != '' && $reply_created != '0000-00-00 00:00:00'){
                                            echo HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($reply_created),"l F d, Y");
                                        }
                                        ?>
                                    </span>
                                </div>
                                
                            </div>
                        </div>
                    </div> 
            <?php } ?>
                    <?php if($this->ticketdetail->status != 5){ ?>
                        <div id="button">
                            <div class="js-tk-subheading js-margin-bottom">
                                <?php echo Text::_('Post reply'); ?>
                            </div>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-title">
                                    <?php echo Text::_('Response'); ?>:&nbsp;<font color="red">*</font></div>
                                <div class="js-value"><?php 
                                    echo $editor->display('responce', '', '', '300', '60', '20', false); ?></div>
                            </div>
                            <?php if ($this->isAttachmentPublished) { ?>
                                <div class="js-tk-tabs-wrapper js-mg-bottom">
                                    <div class="js-title"><?php echo Text::_('Attachments'); ?>:&nbsp;</div>
                                    <div class="js-value">
                                        <div id="js-attachment-files" class="js-attachment-files">
                                            <span class="js-value-text">
                                                <input type="file" class="inputbox" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                                <span class='js-attachment-remove'></span>
                                            </span>
                                        </div>
                                        <div id="js-attachment-option">
                                            <span class="js-attachment-ins">
                                                <small><?php echo Text::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo Text::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></small>
                                            </span>
                                            <span id="js-attachment-add"><?php echo Text::_('Add Files'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-title"><?php echo Text::_('Ticket Status'); ?>:&nbsp;</div>
                                <div class="js-value">
                                    <div class="jsst-formfield-radio-button-wrp">
                                        <input type="checkbox" name="replystatus" id ="replystatus" value="4"/>
                                        <?php echo Text::_('Close on reply'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input  class="button setfloatoverride" type="button" onclick="validate_form_department(document.adminForm)" value="<?php echo Text::_('Post reply'); ?>"/></div></div>
                        </div> <!--  end div id=button -->
                    <?php } ?>
                </div><?php /* </div> */?>
                <div class="js-tkt-det-right">
                    <div  class="js-tkt-det-cnt js-tkt-det-tkt-info">
                        <?php if ($this->ticketdetail->lock == 1) { ?>
                            <div class="js-tkt-det-status" style="background-color: darkred;"><?php echo Text::_('Lock'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 0) { ?>
                            <div class="js-tkt-det-status" style="background-color: #9ACC00;"><?php echo Text::_('New'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 1) { ?>
                            <div class="js-tkt-det-status" style="background-color: orange;"><?php echo Text::_('Waiting reply'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 2) { ?>
                            <div class="js-tkt-det-status" style="background-color: #FF7F50;"><?php echo Text::_('In progress'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 3) { ?>
                            <div class="js-tkt-det-status" style="background-color: #507DE4;"><?php echo Text::_('Replied'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 4) { ?>
                            <div class="js-tkt-det-status" style="background-color: #CB5355;"><?php echo Text::_('Close'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 5) { ?>
                            <div class="js-tkt-det-status" style="background-color: #ee1e22;"><?php echo Text::_('Close due to merged'); ?></div>
                        <?php } ?>
                        <div class="js-tkt-det-info-cnt">
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo Text::_('Created'); ?>&nbsp;:</span>
                                <span class="js-value"><?php echo HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($this->ticketdetail->created),'y-m-d H:i:s'); ?></span>
                            </div>
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo Text::_('Last Reply'); ?>&nbsp;:</span>
                                <span class="js-value"><?php if ($this->ticketdetail->lastreply == '' || $this->ticketdetail->lastreply == '0000-00-00 00:00:00') echo Text::_('Not given'); else echo HTMLHelper::_('date',$this->ticketdetail->lastreply,$this->config['date_format']); ?></span>
                            </div>
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo Text::_('Ticket Id'); ?>&nbsp;:</span>
                                <span class="js-value"><?php echo $this->ticketdetail->ticketid; ?>
                                    <a href="#" title="<?php echo Text::_('Copy') ?>" class="js-tkt-det-copy-id" id="ticketidcopybtn" data-ticket-hash-id = "<?php echo $this->ticketdetail->ticketid; ?>" success="<?php echo Text::_('Copied') ?>"><?php echo Text::_('Copy') ?></a>
                                </span>
                            </div>
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo Text::_('Department'); ?>&nbsp;:</span>
                                <span class="js-value"><?php echo Text::_($this->ticketdetail->departmentname); ?></span>
                            </div>
                            <?php
                                $customfields = getCustomFieldClass()->userFieldsData(1);
                                foreach ($customfields as $field) {
                                    if($field->userfieldtype != 'termsandconditions'){
                                        echo getCustomFieldClass()->showCustomFields($field, 3 , $this->ticketdetail->params , $this->ticketdetail->id);   
                                    }    
                                }
                            ?>
                        </div>
                    </div>
                    <div class="js-tkt-det-cnt js-tkt-det-tkt-prty">
                        <div class="js-tkt-det-hdg">
                            <div class="js-tkt-det-hdg-txt">
                                <?php echo Text::_('Priority'); ?>
                            </div>
                            <a title="Change" href="#" class="js-tkt-det-hdg-btn" id="chng-prority">
                                <?php echo Text::_('Change'); ?>
                            </a>
                        </div>
                        <div class="js-tkt-det-tkt-prty-txt" style="color:#FFFFF;background:<?php echo $this->ticketdetail->prioritycolour; ?>;"><?php echo Text::_($this->ticketdetail->priority); ?>
                        </div>
                    </div>
                    <?php if(isset($this->usertickets) && !empty($this->usertickets)){  ?>
                        <div class="js-tkt-det-cnt js-tkt-det-user-tkts" id="usr-tkt">
                            <div class="js-tkt-det-hdg">
                                <div class="js-tkt-det-hdg-txt">
                                    <?php echo $this->ticketdetail->name . "'s "; ?>
                                    <?php echo Text::_('Tickets'); ?> 
                                </div>
                            </div>
                            <div class="js-tkt-det-usr-tkt-list">
                                <?php foreach($this->usertickets AS $userticket){ ?>
                                    <div class="js-tkt-det-user">
                                        <div class="js-tkt-det-user-image">
                                            <img src="components/com_jssupportticket/include/images/user.png" srcset="" class="avatar avatar-96 photo" height="96" width="96">
                                        </div>
                                        <div class="js-tkt-det-user-cnt">
                                            <div class="js-tkt-det-user-data name">
                                                <span id="usr-tkts">
                                                    <a title="view ticket" href="<?php echo 'index.php?option=' . $this->option . '&c=ticket&layout=ticketdetails&cid[]='.$userticket->id; ?>">
                                                        <span class="js-tkt-det-user-val">
                                                            <?php echo $userticket->subject; ?>
                                                        </span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="js-tkt-det-user-data">
                                                <span class="js-tkt-det-user-tit"><?php echo Text::_('Department'); ?> : </span>
                                                <span class="js-tkt-det-user-val"><?php echo $userticket->departmentname; ?></span>
                                            </div>
                                            <div class="js-tkt-det-user-data">
                                                <span class="js-tkt-det-prty" style="background: <?php echo $userticket->prioritycolour; ?>;">
                                                    <?php echo Text::_($userticket->priority); ?>
                                                </span>
                                                <?php if ($userticket->status == 0) { ?>
                                                    <span class="js-tkt-det-status"><?php echo Text::_('New'); ?></span>
                                                <?php } elseif ($userticket->status == 1) { ?>
                                                    <span class="js-tkt-det-status"><?php echo Text::_('Waiting reply'); ?></span>
                                                <?php } elseif ($userticket->status == 2) { ?>
                                                    <span class="js-tkt-det-status"><?php echo Text::_('In progress'); ?></span>
                                                <?php } elseif ($userticket->status == 3) { ?>
                                                    <span class="js-tkt-det-status"><?php echo Text::_('Replied'); ?></span>
                                                <?php } elseif ($userticket->status == 4) { ?>
                                                    <span class="js-tkt-det-status"><?php echo Text::_('Close'); ?></span>
                                                <?php } elseif ($userticket->status == 5) { ?>
                                                    <span class="js-tkt-det-status"><?php echo Text::_('Close due to merged'); ?></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>  
                </div>
            </div>
            <!-- POPUP START -->
            <!-- priority popup -->
            <div id="userpopupblack" style="display:none;" ></div>
            <div id="userpopupforchangepriority" style="display:none;">
                <div class="js-ticket-priorty-header">
                    <?php echo Text::_('Change Priority'); ?><span class="close-history"></span>
                </div>
                <div class="js-ticket-priorty-fields-wrp">
                    <div class="js-ticket-select-priorty">
                        <?php echo $this->lists['priorities']; ?>
                    </div>
                </div>
                <div class="js-ticket-priorty-btn-wrp">
                    <button type="submit" class="js-ticket-priorty-save" id="changepriority"  onclick="actioncall(1)" ><?php echo Text::_('Save'); ?></button>
                </div>
                   
            </div>
            <!-- POPUP END -->
            <input type="hidden" name="id" value="<?php echo $this->ticketdetail->id; ?>" />
            <input type="hidden" name="ticketrandomid" value="<?php echo $this->ticketdetail->ticketid; ?>" />
            <input type="hidden" name="email" value="<?php echo $this->ticketdetail->email; ?>" />
            <input type="hidden" name="email_ban" id="email_ban" value="<?php echo $this->isemailban; ?>" />
            <input type="hidden" name="lastreply" value="<?php echo $this->ticketdetail->lastreply; ?>" />
            <input type="hidden" id="staffid" name="staffid" value="<?php echo JSSupportTicketCurrentUser::getInstance()->getId(); ?>" />

            <input type="hidden" id="callaction" name="callaction" value="" />
            <input type="hidden" id="callfrom" name="callfrom" value="postreply" />
            <input type="hidden" id="option" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" id="c" name="c" value="ticket" />
            <input type="hidden" id="view" name="view" value="ticket" />
            <input type="hidden" id="layout" name="layout" value="tickets" />
            <input type="hidden" id="task" name="task" value="actionticket" />
            <input type="hidden" id="check" name="check" value="0" />
            <input type="hidden" id="boxchecked" name="boxchecked" value="0" />
            <input type="hidden" id="created" name="created" value="<?php echo $curdate = date('Y-m-d H:i:s'); ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>

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
    function validate_form_department(f) {
        if(isTinyMCE()){
            var content = tinyMCE.get('responce').getContent();
        }else{
            var content = jQuery("textarea#responce").val();            
        }
        jQuery('#callfrom').val('postreply');
        if (content != '') {
            document.adminForm.submit();
        } else {
            alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
    }
    function actioncall(value) {
        jQuery('#callfrom').val('action');
        jQuery('#callaction').val(value);
        document.adminForm.submit();
    }
    function closePopup() {
        jQuery('#popup-record-data').hide();
        jQuery("div.jsst-popup-wrapper").slideUp('slow');
        setTimeout(function () {
            jQuery('div.jsst-popup-background').hide();
        }, 700);
    }
    function editResponce(id) {
        var rsrc = 'responce_' + id;
        var src = 'responce_edit_' + id;
        var esrc = 'editor_responce_' + id;
        showhide(rsrc, 'none');
        showhide(src, 'block');
        jQuery('#' + src).html("Loading...");
        jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=editresponce&id=' + id + '&<?php echo Factory::getSession()->getFormToken(); ?>=1', {data: id}, function (data) {
            jQuery('#' + src).html(data); //retuen value
            if (!tinyMCE.get(esrc)) { // toggle editor
                tinyMCE.execCommand('mceToggleEditor', false, esrc);
                return false;
            }
        });
    }

    function saveResponce(id) {
        var esrc = 'editor_responce_' + id;
        if (!tinyMCE.get(esrc)) { // check toggle
            alert("Please toggle editor");
        } else {
            var contant = tinyMCE.get(esrc).getContent();
            var rsrc = 'responce_' + id;
            var src = 'responce_edit_' + id;
            showhide(rsrc, 'block');
            showhide(src, 'none');


            jQuery('#' + rsrc).html("Saving...");
            var arr = new Array();
            arr[0] = id;
            arr[1] = contant;
            jQuery.ajax({
                type: "POST",
                url: "index.php?option=com_jssupportticket&c=ticket&task=saveresponceajax&id=" + arr[0] + "&val=" + arr[1] + "&<?php echo Factory::getSession()->getFormToken(); ?>=1",
                data: arr,
                success: function (data) {
                    if (data == 1) {
                        jQuery('#' + rsrc).html(contant);
                    } else if (data == 10) {
                        jQuery('#' + rsrc).html(data);
                    } else {
                        jQuery('#' + rsrc).html(data);
                    }
                    tinymce.remove(tinyMCE.get(esrc));

                }
            });
        }
    }

    function closeResponce(id) {
        var rsrc = 'responce_' + id;
        var src = 'responce_edit_' + id;
        var esrc = 'editor_responce_' + id;
        showhide(rsrc, 'block');
        showhide(src, 'none');
        tinymce.remove(tinyMCE.get(esrc));

    }

    function deleteResponce(id) {
        if (confirm("<?php echo Text::_('Are you sure to delete'); ?>")) {

            var rsrc = 'responce_' + id;
            jQuery('#' + rsrc).html("Deleting...");

            jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=deleteresponceajax&id=' + id + '&<?php echo Factory::getSession()->getFormToken(); ?>=1', {data: id}, function (data) {
                jQuery('#' + src).html(data);
            });
        }
    }
    function showhide(layer_ref, state) {
        if (state == 'none') {
            jQuery('div#' + layer_ref).hide('slow');
        } else if (state == 'block') {
            jQuery('div#' + layer_ref).show('slow');

        }
    }
</script>
