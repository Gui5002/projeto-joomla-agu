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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewReports extends JSSupportTicketView
{
	function display($tpl = null){

        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        if($layoutName == 'reports'){
            ToolbarHelper::title(Text::_('Reports'));
        }elseif($layoutName == 'overallreport'){
            ToolbarHelper::title(Text::_('Overall Report'));
            $result = $this->getJSModel('reports')->getOverallReportData();
            $this->result=$result;
        }

        parent::display($tpl);
    }
}
?>
