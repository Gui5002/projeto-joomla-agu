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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Plugin\PluginHelper;

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelTicket extends JSSupportTicketModel {

    var $activity_log;
    var $_jinput = null;
    function __construct() {
        parent::__construct();

        $this->activity_log = $this->getJSModel('activitylog');
        $this->_jinput = Factory::getApplication()->input;
    }

    function getTicketNameById($id){
        if(!is_numeric($id))
            return $id;
        $db = $this->getDbo();
        $query = "SELECT subject FROM `#__js_ticket_tickets` WHERE id='".$id."'";
        $db->setQuery($query);
        $subject = $db->loadResult();
        return $subject;
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

    function storeTicket($data){

        // PluginHelper::importPlugin('jssupportticket');
        // $dispatcher = JDispatcher::getInstance();
        // $dispatcher->trigger( 'onSaveForm', array(&$data));

        //to check hash
        if($data['id'] != ''){
            $db = $this->getDbo();
            $query = "SELECT hash,uid FROM `#__js_ticket_tickets` WHERE ticketid='".$data['ticketid']."'";
            $db->setQuery($query);
            $row = $db->loadObject();
            $data['uid'] = $row->uid;
            if( $row->hash != $this->generateHash($data['id']) ){
                return false;
            }//end
        }

        $config = $this->getJSModel('config')->getConfigByFor('default');
        $user = JSSupportticketCurrentUser::getInstance();
        if(isset($data['ticketviaemail']) && $data['ticketviaemail'] == 1){
            $eventtype = Text::_('New Ticket via email');
        }else{
            $eventtype = Text::_('New Ticket');
        }


        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }else{
            // Set the role before the limit checks below, which can return early.
            // Without this $msg1 was never assigned for a non-admin author, so the
            // history line came out as "Ticket is created by test ()" - and reading
            // the undefined variable also raised a notice. Mirrors storeTicketReplies().
            $msg1 = Text::_('User');
            if($user->getIsGuest()){
                $msg1 = Text::_('Guest');
            }

            if($data['id'] == ''){
                $checkduplicatetk = $this->checkIsTicketDuplicate($data['subject'],$data['email']);
                if(!$checkduplicatetk){
                    return TICKET_DUPLICATE;
                }
            }


        }


        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string

        //for new ticket case
        if (($data['id']) == ''){
            $data['ticketid'] = $this->getTicketId();
            $data['attachmentdir'] = $this->getRandomFolderName();
            $data['created'] = date('Y-m-d H:i:s');
        }
        if ($data['id'] <> '') {// for edit case to change the overdue if criteria is passed
            $curdate = date('Y-m-d H:i:s');
            // date fucntion error null
        }

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
        $data['params'] = $params;
        // empty values causes problem
        if($data['uid'] == "") $data['uid'] = NULL;
        if($data['departmentid'] == "") $data['departmentid'] = NULL;

        $row = $this->getTable('tickets');
        if(!isset($data['ticketviaemail'])){
			$data['message'] = Factory::getApplication()->input->get('message', '', 'raw');
            //$data['message'] = $this->getJSModel('jssupportticket')->getHtmlInput('message');
			if(!$user->getIsAdmin())
				$data['uid'] = $user->getId();
		}
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
        if (getJSTicketPHPFunctionsClass()->jsticket_trim($row->helptopicid) == "") { $row->helptopicid = NULL; }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($row->id, 1, $eventtype, $message, 'Error');
            return SAVE_ERROR;
        }
        $ticketid = $row->id;
        if($data['id'] == ''){
            $db = Factory::getDbo();
            $hash = $this->generateHash($ticketid);
            $query = "UPDATE `#__js_ticket_tickets` SET attachmentdir = CONCAT(attachmentdir,id),hash='$hash' WHERE id = ".$ticketid;
            $db->setQuery($query);
            $db->execute();
        }
        $filesize = $config['filesize'];
        $total = isset($_FILES['filename']['name']) ? getJSTicketPHPFunctionsClass()->jsticket_count($_FILES['filename']['name']) : 0;
        $model_attachment = $this->getJSModel('attachments');
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['filename']['name'][$i] != '') {
                if ($_FILES['filename']['size'][$i] > 0) {
                    $uploadfilesize = $_FILES['filename']['size'][$i];
                    $uploadfilesize = $uploadfilesize / 1024; //kb
                    if ($uploadfilesize > $filesize) {
                        return FILE_SIZE_ERROR;
                    }
                    $file_name = getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                    $result = $model_attachment->checkExtension($file_name);
                    if ($result == 'N') {
                        return FILE_EXTENTION_ERROR;
                    }
                    $res = $model_attachment->uploadAttchments($i, $ticketid, 1, 0, 'ticket');
                    if ($res) {
                        $result = $this->storeTicketAttachment($ticketid, $uploadfilesize, $file_name);
                    }else{
                        return FILE_RW_ERROR;
                    }
                }
            }
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

        /*
        if (isset($data['issuesummary']) AND ! empty($data['issuesummary'])) {
            $result = $this->storeTicketIssueSummary($ticketid, $user->getId(), $user->getName(), $data['issuesummary'], $data['created']);
        }
        */


        if ($data['id'] == "") {
            $msg = Text::_('Ticket is created by');
        }else{
            $msg = Text::_('Ticket is updated by');
        }
        $message = $msg . " " . $user->getName() ." (" . $msg1 . ")";
        $this->activity_log->storeActivityLog($row->id, 1, $eventtype, $message, 'Sucessfull');

        if ($data['id'] == '')  // only for new ticket
            $this->getJSModel('email')->sendMail(1,1,$row->id); // Mailfor,Create Ticket,Ticketid

        JSSupportticketMessage::$recordid = $ticketid;
		//return $ticketid;
        return SAVED;
    }

    private function ticketMultiSearch($searchkeys){
        $db = Factory::getDbo();
        $inquery = "";
        $flag = true;
        if(!empty($searchkeys))
            if(isset($searchkeys['filter_ticketsearchkeys']) && !empty($searchkeys['filter_ticketsearchkeys'])){
                $keys = $searchkeys['filter_ticketsearchkeys'];
                $db = Factory::getDbo();
                $keys = getJSTicketPHPFunctionsClass()->jsticket_trim($keys);
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
                    $searchkeys['filter_ticketid'] = getJSTicketPHPFunctionsClass()->jsticket_trim($searchkeys['filter_ticketid']);
                    $inquery =" AND ticket.ticketid = ".$db->quote($searchkeys['filter_ticketid']);
                    $result['ticketid'] = $searchkeys['filter_ticketid'];
                }
                if(isset($searchkeys['filter_from']) && !empty($searchkeys['filter_from'])){
                    $searchkeys['filter_from'] = getJSTicketPHPFunctionsClass()->jsticket_trim($searchkeys['filter_from']);
                    $inquery .=" AND ticket.name LIKE ".$db->quote('%'.$searchkeys['filter_from'].'%');
                    $result['from'] = $searchkeys['filter_from'];
                }
                if(isset($searchkeys['filter_email']) && !empty($searchkeys['filter_email'])){
                    $searchkeys['filter_email'] = getJSTicketPHPFunctionsClass()->jsticket_trim($searchkeys['filter_email']);
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
                    $searchkeys['filter_subject'] = getJSTicketPHPFunctionsClass()->jsticket_trim($searchkeys['filter_subject']);
                    $inquery .=" AND ticket.subject LIKE ".$db->quote('%'.$searchkeys['filter_subject'].'%');
                    $result['subject'] = $searchkeys['filter_subject'];
                }
                $config = $this->getJSModel('config')->getConfigs();
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
                }

                if(isset($searchkeys['filter_datestart']) && !empty($searchkeys['filter_datestart']) ){
                    $dateformat = $config['date_format'];
                    if ($dateformat == 'm-d-Y') {
                      $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $searchkeys['filter_datestart']);
                      $searchkeys['filter_datestart'] = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                    } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                      $arr = getJSTicketPHPFunctionsClass()->jsticket_explode('-', $searchkeys['filter_datestart']);
                      $searchkeys['filter_datestart'] = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                    $searchkeys['filter_datestart'] = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($searchkeys['filter_datestart']),"Y-m-d H:i:s" );
                }


                if(isset($searchkeys['filter_datestart']) && !empty($searchkeys['filter_datestart'])){
                    $inquery .=" AND DATE(ticket.created) >= ".$db->quote($searchkeys['filter_datestart']);
                    $result['datestart'] = $searchkeys['filter_datestart'];
                }
                if(isset($searchkeys['filter_dateend']) && !empty($searchkeys['filter_dateend'])){
                    $inquery .=" AND DATE(ticket.created) <= ".$db->quote($searchkeys['filter_dateend']);
                    $result['dateend'] = $searchkeys['filter_dateend'];
                }
                if(isset($searchkeys['filter_assignedtome']) && $searchkeys['filter_assignedtome']==1){
                    $user = JSSupportticketCurrentUser::getInstance();
                    $inquery .=" AND ticket.staffid = ".$user->getStaffId();
                    $result['assignedtome'] = $searchkeys['filter_assignedtome'];
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
                            $check_string = json_encode($valarray[$uf->field]);
                            $check_string = getJSTicketPHPFunctionsClass()->jsticket_trim($check_string,'"');
                            $check_string = getJSTicketPHPFunctionsClass()->jsticket_str_replace('\\', '\\\\\\\\', $check_string);
                            $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars($check_string) . '.*"\' ';
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
                                if(isset($valarray) && isset($valarray[$uf->field]) && is_array($valarray[$uf->field])){
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


    function getAdminMyTickets($searchdepartmentid, $searchpriorityid, $searchstaffmember, $searchsubject, $searchfrom, $searchfromemail, $searchticketid, $listtype, $sortby,$datestart, $dateend, $limitstart, $limit) {

        $db = $this->getDBO();
        // Ticket Default Status
        // 0 -> New Ticket
        // 1 -> Waiting admin/staff reply
        // 2 -> in progress
        // 3 -> waiting for customer reply
        // 4 -> close ticket

        // $listtype == 1  - open
        // $listtype == 2  - answerd
        // $listtype == 3  - overdue
        // $listtype == 4  - close
        // $listtype == 5  - my tickets
		$totalquery = "SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE 1 = 1 ";
        $query = "SELECT ticket.*, dep.departmentname AS departmentname, dep.id AS departmentid, priority.priority AS priority, priority.prioritycolour AS prioritycolour
                  FROM `#__js_ticket_tickets` AS ticket
                  LEFT JOIN `#__js_ticket_departments` AS dep ON ticket.departmentid = dep.id
                  LEFT JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                  WHERE 1=1 ";

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
            case 3:
                $query .= " AND ticket.isoverdue = 1 AND (ticket.status != 4 OR ticket.status != 5) ";
                $totalquery .= " AND ticket.isoverdue = 1 AND (ticket.status != 4 OR ticket.status != 5) ";
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
                        WHERE ticket.status != 4 AND ticket.status != 5 AND ticket.isanswered = 0";
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
        $lists['searchsubject'] = $searchsubject;
        $lists['searchfrom'] = $searchfrom;
        $lists['searchfromemail'] = $searchfromemail;
        $lists['datestart'] = $datestart;
        $lists['dateend'] = $dateend;
        $lists['searchticket'] = $searchticketid;
        $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'filter_department', '', 'value', 'text',$searchdepartmentid);
        $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'filter_priority', '', 'value', 'text',$searchpriorityid);

        $result[0] = $tickets;
        $result[1] = $total;
        $result[2] = $lists;
        $result[3] = $ticketinfo;
        return $result;
    }

    function getUserMyTickets($email,$listtype,$searchkeys,$sortby,$limitstart,$limit) {
        if(!$email) return false;
        $db = $this->getDBO();
        $user = JSSupportticketCurrentUser::getInstance();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` AS ticket WHERE ";
        if(!$user->getIsGuest()){
            $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
        }else{
            $query .= "ticket.email = ".$db->quote($email);
        }

        switch ($listtype){
            case 1:
                $query .= " AND ticket.status != 4 AND ticket.status != 5";
            break;
            case 4:
                $query .= " AND (ticket.status = 4 OR ticket.status = 5)";
            break;
            case 2:
                $query .= " AND ticket.status != 4 AND ticket.status != 5 AND ticket.isanswered = 1";
            break;
            case 5:
                $query .= " ";
            break;
        }

        $multisearchquery = $this->ticketMultiSearch($searchkeys);

        $departments = $this->getJSModel('department')->getDepartments();
        $priorities = $this->getPriorities();
        $departmentid = isset($multisearchquery['department']) ? $multisearchquery['department'] : '';
        $priorityid = isset($multisearchquery['priority']) ? $multisearchquery['priority'] : '';

        $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'filter_department', '', 'value', 'text',$departmentid);
        $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'filter_priority', '', 'value', 'text',$priorityid);

        $query .= $multisearchquery['inquery'];
        $db->setQuery($query);
        $total = $db->loadResult(); //Total Tickets

        if ($total <= $limitstart)
            $limitstart = 0;

        $query = "SELECT ticket.*,dep.departmentname AS departmentname, priority.priority AS priority, priority.prioritycolour AS prioritycolour,
                    (SELECT COUNT(attach.id) From `#__js_ticket_attachments` AS attach WHERE attach.ticketid = ticket.id) AS attachments, (SELECT reply.name FROM `#__js_ticket_replies` AS reply WHERE ticket.id = reply.ticketid ORDER BY created DESC LIMIT 1 ) lastreplyby
                        FROM `#__js_ticket_tickets` AS ticket
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        LEFT JOIN `#__js_ticket_departments` AS dep ON ticket.departmentid = dep.id
                        WHERE ";
        if(!$user->getIsGuest()){
            $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
        }else{
            $query .= "ticket.email = ".$db->quote($email);
        }

        switch ($listtype){
            case 1:
                $query .= " AND ticket.status != 4 AND ticket.status != 5";
            break;
            case 4:
                $query .= " AND (ticket.status = 4 OR ticket.status = 5)";
            break;
            case 2:
                $query .= " AND ticket.status != 4 AND ticket.status != 5 AND ticket.isanswered = 1";
            break;
            case 5:
                $query .= " ";
            break;
        }

        $query .= $multisearchquery['inquery'];
        $query .= " ORDER BY ".$sortby;

        $db->setQuery($query,$limitstart,$limit);
        $result = $db->loadObjectList(); //Tickets
        $ticketinfo = array();
        $config = $this->getJSModel('config')->getConfigs();
        if($config['show_count_tickets'] == 1){
            $query = "SELECT COUNT(DISTINCT ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE ticket.status != 4 AND ticket.status != 5 AND ";
            if(!$user->getIsGuest()){
                $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
            }else{
                $query .= "ticket.email = ".$db->quote($email);
            }
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['open'] = $db->loadResult(); // Open Tickets

            $query = "SELECT COUNT(DISTINCT ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE ticket.status != 4 AND ticket.isanswered = 1 AND ticket.status != 5 AND ";
            if(!$user->getIsGuest()){
                $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
            }else{
                $query .= "ticket.email = ".$db->quote($email);
            }
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['answered'] = $db->loadResult(); // Answered Tickets

            $query = "SELECT COUNT(DISTINCT ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE (ticket.status = 4 OR ticket.status = 5) AND ";
            if(!$user->getIsGuest()){
                $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
            }else{
                $query .= "ticket.email = ".$db->quote($email);
            }
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['close'] = $db->loadResult(); // Closed Tickets

            $query = "SELECT COUNT(DISTINCT ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE ";
            if(!$user->getIsGuest()){
                $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
            }else{
                $query .= "ticket.email = ".$db->quote($email);
            }
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['allticket'] = $db->loadResult(); // Closed Tickets
        }

        $multisearchquery['inquery'] = ""; // empty the inquery

        if($total == '') $total = 0;

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
        $user = JSSupportticketCurrentUser::getInstance();
        $departments = $this->getJSModel('department')->getDepartments();
        $defaultdepartmentid = $this->getJSModel('department')->getDefaultDepartmentID();
        $priorities = $this->getPriorities();
        if ($id <> '') {
            $user = JSSupportticketCurrentUser::getInstance();
            $query = "SELECT ticket.*,ticket.name AS ticketname, user.name
                      FROM `#__js_ticket_tickets` AS ticket
                      LEFT JOIN `#__users` AS user ON user.id = ticket.uid
                      WHERE ticket.id = " . $db->quote($id);
            $db->setQuery($query);
            $editticket = $db->loadObject();
            //to store hash value of id against old tickets
            if(isset($editticket)){
                if( $editticket->hash == null ){
                    $hash = $this->generateHash($id);
                    $query = "UPDATE `#__js_ticket_tickets` SET `hash`='".$hash."' WHERE id=".$id;
                    $db->setQuery($query);
                    $db->execute();
                } //end
            }
        }
        if(isset($editticket))
            $premade = $this->getJSModel('premade')->getPremade($editticket->departmentid);
        else
            $premade = $this->getJSModel('premade')->getPremade($defaultdepartmentid);
       
        //get the required fields for combobox
        $userfieldmodel = $this->getJSModel('userfields');
        $reqdepartment = $userfieldmodel->isFieldRequiredByField('department') == 1 ? ' required' : '';
        $reqhelptopicid = $userfieldmodel->isFieldRequiredByField('helptopic') == 1 ? ' required' : '';
        $reqassignto = $userfieldmodel->isFieldRequiredByField('assignto') == 1 ? ' required' : '';

       if(isset($editticket)) {
            $departmentid = isset($data['departmentid']) ? $data['departmentid'] : $editticket->departmentid;
            $helptopicid = isset($data['helptopicid']) ? $data['helptopicid'] : $editticket->helptopicid;
            $priorityid = isset($data['priorityid']) ? $data['priorityid'] : $editticket->priorityid;
            $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'departmentid', 'class="js-form-select-field '.$reqdepartment.'" ' . 'onChange="gethelptopicandpremade(\'helptopic\',\'premades\', this.value)"', 'value', 'text', $departmentid);
            $lists['helptopic'] = HTMLHelper::_('select.genericList', $this->getJSModel('helptopic')->getHelpTopicForCombo($editticket->departmentid, Text::_('Select Help Topic')), 'helptopicid', 'class="js-form-select-field' .$reqhelptopicid.'" ' . '', 'value', 'text', $helptopicid);
            $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'priorityid', 'class="js-form-select-field required" ' . '', 'value', 'text', $priorityid);
        } else {
            $query = "SELECT id FROM `#__js_ticket_priorities` WHERE isdefault = 1";
            $db->setQuery($query);
            $priority = $db->loadObject();
            if(isset($data['departmentid'])){
                $departmentid = $data['departmentid'];
            }else if(Factory::getApplication()->input->get('departmentid') > 0){
                $departmentid = Factory::getApplication()->input->get('departmentid');
            }else{
                $departmentid = $defaultdepartmentid;
            }

            if(isset($data['helptopicid'])){
                $helptopicid = $data['helptopicid'];
            }else if(Factory::getApplication()->input->get('helptopicid') > 0){
                $helptopicid = Factory::getApplication()->input->get('helptopicid');
            }else{
                $helptopicid = 0;
            }
            $priorityid = '';
            if(isset($data['priorityid'])){
                $priorityid = $data['priorityid'];
            }elseif(isset($priority->id)){
                $priorityid = $priority->id;
            }
            $staffid = isset($data['staffid']) ? $data['staffid'] : $user->getID();
            $helptopiccombovalue = ($departmentid == '') ? $defaultdepartmentid : $departmentid;
            $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'departmentid', 'class="inputbox js-form-select-field '.$reqdepartment.'" ' . 'onChange="gethelptopicandpremade(\'helptopic\',\'premades\', this.value)"', 'value', 'text', $departmentid);
            $lists['helptopic'] = HTMLHelper::_('select.genericList', $this->getJSModel('helptopic')->getHelpTopicForCombo($helptopiccombovalue, Text::_('Select Help Topic')), 'helptopicid', 'class="inputbox js-form-select-field '.$reqhelptopicid.'" ' . '', 'value', 'text', $helptopicid);
            $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'priorityid', 'class="inputbox js-form-select-field required" ' . '', 'value', 'text', $priorityid);
        }

        $reqpremade = $userfieldmodel->isFieldRequiredByField('premade') == 1 ? ' required' : '';
        // $premade was bool FALSE in edit case which was showing error in joomla select code
        if(!$premade){
            $premade = array();
        }
        $lists['premade'] = HTMLHelper::_('select.genericList', $premade, 'premadeid', 'class="inputbox js-ticket-premade-select '.$reqpremade.'" ' . 'onChange="getpremade(\'issue_summary\', this.value ,append.checked)"', 'value', 'text', '');

        $ufields = $this->getJSModel('userfields');
        if (isset($editticket))
            $result[0] = $editticket;
        $result[1] = '';
        $result[2] = $lists;
        //$result[3] = $ufields->getUserFieldsForForm(1, $id);
        $result[4] = $ufields->getFieldsOrderingforForm(1);
        $result[5] = $this->getJSModel('attachments')->getAttachmentForForm($id);

        return $result;
        
    }


    function getTicketDetailById($id){
        if (!is_numeric($id))
            return false;
        $permission_granted = false;
        $time_taken = 0;
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $permission_granted = true;
        }

        $db = $this->getDbo();

        $inquery="";
        $query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,attach.id AS attachmentid,
            helptopic.topic AS helptopic ,attach.filename,attach.filesize,
            (SELECT COUNT(id) FROM `#__js_ticket_attachments` WHERE ticketid = ticket.id AND replyattachmentid = 0) AS count,ticket.priorityid
            FROM `#__js_ticket_tickets` AS ticket
            JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
            LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
            LEFT JOIN `#__js_ticket_help_topics` AS helptopic ON ticket.helptopicid = helptopic.id
            LEFT JOIN `#__js_ticket_attachments` AS attach ON ticket.id = attach.ticketid AND attach.replyattachmentid = 0
            WHERE ticket.id=" . $id;

        $query .= $inquery;
        $db->setQuery($query);
        $ticketdetails = $db->loadObjectList();
        $ticketemail = array();

        $query = "SELECT replies.*  
                FROM`#__js_ticket_replies` AS replies
                WHERE replies.ticketid = " . $id . " ORDER BY replies.created ASC";

        $db->setQuery($query);
        $replies = $db->loadObjectList();
        $attachmentmodel = $this->getJSModel('attachments');
        foreach ($replies AS $reply) {
            $reply->attachments = $attachmentmodel->getAttachmentForReply($id, $reply->id);
        }

        if($user->getIsAdmin()){
            if ($user->getIsAdmin()){
                $departments = $this->getJSModel('department')->getDepartments();
                $premade = $this->getJSModel('premade')->getPremade($ticketdetails[0]->departmentid);
            }

            $priorities = $this->getPriorities();
            $assign_staffid = '';
            $assign_departmentid = '';
            if(isset($ticketdetails[0])) $assign_departmentid = $ticketdetails[0]->departmentid;
            $lists['departments'] = HTMLHelper::_('select.genericList', $departments, 'departmentid', 'class="inputbox js-ticket-premade-select" ' . '', 'value', 'text', $assign_departmentid);
            $lists['premade'] = HTMLHelper::_('select.genericList', $premade, '', 'class="inputbox js-ticket-premade-select" ' . 'onChange="getpremade(\'responcemsg\', this.value ,append.checked)"', 'value', 'text', '');
            $lists['priorities'] = HTMLHelper::_('select.genericList', $priorities, 'priorityid', 'class="inputbox"', 'value', 'text', $ticketdetails[0]->priorityid);


            $config_ticket = $this->getJSModel('config')->getConfigByFor('ticket');

            $result[3] = $lists;
            $result[5] = $config_ticket;
        } //end is staff

        $ufields = $this->getJSModel('userfields');
        $tickethistory = $this->getTicketHistory($id);
        $result[0] = isset($ticketdetails[0]) ? $ticketdetails[0] : '';
        $result[2] = $replies;
        $result[6] = $ticketdetails;
        $result[11] = $ticketemail;
        //$result[7] = $ufields->getUserFieldsForView(1, $id);

        /*
         * Which ticket fields the current viewer is allowed to see.
         *
         * The view already expects this at index 8 (views/ticket/view.html.php),
         * but the assignment was commented out, so the detail page had no field
         * settings at all and rendered Department / Due Date / Help Topic
         * unconditionally - they stayed on screen as empty rows after being
         * unpublished in Ticket Fields.
         *
         * getFieldsOrderingforForm() is the right accessor: it applies
         * `published = 1` for logged-in users and `isvisitorpublished = 1` for
         * guests. The commented-out line called getFieldsOrdering(), which does
         * no such filtering.
         */
        $result[8] = $this->getJSModel('userfields')->getFieldsOrderingforForm(1);

        //get user tickets for right widget
        if($ticketdetails[0]->uid > 0){

            //count all ticket of user
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE `uid` = ".$ticketdetails[0]->uid;
            $db->setQuery($query);
            $result[12] = $db->loadResult();


            //get user tickets for right widget
            $inquery = " WHERE ticket.id != " . $id . " AND ticket.uid = " . $ticketdetails[0]->uid;

            $query = "SELECT ticket.id,ticket.subject,ticket.status,priority.priority AS priority,priority.prioritycolour AS prioritycolour,department.departmentname AS departmentname
                    FROM `#__js_ticket_tickets` AS ticket
                    LEFT JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                    LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id";
            $query .= $inquery . " LIMIT 3 ";
            $db->setQuery($query);
            $result[13] = $db->loadObjectList();
        }
        //attachment data
        $query = "SELECT published,isvisitorpublished
                    FROM `#__js_ticket_fieldsordering` WHERE field = 'attachments'";
        $db->setQuery($query);
        $result['publishedInfo'] = $db->loadObject();

        if($tickethistory) $result[9] = $tickethistory;
        return $result;
    }

    function ticketClose($ticketid ,$created,$banemailandcloseticket = false) { // cron flag is to check whether current call is cron call
        if (!is_numeric($ticketid))
            return false;
        $eventtype = Text::_('Close Ticket');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        // $row->reopened = '';
        $row->status = 4;
        $row->closed = $created;
        $row->update = $created;
        $row->isoverdue = 0;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $referenceid = $row->id;
        $msg = Text::_('Ticket is closed by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $result = $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');

        $this->getJSModel('email')->sendMail(1,2,$ticketid); // Mailfor,Close Ticket,Ticketid

        /*
         * Private Credentials is a Pro feature and this edition does not ship
         * models/privatecredentials.php. getJSModel() require_once's the file, and
         * a missing require_once is fatal - so closing a ticket died here, AFTER
         * the ticket had already been marked closed, logged and emailed. The
         * failure was invisible because no ticket in this install had ever been
         * closed.
         *
         * Guarded on the file rather than deleted so the line still works if this
         * model is ever merged back into the Pro codebase.
         */
        $credentialsModel = JPATH_COMPONENT_ADMINISTRATOR . '/models/privatecredentials.php';
        if (is_file($credentialsModel)) {
            // on ticket close make remove credentails data and show messsage on retrive.
            $this->getJSModel('privatecredentials')->deleteCredentialsOnCloseTicket($ticketid);
        }

        return TICKET_ACTION_OK;
    }

    function reopenTicket($ticketid, $lastreply) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = Text::_('Reopen Ticket');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }else{
            $canreopen = $this->checkCanReopenTicket($ticketid, $lastreply);
            if ($canreopen == false) {
                $msg = Text::_('Ticket reopen time limit end');
                $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
                $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
                return TIME_LIMIT_END;
            }

            if(!$user->getIsGuest()){
                $email = $user->getEmail();
                $userTicket = $this->isUserTicket($ticketid, $email);
                $msg1 = Text::_('User');
                if (!$userTicket) {
                    $msg = Text::_('You are not allowed');
                    $message = Text::_('User') . " ( " . $user->getName() . " ) " . $msg;
                    $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
                    return OTHER_USER_TASK;
                }
            }else{ //User Guest
                $msg1 = Text::_('Guest');
            }
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->status = 0;
        $row->reopened = date('Y-m-d H:i:s');
        $row->update = date('Y-m-d H:i:s');
        $row->lastreply = date('Y-m-d H:i:s');
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $referenceid = $row->id;
        $msg = Text::_('Ticket is reopened by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');

        return TICKET_ACTION_OK;
    }

    function changeTicketPriority($ticketid, $priorityid, $created) {
        if (!is_numeric($ticketid))
            return false;
        if (!is_numeric($priorityid))
            return false;
        $eventtype = Text::_('Change ticket priority');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }else{
            if($user->getIsGuest()){
                $email = $user->email;
                $userTicket = $this->isUserTicket($ticketid, $email);
                $msg1 = Text::_('User');
                if (!$userTicket) {
                    $msg = Text::_('You are not allowed');
                    $message = Text::_('User') . " ( " . $user->getName() . " ) " . $msg;
                    $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
                    return OTHER_USER_TASK;
                }
            }else return OTHER_USER_TASK;
        }
        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->priorityid = $priorityid;
        $row->update = $created;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $result = $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return PRIORITY_CHANGE_ERROR;
        }
        $referenceid = $row->id;
        $msg = Text::_('Ticket priority is changed by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $result = $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,11,$ticketid); // Mailfor,priority change,Ticketid
        return PRIORITY_CHANGED;
    }

    function ticketMarkInprogress($ticketid,$created) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = Text::_('Mark in Progress');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }else{
            $per = $user->checkUserPermission('Mark In Progress');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = Text::_('Staff');
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->status = 2;
        $row->update = $created;
        if (!$row->store()) {
            $this->setError($row->getError());
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }

        $referenceid = $row->id;
        $msg = Text::_('Ticket is marked as in progress by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,9,$ticketid); // Mailfor,inprogress tickert,Ticketid
        return TICKET_ACTION_OK;
    }

    function markOverDueTicket($ticketid,$created,$cron_flag = 0) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = Text::_('Mark overdue');
        $user = JSSupportticketCurrentUser::getInstance();
        $msg1 = "System";
        if($cron_flag == 0){
            if($user->getIsAdmin()){
                $msg1 = Text::_('Admin');
            }else{
                $per = $user->checkUserPermission('Mark Overdue');
                if ($per == false)
                    return PERMISSION_ERROR;
                $msg1 = Text::_('Staff');
            }
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->isoverdue = 1;
        $row->update = $created;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $referenceid = $row->id;
        $msg = Text::_('Ticket is marked as overdue by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,8,$ticketid); // Mailfor,over due Ticket,Ticketid
        return TICKET_ACTION_OK;
    }

    function unMarkOverDueTicket($ticketid,$created) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = Text::_('Mark overdue');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }else{
            $per = $user->checkUserPermission('Mark Overdue');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = Text::_('Staff');
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->isoverdue = 0;
        $row->update = $created;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $referenceid = $row->id;
        $msg = Text::_('Ticket is unmarked as overdue by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');
        //$this->getJSModel('email')->sendMail(1,8,$ticketid); // Mailfor,over due Ticket,Ticketid
        return TICKET_ACTION_OK;
    }

    function lockTicket($id) {
        if (!is_numeric($id))
            return false;
        $eventtype = Text::_('Lock ticket');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }else{
            $per = $user->checkUserPermission('Lock Ticket');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = Text::_('Staff');
        }

        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_tickets` AS ticket set ticket.lock = 1 WHERE id =" . $id;
        $db->setQuery($query);
        if (!$db->execute()) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $msg = Text::_('Ticket is locked by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,6,$id); // Mailfor,lock,Ticketid

        return TICKET_ACTION_OK;
    }

    function unlockTicket($id) {
        if (!is_numeric($id))
            return false;
        $eventtype = Text::_('Unlock ticket');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }else{
            $per = $user->checkUserPermission('Lock Ticket');
            if ($per == false){
                return PERMISSION_ERROR;
            }
            $msg1 = Text::_('Staff');
        }
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_tickets` AS ticket set ticket.lock = 0 WHERE ticket.id =" . $id;
        $db->setQuery($query);
        if (!$db->execute()) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $msg = Text::_('Ticket is unlocked by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,7,$id); // Mailfor,unlock,Ticketid
        return TICKET_ACTION_OK;
    }


    function storeTicketReplies($ticketid, $message, $created, $data2) {
        if (!is_numeric($ticketid))
            return false;

        $data2 = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data2);// Sanitize entire array to string

        //validate reply for break down
        $ticketrandomid   = $data2['ticketid'];
        $hash = $data2['hash'];
        $db = $this->getDBo();
        $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ticketid='$ticketrandomid' AND IF(hash is NULL,true,hash='$hash')";
        $db->setQuery($query);
        $res = $db->loadResult();
        if($res != $data2['id']){
            return false;
        }//end

        /*$ticketviaemailstaffid = 0;
        // set in ticket via email
        if(isset($data2['staffid'])){
            $ticketviaemailstaffid = $data2['staffid'];
            unset($data2['staffid']);
        }*/
        if(isset($data['ticketviaemail']) && $data['ticketviaemail'] == 1){
            $eventtype = Text::_('Reply ticket via email');
        }else{
            $eventtype = Text::_('Reply ticket');
        }

        $user = JSSupportticketCurrentUser::getInstance();
        $uname = $user->getName();


        if(isset($data2['appendsignature']) && $data2['appendsignature'] != 3){
            $id = $data2['id'];
            $appendSignature = $data2['appendsignature'];
            if ($appendSignature == 1) {
                $signature = $this->getJSModel('staff')->getStaffMemberSignature($user->getId());
            } elseif ($appendSignature == 2) {
                $signature = $this->getJSModel('department')->getDepartmentSignature($id);
            }
            $signature = getJSTicketPHPFunctionsClass()->jsticket_str_replace(Chr(13), '<br>', $signature);
            $message .= '<br/><br/>' . $signature;
        }

        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
            $status = 3;
        }else{
            $status = 1;
            $msg1 = Text::_('User');
            if($user->getIsGuest()){
                $msg1 = Text::_('Guest');
                $uname = $this->getReplierName($ticketid);
            }
        }
        $config = $this->getJSModel('config')->getConfigByFor('default');

        if(!$user->getIsAdmin() && $config['reply_to_closed_ticket'] != 1){ // to hanlde closed ticket reply confiration for user
            $row = $this->getTable('tickets');
            $row->load($ticketid);
            $closed = $row->status;
            if($closed == 4){
                $this->getJSModel('email')->sendMail(1,14,$ticketid); // sned to email to user when he tries to reply to a colsed ticket (email is sent to handle ticket via email case)
                return POST_ERROR;
            }
        }

        $res = $this->updateTicketStatus($ticketid,$status);
        if(!$res) return POST_ERROR;

        $row = $this->getTable('replies');
        $data['ticketid'] = $ticketid;
        // Default to 0, not '': an empty string binds into this integer column as
        // NULL, and the two reply paths then disagree (storeUserReplies() writes 0).
        // A later "WHERE staffid = 0" would silently skip every NULL row.
        $data['staffid'] = (isset($data2['staffid']) && $data2['staffid'] !== '') ? (int) $data2['staffid'] : 0;
        $data['name'] = $uname;
        $data['message'] = $message;
        $data['status'] = 1;
        $data['created'] = $created;
        //utf auto switch
        if($this->getJSModel('config')->getConfigurationByName('read_utf_ticket_via_email') == 1){
            $data['name'] = iconv_mime_decode($data['name'],0,"UTF-8");
        }

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
		$data['message'] = $message;

        $return_value = true;
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return MESSAGE_EMPTY;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return POST_ERROR;
        }
        $replyattachmentid = $row->id;
         if(!$user->getIsAdmin() && $return_value != false){// store time
            $data2['ticketid'] = $ticketid;
            $data2['timer_edit_desc'] = Factory::getApplication()->input->get('timer_edit_desc', '', 'raw');
            $this->getJSModel('staff')->storeTimeTaken($data2,$row->id,1);
        }
        $isanswered = 0;
        if($status == 3)
            $isanswered = 1;
        $res = $this->updateIsAnswered($ticketid,$isanswered);
        if(!$res) return POST_ERROR;

        $res = $this->updateTicketLastReply($ticketid,$created);
        if(!$res) return POST_ERROR;

        if (isset($data2['assigntomyself'])){
            $res = $this->updateTicketAssignToMyself($ticketid, $user->getStaffid());
            if(!$res) return POST_ERROR;
        }

        $total = 0;
        if(isset($_FILES['filename']))
            $total = getJSTicketPHPFunctionsClass()->jsticket_count($_FILES['filename']['name']);
        if ($total > 0) {
            $config = $this->getJSModel('config')->getConfigByFor('default');
            $filesize = $config['filesize'];
            $model_attachment = $this->getJSModel('attachments');
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['filename']['name'][$i] != '') {
                    if ($_FILES['filename']['size'][$i] > 0) {
                        $uploadfilesize = $_FILES['filename']['size'][$i];
                        $uploadfilesize = $uploadfilesize / 1024; //kb
                        if ($uploadfilesize > $filesize) { // filename
                            return FILE_SIZE_ERROR;
                        }
                        $file_name = getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                        $result = $model_attachment->checkExtension($file_name);
                        if ($result == 'N') { // filename
                            return FILE_EXTENTION_ERROR;
                        }
                        $res = $model_attachment->uploadAttchments($i, $ticketid, 1, 0, 'ticket');
                        if ($res == true) {
                            $result = $this->storeTicketAttachment($ticketid, $uploadfilesize, $file_name, $replyattachmentid);
                        }
                    }
                }
            }
        }
        //for ticket close
        if (isset($data2['replystatus']) && $data2['replystatus'] == 4) {
            $result = $this->updateTicketStatus($ticketid, $data2['replystatus'], $data2['created']);
            if ($result == false)
                return POST_ERROR;
            $referenceid = $ticketid;
            $msg = Text::_('Ticket is closed by');
            $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
            $this->activity_log->storeActivityLog($referenceid, 1, $eventtype, $message, 'Sucessfull');
        }

        $referenceid = $ticketid;
            $msg = Text::_('Ticket is replied by');
            $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";


        $this->activity_log->storeActivityLog($referenceid, 1, $eventtype, $message, 'Sucessfull');

        $replyfromadminstaff = 0;
        if($user->getIsAdmin()) $replyfromadminstaff = 1;

        if(isset($data2['ticketviaemail']) && $data2['ticketviaemail'] == 1){ // overwrite if ticket via email
            if($data2['status'] == 3) $replyfromadminstaff = 1;
        }
        if($replyfromadminstaff == 1){
            $this->getJSModel('email')->sendMail(1,4,$ticketid); // Mailfor,reply,Ticketid [admin/staffmember]
        }else{
            $this->getJSModel('email')->sendMail(1,5,$ticketid); // Mailfor,reply,Ticketid [user reply]

        }
        return POSTED;
    }

    function storeUserReplies() {
        $data = Factory::getApplication()->input->post->getArray();
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string

        /*
         * The form posts the PUBLIC tracking id in "ticketid" (e.g. Md4jtbQ8HCczw)
         * and the numeric row id in "id". Everything below needs the numeric one:
         * the tickets row is loaded by primary key, getReplierName() and
         * reopenTicket() both bail on !is_numeric(), and replies.ticketid is a
         * numeric column. Passing the tracking string meant the ticket row never
         * loaded, the replier name came back false, and the reply was bound with a
         * non-numeric ticketid that stored as 0 - so it never appeared in the thread.
         */
        $ticketid = (isset($data['id']) && is_numeric($data['id']) && (int) $data['id'] > 0)
            ? (int) $data['id']
            : (int) $this->getIdFromTrackingId(isset($data['ticketid']) ? $data['ticketid'] : '');

        if ($ticketid <= 0) {
            return SENT_ERROR;
        }

        // Normalise before anything binds $data to the replies row.
        $data['ticketid'] = $ticketid;

        // update status that reply from the USER
        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->status = 1;

        if($data['isreopen'] == 1) {
            $result = $this->reopenTicket($data['ticketid'], $data['lastreply']);
            if($result == TICKET_ACTION_ERROR)
                return SENT_ERROR;
            elseif($result != TICKET_ACTION_OK)
                return $result;
        }

        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SENT_ERROR;
        }

        $user = JSSupportticketCurrentUser::getInstance();
        $eventtype = Text::_('Reply ticket');
        $row = $this->getTable('replies');

        /*
         * The posted "id" is the TICKET's row id, but #__js_ticket_replies also has
         * an "id" primary key. Binding the whole POST array set the reply's PK, so
         * store() ran an UPDATE against a reply that does not exist instead of an
         * INSERT - it affected no rows, still returned true, and the caller happily
         * logged "Ticket is replied by ...". The reply was silently never saved.
         */
        unset($data['id']);

        $name = $this->getReplierName($ticketid);
        $data['name'] = $name;
        $data['staffid'] = 0; //Front end reply clue
        $data['status'] = 1; //Front end reply clue
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        $messagedata = Factory::getApplication()->input->get('message', '', 'raw');
        if ($messagedata == "") { // ticket is autoclose and close and reopen to handel editor show correctly change id message to messages
            $messagedata = Factory::getApplication()->input->get('messages', '', 'raw');
        }
        $data['message'] = $messagedata;

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $result = $this->activity_log->storeActivityLog($ticketid, 1,$eventtype,$message,'Error');
            return SENT_ERROR;
        }
        $msg = Text::_('Ticket is replied by');
        $msg1 = Text::_('User');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $result = $this->activity_log->storeActivityLog($ticketid, 1,$eventtype,$message,'Sucessfull');


        $config = $this->getJSModel('config')->getConfigByFor('default');
        $ticketid = $row->ticketid;
        $replyattachmentid = $row->id;
        $filesize = $config['filesize'];
        $total = getJSTicketPHPFunctionsClass()->jsticket_count((isset($_FILES['filename']['name'])) ? $_FILES['filename']['name'] : 0);
        if ($total > 0) {
            $model_attachment = $this->getJSModel('attachments');
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['filename']['name'][$i] != '') {
                    if ($_FILES['filename']['size'][$i] > 0) {
                        $uploadfilesize = $_FILES['filename']['size'][$i];
                        $uploadfilesize = $uploadfilesize / 1024; //kb
                        if ($uploadfilesize > $filesize) { // filename
                            return FILE_SIZE_ERROR;
                        }
                        $file_name = getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                        $result = $model_attachment->checkExtension($file_name);
                        if ($result == 'N') { // filename
                            return FILE_EXTENTION_ERROR;
                        }
                        $res = $model_attachment->uploadAttchments($i, $ticketid, 1, 0, 'ticket');
                        if ($res == true) {
                            $result = $this->storeReplyAttachment($ticketid, $replyattachmentid, $uploadfilesize, $file_name);
                        }
                    }
                }
            }
        }

        $this->getJSModel('email')->sendMail(1,5,$ticketid); // Mailfor,reply,Ticketid [ticket owner reply]
        //die('reply owner');
        return SENT;
    }

    function ticketDepartmentTransfer($ticketid,$departmentid, $note, $created, $data2) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = Text::_('Ticket department transfer');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }else{
            $per = $user->checkUserPermission('Ticket Department Transfer');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = Text::_('Staff');
        }
		
		if($created == "") $created = date("Y-m-d H:i:s");
        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->departmentid = $departmentid;
        $row->update = $created;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_TRANSFER_ERROR;
        }

        //for internal note
        $result = $this->storeTicketInternalNote($ticketid, $data2['staffid'], $note, $created, '');
        if ($result == false)
            return TICKET_TRANSFER_ERROR;

        $referenceid = $row->id;
        $msg = Text::_('Department is transfered by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ")";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');

        $this->getJSModel('email')->sendMail(1,12,$ticketid); // Mailfor,department transwer,Ticketid
        return TICKET_TRANSFERED;
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

    function storeTicketIssueSummary($ticketid, $uid, $name, $issuesummary, $created) {
        if (!is_numeric($ticketid))
            return false;
        if (!is_numeric($uid))
            return false;
		
		if($created == "") $created = date("Y-m-d H:i:s");
        $row = $this->getTable('replies');
        $data['ticketid'] = $ticketid;
        $data['staffid'] = $uid;
        $data['name'] = $name;
        $data['message'] = $issuesummary;
        $data['status'] = 1;
        $data['created'] = $created;

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return false;
        }
        return true;
    }

    function storeTicketAttachment($ticketid, $filesize, $filename, $replyattachmentid = 0) {
        if (!is_numeric($ticketid))
            return false;
        $row = $this->getTable('attachments');
        $data['ticketid'] = $ticketid;
        $data['replyattachmentid'] = $replyattachmentid; // this should set to zero when new ticket created
        $data['filename'] = $filename;
        $data['filesize'] = $filesize;
        $data['created'] = $curdate = date('Y-m-d H:i:s');

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return false;
        }
        return true;
    }

    function getReplierName($ticketid) {
        if (!is_numeric($ticketid))
            return false;
        $db = $this->getDbo();
        $query = "SELECT ticket.name From `#__js_ticket_tickets` AS ticket WHERE ticket.id = " . $ticketid;
        $db->setQuery($query);
        $name = $db->loadResult();
        return $name;
    }

    function storeReplyAttachment($ticketid, $replyattachmentid, $filesize, $filename) {
        if (!is_numeric($ticketid))
            return false;
        if (!is_numeric($replyattachmentid))
            return false;
        $row = $this->getTable('attachments');
        $data['ticketid'] = $ticketid;
        $data['replyattachmentid'] = $replyattachmentid;
        $data['filename'] = $filename;
        $data['filesize'] = $filesize;
        $data['created'] = $curdate = date('Y-m-d H:i:s');

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
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

    function checkForNewMessageSetting($id) {
        if (!is_numeric($id))
            return false;
        $db = Factory::getDBO();
        $query = "SELECT dep.messageautoresponce
                    FROM `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_departments` AS dep ON dep.id = ticket.departmentid
                    WHERE ticket.id = " . $id;
        $db->setQuery($query);
        $depsetting = $db->loadResult();
        return $depsetting;
    }

    function getAttachmentByTicketId($id){
        if(!is_numeric($id)) return false;
        $db = Factory::getDBO();
        $query = "SELECT attachment.filename , ticket.attachmentdir
                    FROM `#__js_ticket_attachments` AS attachment
                    JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = attachment.ticketid AND ticket.id =".$id. " AND attachment.replyattachmentid = 0 ";
        $db->setQuery($query);
        $attachments = $db->loadObjectList();
        return $attachments;
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

    function delete_Ticket($id) {
        if (!is_numeric($id))
            return false;
        $user = JSSupportticketCurrentUser::getInstance();
        $eventtype = Text::_('Delete ticket');
        if($user->getIsAdmin()){
            $msg1 = Text::_('Admin');
        }else{
            $per = $user->checkUserPermission('Delete Ticket');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = Text::_('Staff');
        }

        $row = $this->getTable('tickets');
        //for email get ticketid first
        $ticket = $this->getTicketIdForEmail($id);

        if ($this->ticketCanDelete($id) == true) {
            if (!$row->delete($id)) {
                $message = $row->getError();
                $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Error');
                return TICKET_ACTION_ERROR;
            }
            //for email to sure ticket is deleted
            $this->getJSModel('email')->sendMail(1,3,$id); // Mailfor,Delete Ticket,Ticketid
        }
        return TICKET_ACTION_OK;
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
        $db = $this->getDBO();

        $dir = $this->getTicketAttachmentDir($id);
        $query = "DELETE ticket,reply,attach
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_replies` AS reply ON reply.ticketid = ticket.id
                        LEFT JOIN `#__js_ticket_attachments` AS attach ON attach.ticketid = ticket.id
                        WHERE ticket.id = " . $id;
        $db->setQuery($query);
        if (!$db->execute()) {
            return DELETE_ERROR;
        } else {
            $this->getJSModel('attachments')->removeTicketAttachments( $dir );
            return DELETED;
        }
    }

    function deleteTicket() {
        $id = Factory::getApplication()->input->get('cid');

        $user = JSSupportticketCurrentUser::getInstance();

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
                $this->getJSModel('attachments')->removeTicketAttachments( $dir );
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

    function getEmailAndTicketIdById($id) {
        if(!is_numeric($id)) return false;
        $db = $this->getDBO();
        $query = "SELECT ticketid,email,subject,staffid FROM `#__js_ticket_tickets` WHERE id =" . $db->quote($id);
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }

    function checkEmailAndTicketID($email, $ticketid) {
        $db = $this->getDBO();
        $email = trim((string) $email);
        $ticketid = trim((string) $ticketid);
        if ($email === '' || $ticketid === '') {
            return 0;
        }
        try {
            $ownerWhere = "(ticket.email = " . $db->quote($email);
            $user = Factory::getUser();
            if (!$user->guest) {
                $ownerWhere .= " AND ticket.uid = " . (int) $user->id;
            }
            $ownerWhere .= ")";
            $watcherWhere = "EXISTS (SELECT watcher.id FROM `#__js_ticket_watchers` AS watcher WHERE watcher.ticketid = ticket.id AND watcher.status = 1 AND watcher.email = " . $db->quote($email) . ")";
            $query = "SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.ticketid = " . $db->quote($ticketid) . " AND (" . $ownerWhere . " OR " . $watcherWhere . ")";
            $db->setQuery($query);
            return $db->loadResult();
        } catch (Throwable $e) {
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE email =" . $db->quote($email) . " AND ticketid =" . $db->quote($ticketid);
            $user = Factory::getUser();
            if (!$user->guest) {
                $query .= " AND uid = " . (int) $user->id;
            }
            $db->setQuery($query);
            return $db->loadResult();
        }
    }

    function getIdFromTrackingId($ticketid) {
        $db = $this->getDBO();
        $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ticketid =" . $db->quote($ticketid);
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function isUserTicket($ticketid, $email) {

        if (!is_numeric($ticketid))
            return false;
        $db = $this->getDBO();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE email =" . $db->quote($email) . " AND id=" . $ticketid;
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result > 0)
            return true;
        return false;
    }
    function checkIsTicketDuplicate($subject,$email){
        if(empty($subject)) return false;
        if(empty($email)) return false;

        $curdate = date('Y-m-d H:i:s');
        $db = $this->getDbo();
        $subject = filter_var($subject, FILTER_SANITIZE_STRING);
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE email = " . $db->quote($email) . " AND subject = '" . $subject . "' ORDER BY created DESC LIMIT 1";
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

    function getPriorities() {

        $db = $this->getDBO();
        $user = JSSupportTicketCurrentUser::getInstance();
        if(Factory::getApplication()->isClient('administrator')){
            $query = "SELECT * FROM `#__js_ticket_priorities`";
        }else{
            $query = "SELECT * FROM `#__js_ticket_priorities` WHERE ispublic = 1";
        }

        $priorities = array();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $priorities[] = array('value' => null, 'text' => Text::_('Select Priority'));
        foreach ($rows as $row) {
            $priorities[] = array('value' => $row->id, 'text' => Text::_($row->priority));
        }

        return $priorities;
    }

    function getAttachmentByReplyId($id){
        if(!is_numeric($id)) return false;
        $db = $this->getDBO();
        $query = "SELECT attachment.filename , ticket.attachmentdir
                    FROM `#__js_ticket_attachments` AS attachment
                    JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = attachment.ticketid AND attachment.replyattachmentid = ".$id ;
        $db->setQuery($query);
        $replyattachments = $db->loadObjectList();
        return $replyattachments;
    }


    private function getTicketHistory($id) {
        if(!is_numeric($id)) return false;
        $db = $this->getDBO();

        $query = "SELECT al.id,al.message,al.datetime,al.uid,al.level
        from `#__js_ticket_activity_log`  AS al
        join `#__js_ticket_tickets` AS tic on al.referenceid=tic.id
        where al.referenceid=" . $id . " AND al.eventfor=1 ORDER BY al.datetime DESC ";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function updateTicketStatus($ticketid,$status,$created = false) {
        if (!is_numeric($ticketid) || (!is_numeric($status)))
            return false;
        $db = $this->getDbo();
        $inquery = '';
        if($status == 4 && $created)
            $inquery = ", ticket.closed = ".$db->quote($created)." , ticket.update = ".$db->quote($created);
        $query = "UPDATE `#__js_ticket_tickets` AS ticket SET ticket.status = $status ".$inquery." WHERE ticket.id = " . $ticketid;
        $db->setQuery($query);
        if (!$db->execute())
            return false;
        else
            return true;
    }

    function updateIsAnswered($ticketid,$isanswered) {
        if (!is_numeric($ticketid) || (!is_numeric($isanswered)))
            return false;
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

    function updateTicketAssignToMyself($ticketid, $staffid) {
        if (!is_numeric($ticketid))
            return false;
        if($staffid){
            $db = $this->getDbo();
            $query = "UPDATE `#__js_ticket_tickets` set staffid = " .$staffid. " WHERE id = " . $ticketid;
            $db->setQuery($query);
            if (!$db->execute()) {
                return false;
            } else {
                return true;
            }
        }
    }

    function isTicketAssigned($ticketid){
        if (!is_numeric($ticketid))
            return false;
        $query = "SELECT staffid FROM `#__js_ticket_tickets` WHERE id=".$ticketid;
        $db = $this->getDbo();
        $db->setQuery($query);
        $staffid = $db->loadResult();
        if($staffid > 0)
            return true;
        return false;
    }

    private function performChecks() {
        // $request = Factory::getApplication()->input->get();
        $request = Factory::getApplication()->input->post->getArray();
        $session = Factory::getApplication()->getSession();
        $type_calc = true;
        if ($type_calc) {
            if ($session->get('jsticket_rot13', null, 'jsticket_checkspamcalc') == 1) {
                $spamcheckresult = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding(str_rot13($session->get('jsticket_spamcheckresult', null, 'jsticket_checkspamcalc')));
            } else {
                $spamcheckresult = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($session->get('jsticket_spamcheckresult', null, 'jsticket_checkspamcalc'));
            }

            $spamcheck = Factory::getApplication()->input->getInt($session->get('jsticket_spamcheckid', null, 'jsticket_checkspamcalc'), '', 'post');

            $session->clear('jsticket_rot13', 'jsticket_checkspamcalc');
            $session->clear('jsticket_spamcheckid', 'jsticket_checkspamcalc');
            $session->clear('jsticket_spamcheckresult', 'jsticket_checkspamcalc');

            if (!is_numeric($spamcheckresult) || $spamcheckresult != $spamcheck) {
                return false; // Failed
            }
        }

        // Hidden field
        $type_hidden = 0;
        if ($type_hidden) {
            $hidden_field = $session->get('hidden_field', null, 'checkspamcalc');
            $session->clear('hidden_field', 'checkspamcalc');

            if (Factory::getApplication()->input->get($hidden_field, '', 'post')) {
                return false; // Hidden field was filled out - failed
            }
        }

        // Time lock
        $type_time = 0;
        if ($type_time) {
            $time = $session->get('time', null, 'checkspamcalc');
            $session->clear('time', 'checkspamcalc');

            if (time() - $this->params->get('type_time_sec') <= $time) {
                return false; // Submitted too fast - failed
            }
        }

        // Own Question
        // Conversion to lower case
        $session->clear('ip', 'jsticket_checkspamcalc');
        $session->clear('saved_data', 'jsticket_checkspamcalc');

        return true;
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

    function getFileSizeAndExtensions() {
        $file = array();
        $db = $this->getDBO();
        $query = "SELECT * FROM `#__js_ticket_config` WHERE configname ='filesize' OR configname='fileextension'";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result AS $res) {
            if ($res->configname == 'filesize') {
                $file['filesize'] = $res->configvalue;
            } elseif ($res->configname == 'fileextension') {
                $file['fileextension'] = $res->configvalue;
            }
        }
        return json_encode($file);
    }


    function saveResponceAJAX($id,$responce){
        if($id) if(!is_numeric($id)) return false;

        $user = JSSupportticketCurrentUser::getInstance();
        $per = $user->checkUserPermission('Edit Ticket');
        if ($per == false) return PERMISSION_ERROR;
        $row = $this->getTable('replies');
        $data['id'] = $id;

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string

        // The edited reply is HTML from the editor, so it is restored AFTER the
        // sanitize pass - jsticket_sanitizeData() uses the STRING filter and
        // strips every tag, which silently flattened edited replies to plain text.
        $data['message'] = $responce;
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
        return SENT;
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
            //$return_value = $editor->display("editor_responce_$id", $row->message, '550', '300', '60', '20', false);
            $return_value =  $editor->display("editor_responce_$id", $row->message, "600", "400", "80", "15", 1, null, null, null, array('mode' => 'advanced'));
        }else{
            $return_value = $editor->display('editor_responce_'.$id, '', '550', '300', '60', '20', false);
        }

        $return_value .= '<br />
        <input type="button" class="tk_dft_btn" value="'.Text::_('Post Reply').'" onclick="saveResponce('.$id.')">
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
            $this->setError($row->getErrorMsg());
            return SENT_ERROR;
        }
        return SENT;
    }

    function getDownloadAttachmentById($id){
        if(!is_numeric($id)) return false;
        $db = Factory::getDbo();
        $query = "SELECT ticket.id AS ticketid,attach.filename,ticket.attachmentdir AS foldername  "
                . " FROM `#__js_ticket_attachments` AS attach "
                . " JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = $id";
        $db->setQuery($query);
        $object = $db->loadObject();
        $ticketid = $object->ticketid;
        $filename = $object->filename;
        $foldername = $object->foldername;
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
        }else{ // user is visitor
            $download = $this->getJSModel('ticket')->validateTicketDetailForVisitor($ticketid);
        }
        if($download == true){
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
            ob_clean();
            flush();
            readfile($file);
            exit();
        }else{
            throw new Exception(Text::_('Page not found'),404);
            exit;
        }
    }
    function getAllDownloadFiles() {
        $downloadid = Factory::getApplication()->input->get('id');
        $ticketattachment = $this->getAttachmentByTicketId($downloadid);
        if(!class_exists('PclZip')){
            require_once('administrator/components/com_jssupportticket/include/lib/pclzip.lib.php');
        }
        $config = $this->getJSModel('config')->getConfigs();
        $path = JPATH_BASE.'/'.$config['data_directory'];
        $path .= '/zipdownloads';
        $this->getJSModel('attachments')->makeDir($path);
        $randomfolder = $this->getRandomFolderName($path);
        $path .= '/' . $randomfolder;
        $this->getJSModel('attachments')->makeDir($path);
        $archive = new PclZip($path . '/alldownloads.zip');
        $arr = array();
        foreach ($ticketattachment AS $ticketattachments) {
            $directory = JPATH_BASE .'/'. $config['data_directory'] . '/attachmentdata/ticket/' . $ticketattachments->attachmentdir . '/';
            // the below line commented was causing error ion scanned_directory variable null instead of array
            $scanned_directory = array_diff(scandir($directory), array('..', '.'));
            array_push($scanned_directory,$ticketattachments->filename);
            $arr[] = $ticketattachments->filename;
        }
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        $scanned_directory = array_diff(scandir($directory), array('..', '.','index.html'));
        $filelist = '';
        foreach ($scanned_directory AS $file) {
            if(in_array($file,$arr)){
                $filelist .= $directory . '/' . $file . ',';
            }
        }
        $filelist = getJSTicketPHPFunctionsClass()->jsticket_substr($filelist, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($filelist) - 1);
        $v_list = $archive->create($filelist, PCLZIP_OPT_REMOVE_PATH, $directory);
        if ($v_list == 0) {
            die("Error : '" . $archive->errorInfo() . "'");
        }
        $file = $path . '/alldownloads.zip';
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . getJSTicketPHPFunctionsClass()->jsticket_basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        @unlink($file);
        /*$path = jssupportticket::$_path;
        $path .= 'zipdownloads';
        $path .= '/' . $randomfolder;*/
        @unlink($path . '/index.html');
        rmdir($path);
        exit();
    }
     function getAllReplyDownloadsFiles() {
        $downloadid = Factory::getApplication()->input->get('id');
        $replyattachment = $this->getAttachmentByReplyId($downloadid);
        if(!class_exists('PclZip')){
            require_once('administrator/components/com_jssupportticket/include/lib/pclzip.lib.php');
        }
        $config = $this->getJSModel('config')->getConfigs();
        $path = JPATH_BASE.'/'.$config['data_directory'];
        $path .= '/zipdownloads';
        $this->getJSModel('attachments')->makeDir($path);
        $randomfolder = $this->getRandomFolderName($path);
        $path .= '/' . $randomfolder;
        $this->getJSModel('attachments')->makeDir($path);
        $archive = new PclZip($path . '/alldownloads.zip');
        $arr=array();
        foreach ($replyattachment AS $replyattachments) {
            $directory = JPATH_BASE .'/'. $config['data_directory'] . '/attachmentdata/ticket/' . $replyattachments->attachmentdir . '/';
            // the below line commented was giving fatal error
            $scanned_directory = array_diff(scandir($directory), array('..', '.'));
            array_push($scanned_directory,$replyattachments->filename);
            $arr[] = $replyattachments->filename;
        }
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        $scanned_directory = array_diff(scandir($directory), array('..', '.','index.html'));
        $filelist = '';
        foreach ($scanned_directory AS $file) {
            if(in_array($file,$arr)){
                $filelist .= $directory . '/' . $file . ',';
            }
        }
        $filelist = getJSTicketPHPFunctionsClass()->jsticket_substr($filelist, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($filelist) - 1);
        $v_list = $archive->create($filelist, PCLZIP_OPT_REMOVE_PATH, $directory);
        if ($v_list == 0) {
            die("Error : '" . $archive->errorInfo() . "'");
        }
        $file = $path . '/alldownloads.zip';
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . getJSTicketPHPFunctionsClass()->jsticket_basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        @unlink($file);
        /*$path = jssupportticket::$_path;
        $path .= 'zipdownloads';
        $path .= '/' . $randomfolder;*/
        @unlink($path . '/index.html');
        rmdir($path);
        exit();
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
        $session = Factory::getApplication()->getSession();
        $db = Factory::getDbo();
        $query = "SELECT uid,email FROM `#__js_ticket_tickets` WHERE id = " . $id;
        $db->setQuery($query);
        $ticket = $db->loadObject();
        $user_id = Factory::getUser();
        $user = JSSupportticketCurrentUser::getInstance();
        if(!empty($ticket->uid)){
            if (($ticket->uid == $user_id->id) || ( $ticket->uid == 0 && $ticket->email == $user->getEmail() ) )  {// seoncd check to handle tickets created as visitor using email of a logged in member
                return true;
            }else{
                $session->set('ticketuserid',$ticket->uid);
                return false;
            }
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

        if(! is_numeric($id))
            return;

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

    /// ...
    function getReplyDataByID() {
        $db = Factory::getDbo();
        $replyid = Factory::getApplication()->input->get('val');
        if(!is_numeric($replyid)) return false;
        $query = "SELECT reply.id AS replyid, reply.message AS message
                    FROM `#__js_ticket_replies` AS reply
                    WHERE reply.id =  " . $replyid ;
        $db->setQuery($query);
        $lastreply = $db->loadObject();

        return json_encode($lastreply);
    }

    function editReply($data) {
        $db = Factory::getDbo();
        if (empty($data))
            return false;
        //$desc = Factory::getApplication()->input->get( 'jsticket_replytext', '', 'post','string', JREQUEST_ALLOWHTML ); // use jsticket_message to avoid conflict
        $desc = Factory::getApplication()->input->get('jsticket_replytext', '', 'raw');
        $query = "UPDATE `#__js_ticket_replies` SET message = " . $db->Quote($desc) . "  WHERE id = " . $data['reply-replyid'];        $db->setQuery($query);
        $db->execute();
        return REPLY_EDITED;
    }

    function makeTicketList($tickets, $total, $maxrecorded, $ticketlimit,$ticketdata,$email,$name) {
        $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
                $html ='
                    <div class="jsst-popup-header">
                       <div class="popup-header-text">
                            '.Text::_("Merge Ticket").'
                        </div>
                        <div class="popup-header-close-img" id="close-pop"></div>
                    </div>
                    <div id="js-ticket-merge-ticket-wrp">
                    <div class="js-ticket-merge-ticket-wrapper">
                        <div class="js-col-xs-12 js-col-md-12 js-ticket-wrapper js-ticket-merge-white-bg">
                            <div class="js-col-xs-12 js-col-md-12 js-ticket-toparea">
                                <div class="js-col-md-2 js-col-xs-12 js-ticket-pic">';
                                    if ($ticketdata->staffphoto){
                                        $html .='<img class="js-ticket-staff-img" src=" '. Uri::root(). $datadirectory . "/staffdata/staff_" . $ticketdata->staffid . "/" . $ticketdata->staffphoto .' ">';
                                    }else {
                                        $html .='<img class="js-ticket-staff-img" src="' . Uri::root().'components/com_jssupportticket/include/images/user.png" />';
                                    }; $html .='
                                </div>
                                <div class="js-col-md-6 js-col-xs-6 js-ticket-data js-nullpadding">
                                    <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses subject">
                                        <span class="js-ticket-title">
                                           '.Text::_('Subject').':'.'&nbsp;:&nbsp
                                        </span>
                                        <a class="js-ticket-merge-ticket-title">' .$ticketdata->subject.' </a>
                                    </div>
                                    <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                        <span class="js-ticket-title">' .Text::_('From').':'.'&nbsp;:&nbsp;</span>
                                        <span class="js-ticket-value" style="cursor:pointer;">'. $ticketdata->name.'</span>
                                    </div>
                                    <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                        <span class="js-ticket-title">' .Text::_('Department').':'.'&nbsp;:&nbsp;</span>
                                        <span class="js-ticket-value" style="cursor:pointer;">'.$ticketdata->departmentname.'</span>
                                    </div>
                                </div>
                                <div class="js-col-md-4 js-col-xs-4 js-ticket-data1 js-ticket-padding-left-xs">
                                    <div class="js-row">
                                        <div class="js-col-md-6 js-col-xs-3">'.Text::_('Ticket ID').' : # '.'</div>
                                        <div class="js-col-md-6 js-col-xs-6">'. $ticketdata->id.'</div>
                                    </div>
                                    <div class="js-row">
                                        <div class="js-col-md-6 js-col-xs-3">'.Text::_('Created').' : '.'</div>
                                        <div class="js-col-md-6 js-col-xs-6">'.date( "Y-m-d", getJSTicketPHPFunctionsClass()->jsticket_strtotime($ticketdata->created)).'</div>
                                    </div>
                                    <div class="js-row">
                                        <div class="js-col-md-6 js-col-xs-3">'.Text::_('Priority').' : '.'</div>
                                        <div class="js-col-md-6 js-col-xs-6"><span class="js-ticket-wrapper-textcolor" style="background:' .$ticketdata->prioritycolour.'">'.Text::_($ticketdata->priorityname).'</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="js-tickets-list-wrp">
                        <form id="ticketpopupsearch" class="js-popup-search">
                            <div class="js-col-md-12 js-form-wrapper jsst-form-wrapper">
                                <div class="js-col-md-12 js-form-title js-merge-form-title js-bold-text">'. Text::_('Search Ticket to merge into') . '</div>
                                <div class="js-merge-form-wrp">
                                    <div class="js-form-value js-merge-form-value"><input class="inputbox js-merge-field" id="name" type="text" name="name" placeholder='.Text::_("Subject").' /></div>
                                    <div class="js-form-value js-merge-form-value"><input class="inputbox js-merge-field" id="email" type="text" name="email" placeholder='.Text::_("Email").'  /></div>
                                </div>
                                <div class="js-merge-form-btn-wrp">
                                    <span class="js-merge-btn"><input type="submit" value=' . Text::_('Search') . ' class="button js-merge-button js-search" /></span>
                                    <span class="js-merge-btn"><input type="submit" value=' . Text::_('Reset') . ' onclick="formField()"  class="button js-merge-button js-cancel" /></span>
                                </div>
                            </div>
                        </form>
                        <div class="js-col-md-12 js-view-tickets">';
                            if (!empty($tickets)) {
                                if (is_array($tickets)) {
                                    foreach ($tickets AS $ticket) {
                                        $html .= '
                                        <div class="js-col-xs-12 js-col-md-12 js-ticket-wrapper js-merge-ticket-overlay js-ticket-merge-white-bg">
                                            <div class="js-col-xs-12 js-col-md-12 js-ticket-toparea">
                                               <div class="js-col-xs-2 js-col-md-2 js-ticket-pic">';
                                                    if ($ticket->staffphoto){
                                                        $html .='<img class="js-ticket-staff-img" src=" '. Uri::root(). $datadirectory . "/staffdata/staff_" . $ticket->staffid . "/" . $ticket->staffphoto .' ">';
                                                    }else {
                                                        $html .='<img class="js-ticket-staff-img" src="' . Uri::root().'components/com_jssupportticket/include/images/user.png" />';
                                                    };
                                                $html .='</div>
                                                <div class="js-col-xs-6 js-col-md-6 js-col-xs-6 js-ticket-data js-nullpadding">
                                                    <div class="js-col-xs-12 js-col-md-12 js-ticket-padding-xs js-ticket-body-data-elipses subject">
                                                        <span class="js-ticket-title">
                                                            '.Text::_('Subject').':&nbsp;:&nbsp
                                                        </span>
                                                        <a class="js-ticket-merge-ticket-title">' .$ticket->subject.' </a>
                                                    </div>
                                                    <div class="js-col-xs-12 js-col-md-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                                        <span class="js-ticket-title">' .Text::_('From').':&nbsp;:&nbsp;</span>
                                                        <span class="js-ticket-value" style="cursor:pointer;">'. $ticket->username.'</span>
                                                    </div>
                                                    <div class="js-col-xs-12 js-col-md-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                                        <span class="js-ticket-title">' .Text::_('Department').':&nbsp;:&nbsp;</span>
                                                        <span class="js-ticket-value" style="cursor:pointer;">'.$ticket->departmentname.'</span>
                                                    </div>
                                                </div>
                                                <div class="js-col-xs-4 js-col-md-4 js-ticket-data1 js-ticket-padding-left-xs">
                                                    <div class="js-row">
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-3">'.Text::_("Ticket ID").' : '.'</div>
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-6">'. $ticket->id.'</div>
                                                    </div>
                                                    <div class="js-row">
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-3">'.Text::_('Created').': '.'</div>
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-6">'.date( "Y-m-d", getJSTicketPHPFunctionsClass()->jsticket_strtotime($ticket->created)).'</div>
                                                    </div>
                                                    <div class="js-row">
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-3">'.Text::_('Priority').': '.'</div>
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-6"><span class="js-ticket-wrapper-textcolor" style="background:' .$ticket->prioritycolour.'">'. Text::_($ticket->priorityname).'</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="js-over-lay">
                                                <a href="#" class="js-merge-btn" onclick=getmergeticketid('.$ticketdata->id.','.$ticket->id.')>'. Text::_("Select").'</a>
                                            </div>
                                        </div>';
                                    }
                                }
                            }else {
                                $html .= messageslayout::getRecordNotFound(1);
                                }
                        $html .= '</div>';
                            $num_of_pages = ceil($total / $maxrecorded);
                            $num_of_pages = ($num_of_pages > 0) ? ceil($num_of_pages) : floor($num_of_pages);
                            if($num_of_pages > 0){
                                $page_html = '';
                                $prev = $ticketlimit;
                                if($prev > 0){
                                    $page_html .= '<a class="jsst_userlink" href="#" onclick="updateticketlist('.($prev - 1).','.$ticketdata->id.');">'.Text::_('Previous').'</a>';
                                }
                                for($i = 0; $i < $num_of_pages; $i++){
                                    if($i == $ticketlimit)
                                        $page_html .= '<span class="jsst_userlink selected" >'.($i + 1).'</span>';
                                    else
                                        $page_html .= '<a class="jsst_userlink" href="#" onclick="updateticketlist('.$i.','.$ticketdata->id.');">'.($i + 1).'</a>';

                                }
                                $next = $ticketlimit + 1;
                                if($next < $num_of_pages){
                                    $page_html .= '<a class="jsst_userlink js-text-align-right" href="#" onclick="updateticketlist('.$next.','.$ticketdata->id.');">'.Text::_('Next').'</a>';
                                }
                                if($page_html != ''){
                                    $html .= '<div class="jsst_userpages">'.$page_html.'</div>';
                                }
                            }
                    $html .='</div>
                    <div class="js-col-md-12 js-form-button-wrapper js-form-button-wrapper-merge">
                        <input id="close-pop" type="button" onclick="closePopup()" value=' . Text::_('Cancel') . ' class="button js-merge-cancel-btn" />
                        <input type="hidden" id="ticketidformerge" value="'.$ticketdata->id.'" />
                    </div>
                    </div>';
        return $html;
    }


    function removeTicketReplies($ticketid) {
        if(!is_numeric($ticketid)) return false;
        $db = $this->getDBo();
        $query = "DELETE FROM `#__js_ticket_replies` WHERE ticketid = ".$ticketid;
        $db->setQuery($query);
        $db->execute($query);
        return;
    }

    static function generateHash($id){
        if(!is_numeric($id))
            return null;
        return getJSTicketPHPFunctionsClass()->jsticket_safe_encoding(json_encode(getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($id)));
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

    function getUserRemainMaxticket(){
        $uid = $this->_jinput->post->get('uid' , 0);
        if($uid == 0){
            $user = JSSupportticketCurrentUser::getInstance();
            $uid = $user->getId();

        }
        if(!is_numeric($uid))
            return false;
        $db = $this->getDbo();

        $config_ticket = $this->getJSModel('config')->getConfigByFor('default');
        $maxticketinterval = $config_ticket['maximum_ticket_interval_time'];
        switch($maxticketinterval){
            case "1": // maximum ticket in a day
                $checkdate = " AND date(created) = " . $db->quote(date('Y-m-d'));
                $msg = Text::_('COM_JSSUPPORTTICKET_MAX_REMAINING_TICKETS_DAY');
            break;
            case "2": // maximum ticket in month
                $checkdate = " AND MONTH(created) = " . $db->quote(date('m',getJSTicketPHPFunctionsClass()->jsticket_strtotime(date('Y-m-d'))));
                $msg = Text::_('COM_JSSUPPORTTICKET_MAX_REMAINING_TICKETS_MONTH');
            break;
            case "3": // maximum ticket in year
                $checkdate = " AND YEAR(created) = " . $db->quote(date('Y',getJSTicketPHPFunctionsClass()->jsticket_strtotime(date('Y-m-d'))));
                $msg = Text::_('COM_JSSUPPORTTICKET_MAX_REMAINING_TICKETS_YEAR');
            break;
            case "4": // maximum ticket in life time
                $checkdate = "";
                $msg = Text::_('COM_JSSUPPORTTICKET_MAX_REMAINING_TICKETS');
            break;
        }

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE uid = " . $uid . $checkdate; // ticket not answer and not closed
        $db->setQuery($query);
        $total = $db->loadResult();
        $ticketperemail = $config_ticket['maximum_ticket'];
        if ($total >= $ticketperemail) {
            $remaining = 0;
        }else{
            $remaining = $ticketperemail - $total;
        }
        $msg = sprintf($msg , $remaining);
        $html = '
        <div class="alert alert-success jsticket-remaining-ticket-wrap">
            <a class="close" data-dismiss="alert">×</a>
            <h4 class="alert-heading">Message</h4>
            <div>
                <div class="alert-message">'. $msg .'</div>
            </div>
        </div>
        ';
        return $html;
    }

    /**
     * Rows for the "Select user" popup on the admin ticket form.
     *
     * This lives on the ticket model because this edition has no staff feature:
     * the form's JS used to POST to `c=staff&task=getusersearchajax`, and
     * neither controllers/staff.php nor models/staff.php ship here, so the
     * request hit a missing controller and the popup stayed empty for ever.
     *
     * The Pro query also excludes accounts that are already staff, with
     * `NOT EXISTS (SELECT id FROM #__js_ticket_staff WHERE uid = user.id)`.
     * That table does not exist in this edition, so the clause is dropped
     * rather than ported - keeping it would turn an empty list into a SQL
     * error.
     *
     * @return string  Ready-to-inject HTML for div#records.
     */
    function getUserSearchAjax() {
        $app = Factory::getApplication();
        $db  = Factory::getDbo();

        $userlimit   = (int) $app->input->get('userlimit', 0);
        $maxrecorded = 4;

        $name         = $app->input->getString('name', '');
        $username     = $app->input->getString('username', '');
        $emailaddress = $app->input->getString('emailaddress', '');

        $wherequery = '';

        if ($name != '') {
            $name = getJSTicketPHPFunctionsClass()->jsticket_trim($name);
            $wherequery .= " AND user.name LIKE " . $db->quote('%' . $name . '%');
        }
        if ($username != '') {
            $username = getJSTicketPHPFunctionsClass()->jsticket_trim($username);
            $wherequery .= " AND user.username LIKE " . $db->quote('%' . $username . '%');
        }
        if ($emailaddress != '') {
            $emailaddress = getJSTicketPHPFunctionsClass()->jsticket_trim($emailaddress);
            $wherequery .= " AND user.email LIKE " . $db->quote('%' . $emailaddress . '%');
        }

        $db->setQuery("SELECT COUNT(user.id) FROM `#__users` AS user WHERE user.block = 0 " . $wherequery);
        $total = (int) $db->loadResult();

        $limit = $userlimit * $maxrecorded;
        if ($limit >= $total) {
            $limit = 0;
        }

        $db->setQuery(
            "SELECT user.id AS userid, user.username AS username, user.email AS useremail, user.name AS displayname
             FROM `#__users` AS user WHERE user.block = 0 " . $wherequery
            . " ORDER BY user.name ASC LIMIT " . (int) $limit . ", " . (int) $maxrecorded
        );
        $users = $db->loadObjectList();

        return $this->makeUserSearchList($users, $total, $maxrecorded, $userlimit);
    }

    /**
     * Table + pager markup for getUserSearchAjax().
     *
     * messagesLayout::getRecordNotFound() prints rather than returns, so the
     * empty state is captured - otherwise it would escape ahead of whatever the
     * controller echoes and land outside div#records.
     */
    function makeUserSearchList($users, $total, $maxrecorded, $userlimit) {
        if (empty($users) || !is_array($users)) {
            ob_start();
            messagesLayout::getRecordNotFound();
            return (string) ob_get_clean();
        }

        $html = '
                <div class="js-ticket-table-wrp js-col-md-12">
                    <div class="js-ticket-table-header">
                        <div class="js-ticket-table-header-col js-col-md-2 js-col-xs-2">' . Text::_('User ID') . '</div>
                        <div class="js-ticket-table-header-col js-col-md-3 js-col-xs-3">' . Text::_('Username') . '</div>
                        <div class="js-ticket-table-header-col js-col-md-4 js-col-xs-4">' . Text::_('Email Address') . '</div>
                        <div class="js-ticket-table-header-col js-col-md-3 js-col-xs-3">' . Text::_('Name') . '</div>
                    </div>
                    <div class="js-ticket-table-body">';

        foreach ($users as $user) {
            $userid      = (int) $user->userid;
            $username    = htmlspecialchars((string) $user->username, ENT_QUOTES, 'UTF-8');
            $useremail   = htmlspecialchars((string) $user->useremail, ENT_QUOTES, 'UTF-8');
            $displayname = htmlspecialchars((string) $user->displayname, ENT_QUOTES, 'UTF-8');

            $html .= '
                        <div class="js-ticket-data-row">
                            <div class="js-ticket-table-body-col js-col-md-2 js-col-xs-2">
                                <span class="js-ticket-display-block">' . Text::_('User ID') . '</span>' . $userid . '
                            </div>
                            <div class="js-ticket-table-body-col js-col-md-3 js-col-xs-3">
                                <span class="js-ticket-display-block">' . Text::_('Username:') . '</span>
                                <span class="js-ticket-title"><a href="#" class="js-userpopup-link" data-id="' . $userid . '" data-email="' . $useremail . '" data-name="' . $displayname . '">' . $username . '</a></span>
                            </div>
                            <div class="js-ticket-table-body-col js-col-md-4 js-col-xs-4">
                                <span class="js-ticket-display-block">' . Text::_('Email:') . '</span>
                                ' . $useremail . '
                            </div>
                            <div class="js-ticket-table-body-col js-col-md-3 js-col-xs-3">
                                <span class="js-ticket-display-block">' . Text::_('Name:') . '</span>
                                ' . $displayname . '
                            </div>
                        </div>';
        }

        $html .= '</div></div>';

        $num_of_pages = (int) ceil($total / $maxrecorded);

        if ($num_of_pages > 1) {
            $page_html = '';

            if ($userlimit > 0) {
                $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist(' . ($userlimit - 1) . '); return false;">' . Text::_('Previous') . '</a>';
            }

            for ($i = 0; $i < $num_of_pages; $i++) {
                if ($i == $userlimit) {
                    $page_html .= '<span class="jsst_userlink selected">' . ($i + 1) . '</span>';
                } else {
                    $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist(' . $i . '); return false;">' . ($i + 1) . '</a>';
                }
            }

            if (($userlimit + 1) < $num_of_pages) {
                $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist(' . ($userlimit + 1) . '); return false;">' . Text::_('Next') . '</a>';
            }

            $html .= '<div class="jsst_userpages">' . $page_html . '</div>';
        }

        return $html;
    }
}
?>
