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

class TableEmails extends Table {

    var $id = null;
    var $autoresponce = null;
    var $priorityid = null;
    var $departmentid = null;
    var $email = null;
    var $name = null;
    var $uid = null;
    var $password = null;
    var $status = 0;
    var $mailhost = null;
    var $mailprotocol = null;
    var $mailencryption = null;
    var $mailport = null;
    var $mailfetchfrequency = null;
    var $mailfetchmaximum = null;
    var $maildeleted = null;
    var $mailerrors = null;
    var $maillastfetch = null;
    var $smtpactive = null;
    var $smtphost = null;
    var $smtpport = null;
    var $smtpsecure = null;
    var $smtpauthencation = null;
    var $update = null;
    var $created = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_email', 'id', $db);
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
