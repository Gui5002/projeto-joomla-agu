<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	March 04, 2014
 ^
 + Project: 	JS Tickets
 ^ 
*/
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;


class JSSupportticketViewPostInstallation extends JSSupportTicketView
{
	function display($tpl = null)	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		ToolbarHelper::title(Text::_('Installation Complete'));
		if($layoutName == 'stepone'){
			ToolbarHelper::title(Text::_('General Configurations') );
			$result = $this->getJSModel('postinstallation')->getConfigurationValues();
			$this->result = $result;
		}elseif($layoutName == 'steptwo'){
			ToolbarHelper::title(Text::_('Ticket Configurations') );
			$result = $this->getJSModel('postinstallation')->getConfigurationValues();
			$this->result = $result;
		}elseif($layoutName == 'stepthree'){
			ToolbarHelper::title(Text::_('Feedback Configurations') );
			$result = $this->getJSModel('postinstallation')->getConfigurationValues();
			$this->result = $result;
		}
		parent::display($tpl);
	}
	
}
