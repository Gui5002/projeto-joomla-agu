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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

class JSSupportticketModelEmail extends JSSupportTicketModel {

    function __construct(){
        parent::__construct();
    }

    function getFormData($id) {

        $db = $this->getDbo();
        $email;
        if (isset($id)) {
            if (!is_numeric($id))
                return False;
            $query = "SELECT * FROM `#__js_ticket_email` WHERE id =" . $id;
            $db->setQuery($query);
            $email = $db->loadObject();
        }
        $priority = $this->getJSModel('priority')->getPrioritiesForCombobx(Text::_('Select Priority'));
        $config = $this->getJSModel('config')->getConfigByFor('default');

        if (isset($email)) {
            $priorityid = $email->priorityid;
            $lists['priority'] = HTMLHelper::_('select.genericList', $priority, 'priorityid', 'class="inputbox" ' . '', 'value', 'text', $priorityid);
        } else {
            $priorityid = '';
            $lists['priority'] = HTMLHelper::_('select.genericList', $priority, 'priorityid', 'class="inputbox" ' . '', 'value', 'text', $config['priority']);
        }

        $result[0] = $email;
        $result[1] = '';
        $result[2] = $lists;

        return $result;
    }

    function storeEmail($data) {
        $row = $this->getTable('emails');
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        try
        {
            $row->store();
        }
        catch (RuntimeException $e)
        {
            $this->getJSModel('systemerrors')->updateSystemErrors($e);
            $this->setError($e);
            return SAVE_ERROR;
        }
        JSSupportticketMessage::$recordid = $row->id;
        return SAVED;
    }

