<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	Feb 24, 2020
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

class JSSupportticketViewGdpr extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        global $sorton,$sortorder;
        if($layoutName == 'erasedatarequests'){
            ToolbarHelper::title(Text::_('User Erase Data Request'));
            $searchemail = $mainframe->getUserStateFromRequest($option . 'filter_email', 'filter_email','','string');
            $jsresetbutton = Factory::getApplication()->input->get('jsresetbutton');
            if($jsresetbutton == 1){
                $limitstart = 0;
            }
            $result = $this->getJSModel('gdpr')->getEraseDataRequests($searchemail,$limitstart,$limit);
            $this->result = $result[0];
            $this->searchemail = $searchemail;
            $total = $result[1];
            $pagination = new Pagination($total, $limitstart, $limit);
        }
        $this->pagination = $pagination;
        parent::display($tpl);
	}
}
?>
