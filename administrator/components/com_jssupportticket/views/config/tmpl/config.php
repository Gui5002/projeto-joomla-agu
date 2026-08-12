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

global $mainframe;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Editor\Editor;

$document = Factory::getDocument();

if (JVERSION < 3) {
    HTMLHelper::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} else {
    HTMLHelper::_('bootstrap.framework');
    HTMLHelper::_('jquery.framework');
}

$captchaselection = array(
    array('value' => '1', 'text' => Text::_('Google Recaptcha')),
    array('value' => '2', 'text' => Text::_('Own Captcha'))
);
$owncaptchaoparend = array(
    array('value' => '2', 'text' => Text::_('2')),
    array('value' => '3', 'text' => Text::_('3'))
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
        'text' => Text::_('Yes')),
    '1' => array('value' => '0',
        'text' => Text::_('No')),);


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
                        <li><?php echo Text::_('Configurations'); ?></li>
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
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text">
                <?php echo Text::_('Configurations'); ?>
            </h1>
        </div>
        <?php
			$adminEmail = JSSupportTicketModel::getJSModel('email')->getEmailById($this->configuration['admin_email']);
			$ticketviaemailaddress = $this->configuration['tve_emailaddress'];
			if($adminEmail == $ticketviaemailaddress){
        ?>
			<div id="js-emailsame-error">
				<?php echo Text::_('Admin email address and ticket via email (email address) cannot be same, your ticket via email will not be work.'); ?>
			</div>
        <?php } ?>
        <form action="index.php" class="jsstadmin-data-wrp jsstadmin-bg-color js-ticket-box-shadow" method="POST" name="adminForm" id="adminForm">
            <div id="tabs_wrapper" class="tabs_wrapper js-col-lg-12 js-col-md-12">
                <div class="idTabs">
                    <span><a id="generalsettingbtn" class="tab selected" ><?php echo Text::_('General Settings'); ?></a></span>
                    <span><a id="ticketsettingbtn" class="tab"><?php echo Text::_('Ticket Settings'); ?></a></span>
                    <span><a id="emialsettingbtn" class="tab"><?php echo Text::_('Default System Email'); ?></a></span>
                    <span><a id="auotrespondersettingbtn" class="tab"><?php echo Text::_('Mail Settings'); ?></a></span>
                    <span><a id="usermenusettingbtn" class="tab"><?php echo Text::_('Staff Menu Settings'); ?></a></span>
                    <span><a id="vismenusettingbtn" class="tab"><?php echo Text::_('Menu Settings'); ?></a></span>
                    <span><a id="feedbacksettingsbtn" class="tab"><?php echo Text::_('Feedback Settings'); ?></a></span>
                </div>
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
                                 <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('You need to rename the existing data directory in the file system before changing the data directory name'); echo '<br/><b>"'.JPATH_SITE.$this->configuration['data_directory'].'"</b>'; ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Date Format'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><?php echo $date_format; ?></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Control Panel Column'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="controlpanel_column_count" value="<?php echo $this->configuration['controlpanel_column_count']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Columns'); ?><br><?php echo Text::_('Values Between 1 T0 12 Default Is 3'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row js-mg-bottom">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Ticket auto-close'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="ticket_auto_close_indays" value="<?php echo $this->configuration['ticket_auto_close_indays']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Days'); ?><br><?php echo Text::_('Ticket auto-close if the user does not respond within the given number of days'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('No. of attachment'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="noofattachment" value="<?php echo $this->configuration['noofattachment']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><br clear="all"/><small><?php echo Text::_('No. of attachment allowed at a time'); ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('File maximum size'); ?></div>
                                <div class="js-col-lg-8 js-col-md-8 js-config-value"><input type="text" name="filesize" value="<?php echo $this->configuration['filesize']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /> &nbsp;KB</div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('File extension'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><textarea name="fileextension" cols="25" rows="3" class="inputbox"><?php echo $this->configuration['fileextension']; ?></textarea></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('File extensions allowed to attach') ?></small></div>
                            </div>
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Breadcrumbs'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo $curlocation; ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><small><?php echo Text::_('Show hide breadcrumbs'); ?></small></div>
                            </div>
                            <?php /*
                            <div class="js-ticket-configuration-row js-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Top Header'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $showhide, 'show_header', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['show_header']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><small><?php echo Text::_('Show hide top header'); ?></small></div>
                            </div>
                            */ ?>
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
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Visitors can create ticket'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'visitor_can_create_ticket', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['visitor_can_create_ticket']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Can visitors create tickets or not'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Ticket id sequence'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $ticketidsequence, 'ticketid_sequence', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticketid_sequence']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Set ticket id sequential or random'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Maximum tickets'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="maximum_ticket" value="<?php echo $this->configuration['maximum_ticket']; ?>" class="inputbox" size="<?php echo $sml_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Maximum tickets per user'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Reopen ticket within days'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="ticket_reopen_within_days" value="<?php echo $this->configuration['ticket_reopen_within_days']; ?>" class="inputbox" size="<?php echo $sml_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('The ticket can be reopened within the given number of the days'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Visitor ticket creation message'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php
                                        $conf   = Factory::getConfig();
                                        $editor = Editor::getInstance($conf->get('editor')); 
                                        echo $editor->display('visitor_message', $this->configuration['visitor_message'], '550', '300', '60', '20', false);
                                    ?>
                                </div>
                            </div>
                            
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('New ticket message'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php
                                        $conf   = Factory::getConfig();
                                        $editor = Editor::getInstance($conf->get('editor')); 
                                        echo $editor->display('new_ticket_message', $this->configuration['new_ticket_message'], '550', '300', '60', '20', false);
                                    ?>
                                </div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('This message will show on the new ticket'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Maximum open tickets'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="ticket_per_email" value="<?php echo $this->configuration['ticket_per_email']; ?>" class="inputbox" size="<?php echo $sml_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Maximum opened tickets per user'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Captcha selection'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $captchaselection, 'captcha_selection', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['captcha_selection']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Which captcha do you want to add'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Own captcha calculation type'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $owncaptchatype, 'owncaptcha_calculationtype', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['owncaptcha_calculationtype']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Select calculation type addition or subtraction'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Own captcha operands'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $owncaptchaoparend, 'owncaptcha_totaloperand', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['owncaptcha_totaloperand']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Select the total operands to be given'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Own captcha subtraction answer positive'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'owncaptcha_subtractionans', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['owncaptcha_subtractionans']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Is subtraction answer should be positive'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Enable print ticket'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'print_ticket_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['print_ticket_user']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Show print ticket icon on the ticket detail page to the user'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Show Captcha To Visitor On Form Ticket'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'show_captcha_visitor_form_ticket', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['show_captcha_visitor_form_ticket']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"></div>
                            </div>
                        </div>
                </div>
                <div id="emialsetting" style="display: none;">
                        <legend><?php echo Text::_('Default System Emails'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Default Alert Email'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $this->lists['emails'], 'alert_email', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['alert_email']); ?>&nbsp;<a href="index.php?option=com_jssupportticket&c=email&layout=formemail"><?php echo Text::_('Add Email'); ?></a></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('If Ticket Department Email Is Not Selected Then This Email Is Used To Send Emails'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Default admin email'); ?></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $this->lists['emails'], 'admin_email', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['admin_email']); ?>&nbsp;<a href="index.php?option=com_jssupportticket&c=email&layout=formemail"><?php echo Text::_('Add Email'); ?></a></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Admin Email Address To Receive Emails'); ?></small></div>
                            </div>
                        </div>
                </div>
                <div id="usermenusetting" style="display: none;">
                        <legend><?php echo Text::_('Staff Members Control Panel Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Open Ticket'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_openticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_openticket_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('My Tickets'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_myticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_myticket_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Role'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addrole_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addrole_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Roles'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_roles_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_roles_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Staff'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addstaff_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addstaff_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Staff'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_staff_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_staff_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Department'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_adddepartment_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_adddepartment_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Departments'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_department_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_department_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Category'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addcategory_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addcategory_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Categories'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_category_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_category_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Knowledge Base'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addkb_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addkb_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Knowledge Base'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_kb_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_kb_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Download'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_adddownload_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_adddownload_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Downloads'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_download_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_download_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add Announcement'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addannouncement_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addannouncement_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Announcements'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_announcement_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_announcement_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Add FAQ'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_addfaq_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_addfaq_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('FAQs'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_faq_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_faq_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Mail'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_mail_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_mail_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('My Profile'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_profile_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_profile_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Staff Reports'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_staff_report_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_staff_report_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Department Reports'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_department_report_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_department_report_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Feedbacks'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_feedback_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_feedback_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Erase Data'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_userdata_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_userdata_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Show').' '.Text::_('Ticket Total Count'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $showhide, 'cplink_totalcount_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_totalcount_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Show').' '.Text::_('Ticket Statistics'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $showhide, 'cplink_ticketstats_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_ticketstats_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Show').' '.Text::_('Latest Tickets'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $showhide, 'cplink_latesttickets_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_latesttickets_staff']); ?></div>
                            </div>
                        </div>
                        <legend><?php echo Text::_('Staff Members Top Menu Links'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Home'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_home_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_home_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Tickets'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_ticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_ticket_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Knowledge Base'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_kb_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_kb_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Announcements'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_announcement_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_announcement_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Downloads'); ?><span class="js-config-pro">*</span>    </div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_download_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_download_staff']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('FAQs'); ?><span class="js-config-pro">*</span></div>
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
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Check Ticket Status'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_checkstatus_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_checkstatus_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Downloads'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_download_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_download_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Announcements'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_announcement_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_announcement_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Knowledge Base'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_kb_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_kb_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('FAQs'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'cplink_faq_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['cplink_faq_user']); ?></div>
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
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Knowledge Base'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_kb_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_kb_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Announcements'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_announcement_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_announcement_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('Downloads'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_download_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_download_user']); ?></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-3 js-col-md-3 js-config-title"><?php echo Text::_('FAQs'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-config-value"><?php echo HTMLHelper::_('select.genericList', $yesno, 'tplink_faq_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['tplink_faq_user']); ?></div>
                            </div>
                        </div>
                </div>
                <div id="auotrespondersetting" style="display: none;">
                        <legend><?php echo Text::_('Ban email New Ticket'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Mail to admin'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'banemail_new_ticket_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['banemail_new_ticket_admin']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Email sent to admin when banned email try to create a ticket'); ?></small></div>
                            </div>
                        </div>
                        <legend><?php echo Text::_('Ticket Operations Mail Setting'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-ticket-configuration-row-mail bgandfontcolor">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo Text::_('Admin'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo Text::_('Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo Text::_('User'); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('New Ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'new_ticket_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['new_ticket_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'new_ticket_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['new_ticket_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket reassign'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reassign_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reassign_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reassign_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reassign_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reassign_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reassign_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket close'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_close_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_close_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_close_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_close_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_close_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_close_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket delete'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_delete_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_delete_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_delete_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_delete_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_delete_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_delete_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket mark overdue'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_overdue_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_overdue_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_overdue_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket ban email'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_email_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_email_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_email_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_email_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_email_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_email_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket Department Transfer'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_department_transfer_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_department_transfer_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_department_transfer_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_department_transfer_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_department_transfer_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_department_transfer_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket Reply User'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reply_user_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_user_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reply_user_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_user_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_reply_user_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_reply_user_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket Response Staff'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_response_staff_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_response_staff_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_response_staff_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_response_staff_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_response_staff_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_response_staff_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket ban email and close ticket'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_and_close_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_and_close_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_and_close_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_and_close_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_ban_and_close_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_ban_and_close_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket unban email'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unbanemail_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unbanemail_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unbanemail_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unbanemail_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unbanemail_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unbanemail_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket Lock'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_lock_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_lock_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_lock_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_lock_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_lock_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_lock_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket mark in progress'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_progress_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_progress_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_progress_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_progress_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_progress_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_progress_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket Unlock'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unlock_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unlock_admin']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unlock_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unlock_staff']); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_unlock_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_unlock_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Ticket Change Priority'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_priority_admin', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_priority_admin']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_priority_staff', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_priority_staff']); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_priority_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_priority_user']); ?></div>
                            </div>
                            <div class="js-ticket-configuration-row-mail">
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-title"><?php echo Text::_('Feedback Email To User'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value">----</div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value">----</div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-value"><?php echo HTMLHelper::_('select.genericList', $enableddisabled, 'ticket_feedback_user', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_feedback_user']); ?><span class="js-config-pro">*</span></div>
                            </div>
                        </div>
                        <legend><?php echo Text::_('Erase Data'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-ticket-configuration-row-mail bgandfontcolor">
                                <div class="js-col-lg-3 js-col-md-3"></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo Text::_('Admin'); ?></div>
                                <div class="js-col-lg-3 js-col-md-3 js-configuration-text"><?php echo Text::_('Staff'); ?></div>
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
                        <legend><?php echo Text::_('Feedback Email Settings'); ?></legend>
                        <div class="js-row js-null-margin">
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Feedback Email Delay Type'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><?php echo HTMLHelper::_('select.genericList', $overduetype_array, 'feedback_email_delay_type', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['feedback_email_delay_type']); ?></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Select delay type for feedback email'); ?></small></div>
                            </div>
                            <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Feedback Email Delay'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value"><input type="text" name="feedback_email_delay" value="<?php echo $this->configuration['feedback_email_delay']; ?>" class="inputbox" size="<?php echo $med_field_width; ?>" /></div>
                                <div class="js-col-lg-4 js-col-md-4"><small><?php echo Text::_('Set no. of days or hours to send feedback email after the ticket is closed'); ?></small></div>
                            </div>
                        </div>
                        <div class="js-row js-ticket-configuration-row">
                                <div class="js-col-lg-4 js-col-md-4 js-config-title"><?php echo Text::_('Feedback successfully stored message'); ?><span class="js-config-pro">*</span></div>
                                <div class="js-col-lg-4 js-col-md-4 js-config-value">
                                    <?php
                                        $conf   = Factory::getConfig();
                                        $editor = Editor::getInstance($conf->get('editor'));
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
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>

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
