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

class JSSupportticketModelHelpTopic extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }
    function getHelpTopicForCombo($val,$title){
        if(isset($val) && $val <>''){
            if(!is_numeric($val)) return false;
        $db = Factory::getDBO();
        $query = "SELECT id, topic FROM `#__js_ticket_help_topics` WHERE departmentid = ".$val." AND status = 1  ORDER BY topic DESC ";
	try{
             $db->setQuery( $query );
             $rows = $db->loadObjectList();
	}
        catch (RuntimeException $e){
             echo $db->stderr();
                return false;
        }
        }
        $helptopics = array();
        if ($title) $helptopics[] =  array('value' => null, 'text' => Text::_($title));
        if(isset($rows)){
            foreach($rows as $row){
                $helptopics[] =  array('value' => $row->id, 'text' => Text::_($row->topic));
            }
        }
        return $helptopics;
    }

    function listHelpTopic($val){
        if(!is_numeric($val)) return false;
        $db = $this->getDBO();

        $query  = "SELECT id, topic FROM `#__js_ticket_help_topics`  WHERE status = 1 AND departmentid = ".$val." ORDER BY topic ASC";
        $db->setQuery($query);
        $result = $db->loadObjectList();

        if (isset($result)){
            $return_value = "<select name='helptopicid' class='inputbox ' >\n";
                                $return_value .= "<option value='' >".Text::_('Help Topic')."</option> \n" ;
            foreach($result as $row){
                $return_value .= "<option value=\"$row->id\" >$row->topic</option> \n" ;
            }
            $return_value .= "</select>\n";
        }else{
            $return_value = "<select name='helptopicid' class='inputbox ' >\n";
                                $return_value .= "<option value='' >".Text::_('Help topic')."</option> \n" ;
            $return_value .= "</select>\n";
                }
        return $return_value;
    }

     function listHelpTopicAndPremade($val){
            if(!is_numeric($val)){
                //return value
                $return_value['helptopic'] = '<span class="js-ticket-error-premade">'.Text::_('No HelpTopic found').' </span>';
                $return_value['premade'] = '<span class="js-ticket-error-premade">'.Text::_('No premade found').' </span>';
                return $return_value;
            }
            $db = $this->getDBO();
            //for helptopic
            $query  = "SELECT id, topic FROM `#__js_ticket_help_topics`  WHERE status = 1 AND departmentid = ".$val." ORDER BY topic ASC";
            $db->setQuery($query);
            $result = $db->loadObjectList();
            if (!empty($result)){
                $reqhelptopicid = $this->getJSModel('userfields')->isFieldRequiredByField('helptopic') == 1 ? ' required' : '';
                $helptopic = "<select name='helptopicid' id='helptopicid' class='inputbox js-form-select-field ".$reqhelptopicid."' >\n";
                        $helptopic .= "<option value='' >".Text::_('Help Topic')."</option> \n";
                foreach($result as $row){
                        $helptopic .= "<option value=\"$row->id\" >".Text::_($row->topic)."</option> \n" ;
                }
                $helptopic .= "</select>\n";
            }else{
                $helptopic = '<span class="js-ticket-error-premade">'.Text::_('No HelpTopic found').' </span>';
            }
            
            //for premade
            $query  = "SELECT id, title FROM `#__js_ticket_department_message_premade`  WHERE status = 1 AND departmentid = ".$val." ORDER BY title ASC";
            $db->setQuery($query);
            $result = $db->loadObjectList();
            if (!empty($result)){
                $premade = "<select name='' class='inputbox js-form-select-field ' onChange=\"getpremade('issue_summary', this.value ,append.checked)\" >\n";
                        $premade .= "<option value='' >".Text::_('Select Premade')."</option> \n" ;
                foreach($result as $row){
                        $premade .= "<option value=\"$row->id\" >".Text::_($row->title)."</option> \n" ;
                }
                $premade .= "</select>\n";
            }else{
                $premade = '<span class="js-ticket-error-premade">'.Text::_('No premade found').' </span>';
            }
            
            //return value
            $return_value['helptopic'] = $helptopic;
            $return_value['premade'] = $premade;
            return $return_value;
    }

    function checkForHelpTopicSetting($id){
        if(!is_numeric($id)) return false;
        $db = Factory::getDBO();
        $query = "Select helptopic.autoresponce 
                    From `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_help_topics` AS helptopic ON helptopic.id = ticket.helptopicid
                    where ticket.id = ".$id;
        $db->setQuery( $query );
        $helptopicsetting = $db->loadResult();
        return $helptopicsetting;
    }


