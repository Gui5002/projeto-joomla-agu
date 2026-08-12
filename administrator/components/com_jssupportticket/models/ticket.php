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
use Joomla\CMS\Factory;

jimport('joomla.application.component.model');
jimport('joomla.html.html');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Editor\Editor;

class JSSupportticketModelTicket extends JSSupportTicketModel {

    var $activity_log;
    var $_jinput = null;
    function __construct() {
        parent::__construct();
    }

    function getRandomFolderName() {
        $foldername = "";
        $length = 7;
        $possible = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        // we refer to the length of $possible a few times, so let's grab it now
        $maxlength = getJSTicketPHPFunctionsClass()->jsticket_strlen($possible);
        if ($length > $maxlength) { // check for length overflow and truncate if necessary
            $length = $maxlength;
        }
        // set up a counter for how many characters are in the ticketid so far
        $i = 0;
        // add random characters to $password until $length is reached
        while ($i < $length) {
            // pick a random character from the possible ones
            $char = getJSTicketPHPFunctionsClass()->jsticket_substr($possible, mt_rand(0, $maxlength - 1), 1);
            if ($i == 0) {
                if (ctype_alpha($char)) {
                    $foldername .= $char;
                    $i++;
                }
            } else {
                $foldername .= $char;
                $i++;
            }
        }
        return $foldername;
    }

