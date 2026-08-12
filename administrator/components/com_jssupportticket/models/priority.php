<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');
jimport('joomla.application.component.model');
jimport('joomla.html.html');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class JSSupportticketModelPriority extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function storePriority($data) {
        $row = $this->getTable('priorities');
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
                if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if($row->isdefault == 1){
            $this->makePriorityDefault($row->id);
        }
        JSSupportticketMessage::$recordid = $row->id;
        return SAVED;
    }
    
    function makePriorityDefault($priorityid) {
        if (!is_numeric($priorityid))
            return false;
        $result = $this->setAllPrioritiesNotDefault();
        if ($result == false) {
            return SET_DEFAULT_ERROR;
        }
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_priorities` set isdefault = 1 WHERE id = " . $priorityid;
        $db->setQuery($query);
        if (!$db->execute()) {
            return SET_DEFAULT_ERROR;
        }
        $configresult = $this->setPriorityConfig($priorityid);
        if ($configresult == false) {
            return SET_DEFAULT_ERROR;
        }
        return SET_DEFAULT;
    }

    function setPriorityConfig($priorityid) {
        if (!is_numeric($priorityid))
            return false;
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_config` set configvalue = " . $priorityid . " WHERE configname = 'priority'";
        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        }
        return true;
    }

    function setAllPrioritiesNotDefault() {
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_priorities` set isdefault = 0";
        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        }
        return true;
    }

    function getAllPriorities($searchpriority,$limitstart, $limit) {
        $wherequery = '';
        $db = $this->getDbo();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_priorities`";
        if($searchpriority){
            $searchpriority= getJSTicketPHPFunctionsClass()->jsticket_trim($searchpriority);
            $wherequery = " WHERE priority LIKE ".$db->quote('%'.$searchpriority.'%');
        }
        $query .= $wherequery;
        $db->setQuery($query);
        $total = $db->loadResult();
        $query = "SELECT * FROM `#__js_ticket_priorities`";
        $query .= $wherequery;
        $db->setQuery($query, $limitstart, $limit);
        $priorites = $db->loadObjectList();
        $result[0] = $priorites;
        $result[1] = $total;
        $result[2] = $searchpriority;
        return $result;
    }
    
    function getFormData($id) {
        if($id){ 
            if(!is_numeric($id))
                return false;
            $db = $this->getDbo();
            $query = "SELECT * FROM `#__js_ticket_priorities` WHERE id =".$id;
            $db->setQuery($query);
            $priority = $db->loadObject();
            $result[0] = $priority;
            return $result;
        }
    }

    function deletePriority() {
        $row = $this->getTable('priorities');
        $c_id = Factory::getApplication()->input->get('cid', array(0), '', 'array');
        $deleteall = 1;
        foreach ($c_id as $id) {
            if(is_numeric($id)){
                if ($this->priorityCanDelete($id) == true) {
                    if (!$row->delete($id)) {
                        $this->setError($row->getError());
                        return DELETE_ERROR;
                    }
                } else
                    $deleteall++;
            }else{
                return false;
            }
        }
        if($deleteall == 1){
            return DELETED;
        }else{
            $deleteall = $deleteall-1;
            JSSupportticketMessage::$recordid = $deleteall;
            return DELETE_ERROR;
        }
    }

    function priorityCanDelete($id) {
        if (!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT COUNT(id) +
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id)
                    FROM `#__js_ticket_priorities` AS priority                    
                    WHERE priority.id=" . $id . " AND isdefault = 1";
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total == 0)
            return true;
        else
            return false;
    }

    function getPrioritiesForCombobx($title = null) {
        $db = $this->getDbo();
        $query = "SELECT id, priority FROM `#__js_ticket_priorities` WHERE status = 1 AND ispublic = 1 ORDER BY priority ASC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        $priority = array();
        if ($title)
            $priority[] = array('value' => '', 'text' => $title);
        foreach ($rows as $row) {
            $priority[] = array('value' => $row->id, 'text' => Text::_($row->priority));
        }
        return $priority;
    }

    function getPriorityById($id) {
        if (!is_numeric($id))
            return false;
        $db = $this->getDbo();
        $query = "SELECT priority FROM `#__js_ticket_priorities` WHERE id = $id";
        $db->setQuery($query);
        $priority = $db->loadObject();
        return $priority;
    }
}

?>
