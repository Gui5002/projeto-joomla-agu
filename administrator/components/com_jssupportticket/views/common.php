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

$option = 'com_jssupportticket';
$mainframe = Factory::getApplication();
$jinput = Factory::getApplication()->input;
$itemid = $jinput->get('Itemid');
$layoutName = $this->getLayout();

$config =  $this->getJSModel('config')->getConfigByFor('default');

$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
//$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
$limitstart = $jinput->get('limitstart',0);

$version = $this->getJSModel('config')->getConfigurationByName('version');
$this->version = $version;
HTMLHelper::_('jquery.framework');
//HTMLHelper::behavior('modal');

$this->option = $option;
$this->Itemid = $itemid;
$this->config = $config;

?>
