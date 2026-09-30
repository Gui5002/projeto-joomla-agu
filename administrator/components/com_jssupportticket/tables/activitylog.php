<?php

/**
 * @Copyright Copyright (C) 2009-2011
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
  + Created by:          Ahmad Bilal
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	Jun 14, 2009
  ^
  + Project: 		JS Jobs
 * File Name:	admin-----/tables/userfielddata.php
  ^
 * Description: Table for user field data
  ^
 * History:		NONE
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Table\Table;

// our table class for the application data
class TableActivityLog extends Table {

    var $id = null;
    var $uid = null;
    var $referenceid = null;
    var $level = null;
    var $eventfor = null;
    var $event = null;
    var $eventtype = null;
    var $message = null;
    var $messagetype = null;
    var $datetime = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_activity_log', 'id', $db);
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
