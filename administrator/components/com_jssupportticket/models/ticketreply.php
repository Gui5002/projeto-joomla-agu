<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');
use Joomla\CMS\Language\Text;

class JSSupportTicketModelTicketreply extends JSSupportticketModel {

    function __construct() {
        parent::__construct();
    }

    function storeTicketReplies($ticketid, $message, $created, $data2) {
        if (!is_numeric($ticketid))
            return false;

        //validate reply for break down
        $ticketrandomid   = $data2['ticketrandomid'];
        $db = $this->getDBo();
        $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ticketid=". $db->quote($ticketrandomid)."";
        $db->setQuery($query);
        $res = $db->loadResult();
        if($res != $ticketid){
            return false;
        }//end

        $eventtype = Text::_('Reply Ticket');
        $user = JSSupportTicketCurrentUser::getInstance();
        $row = $this->getTable('replies');
        $data['ticketid'] = $ticketid;
        if($user->getIsAdmin()){
            $data['staffid'] = $user->getId();
        }else{
            $data['staffmemberid'] = 0;
        }
        $data['name'] = isset($uname) ? $uname : '';
        $data['message'] = $message;
        $data['status'] = 3;
        $data['created'] = $created;
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string\
		$data['message'] = $message;
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            return POST_ERROR;
        }

        $replyattachmentid = $row->id;
		$ATTACHMENTRESULT = $this->getJSModel('attachments')->storeTicketAttachment($ticketid,$replyattachmentid);
        if($ATTACHMENTRESULT !== true){
            return $ATTACHMENTRESULT;
        }

        if($user->getIsAdmin()){ // admin reply
            $result = $this->getJSModel('ticket')->updateStatus($ticketid, 3, $data2['created']);
            $this->getJSModel('ticket')->updateIsAnswered($ticketid,1);
            if(isset($data2['replystatus'])){
                $this->getJSModel('ticket')->ticketClose($ticketid ,$data2['created']);
            }
        }elseif(!$user->getIsGuest()){ // user reply
            $result = $this->getJSModel('ticket')->updateStatus($ticketid, 2, $data2['created']);
            $this->getJSModel('ticket')->updateIsAnswered($ticketid,0);
        }
        $this->getJSModel('ticket')->updateTicketLastReply($ticketid,$data2['created']);
        if ($result == false)
            return POST_ERROR;

        if($user->getIsAdmin()){
            $this->getJSModel('email')->sendMail(1,4,$ticketid); // Mailfor,reply,Ticketid [admin/staffmember]
        }else{
            $this->getJSModel('email')->sendMail(1,5,$ticketid); // Mailfor,reply,Ticketid [user reply]
        }

        return POSTED;
    }

    function removeTicketReplies($ticketid) {
        if(!is_numeric($ticketid)) return false;
        $db = $this->getDBo();
        $query = "DELETE FROM `#__js_ticket_replies` WHERE ticketid = ".$ticketid;
        $db->setQuery($query);
        $db->execute();
        return;
    }

}
?>
