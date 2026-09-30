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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;

global $mainframe;

/*
 * Free edition: settings that drive a Pro-only feature keep their place in this
 * screen so the layout matches the Pro edition, but their label carries a "*"
 * via jsstProCfg() (defined in the component entry file). Two tabs (Staff Menu
 * Settings, Feedback Settings) are Pro in full and are flagged on the tab itself
 * rather than repeating the marker on every row.
 */

$document = Factory::getDocument();
$conf   = Factory::getConfig();
$editor = Editor::getInstance($conf->get('editor'));

if (JVERSION < 3) {
    HTMLHelper::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} else {
    HTMLHelper::_('bootstrap.framework');
    HTMLHelper::_('jquery.framework');
}
// $document->addScript('components/com_jssupportticket/include/js/jquery_idTabs.js');

$captchaselection = array(
    array('value' => '1', 'text' => Text::_('Google Recaptcha')),
    array('value' => '2', 'text' => Text::_('Own Captcha'))
);
$owncaptchaoparend = array(
    array('value' => '2', 'text' => '2'),
    array('value' => '3', 'text' => '3')
);
$owncaptchatype = array(
    array('value' => '0', 'text' => Text::_('Any')),
    array('value' => '1', 'text' => Text::_('Addition')),
    array('value' => '2', 'text' => Text::_('Subtraction'))
);


$date_format = array(
    '0' => array('value' => 'd-m-Y', 'text' => Text::_('DD-MM-YYYY')),
    '1' => array('value' => 'm-d-Y', 'text' => Text::_('MM-DD-YYYY')),
    '2' => array('value' => 'Y-m-d', 'text' => Text::_('YYYY-MM-DD')),);

$yesno = array(
    '0' => array('value' => '1',
        'text' => Text::_('JYES')),
    '1' => array('value' => '0',
        'text' => Text::_('JNO')),);


$overduetype_array = array(
    '0' => array('value' => '1',
        'text' => Text::_('Days')),
    '1' => array('value' => '2',
        'text' => Text::_('Hours')),);


$enableddisabled = array(
    '0' => array('value' => '1',
        'text' => Text::_('Enabled')),
    '1' => array('value' => '0',
        'text' => Text::_('Disabled')),);

$showhide = array(
    '0' => array('value' => '1',
        'text' => Text::_('Show')),
    '1' => array('value' => '0',
        'text' => Text::_('Hide')),);
$ticketidsequence = array(
    '0' => array('value' => '1',
        'text' => Text::_('Random')),
    '1' => array('value' => '2',
        'text' => Text::_('Sequential')),);

$ticketsorting = array(
    '0' => array('value' => '1',
        'text' => Text::_('Ascending')),
    '1' => array('value' => '2',
        'text' => Text::_('Descending')),);

$maxticketinterval = array(
    '0' => array('value' => '1',
        'text' => Text::_('Day')),
    '1' => array('value' => '2',
        'text' => Text::_('Month')),
    '2' => array('value' => '3',
        'text' => Text::_('Year')),
    '3' => array('value' => '4',
        'text' => Text::_('Life Time')));

$offline = HTMLHelper::_('select.genericList', $yesno, 'offline', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['offline']);