    function getAllEmails($searchemail, $searchtype, $limitstart, $limit) {
        $type[] = array('value' => null, 'text' => Text::_('Select Email'));
        $type[] = array('value' => 1, 'text' => Text::_('Yes'));
        $type[] = array('value' => 0, 'text' => Text::_('No'));
        $lists['autoresponcetype'] = HTMLHelper::_('select.genericList', $type, 'filter_autoresponcetype', 'class="inputbox" ' . '', 'value', 'text', $searchtype);
        $db = $this->getDbo();
        //For Total Record
        $query = "SELECT COUNT(id) From `#__js_ticket_email`";
        $db->setQuery($query);
        $total = $db->loadResult();

        $query = "SELECT email.id, email.email, email.autoresponce, email.created, email.update,priority.priority
                    FROM `#__js_ticket_email` AS email
                    LEFT JOIN `#__js_ticket_priorities`AS priority ON priority.id=email.priorityid
                    WHERE email.status <> -1 ";
        if (isset($searchemail) && $searchemail <> ''){
            $searchemail = getJSTicketPHPFunctionsClass()->jsticket_trim($searchemail);
            $query .= " AND email.email LIKE " . $db->quote('%' . $searchemail . '%');
        }
        if (isset($searchtype) && $searchtype <> '') {
            if (!is_numeric($searchtype))
                return False;
            $query .= " AND email.autoresponce =" . $searchtype;
        }
        $db->setQuery($query, $limitstart, $limit);
        $emails = $db->loadObjectList();
        if ($searchemail)
            $lists['searchemail'] = $searchemail;
        $result[0] = $emails;
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function deleteEmail() {
        $row = $this->getTable('emails');
        $c_id = Factory::getApplication()->input->get('cid', array(0), '', 'array');
        foreach ($c_id as $id) {
            if ($this->emailCanDelete($id) == true) {
                if (!$row->delete($id)) {
                    $this->setError($row->getError());
                    return DELETE_ERROR;
                }
            }
        }
        return DELETED;
    }

    function emailCanDelete($id) {
        if (!is_numeric($id)) return FALSE;
        return true;
    }

    function getEmailForCombobox($title = ''){
        $db= $this->getDbo();
        $query="SELECT id, email FROM `#__js_ticket_email` WHERE status = 1 ORDER BY email ASC";
        try{
            $db->setQuery($query);
            $rows=$db->loadObjectList();
            $emaillist = array();
            if($title)
                $emaillist[]=array('value'=>'','text'=>$title);
            foreach ($rows as $row) {
                $emaillist[]=array('value'=>$row->id,'text'=>$row->email);
            }
            return $emaillist;
        }
        catch (RuntimeException $e){
            $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
            return false;
        }
    }

    function sendMail($mailfor, $action, $id = null, $tablename = null) {
        if (!is_numeric($mailfor)) return false;
        if (!is_numeric($action)) return false;
        if ($id != null) if (!is_numeric($id)) return false;
        $config = $this->getJSModel('config')->getConfigs();
        switch ($mailfor) {
            case 1: // Mail For Tickets
                switch ($action) {
                    case 1: // New Ticket Created
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $message = $ticket->message;
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{MESSAGE}' => $message,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;

                        // New ticket mail to User
                        $template = $this->getTemplateForEmail('ticket-new');
						//Parsing template
						$msgSubject = $template->subject;
						$msgBody = $template->body;
						$link = $this->setGuestUrl($trackingid,$email);
						$matcharray['{TICKETURL}'] = $link;
						$this->replaceMatches($msgSubject, $matcharray);
						$this->replaceMatches($msgBody, $matcharray);
						$msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###" />';
						$this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        // New ticket mail to admin
                        if ($config['new_ticket_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $adminName = $this->getJoomlaNameByEmail($adminEmail);
                            $template = $this->getTemplateForEmail('ticket-new-admin');
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $matcharray['{USERNAME}'] = $adminName;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 2: // Close Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $message = $ticket->message;
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{MESSAGE}' => $message,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('close-tk');
                        // Close ticket mail to admin
                        if ($config['ticket_close_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_close_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 3: // Delete Ticket
                        $session = Factory::getApplication()->getSession();
                        $trackingid = $session->get('ticketid');
                        $email = $session->get('ticketemail');
                        $subject = $session->get('ticketsubject');
                        $matcharray = array(
                            '{TRACKINGID}' => $trackingid,
                            '{SUBJECT}' => $subject
                        );
                        $object = $this->getSenderEmailAndName(null);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('delete-tk');
                        // Delete ticket mail to admin
                        if ($config['ticket_delete_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_delete_user'] == 1) {
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 4: // Reply Ticket (Admin/Staff Member)
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $message = $this->getJSModel('ticket')->getLatestReplyByTicketId($id);
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{MESSAGE}' => $message,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('responce-tk');
                        // Reply ticket mail to admin
                        if ($config['ticket_response_staff_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_response_staff_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###" />';

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 5: // Reply Ticket (Ticket Member)
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $message = $this->getJSModel('ticket')->getLatestReplyByTicketId($id);
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{MESSAGE}' => $message,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('reply-tk');
                        // New ticket mail to admin
                        if ($config['ticket_reply_user_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_reply_user_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 6: // Lock Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('lock-tk');
                        // New ticket mail to admin
                        if ($config['ticket_lock_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_lock_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 7: // Unlock Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('unlock-tk');
                        // New ticket mail to admin
                        if ($config['ticket_unlock_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_unlock_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 8: // Markoverdue Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $subject = $ticket->subject;
                        $matcharray = array(
                            '{TRACKINGID}' => $trackingid,
                            '{SUBJECT}' => $subject,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('moverdue-tk');
                        // New ticket mail to admin
                        if ($config['ticket_overdue_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_overdue_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 9: // Mark in progress Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $subject = $ticket->subject;
                        $matcharray = array(
                            '{TRACKINGID}' => $trackingid,
                            '{SUBJECT}' => $subject,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('markprgs-tk');
                        // New ticket mail to admin
                        if ($config['ticket_progress_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_progress_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 10: // Ban email and close Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $subject = $ticket->subject;
                        $matcharray = array(
                            '{EMAIL_ADDRESS}' => $email,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('banemailcloseticket-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['ticker_ban_and_close_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticker_ban_and_close_user'] == 1) {
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 11: // Priority change ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $subject = $ticket->subject;
                        $email = $ticket->email;
                        $Priority = $this->getJSModel('priority')->getPriorityById($ticket->priorityid);
                        $matcharray = array(
                            '{PRIORITY_TITLE}' => $Priority->priority,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{DEPARTMENT}' => $ticket->departmentname,
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('pchnge-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['ticket_priority_admin'] == 1) {
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_priority_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 12: // DEPARTMENT TRANSFER
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $subject = $ticket->subject;
                        $email = $ticket->email;
                        $Department = $this->getJSModel('department')->getDepartmentById($ticket->departmentid);
                        $matcharray = array(
                            '{DEPARTMENT_TITLE}' => $Department,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{PRIORITY}' => $ticket->priority
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('deptrans-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['ticket_department_transfer_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_department_transfer_user'] == 1) {
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                }
                break;
            case 2: // Ban Email
                switch ($action) {
                    case 1: // Ban Email
                        if ($tablename != null)
                            $banemailRecord = $this->getRecordByTablenameAndId($tablename, $id);
                        else
                            $banemailRecord = $this->getRecordByTablenameAndId('js_ticket_email_banlist', $id);
                        $email = $banemailRecord->email;
                        $matcharray = array(
                            '{EMAIL_ADDRESS}' => $email
                        );
                        $object = $this->getDefaultSenderEmailAndName();
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('banemail-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['ticket_ban_email_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_ban_email_user'] == 1) {
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 2: // Unban Email
                        if ($tablename != null)
                            $ticket = $this->getRecordByTablenameAndId($tablename, $id);
                        else
                            $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $email = $ticket->email;
                        $matcharray = array(
                            '{EMAIL_ADDRESS}' => $email
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('unbanemail-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['unban_email_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['unban_email_user'] == 1) {
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                }
                break;
            case 3: // Sending email alerts on mail system
                switch ($action) {
                    case 1: // Store message
                        $mailRecord = $this->getMailRecordById($id);
                        $matcharray = array(
                            '{STAFF_MEMBER_NAME}' => $mailRecord->sendername,
                            '{SUBJECT}' => $mailRecord->subject,
                            '{MESSAGE}' => $mailRecord->message
                        );
                        $object = $this->getSenderEmailAndName(null);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('mail-new');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $email = $mailRecord->receveremail;
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);

                        $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        break;
                    case 2: // Store reply
                        $mailRecord = $this->getMailRecordById($id, 1);
                        $matcharray = array(
                            '{STAFF_MEMBER_NAME}' => $mailRecord->sendername,
                            '{SUBJECT}' => $mailRecord->subject,
                            '{MESSAGE}' => $mailRecord->message
                        );
                        $object = $this->getSenderEmailAndName(null);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('mail-rpy');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $email = $mailRecord->receveremail;
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);

                        $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        break;
                }
                break;
            case 4: // GDPR
                switch($action){
                    case 1: // erase data request email
                        $mailRecord = $this->getRecordByTablenameAndId($tablename, $id);
                        $matcharray = array(
                            '{SITETITLE}' => $config['title'],
                            '{USERNAME}' => $mailRecord->name,
                            '{CURRENT_YEAR}' => date('Y')
                        );
                        if($config['erase_data_request_user'] == 1){
                            $object = $this->getSenderEmailAndName(null);
                            $senderEmail = $object->email;
                            $senderName = $object->name;
                            $template = $this->getTemplateForEmail('delete-user-data');
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $Email = $mailRecord->email;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // Erase data request receive
                        if ($config['erase_data_request_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $adminName = $this->getJoomlaNameByEmail($adminEmail);
                            $template = $this->getTemplateForEmail('delete-user-data-admin');
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $link = $this->setAdminUrl($id);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                    break;
                    case 2: // user data delete
                        $mailRecord = $this->getRecordByTablenameAndId($tablename, $id);
                        $matcharray = array(
                            '{SITETITLE}' => $config['title'],
                            '{USERNAME}' => $mailRecord->name,
                            '{CURRENT_YEAR}' => date('Y')
                        );
                        if($config['delete_user_data'] == 1){
                            $object = $this->getSenderEmailAndName(null);
                            $senderEmail = $object->email;
                            $senderName = $object->name;
                            $template = $this->getTemplateForEmail('user-data-deleted');
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $Email = $mailRecord->email;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                    break;
                }
            break;
        }
    }

    private function getRecordByTablenameAndId($tablename, $id) {
        if (!is_numeric($id))
            return false;
        $db = Factory::getDBO();
        switch($tablename){
            case 'js_ticket_tickets':
                $query = "SELECT ticket.*,department.departmentname,priority.priority "
                    . " FROM `#__" . $tablename . "` AS ticket "
                    . " LEFT JOIN `#__js_ticket_departments` AS department ON department.id = ticket.departmentid "
                    . " LEFT JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid "
                    . " WHERE ticket.id = " . $id;
            break;
            default:
                $query = "SELECT * FROM `#__" . $tablename . "` WHERE id = " . $id;
            break;
        }

        $db->setQuery($query);
        if (!$db->execute()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
            $db->setError($db->getErrorMsg());
            return false;
        }
        $record = $db->loadObject();
        return $record;
    }


    private function replaceMatches(&$string, $matcharray) {
        foreach ($matcharray AS $find => $replace) {
            $string = getJSTicketPHPFunctionsClass()->jsticket_str_replace($find, $replace, $string);
        }
    }

    private function getSenderEmailAndName($id) {
        if ($id) {
            if (!is_numeric($id))
                return false;
            $db = Factory::getDBO();
            $query = "SELECT email.email,email.name
                        FROM `#__js_ticket_tickets` AS ticket
                        JOIN `#__js_ticket_departments` AS department ON department.id = ticket.departmentid
                        JOIN `#__js_ticket_email` AS email ON email.id = department.emailid
                        WHERE ticket.id = " . $id;
            $db->setQuery($query);
            if (!$db->execute()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                $db->setError($db->getErrorMsg());
                return false;
            }
            $email = $db->loadObject();
        } else {
            $email = '';
        }
        if (empty($email)) {
            $email = $this->getDefaultSenderEmailAndName();
        }
        return $email;
    }

    private function getDefaultSenderEmailAndName() {
        $config = $this->getJSModel('config')->getConfigByFor('email');
        $emailid = $config['alert_email'];
        $db = Factory::getDbo();
        $query = "SELECT email,name FROM `#__js_ticket_email` WHERE id = " . $emailid;
        $db->setQuery($query);
        $email = $db->loadObject();
        return $email;
    }

    function getEmailById($id) {
        if (!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT email  FROM `#__js_ticket_email` WHERE id = " . $id;
        $db->setQuery($query);
        $email = $db->loadResult();
        return $email;
    }

    private function getTemplateForEmail($templatefor) {
        $db = $this->getDBO();
        $query = "SELECT * FROM `#__js_ticket_emailtemplates` WHERE templatefor = " . $db->quote($templatefor);
        $db->setQuery($query);
        $template = $db->loadObject();
        return $template;
    }

     private function sendEmail($recevierEmail, $subject, $body, $senderEmail, $senderName, $attachments, $action) {
        /*
          $attachments = array( WP_CONTENT_DIR . '/uploads/file_to_attach.zip' );
          $headers = 'From: My Name <myname@example.com>' . "\r\n";
          wp_mail('test@example.org', 'subject', 'message', $headers, $attachments );

          $action
          For which action of $mailfor you want to send the mail
          1 => New Ticket Create
          2 => Close Ticket
          3 => Delete Ticket
          4 => Reply Ticket (Admin/Staff Member)
          5 => Reply Ticket (Ticket member)

            switch ($action) {
                case 1:
                    do_action('jsst-beforeemailticketcreate', $recevierEmail, $subject, $body, $senderEmail);
                    break;
                case 2:
                    do_action('jsst-beforeemailticketreply', $recevierEmail, $subject, $body, $senderEmail);
                    break;
                case 3:
                    do_action('jsst-beforeemailticketclose', $recevierEmail, $subject, $body, $senderEmail);
                    break;
                case 4:
                    do_action('jsst-beforeemailticketdelete', $recevierEmail, $subject, $body, $senderEmail);
                    break;
            }
         */
        if(empty($senderName)){
            $senderName = $this->getJSModel('config')->getConfigurationByName('title');
        }
        $headers = 'From: ' . $senderName . ' <' . $senderEmail . '>' . "\r\n";
        // echo 'before <br/>'.$body;
        // //$body = preg_replace('/\r?\n|\r/', '<br/>', $body);
        // echo '1st <br/>'.$body;
        // //$body = getJSTicketPHPFunctionsClass()->jsticket_str_replace(array("\r\n", "\r", "\n"), "<br/>", $body);
        // echo '2nd <br/>'.$body;
        // //$body = nl2br($body);
        // echo 'last <br/>'.$body;
        // die('here');
        // echo '<br/>'.$subject.'<br/>';
        // echo $body.'<br/>';

        if(!empty($recevierEmail) && !empty($senderEmail)){
            $message = Factory::getMailer();
            $message->addRecipient($recevierEmail);
            $message->setSubject($subject);
            $siteAddress = Uri::base();
            //echo 'admin'.$adminEmail.$body;
            $message->setBody($body);
            $sender = array( $senderEmail, $senderName );
            $message->setSender($sender);
            $message->IsHTML(true);
            $sent = $message->send();
            return $sent;
        }else return true;
    }

    function getMailRecordById($id, $replyto = null) {
        if (!is_numeric($id))
            return false;
        $db = Factory::getDBO();
        if ($replyto == null) {
            $query = "SELECT mail.subject,mail.message
                        FROM `#__js_ticket_mail` AS mail
                        WHERE mail.id = " . $id;
        } else {
            $query = "SELECT mail.subject,reply.message
                        FROM `#__js_ticket_mail` AS reply
                        JOIN `#__js_ticket_mail` AS mail ON mail.id = reply.replytoid
                        WHERE reply.id = " . $id;
        }
        $db->setQuery($query);
        if (!$db->execute()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
            $db->setError($db->getErrorMsg());
            return false;
        }
        $result = $db->loadObject();
        return $result;
    }

    function getJoomlaNameByEmail($emailaddress){
        $db = Factory::getDbo();
        $query = "SELECT name FROM `#__users` WHERE email = ".$db->quote($emailaddress);
        $db->setQuery($query);
        $name = $db->loadResult();
        return $name;
    }

    function getURL(){
        $url = Uri::root();
        $url .= 'index.php?option=com_jssupportticket';
        return $url;
    }
    function getAdminURL(){
        $url = Uri::root();
        $url .= 'administrator/index.php?option=com_jssupportticket';
        return $url;
    }
    function setGuestUrl($trackingid,$email){
        $url = $this->getURL();
        $private = $trackingid.','.$email;
        $data = getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($private);
        $url .= '&c=ticket&layout=ticketdetail&jsticket='.$data;
        $link = '<a href="'.$url.'" target="_blank">'.Text::_('Ticket Detail').'</a>';
        return $link;
    }
    function setUserUrl($id){
        $url = $this->getURL();
        $url .= '&c=ticket&layout=ticketdetail&id='.$id;
        $link = '<a href="'.$url.'" target="_blank">'.Text::_('Ticket Detail').'</a>';
        return $link;
    }
    function setAdminUrl($id){
        $url = $this->getAdminURL();
        $url .= '&c=ticket&layout=ticketdetails&cid='.$id;
        $link = '<a href="'.$url.'" target="_blank">'.Text::_('Ticket Detail').'</a>';
        return $link;
    }
}?>
