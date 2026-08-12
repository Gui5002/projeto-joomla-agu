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

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;

class JSSupportticketViewDepartment extends JSSupportTicketView
{
	function display($tpl = null)
	{
          require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		ToolbarHelper::title(Text::_('Departments'));
		if($layoutName == 'formdepartment'){
               ToolbarHelper::save('savedepartmentsave','Save Department');
               ToolbarHelper::save2new('savedepartmentandnew');
               ToolbarHelper::save('savedepartment');

               $c_id = Factory::getApplication()->input->get('cid', array (0), '', 'array');
               $c_id = $c_id[0];
               $result = $this->getJSModel('department')->getDepartmentForForm($c_id);
               $isNew = true;
               if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
               $text = $isNew ? Text::_('Add') : Text::_('Edit');
               ToolbarHelper::title(Text::_('Department') . ': <small><small>[ ' . $text . ' ]</small></small>');
               if ($isNew)	ToolbarHelper::cancel('canceldepartment'); else ToolbarHelper::cancel('canceldepartment', 'Close');

               $this->lists = $result[1];
               if(isset($result[0])) $this->department = $result[0];
		}elseif($layoutName == 'departments'){
               ToolbarHelper::addNew('addnewdepartment');
               ToolbarHelper::editList('addnewdepartment');
               ToolbarHelper::deleteList(Text::_('Are you sure to delete'),'deletedepartment');
               $mainframe->setUserState( $option.'.limitstart', $limitstart );
               $searchdepartment = Factory::getApplication()->input->getString('filter_departmentname');
               $searchtype = $mainframe->getUserStateFromRequest( $option.'filter_type', 'filter_type',	'',	'string' );
               
               $result = $this->getJSModel('department')->getAllDepartments($searchdepartment, $searchtype,$limitstart,$limit);
               $total = $result[1];
               $this->department = $result[0];
               $this->lists = $result[2];
               $pagination = new Pagination($total, $limitstart, $limit);
               $this->pagination = $pagination;
          }

		
		parent::display($tpl);
	}
}
?>
