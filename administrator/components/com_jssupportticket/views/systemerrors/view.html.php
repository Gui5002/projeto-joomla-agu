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

class JSSupportticketViewSystemErrors extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		ToolbarHelper::title(Text::_('System Errors'));
		if($layoutName == 'systemerrors'){
			$result = $this->getJSModel('systemerrors')->getSystemErrors($limitstart, $limit);
			$total = $result[1];
			if ( $total <= $limitstart ) $limitstart = 0;
			$pagination = new Pagination( $total, $limitstart, $limit );
			$this->systemerrors = $result[0];
			$this->pagination = $pagination;
		}elseif($layoutName == 'error'){
			$errorid = Factory::getApplication()->input->get('cid');
			$result = $this->getJSModel('systemerrors')->getErrorDetail($errorid);
			$this->error = $result;
		}
		parent::display($tpl);
	}
}
?>
