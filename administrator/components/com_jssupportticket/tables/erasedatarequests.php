<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	March 10, 2020
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Table\Table;

class TableErasedatarequests extends Table {

	var $id = null;
	var $uid = null;
	var $subject = null;
	var $message = null;
	var $status = null;
	var $created = null;


	function __construct(&$db) {
        parent::__construct('#__js_ticket_erasedatarequests', 'id', $db);
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

        return true;
    }
}

?>
