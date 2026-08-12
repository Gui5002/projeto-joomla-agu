<?php

/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');
use Joomla\CMS\Factory;

class JSSupportticketModelProinstaller extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getServerValidate() {
        $result = array();
        $array = getJSTicketPHPFunctionsClass()->jsticket_explode('.', phpversion());
        $phpversion = $array[0] . '.' . $array[1];
        $curlexist = function_exists('curl_version');
        $curlversion = '';
		$gd_lib = 1;
        $zip_lib = 0;
        if (file_exists('components/com_jssupportticket/include/lib/pclzip.lib.php')) {
            $zip_lib = 1;
        }
        $result = $this->getStepTwoValidate();
        $result['phpversion'] = $phpversion;
        $result['curlexist'] = $curlexist;
        $result['curlversion'] = $curlversion;
        $result['gdlib'] = $gd_lib;
        $result['ziplib'] = $zip_lib;
        return $result;
    }

    function getConfigByConfigName($configname) {
        $db = Factory::getDBO();
        $query = "SELECT * FROM `#__js_ticket_config` WHERE configname = " . $db->quote($configname);
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }

    function getCountConfig() {
        $db = Factory::getDBO();
        $query = "SELECT COUNT(*) AS count_config FROM `#__js_ticket_config` ";
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function getStepTwoValidate() {
        $return['admin_dir'] = getJSTicketPHPFunctionsClass()->jsticket_substr(sprintf('%o', fileperms('components/com_jssupportticket')), -3);
        if(!is_writable('components/com_jssupportticket')){
          $return['admin_dir'] = 0;
        }
        $return['site_dir'] = getJSTicketPHPFunctionsClass()->jsticket_substr(sprintf('%o', fileperms('../components/com_jssupportticket')), -3);
        if(!is_writable('../components/com_jssupportticket')){
          $return['site_dir'] = 0;
        }
        $return['tmp_dir'] = getJSTicketPHPFunctionsClass()->jsticket_substr(sprintf('%o', fileperms('../tmp')), -3);
        if(!is_writable('../tmp')){
          $return['tmp_dir'] = 0;
        }
        $db = $this->getDbo();
        $query = 'CREATE TABLE IF NOT EXISTS js_test_table(
                    id int,
                    name varchar(255)
                );';
        $db->setQuery($query);
        $return['create_table'] = 0;
        if ($db->execute()) {
            $return['create_table'] = 1;
        }
        $query = 'INSERT INTO js_test_table(id,name) VALUES (1,\'JoomSky\'),(2,\'Test 1\');';
        $db->setQuery($query);
        $return['insert_record'] = 0;
        if ($db->execute()) {
            $return['insert_record'] = 1;
        }
        $query = 'UPDATE js_test_table SET name = \'JoomSky Test\' WHERE id = 1;';
        $db->setQuery($query);
        $return['update_record'] = 0;
        if ($db->execute()) {
            $return['update_record'] = 1;
        }
        $query = 'DELETE FROM js_test_table;';
        $db->setQuery($query);
        $return['delete_record'] = 0;
        if ($db->execute()) {
            $return['delete_record'] = 1;
        }
        $query = 'DROP TABLE js_test_table;';
        $db->setQuery($query);
        $return['drop_table'] = 0;
        if ($db->execute()) {
            $return['drop_table'] = 1;
        }
        if($return['tmp_dir'] >= 755){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_URL, 'http://test.setup.joomsky.com/logo.png');
            $fp = fopen('../tmp/logo.png', 'w+');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
            curl_exec ($ch);
            curl_close ($ch);
            fclose($fp);
            $return['file_downloaded'] = 0;
            if(file_exists('../tmp/logo.png')){
                $return['file_downloaded'] = 1;
            }
        }else $return['file_downloaded'] = 0;
        return $return;
    }

    function getmyversionlist($data) {
        if(getJSTicketPHPFunctionsClass()->jsticket_trim($data['transactionkey']) == ''){
            $response = '["0","Please insert product key"]';
            return $response;
        }
        $post_data['transactionkey'] = $data['transactionkey'];
        $_SESSION['transactionkey'] = $data['transactionkey'];
        $post_data['serialnumber'] = $data['serialnumber'];
        $post_data['domain'] = $data['domain'];
        $post_data['producttype'] = $data['producttype'];
        $post_data['productcode'] = $data['productcode'];
        $post_data['productversion'] = $data['productversion'];
        $post_data['JVERSION'] = $data['JVERSION'];
        $post_data['count'] = $data['config_count'];
        $post_data['installerversion'] = $data['installerversion'];
        $ch = curl_init();
        $url = "https://setup.joomsky.com/jssupportticketjm/pro/index.php";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        if(curl_error($ch)) { 
            $response = '["0","'.curl_error($ch).'"]';
        }else{
            $response = curl_exec($ch);
        }
        curl_close($ch);
        return $response;
    }
}
?>
