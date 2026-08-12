<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
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


HTMLHelper::_('behavior.formvalidator');
if(JVERSION < 4){
    HTMLHelper::_('behavior.modal');
}
$document = Factory::getDocument();
$document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');

Text::script('Error file size too large');
Text::script('Error file extension mismatch');
?>
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = Factory::getDocument();
    $document->addStyleSheet(Uri::root().'components/com_jssupportticket/include/css/inc.css/ticket-ticketdetail.css', 'text/css');
    $language = Factory::getLanguage();
    $document->addStyleSheet(Uri::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(Uri::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
        if($this->ticketdetail){
            $document = Factory::getDocument();
            $document->addScript('administrator/components/com_jssupportticket/include/js/jquery_idTabs.js');
            $document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
            Text::script('JS_ERROR_FILE_SIZE_TO_LARGE');
            Text::script('JS_ERROR_FILE_EXT_MISMATCH'); ?>

            <script type="text/javascript">
                //
                jQuery(document).ready(function ($) {
                    jQuery(document).delegate("#ticketidcopybtn", "click", function(){
                        var temp = jQuery("<input>");
                        jQuery("body").append(temp);
                        temp.val(jQuery("#ticketrandomid").val()).select();
                        document.execCommand("copy");
                        temp.remove();
                        jQuery("#ticketidcopybtn").text(jQuery("#ticketidcopybtn").attr('success'));
                    });
                });
                function updateticketlist(pagenum,ticketid){
                    jQuery('div#jsjob_installer_waiting_div').show();
                    jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo Factory::getSession()->getFormToken(); ?>=1", {ticketid:ticketid,ticketlimit:pagenum}, function (data) {
                        if(data){
                            var d = JSON.parse(data);
                            jQuery('div#jsjob_installer_waiting_div').hide();
                        }
                    });
                }
                // ////////////////////////////////////////////////////////////////////////////
                //more actions
                jQuery(document).ready(function() {
                    jQuery('a[href="#"]').click( function(e) {
                        e.preventDefault();
                    });
                });
            </script>
            <?php
                $yesno = array(
                '0' => array('value' => '1',
                    'text' => Text::_('JYes')),
                '1' => array('value' => '0',
                    'text' => Text::_('JNo')),);
                 $time_confilct_combo = HTMLHelper::_('select.genericList', $yesno, 'time-confilct-combo', 'class="inputbox" ' . '', 'value', 'text', '');
            ?>
            <div id="jsjob_installer_waiting_div" style="display:none;"></div>
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
                                    <?php
                                        $link = "index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=".$this->Itemid;
                                    ?>
                                    <a href="<?php echo $link; ?>" title="Dashboard">
                                        <?php echo Text::_('My Tickets'); ?>
                                    </a>
                                </li>
                                <li>
                                    <?php echo Text::_('Ticket Detail')?>
                                </li>
                            </ul>
                        </div>
                    </div>
        		<?php } ?>
            </div>
            <div id="tk-detail-wraper">
                <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" onSubmit="return myValidate(this);">
                    <div id="message"></div>
                    <div id="tk_detail_content_wraper">
                        <div class="js-col-md-12 js-ticket-detail-wrapper"> <!-- Ticket Detail Data Top -->
                            <div class="js-ticket-detail-box"><!-- Ticket Detail Box -->
                                <div class="js-ticket-detail-left">
                                    <div class="js-tkt-det-cnt js-tkt-det-info-wrp">
                                        <div class="js-tkt-det-user">
                                            <div class="js-ticket-user-img-wrp">
                                                <img class="js-ticket-staff-img" src="components/com_jssupportticket/include/images/user.png" alt="<?php echo Text::_('New Ticket'); ?>" />
                                            </div>
                                            <div class="js-tkt-det-user-cnt">
                                                <div class="js-ticket-user-name-wrp">
                                                    <?php echo $this->ticketdetail->name; ?>
                                                </div>
                                                <div class="js-ticket-user-subject-wrp">
                                                    <?php echo $this->ticketdetail->subject; ?>
                                                    <div class="js-ticket-user-email-wrp">
                                                        <?php echo $this->ticketdetail->email; ?>
                                                    </div>
                                                    <div class="js-ticket-user-email-wrp">
                                                        <?php echo $this->ticketdetail->phone; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="js-tkt-go-to-all-wrp">
                                            <a class="js-tkt-go-to-all" href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>">     <?php echo Text::_('Show').' '.Text::_('All Tickets'); ?>
                                            </a>
                                        </div>
                                        <div class="js-tkt-det-tkt-msg">
                                            <?php echo $this->ticketdetail->message; ?>
                                        </div>
                                    <div class="js-ticket-btn-box">
                                        <?php if($this->ticketdetail->status != 5){ ?>
                                        <a class="js-button" href="#" onclick="actioncall('<?php if ($this->ticketdetail->status == 4) echo 8; else echo 3; ?>')">
                                            <?php if ($this->ticketdetail->status == 4){  ?>
                                                <img class="js-button-icon" title="<?php echo Text::_('Reopen Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/reopen.png">
                                                <span><?php echo Text::_('Reopen Ticket'); ?></span>
                                            <?php }else{ ?>
                                                <img class="js-button-icon" title="<?php echo Text::_('Close Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/close.png">
                                                <span><?php echo Text::_('Close Ticket'); ?></span>
                                            <?php } ?>
                                        </a>
                                    <?php } ?>
                                    <!-- Print Ticket -->
                                    <?php $link_print = 'index.php?option=' . $this->option . '&c=ticket&layout=print_ticket&id='.$this->ticketdetail->id.'&tmpl=component&print=1'; ?>
                                        <?php if($this->config['print_ticket_user'] == 1){ ?>
                                        <a class="js-button" id="" href="<?php echo $link_print; ?>" target="_blank">
                                            <img class="js-button-icon" title="<?php echo Text::_('Print Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/print.png">
                                            <span><?php echo Text::_('Print Ticket'); ?></span>
                                        </a>
                                <?php } ?>
                            </div>
                                <?php } ?>
                            <!-- data edit -->
                            </div>
                            <div class="js-ticket-thread-heading">
                                <?php echo Text::_('Ticket Thread'); ?>
                            </div>
                            <div class="js-ticket-thread internal-note"><!-- Left Side Image -->
                                    <div class="js-ticket-user-img-wrp">
                                         <img class="js-ticket-staff-img" src="<?php echo Uri::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                    </div>
                                    <div class="js-ticket-thread-cnt">
                                        <div class="js-ticket-user-name-wrp">
                                            <span><?php echo $this->ticketdetail->name; ?></span>
                                        </div>
                                        <div class="js-ticket-user-email-wrp">
                                            <?php echo $this->ticketdetail->email; ?>
                                        </div>
                                        <div class="js-ticket-user-email-wrp">
                                            <?php echo $this->ticketdetail->message; ?>
                                        </div>
                                        <?php
                                        if (isset($this->attachment) && !empty($this->attachment)) { ?>
                                            <div class="js-ticket-attachments-wrp">
                                                <?php foreach ($this->attachment as $attachment) {
                                                    echo '
                                                        <div class="js_ticketattachment">
                                                            <span class="js-ticket-download-file-title">
                                                                ' . $attachment->filename  . '
                                                            </span>
                                                            <a class="js-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&'. Factory::getSession()->getFormToken() .'=1">
                                                                <img class="js-ticket-download-img" src="components/com_jssupportticket/include/images/download.png">
                                                            </a>
                                                        </div>';
                                                }?>
                                               </div>
                                        <?php
                                        } ?>
                                        <div class="js-ticket-time-stamp-wrp">
                                            <span class="js-ticket-ticket-created-date">
                                                <?php echo HTMLHelper::_('date',$this->ticketdetail->created,"l F d, Y");?>
                                            </span>
                                        </div>
                                    </div>
                            </div>
                             <!--replay a message  -->


                            <?php for ($i = 0, $n = getJSTicketPHPFunctionsClass()->jsticket_count($this->messages); $i < $n; $i++) {
                                $row = & $this->messages[$i]; ?>
                                <div class="js-ticket-detail-box js-ticket-post-reply-box"><!-- Ticket Detail Box -->

                                        <div class="js-ticket-user-img-wrp">
                                            <img class="js-ticket-staff-img" src="<?php echo Uri::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                        </div>
                                        <div class="js-ticket-thread-cnt">
                                            <div class="js-ticket-user-name-wrp">
                                               <?php if($row->name) echo $row->name; else echo $this->ticketdetail->name; ?>
                                            </div>
                                                <!-- Right Side Ticket Data -->
                                            <div class="js-ticket-rows-wrapper">
                                                <div class="js-ticket-user-email-wrp" >
                                                    <div class="js-ticket-row">
                                                        <div class="js-ticket-field-value">
                                                            <?php echo $row->message; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                $count = $row->count;
                                                if ($count >= 1) {
                                                    $outdex = $i + $count;
                                                    echo ' <div class="js-ticket-attachments-wrp">';
                                                                for ($j = $i; $j < $outdex; $j++) {
                                                                    if ($row->filename && $row->filename <> '') {
                                                                        $datadirectory = $this->config['data_directory'];
                                                                        $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$row->attachmentid.'&'.Factory::getSession()->getFormToken().'=1';
                                                                        echo '  <div class="js_ticketattachment">
                                                                                    <span class="js-ticket-download-file-title">'
                                                                                        . $row->filename . "&nbsp(" . getJSTicketPHPFunctionsClass()->jsticket_round($row->filesize, 2) . " KB)";
                                                                                    echo '</span>
                                                                                    <a class="js-download-button" target="_blank" href="' . $path . '">
                                                                                        <img class="js-ticket-download-img" src="components/com_jssupportticket/include/images/download.png">
                                                                                    </a>
                                                                                </div>';

                                                                    }

                                                                };
                                                                echo ' </div>';
                                                    $i = $outdex - 1;
                                                }?>
                                                <div class="js-ticket-time-stamp-wrp">
                                                    <span class="js-ticket-ticket-created-date">
                                                         <?php echo HTMLHelper::_('date',$row->created,"l F d, Y");?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            <?php } ?>

                        <div class="js-ticket-tabs-wrapper">
                            <?php  if ($this->ticketdetail->lock != 1 && $this->ticketdetail->status != 4 && $this->ticketdetail->status != 5) { ?>
                                    <div class="js-ticket-reply-forms-heading"><?php echo Text::_('Reply a Message'); ?></div>
                                    <div class="js-ticket-reply-field-wrp">
                                        <div class="js-ticket-reply-field">
                                            <?php
                                            echo $editor->display('responce', '', '550', '300', '60', '20', false);
                                            ?>
                                            <input type='hidden' id='message-required' name="message-required" value="<?php echo 'required'; ?>">
                                        </div>
                                    </div>
                                    <?php if ($this->isAttachmentPublished) { ?>
                                        <div class="js-attachment-wrp">
                                            <div class="js-form-title"><?php echo Text::_('Attachments'); ?></div>
                                            <div class="js-form-value js-attachment-files-wrp">
                                                <div id="js-attachment-files" class="js-attachment-files">
                                                    <span class="js-attachment-file-box">
                                                        <input type="file" class="inputbox js-attachment-inputbox js-form-input-field-attachment" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                                        <span class='js-attachment-remove'></span>
                                                    </span>
                                                </div>
                                                <div id="js-attachment-option">
                                                    <?php echo Text::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo Text::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?>
                                                </div>
                                                <span id="js-attachment-add"><?php echo Text::_('Add More').' '.Text::_('Attachments'); ?></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="js-ticket-reply-form-button-wrp">
                                        <input  class="js-ticket-save-button" type="submit" value="<?php echo Text::_('Post reply'); ?>"/>
                                    </div>
                            <?php }?>
                        </div>
                            </div>
                                <div class="js-ticket-detail-right"><!-- Right Side Ticket Data -->
                                    <div class="js-ticket-rows-wrp js-tkt-detail-cnt" >
                                        <?php
                                            $color = "#ed1c24;";
                                            if ($this->ticketdetail->lock == 1) {
                                                $color = "#5bb12f;";
                                            } elseif ($this->ticketdetail->status == 0) {
                                                $color = "#5bb12f;";
                                            } elseif ($this->ticketdetail->status == 1) {
                                                $color = "#28abe3;";
                                            } elseif ($this->ticketdetail->status == 2) {
                                                $color = "#ff7f50;";
                                            } elseif ($this->ticketdetail->status == 3) {
                                                $color = "#FFB613;";
                                            } elseif ($this->ticketdetail->status == 4) {
                                                $color = "#ed1c24;";
                                            } elseif ($this->ticketdetail->status == 5) {
                                                $color = "#dc2742;";
                                            }
                                        ?>
                                        <div class="js-tkt-det-status" style="background-color:<?php echo $color;?>;">
                                            <?php
                                            $printstatus = 1;
                                            $ticketmessage = '';
                                            if ($this->ticketdetail->status == 4 || $this->ticketdetail->status == 5 )
                                                $ticketmessage = Text::_('Closed');
                                            elseif ($this->ticketdetail->status == 2)
                                                $ticketmessage = Text::_('In Progress');
                                            else
                                                $ticketmessage = Text::_('Open');
                                            $printstatus = 1;
                                            if ($this->ticketdetail->lock == 1) {
                                                echo '<div class="js-ticket-status-note">' . Text::_('Locked').'</div>';
                                                $printstatus = 0;
                                            }
                                            if ($this->ticketdetail->isoverdue == 1) {
                                                echo '<div class="js-ticket-status-note">' . Text::_('Overdue') . '</div>';
                                                $printstatus = 0;
                                            }
                                            if ($printstatus == 1) {
                                                echo $ticketmessage;
                                            }
                                            ?>
                                        </div>
                                        <div class="js-tkt-det-info-cnt">
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                               <?php echo Text::_('Created'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                                <?php
                                                    $startTimeStamp = getJSTicketPHPFunctionsClass()->jsticket_strtotime($this->ticketdetail->created);
                                                    $endTimeStamp = getJSTicketPHPFunctionsClass()->jsticket_strtotime("now");
                                                    $timeDiff = abs($endTimeStamp - $startTimeStamp);
                                                    $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
                                                    // and you might want to convert to integer
                                                    $numberDays = intval($numberDays);
                                                    if ($numberDays != 0 && $numberDays == 1) {
                                                        $day_text = Text::_('Day');
                                                    } elseif ($numberDays > 1) {
                                                        $day_text = Text::_('Days');
                                                    } elseif ($numberDays == 0) {
                                                        $day_text = Text::_('Today');
                                                    }
                                                ?>
                                                <?php
                                                    if ($numberDays == 0) {
                                                        echo $day_text;
                                                    } else {
                                                        echo $numberDays . ' ' . $day_text . ' ';
                                                        echo Text::_('Ago');
                                                    }
                                                ?>
                                                <?php //echo HTMLHelper::_('date',$this->ticketdetail->created,"d F, Y");
                                                ?>
                                            </div>
                                        </div>

                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                               <?php echo Text::_('Last Reply'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                               <?php if ($this->ticketdetail->lastreply == '' || $this->ticketdetail->lastreply == '0000-00-00 00:00:00') {echo Text::_('No Last Reply'); } else {echo HTMLHelper::_('date',$this->ticketdetail->lastreply,"d F, Y"); } ?>
                                            </div>
                                        </div>
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                                <?php echo Text::_('Department'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                                <?php echo $this->ticketdetail->departmentname; ?>
                                            </div>
                                        </div>
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                               <?php echo Text::_('Ticket ID'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                               <?php echo $this->ticketdetail->ticketid; ?>
                                               <a href="javascript:void(0)" title="Copy" class="js-tkt-det-copy-id" id="ticketidcopybtn" success=<?php echo Text::_("Copied"); ?>><?php echo Text::_('Copy'); ?></a>
                                            </div>
                                        </div>
                                        <?php if($this->ticketdetail->ticketviaemail == 1 && $isstaff){ ?>
                                            <div class="js-ticket-row">
                                                <div class="js-ticket-field-title">
                                                    <?php echo Text::_('Ticket Email'); ?>&nbsp;:
                                                </div>
                                                <div class="js-ticket-field-value">
                                                    <?php echo $this->ticketdetail->departmentname; ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                                <?php echo Text::_('Due Date'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                               <?php if ($this->ticketdetail->duedate == '' || $this->ticketdetail->duedate == '0000-00-00 00:00:00') echo Text::_('JNone'); else echo HTMLHelper::_('date',$this->ticketdetail->duedate,"d F, Y"); ?>
                                            </div>
                                        </div>

                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                                <?php echo Text::_('Status'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                               <?php
                                            if ($this->ticketdetail->lock == 1) {
                                                $msg = Text::_('Lock');
                                            } elseif ($this->ticketdetail->status == 0) {
                                                $msg = Text::_('Open');
                                            } elseif ($this->ticketdetail->status == 1) {
                                                $msg = Text::_('On Waiting');
                                            } elseif ($this->ticketdetail->status == 2) {
                                                $msg = Text::_('In Progress');
                                            } elseif ($this->ticketdetail->status == 3) {
                                                $msg = Text::_('Replied');
                                            } elseif ($this->ticketdetail->status == 4) {
                                                $msg = Text::_('Closed');
                                            } elseif ($this->ticketdetail->status == 5) {
                                                $msg = Text::_('Closed and Merged');
                                            }
                                            ?>
                                            <?php echo $msg; ?>

                                            </div>
                                        </div>
                                        <?php
                                        $customfields = getCustomFieldClass()->userFieldsData(1);
                                        if(!empty($customfields)){ ?>
                                            <div class="js-ticket-row">
                                                <?php
                                                    foreach ($customfields as $field) {
                                                    if($field->userfieldtype != 'termsandconditions'){
                                                        $array =  getCustomFieldClass()->showCustomFields($field, 5, $this->ticketdetail->params , $this->ticketdetail->id);
                                                        if(!empty($array)){ ?>
                                                            <div class="js-ticket-row">
                                                                <div class="js-ticket-field-title">
                                                                    <?php echo Text::_($array['title']); ?>&nbsp;:
                                                                </div>
                                                                <div class="js-ticket-field-value">
                                                                    <?php echo Text::_($array['value']); ?>
                                                                </div>
                                                            </div>

                                                    <?php
                                                        }
							}
                                                    }
                                                ?>
                                            </div>
                                        <?php } ?>
                                        <!-- Status box -->
                                        <?php
                                            if ($this->ticketdetail->lock == 1) {
                                                $color = "#5bb12f;";
                                                $ticketmessage = Text::_('Lock');
                                            } elseif ($this->ticketdetail->status == 0) {
                                                $color = "#5bb12f;";
                                                $ticketmessage = Text::_('Open');
                                            } elseif ($this->ticketdetail->status == 1) {
                                                $color = "#28abe3;";
                                                $ticketmessage = Text::_('On Waiting');
                                            } elseif ($this->ticketdetail->status == 2) {
                                                $color = "#ff7f65;";
                                                $ticketmessage = Text::_('In Progress');
                                            } elseif ($this->ticketdetail->status == 3) {
                                                $color = "#FFB613;";
                                                $ticketmessage = Text::_('Replied');
                                            } elseif ($this->ticketdetail->status == 4) {
                                                $color = "#ed1c24;";
                                                $ticketmessage = Text::_('Closed');
                                            } elseif ($this->ticketdetail->status == 5) {
                                                $color = "#dc2742;";
                                                $ticketmessage = Text::_('Closed and Merged');
                                            }
                                        ?>

                                    </div>

                                </div>

                                <div class="js-ticket-rows-wrp  js-tkt-detail-cnt" >
                                    <div class="js-tkt-det-hdg">
                                        <div class="js-tkt-det-hdg-txt">
                                            <?php echo Text::_('Priority'); ?>
                                        </div>
                                    </div>
                                    <div class="js-ticket-field-value js-ticket-priorty" style="background:<?php echo $this->ticketdetail->prioritycolour; ?>; color:#ffffff;">
                                       <?php echo Text::_($this->ticketdetail->priority); ?>
                                    </div>
                                </div>
                                <!--<?php if($this->ticketdetail->status != 5){ ?>-->

                                </div>
                                <!-- comment start -->
                                <!-- Print Ticket -->

                            </div>
                            <!-- comment end -->
                        </div>
                        <!-- Ticket Post Replay -->

                    </div>

                    <input type="hidden" name="created" value="<?php echo $curdate = date('Y-m-d H:i:s'); ?>" />
                        <input type="hidden" name="view" value="ticket" />
                        <input type="hidden" name="c" value="ticket" />
                        <input type="hidden" name="layout" value="ticketdetail" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="task" value="actionticket" />
                        <input type="hidden" name="check" value="" />
                        <input type="hidden" name="email" value="<?php echo $this->email; ?>" />
                        <input type="hidden" name="callaction" id="callaction" value="" />
                        <input type="hidden" name="callfrom" id="callfrom" value="savemessage" />
                        <input type="hidden" name="isreopen" id="isreopen" value="" />
                        <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                        <input type="hidden" name="lastreply" value="<?php echo $this->ticketdetail->lastreply; ?>" />
                        <input type="hidden" id="ticketid" name="ticketid" value="<?php if (isset($this->ticketdetail)) echo $this->ticketdetail->id; ?>" />
                        <input type="hidden" id="ticketrandomid" name="ticketrandomid" value="<?php if (isset($this->ticketdetail)) echo $this->ticketdetail->ticketid; ?>" />
                    <?php echo HTMLHelper::_('form.token'); ?>
                </form>
                <div id="popup-record-data" style="display:inline-block;width:100%;"></div>
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
            messageslayout::getRecordNotFound(); //No Record
        }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>
    <script language="Javascript">
        jQuery(document).ready(function () {
            jQuery(".cb-enable").click(function () {
                var append_sig = jQuery(this).attr('for');
                var parent = jQuery(this).parents('.switch');
                jQuery('.cb-disable', parent).removeClass('selected');
                if (typeof append_sig !== 'undefined' && append_sig !== null) {
                    if (append_sig == 'appendsignature1') {
                        jQuery('label[data-signature="appendsignature2"]').removeClass('cb-enable').addClass('cb-disable');
                        jQuery('label[data-signature="appendsignature3"]').removeClass('cb-enable').addClass('cb-disable');
                        jQuery(this).addClass('selected');
                    }
                } else {
                    jQuery(this).addClass('selected');
                }

                jQuery('.checkbox', parent).attr('checked', true);
            });

            jQuery(".cb-disable").click(function () {
                var append_sig = jQuery(this).attr('for');
                var parent = jQuery(this).parents('.switch');
                jQuery('.cb-enable', parent).removeClass('selected');
                if (typeof append_sig !== 'undefined' && append_sig !== null) {
                    if ((append_sig == 'appendsignature2') || (append_sig == 'appendsignature3') || (append_sig == 'appendsignature1')) {
                        jQuery(this).removeClass('cb-disable').addClass('cb-enable');
                        jQuery(this).addClass('selected');
                    }
                } else {
                    jQuery(this).addClass('selected');
                }
                jQuery('.checkbox', parent).attr('checked', false);
            });


        }); //end .readyFunction

        jQuery("#js-attachment-add").click(function () {
            var obj = this;
            var current_files = jQuery('input[name="filename[]"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            var append_text = "<span class='js-attachment-file-box'><input name='filename[]' class=' js-attachment-inputbox js-form-input-field-attachment' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
            if (current_files < total_allow) {
                jQuery("#js-attachment-files").append(append_text);
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
                if(tinyMCE.editors.length > 0){
                    is_tinyMCE_active = true;
                }
            }
            return is_tinyMCE_active;
        }
        function myValidate(f) {
            var chk_reopen = jQuery('input#isreopen').val();
            if (document.formvalidator.isValid(f)) {
                if (chk_reopen == "") {
                    var msg_obj = jQuery("#message-required");
                    if (typeof msg_obj !== 'undefined' && msg_obj !== null) {
                        var msg_required_val = jQuery("#message-required").val();
                        if (msg_required_val != '') {
                            if(isTinyMCE()){
                                var message = tinyMCE.get('responce').getContent();
                            }else{
                                var message = jQuery('textarea#responce').val();
                            }
                            if (message == '') {
                                alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
                                if(isTinyMCE()){
                                    tinyMCE.get('responce').focus();
                                }else{
                                    jQuery('textarea#responce').focus();
                                }
                                return false;
                            }
                        }
                    }
                } else if (chk_reopen == 1) {
                    var msg_ro_obj = jQuery("#reopen-message-required");
                    if (typeof msg_ro_obj !== 'undefined' && msg_ro_obj !== null) {
                        var msg_ro_required_val = jQuery("#reopen-message-required").value;
                        if (msg_ro_required_val != '') {
                            if(isTinyMCE()){
                                var message_ro = tinyMCE.get('messages').getContent();
                            }else{
                                var message_ro = jQuery('textarea#messages').val();
                            }
                            if (message_ro == '') {
                                alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
                                if(isTinyMCE()){
                                    tinyMCE.get('messages').focus();
                                }else{
                                    jQuery('textarea#messages').focus();
                                }
                                return false;
                            }
                        }
                    }
                }
                f.check.value = '<?php if (JVERSION < 3) echo JUtility::getToken(); else echo Factory::getSession()->getFormToken(); ?>';//send token
            } else {
                alert("<?php echo Text::_('Some values are not accepatable please retry'); ?>");
                return false;
            }
            return true;
        }
        function actioncall(value) {
            if(value == 3){
                var yesclose = confirm('<?php echo Text::_('Are you sure to close ticket'); ?>');
                if(yesclose != true){
                    return;
                }
            }
            jQuery('#callfrom').val('action');
            jQuery('#callaction').val(value);
            document.adminForm.submit();
        }

        function combo(value) {
            var ele = jQuery('#priorityid');
            if (value == 1) {
                ele.prop('disabled', false);
            } else {
                ele.prop('disabled', true);
            }
        }

        function showhide(layer_ref, state) {
            if (state == 'none') {
                jQuery('#' + layer_ref).hide('slow');
            } else if (state == 'block') {
                jQuery('#' + layer_ref).show('slow');

            }
        }
    </script>
</div>
