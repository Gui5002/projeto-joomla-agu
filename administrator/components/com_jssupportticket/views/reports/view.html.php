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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;


class JSSupportTicketViewReports extends JSSupportticketView
{
	function display($tpl = null)
	{
		require_once(JPATH_COMPONENT."/views/common.php");                

		ToolbarHelper::title(Text::_('Reports'));
		if($layoutName == 'overallreports'){
            $result = $this->getJSModel('reports')->getOverallReportsData();
            $this->result=$result;
        }

		parent::display($tpl);
	}
}
?>
