<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelReports extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getOverallReportData(){
        $db = Factory::getDbo();
        $result = array();

        //Overall Data by status
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status != 4 AND isanswered = 0 AND status != 5";
        $db->setQuery($query);
        $openticket = $db->loadResult();
        $result['openticket'] = $openticket;
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4";
        $db->setQuery($query);
        $closeticket = $db->loadResult();
        $result['closeticket'] = $closeticket;

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 3 AND isanswered = 1";
        $db->setQuery($query);
        $answeredticket = $db->loadResult();
        $result['answeredticket'] = $answeredticket;

        $overdueticket = 0;
        $result['overdueticket'] = $overdueticket;

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets`";
        $db->setQuery($query);
        $alltickets = $db->loadResult();
        $result['alltickets'] = $alltickets;

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4 AND (lastreply IS NOT NULL AND lastreply >= '1971-01-01')";
        $db->setQuery($query);
        $pendingticket = $db->loadResult();

        $result['status_chart'] = "['".Text::_('New')."',$openticket],['".Text::_('Answered')."',$answeredticket],['".Text::_('Overdue')."',$overdueticket],['".Text::_('Pending')."',$pendingticket]";
        $total = $openticket + $closeticket + $answeredticket + $overdueticket + $pendingticket;
        $result['bar_chart'] = "
        ['".Text::_('New')."',$openticket,'#FF9900'],
        ['".Text::_('Answered')."',$answeredticket,'#179650'],
        ['".Text::_('Closed')."',$closeticket,'#5F3BBB'],
        ['".Text::_('Pending')."',$pendingticket,'#D98E11'],
        ['".Text::_('Overdue')."',$overdueticket,'#DB624C']        
        ";
        $query = "SELECT dept.departmentname,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE departmentid = dept.id) AS totalticket
                    FROM `#__js_ticket_departments` AS dept";
        $db->setQuery($query);
        $department = $db->loadObjectList();
        $result['pie3d_chart1'] = "";
        foreach($department AS $dept){
            $result['pie3d_chart1'] .= "['$dept->departmentname',$dept->totalticket],";
        }
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $department = $db->loadObjectList();
        $result['pie3d_chart2'] = "";
        foreach($department AS $dept){
            $result['pie3d_chart2'] .= "['".Text::_($dept->priority)."',$dept->totalticket],";
        }
        $ticketviaemail = 0;
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets`";
        $db->setQuery($query);
        $directticket = $db->loadResult();
        $replyviaemail = 0;
        $query = "SELECT COUNT(id) FROM `#__js_ticket_replies`";
        $db->setQuery($query);
        $directreply = $db->loadResult();

        $result['stack_data'] = "['".Text::_('Tickets')."',$directticket,$ticketviaemail,''],['".Text::_('Replies')."',$directreply,$replyviaemail,'']";
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND status != 4 AND isanswered = 0 AND status != 5 ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $openticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 4 AND status != 0 ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $answeredticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND status != 4 ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $overdueticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isanswered != 1 AND status != 4 AND (lastreply IS NOT NULL AND lastreply >= '1971-01-01') ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $pendingticket_pr = $db->loadObjectList();
        $result['priority_status_total'] = 0;
        $result['stack_chart_horizontal']['title'] = "['".Text::_('Tickets')."',";
        $result['stack_chart_horizontal']['data'] = "['".Text::_('Overdue')."',";
        foreach($overdueticket_pr AS $pr){
            $ticketTotal = 0;
            $result['priority_status_total'] += $ticketTotal;
            $result['stack_chart_horizontal']['title'] .= "'".Text::_($pr->priority)."',";
            $result['stack_chart_horizontal']['data'] .= $ticketTotal.",";
        }
        $result['stack_chart_horizontal']['title'] .= "]";
        $result['stack_chart_horizontal']['data'] .= "],['".Text::_('Pending')."',";

        foreach($pendingticket_pr AS $pr){
            $ticketTotal = (int) $pr->totalticket;
            $result['priority_status_total'] += $ticketTotal;
            $result['stack_chart_horizontal']['data'] .= $ticketTotal.",";
        }

        $result['stack_chart_horizontal']['data'] .= "],['".Text::_('Answered')."',";

        foreach($answeredticket_pr AS $pr){
            $ticketTotal = (int) $pr->totalticket;
            $result['priority_status_total'] += $ticketTotal;
            $result['stack_chart_horizontal']['data'] .= $ticketTotal.",";
        }

        $result['stack_chart_horizontal']['data'] .= "],['".Text::_('New')."',";

        foreach($openticket_pr AS $pr){
            $ticketTotal = (int) $pr->totalticket;
            $result['priority_status_total'] += $ticketTotal;
            $result['stack_chart_horizontal']['data'] .= $ticketTotal.",";
        }
        
        $result['stack_chart_horizontal']['data'] .= "]";
        $result['has_priority_status_data'] = ($result['priority_status_total'] > 0) ? 1 : 0;

        $result['has_staff_ticket_data'] =  0;

        //To show priority colors on chart
        $jsonColorList = "[";
        $query = "SELECT prioritycolour FROM `#__js_ticket_priorities` ORDER BY priority ";
        $db->setQuery($query);
        foreach($db->loadObjectList() as $priority){
            $jsonColorList.= "'".$priority->prioritycolour."',";
        }
        $jsonColorList .= "]";
        $result['priorityColorList'] = $jsonColorList;
        //end priority colors

        return $result;
    }

}
