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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_jssupportticket')) {
	throw new Exception(Text::_('Authorise error'),404);
}


$version = new Version;
$joomla = $version->getShortVersion();
$jversion = getJSTicketPHPFunctionsClass()->jsticket_substr($joomla, 0, 3);

if (!defined('JVERSION')) {
    define('JVERSION', $jversion);
}

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/bootstrap.min.css');
$language = Factory::getLanguage();
if($language->isRTL()){
	$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadminrtl.css');
}
if (JVERSION < 3) {
    HTMLHelper::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} elseif (JVERSION < 4)  {
    HTMLHelper::_('bootstrap.framework');
    HTMLHelper::_('jquery.framework');
} elseif (JVERSION > 5)  {
	HTMLHelper::_('jquery.framework');
	HTMLHelper::_('bootstrap.framework');
}
$document->addScript('components/com_jssupportticket/include/js/tree.js');

require_once(JPATH_COMPONENT.'/JSApplication.php');
$base = JPATH_BASE;
$base = getJSTicketPHPFunctionsClass()->jsticket_substr($base, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($base) - 14); //remove administrator
require_once($base.'/components/com_jssupportticket/views/messageslayout.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/currentuser.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/constants.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/messages.php');

function getCustomFieldClass() {
    include_once JPATH_COMPONENT_ADMINISTRATOR.'/include/classes/customfields.php';
    $obj = new customfields();
    return $obj;
}

function getJSTicketPHPFunctionsClass() {
	include_once JPATH_COMPONENT_ADMINISTRATOR.'/include/classes/jsticketphpfunctions.php';
	$obj = new jsticketphpfunctions();
	return $obj;
}

$jinput = Factory::getApplication()->input;
$task = $jinput->getCmd('task');
$c = '';
if (getJSTicketPHPFunctionsClass()->jsticket_strstr($task, '.')) {
	$array = getJSTicketPHPFunctionsClass()->jsticket_explode('.', $task);
	$c = $array[0];
	$task = $array[1];
} else {
	$c = $jinput->getCmd('c', 'jssupportticket');
	$task = $jinput->getCmd('task', 'display');
}
if ($c != '') {
	$path = JPATH_COMPONENT . '/controllers/' . $c . '.php';
	jimport('joomla.filesystem.file');
	if (file_exists($path)) {
		require_once ($path);
	} else {
		throw new Exception(Text::_('Unknown Controller: <br>' . $c . ':' . $path),500);		
	}
}
$c = 'JSSupportticketController'.$c;
$controller = new $c ();
$controller->execute($task);

$controller->redirect();

?>
