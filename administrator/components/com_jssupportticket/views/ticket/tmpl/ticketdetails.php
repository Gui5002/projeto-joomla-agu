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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;

$conf   = Factory::getConfig();
$editor = Editor::getInstance($conf->get('editor'));

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('behavior.formvalidator');
$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/jsst-admin-workspace-v2.css?v=61');
$document->addStyleSheet('components/com_jssupportticket/include/css/jsst-ticket-detail-admin.css?v=63');
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

    // timer reply edit
            function showPopupAndFillValues(id,pfor) {
            jQuery('div.edit-time-popup').hide();
            if(pfor == 1){
                jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getReplyDataByID&<?php echo Factory::getSession()->getFormToken(); ?>=1", {val: id}, function (data) {
                    if (data) {
                        jQuery('div.popup-header-text').html('<?php echo Text::_("Edit Reply");?>');
                        d = jQuery.parseJSON(data);
                        tinyMCE.get('jsticket_replytext').execCommand('mceSetContent', false, d.message);
                        jQuery('div.edit-time-popup').hide();
                        jQuery('form#jsst-time-edit-form').hide();
                        jQuery('form#jsst-note-edit-form').hide();
                        jQuery('form#jsst-reply-form').show();
                        jQuery('form#jsst-reply-form input[name="reply-replyid"]').val(id);
                        jQuery('div.jsst-popup-background').show();
                        jQuery('div.jsst-popup-wrapper').slideDown('slow');
                    }
                });
            }else if(pfor == 2){
                jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTimeByReplyID&<?php echo Factory::getSession()->getFormToken(); ?>=1", {val: id}, function (data) {
                    if (data) {
                        jQuery('div.popup-header-text').html('<?php echo Text::_("Edit Time");?>');
                        d = jQuery.parseJSON(data);
                        jQuery('div.edit-time-popup').hide();
                        jQuery('form#jsst-reply-form').hide();
                        jQuery('form#jsst-note-edit-form').hide();
                        jQuery('div.system-time-div').hide();
                        jQuery('form#jsst-time-edit-form').show();
                        jQuery('form#jsst-time-edit-form input[name="reply-replyid"]').val(id);
                        jQuery('div.jsst-popup-background').show();
                        jQuery('div.jsst-popup-wrapper').slideDown('slow');
                        jQuery('form#jsst-time-edit-form input[name="edited_time"]').val(d.time);
                        tinyMCE.get('edit_reason').execCommand('mceSetContent', false, d.desc);
                        if(d.conflict == 1){
                            jQuery('div.system-time-div').show();
                            jQuery('form#jsst-time-edit-form input[name="time-confilct"]').val(d.conflict);
                            jQuery('form#jsst-time-edit-form input[name="systemtime"]').val(d.systemtime);
                            jQuery('form#jsst-time-edit-form select[name="time-confilct-combo"]').val(0);
                        }
                    }
                });
            }else if(pfor == 3){
                jQuery.post("index.php?option=com_jssupportticket&c=note&task=getTimeByNoteID&<?php echo Factory::getSession()->getFormToken(); ?>=1", {val: id}, function (data) {
                    if (data) {
                        jQuery('div.popup-header-text').html('<?php echo Text::_("Edit Time");?>');
                        d = jQuery.parseJSON(data);
                        jQuery('div.edit-time-popup').hide();
                        jQuery('form#jsst-reply-form').hide();
                        jQuery('form#jsst-note-edit-form').show();
                        jQuery('form#jsst-time-edit-form').hide();
                        jQuery('div.system-time-div').hide();
                        jQuery('input#note-noteid').val(id);
                        jQuery('div.jsst-popup-background').show();
                        jQuery('div.jsst-popup-wrapper').slideDown('slow');
                        jQuery('form#jsst-note-edit-form input[name="edited_time"]').val(d.time);
                        tinyMCE.get('t_desc').execCommand('mceSetContent', false, d.desc);
                        if(d.conflict == 1){
                            jQuery('div.system-time-div').show();
                            jQuery('form#jsst-note-edit-form input[name="time-confilct"]').val(d.conflict);
                            jQuery('form#jsst-note-edit-form input[name="systemtime"]').val(d.systemtime);
                            jQuery('form#jsst-note-edit-form select[name="time-confilct-combo"]').val(0);
                        }
                    }
                });
            }else if(pfor == 4){
                var ticketid = id;
                jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo Factory::getSession()->getFormToken(); ?>=1", {ticketid:ticketid}, function (data) {
                    if (data) {
                        var d = JSON.parse(data);
                        if(d['status'] == 1){
                            jQuery("div#popup-record-data").show();
                            jQuery("div#popup-record-data").html("");
                            jQuery("div#js-history-back").show();
                            jQuery("div#popup-record-data").html(d['data']);
                        }
                    }
                });
            }

             return false;
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
        if (typeof window.jsstInitAdminAttachmentDropzones === 'function') {
            window.jsstInitAdminAttachmentDropzones(document);
        }
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
        jQuery("a#moreactions").click(function(e){
            e.preventDefault();
            jQuery("div#js-tk-actiondiv").slideToggle();
        });

        jQuery("a#requester-showmore").click(function(e){
            e.preventDefault();
            jQuery("a#requester-showmore").find('img').toggleClass('js-hidedetail');
            jQuery("div#req-moredetail").slideToggle();
        });
        // Attachments are handled by the shared admin drag-and-drop controller.
        //History Popup
        jQuery(document).ready(function(){
            jQuery("a#js-tk-history").click(function(){
               jQuery('div#js-history-back').show();
               jQuery('div#js-history-popup').slideDown('slow');

            });
            jQuery('div#js-history-back,.jsst-popup-close,div#js-private-crendentials-back').click(function(){
               jQuery('div#js-history-popup').slideUp('slow');
               jQuery("div#userpopupforchangepriority").slideUp('slow');
               jQuery("div#userpopupforchangedepartment").slideUp('slow');
               jQuery("div#js-private-crendentials-popup").slideUp('slow');
               jQuery("div#userpopupforassignstaff").slideUp('slow');
               jQuery("div#userpopupforintnote").slideUp('slow');
               jQuery("div#popup-record-data").slideUp('slow');
               setTimeout(function () {
                   jQuery('div#js-history-back').hide();
                   jQuery('div#js-private-crendentials-back').hide();
                }, 700);
            });
        });
        jQuery("div.popup-header-close-img,div.jsst-popup-background,input#cancel").click(function (e) {
            jQuery("div.jsst-popup-wrapper").slideUp('slow');
            setTimeout(function () {
                jQuery('div.jsst-popup-background').hide();
            }, 700);
        });
        jQuery(document).delegate("#close-pop", "click", function (e) {
            jQuery("div#mergeticketselection").fadeOut();
            jQuery("div#popup-record-data").html("");
            jQuery('div#js-history-back').hide();
        });
        jQuery(document).delegate("#ticketpopupsearch",'submit', function (e) {
            var ticketid = jQuery("#ticketidformerge").val();
            e.preventDefault();
            var name = jQuery("input#name").val();
            var email = jQuery("input#email").val();
            jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo Factory::getSession()->getFormToken(); ?>=1",{name: name, email: email,ticketid:ticketid}, function (data) {
                var d = JSON.parse(data);
                if (data) {
                    jQuery("div#popup-record-data").html("");
                    jQuery("div#popup-record-data").html(d['data']);
                }
            });//jquery closed
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
<div id="js-history-back" style="display:none"> </div>
    <div id="js-history-popup" style="display:none">
        <div id="js-history-head">
            <span class="js-title"><?php echo Text::_('Ticket History'); ?></span>
            <span class="js-image jsst-popup-close" role="button" tabindex="0" aria-label="<?php echo Text::_('Close'); ?>"><img src="components/com_jssupportticket/include/images/popup-close.png" alt=""></span>
        </div>
        <div class="js-history-messagewrapper"><?php
        foreach ($this->tickethistory as $history) { ?>
            <div id="js-history-row">
                <span class="js-col-xs-12 js-col-md-2 js-data"><?php echo HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($history->datetime),'Y-m-d'); ?></span>
                <span class="js-col-xs-12 js-col-md-2 js-data"><?php echo HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($history->datetime),'H:i:s'); ?></span>
                <?php
                    if ($history->level == 1) //admin
                        $color = "blue";
                    elseif ($history->level == 2) //staff
                        $color = "orange";
                    else  //user
                        $color = "black";
                ?>
                <span class="js-col-xs-12 js-col-md-8 js-data" style="color:<?php echo $color; ?>"><?php echo $history->message; ?></span>
            </div> <?php
        } ?>
        </div>
    </div>
