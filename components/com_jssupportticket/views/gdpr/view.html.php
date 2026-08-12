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

class JSSupportTicketViewGdpr extends JSSupportticketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
		if($layoutName == 'adderasedatarequest'){

			$uid = $user->getId();
			$result = $this->getJSModel('gdpr')->getUserEraseDataRequest($uid);
			$this->erasedaatrequest = $result;
		}
		require_once(JPATH_COMPONENT."/views/gdpr/gdpr_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
