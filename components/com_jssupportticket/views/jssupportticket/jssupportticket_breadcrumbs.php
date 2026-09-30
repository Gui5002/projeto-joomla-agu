<?php 
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
   + Project:    JS Tickets
  ^
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

	$commonpath="index.php?option=com_jssupportticket";
	$pathway = $mainframe->getPathway();

	if ($config['cur_location'] == 1) {
		switch($layoutName){
			case 'controlpanel':
				$pathway->addItem(Text::_('Control Panel'), $commonpath.'&c=jssupportticket&layout=controlpanel');
			break;
		}
	}	

?>

