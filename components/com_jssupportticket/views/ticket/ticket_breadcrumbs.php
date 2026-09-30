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
use Joomla\CMS\Language\Text;

	$commonpath="index.php?option=com_jssupportticket";
	$pathway = $mainframe->getPathway();
	if ($config['cur_location'] == 1) {
		$pathway->addItem(Text::_('Control Panel'), $commonpath.'&c=jssupportticket&layout=controlpanel');
		switch($layoutName){
			case 'formticket':
				if($id){ //edit
					$pathway->addItem(Text::_('Edit Ticket'), '');
				}else{ //new
					$pathway->addItem(Text::_('Create Ticket'), '');
				}
				break;
			case 'mytickets':
				$pathway->addItem(Text::_('My Tickets'), $commonpath."&c=ticket&layout=mytickets&Itemid=".$Itemid);
				break;
			case 'ticketdetail':
				$pathway->addItem(Text::_('My Tickets'), $commonpath."&c=ticket&layout=mytickets&Itemid=".$Itemid);
				$pathway->addItem(Text::_('Ticket Detail'), '');
			break;
		}
	}
?>
