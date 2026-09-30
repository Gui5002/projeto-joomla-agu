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
use Joomla\CMS\Table\Table;

class Tabledepartmentpremade extends Table {

    var $id = null;
    var $departmentid = null;
    var $isenabled = null;
    var $title = null;
    var $answer = null;
    var $created = null;
    var $update = null;
    var $status = 0;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_department_message_premade', 'id', $db);
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
