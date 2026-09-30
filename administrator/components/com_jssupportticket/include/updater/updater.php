<?php
	
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , ahmad@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
	
	$db = Factory::getDbo();
	$query = "SELECT * FROM `#__js_ticket_config` WHERE configname = 'version' OR configname = 'last_version' OR configname = 'last_step_updater'";
	$db->setQuery($query);
	$result = $db->loadObjectList();
	$config = array();
	foreach($result AS $rs){
		$config[$rs->configname] = $rs->configvalue;
	}
	$config['version'] = getJSTicketPHPFunctionsClass()->jsticket_str_replace('.', '', $config['version']);
	if(!empty($config['last_version']) && $config['last_version'] != '' && $config['last_version'] < $config['version']){
		$last_version = $config['last_version'] + 1; // files execute from the next version
		$currentversion = $config['version'];
		for($i = $last_version; $i <= $currentversion; $i++){
			$path = JPATH_COMPONENT_ADMINISTRATOR.'/include/updater/files/'.$i.'.php';
			if(file_exists($path)){
				include_once($path);
			}
		}
	}
	$mainfile = JPATH_COMPONENT_ADMINISTRATOR.'/views/jssupportticket/tmpl/controlpanel.php';
	$contents = file_get_contents($mainfile);
	//$contents = getJSTicketPHPFunctionsClass()->jsticket_str_replace('$path = JPATH_COMPONENT_ADMINISTRATOR."/include/updater/updater.php";require_once($path);', '', $contents);
	$contents = getJSTicketPHPFunctionsClass()->jsticket_str_replace('$path = JPATH_COMPONENT_ADMINISTRATOR.\'/include/updater/updater.php\';require_once($path);', '', $contents);
	file_put_contents($mainfile, $contents);

	function recursiveremove($dir) {
		$structure = glob(getJSTicketPHPFunctionsClass()->jsticket_rtrim($dir, "/").'/*');
		if (is_array($structure)) {
			foreach($structure as $file) {
				if (is_dir($file)) recursiveremove($file);
				elseif (is_file($file)) unlink($file);
			}
		}
		rmdir($dir);
	}            	
	$dir = JPATH_COMPONENT_ADMINISTRATOR.'/include/updater';
	recursiveremove($dir);

?>
