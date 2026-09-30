<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 * Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Project:     JS Tickets
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
?>

<script language="Javascript">
    function confirmdelete() {
        if (confirm("<?php echo Text::_('Are you sure to delete'); ?>") == true) {
            return true;
        } else {
            return false;
        }
    }
</script>
<div id="js-tk-admin-wrapper" class="jsst-screen jsst-proinstaller-step2">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'JS Support Ticket Pro Installer';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('JS Support Ticket Pro Installer'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow jsst-proinstaller-card">
            <div style="display:none;" id="jsjob_installer_waiting_div"></div>
            <span style="display:none;" id="jsjob_installer_waiting_span"><?php echo Text::_('Please wait installation in progress'); ?></span>
            <div id="jsst-main-wrapper" class="jsst-proinstaller-v2">
                <div id="jsst-lower-wrapper">
                    <div class="jsst_installer_wrapper" id="jsst-installer_id">
                        <div class="jsst-proinstaller-v2__intro">
                            <div class="jsst-proinstaller-v2__logo">
                                <img alt="<?php echo htmlspecialchars(Text::_('JS Support Ticket'), ENT_QUOTES, 'UTF-8'); ?>" src="components/com_jssupportticket/include/images/jsst-support-icon-v26-128.png">
                            </div>
                            <span class="jsst-proinstaller-v2__eyebrow"><?php echo Text::_('Pro Installer'); ?></span>
                            <h2><?php echo Text::_('Choose Pro Version'); ?></h2>
                            <p><?php echo Text::_('Your activation key has been accepted. Select the Pro package version you want to install and continue.'); ?></p>
                        </div>
                        <div class="jsst-proinstaller-v2__form">
                            <div class="jsst-proinstaller-v2__content" id="jsst_middle">
                                <div class="jsst-proinstaller-v2__activation">
                                    <label for="transactionkey"><?php echo Text::_('Activation Key'); ?></label>
                                    <div class="jsst_form_field_wrp">
                                        <div class="jsst_bg_overlay">
                                            <input type="text" name="transactionkey" id="transactionkey" class="jsst_key_field" value="<?php if(isset($this->transactionkey)) echo $this->transactionkey; ?>" placeholder="<?php echo Text::_('Please Insert Your Activation Key'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div id="jsst_error_message" class="jsst_error_messages" style="display: none"></div>
                                <?php
                                if (isset($this->response) && $this->response != '') {
                                    $response = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($this->response);
                                    $response = json_decode($response); ?>
                                    <div class="jslm_error_messages jsst-proinstaller-v2__messages">
                                        <?php if ($response[0] != true) { ?>
                                            <span class="jsst_error_messages" id="jsst_response_error_message"><span class="jsst_msg"><?php echo $response[1]; ?></span></span>
                                        <?php } else { ?>
                                            <div id="jsst_next_form"><?php echo $response[2]; ?></div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
<?php
/*
 * The "Select Version" label and the hint above it used to be hidden here,
 * which left an unlabelled dropdown with nothing explaining what to pick. Both
 * are part of the injected markup and both are styled by the
 * `.jsst-proinstaller-v2 #jsst_next_form` rules, so they are shown now: the
 * hint reads as muted helper text and the label sits above the field, matching
 * the Activation Key block above it.
 */
?>