<div id="js-tk-admin-wrapper" class="jsst-ticket-detail-v2 jsst-ticket-detail-admin-v2">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div class="jsst-popup-background" style="display:none;" ></div>
        <div class="jsst-popup-wrapper" style="display:none;" >
            <div class="jsst-popup-header" >
                <div class="popup-header-text" >
                    <?php echo Text::_('Edit Timer')?>
                </div>
                <div class="popup-header-close-img" >
                </div>
            </div>
            <div class="edit-time-popup" style="display:none;" >
                <div class="js-tk-tabs-wrapper-wrapper">
                    <div class="js-title"><?php echo Text::_('Time'); ?>&nbsp;<font color="red">*</font></div>
                    <div class="js-value"><input class="inputbox" type="text" name="edited_time" id="timer-edited-time" size="40" maxlength="255" value="" /></div>
                </div>
                <div class="js-tk-tabs-wrapper-wrapper">
                    <div class="js-title"><?php echo Text::_('Reason For Editing the timer'); ?></div>
                    <div class="js-value">
                        <textarea name="ttt_desc" id="ttt_desc" cols="60" rows="20" style="height: 300px;" >  </textarea>
                    </div>
                </div>
                <div class="js-col-md-12 js-form-button-wrapper">
                    <input type="button" class="button js-button-save" name="ok" onclick="updateTimerFromPopup()" value="<?php echo Text::_('Ok'); ?>" />
                    <input type="button" class="button js-button-cancel" name="cancel"  value="<?php echo Text::_('Cancel'); ?>" />
                </div>
            </div>

            <form id="jsst-reply-form" style="display:none;" method="post" >
                <div class="js-col-md-12 js-form-wrapper">
                    <div class="js-col-md-12 js-form-title"><?php echo Text::_('Reply'); ?></div>
                    <div class="js-col-md-12 js-form-value">
                        <?php
                            echo $editor->display('jsticket_replytext', '', '', '100', '20', '20', false);
                        ?>
                    </div>
                </div>
                <div class="js-col-md-12 js-form-button-wrapper">
                    <input type="submit" class="button js-button-save" name="ok" value="<?php echo Text::_('Save'); ?>" />
                    <input type="button" class="button js-button-cancel" name="cancel" onclick="closePopup()" value="<?php echo Text::_('Cancel'); ?>" />
                </div>
                <input type="hidden" name="reply-replyid" id="reply-edit-replyid" value="" />
                <input type="hidden" name="reply-tikcetid" id="reply-edit-ticketid" value="<?php echo $this->ticketdetail->id; ?>" />
                 <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="layout" value="ticketdetail" />
                <input type="hidden" id="reply-edit-task" name="task" value="saveeditedreply" />
                <input type="hidden" name="c" value="ticket" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
            <?php
            // undefined error for $time_confilct_combo
            $yesno = array(
                            '0' => array('value' => '1','text' => Text::_('JYES')),
                            '1' => array('value' => '0','text' => Text::_('JNO'))
                        );
                 $time_confilct_combo = HTMLHelper::_('select.genericList', $yesno, 'time-confilct-combo', 'class="inputbox" ' . '', 'value', 'text', '');
            ?>
            <form id="jsst-time-edit-form" style="display:none" method="post" >
                <div class="js-tk-tabs-wrapper">
                    <div class="js-title"><?php echo Text::_('Time'); ?></div>
                    <div class="js-value"><input class="inputbox" type="text" name="edited_time" id="reply-edited-time" size="40" maxlength="255" value="" /></div>
                </div>
                <div class="js-tk-tabs-wrapper system-time-div" style="display:none;" >
                    <div class="js-col-md-12 js-title"><?php echo Text::_('System Time'); ?></div>
                    <div class="js-col-md-12 js-value"><input class="inputbox" type="text" name="systemtime" id="reply-systemtime" size="40" maxlength="255" value="" disabled="disabled" /></div>
                </div>
                <div class="js-tk-tabs-wrapper">
                    <div class="js-title"><?php echo Text::_('Reason For Editing'); ?></div>
                    <div class="js-value">
                            <?php
                                echo $editor->display('edit_reason', '', '', '100', '20', '20', false);
                            ?>
                    </div>
                </div>
                <div class="js-tk-tabs-wrapper system-time-div" style="display:none;" >
                    <div class="js-title"><?php echo Text::_('Resolve conflict'); ?></div>
                    <div class="js-value"><?php echo $time_confilct_combo; ?></div>
                </div>
                <div class="js-col-md-12 js-form-button-wrapper">
                    <input type="submit" class="button js-button-save" name="ok" value="<?php echo Text::_('Save'); ?>" />
                    <input type="button" class="button js-button-cancel" name="cancel" onclick="closePopup()" value="<?php echo Text::_('Cancel'); ?>" />
                </div>
                <input type="hidden" name="reply-replyid" id="time-edit-replyid" value="" />
                <input type="hidden" name="reply-tikcetid" id="time-edit-ticketid" value="<?php echo $this->ticketdetail->id; ?>" />
                <input type="hidden" name="time-confilct" id="reply-time-conflict" value="" />
                
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="layout" value="ticketdetail" />
                <input type="hidden" id="time-edit-task" name="task" value="saveeditedtime" />
                <input type="hidden" name="c" value="ticket" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
            <form id="jsst-note-edit-form" style="display:none" method="post" >
                <div class="js-tk-tabs-wrapper">
                    <div class="js-title"><?php echo Text::_('Time'); ?></div>
                    <div class="js-value"><input class="inputbox" type="text" name="edited_time" id="note-edited-time" size="40" maxlength="255" value="" /></div>
                </div>
                <div class="js-tk-tabs-wrapper system-time-div" style="display:none;" >
                    <div class="js-title"><?php echo Text::_('System Time'); ?></div>
                    <div class="js-value"><input class="inputbox" type="text" name="systemtime" id="note-systemtime" size="40" maxlength="255" value="" disabled="disabled" /></div>
                </div>
                <div class="js-tk-tabs-wrapper">
                    <div class="js-title"><?php echo Text::_('Reason For Editing'); ?></div>
                    <div class="js-value">
                        <?php
                            echo $editor->display('t_desc', '', '', '100', '20', '20', false);
                        ?>
                    </div>
                </div>
                <div class="js-tk-tabs-wrapper system-time-div" style="display:none;" >
                    <div class="js-title"><?php echo Text::_('Resolve conflict'); ?></div>
                    <div class="js-value"><?php echo $time_confilct_combo; ?></div>
                </div>
                <div class="js-col-md-12 js-form-button-wrapper">
                    <input type="submit" class="button js-button-save" name="ok" value="<?php echo Text::_('Save'); ?>" />
                    <input type="button" class="button js-button-cancel" name="cancel" onclick="closePopup()" value="<?php echo Text::_('Cancel'); ?>" />
                </div>
                <input type="hidden" name="note-tikcetid" id="note-tikcetid" value="<?php echo $this->ticketdetail->id; ?>" />
                <input type="hidden" name="note-noteid" id="note-noteid" value="" />
                <input type="hidden" name="time-confilct" id="note-time-conflict" value="" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="layout" value="ticketdetail" />
                <input type="hidden" id="note-edit-task" name="task" value="saveeditedtimenote" />
                <input type="hidden" name="c" value="ticket" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
        </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitleRaw = $this->ticketdetail->subject;
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label' => 'Tickets', 'link' => 'index.php?option=com_jssupportticket&c=ticket&layout=tickets'),
    array('label' => 'Ticket Detail', 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <!-- Container-query scope. It wraps the page content but not the popups below,
                 so the layout containment container-type implies cannot capture them. -->
            <div class="jsst-cq-scope">
            <div id="js-tk-ticket-detail">
                <div class="js-tkt-det-left">
                    <div class="js-tkt-det-cnt js-tkt-det-info-wrp">
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
                                <?php echo Text::_('View All Tickets By').' '; ?>
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
                                <?php // Sibling of the close/reopen link, not nested inside it: an <a> cannot
                                      // contain another <a>, and while it sat in the "else" branch the history
                                      // button only existed for closed tickets. ?>
                                <a class="js-detal-alinks" id="js-tk-history" href="#">
                                    <img title="<?php echo Text::_('Ticket History'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/history.png">
                                    <span>
                                        <?php echo Text::_('Ticket History'); ?>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="js-tk-subheading">
                        <?php echo Text::_('Ticket Thread'); ?>
                    </div>
                    <div class="js-ticket-threads">
                        <div class="js-tk-pic">
                            <img src="<?php echo Uri::root(); ?>components/com_jssupportticket/include/images/user.png" />
                        </div>
                        <div class="js-tk-message">
                            <div class="js-ticket-thread-data">
                                <span class="js-ticket-thread-person">
                                    <?php echo $this->ticketdetail->name; ?>
                                </span>
                            </div>
                            <div class="js-ticket-thread-data">
                                <span class="js-ticket-thread-email">
                                    <?php echo $this->ticketdetail->email; ?>                
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
                    foreach ($this->ticketreplies as $row) { ?>
                        <div class="js-ticket-threads">
                            <div class="js-tk-pic">
                                <?php // This edition has no staff module and the replies query selects only
                                      // replies.*, so staffphoto/staffid are not columns here. Reading them
                                      // raised an undefined-property warning that printed inside this box.
                                      // Every reply therefore uses the default avatar. ?>
                                <img src="<?php echo Uri::root(); ?>components/com_jssupportticket/include/images/user.png" alt="" />
                            </div>
                            <div class="js-tk-message">
                                <div class="js-ticket-thread-data">
                                    <span class="js-ticket-thread-person">
                                        <?php echo $row->name; ?>
                                    </span>
                                </div>
                                <?php $message = $row->message;
                                if($row->mergemessage == 1){
                                    $message = getJSTicketPHPFunctionsClass()->jsticket_str_replace("id=","cid[]=",$message);
                                    $message = getJSTicketPHPFunctionsClass()->jsticket_str_replace("layout=ticketdetail","layout=ticketdetails",$message);
                                } ?>
                                <div class="js-ticket-thread-data note-msg">
                                    <?php echo html_entity_decode($message); ?>
                                    <?php
                                    if (isset($row->attachments)) {
                                        echo '<div class="js_ticketattachment_wrp">';
                                            foreach ($row->attachments as $attachment) {
                                                echo '<div class="js_ticketattachment">';
                                                    $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&' . Factory::getSession()->getFormToken() . '=1';
                                                    echo "<img src='components/com_jssupportticket/include/images/clip.png'><a target='_blank' href=" . $path . ">"
                                                    . $attachment->filename . "&nbsp(" . getJSTicketPHPFunctionsClass()->jsticket_round($attachment->filesize, 2) . " KB)" . "</a>";
                                                echo "</div>";
                                            }
                                            echo '</div>';
                                    } ?>
                                </div>
                                <div class="js-ticket-thread-cnt-btm">
                                    <span class="js-ticket-thread-time">
                                        <?php $replyby = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($row->created),"l F d, Y, H:i:s"); echo ' ( '. $replyby.' )'; ?>
                                    </span>
                                </div>
                                
                            </div>
                        </div>
                    <?php
                    } ?>
                    <?php if($this->ticketdetail->status != 5){ ?>
                        <div id="button">
                            <div class="js-tk-subheading js-margin-bottom">
                                <?php echo Text::_('Post reply'); ?>
                            </div>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-title"><?php echo Text::_('Premade'); ?>:&nbsp;</div>
                                 <div class="js-value"><?php echo $this->lists['premade']; ?></div>
                            </div>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-ticket-detail-append-signature-xs">
                                    <input class="floatnone" type="checkbox" name="append" id ="append" checked="checked"/> <?php echo Text::_('Append'); ?>
                                </div>
                            </div>
                            <div class="jsst-admin-response-block js-mg-bottom">
                                <div class="jsst-admin-response-label">
                                    <?php echo Text::_('Response'); ?>:&nbsp;<font color="red">*</font>
                                </div>
                                <div class="jsst-admin-response-editor-shell">
                                    <?php
                                        echo '<div class="jsst-admin-response-editor">';
                                        echo $editor->display('responce', '', '100%', '330', '100', '24', false);
                                        echo '</div>';
                                    ?>
                                </div>
                            </div>
                            <script>
                                (function(){
                                    function jsstMakeAdminResponseFrameLight(frame){
                                        if(!frame){ return; }
                                        try {
                                            if(frame.tagName && frame.tagName.toLowerCase() === 'textarea'){
                                                frame.style.background = '#ffffff';
                                                frame.style.color = '#111827';
                                                return;
                                            }
                                            var doc = frame.contentDocument || (frame.contentWindow ? frame.contentWindow.document : null);
                                            if(!doc || !doc.body){ return; }
                                            doc.documentElement.style.background = '#ffffff';
                                            doc.body.style.background = '#ffffff';
                                            doc.body.style.color = '#111827';
                                            doc.body.style.fontFamily = 'Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
                                            doc.body.style.fontSize = '14px';
                                            doc.body.style.lineHeight = '1.55';
                                            doc.body.style.padding = '14px';
                                            doc.body.style.margin = '0';
                                            doc.body.style.boxSizing = 'border-box';
                                            var style = doc.getElementById('jsst-admin-response-editor-light-style');
                                            if(!style){
                                                style = doc.createElement('style');
                                                style.id = 'jsst-admin-response-editor-light-style';
                                                style.appendChild(doc.createTextNode('html,body{background:#fff!important;color:#111827!important;} body,p,div,span,li,td,th{color:#111827!important;} a{color:#2563eb!important;}'));
                                                doc.head.appendChild(style);
                                            }
                                        } catch(e) {}
                                    }

                                    function jsstResizeAdminResponseEditor(){
                                        var shell = document.querySelector('.jsst-ticket-detail-admin-v2 #button .jsst-admin-response-editor-shell');
                                        var editorWrap = document.querySelector('.jsst-ticket-detail-admin-v2 #button .jsst-admin-response-editor');
                                        if(!shell || !editorWrap){ return; }

                                        var availableWidth = Math.max(260, shell.clientWidth || editorWrap.clientWidth || 0);
                                        var editorHeight = window.innerWidth <= 760 ? 230 : 330;

                                        var nodes = editorWrap.querySelectorAll('table, tbody, tr, td, iframe, textarea, .mce-tinymce, .mce-container, .mce-container-body, .mce-panel, .mce-stack-layout, .mce-edit-area, .tox, .tox-tinymce, .tox-editor-container, .tox-edit-area, .js-editor-tinymce, .joomla-editor, .editor');
                                        for(var i = 0; i < nodes.length; i++){
                                            nodes[i].style.width = '100%';
                                            nodes[i].style.maxWidth = '100%';
                                            nodes[i].style.minWidth = '0';
                                            nodes[i].style.boxSizing = 'border-box';
                                        }

                                        var topEditors = editorWrap.querySelectorAll('#responce_parent, #responce_tbl, .mce-tinymce, .tox-tinymce');
                                        for(var t = 0; t < topEditors.length; t++){
                                            topEditors[t].style.width = availableWidth + 'px';
                                            topEditors[t].style.maxWidth = '100%';
                                        }

                                        var frames = editorWrap.querySelectorAll('iframe, textarea#responce');
                                        for(var j = 0; j < frames.length; j++){
                                            frames[j].style.width = '100%';
                                            frames[j].style.minHeight = editorHeight + 'px';
                                            jsstMakeAdminResponseFrameLight(frames[j]);
                                        }

                                        if(window.tinyMCE && window.tinyMCE.get){
                                            var ed = window.tinyMCE.get('responce');
                                            if(ed){
                                                try {
                                                    var container = ed.getContainer ? ed.getContainer() : null;
                                                    if(container){
                                                        container.style.width = availableWidth + 'px';
                                                        container.style.maxWidth = '100%';
                                                        container.style.boxSizing = 'border-box';
                                                    }
                                                } catch(e) {}
                                                if(ed.theme && ed.theme.resizeTo){
                                                    try { ed.theme.resizeTo(availableWidth, editorHeight); } catch(e) {}
                                                }
                                                try {
                                                    if(ed.iframeElement){ jsstMakeAdminResponseFrameLight(ed.iframeElement); }
                                                } catch(e) {}
                                            }
                                        }
                                    }
                                    if(document.readyState === 'loading'){
                                        document.addEventListener('DOMContentLoaded', jsstResizeAdminResponseEditor);
                                    }else{
                                        jsstResizeAdminResponseEditor();
                                    }
                                    window.addEventListener('resize', jsstResizeAdminResponseEditor);
                                    window.addEventListener('load', jsstResizeAdminResponseEditor);
                                    setTimeout(jsstResizeAdminResponseEditor, 200);
                                    setTimeout(jsstResizeAdminResponseEditor, 700);
                                    setTimeout(jsstResizeAdminResponseEditor, 1400);
                                    setTimeout(jsstResizeAdminResponseEditor, 2600);
                                })();
                            </script>
                            <?php if ($this->isAttachmentPublished) { ?>
                                <div class="js-tk-tabs-wrapper js-mg-bottom">
                                    <div class="js-title"><?php echo Text::_('Attachments'); ?>:&nbsp;</div>
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
                                            <span class="js-value-text jsst-attachment-input-row">
                                                <input type="file" class="inputbox" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlength="30"/>
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
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-title"><?php echo Text::_('Ticket Status'); ?>:&nbsp;</div>
                                <div class="js-value">
                                    <div class="jsst-formfield-radio-button-wrp">
                                        <input type="checkbox" name="replystatus" id ="replystatus" value="4"/>
                                        <?php echo Text::_('Close On Reply'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input  class="button setfloatoverride" type="button" onclick="validate_form_department(document.adminForm)" value="<?php echo Text::_('Post reply'); ?>"/></div></div>
                        </div> <!--  end div id=button -->
                    <?php } ?>
                </div><?php /* </div> */ ?>
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
                                <span class="js-title"><?php echo Text::_('Help Topic'); ?>&nbsp;:</span>
                                <span class="js-value"><?php echo Text::_($this->ticketdetail->helptopic); ?></span>
                            </div>
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo Text::_('Ticket Id'); ?>&nbsp;:</span>
                                <span class="js-value"><?php echo $this->ticketdetail->ticketid; ?>
                                    <a href="#" title="Copy" class="js-tkt-det-copy-id" id="ticketidcopybtn" data-ticket-hash-id = "<?php echo $this->ticketdetail->ticketid; ?>" success=<?php echo Text::_('Copied'); ?>><?php echo Text::_('Copy'); ?></a>
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
                            <a title="<?php echo htmlspecialchars(Text::_('Change'), ENT_QUOTES, 'UTF-8'); ?>" href="#" class="js-tkt-det-hdg-btn" id="chng-prority">
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
                                    <?php echo $this->ticketdetail->name . "’s " . Text::_('Tickets'); ?> 
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
                                                    <a title="<?php echo htmlspecialchars(Text::_('View Ticket'), ENT_QUOTES, 'UTF-8'); ?>" href="<?php echo 'index.php?option=' . $this->option . '&c=ticket&layout=ticketdetails&cid[]='.$userticket->id; ?>">
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
            </div><!-- /.jsst-cq-scope -->
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
            <input type="hidden" name="ticketid" value="<?php echo $this->ticketdetail->ticketid; ?>" />
            <input type="hidden" name="hash" value="<?php echo $this->ticketdetail->hash; ?>" />
            <input type="hidden" name="email" value="<?php echo $this->ticketdetail->email; ?>" />
            <input type="hidden" name="lastreply" value="<?php echo $this->ticketdetail->lastreply; ?>" />

            <input type="hidden" name="callaction" id="callaction" value="" />
            <input type="hidden" name="callfrom" id="callfrom" value="" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="c" value="ticket" />
            <input type="hidden" name="layout" value="tickets" />
            <input type="hidden" id="task" name="task" value="actionticket" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="created" value="<?php echo date('Y-m-d H:i:s'); ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>

    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>


<script type="text/javascript">
    function isTinyMCE(){
        is_tinyMCE_active = false;
        if (typeof(tinyMCE) != "undefined") {
            //if(tinyMCE.editors.length > 0){ //this line generate error
                is_tinyMCE_active = true;
            //}
        }
        return is_tinyMCE_active;
    }
    // Shared drag-and-drop attachment helpers are loaded from
    // include/js/jsst-admin-attachments.js.

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
    function getpremade(src, val, append) {
        var link = 'index.php?option=com_jssupportticket&c=ticket&task=getpremadeforinternalnote&<?php echo Factory::getSession()->getFormToken(); ?>=1';
        jQuery.post(link,{val:val},function(data){
            if(data){
                if (append == true) {
                    if(isTinyMCE()){
                        var content = tinyMCE.get('responce').getContent();                        
                    }else{
                        var content = jQuery('textarea#responce').val();
                    }
                    content = content + data;
                    if(isTinyMCE()){
                        tinyMCE.get('responce').execCommand('mceSetContent', false, content);                        
                    }else{
                        jQuery('textarea#responce').val(content);
                    }
                } else {
                    if(isTinyMCE()){
                        tinyMCE.get('responce').execCommand('mceSetContent', false, data);                        
                    }else{
                        jQuery('textarea#responce').val(data);
                    }
                }
            }
        });
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
            alert("<?php echo Text::_('Please toggle editor', true); ?>");
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
        if (confirm("<?php echo Text::_('Are you sure delete'); ?>")) {

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


<script type="text/javascript" id="jsst-admin-ticket-detail-mobile-order-v199">
(function(){
    function textOf(node){ return (node && node.textContent ? node.textContent : '').replace(/\s+/g,' ').trim().toLowerCase(); }
    function insertAfter(ref, node){ if(ref && ref.parentNode && node){ ref.parentNode.insertBefore(node, ref.nextSibling); } }
    function wrapNodes(nodes, className){
        nodes = (nodes || []).filter(Boolean);
        if(!nodes.length){ return null; }
        if(nodes[0].parentNode && nodes[0].parentNode.classList && nodes[0].parentNode.classList.contains(className)){ return nodes[0].parentNode; }
        var first = nodes[0], parent = first.parentNode;
        if(!parent){ return null; }
        var wrap = document.createElement('div');
        wrap.className = className;
        parent.insertBefore(wrap, first);
        nodes.forEach(function(node){ wrap.appendChild(node); });
        return wrap;
    }
    function collectFromHeadingUntil(heading, stopNodes){
        var nodes = [];
        var current = heading;
        stopNodes = stopNodes || [];
        while(current){
            var next = current.nextElementSibling;
            nodes.push(current);
            if(!next || stopNodes.indexOf(next) !== -1){ break; }
            current = next;
        }
        return nodes;
    }
    function arrangeMobileTicketDetail(){
        if(window.innerWidth > 900){ return; }
        var root = document.querySelector('#js-tk-admin-wrapper.jsst-ticket-detail-admin-v2');
        if(!root || root.getAttribute('data-jsst-mobile-detail-arranged') === '1'){ return; }
        var left = root.querySelector('.js-tkt-det-left');
        var right = root.querySelector('.js-tkt-det-right');
        if(!left){ return; }
        root.setAttribute('data-jsst-mobile-detail-arranged','1');

        var menu = root.querySelector(':scope > #js-tk-leftmenu');
        if(menu){
            menu.style.display = 'block';
            menu.style.visibility = 'visible';
            menu.style.opacity = '1';
            menu.style.width = '100%';
            menu.style.maxWidth = '100%';
            menu.style.position = 'relative';
        }

        var headings = Array.prototype.slice.call(left.children).filter(function(el){ return el.classList && el.classList.contains('js-tk-subheading'); });
        var internalHeading = null, timelineHeading = null, threadHeading = null;
        headings.forEach(function(h){
            var t = textOf(h);
            if(t.indexOf('internal') !== -1){ internalHeading = h; }
            if(t.indexOf('modern timeline') !== -1 || h.classList.contains('jsst-ticket-feature-heading')){ timelineHeading = h; }
            if(t.indexOf('ticket thread') !== -1){ threadHeading = h; }
        });

        var postReply = left.querySelector('#button');
        var info = left.querySelector('.js-tkt-det-info-wrp');
        var actions = info ? info.querySelector('.js-tkt-det-actn-btn-wrp') : null;

        var essentialBlock = null;
        if(right){
            var statusCard = right.querySelector('.js-tkt-det-tkt-info');
            var priorityCard = right.querySelector('.js-tkt-det-prty');
            if(statusCard || priorityCard){
                essentialBlock = document.createElement('div');
                essentialBlock.className = 'jsst-admin-mobile-essential-block';
                var essentialTitle = document.createElement('div');
                essentialTitle.className = 'js-tk-subheading';
                essentialTitle.appendChild(document.createTextNode('Ticket Status'));
                essentialBlock.appendChild(essentialTitle);
                if(statusCard){ essentialBlock.appendChild(statusCard); }
                if(priorityCard){ essentialBlock.appendChild(priorityCard); }
                if(info){ insertAfter(info, essentialBlock); }
                else { left.insertBefore(essentialBlock, left.firstChild); }
            }
        }

        var actionBlock = null;
        if(actions){
            actionBlock = document.createElement('div');
            actionBlock.className = 'jsst-admin-mobile-actions-block';
            var actionTitle = document.createElement('div');
            actionTitle.className = 'js-tk-subheading';
            actionTitle.appendChild(document.createTextNode('Ticket Actions'));
            actionBlock.appendChild(actionTitle);
            actionBlock.appendChild(actions);
            if(essentialBlock){ insertAfter(essentialBlock, actionBlock); }
            else if(info){ insertAfter(info, actionBlock); }
            else { left.insertBefore(actionBlock, left.firstChild); }
        }

        if(threadHeading){
            var stopNodes = [];
            if(postReply){ stopNodes.push(postReply); }
            if(internalHeading){ stopNodes.push(internalHeading); }
            if(timelineHeading){ stopNodes.push(timelineHeading); }
            var threadNodes = collectFromHeadingUntil(threadHeading, stopNodes);
            var threadBlock = wrapNodes(threadNodes, 'jsst-admin-mobile-thread-block');
            if(actionBlock && threadBlock){ insertAfter(actionBlock, threadBlock); }
            else if(essentialBlock && threadBlock){ insertAfter(essentialBlock, threadBlock); }
            else if(info && threadBlock){ insertAfter(info, threadBlock); }
        }

        if(internalHeading && !(internalHeading.parentNode && internalHeading.parentNode.classList.contains('jsst-admin-mobile-internal-block'))){
            var internalNodes = [];
            var current = internalHeading;
            while(current){
                var next = current.nextElementSibling;
                internalNodes.push(current);
                if(!next || next === timelineHeading || next === postReply){ break; }
                current = next;
            }
            var internalBlock = wrapNodes(internalNodes, 'jsst-admin-mobile-internal-block');
            if(postReply && internalBlock){ insertAfter(postReply, internalBlock); }
        }

        if(timelineHeading && !(timelineHeading.parentNode && timelineHeading.parentNode.classList.contains('jsst-admin-mobile-timeline-block'))){
            var timeline = left.querySelector('.jsst-ticket-feature-timeline');
            if(timeline){
                var tBlock = document.createElement('div');
                tBlock.className = 'jsst-admin-mobile-timeline-block';
                left.insertBefore(tBlock, timelineHeading);
                tBlock.appendChild(timelineHeading);
                tBlock.appendChild(timeline);
                var internalBlockExisting = left.querySelector('.jsst-admin-mobile-internal-block');
                if(internalBlockExisting){ insertAfter(internalBlockExisting, tBlock); }
                else if(postReply){ insertAfter(postReply, tBlock); }
            }
        }
    }
    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', arrangeMobileTicketDetail);
    }else{
        arrangeMobileTicketDetail();
    }
    window.addEventListener('load', arrangeMobileTicketDetail);
    setTimeout(arrangeMobileTicketDetail, 300);
    setTimeout(arrangeMobileTicketDetail, 900);
})();
</script>

<script type="text/javascript" id="jsst-ticket-detail-mobile-v200-align">
(function(){
    function alignTicketDetailMobile(){
        if(window.innerWidth > 900){ return; }
        var root = document.querySelector('#js-tk-admin-wrapper.jsst-ticket-detail-admin-v2');
        if(!root){ return; }
        var area = root.querySelector(':scope > #js-tk-cparea') || root.querySelector('#js-tk-cparea');
        root.style.setProperty('padding-left', window.innerWidth <= 420 ? '6px' : '8px', 'important');
        root.style.setProperty('padding-right', window.innerWidth <= 420 ? '6px' : '8px', 'important');
        root.style.setProperty('margin-left', '0', 'important');
        root.style.setProperty('margin-right', '0', 'important');
        root.style.setProperty('width', '100%', 'important');
        root.style.setProperty('max-width', '100%', 'important');
        if(area){
            area.style.setProperty('padding', '0', 'important');
            area.style.setProperty('padding-left', '0', 'important');
            area.style.setProperty('padding-right', '0', 'important');
            area.style.setProperty('margin-left', '0', 'important');
            area.style.setProperty('margin-right', '0', 'important');
            area.style.setProperty('left', 'auto', 'important');
            area.style.setProperty('transform', 'none', 'important');
            area.style.setProperty('width', '100%', 'important');
            area.style.setProperty('max-width', '100%', 'important');
        }
    }
    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', alignTicketDetailMobile);
    }else{
        alignTicketDetailMobile();
    }
    window.addEventListener('load', alignTicketDetailMobile);
    window.addEventListener('resize', alignTicketDetailMobile);
    setTimeout(alignTicketDetailMobile, 100);
    setTimeout(alignTicketDetailMobile, 500);
    setTimeout(alignTicketDetailMobile, 1200);
})();
</script>
