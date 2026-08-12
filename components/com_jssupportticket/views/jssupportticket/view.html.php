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

class JSSupportTicketViewJsSupportticket extends JSSupportticketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
		if($layoutName == 'controlpanel'){
			$latest_tickets = $this->getJSModel('ticket')->getUserMyTicketsForCP();
			$userticketstats = $this->getJSModel('jssupportticket')->getUserTicketStatsForCP();	

	        $this->userticketstats=$userticketstats;
            $this->latest_tickets=$latest_tickets;
		}
		require_once(JPATH_COMPONENT."/views/jssupportticket/jssupportticket_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
