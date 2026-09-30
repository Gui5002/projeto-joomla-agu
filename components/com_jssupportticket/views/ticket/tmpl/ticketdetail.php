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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;

$conf   = Factory::getConfig();
$editor = Editor::getInstance($conf->get('editor'));
Text::script('Error file size too large');
Text::script('Error file extension mismatch');
$document = Factory::getDocument();
$jsstTicketFeatures = isset($this->ticketFeatures) && is_array($this->ticketFeatures) ? $this->ticketFeatures : array();
$jsstTicketTags = isset($jsstTicketFeatures['tags']) && is_array($jsstTicketFeatures['tags']) ? $jsstTicketFeatures['tags'] : array();
$jsstAvailableTags = isset($jsstTicketFeatures['available_tags']) && is_array($jsstTicketFeatures['available_tags']) ? $jsstTicketFeatures['available_tags'] : array();
$jsstTicketWatchers = isset($jsstTicketFeatures['watchers']) && is_array($jsstTicketFeatures['watchers']) ? $jsstTicketFeatures['watchers'] : array();
$jsstTicketTimeline = isset($jsstTicketFeatures['timeline']) && is_array($jsstTicketFeatures['timeline']) ? $jsstTicketFeatures['timeline'] : array();
$jsstTicketSla = isset($jsstTicketFeatures['sla']) && is_array($jsstTicketFeatures['sla']) ? $jsstTicketFeatures['sla'] : array();
$jsstMatchedSla = isset($jsstTicketFeatures['matched_sla']) ? $jsstTicketFeatures['matched_sla'] : null;
$jsstFeatureToken = Factory::getSession()->getFormToken();
if (!function_exists('jsstFrontendTicketPermissionAllowed')) {
    function jsstFrontendTicketPermissionAllowed($permissions, $permission) {
        return is_array($permissions) && isset($permissions[$permission]) && (int) $permissions[$permission] === 1;
    }
}
if (!function_exists('jsstFrontendFeatureEsc')) {
    function jsstFrontendFeatureEsc($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('jsstFrontendFeatureDate')) {
    function jsstFrontendFeatureDate($value, $format = 'Y-m-d H:i:s') {
        if (!$value || $value === '0000-00-00 00:00:00') {
            return '—';
        }
        try {
            return HTMLHelper::_('date', $value, $format);
        } catch (Throwable $e) {
            return jsstFrontendFeatureEsc($value);
        }
    }
}
?>
<div class="js-row js-null-margin jsst-ticket-detail-page">
<?php
if(isset($this->perm_not_allowed) && $this->perm_not_allowed == 2 ){
    messageslayout::getPermissionNotAllow(); //permission not granted
}elseif(isset($this->perm_not_allowed) && $this->perm_not_allowed == 3 ){
    messageslayout::getUserGuest($this->layoutname,$this->Itemid); //visitor trying to view ticket that belongs to logged in user.
}elseif(isset($this->perm_not_allowed) && $this->perm_not_allowed == 4 ){
    messageslayout::getUserNotAllowedToViewTicket(); //permission not granted
}elseif(!isset($this->ticketdetail) || !is_object($this->ticketdetail)){
    messageslayout::getUserNotAllowedToViewTicket(); //permission not granted
}else{

/*
 * Ticket fields the current viewer may see, keyed by the `field` column
 * ('department', 'helptopic', 'duedate', ...). The model already filters this
 * list by published / isvisitorpublished, so presence in the map is the whole
 * test.
 *
 * jsstTicketFieldVisible() defaults to TRUE when the list is empty. An empty
 * list means "the model did not supply one", not "hide everything" - failing
 * open keeps every existing sidebar row rendering exactly as before on any
 * code path that does not populate index 8.
 */
$jsstVisibleTicketFields = array();
if (isset($this->fieldsordering) && is_array($this->fieldsordering)) {
    foreach ($this->fieldsordering as $jsstField) {
        if (isset($jsstField->field) && $jsstField->field !== '') {
            $jsstVisibleTicketFields[strtolower($jsstField->field)] = true;
        }
    }
}

if (!function_exists('jsstTicketFieldVisible')) {
    function jsstTicketFieldVisible($map, $field) {
        if (empty($map)) {
            return true;
        }
        return isset($map[strtolower($field)]);
    }
}

$per_viewticket = true;
$isstaffdisable = true;
$per_ticketmerge = false;
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = Factory::getDocument();
    $language = Factory::getLanguage();
            if($this->ticketdetail){
                $document = Factory::getDocument();
                $document->addScript('administrator/components/com_jssupportticket/include/js/jquery_idTabs.js');
                $document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
                // $document->addScript('components/com_jssupportticket/include/js/timer.jquery.js');
                Text::script('JS_ERROR_FILE_SIZE_TO_LARGE');
                Text::script('JS_ERROR_FILE_EXT_MISMATCH'); ?>

                <script language="javascript">
                    function validate_form(f) {
                        if (document.formvalidator.isValid(f)) {
                            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo Factory::getSession()->getFormToken(); ?>';//send token
                        }
                        else {
                            alert("<?php echo Text::_('Some values are not valid. Please review the form and try again.'); ?>");
                            return false;
                        }
                        return true;
                    }
                    function confirmdelete(deletefor) {
                        msg = '';
                        if(deletefor == 0){
                            msg = "<?php echo Text::_('Are you sure you want to delete this item?'); ?>";
                        }else if(deletefor == 1){
                            msg = "<?php echo Text::_('Are you sure you want to permanently delete this item?'); ?>";
                        }

                        if (confirm(msg) == true) {
                            return true;
                        } else
                            return false;
                    }

                </script>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        jQuery.noConflict();

                        jQuery("div.popup-header-close-img,div.jsst-popup-background,.jsst-popup-cancel").click(function (e) {
                            jQuery("div.jsst-popup-wrapper").slideUp('slow');
                            jQuery("div.jsst-merge-popup-wrapper").slideUp('slow');
                            setTimeout(function () {
                                jQuery('div.jsst-popup-background').hide();
                            }, 700);
                        });


                        jQuery(document).delegate("#ticketidcopybtn", "click", function(){
                            var temp = jQuery("<input>");
                            jQuery("body").append(temp);
                            temp.val(jQuery("#ticketid").val()).select();
                            document.execCommand("copy");
                            temp.remove();
                            jQuery("#ticketidcopybtn").text(jQuery("#ticketidcopybtn").attr('success'));
                        });
                    });


                    // ////////////////////////////////////////////////////////////////////////////
                    //more actions
                    jQuery(document).ready(function() {
                        jQuery('a[href="#"]').click( function(e) {
                            e.preventDefault();
                        });
                        //more actions
                        jQuery("a#jstkmoreactions").click(function(e){
                            jQuery("div#tk-more-actions").slideToggle();
                        });
                        //more detail
                        jQuery("a#tk-show-moredetail").click(function(e){
                            jQuery("div#tk-moredetail-data").slideToggle();
                            jQuery("img.js-showdetail").toggleClass("js-hidedetail");
                        });
                        //History Popup
                        jQuery("a#jstkhistory").click(function(e){
                            jQuery('div#js-history-back').show();
                            jQuery('div#js-history-popup').slideDown('slow');
                        });
                        jQuery('div#js-history-back,span.jsst-dialog-close,span.close-history').click(function(){
                           jQuery('div#js-history-popup').slideUp('slow');
                           setTimeout(function () {
                               jQuery('div#js-history-back').hide();
                            }, 700);
                        });
                    });

                    function closePopup(){
                        setTimeout(function () {
                            jQuery('div.jsst-popup-background,div#js-history-popup').hide();
                            jQuery('div#js-history-back').hide();
                            }, 700);

                        jQuery('div.jsst-popup-wrapper').slideUp('slow');
                        jQuery('div#js-history-popup').slideUp('slow');

                    }

                    function formField(){
                        jQuery('div#jsjob_installer_waiting_div').show();
                        jQuery("#name").val("");
                        jQuery("#email").val("");
                        jQuery("#ticketpopupsearch").submit();
                    }
                </script>
                <div id="js-history-back" style="display:none"> </div>
                <div id="js-history-popup" style="display:none">
                    <div id="js-history-head">
                        <span class="js-title"><?php echo Text::_('Ticket History'); ?></span>
                        <span class="js-image jsst-dialog-close"></span>
                    </div>
                    <div class="js-ticket-history-table-wrp">
                        <table class="table js-table-striped">
                            <thead>
                              <tr>
                                <th class="js-ticket-textalign-center"><?php echo Text::_('Date'); ?></th>
                                <th class="js-ticket-textalign-center"><?php echo Text::_('Time'); ?></th>
                                <th class=""><?php echo Text::_('Message Logs'); ?></th>
                              </tr>
                            </thead>
                            <tbody class="js-ticket-ticket-history-body">
                                <?php if(isset($this->tickethistory))
                                    foreach ($this->tickethistory as $history) { ?>
                                      <tr>
                                        <td class="js-ticket-textalign-center"><?php echo HTMLHelper::_('date',$history->datetime,'Y-m-d'); ?></td>
                                        <td class="js-ticket-textalign-center"><?php echo HTMLHelper::_('date',$history->datetime,'H:i:s'); ?></td>
                                        <?php
                                            if ($history->level == 1) //admin
                                                $color = "blue";
                                            elseif ($history->level == 2) //staff
                                                $color = "orange";
                                            else  //user
                                                $color = "black";
                                        ?>
                                        <td class="" style="color:<?php echo $color; ?>"><?php echo $history->message; ?></td>
                                      </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                        <div class="js-ticket-priorty-btn-wrp">
                            <button type="button" role="button" class="js-ticket-priorty-cancel" onclick="closePopup();"><?php echo Text::_('Cancel');?></button>
                        </div>
                    </div>
                </div>
                <div id="jsjob_installer_waiting_div" style="display:none;"></div>
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
                                        <?php
                                            $link = "index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=".$this->Itemid;
                                        ?>
                                        <a href="<?php echo $link; ?>" title="<?php echo htmlspecialchars(Text::_('Dashboard'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo Text::_('My Tickets'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php echo Text::_('Ticket Detail')?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div id="tk-detail-wraper">
                    <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
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
                                                <a class="js-tkt-go-to-all" href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>"><?php echo Text::_('Show All').' '.Text::_('Tickets'); ?>
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
                                        <a class="js-button" id="jstkhistory" href="#">
                                            <img class="js-button-icon" title="<?php echo Text::_('Ticket History'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/history.png">
                                            <span><?php echo Text::_('Ticket History'); ?></span>
                                        </a>
                                </div>
                                    <?php } ?>
                                <!-- data edit -->
                                </div>
                                <?php
                                // Custom fields are intentionally rendered in the main content column.
                                // The old nested sidebar rows squeezed every field into a very narrow grid cell.
                                $jsstCustomFieldItems = array();
                                $customfields = getCustomFieldClass()->userFieldsData(1);
                                if (!empty($customfields)) {
                                    foreach ($customfields as $field) {
                                        if ($field->userfieldtype == 'termsandconditions') {
                                            continue;
                                        }

                                        $array = getCustomFieldClass()->showCustomFields(
                                            $field,
                                            5,
                                            $this->ticketdetail->params,
                                            $this->ticketdetail->id
                                        );

                                        if (empty($array)) {
                                            continue;
                                        }

                                        $fieldType = strtolower(trim((string) $field->userfieldtype));
                                        $wideFieldTypes = array(
                                            'textarea',
                                            'editor',
                                            'multiple',
                                            'multiselect',
                                            'dependentfield',
                                            'file'
                                        );

                                        $jsstCustomFieldItems[] = array(
                                            'title' => $array['title'],
                                            'value' => $array['value'],
                                            'wide' => in_array($fieldType, $wideFieldTypes, true)
                                        );
                                    }
                                }

                                if (!empty($jsstCustomFieldItems)) { ?>
                                    <section class="jsst-ticket-custom-fields-panel" aria-labelledby="jsst-ticket-custom-fields-title">
                                        <div class="js-ticket-thread-heading jsst-ticket-custom-fields-heading">
                                            <span id="jsst-ticket-custom-fields-title"><?php echo Text::_('Additional Information'); ?></span>
                                            <span class="jsst-ticket-custom-fields-count">
                                                <?php echo count($jsstCustomFieldItems); ?>
                                            </span>
                                        </div>
                                        <div class="jsst-ticket-custom-fields-grid">
                                            <?php foreach ($jsstCustomFieldItems as $jsstCustomFieldItem) { ?>
                                                <div class="jsst-ticket-custom-field<?php echo $jsstCustomFieldItem['wide'] ? ' is-wide' : ''; ?>">
                                                    <div class="jsst-ticket-custom-field-label">
                                                        <?php echo Text::_($jsstCustomFieldItem['title']); ?>
                                                    </div>
                                                    <div class="jsst-ticket-custom-field-value">
                                                        <?php echo Text::_($jsstCustomFieldItem['value']); ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </section>
                                <?php } ?>

                                <div class="jsst-ticket-workflow-sections">

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
                                            if (isset($this->ticketattachment[0]->filename)) { ?>
                                                <div class="js-ticket-attachments-wrp">
                                                    <?php foreach ($this->ticketattachment as $attachment) {
                                                        echo '
                                                            <div class="js_ticketattachment">
                                                                <span class="js-ticket-download-file-title">
                                                                    ' . $attachment->filename  . '
                                                                </span>
                                                                <a class="js-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&'. Factory::getSession()->getFormToken() .'=1">'.
                                                                    Text::_("Download").'
                                                                </a>
                                                            </div>';
                                                    }
                                                    echo'
                                                        <a class="js-all-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=downloadall&id='.$attachment->id.'&'. Factory::getSession()->getFormToken() .'=1">'.Text::_("Download All").'</a>';?>
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
                                <div class="js-ticket-post-reply-wrapper">
                                    <!-- Ticket Replies -->
                                    <?php  if (!empty($this->ticketreplies)) { ?>
                                    <?php $i = 0;
                                    foreach ($this->ticketreplies AS $row) {
                                        $i++;
                                        // Free edition has no staff module, so a reply is always authored by
                                        // the ticket user and shown with the default avatar. The staff
                                        // name/photo columns are not selected by this edition's queries.
                                        $staffname = $row->name; ?>
                                        <div class="js-ticket-detail-box js-ticket-post-reply-box"><!-- Ticket Detail Box -->
                                            <!-- Left Side Image -->
                                            <div class="js-ticket-user-img-wrp">
                                                    <img class="js-ticket-staff-img" src="<?php echo Uri::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                            </div>
                                            <div class="js-ticket-thread-cnt">
                                                <div class="js-ticket-user-name-wrp">
                                                   <?php echo $staffname; ?>
                                                </div>
                                                <div class="js-ticket-user-email-wrp">
                                                    <?php if ($row->ticketviaemail == 1) { ?>
                                                        <?php echo Text::_('Created by Email'); ?>
                                                    <?php } ?>
                                                </div>
                                                <div class="js-ticket-rows-wrapper">
                                                    <div >
                                                        <div class="js-ticket-row">
                                                            <div class="js-ticket-field-value">
                                                                <?php $message = $row->message;
                                                                if($row->mergemessage == 1){
                                                                    $message = str_replace("cid[]=","id=",$message);
                                                                    $message = str_replace("layout=ticketdetails","layout=ticketdetail",$message);
                                                                } ?>
                                                                <?php echo html_entity_decode($message); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php if (!empty($row->attachments)) { ?>
                                                         <?php if (isset($row->attachments)) { ?>
                                                            <div class="js-ticket-attachments-wrp">
                                                                <?php foreach ($row->attachments as $attachment) {
                                                                    $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid . '&'. Factory::getSession()->getFormToken() . '=1';
                                                                    echo ' <div class="js_ticketattachment">
                                                                                <span class="js-ticket-download-file-title">'
                                                                                    . $attachment->filename . "&nbsp(" . getJSTicketPHPFunctionsClass()->jsticket_round($attachment->filesize, 2) . " KB)";
                                                                            echo '</span>
                                                                                <a class="js-download-button" target="_blank" href="' . $path . '">
                                                                                    '.Text::_('Download').'
                                                                                </a>
                                                                        </div>';
                                                                }
                                                                    echo'
                                                                        <a class="js-all-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=downloadallforreply&id='.$row->id.'&' . Factory::getSession()->getFormToken() . '=1">'.Text::_('Download All').'
                                                                             </a>';?>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                                <div class="js-ticket-time-stamp-wrp">
                                                    <span class="js-ticket-ticket-created-date">
                                                         <?php echo HTMLHelper::_('date',$row->created,"l F d, Y");?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                    <?php }
                                } ?>
                            </div>

                            <div class="js-ticket-tabs-wrapper">
                                <?php if(1 == 1){ 
                                    if ($this->ticketdetail->lock != 1 && $this->ticketdetail->status != 4 && $this->ticketdetail->status != 5) { ?>
                                        <div class="js-ticket-reply-forms-heading"><?php echo Text::_('Reply with a Message'); ?></div>
                                        <div class="js-ticket-reply-field-wrp">
                                            <div class="js-ticket-reply-field">
                                                <?php
                                                    // Keep the field named "responce". The model reads the body from
                                                    // POST "message", so the controller maps it across - renaming the
                                                    // editor to "message" collides with other ids on a Joomla page.
                                                    echo $editor->display('responce', '', '550', '300', '60', '20', false);
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                        $isguest = $this->user->getIsGuest();
                                        if ($isguest == 0) {
                                            $publisheCheck =  $this->isAttachmentPublished;
                                        } else {
                                            $publisheCheck =  $this->isAttachmentVisitorPublished;
                                        }
                                        if ($publisheCheck) { ?>
                                            <div class="js-attachment-wrp">
                                                <div class="js-form-title"><?php echo Text::_('Attachments'); ?></div>
                                                <div class="js-form-value js-attachment-files-wrp">
                                                    <div class="js-attachment-files" data-jsst-max-attachments="<?php echo (int) $this->config['noofattachment']; ?>">
                                                        <span class="js-attachment-file-box">
                                                            <input type="file" class="inputbox js-attachment-inputbox js-form-input-field-attachment" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlength='30'/>
                                                            <span class='js-attachment-remove'></span>
                                                        </span>
                                                    </div>
                                                    <div class="js-attachment-option">
                                                        <?php echo Text::_('Maximum file size') . ': ' . $this->config['filesize']; ?> KB<br><?php echo Text::_('Allowed file types') . ': ' . $this->config['fileextension']; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="js-ticket-reply-form-button-wrp">
                                            <input  class="js-ticket-save-button" type="button" onclick="validate_form_department(document.adminForm)" value="<?php echo Text::_('Post Reply'); ?>"/>
                                        </div>
                                    <?php }
                                }?>
                                </div><!-- /.jsst-ticket-workflow-sections -->
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
                                                    $color = "#69d2e7;";
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
                                                            echo Text::_('ago');
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
                                                   <?php if ($this->ticketdetail->lastreply == '' || $this->ticketdetail->lastreply == '0000-00-00 00:00:00') {echo Text::_('No last reply'); } else {echo HTMLHelper::_('date',$this->ticketdetail->lastreply,"d F, Y"); } ?>
                                                </div>
                                            </div>
                                            <?php if (jsstTicketFieldVisible($jsstVisibleTicketFields, 'department')) { ?>
                                            <div class="js-ticket-row">
                                                <div class="js-ticket-field-title">
                                                    <?php echo Text::_('Department'); ?>&nbsp;:
                                                </div>
                                                <div class="js-ticket-field-value">
                                                    <?php echo $this->ticketdetail->departmentname; ?>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <div class="js-ticket-row">
                                                <div class="js-ticket-field-title">
                                                   <?php echo Text::_('Ticket ID'); ?>&nbsp;:
                                                </div>
                                                <div class="js-ticket-field-value">
                                                   <?php echo $this->ticketdetail->ticketid; ?>
                                                   <a href="javascript:void(0)" title="Copy" class="js-tkt-det-copy-id" id="ticketidcopybtn" success=<?php echo Text::_('Copied'); ?>><?php echo Text::_('Copy'); ?></a>
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
                                                    $msg = Text::_('On Hold');
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
                                            <?php if (jsstTicketFieldVisible($jsstVisibleTicketFields, 'helptopic')) { ?>
                                            <div class="js-ticket-row">
                                                <div class="js-ticket-field-title">
                                                    <?php echo Text::_('Help Topic'); ?>&nbsp;:
                                                </div>
                                                <div class="js-ticket-field-value">
                                                    <?php echo $this->ticketdetail->helptopic; ?>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <?php if(isset($this->time_taken)){ ?>
                                                <div class="js-ticket-row">
                                                    <div class="js-ticket-field-title">
                                                         <?php echo Text::_('Total Time Taken'); ?>&nbsp;:
                                                    </div>
                                                    <div class="js-ticket-field-value">
                                                        <?php
                                                        $time = $this->time_taken;
                                                        // to handle php 8.1 warning for intget to float type casting
                                                        if($time > 3600){
                                                            $hours = floor($time / 3600);
                                                        }else{
                                                            $hours = 0;
                                                        }

                                                            if($time > 60){
                                                                $mins = floor(fmod($time / 60, 60));
                                                            }else{
                                                                $mins = 0;
                                                            }
                                                            if($time > 0){
                                                                $secs = floor($time % 60);
                                                            }else{
                                                                $secs = 0;
                                                            }
                                                            echo Text::_(''). sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                                                        ?>
                                                    </div>
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
                                                    $ticketmessage = Text::_('On Hold');
                                                } elseif ($this->ticketdetail->status == 2) {
                                                    $color = "#69d2e7;";
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
                                        <?php
                                            $jsstPriorityNameForColor = strtolower(trim((string) $this->ticketdetail->priority));
                                            if ($jsstPriorityNameForColor === 'normal') {
                                                $jsstPriorityColor = '#16a34a';
                                            } elseif ($jsstPriorityNameForColor === 'high') {
                                                $jsstPriorityColor = '#ef4444';
                                            } elseif ($jsstPriorityNameForColor === 'low') {
                                                $jsstPriorityColor = '#0ea5e9';
                                            } elseif ($jsstPriorityNameForColor === 'urgent' || $jsstPriorityNameForColor === 'critical') {
                                                $jsstPriorityColor = '#dc2626';
                                            }
                                            $jsstPriorityColor = (string) $this->ticketdetail->prioritycolour;
                                        ?>
                                        <div class="js-ticket-field-value js-ticket-priorty" style="background:<?php echo $jsstPriorityColor; ?>; color:#ffffff;">
                                           <?php echo Text::_($this->ticketdetail->priority); ?>
                                        </div>
                                    </div>
                                    </div>

                                </div>
                            </div>
                            <!-- Ticket Post Replay -->

                        </div>

                        <input type="hidden" name="email" value="<?php echo $this->ticketdetail->email; ?>" />
                        <input type="hidden" name="email_ban" id="email_ban" value="<?php echo (isset($this->isemailban)) ? $this->isemailban : ''; ?>" />
                        <!-- Free edition has no staff/agent module: staffid is always empty, but the
                             field is kept so the posted form shape stays the same as Pro's. -->
                        <input type="hidden" id="staffid" name="staffid" value="" />

                        <input type="hidden" name="callaction" id="callaction" value="" />
                        <input type="hidden" name="callfrom" id="callfrom" value="" />
                        <input type="hidden" name="view" value="ticket" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                        <input type="hidden" name="lastreply" value="<?php echo $this->ticketdetail->lastreply; ?>" />
                        <!-- Read by storeUserReplies(). Reopening has its own path (callaction 8),
                             so a plain reply never reopens the ticket. -->
                        <input type="hidden" name="isreopen" id="isreopen" value="0" />
                        <input type="hidden" name="id" value="<?php echo $this->ticketdetail->id; ?>" />
                        <input type="hidden" name="ticketid" id="ticketid" value="<?php echo $this->ticketdetail->ticketid; ?>" />
                        <input type="hidden" name="hash" value="<?php echo $this->ticketdetail->hash; ?>" />
                        <input type="hidden" name="layout" value="tickets" />
                        <input type="hidden" id="task" name="task" value="actionticket" />
                        <input type="hidden" name="c" value="ticket" />
                        <input type="hidden" name="created" value="<?php echo $curdate = date('Y-m-d H:i:s'); ?>"/>
                        <?php echo HTMLHelper::_('form.token'); ?>
                    </form>
                    <div id="popup-record-data" style="display:inline-block;width:100%;"></div>
                </div>
<?php
            }else{
                messageslayout::getRecordNotFound(); //No Record
            }
}/*else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}*///End ?>
    <script language="Javascript">
        jQuery(document).ready(function () {
            jsstInitAttachmentDropzones();
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

        function jsstTicketDetailMaxAttachments(zone) {
            if (zone.find('input[name="noteattachment"]').length) {
                return 1;
            }
            var max = parseInt(zone.attr('data-jsst-max-attachments'), 10);
            if (!max || max < 1) {
                max = <?php echo (int) $this->config['noofattachment']; ?>;
            }
            return max;
        }

        function jsstTicketDetailAttachmentTemplate() {
            return "<span class='js-attachment-file-box'><input name='filename[]' class='js-attachment-inputbox js-form-input-field-attachment' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlength='30' /><span class='jsst-attachment-file-name'><?php echo Text::_('No file selected'); ?></span><span class='js-attachment-remove'></span></span>";
        }

        function jsstTicketDetailSelectedAttachmentCount(zone) {
            var count = 0;
            var selector = zone.find('input[name="noteattachment"]').length ? 'input[name="noteattachment"]' : 'input[name="filename[]"]';
            zone.find(selector).each(function () {
                if (this.files && this.files.length) {
                    count++;
                }
            });
            return count;
        }

        function jsstEnsureTicketDetailAttachmentLabel(input) {
            var row = jQuery(input).closest('.js-attachment-file-box, .js-value-text');
            if (!row.children('.jsst-attachment-file-name').length) {
                jQuery(input).after('<span class="jsst-attachment-file-name"><?php echo Text::_('No file selected'); ?></span>');
            }
        }

        function jsstUpdateTicketDetailAttachmentRow(input) {
            var row = jQuery(input).closest('.js-attachment-file-box, .js-value-text');
            jsstEnsureTicketDetailAttachmentLabel(input);
            var label = row.children('.jsst-attachment-file-name').first();
            if (input.files && input.files.length) {
                label.text(input.files[0].name);
                row.addClass('is-file-attached');
            } else {
                label.text("<?php echo Text::_('No file selected'); ?>");
                row.removeClass('is-file-attached');
            }
        }

        function jsstUpdateTicketDetailAttachmentState(zone) {
            var max = jsstTicketDetailMaxAttachments(zone);
            var selected = jsstTicketDetailSelectedAttachmentCount(zone);
            var wrapper = zone.closest('.js-attachment-files-wrp, .js-ticket-attachment-wrp');
            var option = wrapper.find('.js-attachment-option').first();
            zone.find('input[type="file"]').each(function () {
                jsstUpdateTicketDetailAttachmentRow(this);
            });
            if (option.length && !option.children('.jsst-attachment-counter').length) {
                option.prepend('<span class="jsst-attachment-counter"></span>');
            }
            option.children('.jsst-attachment-counter').text(selected + ' / ' + max + ' <?php echo Text::_('attachments selected'); ?>');
            wrapper.find('#js-attachment-add').toggle(selected < max);
        }

        function jsstGetTicketDetailAttachmentInput(zone) {
            var noteInput = zone.find('input[name="noteattachment"]').first();
            var max = jsstTicketDetailMaxAttachments(zone);
            if (jsstTicketDetailSelectedAttachmentCount(zone) >= max) {
                alert("<?php echo Text::_('File upload limit exceeded.'); ?>");
                return null;
            }
            if (noteInput.length) {
                return noteInput;
            }
            var emptyInput = zone.find('input[name="filename[]"]').filter(function () {
                return !this.files || this.files.length === 0;
            }).first();
            if (!emptyInput.length) {
                zone.append(jsstTicketDetailAttachmentTemplate());
                emptyInput = zone.find('input[name="filename[]"]').last();
                jsstEnsureTicketDetailAttachmentLabel(emptyInput.get(0));
            }
            return emptyInput;
        }

        jQuery(document).on('click', '#js-attachment-add', function (event) {
            event.preventDefault();
            var zone = jQuery(this).closest('.js-attachment-files-wrp, .js-ticket-attachment-wrp').find('.js-attachment-files').first();
            if (!zone.length) {
                return;
            }
            var input = jsstGetTicketDetailAttachmentInput(zone);
            if (input) {
                input.trigger('click');
            }
        });

        jQuery(document).delegate('.js-attachment-remove', 'click', function (e) {
            e.preventDefault();
            var row = jQuery(this).closest('.js-attachment-file-box, .js-value-text');
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
            jsstUpdateTicketDetailAttachmentState(zone);
        });

        function jsstInitAttachmentDropzones() {
            var zones = jQuery('.jsst-ticket-detail-page .js-attachment-files');
            if (!zones.length) {
                return;
            }
            zones.each(function () {
                var zone = jQuery(this);
                jsstPrepareAttachmentDropzone(zone);
                jsstUpdateTicketDetailAttachmentState(zone);
                if (zone.data('jsstDragDropReady')) {
                    return;
                }
                zone.data('jsstDragDropReady', true);
                zone.addClass('jsst-dragdrop-enabled');

                zone.on('change', 'input[type="file"]', function () {
                    jsstUpdateTicketDetailAttachmentRow(this);
                    jsstUpdateTicketDetailAttachmentState(zone);
                });

                zone.on('click', '.jsst-dropzone-message', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    var input = jsstGetTicketDetailAttachmentInput(zone);
                    if (input) {
                        input.trigger('click');
                    }
                });

                zone.on('dragenter dragover', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    zone.addClass('is-dragover');
                    jsstSetDropzoneText(zone, "<?php echo Text::_('Release files to attach'); ?>");
                });
                zone.on('dragleave dragend', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    zone.removeClass('is-dragover');
                    jsstSetDropzoneText(zone, "<?php echo Text::_('Drag and drop files here'); ?>");
                });
                zone.on('drop', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    zone.removeClass('is-dragover');
                    jsstSetDropzoneText(zone, "<?php echo Text::_('Drag and drop files here'); ?>");
                    var originalEvent = event.originalEvent || event;
                    var files = originalEvent.dataTransfer && originalEvent.dataTransfer.files ? originalEvent.dataTransfer.files : null;
                    if (!files || !files.length) {
                        return;
                    }
                    jsstAttachDroppedFiles(zone, files);
                });
            });
        }

        function jsstPrepareAttachmentDropzone(zone) {
            if (!zone.children('.jsst-dropzone-message').length) {
                zone.prepend('<button type="button" class="jsst-dropzone-message"><span class="jsst-dropzone-icon">&#8682;</span><span class="jsst-dropzone-copy"><strong><?php echo Text::_('Drag and drop files here'); ?></strong><small><?php echo Text::_('or click to choose files'); ?></small></span></button>');
            }
        }

        function jsstSetDropzoneText(zone, text) {
            var label = zone.children('.jsst-dropzone-message').find('strong').first();
            if (label.length) {
                label.text(text);
            }
        }

        function jsstSetDroppedFile(input, file) {
            if (typeof DataTransfer === 'undefined') {
                alert("<?php echo Text::_('Your browser cannot attach dropped files. Click the upload area to choose files.'); ?>");
                return false;
            }
            var transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            jQuery(input).trigger('change');
            jsstUpdateTicketDetailAttachmentRow(input);
            return true;
        }

        function jsstAttachDroppedFiles(zone, files) {
            var noteInput = zone.find('input[name="noteattachment"]').first();
            if (noteInput.length) {
                if (files.length > 1) {
                    alert("<?php echo Text::_('Only one file can be attached here.'); ?>");
                }
                jsstSetDroppedFile(noteInput.get(0), files[0]);
                jsstUpdateTicketDetailAttachmentState(zone);
                return;
            }

            var max = jsstTicketDetailMaxAttachments(zone);
            for (var i = 0; i < files.length; i++) {
                if (jsstTicketDetailSelectedAttachmentCount(zone) >= max) {
                    alert("<?php echo Text::_('File upload limit exceeded.'); ?>");
                    break;
                }
                var emptyInput = jsstGetTicketDetailAttachmentInput(zone);
                if (!emptyInput || !jsstSetDroppedFile(emptyInput.get(0), files[i])) {
                    break;
                }
            }
            jsstUpdateTicketDetailAttachmentState(zone);
        }

        function validate_form_department(f) {
            var content = jQuery('textarea#responce').val();
            if(content == ''){
                if(isTinyMCE()){
                    content = tinyMCE.get('responce').getContent();
                }else{
                    content = true;
                }
            }
            // "savemessage" is the case this edition's controller implements.
            jQuery('#callfrom').val('savemessage');
            if (content !== '') {
                document.adminForm.submit();
            } else {
                alert("<?php echo Text::_('Some values are not valid. Please review the form and try again.'); ?>");
                return false;
            }
        }
        function isTinyMCE(){
            is_tinyMCE_active = false;
            if (typeof(tinyMCE) != "undefined") {
                //if(tinyMCE.editors.length > 0){ //this line generate error
                    is_tinyMCE_active = true;
                //}
            }
            return is_tinyMCE_active;
        }
        function getpremade(src, premadeid, append) {
            var link = 'index.php?option=com_jssupportticket&c=ticket&task=getpremadeforinternalnote&<?php echo Factory::getSession()->getFormToken(); ?>=1';
            jQuery.post(link,{val:premadeid},function(data){
                if(data){
                    if (append == true) {
                        var content = jQuery('textarea#responce').val();
                        if(content == ''){
                            if(isTinyMCE()){
                                content = tinyMCE.get('responce').getContent();
                            }
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
            if(value == 3){
                var yesclose = confirm('<?php echo Text::_('Are you sure you want to close this ticket?'); ?>');
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

        function editreply(id,jsession) {
            var rsrc = 'responce_' + id;
            var src = 'responce_edit_' + id;
            var esrc = 'editor_responce_' + id;
            showhide(rsrc, 'none');
            showhide(src, 'block');
            jQuery('#' + src).html("<?php echo Text::_('Loading'); ?> ...");
            jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=editresponce&"+jsession, {id: id}, function (data, status) {
                jQuery('#' + src).html(data);  //retuen value
                if (!tinyMCE.get(esrc)) {  //toggle editor
                    tinyMCE.execCommand('mceToggleEditor', false, esrc);
                    return false;
                }
            });
        }

        function saveResponce(id,jsession) {
            var esrc = 'editor_responce_' + id;
            if (!tinyMCE.get(esrc)) { // check toggle
                alert("<?php echo Text::_('Please toggle editor', true); ?>");
            } else {
                if(isTinyMCE()){
                    var content = tinyMCE.get(esrc).getContent();
                }
                var rsrc = 'responce_' + id;
                var src = 'responce_edit_' + id;
                showhide(rsrc, 'block');
                showhide(src, 'none');

                jQuery('#' + rsrc).html("Saving...");
                var arr = new Array();
                arr[0] = id;
                arr[1] = content;
                var link = 'index.php?option=com_jssupportticket&c=ticket&task=saveresponceajax&'+jsession;
                jQuery.post(link,{val:JSON.stringify(arr)}, function(data){
                    if(data){
                        if(data == 1){
                            jQuery('#' + rsrc).html(content);
                        }else{
                            jQuery('#' + rsrc).html(data);
                        }
                        tinymce.remove(tinyMCE.get(esrc));
                    }
                });
            }
        }
        function deletereply(id,jsession) {
            if(confirm("<?php echo Text::_('Are you sure you want to delete this item?'); ?>")){
                var rsrc = 'responce_' + id;
                jQuery('#' + rsrc).html("Deleting...");

               jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=deleteresponceajax&"+jsession, {id : id}, function(data){
                    jQuery('#' + rsrc).html(data);
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
        function showhide(layer_ref, state) {
            if (state == 'none') {
                jQuery('#' + layer_ref).hide('slow');
            } else if (state == 'block') {
                jQuery('#' + layer_ref).show('slow');

            }
        }
    </script>
</div>
<?php /*} */?>
