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

class JSSupportticketViewHelpTopic extends JSSupportTicketView
{
	function display($tpl = null)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");		
		ToolbarHelper::title(Text::_('Tickets'));
		if($layoutName == 'formhelptopic'){
			$cids = Factory::getApplication()->input->get('cid', array (0), '', 'array');
			$c_id= $cids[0];
			$result=$this->getJSModel('helptopic')->gethelpTopicForForm($c_id);
			$this->helptopicid = $c_id;
			if(isset($result[0])) $this->helptopic = $result[0];
			$this->lists = $result[1];
			$isNew = true;
			if ( isset($result[0]->id) ) $isNew = false;
			$text = $isNew ? Text::_('Add') : Text::_('Edit');
			ToolbarHelper::title(Text::_('Help Topic').'<small><small> ['.$text.']</small></small>' );
			ToolbarHelper::save('savehelptopicsave','Save Help Topic');
			ToolbarHelper::save2new('savehelptopicandnew');
			ToolbarHelper::save('savehelptopic');
			if ($isNew)	ToolbarHelper::cancel('cancelhelptopic'); else ToolbarHelper::cancel('cancelhelptopic', 'Close');
			HTMLHelper::_('behavior.formvalidator');
		}elseif($layoutName == 'helptopices'){                          //helptopics
			ToolbarHelper::title(Text::_('Help Topics') );
			$helptopic = Factory::getApplication()->input->getString('filter_ht_helptopic');
			$statusid = $mainframe->getUserStateFromRequest($option.'filter_ht_statusid', 'filter_ht_statusid', '', 'string');
			$result = $this->getJSModel('helptopic')->getAllHelpTopices($helptopic,  $statusid ,$limitstart, $limit);
			$total = $result[1];
			if ( $total <= $limitstart ) $limitstart = 0;
			$pagination = new Pagination( $total, $limitstart, $limit );
			ToolbarHelper::addNew('edithelptopic');
			ToolbarHelper::editList('edithelptopic');
			//ToolbarHelper::deleteList('JS_ARE_YOU_SURE_DELETE_HELPTOPIC','removehelptopic');
			ToolbarHelper::deleteList(Text::_('Are you sure to delete'),'removehelptopic');
			$this->helptopic = $result[0];
			$this->lists = $result[2];
			$this->pagination = $pagination;
		}
		
		parent::display($tpl);
	}
}
?>
