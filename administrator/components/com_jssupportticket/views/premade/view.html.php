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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewPremade extends JSSupportTicketView
{
	function display($tpl = null)
	{
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        ToolbarHelper::title(Text::_('Tickets'));
        if($layoutName == 'formpremade'){
            $cids = Factory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id= $cids[0];
            $result=$this->getJSModel('premade')->getPremadeForForm($c_id);
            $this->premadeid = $c_id;
            if(isset($result[0])) $this->premade = $result[0];
            $this->lists = $result[1];
            $isNew = true;
            if ( isset($result[0]->id) ) $isNew = false;
            $text = $isNew ? Text::_('Add') : Text::_('Edit');
            ToolbarHelper::title(Text::_('Premade Messages').'<small><small> ['.$text.']</small></small>' );
            ToolbarHelper::save('savepremadesave','Save Premade Message');
            ToolbarHelper::save2new('savepremadeandnew');
            ToolbarHelper::save('savepremade');
            if ($isNew)	ToolbarHelper::cancel('cancelpremade'); else ToolbarHelper::cancel('cancelpremade', 'Close');
            HTMLHelper::_('behavior.formvalidator');
        }elseif($layoutName == 'departmentspremade'){
            ToolbarHelper::title(Text::_('Premade Messages') );
            $title = Factory::getApplication()->input->getString('filter_dp_title');
            $departmentid = $mainframe->getUserStateFromRequest($option.'filter_dp_departmentid', 'filter_dp_departmentid', '', 'int');
            $statusid = $mainframe->getUserStateFromRequest($option.'filter_dp_statusid', 'filter_dp_statusid', '', 'int');
            $result = $this->getJSModel('premade')->getAllDepartmentsPremade($title,$departmentid, $statusid ,$limitstart, $limit);
            $total = $result[1];
            if ( $total <= $limitstart ) $limitstart = 0;
            $pagination = new Pagination( $total, $limitstart, $limit );
            ToolbarHelper::addNew('editpremade');
            ToolbarHelper::editList('editpremade');
            ToolbarHelper::deleteList('Are you sure to delete','removedepartmentpremade');
            $this->premade = $result[0];
            $this->lists = $result[2];
            $this->pagination = $pagination;
        }
        parent::display($tpl);
	}
}
?>
