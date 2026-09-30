<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

require_once JPATH_COMPONENT . '/JSApplication.php';
require_once JPATH_COMPONENT . '/views/messageslayout.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/currentuser.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/constants.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/messages.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/cronjob.php';

$language = Factory::getLanguage();
$language->load('com_jssupportticket', JPATH_ADMINISTRATOR, null, true);

$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/bootstrap.min.css');
$document->addStyleSheet('components/com_jssupportticket/include/css/jsst-ticket-detail-site.css?v=281');


function getCustomFieldClass() {
    include_once JPATH_COMPONENT_ADMINISTRATOR . '/include/classes/customfields.php';
    return new customfields();
}

function getJSTicketPHPFunctionsClass() {
    include_once JPATH_COMPONENT_ADMINISTRATOR . '/include/classes/jsticketphpfunctions.php';
    return new jsticketphpfunctions();
}

require_once JPATH_COMPONENT . '/include/css/color.php';

// Publish the seven administrator-configurable theme colours as CSS custom
// properties. color.php defines $color1..$color7 (written by the Themes screen);
// anything missing or not a valid hex literal falls back to the shipped default
// that jsst-modern-site-late.css already declares on :root. This declaration is
// emitted after the component stylesheets, so it wins on source order and the
// whole frontend re-themes without touching a single rule.
$jsstThemeVars = array(
    'color1' => array('--jsst-top-menu-bg', '#2563eb'),
    'color2' => array('--jsst-heading-color', '#1e293b'),
    'color3' => array('--jsst-content-bg', '#f8fafc'),
    'color4' => array('--jsst-content-text', '#334155'),
    'color5' => array('--jsst-border-color', '#dbe4ef'),
    'color6' => array('--jsst-secondary-bg', '#eff6ff'),
    'color7' => array('--jsst-header-action-text', '#ffffff'),
);

$jsstThemeCss = '';
foreach ($jsstThemeVars as $jsstVar => $jsstMeta) {
    $jsstValue = isset($$jsstVar) && is_string($$jsstVar) ? trim($$jsstVar) : '';
    if (!preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $jsstValue)) {
        $jsstValue = $jsstMeta[1];
    }
    $jsstThemeCss .= $jsstMeta[0] . ':' . strtolower($jsstValue) . ';';
}
$document->addStyleDeclaration(':root{' . $jsstThemeCss . '}');
unset($jsstThemeVars, $jsstThemeCss, $jsstVar, $jsstMeta, $jsstValue);

if (JVERSION < 3) {
    HTMLHelper::_('behavior.mootools');
    $document->addScript('administrator/components/com_jssupportticket/include/js/jquery.js');
} else {
    HTMLHelper::_('bootstrap.framework');
    HTMLHelper::_('jquery.framework');
}

$document->addScript('components/com_jssupportticket/include/js/my_js.js');

$c = Factory::getApplication()->input->getCmd('c', 'jssupportticket');
if ($c) {
    $path = JPATH_COMPONENT . '/controllers/' . $c . '.php';

    if (is_file($path)) {
        require_once $path;
    } else {
        throw new Exception(Text::_('Unknown Controller: <br>' . $c . ':' . $path), 500);
    }
}

$class = 'JSSupportTicketController' . $c;
$controller = new $class();

ob_start();
$controller->execute(Factory::getApplication()->input->getCmd('task', 'display'));
$content = ob_get_clean();

// Added after controller execution so these land last in the document head, after
// anything a view added for itself (e.g. Joomla's calendar-jos.css). The old
// per-view include/css/inc.css/*.css sheets each view used to add here have been
// folded into jsst-modern-site-late.css and deleted.
$document->addStyleSheet('components/com_jssupportticket/include/css/jsst-modern-site-late.css?v=432');
$document->addStyleSheet('components/com_jssupportticket/include/css/jsst-site-complete.css?v=465');
$document->addScript('components/com_jssupportticket/include/js/jsst-site-complete.js?v=401');

// Keep frontend behavior scoped to the JS Support Ticket component.
// Do not move, hide, or restyle Joomla template widgets from this component.


if (trim($content) !== '') {
    $layout = Factory::getApplication()->input->getCmd('layout', 'default');
    $safeController = htmlspecialchars($c, ENT_QUOTES, 'UTF-8');
    $safeLayout = htmlspecialchars($layout, ENT_QUOTES, 'UTF-8');
    echo '<div id="jsst-site" class="jsst-root jsst-site-root jsst-controller-' . $safeController . ' jsst-layout-' . $safeLayout . '" data-jsst-scope="site" data-jsst-controller="' . $safeController . '" data-jsst-layout="' . $safeLayout . '" data-jsst-version="1.9.0">' . $content . '</div>';
}

$controller->redirect();
