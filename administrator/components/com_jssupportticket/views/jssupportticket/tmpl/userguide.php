<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

?>

<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="js-tk-heading"><h4><img id="js-admin-responsive-menu-link" src="components/com_jssupportticket/include/images/c_p/left-icons/menu.png" /><?php echo Text::_('JS_USER_GUIDE'); ?></h4></div> 
        <table width="100%" class="adminlist">
            <tr>
                <td>
                    <h1>User Guide</h1>
                    Go through by the following steps.
                    <h3><u>System Emails</u></h3>
                    System Emails are those emails which is used to send emails to <b>"Ticket Users"</b>, <b>"Staff Member"</b>
                    and one email is for the Administrator email which is used to receive to the emails from the 
                    <b>"JS Tickets"</b> so can add atleast one email in system emails.
                    After Adding system email goto the configuration and set the administrator and default email.
                    <h3><u>Groups</u></h3>
                    Groups are used for the rights of the staff members, Like they can create, delete and edit ticket
                    and so on, Also groups are used to create Group of Department. It is used for these two reason.
                    So you have to create some groups for your system.
                    <h3><u>Departments</u></h3>
                    Departments as the word describe it self categories of a company such as Billing, Support and etc
                    here we can use it in the same sense. With some <b>"Auto-Responder"</b> setting and <b>"Outing Email"</b>
                    Outing email is used to send the email against Ticket actions related to this department, Auto-Responder is used to auto-response against
                    the Ticket related to this department
                    <h3><u>Help Topics</u></h3>
                    Help Topics are subcategories of department such as if there is a Billing Department then the Help Topics of
                    Billing departments are <b>"Payout, Cash"</b> and etc, Also there is <b>"Auto Response"</b> setting this setting is 
                    for i-e if a department set to no auto response for ticket but the one help topic of this department will auto response.
                    <h3><u>Staff</u></h3>
                    Staff member are the member of <b>"JS Ticket"</b> system which handle the ticket they were created according to the group
                    and department, By default they can see the all of ticket of thier department and will perform action according to the 
                    group right's which they are assign, such as close, create, edit ticket and so on right's are selected when you create the
                    group.
                    <h3><u>Configurations</u></h3>
                    Configurations have main five tabs.<br/>1.<b><u>General Setting</u></b><br/>Where you can set the title to show on front end offline mode
                    as well offline message, Data directory name where files were saved which were uploaded by User or staff member or admin, Date format, File size,
                    File extension, Theme, Current location to show on front end, Ticket Overdue setting and No. of attachments.<br/>2.<b><u>Ticket Setting</u></b><br/>
                    In ticket setting you can set the no of ticket submitted by using email address, Reopen close ticket within days, Staff can lock ticket, Visitor can create
                    ticket, Show Captcha if visitor come to submit ticket And Staff member name show on ticket replies.<br/>3.<b><u>Default Email Setting</u></b><br/>You can set
                    the default alert email which is used to send to emails against ticket action And also you can set the Default Admin Email to receive the email from <b>JS Tickets</b>.
                    <br/>4.<b><u>Knowledgebase</u></b><br/>Here you can set to show knowledgebase on front end or not by enable setting and also set rights for Staff member to create 
                    knowledgebase categories and article these setting are global for all Staff members. Also you can set how many article show on one row in front end.
                    <br/>5.<b><u>Mail Setting</u></b><br/>Where you can set Admin can recevie email for every new ticket submit, Email can send if the staff member can submit ticket
                    , Also you can set Admin recevie email if banned email try to submit new ticket and also you can set the setting for email against ticket actions that who can receive
                    email.
                    <h3><u>Tickets</u></h3>
                    Here Admin can manage all ticket which were submitted, Ticket listing is categroiesed in five parts Open Tickets, Answered Tickets, Overdue Tickets, Closed Ticket And My Tickets
                    <br/> Here Admin can create custom user field if Admin can release that one more fields were miss in the form than he/she can create custom user field and also they can manage the
                    ordering of fields with their requirments.
                    <h3><u>Mail</u></h3>
                    Mail is designed for the conversation between the staff member's of <b>"JS Tickets"</b>.
                    <h3><u>Baned Email</u></h3>
                    Baned email are those email which were baned by admin or staff member, Baned email are not able to submit a new ticket untill they were
                    unbaned.
                    <h3><u>Banlist Log</u></h3>
                    Once the email is baned after that if they try to submit new ticket then the log is created automatically in which the logger name email address
                    and ip address were saved in log list.
                    <h3><u>Email Templates</u></h3>
                    Each email were send to either <b>"Admin, Staff Member OR User"</b> have their appropiate template with some defined paramater for the convenience 
                    to manage the template for your requirements.
                    <h3><u>Knowledgebase</u></h3>
                    Knowledgebase have categories and article so you can manage it easily by creating some categories and also nested categories and articles related to
                    categroies. Categories and Article can be created by <b>"Admin Or Staff Member"</b>
                    <h3><u>System Errors</u></h3>
                    System errors are those errors which generate during the any storage process. If any error is generated then the log of system error is stored in system
                    errors.
                    <br/>
                    <div style="float:right;">Thanks for reading the guide, Hope this will useful for you.</div>
                </td>
            </tr>
        </table>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
