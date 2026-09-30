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

class TableHelpTopic extends Table {

    var $id = null;
    var $autoresponce = 0;
    var $departmentid = null;
    var $priorityid = null;
    var $topic = null;
    var $status = 0;
    var $created = null;
    var $updated = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_help_topics', 'id', $db);
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
