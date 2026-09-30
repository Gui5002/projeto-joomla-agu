<?php
/**
 * @Copyright Copyright (C) 2009-2010 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Al-Barr Technologies
  + Contact:		www.al-barr.com , info@al-barr.com
 * Created on:	Dec 06, 2012
  ^
  + Project: 		Job Posting and Employment Application
 * File Name:	admin-----/models/jobsharing.php
  ^
 * Description: Model for application on admin site
  ^
 * History:		NONE
  ^
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelActivityLog extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function storeActivityLog($referenceid, $eventfor, $eventtype, $message, $messagetype) {
        $row = $this->getTable('activitylog');
        $user = JSSupportticketCurrentUser::getInstance();
        
        $level="";
        if($user->getIsAdmin()){
            $level = 1; // 1 for admin 
        }elseif($user->getIsGuest()){
            $level = 3; // 3 for Guest
        }

        switch ($eventfor) {
            case 1:
                $event = Text::_('Ticket');
            break;
        }

        $data = array();
        $data['uid'] = $user->getId();
        $data['referenceid'] = $referenceid;
        $data['level'] = $level;
        $data['eventfor'] = $eventfor;
        $data['event'] = $event;
        $data['eventtype'] = $eventtype;
        $data['message'] = $message;
        $data['messagetype'] = $messagetype;
        $data['datetime'] = date('Y-m-d H:i:s');

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->setError($row->getError());
            echo $row->getError();
            return false;
        }

        // 1.6.0 feature foundation: mirror legacy activity into the modern
        // activity_events table. This is intentionally non-blocking so old
        // ticket workflow remains safe even if the new table is unavailable.
        try {
            $this->getJSModel('featurefoundation')->storeActivityEventLegacy($referenceid, $eventfor, $eventtype, $message, $messagetype);
        } catch (Throwable $e) {
        }

        return true;
    }
}
