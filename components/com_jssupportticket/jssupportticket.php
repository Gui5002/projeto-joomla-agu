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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Filesystem\File;

require_once(JPATH_COMPONENT.'/JSApplication.php');
require_once(JPATH_COMPONENT.'/views/messageslayout.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/currentuser.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/constants.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/messages.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/models/cronjob.php');
$language = Factory::getLanguage();
$language->load('com_jssupportticket', JPATH_ADMINISTRATOR, null, true);
$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/bootstrap.min.css');
$document->addStyleSheet('components/com_jssupportticket/include/css/jssupportticketdefault.css');
if($language->isRTL()){
	$document->addStyleSheet('components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
}

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

require_once('include/css/color.php');
if (JVERSION < 3) {
    HTMLHelper::_('behavior.mootools');
    $document->addScript('administrator/components/com_jssupportticket/include/js/jquery.js');
}else{
    HTMLHelper::_('bootstrap.framework');
    HTMLHelper::_('jquery.framework');
}

$document->addScript('components/com_jssupportticket/include/js/my_js.js');

if ($c = Factory::getApplication()->input->getCmd('c', 'jssupportticket'))
{
	$path = JPATH_COMPONENT.'/controllers/'.$c.'.php';
	jimport('joomla.filesystem.file');

	if (file_exists($path))
	{
		require_once ($path);
	}
	else
	{
		throw new Exception(Text::_('Unknown Controller: <br>' . $c . ':' . $path),500);
	}
}

$c = 'JSSupportTicketController'.$c;
$controller = new $c ();
$controller->execute(Factory::getApplication()->input->getCmd('task', 'display'));
$controller->redirect();
?>