    function getUserMyTicketsForCP() {

        $db = $this->getDBO();
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsGuest())
            return false;
        $query = "SELECT ticket.id AS ticketid,ticket.subject,ticket.name,ticket.status,ticket.created,dep.departmentname AS departmentname, priority.priority AS priority, priority.prioritycolour AS prioritycolour
                        FROM `#__js_ticket_tickets` AS ticket
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        LEFT JOIN `#__js_ticket_departments` AS dep ON ticket.departmentid = dep.id
                        WHERE ticket.uid = " .$user->getId();
        $query .= " ORDER BY ticket.created DESC";
        $db->setQuery($query,0,4);
        $result = $db->loadObjectList(); //Tickets
        return $result;
    }

    function getDefaultTicketSorting($value=2){ // 2 for query
        $ticketsorting = $this->getJSModel('config')->getConfigurationByName('tickets_sorting');
        if($ticketsorting == 1){
            $sort = "ASC";
        }else{
            $sort = "DESC";
        }
        if($value == 1) // 1 for showing value in html
            $sort = getJSTicketPHPFunctionsClass()->jsticket_strtolower($sort);
        return $sort;
    }

    function storeTicket($data){
        $user = JSSupportTicketCurrentUser::getInstance();
        $eventtype = Text::_('New Ticket');
        //for new ticket case
        if (($data['id']) == ''){
            $data['ticketid'] = $this->getTicketId();
            $data['attachmentdir'] = $this->getRandomFolderName();
            $data['created'] = date('Y-m-d H:i:s');
            if(!$user->getIsAdmin()){
                $checkduplicatetk = $this->checkIsTicketDuplicate($data['subject'],$data['email']);
                if(!$checkduplicatetk){
                    return TICKET_DUPLICATE;
                }
            }
        }
        $row = $this->getTable('tickets');
        //$data['message'] = Factory::getApplication()->input->get('message', '', 'post', 'string', JREQUEST_ALLOWHTML);
        $data['message']  = Factory::getApplication()->input->get('message', '', 'raw');
        if(!$user->getIsAdmin())
            $data['uid'] = $user->getId();

        //custom field code start
        $customflagforadd = false;
        $customflagfordelete = false;
        $custom_field_namesforadd = array();
        $custom_field_namesfordelete = array();
        $userfield = $this->getJSModel('userfields')->getUserfieldsfor(1);
        $params = array();
        foreach ($userfield AS $ufobj) {
            $vardata = '';
            if($ufobj->userfieldtype == 'file'){
                if(isset($data[$ufobj->field.'_1']) && $data[$ufobj->field.'_1']== 0){
                    $vardata = $data[$ufobj->field.'_2'];
                }else{
                    $vardata = $_FILES[$ufobj->field]['name'];
                }
				$config = $this->getJSModel('config')->getConfigByFor('default');
				$model_attachment = $this->getJSModel('attachments');
				$file_size = $config['filesize'];
				if($_FILES[$ufobj->field]['size'] > ($file_size * 1024)){
					$vardata = '';
				}else{
					if ($_FILES[$ufobj->field]['name'] != "") {
						$is_allow = $model_attachment->checkExtension($_FILES[$ufobj->field]['name']);
						if($is_allow == 'N'){
							$vardata = '';
						}else{
							$vardata = $_FILES[$ufobj->field]['name'];
							$customflagforadd=true;
							$custom_field_namesforadd[]=$ufobj->field;
						}
					}
				}
            }elseif($ufobj->userfieldtype == 'date'){
                if(isset($data[$ufobj->field]) && !empty($data[$ufobj->field])){
                    $tempdate = $data[$ufobj->field];
                    $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                    if ($dateformat == 'm-d-Y') {
                      $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $tempdate);
                      $tempdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                    } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                      $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $tempdate);
                      $tempdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                    $vardata = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($tempdate),"Y-m-d" );
                    //$jDate = new Joomla\CMS\Date\Date(getJSTicketPHPFunctionsClass()->jsticket_strtotime($tempdate),"Y-m-d" );
                    //$fromdate = $jDate->format('Y-m-d');
                }else{
                    $vardata = '';
                }
            }
            else{
                $vardata = isset($data[$ufobj->field]) ? $data[$ufobj->field] : '';
            }
            if(isset($data[$ufobj->field.'_1']) && $data[$ufobj->field.'_1'] == 1){
                $customflagfordelete = true;
                $custom_field_namesfordelete[]= $data[$ufobj->field.'_2'];
            }
            if($vardata != ''){
                //had to comment this so that multpli field should work properly
                // if($ufobj->userfieldtype == 'multiple'){
                //     $vardata = getJSTicketPHPFunctionsClass()->jsticket_explode(',', $vardata[0]); // fixed index
                // }
                if(is_array($vardata)){
                    $vardata = implode(', ', $vardata);
                }
                $params[$ufobj->field] = getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($vardata);
            }
        }
        if($data['id'] != ''){
            if(is_numeric($data['id'])){
                $db = $this->getDbo();
                $query = "SELECT params FROM `#__js_ticket_tickets` WHERE id = " . $data['id'];
                $db->setQuery($query);
                $oParams = $db->loadResult();

                if(!empty($oParams)){
                    $oParams = json_decode($oParams,true);
                    $unpublihsedFields = $this->getJSModel('userfields')->getUserUnpublishFieldsfor(1);
                    foreach($unpublihsedFields AS $field){
                        if(isset($oParams[$field->field])){
                            $params[$field->field] = $oParams[$field->field];
                        }
                    }
                }
            }
        }

        if (!empty($params)) {
            $params = json_encode($params);
        }
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        $data['params'] = $params;
        // empty values causes problem
        if($data['uid'] == "") $data['uid'] = NULL;
        if($data['departmentid'] == "") $data['departmentid'] = NULL;


        if (!$row->bind($data)) {
            $this->setError($row->getError());
            echo $row->getError();
            $return_value = false;
        }
        if(!$data['id'])
        if (!$row->check()) {
            $this->setError($row->getError());
            return MESSAGE_EMPTY;
        }
        try{
            $row->store();
        }
        catch (Exception $e){
            $this->setError($row->getError());
            $return_value = false;
        }

        if($data['id'] == ''){
            $db = Factory::getDbo();
            $query = "UPDATE `#__js_ticket_tickets` SET attachmentdir = CONCAT(attachmentdir,id) WHERE id = ".$row->id;
            $db->setQuery($query);
            $db->execute();
        }

        if (isset($return_value) && $return_value == false) {
            return SAVE_ERROR;
        }

        $ticketid = $row->id;
        $ATTACHMENTRESULT = $this->getJSModel('attachments')->storeTicketAttachment($ticketid);
        if($ATTACHMENTRESULT !== true){
            return $ATTACHMENTRESULT;
        }

        // new
        //removing custom field attachments

        if($customflagfordelete == true){
            foreach ($custom_field_namesfordelete as $key) {
                $res = $this->removeFileCustom($ticketid,$key);
            }
        }
        //storing custom field attachments
        if($customflagforadd == true){
            foreach ($custom_field_namesforadd as $key) {
                if ($_FILES[$key]['size'] > 0) { // logo
                    $res = $this->uploadFileCustom($ticketid,$key);
                }
            }
        }

        if ($data['id'] == '')  // only for new ticket
            $this->getJSModel('email')->sendMail(1,1,$row->id); // Mailfor,Create Ticket,Ticketid

        JSSupportTicketMessage::$recordid = $ticketid;
        return SAVED;
    }

    function checkIsTicketDuplicate($subject,$email){
        if(empty($subject)) return false;
        if(empty($email)) return false;

        $curdate = date('Y-m-d H:i:s');
        $db = $this->getDbo();
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE email = " . $db->quote($email) . " AND subject = " . $db->quote($subject) . " ORDER BY created DESC LIMIT 1";
        $db->setQuery($query);
        $datetime = $db->loadResult();
        if($datetime){
            $diff = getJSTicketPHPFunctionsClass()->jsticket_strtotime($curdate) - getJSTicketPHPFunctionsClass()->jsticket_strtotime($datetime);
            if($diff <= 15){
                return false;
            }
        }
        return true;
    }

    function getAdminMyTickets($searchdepartmentid, $searchpriorityid, $searchsubject, $searchfrom, $searchfromemail, $searchticketid, $listtype, $sortby,$datestart, $dateend, $limitstart, $limit) {
        $db = $this->getDBO();
        $user = JSSupportTicketCurrentUser::getInstance();
        // $listtype == 1  - open
        // $listtype == 2  - answerd
        // $listtype == 4  - close
        // $listtype == 5  - all tickets

        $totalquery = "SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE 1 = 1 ";
        $query = "SELECT ticket.*, dep.departmentname AS departmentname, dep.id AS departmentid , priority.priority AS priority, priority.prioritycolour AS prioritycolour
                  FROM `#__js_ticket_tickets` AS ticket
                  JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                  LEFT JOIN `#__js_ticket_departments` AS dep ON ticket.departmentid = dep.id
                  WHERE 1=1";

            $userquery = '';
            $uid = getJSTicketPHPFunctionsClass()->jsticket_trim(Factory::getApplication()->input->get('uid'));
            if($uid != null && is_numeric($uid) && $uid > 0){
                $userquery = ' AND ticket.uid = '.$uid;
            }
            $data = getCustomFieldClass()->userFieldsForSearch(1);
            $valarray = array();
            if (!empty($data)) {
                foreach ($data as $uf) {
                    $valarray[$uf->field] = Factory::getApplication()->input->get($uf->field,NULL, 'post');
                    $jsresetbutton = Factory::getApplication()->input->get('jsresetbutton',0);
                    if($jsresetbutton == 1){
                        $valarray[$uf->field] = null;
                    }
                    if (isset($valarray[$uf->field]) && $valarray[$uf->field] != null) {
                        switch ($uf->userfieldtype) {
                            case 'text':
                            case 'file':
                            case 'email':
                                $query .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                $totalquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                break;
                            case 'combo':
                                $query .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                $totalquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'depandant_field':
                                $query .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                $totalquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'radio':
                                $query .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                $totalquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'checkbox':
                                $finalvalue = '';
                                // undefined error for variable
                                if(isset($valarray[$uf->field]) && !empty($valarray[$uf->field])){
                                    if(is_array($valarray[$uf->field])){
                                        foreach($valarray[$uf->field] AS $value){
                                            $finalvalue .= $value.'.*';
                                        }
                                    }else{
                                        $finalvalue .= $valarray[$uf->field].'.*';
                                    }
                                }
                                $query .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($finalvalue) . '.*"\' ';
                                $totalquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($finalvalue) . '.*"\' ';
                                break;
                            case 'date':
                                $tempdate = getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]);
                                $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                                if ($dateformat == 'm-d-Y') {
                                  $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $tempdate);
                                  $tempdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                                } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                                  $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $tempdate);
                                  $tempdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                                }
                                $tempdate = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($tempdate),"Y-m-d" );
                                $valarray[$uf->field] = $tempdate;
                                $query .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                $totalquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'textarea':
                                $query .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                $totalquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                break;
                            case 'multiple':
                                $finalvalue = '';
                                foreach($valarray[$uf->field] AS $value){
                                    if($value != null){
                                        $finalvalue .= $value.'.*';
                                    }
                                }
                                if($finalvalue !=''){
                                    $query .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*'.getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($finalvalue).'"\'';
                                    $totalquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*'.getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($finalvalue).'"\'';
                                }
                                break;
                        }
                        $params_filter = $valarray;
                    }
                }
            }


        if ($searchsubject <> ''){
            $searchsubject = getJSTicketPHPFunctionsClass()->jsticket_trim($searchsubject);
            $query .= " AND ticket.subject LIKE " . $db->quote('%' . $searchsubject . '%');
            $totalquery .= " AND ticket.subject LIKE " . $db->quote('%' . $searchsubject . '%');
        }
        if ($searchfrom <> ''){
            $searchfrom = getJSTicketPHPFunctionsClass()->jsticket_trim($searchfrom);
            $query .= " AND ticket.name LIKE " . $db->quote('%' . $searchfrom . '%');
            $totalquery .= " AND ticket.name LIKE " . $db->quote('%' . $searchfrom . '%');
        }
        if ($searchfromemail <> ''){
            $searchfromemail = getJSTicketPHPFunctionsClass()->jsticket_trim($searchfromemail);
            $query .= " AND ticket.email LIKE " . $db->quote('%' . $searchfromemail . '%');
            $totalquery .= " AND ticket.email LIKE " . $db->quote('%' . $searchfromemail . '%');
        }
        if ($searchticketid <> ''){
            $searchticketid = getJSTicketPHPFunctionsClass()->jsticket_trim($searchticketid);
            $query .= " AND ticket.ticketid LIKE " . $db->quote('%' . $searchticketid . '%');
            $totalquery .= " AND ticket.ticketid LIKE " . $db->quote('%' . $searchticketid . '%');
        }
        if ($searchdepartmentid <> ''){
            if(!is_numeric($searchdepartmentid)) return false;
            $query .= " AND ticket.departmentid = " . $searchdepartmentid;
            $totalquery .= " AND ticket.departmentid = " . $searchdepartmentid;
        }
        if ($searchpriorityid <> ''){
            if(!is_numeric($searchpriorityid)) return false;
            $query .= " AND ticket.priorityid = " . $searchpriorityid;
            $totalquery .= " AND ticket.priorityid = " . $searchpriorityid;
        }
        $config = $this->getJSModel('config')->getConfigs();
        $dateformat = $config['date_format'];
        if(isset($dateend) && !empty($dateend)){
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $dateend);
              $dateend = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $dateend);
              $dateend = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $dateend = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($dateend),"Y-m-d H:i:s" );
        }

        if(isset($datestart) && !empty($datestart) ){
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $datestart);
              $datestart = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $datestart);
              $datestart = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $datestart = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($datestart),"Y-m-d H:i:s" );
        }


        if(isset($datestart) && !empty($datestart)){
            $query .=" AND DATE(ticket.created) >= ".$db->quote($datestart);
            $totalquery .=" AND DATE(ticket.created) >= ".$db->quote($datestart);
        }
        if(isset($dateend) && !empty($dateend)){
            $query .=" AND DATE(ticket.created) <= ".$db->quote($dateend);
            $totalquery .=" AND DATE(ticket.created) <= ".$db->quote($dateend);
        }
        switch ($listtype) {
            case 1:
                $query .= " AND ticket.status != 4 AND ticket.isanswered = 0 AND ticket.status != 5";
                $totalquery .= " AND ticket.status != 4 AND ticket.isanswered = 0 AND ticket.status != 5";
                break;
            case 2:
                /*$query .= " AND ticket.status != 4 AND ticket.isanswered = 1 ";
                $totalquery .= " AND ticket.status != 4 AND ticket.isanswered = 1 ";*/
                $query .= " AND ticket.status = 3 AND ticket.isanswered = 1 ";
                $totalquery .= " AND ticket.status = 3 AND ticket.isanswered = 1 ";
                break;
            case 4:
                $query .= " AND (ticket.status = 4 OR ticket.status = 5)";
                $totalquery .= " AND (ticket.status = 4 OR ticket.status = 5)";
                break;
            case 5:
                $query .= " ";
                break;
        }
        $totalquery .= $userquery;

        $db = Factory::getDbo();
        $db->setQuery($totalquery);
        $total = $db->loadResult();

        if ($total <= $limitstart)
            $limitstart = 0;
        $query .= $userquery;
        $query .=  ' ORDER BY '.$sortby;
        $db->setQuery($query, $limitstart, $limit);
        $tickets = $db->loadObjectList(); // Tickets
        $ticketinfo = array();
        $config = $this->getJSModel('config')->getConfigs();
        if($config['show_count_tickets'] == 1){
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` as ticket
                        WHERE ticket.status != 4 AND ticket.isanswered = 0 AND ticket.status != 5";
            $query .= $userquery;
            $db->setQuery($query);
            $ticketinfo['open'] = $db->loadResult(); // Open Tickets

            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` AS ticket
                        WHERE (ticket.status = 4 OR ticket.status = 5)";
            $query .= $userquery;
            $db->setQuery($query);
            $ticketinfo['close'] = $db->loadResult(); // Closed Tickets

            //$query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status != 4 AND isanswered = 1";
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` AS ticket WHERE status = 3 AND isanswered = 1";
            $query .= $userquery;
            $db->setQuery($query);
            $ticketinfo['isanswered'] = $db->loadResult(); // IsAnswered Tickets

            $inquery = "";
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` AS ticket WHERE 1=1 ";
            $query .= $inquery;
            $query .= $userquery;
            $db->setQuery($query);
            $ticketinfo['mytickets'] = $db->loadResult(); // My Tickets
        }

        $departments = $this->getJSModel('department')->getDepartments();
        $priorities = $this->getPriorities();
        $lists['params'] =  isset($params_filter) ? $params_filter : '';
        $lists['datestart'] = $datestart;
        $lists['dateend'] = $dateend;
        $lists['searchsubject'] = $searchsubject;
        $lists['searchfrom'] = $searchfrom;
        $lists['searchfromemail'] = $searchfromemail;
        $lists['searchticket'] = $searchticketid;
        $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'filter_department', '', 'value', 'text',$searchdepartmentid);
        $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'filter_priority', '', 'value', 'text',$searchpriorityid);
        $result[0] = $tickets;
        $result[1] = $total;
        $result[2] = $lists;
        $result[3] = $ticketinfo;
        return $result;
    }

    function getPriorities() {

        $db = $this->getDBO();
        $user = JSSupportTicketCurrentUser::getInstance();
        $query = "SELECT * FROM `#__js_ticket_priorities` WHERE ispublic = 1";

        $priorities = array();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $priorities[] = array('value' => '', 'text' => Text::_('Select Priority'));
        foreach ($rows as $row) {
            $priorities[] = array('value' => $row->id, 'text' => Text::_($row->priority));
        }

        return $priorities;
    }

    function getusersearchajax() {
      $userlimit = Factory::getApplication()->input->get('userlimit',0);
      $maxrecorded = 4;
      $db = Factory::getDbo();
      $name = Factory::getApplication()->input->getString('name','');
      $username = Factory::getApplication()->input->getString('username','');
      $emailaddress = Factory::getApplication()->input->getString('emailaddress','');
      $wherequery = '';
      if($name!=''){
        $wherequery = " AND user.name LIKE ".$db->quote('%'.$name.'%');
      }
      if($username!=''){
        $wherequery = " AND user.username LIKE ".$db->quote('%'.$username.'%');
      }
      if($emailaddress!=''){
        $wherequery = " AND user.email LIKE ".$db->quote('%'.$emailaddress.'%');
      }
      $query = "SELECT DISTINCT COUNT(user.id)
              FROM `#__users` AS user
              WHERE 1 = 1 ";
      $query .= $wherequery;
      $db->setQuery($query);
      $total = $db->loadResult();
      $limit = $userlimit * $maxrecorded;
      if($limit >= $total){
          $limit = 0;
      }
      $query = "SELECT DISTINCT user.id AS userid, user.name AS displayname, user.email AS useremail, user.username AS username
              FROM `#__users` AS user
              WHERE 1 = 1 ";
      $query .= $wherequery;
      $query .= " LIMIT $limit, $maxrecorded ";
      $db->setQuery($query);
      $users = $db->loadObjectList();
      $html = $this->makeUserList($users,$total,$maxrecorded,$userlimit);
      return $html;
    }

    function makeUserList($users,$total,$maxrecorded,$userlimit){
        $html = '';
        if(!empty($users)){
            if(is_array($users)){
                $html ='
                <div class="js-ticket-table-wrp js-col-md-12">
                    <div class="js-ticket-table-header">
                        <div class="js-ticket-table-header-col js-col-md-2 js-col-xs-2">'.Text::_('User ID').'</div>
                        <div class="js-ticket-table-header-col js-col-md-3 js-col-xs-3">'.Text::_('User Name').'</div>
                        <div class="js-ticket-table-header-col js-col-md-4 js-col-xs-4">'.Text::_('Email Address').'</div>
                        <div class="js-ticket-table-header-col js-col-md-3 js-col-xs-3">'.Text::_('Name').'</div>
                    </div>
                    <div class="js-ticket-table-body">';
                foreach($users AS $user){
                    $html .='
                        <div class="js-ticket-data-row">
                            <div class="js-ticket-table-body-col js-col-md-2 js-col-xs-2">
                                <span class="js-ticket-display-block">'.Text::_('User ID').'</span>'.$user->userid.'
                            </div>
                            <div class="js-ticket-table-body-col js-col-md-3 js-col-xs-3">
                                <span class="js-ticket-display-block">'.Text::_('User Name').':'.'</span>
                                <span class="js-ticket-title"><a href="#" class="js-userpopup-link" data-id="'.$user->userid.'" data-email="'.$user->useremail.'" data-name="'.$user->username.'">'.$user->username.'</a></span>
                            </div>
                                <div class="js-ticket-table-body-col js-col-md-4 js-col-xs-4">
                                <span class="js-ticket-display-block">'.Text::_('Email').':'.'</span>
                                    '.$user->useremail.'
                                </div>
                                <div class="js-ticket-table-body-col js-col-md-3 js-col-xs-3">
                                    <span class="js-ticket-display-block">'.Text::_('Name').':'.'</span>
                           '.$user->displayname.'
                            </div>
                        </div>';
                }
                $html .='</div>';
                $num_of_pages = ceil($total / $maxrecorded);
                $num_of_pages = ($num_of_pages > 0) ? ceil($num_of_pages) : floor($num_of_pages);
                if($num_of_pages > 0){
                    $page_html = '';
                    $prev = $userlimit;
                    if($prev > 0){
                        $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist('.($prev - 1).');">'.Text::_('Previous').'</a>';
                    }
                    for($i = 0; $i < $num_of_pages; $i++){
                        if($i == $userlimit)
                            $page_html .= '<span class="jsst_userlink selected" >'.($i + 1).'</span>';
                        else
                            $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist('.$i.');">'.($i + 1).'</a>';

                    }
                    $next = $userlimit + 1;
                    if($next < $num_of_pages){
                        $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist('.$next.');">'.Text::_('Next').'</a>';
                    }
                    if($page_html != ''){
                        $html .= '<div class="jsst_userpages">'.$page_html.'</div>';
                    }
                }
            }
        }else{
            $html = messagesLayout::getRecordNotFound();
        }
        return $html;
    }
    private function ticketMultiSearch($searchkeys){
        $db = Factory::getDbo();
        $inquery="";
        $flag = true;
        if(!empty($searchkeys))
            if(isset($searchkeys['filter_ticketsearchkeys']) && !empty($searchkeys['filter_ticketsearchkeys'])){
                $keys = $searchkeys['filter_ticketsearchkeys'];
                if (getJSTicketPHPFunctionsClass()->jsticket_strlen($keys) == 11 || is_numeric($keys))
                    $inquery = " AND ticket.ticketid = ".$db->quote($keys);
                else if (getJSTicketPHPFunctionsClass()->jsticket_strpos($keys, '@') && getJSTicketPHPFunctionsClass()->jsticket_strpos($keys, '.'))
                    $inquery = " AND ticket.email LIKE ".$db->quote('%'.$keys.'%');
                else
                    $inquery = " AND ticket.subject LIKE ".$db->quote('%'.$keys.'%');
                $result['searchkeys'] = $keys;
                $flag = false;
            }else{
                if(isset($searchkeys['filter_ticketid']) && !empty($searchkeys['filter_ticketid'])){
                    $inquery =" AND ticket.ticketid = ".$db->quote($searchkeys['filter_ticketid']);
                    $result['ticketid'] = $searchkeys['filter_ticketid'];
                }
                if(isset($searchkeys['filter_from']) && !empty($searchkeys['filter_from'])){
                    $inquery .=" AND ticket.name LIKE ".$db->quote('%'.$searchkeys['filter_from'].'%');
                    $result['from'] = $searchkeys['filter_from'];
                }
                if(isset($searchkeys['filter_email']) && !empty($searchkeys['filter_email'])){
                    $inquery .=" AND ticket.email LIKE ".$db->quote('%'.$searchkeys['filter_email'].'%');
                    $result['email'] = $searchkeys['filter_email'];
                }
                if(isset($searchkeys['filter_department']) && !empty($searchkeys['filter_department'])){
                    $inquery .=" AND ticket.departmentid =".$searchkeys['filter_department'];
                    $result['department'] = $searchkeys['filter_department'];
                }
                if(isset($searchkeys['filter_priority']) && !empty($searchkeys['filter_priority'])){
                    $inquery .=" AND ticket.priorityid = ".$searchkeys['filter_priority'];
                    $result['priority'] = $searchkeys['filter_priority'];
                }
                if(isset($searchkeys['filter_subject']) && !empty($searchkeys['filter_subject'])){
                    $inquery .=" AND ticket.subject LIKE ".$db->quote('%'.$searchkeys['filter_subject'].'%');
                    $result['subject'] = $searchkeys['filter_subject'];
                }
                $config = $this->getJSModel('config')->getConfigs();
                if(isset($searchkeys['filter_datestart']) && !empty($searchkeys['filter_datestart'])){
                    $dateformat = $config['date_format'];
                    if ($dateformat == 'm-d-Y') {
                      $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $searchkeys['filter_datestart']);
                      $searchkeys['filter_datestart'] = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                    } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                      $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $searchkeys['filter_datestart']);
                      $searchkeys['filter_datestart'] = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                    $searchkeys['filter_datestart'] = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($searchkeys['filter_datestart']),"Y-m-d H:i:s" );
                    $inquery .=" AND DATE(ticket.created) >= ".$db->quote($searchkeys['filter_datestart']);
                    $result['datestart'] = $searchkeys['filter_datestart'];
                }
                if(isset($searchkeys['filter_dateend']) && !empty($searchkeys['filter_dateend'])){
                    $dateformat = $config['date_format'];
                    if ($dateformat == 'm-d-Y') {
                      $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $searchkeys['filter_dateend']);
                      $searchkeys['filter_dateend'] = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                    } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                      $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $searchkeys['filter_dateend']);
                      $searchkeys['filter_dateend'] = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                    $searchkeys['filter_dateend'] = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($searchkeys['filter_dateend']),"Y-m-d H:i:s" );
                    $inquery .=" AND DATE(ticket.created) <= ".$db->quote($searchkeys['filter_dateend']);
                    $result['dateend'] = $searchkeys['filter_dateend'];
                }
                if($inquery=="")
                    $result['iscombinesearch'] = false;
                else
                    $result['iscombinesearch'] = true;

            }

        //Custom field search
        //start

        $mainframe = Factory::getApplication();
        $option = 'com_jssupportticket';
        $data = getCustomFieldClass()->userFieldsForSearch(1);
        $valarray = array();
        $jsresetbutton = Factory::getApplication()->input->get('jsresetbutton', NULL , 'post');
        if (!empty($data)) {
            foreach ($data as $uf) {
                $valarray[$uf->field] = $mainframe->getUserStateFromRequest($option . $uf->field , $uf->field , '','string');
                if($jsresetbutton == 1){//to reset date fields
                    $mainframe->setUserState($option.$uf->field,null);
                    $valarray[$uf->field] = null;
                }
                if (isset($valarray[$uf->field]) && $valarray[$uf->field] != null) {
                    switch ($uf->userfieldtype) {
                        case 'text':
                        case 'file':
                        case 'email':
                            $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                            break;
                        case 'combo':
                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'depandant_field':
                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'radio':
                            if(isset($jsresetbutton)){
                                $mainframe->setUserState($option.$uf->field,'');
                                $valarray[$uf->field] = '';
                            }else{
                                $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            }
                            break;
                        case 'checkbox':
                            if(isset($jsresetbutton)){
                                $mainframe->setUserState($option.$uf->field,array());
                                $valarray[$uf->field] = array();
                            }else{
                                $finalvalue = '';
                                if(is_array($valarray[$uf->field])){
                                    foreach($valarray[$uf->field] AS $value){
                                        $finalvalue .= $value.'.*';
                                    }
                                }
                                $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($finalvalue) . '.*"\' ';
                            }
                            break;
                        case 'date':
                            $tempdate = getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]);
                            $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                            if ($dateformat == 'm-d-Y') {
                              $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $tempdate);
                              $tempdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                              $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $tempdate);
                              $tempdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                            }
                            $tempdate = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($tempdate),"Y-m-d" );
                            $valarray[$uf->field] = $tempdate;

                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'textarea':
                            $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                            break;
                        case 'multiple':
                            $finalvalue = '';
                            foreach($valarray[$uf->field] AS $value){
                                if($value != null){
                                    $finalvalue .= $value.'.*';
                                }
                            }
                            if($finalvalue !=''){
                                $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*'.getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($finalvalue).'"\'';
                            }
                            break;
                    }
                    $result['params'] = $valarray;
                }
            }
        }
        if($flag){
            if($inquery=="")
                $result['iscombinesearch'] = false;
            else
                $result['iscombinesearch'] = true;
        }

        //end

        $result['inquery'] = $inquery;
        return $result;
    }

    function getUserMyTickets($uid,$listtype,$searchkeys,$sortby,$limitstart,$limit) {
        if(!is_numeric($uid)) return false;
        $db = $this->getDBO();
        $multisearchquery = $this->ticketMultiSearch($searchkeys);
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.uid = ".$db->quote($uid);
        $listquery = '';
        $query .= $multisearchquery['inquery'];
        switch ($listtype) {
            case 1:
                $listquery .= " AND ticket.status != 4 ";
            break;
            case 3:
                $listquery .= " AND ticket.status != 4 AND ticket.isanswered=1 ";//AND ticket.status = 3
            break;
            case 4:
                $listquery .= " AND ticket.status = 4";
            break;
            case 5:
                $listquery .= " ";
            break;
        }
        $query .= $listquery;
        $db->setQuery($query);
        $total = $db->loadResult(); //Total Tickets
        if ($total <= $limitstart)
            $limitstart = 0;

        $query = "SELECT ticket.*,dep.departmentname AS departmentname, priority.priority AS priority, priority.prioritycolour AS prioritycolour,
                    (SELECT COUNT(attach.id) From `#__js_ticket_attachments` AS attach WHERE attach.ticketid = ticket.id) AS attachments
                        FROM `#__js_ticket_tickets` AS ticket
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        LEFT JOIN `#__js_ticket_departments` AS dep ON ticket.departmentid = dep.id
                        WHERE ticket.uid =".$uid;
        $departments = $this->getJSModel('department')->getDepartmentsForCombobox();
        $priorities = $this->getJSModel('priority')->getPrioritiesForCombobx(Text::_('Select Priority'));
        $departmentid = isset($multisearchquery['department']) ? $multisearchquery['department'] : '';
        $priorityid = isset($multisearchquery['priority']) ? $multisearchquery['priority'] : '';

        $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'filter_department', '', 'value', 'text',$departmentid);
        $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'filter_priority', '', 'value', 'text',$priorityid);

        $query .= $multisearchquery['inquery'];

        $query .= $listquery;
        $query .= " ORDER BY ".$sortby;

        $db->setQuery($query,$limitstart,$limit);
        $result = $db->loadObjectList(); //Tickets
        $ticketinfo = array();
        $config = $this->getJSModel('config')->getConfigs();
        if($config['show_count_tickets'] == 1){
            $query = "SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.status != 4 AND ticket.uid = $uid ";
			$query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['open'] = $db->loadResult(); // Open Tickets

            $query = "SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.status = 4 AND ticket.uid = $uid ";
			$query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['close'] = $db->loadResult(); // Closed Tickets

            $query = "SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE status != 4 AND isanswered = 1 AND ticket.uid = $uid ";
			$query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['isanswered'] = $db->loadResult(); // IsAnswered Tickets

            $query = "SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.uid = $uid ";
			$query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['mytickets'] = $db->loadResult(); // My Tickets
        }
        if($total == '') $total = 0;
        //$lists['searchticket'] = $searchticketid;
        $return[0] = $result;
        $return[1] = $total;
        $return[2] = $ticketinfo;
        $return[3] = $lists;
        $return[4] = $multisearchquery;
        return $return;
    }

    function getFormData($id,$data) {
        if($id) if (!is_numeric($id)) return false;
        $db = $this->getDBO();
        $user = JSSupportTicketCurrentUser::getInstance();

        $departments = $this->getJSModel('department')->getDepartmentsForCombobox();
        $priorities = $this->getJSModel('priority')->getPrioritiesForCombobx();
        if (isset($id) && $id <> '') {
            $query = "SELECT ticket.*,user.username AS uname
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__users` AS user ON user.id = ticket.uid
                        WHERE ticket.id = " . $db->quote($id);
            $db->setQuery($query);
            $editticket = $db->loadObject();
        }
        if (isset($editticket)) {
            $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'departmentid', 'class="inputbox js-form-select-field " ' . '', 'value', 'text', $editticket->departmentid);
            $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'priorityid', 'class="inputbox js-form-select-field  required" ' . '', 'value', 'text', $editticket->priorityid);
        } else {
            $query = "SELECT id FROM `#__js_ticket_priorities` WHERE isdefault = 1";
            $db->setQuery($query);
            $priority = $db->loadObject();
            $departmentid = isset($data['departmentid']) ? $data['departmentid'] : '';
            $priorityid = isset($data['priorityid']) ? $data['priorityid'] : $priority->id;
            $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'departmentid', 'class="inputbox js-form-select-field " ' . '', 'value', 'text', $departmentid);
            $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'priorityid', 'class="inputbox js-form-select-field  required" ' . '', 'value', 'text', $priorityid);
        }

        $model_userfields = $this->getJSModel('userfields');
        if (isset($editticket))
            $result[0] = $editticket;
        $result[1] = '';
        $result[2] = $lists;

        $result[4] = $model_userfields->getFieldsOrderingforForm(1);
        if (isset($id) && $id <> '') {
            $result[5] = $this->getJSModel('attachments')->getAttachmentForForm($id);
        }
        return $result;
    }
    function getTicketDetailById($id, $uid = '') {
        if (!is_numeric($id))
            return false;
        if($uid) if(!is_numeric($uid)) return false;
        $permission_granted = false;
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $permission_granted = true;
        }
        elseif (!$user->getIsGuest()){
            $permission_granted = $this->validateTicketDetailForUser($id);
            if (!$permission_granted) { // to show message when ticket exsists but current user not allowed to view it.
                $session = Factory::getApplication()->getSession();
                $ticketuserid = $session->get('ticketuserid',-1);
                if($ticketuserid == 0){
                    return 4;// when ticket belongs to visitor but logged in member trying to view it
                }else{
                    return 2;// when logged in tries to view ticket that does not belong to him
                }
            }
        }else{
            $permission_granted = $this->validateTicketDetailForVisitor($id);
            if (!$permission_granted) { // to show message when ticket exsists but visitor can not view it.
                return 3;
            }
        }
        $db = $this->getDbo();
        $result = array();

        $query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,
            attach.filename,attach.filesize,attach.id AS attachmentid,
            (SELECT COUNT(id) FROM `#__js_ticket_attachments` WHERE ticketid = ticket.id AND replyattachmentid = 0) AS count
            FROM `#__js_ticket_tickets` AS ticket
            JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
            LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
            LEFT JOIN `#__js_ticket_attachments` AS attach ON ticket.id = attach.ticketid AND (attach.replyattachmentid IS NULL OR attach.replyattachmentid = 0)
            WHERE ticket.id=" . $id;
        if($uid) $query .= " AND ticket.uid = $uid";
        $db->setQuery($query);
        $ticketdetails = $db->loadObjectList();

        // in replies staffid used as joomla userid
        $query = "SELECT replies.*, attachment.filename AS filename, attachment.filesize AS filesize, user.name AS name,attachment.id AS attachmentid,
                 (SELECT COUNT(id) FROM `#__js_ticket_attachments` WHERE ticketid = replies.ticketid AND replyattachmentid = replies.id) AS count
                 FROM`#__js_ticket_replies` AS replies
                 LEFT JOIN`#__users` AS user ON user.id = replies.staffid
                 LEFT JOIN `#__js_ticket_attachments` AS attachment ON replies.ticketid = attachment.ticketid AND replies.id = attachment.replyattachmentid
                 WHERE replies.ticketid = " . $id . " ORDER BY replies.created ASC";
        $db->setQuery($query);
        $replies = $db->loadObjectList();
        $app = Factory::getApplication();
        // to show current priority as selected in change piriorty combobox
        if(isset($ticketdetails[0])){
            $result[0] = $ticketdetails[0];
        }
        if (Factory::getApplication()->isClient('administrator')){
            $departments = $this->getJSModel('department')->getDepartmentsForCombobox();
            $priorities = $this->getJSModel('priority')->getPrioritiesForCombobx(Text::_("Select Priority"));
            $priorityid = '';
            if(isset($result[0]) && !empty($result[0])){
                $priorityid = $result[0]->priorityid;
            }
            $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'departmentid', 'class="inputbox" ' . '', 'value', 'text', '');
            $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'priorityid', 'class="inputbox"', 'value', 'text', $result[0]->priorityid);
        }

        $model_userfields = $this->getJSModel('userfields');
        if(isset($ticketdetails[0])){
            $result[0] = $ticketdetails[0];
        }

        $result[2] = $replies;
        if(isset($lists)) $result[3] = $lists;
        $result[6] = $ticketdetails;
        //$result[7] = $model_userfields->getUserFieldsForView(1, $id);
	    //attachment data
        $query = "SELECT published
                    FROM `#__js_ticket_fieldsordering` WHERE field = 'attachments'";
        $db->setQuery($query);
        $result['publishedInfo'] = $db->loadResult();
        return $result;
    }

    function ticketClose($ticketid ,$created) {
        if (!is_numeric($ticketid))
            return false;
        $user = JSSupportTicketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            if($user->getIsGuest()){
                $email = Factory::getApplication()->input->getString('email');
                $userTicket = $this->checkEmailAndTicketID($email,$ticketid);
            }else{
                $uid = $user->getId();
                $userTicket = $this->checkTicketIdAndUid($uid,$ticketid);
            }
            if (!$userTicket) {
                return OTHER_USER_TASK;
            }
        }

        $row = $this->getTable('tickets');
        $data['id'] = $ticketid;
        // $data['reopened'] = '';
        $data['status'] = 4;
        $data['closed'] = $created;
        $data['update'] = $created;

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            return TICKET_ACTION_ERROR;
        }
        $this->getJSModel('email')->sendMail(1,2,$ticketid); // Mailfor,Close Ticket,Ticketid

        return TICKET_ACTION_OK;
    }

    function reopenTicket($ticketid, $lastreply) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = Text::_('Reopen Ticket');
        $user = JSSupportTicketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $canreopen = $this->checkCanReopenTicket($ticketid, $lastreply);
            if ($canreopen == false) {
                return TIME_LIMIT_END;
            }
            if($user->getIsGuest()){
                $email = Factory::getApplication()->input->get('email');
                $userTicket = $this->checkEmailAndTicketID($email,$ticketid);
            }else{
                $uid = $user->getId();
                $userTicket = $this->checkTicketIdAndUid($uid,$ticketid);
            }
            if (!$userTicket) {
                return OTHER_USER_TASK;
            }
        }

        $row = $this->getTable('tickets');
        $data['id'] = $ticketid;
        $data['status'] = 0;
        $data['isanswered'] = 0;
        $data['reopened'] = date('Y-m-d H:i:s');
        $data['update'] = date('Y-m-d H:i:s');

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            return TICKET_ACTION_ERROR;
        }
        return TICKET_ACTION_OK;
    }

    function changeTicketPriority($ticketid, $priorityid, $created) {
        if (!is_numeric($ticketid))
            return false;
        if (!is_numeric($priorityid))
            return false;
        $row = $this->getTable('tickets');
        $data['id'] = $ticketid;
        $data['priorityid'] = $priorityid;
        $data['update'] = $created;
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->_db->getErrorMsg());
            $return_value = false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->_db->getErrorMsg());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            return PRIORITY_CHANGE_ERROR;
        }
        $this->getJSModel('email')->sendMail(1,11,$ticketid); // Mailfor,priority change,Ticketid
        return PRIORITY_CHANGED;
    }

    function checkCanReopenTicket($ticketid, $lastreply) {
        if (!is_numeric($ticketid))
            return false;
        $config_ticket = $this->getJSModel('config')->getConfigByFor('ticket');
        $days = $config_ticket['ticket_reopen_within_days'];
        if (!$lastreply)
            $lastreply = date('Y-m-d H:i:s');
        $date = date("Y-m-d H:i:s", getJSTicketPHPFunctionsClass()->jsticket_strtotime(date("Y-m-d H:i:s", getJSTicketPHPFunctionsClass()->jsticket_strtotime($lastreply)) . " +" . $days . " day"));
        if ($date < date('Y-m-d H:i:s'))
            return false;
        else
            return true;
    }

    function getTicketUserNameById($ticketid) {
        if (!is_numeric($ticketid))
            return false;
        $db = $this->getDbo();
        $query = "SELECT ticket.name From `#__js_ticket_tickets` AS ticket WHERE ticket.id = " . $ticketid;
        $db->setQuery($query);
        $name = $db->loadResult();
        return $name;
    }

    function updateStatus($id, $status, $created) {
        if (!is_numeric($id))
            return false;
        $row = $this->getTable('tickets');
        $data['id'] = $id;
        $data['status'] = $status; // Ticket Closed
        $data['closed'] = $created;
        $data['update'] = $created;

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            echo $row->getError();
            return false;
        }
        return true;
    }

    function getTicketIdForEmail($id) {
        if (!is_numeric($id))
            return false;
        $db = $this->getDbo();
        $query = "Select ticketid,email from `#__js_ticket_tickets` where id = " . $id;
        $db->setQuery($query);
        $ticket = $db->loadObject();
        return $ticket;
    }

    function getTicketAttachmentDir($id){
        if(!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = $id";
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function enforcedeleteTicket() {
        $id = Factory::getApplication()->input->get('cid');
        if (!is_numeric($id))
            return false;
        $session = Factory::getSession();
        $session->set('ticketid',$this->getTrackingIdById($id));
        $session->set('ticketemail',$this->getTicketEmailById($id));
        $session->set('ticketsubject',$this->getTicketSubjectById($id));
		$dir = $this->getTicketAttachmentDir($id);

        $db = $this->getDBO();
        $query = "DELETE ticket,reply,attach
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_replies` AS reply ON reply.ticketid = ticket.id
                        LEFT JOIN `#__js_ticket_attachments` AS attach ON attach.ticketid = ticket.id
                        WHERE ticket.id = " . $id;
        $db->setQuery($query);
        if (!$db->execute()) {
            return DELETE_ERROR;
        } else {
            //for email to sure ticket is deleted
			$this->getJSModel('attachments')->removeTicketAttachments( $dir );
			$this->getJSModel('email')->sendMail(1,3,$id); // Mailfor,Delete Ticket,Ticketid
            return DELETED;
        }
    }

    function deleteTicket() {
        $id = Factory::getApplication()->input->get('cid');
        if (!is_numeric($id))
            return false;
        $dir = $this->getTicketAttachmentDir($id);
        if($this->canDeleteTicket($id)){
            $db = $this->getDBO();
            $query = "DELETE ticket,attach
                            FROM `#__js_ticket_tickets` AS ticket
                            LEFT JOIN `#__js_ticket_attachments` AS attach ON attach.ticketid = ticket.id
                            WHERE ticket.id = " . $id;
            $db->setQuery($query);
            if (!$db->execute()) {
                return DELETE_ERROR;
            } else {
	        //for email to sure ticket is deleted
			$this->getJSModel('attachments')->removeTicketAttachments( $dir );
	        $this->getJSModel('email')->sendMail(1,3,$id); // Mailfor,Delete Ticket,Ticketid
                return DELETED;
            }
        }else{
            return IN_USE;
        }
    }

    function canDeleteTicket($id){
        if(!is_numeric($id)) return false;
        $db = Factory::getDbo();
        $query = "SELECT COUNT(reply.id) FROM `#__js_ticket_replies` AS reply WHERE reply.ticketid = $id";
        $db->setQuery($query);
        $result = $db->loadResult();
        if($result == 0)
            return true;
        else
            return false;
    }

    function checkEmailAndTicketID($email, $ticketid) {
        $db = $this->getDBO();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE email =" . $db->quote($email) . " AND ticketid =" . $db->quote($ticketid);
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function checkTicketIdAndUid($uid,$ticketid) {
        if(!is_numeric($uid)) return false;
        if(!is_numeric($ticketid)) return false;
        $db = $this->getDBO();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE uid =" . $uid . " AND id =" . $ticketid;
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function getIdFromTrackingId($ticketid) {
        $db = $this->getDBO();
        $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ticketid =" . $db->quote($ticketid);
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    private function sendEmail($id, $uid, $for, $message, $appendsignature, $to) {
        if ($to != '')
            $model_email = $this->getJSModel('email');
        switch ($to) {
            case 'user':
                $model_email->sendMail($id, $uid, 1, $message, '');
                break;
                break;
            case 'admin':
                $model_email->sendMailtoAdmin($id, $uid, 1, $message, '');
                break;
            case 'all':
                $model_email->sendMail($id, $uid, 1, $message, '');
                $model_email->sendMailtoAdmin($id, $uid, 1, $message, '');
                break;
        }
    }

    function getTicketId() {
        $db = $this->getDBO();
        $query = "SELECT ticketid FROM `#__js_ticket_tickets`";
        $ticketid_sequence = $this->getJSModel('config')->getConfigurationByName('ticketid_sequence');
        $match = '';
        $ticketid = "";
        do {
            if($ticketid_sequence == 1){ // Random ticketid
                $ticketid = "";
                $length = 13;
                $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
                $maxlength = getJSTicketPHPFunctionsClass()->jsticket_strlen($possible);
                if ($length > $maxlength) {
                    $length = $maxlength;
                }
                $i = 0;
                while ($i < $length) {
                    $char = getJSTicketPHPFunctionsClass()->jsticket_substr($possible, mt_rand(0, $maxlength - 1), 1);
                    if (!getJSTicketPHPFunctionsClass()->jsticket_strstr($ticketid, $char)) {
                        if ($i == 0) {
                            if (ctype_alpha($char)) {
                                $ticketid .= $char;
                                $i++;
                            }
                        } else {
                            $ticketid .= $char;
                            $i++;
                        }
                    }
                }
            }else{ // Sequential ticketid
                if($ticketid == ""){
                    $ticketid = 0; // by default its set to zero
                }
                $maxquery = "SELECT max(convert(ticketid, SIGNED INTEGER)) FROM `#__js_ticket_tickets`";
                $db->setQuery($maxquery);
                $maxticketid = $db->loadResult();
                if(is_numeric($maxticketid)){
                    $ticketid = $maxticketid + 1;
                }else{
                    $ticketid = $ticketid + 1;
                }
            }
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            foreach ($rows as $row) {
                if ($ticketid == $row->ticketid){
                    $match = 'Y';
                    break;
                }else{
                    $match = 'N';
                }
            }
        }while ($match == 'Y');

        return $ticketid;
    }

    function updateIsAnswered($ticketid,$isanswered) {
        if (!is_numeric($ticketid))
            return false;
        if(!is_numeric($isanswered)) return false;
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_tickets` set isanswered = $isanswered WHERE id = " . $ticketid;
        $db->setQuery($query);
        if (!$db->execute())
            return false;
        else
            return true;
    }

    function updateTicketLastReply($ticketid, $created) {
        if (!is_numeric($ticketid))
            return false;
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_tickets` set lastreply = " . $db->quote($created) . " WHERE id = " . $ticketid;
        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        } else {
            return true;
        }
    }

    function getLatestReplyByTicketId($id) {
        if (!is_numeric($id))
            return false;
        $db = Factory::getDBO();
        $query = "SELECT reply.message FROM `#__js_ticket_replies` AS reply WHERE reply.ticketid = " . $id . " ORDER BY reply.created DESC LIMIT 1";
        $db->setQuery($query);
        $message = $db->loadResult();

        return $message;
    }

    function saveResponceAJAX($id,$responce){
        if($id) if(!is_numeric($id)) return false;

        $user = JSSupportTicketCurrentUser::getInstance();
        $per = $user->checkUserPermission('Edit Ticket');
        if ($per == false) return PERMISSION_ERROR;
        $row = $this->getTable('replies');
        $data['id'] = $id;
        //$data['message'] = Factory::getApplication()->input->get('message', '', 'raw');
        $data['message'] = $responce;
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)){
            $this->setError($row->getError());
            return SENT_ERROR;
        }
        if (!$row->check()){
            $this->setError($row->getError());
            return SENT_ERROR;
        }
        if (!$row->store()){
            $this->setError($row->getError());
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            return SENT_ERROR;
        }
        return SAVED;
    }

    function editResponceAJAX($id){
        $db = $this->getDBO();
        if($id) if(!is_numeric($id)) return false;

        $query = "SELECT message FROM `#__js_ticket_replies` WHERE id = ".$id;
        $db->setQuery( $query );
        $row = $db->loadObject();
        $conf   = Factory::getConfig();
        $editor = Editor::getInstance($conf->get('editor'));
        if(isset($row)){
            $return_value =  $editor->display("editor_responce_$id", $row->message, "600", "400", "80", "15", 1, null, null, null, array('mode' => 'advanced'));
        }else{
            $return_value = $editor->display('editor_responce_'.$id, '', '550', '300', '60', '20', false);
        }

        $return_value .= '<br />
        <input type="button" class="tk_dft_btn" value="'.Text::_('Save').'" onclick="saveResponce('.$id.')">
        <input type="button" class="tk_dft_btn" value="'.Text::_('Close').'" onclick="closeResponce('.$id.')">';
        return $return_value;
    }

    function deleteResponceAJAX($id){
        if($id) if(!is_numeric($id)) return false;
        $user = JSSupportticketCurrentUser::getInstance();
        $per = $user->checkUserPermission('Delete Ticket');
        if ($per == false) return PERMISSION_ERROR;
        $row = $this->getTable('replies');
        if (!$row->delete($id)){
            $this->setError($row->getError());
            return DELETE_ERROR;
        }
        return DELETED;
    }
    function getUserListForRegistration() {
        $db = Factory::getDbo();
        $query = "SELECT DISTINCT user.ID AS userid, user.username AS username, user.email AS useremail, user.name AS userdisplayname
                    FROM `#__users` AS user ORDER BY userdisplayname";
        $db->setQuery($query);
        $users = $db->loadObjectList();
        return $users;
    }
    function getTicketSubjectById($id) {
        if (!is_numeric($id))
            return false;
            $db = Factory::getDbo();
        $query = "SELECT subject FROM `#__js_ticket_tickets` WHERE id = " . $id;
        $db->setQuery($query);
        $subject = $db->loadResult();
        return $subject;
    }

    function getTrackingIdById($id) {
        if (!is_numeric($id))
            return false;
            $db = Factory::getDbo();
        $query = "SELECT ticketid FROM `#__js_ticket_tickets` WHERE id = " . $id;
        $db->setQuery($query);
        $ticketid = $db->loadResult();
        return $ticketid;
    }

    function getTicketEmailById($id) {
        if (!is_numeric($id))
            return false;
            $db = Factory::getDbo();
        $query = "SELECT email FROM `#__js_ticket_tickets` WHERE id = " . $id;
        $db->setQuery($query);
        $ticketemail = $db->LoadResult();
        return $ticketemail;
    }

    function getDownloadAttachmentById($id){
        if(!is_numeric($id)) return false;
        $db = Factory::getDbo();
        $query = "SELECT ticket.id AS ticketid,attach.filename, ticket.attachmentdir "
                . " FROM `#__js_ticket_attachments` AS attach "
                . " JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = $id";
        $db->setQuery($query);
        $object = $db->loadObject();
        $ticketid = $object->ticketid;
        $filename = $object->filename;
        $attachmentdir = $object->attachmentdir;
        $download = false;
        $user = Factory::getUser();
        if(!$user->guest){
            if(Factory::getApplication()->isClient('administrator')){
                $download = true;
            }else{
                if($this->getJSModel('ticket')->validateTicketDetailForUser($ticketid)){
                    $download = true;
                }
            }
        }

        if($download == true){
            $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
            $base = JPATH_BASE;
            if(Factory::getApplication()->isClient('administrator')){
                $base = getJSTicketPHPFunctionsClass()->jsticket_substr($base, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($base) - 14); //remove administrator
            }
            $path = $base.'/'.$datadirectory;
            $path = $path . '/attachmentdata';
            $path = $path . '/ticket/' . $attachmentdir;
            $file = $path . '/' . $filename;
            header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . getJSTicketPHPFunctionsClass()->jsticket_basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // ob_clean();
            flush();
            readfile($file);
            exit();
        }else{
            throw new Exception(Text::_('Page not found'),404);
            exit;
        }
    }

    function getDownloadAttachmentByName($file_name,$id){
        if(empty($file_name)) return false;
        if(!is_numeric($id)) return false;
        $db = Factory::getDbo();
		$file_name = getJSTicketPHPFunctionsClass()->jsticket_basename($file_name);
        $filename = getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ', '_',$file_name);
        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = ".$id;
        $db->setQuery($query);
        $foldername = $db->loadResult();

        $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
        $base = JPATH_BASE;
        if(Factory::getApplication()->isClient('administrator')){
            $base = getJSTicketPHPFunctionsClass()->jsticket_substr($base, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($base) - 14); //remove administrator
        }
        $path = $base.'/'.$datadirectory;
        $path = $path . '/attachmentdata';
        $path = $path . '/ticket/' . $foldername;
        $file = $path . '/' . $filename;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . getJSTicketPHPFunctionsClass()->jsticket_basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        //ob_clean();
        flush();
        readfile($file);
        exit();
        exit;
    }

    function validateTicketDetailForUser($id) {
        if (!is_numeric($id))
            return false;
        if(Factory::getApplication()->isClient('administrator')) return true;
        $db = Factory::getDbo();
        $query = "SELECT uid FROM `#__js_ticket_tickets` WHERE id = " . $id;
        $db->setQuery($query);
        $uid = $db->loadResult();
        $user = Factory::getUser();
        if ($uid == $user->id) {
            return true;
        } else {
            return false;
        }
    }

    function validateTicketDetailForVisitor($id) {
        $session = Factory::getApplication()->getSession();
        $ticketid = $session->get('userticketid');
        $ticketid = $this->getJSModel('ticket')->getIdFromTrackingId($ticketid);
        if ($ticketid == $id) {
            return true;
        } else {
            return false;
        }
    }

    // new
    function uploadFileCustom($id,$field){
        if(! is_numeric($id)) return;

        $db = Factory::getDbo();

        $config = $this->getJSModel('config')->getConfigByFor('default');
        $model_attachment = $this->getJSModel('attachments');

        if ($_FILES[$field]['size'] > 0) {
            $file_name = getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ', '_', $_FILES[$field]['name']);
            $file_tmp = $_FILES[$field]['tmp_name']; // actual location
        }else{
            return;
        }
        $file_size = $config['filesize'];
        if($_FILES[$field]['size'] > ($file_size * 1024)){
            return;
        }
        if ($file_name != "" AND $file_tmp != "") {
            $is_allow = $model_attachment->checkExtension($file_name);
            if($is_allow == 'N'){
                return;
            }
        }
        $datadirectory = $config['data_directory'];
        $base = JPATH_BASE;
        if(Factory::getApplication()->isClient('administrator')){
            $base = getJSTicketPHPFunctionsClass()->jsticket_substr($base, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($base) - 14); //remove administrator
        }
        $path = $base.'/'.$datadirectory;
        if (!file_exists($path)){ // create user directory
            $model_attachment->makeDir($path);
        }
        $path = $path . '/attachmentdata';
        if (!file_exists($path)){ // create user directory
            $model_attachment->makeDir($path);
        }
        $path = $path . '/ticket';
        if (!file_exists($path)){ // create user directory
            $model_attachment->makeDir($path);
        }

        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = ".$id;
        $db->setQuery($query);
        $foldername = $db->loadResult();
        $userpath = $path . '/' . $foldername;
        if (!file_exists($userpath)) { // create user directory
            $model_attachment->makeDir($userpath);
        }
        move_uploaded_file($file_tmp, $userpath . '/' . $file_name);
        /*
        //Override the record and delete the old file if exists
        $query = "SELECT params FROM `#__js_ticket_tickets` WHERE id = ".$id;
        $db->setQuery($query);
        $params = $db->loadResult();
        $p_array = json_decode($params,true);
        //Remove old file if exists
        $old_file = $p_array[$field];
        if(file_exists($userpath . '/' . $old_file)){
            unlink($userpath . '/' . $old_file);
        }
        //--------------------------
        $p_array[$field] = $file_name;
        $params = json_encode($p_array);
        $query = "UPDATE `#__js_ticket_tickets` SET params = '".$params."' WHERE id = ".$id;
        $db->setQuery($query);
        $db->execute();
        */
        return;
    }

    function removeFileCustom($id, $key){
        $filename = getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ', '_', $key);

        if(! is_numeric($id))
            return;

        $db = Factory::getDbo();
        $config = $this->getJSModel('config')->getConfigByFor('default');
        $datadirectory = $config['data_directory'];

        $base = JPATH_BASE;
        if(Factory::getApplication()->isClient('administrator')){
            $base = getJSTicketPHPFunctionsClass()->jsticket_substr($base, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($base) - 14); //remove administrator
        }

        $path = $base . '/' . $datadirectory. '/attachmentdata/ticket';

        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = ".$id;
        $db->setQuery($query);
        $foldername = $db->loadResult();
        $userpath = $path . '/' . $foldername.'/'.$filename;
        unlink($userpath);
        return;
    }
}
?>
