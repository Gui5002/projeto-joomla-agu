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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelProinstaller extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getServerValidate() {
        $result = array();
        $phpversion = PHP_VERSION;

        $curlexist = function_exists('curl_init') && function_exists('curl_version');
        $curlversion = '';
        $curlssl = 0;
        if ($curlexist) {
            $curlinfo = curl_version();
            $curlversion = isset($curlinfo['version']) ? $curlinfo['version'] : '';
            if (defined('CURL_VERSION_SSL') && isset($curlinfo['features']) && ($curlinfo['features'] & CURL_VERSION_SSL)) {
                $curlssl = 1;
            }
        }


        $zip_lib = 0;
        if (class_exists('\Joomla\CMS\Filesystem\Archive') || class_exists('ZipArchive') || function_exists('gzinflate') || file_exists('components/com_jssupportticket/include/lib/pclzip.lib.php')) {
            $zip_lib = 1;
        }

        $result = $this->getStepTwoValidate();
        $result['phpversion'] = $phpversion;
        $result['curlexist'] = $curlexist ? 1 : 0;
        $result['curlssl'] = $curlssl;
        $result['curlversion'] = $curlversion;
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
            $return['file_downloaded'] = 0;
            if(function_exists('curl_init')){
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
                if(file_exists('../tmp/logo.png')){
                    $return['file_downloaded'] = 1;
                }
            }
        }else $return['file_downloaded'] = 0;
        return $return;
    }

    function getmyversionlist($data) {
        if(getJSTicketPHPFunctionsClass()->jsticket_trim($data['transactionkey']) == ''){
            return '["0","Please insert product key"]';
        }
        require_once JPATH_ADMINISTRATOR . '/components/com_jssupportticket/include/classes/proinstallclient.php';
        $_SESSION['transactionkey'] = $data['transactionkey'];
        try {
            $client = new JSSTProInstallClient();
            $versions = $client->getVersions([
                'transactionkey' => $data['transactionkey'],
                'domain' => $data['domain'],
                'producttype' => $data['producttype'],
                'productcode' => $data['productcode'],
                'productversion' => $data['productversion'],
                'JVERSION' => $data['JVERSION'],
                'count_config' => $data['config_count'],
            ]);
            $html = $this->buildVersionSelectHtml($data, $versions['versions'] ?? []);
            return json_encode([true, '', $html]);
        } catch (Throwable $e) {
            return json_encode([false, $e->getMessage()]);
        }
    }

    private function buildVersionSelectHtml($data, $versions) {
        $options = '<option value="">' . htmlspecialchars(Text::_('Choose Version'), ENT_QUOTES, 'UTF-8') . '</option>';
        foreach ((array)$versions as $version) {
            $version = (string)$version;
            $options .= '<option value="' . htmlspecialchars($version, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($version, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        $installnew = ((int)$data['config_count'] > 50) ? 0 : 1;
        return '
            <script>
                function opendiv(){
                    document.getElementById("jsjob_installer_waiting_div").style.display="block";
                    document.getElementById("jsjob_installer_waiting_span").style.display="block";
                    return true;
                }
            </script>
            <form action="index.php" method="POST" name="adminForm" id="adminForm">
                <div id="jsjob_installer_waiting_div" style="display:none;"></div>
                <div class="js_wrapper">
                    <span id="jsjob_installer_waiting_span" style="display:none;">Please wait, installation is in progress.</span>
                    <span id="jsjob_installer_helptext">Please select the version you want to install.</span>
                    <div id="jsjob_installer_formlabel"><label id="transactionkeymsg_after" for="productversioninstall">Select Version</label></div>
                    <div id="jsjob_installer_forminput"><select id="productversioninstall" name="productversioninstall">' . $options . '</select></div>
                    <div id="jsjob_installer_formsubmitbutton"><input type="submit" class="button" id="jsjob_instbutton_after" name="submit_app" onclick="return opendiv();" value="Continue" /></div>
                    <input type="hidden" name="domain" value="' . htmlspecialchars($data['domain'], ENT_QUOTES, 'UTF-8') . '" />
                    <input type="hidden" name="producttype" value="' . htmlspecialchars($data['producttype'], ENT_QUOTES, 'UTF-8') . '" />
                    <input type="hidden" name="productcode" value="jssupportticket" />
                    <input type="hidden" name="productversion" value="' . htmlspecialchars($data['productversion'], ENT_QUOTES, 'UTF-8') . '" />
                    <input type="hidden" name="transactionkey" value="' . htmlspecialchars($data['transactionkey'], ENT_QUOTES, 'UTF-8') . '" />
                    <input type="hidden" name="count_config" value="' . htmlspecialchars($data['config_count'], ENT_QUOTES, 'UTF-8') . '" />
                    <input type="hidden" name="JVERSION" value="' . htmlspecialchars($data['JVERSION'], ENT_QUOTES, 'UTF-8') . '" />
                    <input type="hidden" name="c" value="installer" />
                    <input type="hidden" name="task" value="installationnext" />
                    <input type="hidden" name="level" value="level2" />
                    <input type="hidden" name="installnew" value="' . $installnew . '" />
                    <input type="hidden" name="option" value="com_jssupportticket" />
                </div>
            </form>';
    }
}
?>
