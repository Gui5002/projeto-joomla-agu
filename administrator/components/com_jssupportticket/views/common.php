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

/*
 * The version actually shown to the admin.
 *
 * Two separate problems used to live here. First, a hardcoded "if the stored
 * value looks older than 171, call it 171" fallback, left over from the 1.7.1
 * release: on a 1.2.9 install it rewrote 129 to 171, so every badge and the
 * About page confidently reported 1.7.1 for a 1.2.9 component.
 *
 * Second, each of the three places that render this ran implode('.',
 * str_split($version)) on the digit string. That is only correct while every
 * part is a single digit - 1.2.10 is stored as "1210" and would render as
 * "1.2.1.0".
 *
 * Joomla's own extension record is the authority: manifest_cache carries the
 * version from the manifest that was installed, already dotted. The config row
 * is the fallback, and $this->version keeps its original digit-string contract
 * so nothing that compares it numerically changes behaviour.
 */
$manifestVersion = '';
$db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
$manifestQuery = $db->getQuery(true)
    ->select($db->quoteName('manifest_cache'))
    ->from($db->quoteName('#__extensions'))
    ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
    ->where($db->quoteName('element') . ' = ' . $db->quote('com_jssupportticket'));
$manifestCache = json_decode((string) $db->setQuery($manifestQuery)->loadResult(), true);

if (is_array($manifestCache) && !empty($manifestCache['version'])) {
    $manifestVersion = (string) $manifestCache['version'];
}

if ($manifestVersion !== '') {
    $this->versionDisplay = $manifestVersion;
} elseif (preg_match('/^\d{3,}$/', (string) $version)) {
    // Digit string: 1 major, 1 minor, everything left is the patch.
    $this->versionDisplay = $version[0] . '.' . $version[1] . '.' . substr($version, 2);
} else {
    $this->versionDisplay = (string) $version;
}
HTMLHelper::_('jquery.framework');

$this->option = $option;
$this->Itemid = $itemid;
$this->config = $config;

?>
