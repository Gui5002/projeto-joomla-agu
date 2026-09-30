<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:  Feb 24, 2020
  ^
  + Project:    JS Tickets
  ^
 */

defined('_JEXEC') or die('Not Allowed');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelGdpr extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

	function getEraseDataRequests($email,$limitstart,$limit){
        $db = Factory::getDbo();
		$query = "SELECT COUNT(id) FROM `#__js_ticket_erasedatarequests`";
        $db->setQuery($query);
        $total = $db->loadObjectList();
        $result = array();
        $inquery = '';
		if ($email != null){
			$email = getJSTicketPHPFunctionsClass()->jsticket_trim($email);
			$inquery .= " WHERE user.email = " . $db->quote($email);
		}

        // Data
        $query = "SELECT request.*, user.email
                    FROM `#__js_ticket_erasedatarequests` AS request
                    LEFT JOIN `#__users` AS user ON user.ID = request.uid
                    ";
        $query .= $inquery;
        $query .= " ORDER BY request.created DESC ";
        $db->setQuery($query, $limitstart, $limit);
        $data = $db->loadObjectList();
        $result[0] = $data;
        $result[1] = $total;
        return $result;
	}

    function getUserEraseDataRequest($uid){
        if($uid == 0){
            return;
        }
        $db = Factory::getDbo();
        $query = "SELECT * FROM `#__js_ticket_erasedatarequests` WHERE uid = $uid";
        $db->setQuery($query);
        $gdprfields = $db->loadObject();
        return $gdprfields;
    }

    function storeUserEraseRequest($data){
    	if (!$data['id']) { //new
            $user = JSSupportTicketCurrentUser::getInstance();
    	    $data['created'] = date('Y-m-d H:i:s');
            $data['uid'] = $user->getId();
            $data['status'] = 1;
    	}
    	$data = filter_var_array($data, FILTER_SANITIZE_STRING);
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
    	$data['message'] = Factory::getApplication()->input->get('message', '', 'raw');
    	$row = $this->getTable('erasedatarequests');
    	$data = $this->getJSmodel('jssupportticket')->stripslashesFull($data);// remove slashes with quotes.
    	$error = 0;
        $return_value = true;
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if(!$data['id'])
        if (!$row->check()) {
            $this->setError($row->getError());
            return MESSAGE_EMPTY;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getErrorMsg());
            $this->setError($row->getError());
            $return_value = false;
        }


    	if ($return_value) {
            if(isset($data['id']) && $data['id'] == ''){
                $this->getJSModel('email')->sendMail(4, 1, $user->getId(),'users'); // Mailfor, Delete request receive
            }
    	    return SAVED;
    	} else {
	       return SAVE_ERROR;
    	}
        return;
    }

    function deleteUserEraseRequest($id){
        if(!is_numeric($id)){
            return false;
        }
        if($this->checkCanDelete($id)){
            $row = $this->getTable('erasedatarequests');
            if ($row->delete($id)) {
                return DELETED;
            } else {
                $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                $this->setError($db->getErrorMsg());
                return DELETE_ERROR;
            }
        }
        return PERMISSION_ERROR;
    }

    function checkCanDelete($id){
        // if(current_user_can('manage_options')){ // allow admin to delete ??
        //     return true;
        // }
        $db = Factory::getDbo();
        $user = JSSupportTicketCurrentUser::getInstance();
        $uid = $user->getId();
        $query = "SELECT uid FROM `#__js_ticket_erasedatarequests` WHERE id = $id";
        $db->setQuery($query);
        $db_uid = $db->loadResult();
        if( $db_uid == $uid){
            return true;
        }else{
            return false;
        }
    }

    private function getUserDetailReportByUserId( $uid = 0){
        $db = Factory::getDbo();
        $curdate = Factory::getApplication()->input->get('date_start', 'get');
        $fromdate = Factory::getApplication()->input->get('date_end', 'get');
        if($uid == 0 || $uid == ''){
            $id = Factory::getApplication()->input->get('uid', 'get');
        }else{
            $id = $uid;
            $query = "SELECT created FROM `#__js_ticket_tickets` WHERE uid = ".$id ." ORDER BY created ASC LIMIT 1";
            $db->setQuery($query);
            $curdate = $db->loadResult();
            $fromdate = date('Y-m-d H:i:s');
        }
        if( empty($curdate) OR empty($fromdate))
            return null;
        if(! is_numeric($id))
            return null;

        $result['curdate'] = $curdate;
        $result['fromdate'] = $fromdate;
        $result['id'] = $id;

        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0 AND (lastreply IS NULL OR lastreply < '1971-01-01') AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "'";
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['openticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "'";
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['closeticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "'";
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['answeredticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "'";
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['overdueticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4 AND (lastreply IS NOT NULL AND lastreply >= '1971-01-01') AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "'";
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['pendingticket'] = $db->loadObjectList();
        //user detail
        $query = "SELECT user.name as display_name,user.email AS user_email,user.username,user.id,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0  AND (lastreply IS NULL OR lastreply < '1971-01-01') AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "' AND uid = user.id) AS openticket,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "' AND uid = user.id) AS closeticket,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "' AND uid = user.id) AS answeredticket,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "' AND uid = user.id) AS overdueticket,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4  AND (lastreply IS NOT NULL AND lastreply >= '1971-01-01') AND created >= '" . $curdate . "' AND created <= '" . $fromdate . "' AND uid = user.id) AS pendingticket
                    FROM `#__users` AS user
                    WHERE user.id = ".$id;
        $db->setQuery($query);
        $user = $db->loadObject();
        $result['users'] = $user;

        //Tickets
        $query = "SELECT ticket.*,priority.priority, priority.prioritycolour
                    FROM `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid
                    WHERE uid = ".$id." AND ticket.created >= '" . $curdate . "' AND ticket.created <= '" . $fromdate . "' ";
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();
        return $result;
    }

    function setUserExportByuid($uid = 0){
        $tb = "\t";
        $nl = "\n";
        $result = $this->getUserDetailReportByUserId($uid);
        if(empty($result))
            return '';

        $fromdate = date('Y-m-d',getJSTicketPHPFunctionsClass()->jsticket_strtotime($result['curdate']));
        $fromdate = date('Y-m-d',getJSTicketPHPFunctionsClass()->jsticket_strtotime($result['curdate']));
        $todate = date('Y-m-d',getJSTicketPHPFunctionsClass()->jsticket_strtotime($result['fromdate']));

        $data = Text::_('User Report').' '.Text::_('From').' '.$fromdate.' - '.$todate.$nl.$nl;

        // By 1 month
        $data .= Text::_('Ticket status by days').$nl.$nl;
        $data .= Text::_('Date').$tb.Text::_('New').$tb.Text::_('Answered').$tb.Text::_('Closed').$tb.Text::_('Pending').$tb.Text::_('Overdue').$nl;
        while (getJSTicketPHPFunctionsClass()->jsticket_strtotime($fromdate) <= getJSTicketPHPFunctionsClass()->jsticket_strtotime($todate)) {
            $openticket = 0;
            $closeticket = 0;
            $answeredticket = 0;
            $overdueticket = 0;
            $pendingticket = 0;
            foreach ($result['openticket'] as $ticket) {
                $ticket_date = date('Y-m-d', getJSTicketPHPFunctionsClass()->jsticket_strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $openticket += 1;
            }
            foreach ($result['closeticket'] as $ticket) {
                $ticket_date = date('Y-m-d', getJSTicketPHPFunctionsClass()->jsticket_strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $closeticket += 1;
            }
            foreach ($result['answeredticket'] as $ticket) {
                $ticket_date = date('Y-m-d', getJSTicketPHPFunctionsClass()->jsticket_strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $answeredticket += 1;
            }
            foreach ($result['overdueticket'] as $ticket) {
                $ticket_date = date('Y-m-d', getJSTicketPHPFunctionsClass()->jsticket_strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $overdueticket += 1;
            }
            foreach ($result['pendingticket'] as $ticket) {
                $ticket_date = date('Y-m-d', getJSTicketPHPFunctionsClass()->jsticket_strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $pendingticket += 1;
            }
            $data .= '"'.$fromdate.'"'.$tb.'"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl;
            $fromdate = date("Y-m-d", getJSTicketPHPFunctionsClass()->jsticket_strtotime("+1 day", getJSTicketPHPFunctionsClass()->jsticket_strtotime($fromdate)));
        }
        $data .= $nl.$nl.$nl;
        // END By 1 month

        // by staffs
        $data .= Text::_('Users Tickets').$nl.$nl;
        if(!empty($result['users'])){
            $data .= Text::_('Name').$tb.Text::_('Username').$tb.Text::_('Email').$tb.Text::_('New').$tb.Text::_('Answered').$tb.Text::_('Closed').$tb.Text::_('Pending').$tb.Text::_('Overdue').$nl;
            $key = $result['users'];
            $agentname = $key->display_name;
            $username = $key->username;
            $email = $key->user_email;

            $data .= '"'.$agentname.'"'.$tb.'"'.$username.'"'.$tb.'"'.$email.'"'.$tb.'"'.$key->openticket.'"'.$tb.'"'.$key->answeredticket.'"'.$tb.'"'.$key->closeticket.'"'.$tb.'"'.$key->pendingticket.'"'.$tb.'"'.$key->overdueticket.'"'.$nl;

            $data .= $nl.$nl.$nl;
        }
        // by priorits tickets
        $data .= Text::_('Tickets').$nl.$nl;
        if(!empty($result['tickets'])){
            $data .= Text::_('Subject').$tb.Text::_('Status').$tb.Text::_('Priority').$tb.Text::_('Created');
            $data .= $nl;
            $status = '';
            foreach ($result['tickets'] as $ticket) {
                switch($ticket->status){
                    case 0:
                        $status = Text::_('New');
                        if($ticket->isoverdue == 1)
                            $status = Text::_('Overdue');
                    break;
                    case 1:
                        $status = Text::_('Pending');
                        if($ticket->isoverdue == 1)
                            $status = Text::_('Overdue');
                    break;
                    case 2:
                        $status = Text::_('In Progress');
                        if($ticket->isoverdue == 1)
                            $status = Text::_('Overdue');
                    break;
                    case 3:
                        $status = Text::_('Answered');
                        if($ticket->isoverdue == 1)
                            $status = Text::_('Overdue');
                    break;
                    case 4:
                        $status = Text::_('Closed');
                    break;
                }
                $created = date('Y-m-d',getJSTicketPHPFunctionsClass()->jsticket_strtotime($ticket->created));
                $data .= '"'.$ticket->subject.'"'.$tb.'"'.$status.'"'.$tb.'"'.Text::_($ticket->priority).'"'.$tb.'"'.$created.'"';
                $data .= $nl;
            }
            $data .= $nl.$nl.$nl;
        }
        return $data;
    }

    function anonymizeUserData($uid){
        if(!is_numeric($uid) || $uid == 0){
            return false;
        }
        $db = Factory::getDbo();
        $query = "SELECT id FROM `#__js_ticket_tickets` WHERE uid = ".$uid;
        $db->setQuery($query);
        $uids = $db->loadObjectList();

        foreach ($uids as $ticket) { // erase tickets data
            // ticket data
            $query = "UPDATE `#__js_ticket_tickets` SET email = '---', subject = '---', message = '---', phone = '', phoneext = '', params = '' WHERE id = ".$ticket->id;
            $db->setQuery($query);
            $db->execute($query);
            // erase replies data
            $query = "SELECT replies.id AS replyid
                        FROM `#__js_ticket_replies` AS replies
                        WHERE replies.ticketid = ".$ticket->id;
            $db->setQuery($query);
            $replies = $db->loadObjectList();
            foreach ($replies as $reply) {
                $query = "UPDATE `#__js_ticket_replies` SET message = '----' WHERE ticketid = ".$ticket->id;
                $db->setQuery($query);
                $db->execute($query);
            }

            // ticket attachments.
            $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
            $mainpath = JPATH_BASE;
            if(Factory::getApplication()->isClient('administrator')){
                $mainpath = getJSTicketPHPFunctionsClass()->jsticket_substr($mainpath, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($mainpath) - 14); //remove administrator
            }
            $mainpath = $mainpath .'/'.$datadirectory;
            $mainpath = $mainpath . '/attachmentdata';
            $query = "SELECT ticket.attachmentdir
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE ticket.id = ".$ticket->id;
            $db->setQuery($query);
            $foldername = $db->loadResult();
            if(!empty($foldername)){
                $folder = $mainpath . '/ticket/'.$foldername;
                if(file_exists($folder)){
                    $path = $mainpath . '/ticket/'.$foldername.'/*.*';
                    $files = glob($path);
                    array_map('unlink', $files);//deleting files
                    rmdir($folder);
                }
            }
            $query = "DELETE FROM `#__js_ticket_attachments` WHERE ticketid = ".$ticket->id;
            $db->setQuery($query);
            $db->execute($query);
        }
        $query = "UPDATE `#__js_ticket_erasedatarequests` SET status = 2 WHERE uid = $uid";
        $db->setQuery($query);
        $db->execute($query);
        $this->getJSModel('email')->sendMail(4, 2, $uid, 'users'); // Mailfor, Delete user data
        return DELETED;
    }

    function deleteUserData($uid){
        if(!is_numeric($uid) || $uid == 0){
            return false;
        }
        $db = Factory::getDbo();
        $query = "SELECT id FROM `#__js_ticket_tickets` WHERE uid = ".$uid;
        $db->setQuery($query);
        $uids = $db->loadObjectList();

        foreach ($uids as $ticket) { // erase tickets data
            // ticket data

            $row = $this->getTable('tickets');
            $row->delete($ticket->id);
            // delete replies
            $this->getJSModel('ticket')->removeTicketReplies($ticket->id);
            // ticket attachments.
            $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
            $mainpath = JPATH_BASE;
            if(Factory::getApplication()->isClient('administrator')){
                $mainpath = getJSTicketPHPFunctionsClass()->jsticket_substr($mainpath, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($mainpath) - 14); //remove administrator
            }
            $mainpath = $mainpath .'/'.$datadirectory;
            $mainpath = $mainpath . '/attachmentdata';
            $query = "SELECT ticket.attachmentdir
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE ticket.id = ".$ticket->id;
            $db->setQuery($query);
            $foldername = $db->loadResult();
            if(!empty($foldername)){
                $folder = $mainpath . '/ticket/'.$foldername;
                if(file_exists($folder)){
                    $path = $mainpath . '/ticket/'.$foldername.'/*.*';
                    $files = glob($path);
                    array_map('unlink', $files);//deleting files
                    rmdir($folder);
                }
            }
            $query = "DELETE FROM `#__js_ticket_attachments` WHERE ticketid = ".$ticket->id;
            $db->setQuery($query);
            $db->execute($query);
        }
        $query = "UPDATE `#__js_ticket_erasedatarequests` SET status = 3 WHERE uid = $uid";
        $db->setQuery($query);
        $db->execute($query);
        $this->getJSModel('email')->sendMail(4, 2, $uid,'users'); // Mailfor, Delete User
        return DELETED;
    }
}
?>