// admins

    function gethelpTopicForForm($id) {
        $db = $this->getDbo();
        $lists = array();
        $result = array();
        if ($id) {
            if (is_numeric($id) == false)
                return false;
            $query = "SELECT helptopic.* FROM `#__js_ticket_help_topics` AS helptopic WHERE helptopic.id = " . $id;
            $db->setQuery($query);
            $helptopic = $db->loadObject();
        }
        $config = $this->getJSModel('config')->getConfigByFor('default');
        if (isset($helptopic)) {
            $lists['priority'] = HTMLHelper::_('select.genericList', $this->getJSModel('priority')->getPriority(Text::_('Select Priority')), 'priorityid', 'class="inputbox required"' . '', 'value', 'text', $helptopic->priorityid);
            $lists['department'] = HTMLHelper::_('select.genericList', $this->getJSModel('department')->getDepartments(Text::_('Select Department')), 'departmentid', 'class="inputbox required" ' . '', 'value', 'text', $helptopic->departmentid);
        } else {
            $lists['priority'] = HTMLHelper::_('select.genericList', $this->getJSModel('priority')->getPriority(Text::_('Select Priority')), 'priorityid', 'class="inputbox required"' . '', 'value', 'text', $config['priority']);
            $lists['department'] = HTMLHelper::_('select.genericList', $this->getJSModel('department')->getDepartments(Text::_('Select Department')), 'departmentid', 'class="inputbox required" ' . '', 'value', 'text', '');
        }
        if (isset($helptopic))
            $result[0] = $helptopic;
        $result[1] = $lists;
        return $result;
    }

    function storeHelpTopic($data) {

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string

        $row = $this->getTable('helptopic');
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

    function getAllHelpTopices($helptopic, $statusid, $limitstart, $limit) {
        $db = $this->getDbo();
        $result = array();
        $status [] = array('value' => null, 'text' => Text::_('Status'));
        $status [] = array('value' => 1, 'text' => Text::_('Active'));
        $status [] = array('value' => 0, 'text' => Text::_('Disabled'));
        $lists['status'] = HTMLHelper::_('select.genericList', $status, 'filter_ht_statusid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text', $statusid);
        $query = "SELECT COUNT(id) FROM `#__js_ticket_help_topics` AS helptopic WHERE helptopic.status <> -1";
        if ($helptopic){
            $helptopic = getJSTicketPHPFunctionsClass()->jsticket_trim($helptopic);
            $query .= " AND helptopic.topic LIKE " . $db->quote('%'.$helptopic.'%');
        }
        if ($statusid){
            if(is_numeric($statusid))
                $query .=" AND helptopic.status = " . $statusid;
        }
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;

        $query = " SELECT helptopic.* ,dep.departmentname AS departmentname,priority.priority AS priority
            FROM  `#__js_ticket_help_topics` AS helptopic
            JOIN   `#__js_ticket_departments` AS dep on helptopic.departmentid = dep.id
            LEFT JOIN   `#__js_ticket_priorities` AS priority on helptopic.priorityid = priority.id
        WHERE helptopic.status <> -1 ";
        if (isset($helptopic) && $helptopic <> ''){
            $helptopic = getJSTicketPHPFunctionsClass()->jsticket_trim($helptopic);
            $query .= " AND helptopic.topic LIKE " . $db->quote('%'.$helptopic.'%');
        }
        if (isset($statusid) && $statusid <> '') {
            if (!is_numeric($statusid))
                return False;
            $query .=" AND helptopic.status = " . $statusid;
        }
        $db->setQuery($query, $limitstart, $limit);
        $helptopicdata = $db->loadObjectList();
        if ($helptopic)
            $lists['helptopic'] = $helptopic;
        $result[0] = $helptopicdata;
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function deleteHelpTopic() {
        $cids = Factory::getApplication()->input->get('cid', array(0), null, 'array');
        $row = $this->getTable('helptopic');
        foreach ($cids as $cid) {
            if ($this->helpTopicCanDelete($cid) == true) {
                if (!$row->delete($cid)) {
                    $this->setError($row->getErrorMsg());
                    return DELETE_ERROR;
                }
            }else{
				return IN_USE;
			}
			
        }
        return DELETED;
    }

    function helpTopicCanDelete($id) {
        if (is_numeric($id) == false)
            return false;
        $db = $this->getDBO();

        $query = "SELECT 
                        (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE helptopicid = " . $id." ) 
                    ";
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total > 0)
            return false;
        else
            return true;
    }
}

?>
