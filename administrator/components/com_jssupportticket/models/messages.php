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
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');
use Joomla\CMS\Language\Text;

class JSSupportticketMessage{

    public static $recordid;
    function __construct(){
    }

    public static function getMessage($result,$entity){
        $msg = Text::_('Unknown');
        $entity =  getJSTicketPHPFunctionsClass()->jsticket_strtoupper ($entity);
        switch ($result) {
            case POSTED:
                switch ($entity) {
                    case 'REPLY':$msg = Text::_('Reply has been posted');break;
                    case 'MESSAGE':$msg = Text::_('Mail has been Send');break;
                    case 'INTERNAL_NOTE':$msg = Text::_('Internal Note Has Been Posted');break;
                }
            break;
            case POST_ERROR:
                switch ($entity) {
                    case 'REPLY':$msg = Text::_('Reply has not been stored');break;
                    case 'MESSAGE':$msg = Text::_('Mail has not been send');break;
                    case 'INTERNAL_NOTE':$msg = Text::_('Internal Note has not been posted');break;
                }
            break;
            case TICKET_TRANSFERED:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = Text::_('Department has been transfered');break;
                    case 'STAFF':$msg = Text::_('Assigned to Staff');break;
                }
            break;
            case TICKET_TRANSFER_ERROR:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = Text::_('Department has not been transfered');break;
                    case 'STAFF':$msg = Text::_('Not assigned to staff');break;
                }
            break;
            case PRIORITY_CHANGED:$msg = Text::_('Priority has been changed');break;
            case PRIORITY_CHANGE_ERROR:$msg = Text::_('Priority has not been changed');break;
            case OTHER_USER_TASK:$msg = Text::_('You Are Not Allowed');break;
            case TICKET_ACTION_OK:
                switch ($entity) {
                    case 'CLOSE':$msg = Text::_('Ticket has been closed');break;
                    case 'REOPEN':$msg = Text::_('Ticket has been reopened');break;
                    case 'MARK_IN_PROGRESS':$msg = Text::_('Ticket has been marked as in progress');break;
                    case 'DELETE':$msg = Text::_('Ticket delete');break;
                    case 'MARK_OVERDUE':$msg = Text::_('Ticket has been marked as overdue');break;
                    case 'LOCK':$msg = Text::_('Ticket has been locked');break;
                    case 'UNLOCK':$msg = Text::_('Ticket has been unlocked');break;
                }
            break;
            case TICKET_ACTION_ERROR:
                switch ($entity) {
                    case 'ClOSE':$msg = Text::_('Ticket has not been closed');break;
                    case 'REOPEN':$msg = Text::_('Ticket has not been reopened');break;
                    case 'MARK_IN_PROGRESS':$msg = Text::_('Ticket has not been marked as in progress');break;
                    case 'DELETE':$msg = Text::_('Ticket has not been deleted');break;
                    case 'Mark Overdue':$msg = Text::_('Ticket has not been marked as overdue');break;
                    case 'LOCK':$msg = Text::_('Ticket has not been locked');break;
                    case 'UNLOCK':$msg = Text::_('Ticket has not been unlocked');break;
                }
            break;
            case FILE_SIZE_ERROR:$msg = Text::_('Error File Size Too Large');break;
            case FILE_EXTENTION_ERROR:$msg = Text::_('Error file ext mismatch');break;
            case TIME_LIMIT_END:$msg = Text::_('Ticket reopen time limit end');break;
            case EMAIL_NOT_EXIST:$msg = Text::_('Ban email does not exist');break;
            case INVALID_CAPTCHA:$msg = Text::_('Incorrect Captcha code');break;
            case LIMIT_EXCEED:$msg = Text::_('Limit exceeds maximum tickets');break;
            case LIMIT_EXCEED_OPEN:$msg = Text::_('Limit exceeds maximum open tickets');break;
            case FILE_RW_ERROR:
                $msg = Text::_('Files cannot be upload due to read write permissions');
            break;
            case MAIL_MARKED:
                switch ($entity) {
                    case 'READ':$msg = Text::_('Mail mark as read');break;
                    case 'UNREAD':$msg = Text::_('Mail mark as unread');break;
                }
            break;
            case MAIL_MARKED_ERROR:
                switch ($entity) {
                    case 'READ':$msg = Text::_('Mail cannot mark as read');break;
                    case 'UNREAD':$msg = Text::_('Mail cannot mark as unread');break;
                }
            break;
            case SENT_ERROR:
                switch ($entity) {
                    case 'MAIL':$msg = Text::_('Mail has not been send');break;
                    case 'MESSAGE':$msg = Text::_('Mail has not been send');break;
                    case 'REPLY':$msg = Text::_('Reply has not been posted');break;
                }
            break;
            case SENT:
                switch ($entity) {
                    case 'MAIL':$msg = Text::_('Mail has been send');break;break;
                    case 'MESSAGE':$msg = Text::_('Mail has been send');break;break;
                    case 'REPLY':$msg = Text::_('Reply has been posted');break;break;
                }
            case SET_DEFAULT:
                switch ($entity) {
                    case 'PRIORITY':$msg = Text::_('Priority has been make default');break;
                }
            break;
            case SET_DEFAULT_ERROR:
                switch ($entity) {
                    case 'PRIORITY':$msg = Text::_('Priority has not been make default');break;
                }
            break;
            case SAVED:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = Text::_('Department has been stored');break;
                    case 'EMAIL':$msg = Text::_('The email has been stored');break;
                    case 'EMAIL_TEMPLATE':$msg = Text::_('Email template has been stored');break;
                    case 'PRIORITY':$msg = Text::_('Priority has been stored');break;
                    case 'TICKET':$msg = Text::_('Ticket has been created');break;
                    case 'USER_FIELD':$msg = Text::_('Field has been stored');break;
                    case 'MESSAGE':$msg = Text::_('Reply has been posted');break;
                    case 'ANNOUNCEMENT':$msg = Text::_('Announcement has been stored');break;
                    case 'DEPARTMENT':$msg = Text::_('Department has been stored');break;
                    case 'DOWNLOAD':$msg = Text::_('Download has been stored');break;
                    case 'EMAIL':$msg = Text::_('The email has been stored');break;
                    case 'BAN_EMAIL':$msg = Text::_('Ban Email has been stored');break;
                    case 'EMAIL_TEMPLATE':$msg = Text::_('Email template has been stored');break;
                    case 'FAQ':$msg = Text::_('FAQ has been stored');break;
                    case 'HELP_TOPIC':$msg = Text::_('Help topic has been stored');break;
                    case 'CATEGORY':$msg = Text::_('Category has been stored');break;
                    case 'ARTICLE':$msg = Text::_('Knowledge base article has been stored');break;
                    case 'DEPARTMENT_PREMADE':$msg = Text::_('Premade department message has been stored');break;
                    case 'ROLE':$msg = Text::_('The role has been stored');break;
                    case 'STAFF':$msg = Text::_('Staff member has been stored');break;
                    case 'GDPR':$msg = Text::_('Erase data request has been submitted');break;
                }
            break;
            case SAVE_ERROR:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = Text::_('Department has not been stored');break;
                    case 'EMAIL':$msg = Text::_('Email has not been stored');break;
                    case 'EMAIL_TEMPLATE':$msg = Text::_('Email template has not been stored');break;
                    case 'PRIORITY':$msg = Text::_('Priority has not been stored');break;
                    case 'TICKET':$msg = Text::_('Ticket has not been stored');break;
                    case 'USER_FIELD':$msg = Text::_('User field has not been stored');break;
                    case 'MESSAGE':$msg = Text::_('Mail has not been send');break;
                    case 'ANNOUNCEMENT':$msg = Text::_('Announcement has not been stored');break;
                    case 'DEPARTMENT':$msg = Text::_('Department has not been stored');break;
                    case 'DOWNLOAD':$msg = Text::_('Download has not been stored');break;
                    case 'EMAIL':$msg = Text::_('Email has not been stored');break;
                    case 'BAN_EMAIL':$msg = Text::_('Ban Email has not been stored');break;
                    case 'EMAIL_TEMPLATE':$msg = Text::_('Email template has not been stored');break;
                    case 'FAQ':$msg = Text::_('FAQ has not been stored');break;
                    case 'HELP_TOPIC':$msg = Text::_('Help topic has not been stored');break;
                    case 'CATEGORY':$msg = Text::_('Category has not been stored');break;
                    case 'ARTICLE':$msg = Text::_('Knowledge base article has not been stored');break;
                    case 'DEPARTMENT_PREMADE':$msg = Text::_('Premade department message has not been stored');break;
                    case 'ROLE':$msg = Text::_('Role has not been stored');break;
                    case 'STAFF':$msg = Text::_('Staff member has not been stored');break;
                    case 'GDPR':$msg = Text::_('Erase data request has not been submitted');break;
                }
            break;
            case ALREADY_EXIST:
                switch ($entity) {
                    case 'BAN_EMAIL':$msg = Text::_('Email already banned');break;
                    case 'STAFF':$msg = Text::_('User already staff member');break;
                }
            break;
            case DELETED:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = Text::_('Department has been deleted');break;
                    case 'EMAIL':$msg = Text::_('Email has been deleted');break;
                    case 'PRIORITY':$msg = Text::_('Priority has been deleted');break;
                    case 'TICKET':$msg = Text::_('Ticket has been deleted');break;
                    case 'USER_FIELD':$msg = Text::_('User field has been deleted');break;
                    case 'MESSAGE':$msg = Text::_('Mail Has Been Deleted');break;
                    case 'ANNOUNCEMENT':$msg = Text::_('Announcement has been deleted');break;
                    case 'DEPARTMENT':$msg = Text::_('Department has been deleted');break;
                    case 'DOWNLOAD':$msg = Text::_('Download has been deleted');break;
                    case 'EMAIL':$msg = Text::_('Email has been deleted');break;
                    case 'BAN_EMAIL':$msg = Text::_('Ban email has been deleted');break;
                    case 'FAQ':$msg = Text::_('FAQ has been deleted');break;
                    case 'HELP_TOPIC':$msg = Text::_('Help topic has been deleted');break;
                    case 'CATEGORY':$msg = Text::_('Category has been deleted');break;
                    case 'ARTICLE':$msg = Text::_('Article has been deleted');break;
                    case 'MAIL':$msg = Text::_('Mail has been deleted');break;
                    case 'DEPARTMENT_PREMADE':$msg = Text::_('Premade department message has been deleted');break;
                    case 'ROLE':$msg = Text::_('The role has been deleted');break;
                    case 'STAFF':$msg = Text::_('Staff member has been deleted');break;
                    case 'GDPR':$msg = Text::_('Erase data request has been deleted');break;
                    case 'ERASED':$msg = Text::_('User identifying data has been erased');break;
                }
            break;
            case DELETE_ERROR:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = Text::_('Department has not been deleted');break;
                    case 'EMAIL':$msg = Text::_('Email has not been deleted');break;
                    case 'PRIORITY':$msg = Text::_('Priority has not been deleted');break;
                    case 'TICKET':$msg = Text::_('Ticket has not been deleted');break;
                    case 'USER_FIELD':$msg = Text::_('User field has not been deleted');break;
                    case 'MESSAGE':$msg = Text::_('Mail has not been deleted');break;
                    case 'ANNOUNCEMENT':$msg = Text::_('Announcement has not been deleted');break;
                    case 'DEPARTMENT':$msg = Text::_('Department has not been deleted');break;
                    case 'DOWNLOAD':$msg = Text::_('Download has not been deleted');break;
                    case 'EMAIL':$msg = Text::_('Email has not been deleted');break;
                    case 'BAN_EMAIL':$msg = Text::_('Ban Email has not been deleted');break;
                    case 'FAQ':$msg = Text::_('FAQ has not been deleted');break;
                    case 'HELP_TOPIC':$msg = Text::_('Help topic has not been deleted');break;
                    case 'CATEGORY':$msg = Text::_('Category has not been deleted');break;
                    case 'ARTICLE':$msg = Text::_('Article has not been deleted');break;
                    case 'MAIL':$msg = Text::_('Mail has not been deleted');break;
                    case 'DEPARTMENT_PREMADE':$msg = Text::_('Premade department message has not been deleted');break;
                    case 'ROLE':$msg = Text::_('Role has not been deleted');break;
                    case 'STAFF':$msg = Text::_('Staff member has not been deleted');break;
                    case 'GDPR':$msg = Text::_('Erase data request has not been deleted');break;
                }
            break;
            case IN_USE:
                switch ($entity) {
                    case 'DEPARTMENT':$msg = Text::_('Department in use cannot be deleted');break;
                    case 'CATEGORY':$msg = Text::_('Category in use cannot deleted');break;
                    case 'STAFF':$msg = Text::_('Staff member in use cannot deleted');break;
                    case 'TICKET':$msg = Text::_('Ticket in use cannot be deleted');break;
                    case 'USER_FIELD':$msg = Text::_('User field in use cannot deleted');break;
                }
            break;
            case CANCEL:$msg = Text::_('Operation Cancel');break;
            case PERMISSION_ERROR:$msg = Text::_('You Are Not Allowed');break;
            case BANNED_EMAIL:$msg = Text::_('Banned email cannot create ticket');break;
            case DUE_DATE_ERROR:$msg = Text::_('Due Date Error Is Not Valid');break;
            case TICKET_EMAIL_BAN_ERROR:$msg = Text::_('Email has not been banned');break;
            case TICKET_EMAIL_BAN:$msg = Text::_('Email has been banned');break;
            case TICKET_EMAIL_UNBAN_ERROR:$msg = Text::_('Email has not been unbanned');break;
            case TICKET_EMAIL_UNBAN:$msg = Text::_('Email has been unbanned');break;
            case MESSAGE_EMPTY: $msg = Text::_('Message field cannot be empty'); break;
            case TICKET_DUPLICATE: $msg = Text::_('Duplicate Ticket not allowed.'); break;
        }
        return $msg;
    }
}
?>
