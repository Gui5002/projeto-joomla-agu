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

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;


// Options button.
if (Factory::getUser()->authorise('core.admin', 'com_jssupportticket')) {
    ToolbarHelper::preferences('com_jssupportticket');
}

class JSSupportticketViewConfig extends JSSupportTicketView
{
	function display($tpl = null)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		ToolbarHelper::title(Text::_('Configurations'));
		if($layoutName == 'config'){
            ToolbarHelper::save('saveconf','Save Configurations');
            ToolbarHelper::cancel('cancelconfig');

            $result = $this->getJSModel('config')->getConfiguration();

            $this->configuration = $result[0];
            $this->lists = $result[1];
        }

		parent::display($tpl);
	}
}
?>
