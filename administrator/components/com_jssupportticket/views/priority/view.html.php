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

class JSSupportticketViewPriority extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        ToolbarHelper::title(Text::_('Priorities'));
        if($layoutName == 'priorities'){
            ToolbarHelper::makeDefault('makeprioritydefault');
            ToolbarHelper::addNew('addnewpriority');
            ToolbarHelper::editList('addnewpriority');
            ToolbarHelper::deleteList(Text::_('Are you sure to delete'),'deletepriority');
            $searchpriority = $mainframe->getUserStateFromRequest( $option.'filter_priority', 'filter_priority', '', 'string' );
            $result = $this->getJSModel('priority')->getAllPriorities($searchpriority,$limitstart,$limit);
            $this->priority = $result[0];
            $this->searchpriority = $result[2];
            $total = $result[1];            
            $pagination = new Pagination($total, $limitstart, $limit);
            $this->pagination = $pagination;
        }elseif($layoutName == 'formpriority'){
            ToolbarHelper::save('saveprioritysave','Save Priority');
            ToolbarHelper::save2new('savepriorityandnew');
            ToolbarHelper::save('savepriority');

            $c_id = Factory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id = $c_id[0];
            $result = $this->getJSModel('priority')->getFormData($c_id);
            $isNew = true;
            if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
            if ($isNew) ToolbarHelper::cancel('cancelpriority'); else ToolbarHelper::cancel('cancelpriority', 'Close');
            $text = $isNew ? Text::_('Add') : Text::_('Edit');
            ToolbarHelper::title(Text::_('Priority') . ': <small><small>[ ' . $text . ' ]</small></small>');
            if(isset($result[0])) $this->priority = $result[0];
            
        }
        parent::display($tpl);
	}
}
?>
