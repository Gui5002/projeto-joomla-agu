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

class JSSupportticketViewEmail extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        ToolbarHelper::title(Text::_('Emails'));
        if($layoutName == 'emails'){
            ToolbarHelper::addNew('addnewemail');
            ToolbarHelper::editList('addnewemail');
            ToolbarHelper::deleteList(Text::_('Are you sure to delete'),'deleteemail');
            $mainframe->setUserState( $option.'.limitstart', $limitstart );
            $searchemail = Factory::getApplication()->input->getString('filter_email');
            $searchtype = $mainframe->getUserStateFromRequest( $option.'filter_autoresponcetype', 'filter_autoresponcetype',	'',	'string' );
            $result = $this->getJSModel('email')->getAllEmails($searchemail, $searchtype,$limitstart,$limit);
            $total = $result[1];
            $this->emails = $result[0];
            $this->lists = $result[2];
            $pagination = new Pagination($total, $limitstart, $limit);
	        $this->pagination = $pagination;
        }elseif($layoutName == 'formemail'){
			ToolbarHelper::save('saveemailsave','Save Email');
			ToolbarHelper::save2new('saveemailandnew');
			ToolbarHelper::save('saveemail');

			$c_id = Factory::getApplication()->input->get('cid', array (0), '', 'array');
			$c_id = $c_id[0];
			$result = $this->getJSModel('email')->getFormData($c_id);
			$isNew = true;
			if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
			$text = $isNew ? Text::_('Add') : Text::_('Edit');
			ToolbarHelper::title(Text::_('Email') . ': <small><small>[ ' . $text . ' ]</small></small>');
			if ($isNew)	ToolbarHelper::cancel('cancelemail'); else ToolbarHelper::cancel('cancelemail', 'Close');

			$this->lists = $result[2];
			$this->email = $result[0];
		}

		
		parent::display($tpl);
	}
}
?>
