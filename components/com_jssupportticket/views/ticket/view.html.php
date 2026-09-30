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
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;	
use Joomla\CMS\Plugin\PluginHelper;

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class jssupportticketViewTicket extends JSSupportTicketView
{

    private function jsstTicketDetailFeatureDefaults() {
        return array(
            'tags' => array(),
            'available_tags' => array(),
            'watchers' => array(),
            'timeline' => array(),
            'sla' => array(),
            'matched_sla' => null,
        );
    }

    private function jsstTicketResultHasDetail($result) {
        return is_array($result) && isset($result[0]) && is_object($result[0]);
    }

    private function jsstDenyTicketDetail($code = 4) {
        $this->ticketdetail = null;
        $this->perm_not_allowed = (int) $code;
        $this->ticketreplies = array();
        $this->ticketattachment = array();
        $this->ticketnotes = array();
        $this->userfields = array();
        $this->fieldsordering = array();
        $this->tickethistory = array();
        $this->ticketFeatures = $this->jsstTicketDetailFeatureDefaults();
        $this->isAttachmentPublished = 0;
        $this->isAttachmentVisitorPublished = 0;
    }

    private function jsstLoggedUserOwnsTicket($ticket, $user) {
        return is_object($ticket) && isset($ticket->uid) && (int) $ticket->uid === (int) $user->getId();
    }

    private function jsstGuestSessionOwnsTicket($ticket) {
        if (!is_object($ticket) || !isset($ticket->ticketid) || !isset($ticket->email)) {
            return false;
        }
        $session = Factory::getApplication()->getSession();
        $ticketid = (string) $session->get('userticketid', '');
        $email = (string) $session->get('useremail', '');
        return $ticketid !== '' && $email !== '' && $ticketid === (string) $ticket->ticketid && strcasecmp($email, (string) $ticket->email) === 0;
    }

	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
		global $sorton,$sortorder;
        if($layoutName == 'formticket'){
			$email='';
			$allow_ticket_form = 1;
			// making sure ticket is not open for edit in case of user
			$id = Factory::getApplication()->input->get('id');

				$email = $user->getEmail();
				$this->email = $email;
	            if($id == '') { // allow ticket form for new case
	            	$allow_ticket_form = 1;
	            }else{ // hide ticket form in edit case for non staff users
	            	$allow_ticket_form = 0;
	            }
			// fill data for form if form is allowed
			if($allow_ticket_form == 1){
	            $data = $mainframe->getUserState('com_jssupportticket.data');
	            $mainframe->setUserState('com_jssupportticket.data',null);
				$result = $this->getJSModel('ticket')->getFormData($id,$data);
				//echo '<pre>';print_r($result);echo '</pre>';

				PluginHelper::importPlugin('jssupportticket');
				// $dispatcher = JDispatcher::getInstance();
				Factory::getApplication()->triggerEvent( 'changeFormField', array(&$result));

				$this->id = $id;
				$this->lists = $result[2];
				if(isset($result[0])){
					$this->editticket = $result[0];
				}
				// for custom plugin
				if(isset($result['custom_params'])){
					$this->custom_params = $result['custom_params'];
				}

				if(isset($result[3])) $this->userfields = $result[3];
				$this->fieldsordering = $result[4];
				$juser = Factory::getUser();
				$this->email = $juser->email;
				$this->name = $juser->name;
				$this->username = $juser->username;
				$this->uid = (int) $juser->id;
				$this->attachments = $result[5];
				$this->form_is_disabled = 0;
			}else{
				$this->form_is_disabled = 1;
			}
		}elseif($layoutName == 'ticketdetail'){
				$checkstatus = Factory::getApplication()->input->get('checkstatus',null,'post');
				$jsticket = Factory::getApplication()->input->get('jsticket',null,'get');
				$id = 0;
				$emailVerifiedAccess = false;
				if($jsticket != null || $checkstatus == 1){
					if($checkstatus == 1){
						$ticketid = Factory::getApplication()->input->getString('ticketid');
						$email = Factory::getApplication()->input->getString('email');
					}else{
						$jsticket = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($jsticket);
						$array = getJSTicketPHPFunctionsClass()->jsticket_explode(',', $jsticket);
						$ticketid = isset($array[0]) ? $array[0] : '';
						$email = isset($array[1]) ? $array[1] : '';
					}

					$res = $this->getJSModel('ticket')->checkEmailAndTicketID($email,$ticketid);
					if($res == 1){
						$session = Factory::getApplication()->getSession();
						$session->set('userticketid',$ticketid);
						$session->set('useremail',$email);
						$id = (int) $this->getJSModel('ticket')->getIdFromTrackingId($ticketid);
						$emailVerifiedAccess = true;
					}else{
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketstatus&Itemid='.$Itemid;
				        $app = Factory::getApplication();
				        $app->enqueueMessage(Text::_('No records found.'), 'fail');
				        $app->redirect(Route::_($link , false));
					}
				}else{
					$id = Factory::getApplication()->input->getInt('id', 0);
				}

				if($id <= 0){
					$this->jsstDenyTicketDetail(3);
				}else{
					$result = $this->getJSModel('ticket')->getTicketDetailById($id);
					if($this->jsstTicketResultHasDetail($result)){
						$ticketdetail = $result[0];
						if($user->getIsGuest() && !$emailVerifiedAccess && !$this->jsstGuestSessionOwnsTicket($ticketdetail)){
							$this->jsstDenyTicketDetail(3);
						}elseif(!$user->getIsGuest() && !$emailVerifiedAccess && !$this->jsstLoggedUserOwnsTicket($ticketdetail, $user)){
							$this->jsstDenyTicketDetail(4);
						}else{
							$this->ticketdetail = $ticketdetail;
		            		$this->isAttachmentPublished = isset($result['publishedInfo']->published) ? $result['publishedInfo']->published : 0;
		            		$this->isAttachmentVisitorPublished = isset($result['publishedInfo']->isvisitorpublished) ? $result['publishedInfo']->isvisitorpublished : 0;
							if(isset($result[2])) $this->ticketreplies = $result[2];
							if(isset($result[6])) $this->ticketattachment = $result[6];
							if(isset($result[7])) $this->userfields = $result[7];
							if(isset($result[8])) $this->fieldsordering = $result[8];
							if(isset($result[9])) $this->tickethistory = $result[9];
						}
					}else{
						$this->jsstDenyTicketDetail(is_scalar($result) ? (int) $result : 4);
					}
				}
				if(isset($email)) $this->email = $email;
				$this->id = $id;

		}elseif($layoutName == 'mytickets'){
			if(!$user->getIsGuest()){
				$defaultsort = $this->getJSModel('ticket')->getDefaultTicketSorting(1);
				$sort =  Factory::getApplication()->input->get('sortby','');
				if ($sort == ''){
			 		$sort='status';
			 		$sort .= $defaultsort;
				}
        			
        		//$searchkeys = Factory::getApplication()->input->post->getArray();
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

				$email_address = $user->getEmail();
				$email = Factory::getApplication()->input->getString('email',$email_address);
				$session = Factory::getApplication()->getSession();
				$session->set('email',$email);
				$listtype = Factory::getApplication()->input->get('lt',1);
				$sortby = $this->getTicketListOrdering($sort);
				$sortlinks = $this->getTicketListSorting($sort);
				// $sortlinks['sorton'] = $sorton;
				$sortlinks['sorton'] = $sorton;
				$sortlinks['sortorder'] = $sortorder;

				$result = $this->getJSModel('ticket')->getUserMyTickets($email,$listtype,$searchkeys,$sortby,$limitstart,$limit);
				$this->filter_data = $result[4];
				//$this->username = $uname;
				$this->result = $result[0];
				$this->ticketinfo = $result[2];
            	$this->lists = $result[3];
				$this->email = $email;
				$this->lt = $listtype;
				$this->sortlinks = $sortlinks;
				$total = $result[1];
				$pagination = new Pagination($total, $limitstart, $limit );
				$this->pagination = $pagination;
			}
		}elseif($layoutName == 'ticketstatus'){
			if(!$user->getIsGuest()){
				$email = $user->getEmail();
				$this->email = $email;
			}
		}elseif($layoutName == 'visitorsuccessmessage'){
			
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
			case "createddesc": $ordering = "ticket.created DESC";  $sorton = "created"; $sortorder="DESC"; break;
			case "createdasc": $ordering = "ticket.created ASC";  $sorton = "created"; $sortorder="ASC"; break;
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
