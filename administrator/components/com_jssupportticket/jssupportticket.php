<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

if (!Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_jssupportticket')) {
    throw new Exception(Text::_('Authorise error'), 404);
}

$version = new Version();
$joomla = $version->getShortVersion();
$jversion = getJSTicketPHPFunctionsClass()->jsticket_substr($joomla, 0, 3);

if (!defined('JVERSION')) {
    define('JVERSION', $jversion);
}

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/bootstrap.min.css');
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsst-admin-tokens.css?v=1');
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsst-modern-admin.css?v=106');
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsst-feature-admin.css?v=63');

$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsst-modern-admin-menu.css?v=89');
// Free edition: "*" markers on Pro menu entries and the Pro features page.
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsst-pro-features.css?v=5');

if (JVERSION < 3) {
    HTMLHelper::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} elseif (JVERSION < 4) {
    HTMLHelper::_('bootstrap.framework');
    HTMLHelper::_('jquery.framework');
}

$document->addScript('components/com_jssupportticket/include/js/tree.js');

require_once JPATH_COMPONENT . '/JSApplication.php';

$jsstErrorLogger = JPATH_COMPONENT_ADMINISTRATOR . '/include/classes/systemerrorlogger.php';
if (is_file($jsstErrorLogger)) {
    require_once $jsstErrorLogger;
    JSSupportTicketSystemErrorLogger::register();
}

// Admin runs from /administrator/components/..., so load the message layout
// from the admin component first. Older builds tried to require the frontend
// component path, which breaks on development installs where only the admin
// package is being tested.
$jsstMessagesLayout = JPATH_COMPONENT_ADMINISTRATOR . '/views/messagesLayout.php';
if (!is_file($jsstMessagesLayout)) {
    $jsstMessagesLayout = JPATH_SITE . '/components/com_jssupportticket/views/messageslayout.php';
}
if (is_file($jsstMessagesLayout)) {
    require_once $jsstMessagesLayout;
}

require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/currentuser.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/constants.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/messages.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/cronjob.php';

/**
 * Appends the Pro marker to a label. Free edition only: settings, menu entries
 * and dashboard tiles that belong to Pro features keep their place in the UI but
 * are flagged with a "*" and lead to the Pro features page.
 */
function jsstProCfg($label) {
    return Text::_($label) . '<span class="jsst-pro-star" aria-hidden="true">*</span>';
}

function getCustomFieldClass() {
    include_once JPATH_COMPONENT_ADMINISTRATOR . '/include/classes/customfields.php';
    return new customfields();
}

function getJSTicketPHPFunctionsClass() {
    include_once JPATH_COMPONENT_ADMINISTRATOR . '/include/classes/jsticketphpfunctions.php';
    return new jsticketphpfunctions();
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


if ($c !== '') {
    $path = JPATH_COMPONENT . '/controllers/' . $c . '.php';
    if (is_file($path)) {
        require_once $path;
    } else {
        throw new Exception(Text::_('Unknown Controller: <br>' . $c . ':' . $path), 500);
    }
}

$class = 'JSSupportticketController' . $c;
$controller = new $class();

ob_start();
$controller->execute($task);
$content = ob_get_clean();

// Queue shared admin behaviour after the view has registered Joomla's jQuery framework.
$document->addScript(Uri::root() . 'administrator/components/com_jssupportticket/include/js/jsst-admin-attachments.js?v=2');
$document->addScript(Uri::root() . 'administrator/components/com_jssupportticket/include/js/jsst-admin-ui-fixes.js?v=3');

// Keep the final QA/responsive layer last so page-specific styles cannot override it.
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsst-admin-qa-fixes.css?v=29');

// Postinstallation wizard only. Every selector inside is scoped to
// #jsst-main-wrapper.post-installation, which those four templates alone emit,
// so loading it globally cannot reach any other screen.
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsst-postinstallation-v1.css?v=14');

if (trim($content) !== '') {
    echo '<div id="jsst-admin" class="jsst-root jsst-admin-root" data-jsst-scope="admin" data-jsst-version="1.7.9">' . $content . '</div>';
}

$controller->redirect();
