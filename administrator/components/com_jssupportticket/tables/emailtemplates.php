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

class TableEmailTemplates extends Table {

    var $id = null;
    var $title = null;
    var $subject = null;
    var $body = null;
    var $templatefor = null;
    var $status = null;
    var $created = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_emailtemplates', 'id', $db);
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
