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
use Joomla\CMS\Table\Table;

class TableTickets extends Table {

    var $id = null;
    var $uid = null;
    var $ticketid = null;
    var $departmentid = null;
    var $priorityid = null;
    var $staffid = null;
    var $email = null;
    var $name = null;
    var $subject = null;
    var $message = null;
    var $helptopicid = null;
    var $phone = null;
    var $phoneext = null;
    var $status = 0; //create new ticket set it to zero;
    var $isoverdue = 0;
    var $isanswered = 0;
    var $duedate = null;
    var $reopened = null;
    var $closed = null;
    var $lastreply = null;
    var $created = null;
    var $update = null;
    var $lock = null;
    var $params = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_tickets', 'id', $db);
    }

    /**
     * Validation
     * 
     * @return boolean true if buffer is valid
     * 
     */
    function check() {
        if (getJSTicketPHPFunctionsClass()->jsticket_trim($this->message) == '') {
            $this->_error = "Message cannot be empty.";
            return false;
        }
        if (getJSTicketPHPFunctionsClass()->jsticket_trim($this->staffid) == '') {
            $this->staffid = NULL;
        }

        return true;
    }

}

?>
