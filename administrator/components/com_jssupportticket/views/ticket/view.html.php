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
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewTicket extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        global $sorton,$sortorder;
        if($layoutName == 'tickets'){
            ToolbarHelper::addNew('addnewticket');
            $defaultsort = $this->getJSModel('ticket')->getDefaultTicketSorting(1);
            $sort =  Factory::getApplication()->input->get('sortby','');
            if ($sort == '') {
                $sort='status';
                $sort .= $defaultsort;
            }

            $sortby = $this->getTicketListOrdering($sort);
            $sortlinks = $this->getTicketListSorting($sort);
            $sortlinks['sorton'] = $sorton;
            $sortlinks['sortorder'] = $sortorder;
            $mainframe->setUserState( $option.'.limitstart', $limitstart );
            $searchsubject = Factory::getApplication()->input->getString('filter_subject');
            $searchfrom = Factory::getApplication()->input->get('filter_from');
            $searchfromemail = Factory::getApplication()->input->getString('filter_fromemail');
            $datestart = Factory::getApplication()->input->getDate('filter_datestart');
            $dateend = Factory::getApplication()->input->getDate('filter_dateend');
            $searchticketid = Factory::getApplication()->input->get('filter_ticketid');
            $searchstaffmember = Factory::getApplication()->input->get('filter_staffmember');
            $searchdepartmentid = Factory::getApplication()->input->get('filter_department');
            $searchpriorityid = Factory::getApplication()->input->get('filter_priority');
            $jsresetbutton = Factory::getApplication()->input->get('jsresetbutton',0);
            if($jsresetbutton == 1){
                $datestart = null;
                $dateend = null;
            }

            $listtype = Factory::getApplication()->input->get('lt',1);
            if($listtype == 1) $text = Text::_('Open');
            if($listtype == 2) $text = Text::_('Answered');
            if($listtype == 3) $text = Text::_('Overdue');
            if($listtype == 4) $text = Text::_('Close');
            if($listtype == 5) $text = Text::_('My Tickets');
            ToolbarHelper::title(Text::_('Tickets') . ' <small><small>[ ' . $text . ' ]</small></small>');
            $result = $this->getJSModel('ticket')->getAdminMyTickets($searchdepartmentid, $searchpriorityid, $searchstaffmember,$searchsubject,$searchfrom,$searchfromemail,$searchticketid,$listtype,$sortby, $datestart, $dateend, $limitstart,$limit);
            $total = $result[1];
            $this->result = $result[0];
            $this->lists = $result[2];
            $this->ticketinfo = $result[3];
            $this->listtype = $listtype;
            $this->sortlinks = $sortlinks;
            $this->sorton = $sorton;
            $this->sortorder = $sortorder;
            $pagination = new Pagination($total, $limitstart, $limit);
            $this->pagination = $pagination;
            try {
                $this->savedViews = $this->getJSModel('featurefoundation')->getActiveSavedViewsForSelect();
            } catch (Throwable $e) {
                $this->savedViews = array();
            }
        }elseif($layoutName == 'ticketdetails'){
            ToolbarHelper::title(Text::_('Ticket'));
            $ticketid = Factory::getApplication()->input->get('cid', array (0), '', 'array');
            $ticketid = $ticketid[0];
            $result = $this->getJSModel('ticket')->getTicketDetailById($ticketid);
            $user = JSSupportticketCurrentUser::getInstance();
            $isstaff = 0;
            $this->ticketdetail = $result[0];
            $this->isAttachmentPublished = $result['publishedInfo']->published;
            if(isset($result[1])) $this->ticketnotes = $result[1];
            if(isset($result[2])) $this->ticketreplies = $result[2];
            $this->lists = $result[3];
            if(isset($result[4])) $this->isemailban = $result[4];
            if(isset($result[6])) $this->ticketattachment = $result[6];
            if(isset($result[7])) $this->userfields = $result[7];
            if(isset($result[8])) $this->fieldsordering = $result[8];
            $this->isstaff = $isstaff;
            if(isset($result[9])) $this->tickethistory = $result[9];
            // Other tickets by the same user, shown in the right-hand sidebar.
            // The model already builds this (result[13]) whenever the ticket
            // belongs to a registered user; only this assignment was missing, so
            // the panel's isset() guard was never satisfied and it never rendered.
            if(isset($result[13])) $this->usertickets = $result[13];
        }elseif($layoutName == 'formticket'){
			ToolbarHelper::save('saveticketsave','Submit Ticket');
			ToolbarHelper::save2new('saveticketandnew');
			ToolbarHelper::save('saveticket');
			$c_id = Factory::getApplication()->input->get('cid', array (0), 'array');
			$c_id = $c_id[0];
			$id = Factory::getApplication()->input->get('id');
			if(isset($id))
				$c_id = $id;
            $data = $mainframe->getUserState('com_jssupportticket.data');
            $mainframe->setUserState('com_jssupportticket.data',null);
			$result = $this->getJSModel('ticket')->getFormData($c_id,$data);
			$isNew = true;
			if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
			$text = $isNew ? Text::_('Add') : Text::_('Edit');
			ToolbarHelper::title(Text::_('Ticket') . ': <small><small>[ ' . $text . ' ]</small></small>');
			if ($isNew) ToolbarHelper::cancel('cancelticket');	else ToolbarHelper::cancel('cancelticket', 'Close');

			$this->lists = $result[2];
			if(isset($result[0]))
			$this->editticket = $result[0];
            $this->data = $data;
            if(isset($result[3])) $this->userfields = $result[3];
			if(isset($result[5])) $this->attachments = $result[5];
			$this->fieldsordering = $result[4];
		}
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
