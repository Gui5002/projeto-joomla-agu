<?php
/**
 * JS Support Ticket 1.6.0 feature foundation table: #__js_ticket_error_logs.
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Table\Table;

class TableErrorLogs extends Table {
    var $id = null;
    var $uid = null;
    var $staffid = null;
    var $ticketid = null;
    var $source = null;
    var $context = null;
    var $severity = null;
    var $message = null;
    var $technical_detail = null;
    var $metadata = null;
    var $is_read = null;
    var $created = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_error_logs', 'id', $db);
    }

    function check() {
        return true;
    }
}

?>