$curlocation = HTMLHelper::_('select.genericList', $yesno, 'cur_location', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cur_location']);

$date_format = HTMLHelper::_('select.genericList', $date_format, 'date_format', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['date_format']);


$overduetype = HTMLHelper::_('select.genericList', $overduetype_array, 'ticket_overdue_type', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_type']);


$big_field_width = 40;
$med_field_width = 25;
$sml_field_width = 15;
?>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-special">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'Configurations';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('Configurations'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <?php
			$adminEmail = JSSupportTicketModel::getJSModel('email')->getEmailById($this->configuration['admin_email']);
			$ticketviaemailaddress = $this->configuration['tve_emailaddress'];
			if($adminEmail == $ticketviaemailaddress){
        ?>
			<div id="js-emailsame-error">
				<?php echo Text::_('COM_JSSUPPORTTICKET_ADMIN_EMAIL_CONFLICTS_WITH_TICKET_EMAIL'); ?>
			</div>
        <?php } ?>
        <form action="index.php" class="jsstadmin-data-wrp jsstadmin-bg-color js-ticket-box-shadow" method="POST" name="adminForm" id="adminForm">
            <div id="tabs_wrapper" class="tabs_wrapper js-col-lg-12 js-col-md-12">
                <div class="idTabs">
                    <span><a id="generalsettingbtn" class="tab selected" ><?php echo Text::_('General Settings'); ?></a></span>
                    <span><a id="ticketsettingbtn" class="tab"><?php echo Text::_('Ticket Settings'); ?></a></span>
                    <span><a id="emialsettingbtn" class="tab"><?php echo Text::_('Default System Email'); ?></a></span>
                    <span><a id="auotrespondersettingbtn" class="tab"><?php echo Text::_('Mail Settings'); ?></a></span>
                    <span><a id="usermenusettingbtn" class="tab"><?php echo jsstProCfg('Staff Menu Settings'); ?></a></span>
                    <span><a id="vismenusettingbtn" class="tab"><?php echo Text::_('Visitor Menu Settings'); ?></a></span>
                    <span><a id="feedbacksettingsbtn" class="tab"><?php echo jsstProCfg('Feedback Settings'); ?></a></span>
                </div>
                <p class="jsst-pro-legend">
                    <span class="jsst-pro-star" aria-hidden="true">*</span>
                    <?php echo Text::_('Settings marked with an asterisk belong to features available in the Pro version.'); ?>
                    <a href="index.php?option=com_jssupportticket&amp;c=jssupportticket&amp;layout=proversion"><?php echo Text::_('See what is in Pro'); ?></a>
                </p>
                <div id="generalsetting" style="display: none;">
                        <legend><?php echo Text::_('General Setting'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Title'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><input type="text" name="title" value="<?php echo $this->configuration['title']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" maxlength="255" /></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Offline'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><?php echo $offline; ?></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Offline Message'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><textarea name="offline_text" cols="25" rows="3" class="inputbox"><?php echo $this->configuration['offline_text']; ?></textarea> </div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Data Directory'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="data_directory" value="<?php echo $this->configuration['data_directory']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>"/> </div>
                                 <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Rename the existing data directory on the server before changing this value.'); echo '<br/><b>"'.JPATH_SITE.$this->configuration['data_directory'].'"</b>'; ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Date Format'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><?php echo $date_format; ?></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row js-mg-bottom">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Auto-Close Tickets'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="ticket_auto_close_indays" value="<?php echo $this->configuration['ticket_auto_close_indays']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Days'); ?><br><?php echo Text::_('Automatically close tickets when the customer does not respond within the configured number of days.'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Maximum Attachments'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="noofattachment" value="<?php echo $this->configuration['noofattachment']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><br clear="all"/><small><?php echo Text::_('Maximum number of files that can be attached to one ticket or reply.'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Maximum File Size'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><input type="text" name="filesize" value="<?php echo $this->configuration['filesize']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /> &nbsp;KB</div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Allowed File Extensions'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><textarea name="fileextension" cols="25" rows="3" class="inputbox"><?php echo $this->configuration['fileextension']; ?></textarea></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Allowed file extensions, separated by commas.') ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Breadcrumbs'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo $curlocation; ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value js-config-help"><small><?php echo Text::_('Show or hide breadcrumbs.'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Top Header'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $showhide, 'show_header', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['show_header']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value js-config-help"><small><?php echo Text::_('Show or hide the top header.'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Show count on my tickets'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'show_count_tickets', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['show_count_tickets']); ?></div>
                            </div>
                        </div>
                </div>
                <div id="ticketsetting" style="display: none;">
                        <legend><?php echo Text::_('Ticket Setting'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Visitors can create ticket'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'visitor_can_create_ticket', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['visitor_can_create_ticket']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Can visitors create tickets or not'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Ticket id sequence'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $ticketidsequence, 'ticketid_sequence', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticketid_sequence']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Set ticket id sequential or random'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Maximum tickets'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="maximum_ticket" value="<?php echo $this->configuration['maximum_ticket']; ?>" class="inputbox" size="<?php echo $sml_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Maximum tickets per user'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Maximum tickets within interval'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $maxticketinterval, 'maximum_ticket_interval_time', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['maximum_ticket_interval_time']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Maximum tickets within time interval per user'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Maximum open tickets'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="ticket_per_email" value="<?php echo $this->configuration['ticket_per_email']; ?>" class="inputbox" size="<?php echo $sml_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Maximum opened tickets per user'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Reopen ticket within days'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="ticket_reopen_within_days" value="<?php echo $this->configuration['ticket_reopen_within_days']; ?>" class="inputbox" size="<?php echo $sml_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('The ticket can be reopened within the given number of the days'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Visitor ticket creation message'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php
                                        echo $editor->display('visitor_message', $this->configuration['visitor_message'], '550', '300', '60', '20', false);
                                    ?>
                                </div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('New ticket message'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php
                                        echo $editor->display('new_ticket_message', $this->configuration['new_ticket_message'], '550', '300', '60', '20', false);
                                    ?>
                                </div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('This message will show on the new ticket'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Allow Users To Reply via Email On Closed Ticket'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'reply_to_closed_ticket', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['reply_to_closed_ticket']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Select whether users can reply to closed tickets via email or not'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Default').' '.Text::_('ticket listing Ordering'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $ticketsorting, 'tickets_sorting', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tickets_sorting']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Select default sorting for ticket listing.'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Show Captcha To Visitor On Form Ticket'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'show_captcha_visitor_form_ticket', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['show_captcha_visitor_form_ticket']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Captcha selection'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $captchaselection, 'captcha_selection', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['captcha_selection']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Which captcha do you want to add'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Own captcha calculation type'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $owncaptchatype, 'owncaptcha_calculationtype', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['owncaptcha_calculationtype']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Select calculation type addition or subtraction'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Own captcha operands'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $owncaptchaoparend, 'owncaptcha_totaloperand', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['owncaptcha_totaloperand']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Select the total operands to be given'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Own captcha subtraction answer positive'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'owncaptcha_subtractionans', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['owncaptcha_subtractionans']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Is subtraction answer should be positive'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Enable print ticket'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'print_ticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['print_ticket_user']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Show print ticket icon on the ticket detail page to the user'); ?></small></div>
                            </div>
                        </div>
                </div>
                <div id="emialsetting" style="display: none;">
                        <legend><?php echo Text::_('Default System Emails'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Default Alert Email'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $this->lists['emails'], 'alert_email', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['alert_email']); ?>&nbsp;<a href="index.php?option=com_jssupportticket&c=email&layout=formemail"><?php echo Text::_('Add Email'); ?></a></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('If Ticket Department Email Is Not Selected Then This Email Is Used To Send Emails'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Default admin email'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $this->lists['emails'], 'admin_email', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['admin_email']); ?>&nbsp;<a href="index.php?option=com_jssupportticket&c=email&layout=formemail"><?php echo Text::_('Add Email'); ?></a></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Admin Email Address To Receive Emails'); ?></small></div>
                            </div>
                        </div>
                </div>
                <div id="usermenusetting" style="display: none;">
                        <div class="jsst-pro-tabnote">
                            <strong><?php echo Text::_('Every setting on this tab is a Pro feature.'); ?></strong>
                            <span><?php echo Text::_('The free edition has no staff module, so these menu links have no effect until you upgrade.'); ?></span>
                            <a href="index.php?option=com_jssupportticket&amp;c=jssupportticket&amp;layout=proversion&amp;feature=staff"><?php echo Text::_('See what is in Pro'); ?></a>
                        </div>
                        <legend><?php echo Text::_('Staff Members Control Panel Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Open Ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_openticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_openticket_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('My Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_myticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_myticket_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Role'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addrole_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addrole_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Roles'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_roles_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_roles_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addstaff_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addstaff_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_staff_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_staff_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Department'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_adddepartment_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_adddepartment_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Departments'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_department_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_department_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Category'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addcategory_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addcategory_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Categories'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_category_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_category_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addkb_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addkb_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_kb_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_kb_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Download'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_adddownload_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_adddownload_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Downloads'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_download_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_download_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Announcement'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addannouncement_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addannouncement_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Announcements'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_announcement_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_announcement_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add FAQ'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addfaq_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addfaq_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('FAQs'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_faq_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_faq_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Mail'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_mail_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_mail_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('My Profile'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_profile_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_profile_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Staff Reports'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_staff_report_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_staff_report_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Department Reports'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_department_report_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_department_report_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Feedbacks'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_feedback_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_feedback_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Erase Data'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_userdata_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_userdata_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Show').' '.Text::_('Ticket Total Count'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $showhide, 'cplink_totalcount_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_totalcount_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Show').' '.Text::_('Ticket Statistics'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $showhide, 'cplink_ticketstats_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_ticketstats_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Show').' '.Text::_('Latest Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $showhide, 'cplink_latesttickets_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_latesttickets_staff']); ?></div>
                            </div>
                        </div>
                        <legend><?php echo Text::_('Staff Members Top Menu Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Home'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_home_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_home_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_ticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_ticket_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_kb_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_kb_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Announcements'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_announcement_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_announcement_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Downloads'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_download_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_download_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('FAQs'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_faq_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_faq_staff']); ?></div>
                            </div>
                        </div>
                </div>
                <div id="vismenusetting" style="display: none;">
                        <legend><?php echo Text::_('User Control Panel Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Open Ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_openticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_openticket_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('My Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_myticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_myticket_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Check Ticket Status'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_checkstatus_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_checkstatus_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo jsstProCfg('Downloads'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_download_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_download_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo jsstProCfg('Announcements'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_announcement_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_announcement_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo jsstProCfg('FAQs'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_faq_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_faq_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo jsstProCfg('Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_kb_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_kb_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Erase Data Requests'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_userdata_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_userdata_user']); ?></div>
                            </div>
                        </div>
                        <legend><?php echo Text::_('User Top Menu Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Home'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_home_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_home_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Tickets'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_ticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_ticket_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo jsstProCfg('Knowledge Base'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_kb_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_kb_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo jsstProCfg('Announcements'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_announcement_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_announcement_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo jsstProCfg('Downloads'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_download_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_download_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo jsstProCfg('FAQs'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_faq_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_faq_user']); ?></div>
                            </div>
                        </div>
                </div>
                <div id="auotrespondersetting" style="display: none;">
                        <legend><?php echo Text::_('Ban email New Ticket'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo jsstProCfg('Mail to admin'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'banemail_new_ticket_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['banemail_new_ticket_admin']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Email sent to admin when banned email try to create a ticket'); ?></small></div>
                            </div>
                        </div>
                        <legend><?php echo Text::_('Ticket Operations Mail Setting'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-ticket-configuration-row-mail bgandfontcolor">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo Text::_('Admin'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo jsstProCfg('Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo Text::_('User'); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('New Ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'new_ticket_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['new_ticket_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'new_ticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['new_ticket_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Ticket reassign'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reassign_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reassign_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reassign_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reassign_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reassign_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reassign_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket close'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_close_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_close_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_close_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_close_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_close_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_close_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket delete'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_delete_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_delete_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_delete_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_delete_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_delete_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_delete_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Ticket mark overdue'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_overdue_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_overdue_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_overdue_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Ticket ban email'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_email_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_email_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_email_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_email_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_email_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_email_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Ticket Department Transfer'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_department_transfer_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_department_transfer_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_department_transfer_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_department_transfer_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_department_transfer_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_department_transfer_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket Reply User'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reply_user_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_user_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reply_user_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_user_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reply_user_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_user_user']); ?></div>
                            </div>
                            <?php // Sent to the customer when they reply to a ticket that is already
                                  // closed and replying to closed tickets is switched off, so the reply
                                  // is not silently discarded. User-only: admins are not blocked. ?>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Reply on closed ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value">&nbsp;</div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value">&nbsp;</div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reply_closed_ticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_closed_ticket_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket Response Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_response_staff_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_response_staff_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_response_staff_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_response_staff_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_response_staff_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_response_staff_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Ticket ban email and close ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_and_close_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_and_close_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_and_close_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_and_close_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_and_close_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_and_close_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Ticket unban email'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unbanemail_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unbanemail_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unbanemail_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unbanemail_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unbanemail_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unbanemail_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Ticket Lock'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_lock_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_lock_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_lock_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_lock_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_lock_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_lock_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Ticket mark in progress'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_progress_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_progress_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_progress_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_progress_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_progress_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_progress_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Ticket Unlock'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unlock_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unlock_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unlock_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unlock_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unlock_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unlock_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket Change Priority'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_priority_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_priority_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_priority_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_priority_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_priority_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_priority_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo jsstProCfg('Feedback Email To User'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value">----</div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value">----</div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_feedback_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_feedback_user']); ?></div>
                            </div>
                        </div>
                        <legend><?php echo Text::_('Erase Data'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-ticket-configuration-row-mail bgandfontcolor">
                                <div class="js-col-lg-3 js-col-md-3"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo Text::_('Admin'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo jsstProCfg('Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo Text::_('User'); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Erase request'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'erase_data_request_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['erase_data_request_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'erase_data_request_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['erase_data_request_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Delete').' '.Text::_('user data'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'delete_user_data', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['delete_user_data']); ?></div>
                            </div>
                        </div>
                </div>
                <div id="feedbacksettings" style="display: none;">
                        <div class="jsst-pro-tabnote">
                            <strong><?php echo Text::_('Every setting on this tab is a Pro feature.'); ?></strong>
                            <span><?php echo Text::_('Customer feedback is collected only in the Pro version, so these settings have no effect until you upgrade.'); ?></span>
                            <a href="index.php?option=com_jssupportticket&amp;c=jssupportticket&amp;layout=proversion&amp;feature=feedback"><?php echo Text::_('See what is in Pro'); ?></a>
                        </div>
                        <legend><?php echo Text::_('Feedback Email Settings'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Feedback Email Delay Type'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $overduetype_array, 'feedback_email_delay_type', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['feedback_email_delay_type']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Select delay type for feedback email'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Feedback Email Delay'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="feedback_email_delay" value="<?php echo $this->configuration['feedback_email_delay']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-help"><small><?php echo Text::_('Set no. of days or hours to send feedback email after the ticket is closed'); ?></small></div>
                            </div>
                        </div>
                        <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Feedback successfully stored message'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php
                                        echo $editor->display('feedback_thanks_message', $this->configuration['feedback_thanks_message'], '550', '300', '60', '20', false);
                                    ?>
                                </div>
                            </div>
                </div>
            </div>
            <input type="hidden" name="task" value="saveconf" />
            <input type="hidden" name="c" value="config" />
            <input type="hidden" name="layout" value="config" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo HTMLHelper::_( 'form.token' ); ?>
            <div class="js-form-button">
                <input type="submit" name="save" id="save" value="<?php echo Text::_('Save Configurations') ?>" class="button js-form-save" onclick="Joomla.submitbutton('saveconf');">
            </div>
        </form>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("div#generalsetting").show();
    });
    jQuery("a#generalsettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").show();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
    });
    jQuery("a#ticketsettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").show();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
    });
    jQuery("a#emialsettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").show();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
    });
    jQuery("a#usermenusettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").show();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();
    });
    jQuery("a#vismenusettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").show();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").hide();    });
    jQuery("a#auotrespondersettingbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").show();
        jQuery("div#feedbacksettings").hide();
    });
    jQuery("a#feedbacksettingsbtn").click(function () {
        jQuery('a.tab').removeClass('selected');
        jQuery(this).addClass('selected');
        jQuery("div#generalsetting").hide();
        jQuery("div#ticketsetting").hide();
        jQuery("div#emialsetting").hide();
        jQuery("div#usermenusetting").hide();
        jQuery("div#vismenusetting").hide();
        jQuery("div#auotrespondersetting").hide();
        jQuery("div#feedbacksettings").show();
    });
</script>
