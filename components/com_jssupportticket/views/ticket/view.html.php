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

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Pagination\Pagination;

class JSSupportTicketViewTicket extends JSSupportticketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
		if($layoutName == 'ticketdetail'){

			$checkstatus = Factory::getApplication()->input->get('checkstatus',null,'post');
			$jsticket = Factory::getApplication()->input->get('jsticket',null,'get');
			if($jsticket != null || $checkstatus == 1){
				
				if($checkstatus == 1){
					$ticketid = Factory::getApplication()->input->get('ticketid');
					$email = Factory::getApplication()->input->getString('email');
				}else{
					$jsticket = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($jsticket);
					$array = getJSTicketPHPFunctionsClass()->jsticket_explode(',', $jsticket);
					$ticketid = $array[0];
					$email = $array[1];
				}
				$res = $this->getJSModel('ticket')->checkEmailAndTicketID($email,$ticketid);
				if($res == 1){
					$session = Factory::getApplication()->getSession();
					$session->set('userticketid',$ticketid);
					$session->set('useremail',$email);
					$id = $this->getJSModel('ticket')->getIdFromTrackingId($ticketid);					
				}else{
					$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketstatus&Itemid='.$Itemid;
			        $app = Factory::getApplication();
			        $app->enqueueMessage(Text::_('Record not found'), 'fail');
			        $app->redirect(Route::_($link , false));
				}
				$result = $this->getJSModel('ticket')->getTicketDetailById($id);
			}else{
				$id = Factory::getApplication()->input->get('id' , null);
				$email = Factory::getApplication()->input->get('email');
				//for email and tracking id
				if(!is_numeric($id)){
					$jsticket = Factory::getApplication()->input->get('jsticket',null,'get');
					$jsticket = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($jsticket);
					$array = getJSTicketPHPFunctionsClass()->jsticket_explode(',', $jsticket);
					$id = $array[0];
					$email = $array[1];
					$result = $this->getJSModel('ticket')->checkEmailAndTicketID($email,$id);
					if($result == 1){
						$id = $this->getJSModel('ticket')->getIdFromTrackingId($id);
					}
				}
				$uid = $user->getId();
				$result = $this->getJSModel('ticket')->getTicketDetailById($id,$uid);
			}

			$this->isAttachmentPublished = $result['publishedInfo'];
			$this->ticketdetail = $result[0];
			$this->messages = $result[2];
			$this->attachment = $result[6];
			$this->email = $email;
			$this->id = $id;
			if(isset($result[7])) $this->userfields = $result[7];
		}elseif($layoutName == 'formticket'){
			$allow_ticket_form = 1;
			$id = Factory::getApplication()->input->get('id');
			if($id == '') { // allow ticket form for new case
            	$allow_ticket_form = 1;
            }else{ // hide ticket form in edit case for non staff users
            	$allow_ticket_form = 0;
            }
			$data = $mainframe->getUserState('com_jssupportticket.data');
			$mainframe->setUserState('com_jssupportticket.data',null);
			$result = $this->getJSModel('ticket')->getFormData($id,$data);
			$this->data = $data;
			$this->lists = $result[2];
			if(isset($result[3])) $this->userfields = $result[3];
			$this->fieldsordering = $result[4];
			$juser = Factory::getUser();
			$this->email = $juser->email;
			$this->name = $juser->name;
			$this->form_is_disabled = $allow_ticket_form;
		}elseif($layoutName == 'mytickets'){
			$sort =  Factory::getApplication()->input->get('sortby','');
			if (isset($sort)){
				if ($sort == '') {$sort='defaultdesc';}
			}else {$sort='defaultdesc';}

    		//$searchticketid = Factory::getApplication()->input->get('filter_ticketid');
	        $option = 'com_jssupportticket';

			$searchkeys['filter_ticketsearchkeys'] = $mainframe->getUserStateFromRequest($option . 'filter_ticketsearchkeys','filter_ticketsearchkeys','','string');
			$searchkeys['filter_ticketid'] = $mainframe->getUserStateFromRequest($option . 'filter_ticketid' , 'filter_ticketid' , '' , 'string');
			$searchkeys['filter_from'] = $mainframe->getUserStateFromRequest($option . 'filter_from' , 'filter_from' , '' , 'string');
			$searchkeys['filter_email'] = $mainframe->getUserStateFromRequest($option . 'filter_email' , 'filter_email' , '' , 'string');
			$searchkeys['filter_department'] = $mainframe->getUserStateFromRequest($option . 'filter_department' , 'filter_department' , '' , 'string');
			$searchkeys['filter_priority'] = $mainframe->getUserStateFromRequest($option . 'filter_priority' , 'filter_priority' , '' , 'string');
			$searchkeys['filter_subject'] = $mainframe->getUserStateFromRequest($option . 'filter_subject' , 'filter_subject' , '' , 'string');
			$searchkeys['filter_datestart'] = $mainframe->getUserStateFromRequest($option . 'filter_datestart' , 'filter_datestart' , '' , 'string');
			$searchkeys['filter_dateend'] = $mainframe->getUserStateFromRequest($option . 'filter_dateend' , 'filter_dateend' , '' , 'string');
			$searchkeys['filter_staffmember'] = $mainframe->getUserStateFromRequest($option . 'filter_staffmember' , 'filter_staffmember' , '' , 'string');

			$jsresetbutton = Factory::getApplication()->input->get('jsresetbutton',0);
			if($jsresetbutton == 1){ //if filter is reset, we need to put start,end dates explicitly null, because joomla make some problem for dates
				$mainframe->setUserState($option.'filter_dateend',null);
				$mainframe->setUserState($option.'filter_datestart',null);
				$searchkeys['filter_datestart'] = null;
				$searchkeys['filter_dateend'] = null;
			}

			$uid = $user->getId();
			$listtype = Factory::getApplication()->input->get('lt',1);
			$sortby = $this->getTicketListOrdering($sort);
			$sortlinks = $this->getTicketListSorting($sort);
			$sortlinks['sorton'] = $sorton;
			$sortlinks['sortorder'] = $sortorder;
			$result = $this->getJSModel('ticket')->getUserMyTickets($uid,$listtype,$searchkeys,$sortby,$limitstart,$limit);
			$this->username = $uname;
			$this->result = $result[0];
			$this->ticketinfo = $result[2];
			$this->lists = $result[3];
			$this->lt = $listtype;
			$this->sortlinks = $sortlinks;
			$this->filter_data = $result[4];
			$total = $result[1];
			$pagination = new Pagination($total, $limitstart, $limit );
			$this->pagination = $pagination;
		}elseif($layoutName == 'ticketstatus'){
			if(!$user->getIsGuest()){
				$email = $user->getEmail();
				$this->email = $email;
			}
		}
		require_once(JPATH_COMPONENT."/views/ticket/ticket_breadcrumbs.php");
		parent::display($tpl);
	}
	function getTicketListOrdering( $sort ) {
		global $sorton, $sortorder;
		$defaultsort = $this->getJSModel('ticket')->getDefaultTicketSorting();
		switch ( $sort ) {
			case "subjectdesc": $ordering = "ticket.subject DESC"; $sorton = "subject"; $sortorder="DESC"; break;
			case "subjectasc": $ordering = "ticket.subject ASC";  $sorton = "subject"; $sortorder="ASC"; break;
			case "prioritydesc": $ordering = "priority.priority DESC"; $sorton = "priority"; $sortorder="DESC"; break;
			case "priorityasc": $ordering = "priority.priority ASC";  $sorton = "priority"; $sortorder="ASC"; break;
			case "ticketiddesc": $ordering = "ticket.ticketid DESC";  $sorton = "ticketid"; $sortorder="DESC"; break;
			case "ticketidasc": $ordering = "ticket.ticketid ASC";  $sorton = "ticketid"; $sortorder="ASC"; break;
			case "answereddesc": $ordering = "ticket.isanswered DESC";  $sorton = "answered"; $sortorder="DESC"; break;
			case "answeredasc": $ordering = "ticket.isanswered ASC";  $sorton = "answered"; $sortorder="ASC"; break;
			case "attachmentsdesc": $ordering = "attachments DESC";  $sorton = "attachments"; $sortorder="DESC"; break;
			case "attachmentsasc": $ordering = "attachments ASC";  $sorton = "attachments"; $sortorder="ASC"; break;
			case "createddesc": $ordering = "ticket.created DESC";  $sorton = "created"; $sortorder="DESC"; break;
			case "createdasc": $ordering = "ticket.created ASC";  $sorton = "created"; $sortorder="ASC"; break;
			case "defaultdesc": $ordering = "ticket.status ASC,ticket.isanswered ASC,ticket.priorityid ASC,ticket.created DESC";  $sorton = "created"; $sortorder="DESC"; break;
			case "defaultasc": $ordering = "ticket.status ASC,ticket.created ASC";  $sorton = "created"; $sortorder="ASC"; break;
			case "statusdesc": $ordering = "ticket.status DESC";  $sorton = "status"; $sortorder="DESC"; break;
			case "statusasc": $ordering = "ticket.status ASC";  $sorton = "status"; $sortorder="ASC"; break;
			default: 
				$ordering = "ticket.status ";
				$ordering .= $defaultsort;
		}
		return $ordering;
	}

	function getTicketListSorting( $sort ) {
		$sortlinks['subject'] = $this->getSortArg("subject",$sort);
		$sortlinks['priority'] = $this->getSortArg("priority",$sort);
		$sortlinks['ticketid'] = $this->getSortArg("ticketid",$sort);
		$sortlinks['answered'] = $this->getSortArg("answered",$sort);
		$sortlinks['status'] = $this->getSortArg("status",$sort);
		$sortlinks['created'] = $this->getSortArg("created",$sort);
		$sortlinks['attachments'] = $this->getSortArg("attachments",$sort);
		$sortlinks['created'] = $this->getSortArg("created",$sort);

		return $sortlinks;
	}
	function getSortArg( $type, $sort ) {
		$mat = array();
		$defaultsort = $this->getJSModel('ticket')->getDefaultTicketSorting(1);
		if ( getJSTicketPHPFunctionsClass()->jsticket_preg_match( "/(\w+)(asc|desc)/i", $sort, $mat ) ) {
			if ( $type == $mat[1] ) {
				return ( $mat[2] == "asc" ) ? "{$type}desc" : "{$type}asc";
			} else {
				return $type . $mat[2];
			}
		}
		$sort = "id";
		$sort .= $defaultsort;
		return $sort;
	}
}
?>
