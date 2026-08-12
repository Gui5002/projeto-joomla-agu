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

class TableDepartments extends Table {

    var $id = null;
    var $emailtemplateid = null;
    var $emailid = null;
    var $autoresponceemailid = null;
    var $managerid = null;
    var $departmentname = null;
    var $departmentsignature = null;
    var $ispublic = 0;
    var $sendemail = 0;
    var $ticketautoresponce = 0;
    var $messageautoresponce = 0;
    var $canappendsignature = 0;
    var $created = null;
    var $updated = null;
    var $isdefault = 0;
    var $status = 0;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_departments', 'id', $db);
    }

    /**
     * Validation
     *
     * @return boolean true if buffer is valid
     *
     */
    function check() {
        return true;
    }

}

?>
