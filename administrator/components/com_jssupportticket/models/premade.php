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
defined('_JEXEC') or die('Not Allowed');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelPremade extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getAllDepartmentsPremade($title, $departmentid, $statusid, $limitstart, $limit) {
        $db = $this->getDBO();
        $result = array();

        $status [] = array('value' => null, 'text' => Text::_('Status'));
        $status [] = array('value' => 1, 'text' => Text::_('Active'));
        $status [] = array('value' => -1, 'text' => Text::_('Offline'));

        $lists['departments'] = HTMLHelper::_('select.genericList', $this->getJSModel('department')->getDepartments(Text::_('Select Department')), 'filter_dp_departmentid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text', $departmentid);
        $lists['status'] = HTMLHelper::_('select.genericList', $status, 'filter_dp_statusid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text', $statusid);
        $query = "SELECT COUNT(id) FROM #__js_ticket_department_message_premade AS deppremade WHERE 1 = 1";

        if ($title){
            $title = getJSTicketPHPFunctionsClass()->jsticket_trim($title);
            $query .= " AND deppremade.title LIKE ".$db->quote('%'.$title.'%')                                                                                                                                                                                                                                                                                                                                                          ;
        }
        if ($departmentid) {
            if (!is_numeric($departmentid))
                return false;
            $query .= " AND deppremade.departmentid = " . $departmentid;
        }
        if ($statusid) {
            if (!is_numeric($statusid))
                return false;
            $query .=" AND deppremade.isenabled= " . $statusid;
        }
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;
        $query = "SELECT deppremade.*, dep.departmentname AS departmentname
		FROM `#__js_ticket_department_message_premade` AS deppremade
		JOIN `#__js_ticket_departments` AS dep ON deppremade.departmentid =dep.id
		WHERE 1 = 1 ";
        if ($title){
            $title = getJSTicketPHPFunctionsClass()->jsticket_trim($title);
            $query .= " AND deppremade.title LIKE " . $db->quote('%'.$title.'%');
        }
        if ($departmentid) {
            if (!is_numeric($departmentid))
                return false;
            $query .= " AND deppremade.departmentid = " . $departmentid;
        }
        if ($statusid) {
            if (!is_numeric($statusid))
                return false;
            $query .=" AND deppremade.isenabled= " . $statusid;
        }
        $db->setQuery($query, $limitstart, $limit);
        if ($title)
            $lists['title'] = $title;
        $result[0] = $db->loadObjectList();
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function storeDepartmentPremade($data) {
        $row = $this->getTable('departmentpremade');

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string

        // The canned response body is HTML from the editor, so it is restored
        // AFTER the sanitize pass. jsticket_sanitizeData() runs InputFilter with
        // the STRING filter, which strips every tag - fetching raw before it ran
        // meant the formatting was thrown away again before the row was bound.
        // This matches the order already used by storeUserReplies().
        $data['answer'] = Factory::getApplication()->input->get('answer', '', 'raw');

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->store()) {
           $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        JSSupportticketMessage::$recordid = $row->id;
        return SAVED;
    }

    function getPremadeForInternalNote($val){
        if($val){
            if(!is_numeric($val)) return false;
            $db = $this->getDBO();
            $query  = "SELECT answer 
                        FROM `#__js_ticket_department_message_premade`  
                        WHERE status = 1 AND id = ".$val;
        
            $db->setQuery($query);
            $result = $db->loadResult();
            return $result;
        }
    }

    function getPremadeForForm($id) {         //getPremadeForForm
        $db = $this->getDBO();
        if ($id) {
            if (is_numeric($id) == false)
                return false;
            $query = "SELECT premade.*
			FROM `#__js_ticket_department_message_premade` AS premade
			WHERE premade.id = " . $id;
            $db->setQuery($query);
            $premade = $db->loadObject();
        }
        $title = "";
        if (isset($premade)) {
            $lists['departments'] = HTMLHelper::_('select.genericList', $this->getJSModel('department')->getDepartments(Text::_('Select Department')), 'departmentid', 'class="inputbox required" ' . '', 'value', 'text', $premade->departmentid);
        } else {
            $lists['departments'] = HTMLHelper::_('select.genericList', $this->getJSModel('department')->getDepartments(Text::_('Select Department')), 'departmentid', 'class="inputbox required" ' . '', 'value', 'text', '');
        }
        if (isset($premade))
            $result[0] = $premade;
        $result[1] = $lists;
        return $result;
    }


    function deleteDepartmentPremade() {
        $cids = Factory::getApplication()->input->get('cid', array(0), null, 'array');
        $row = $this->getTable('departmentpremade');
        $deleteall = 1;
        foreach ($cids as $cid) {
            if ($this->departmentPremadeCanDelete($cid) == true) {
                if (!$row->delete($cid)) {
                    $this->setError($row->getErrorMsg());
                    return DELETE_ERROR;
                }
            } else
                $deleteall++;
        }
        if($deleteall == 1){
            return DELETED;
        }else{
            $deleteall = $deleteall-1;
            JSSupportticketMessage::$recordid = $deleteall;
            return DELETE_ERROR;
        }
    }

    function departmentPremadeCanDelete($id) {
        if (is_numeric($id) == false)
            return false;
        return true;
    }

    function getPremade($departmentid) {
        $db = $this->getDBO();
        $query = "SELECT * FROM `#__js_ticket_department_message_premade` WHERE status = 1";
        if (isset($departmentid) && $departmentid <> '') {
            if (!is_numeric($departmentid))
                return false;
            $query .= " AND departmentid = " . $departmentid;
        }
        //echo $query; die();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $premade = array();
        if (!empty($rows)) {
            $premade[] = array('value' => null, 'text' => Text::_('Select Premade'));
            foreach ($rows as $row) {
                $premade[] = array('value' => $row->id, 'text' => Text::_($row->title));
            }
        } else
            $premade[] = array('value' => null, 'text' => Text::_('Select Premade'));
        return $premade;
    }

}

?>
