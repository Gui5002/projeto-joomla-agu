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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewJSSupportticket extends JSSupportTicketView {

    function display($tpl = null) {
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        ToolbarHelper::title(Text::_('Tickets'));
        if ($layoutName == 'controlpanel') {
            ToolbarHelper::title(Text::_('Control Panel'));
            $result = $this->getJSModel('jssupportticket')->getControlPanelData();
            $latestdepartments = $this->getJSModel('department')->getLatestDepartmentsForAdminCP();
            $version = $this->getJSModel('config')->getConfigurationByName('version');
            $this->result = $result;
            $this->latestdepartments = $latestdepartments;
        } elseif ($layoutName == 'aboutus') {
            ToolbarHelper::title(Text::_('About Us'));
        } elseif ($layoutName == 'themes') {
            ToolbarHelper::title(Text::_('Themes'));
            $result = $this->getJSModel('jssupportticket')->getCurrentTheme();
            $this->result = $result;
        
	} elseif ($layoutName == 'translation') {
            ToolbarHelper::title(Text::_('Language Translations'));
        } elseif ($layoutName == 'proversion') {
            ToolbarHelper::title(Text::_('Pro Version'));
        }
        parent::display($tpl);
    }

}

?>
