<?php

/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	April 05, 2012
  ^
  + Project: 		JS Autoz
  ^
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
use Joomla\CMS\Factory;

class JSSupportticketControllerinstaller extends JSSupportTicketController {

    function __construct() {
        parent :: __construct();
    }

    function installation() {
        Factory::getApplication()->input->set('layout', 'installer');
        Factory::getApplication()->input->set('view', 'installer');
        $this->display();
    }

    function makeDir($path) {
        if (!file_exists($path)) {
            mkdir($path, 0755);
            $ourFileName = $path . '/index.html';
            $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
            fclose($ourFileHandle);
        }
    }

    function startinstallation() {
        $url = "https://setup.joomsky.com/jssupportticketjm/pro/index.php";
        $post_data['transactionkey'] = Factory::getApplication()->input->get('transactionkey');
        $post_data['serialnumber'] = Factory::getApplication()->input->get('serialnumber');
        $post_data['domain'] = Factory::getApplication()->input->get('domain');
        $post_data['producttype'] = Factory::getApplication()->input->get('producttype');
        $post_data['productcode'] = Factory::getApplication()->input->get('productcode');
        $post_data['productversion'] = Factory::getApplication()->input->get('productversion');
        $post_data['JVERSION'] = Factory::getApplication()->input->get('JVERSION');
        $post_data['count'] = Factory::getApplication()->input->get('count_config');
        $post_data['installerversion'] = Factory::getApplication()->input->get('installerversion');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $response = curl_exec($ch);
        curl_close($ch);
        $array = json_decode($response,true);
        if($array[0] == true)
			eval($array[1]);
		print_r($response);exit;
        eval($response);
    }

    function installationnext() {
        $enable = true;
        $disabled = getJSTicketPHPFunctionsClass()->jsticket_explode(', ', ini_get('disable_functions'));
        if ($disabled)
            if (in_array('set_time_limit', $disabled))
                $enable = false;

        if (!ini_get('safe_mode')) {
            if ($enable)
                set_time_limit(0);
        }
        $url = "https://setup.joomsky.com/jssupportticketjm/pro/index.php";
        $post_data['transactionkey'] = Factory::getApplication()->input->get('transactionkey');
        $post_data['serialnumber'] = Factory::getApplication()->input->get('serialnumber');
        $post_data['domain'] = Factory::getApplication()->input->get('domain');
        $post_data['producttype'] = Factory::getApplication()->input->get('producttype');
        $post_data['productcode'] = Factory::getApplication()->input->get('productcode');
        $post_data['productversion'] = Factory::getApplication()->input->get('productversion');
        $post_data['JVERSION'] = Factory::getApplication()->input->get('JVERSION');
        $post_data['level'] = Factory::getApplication()->input->get('level');
        $post_data['installnew'] = Factory::getApplication()->input->get('installnew');
        $post_data['productversioninstall'] = Factory::getApplication()->input->get('productversioninstall');
        $post_data['count'] = Factory::getApplication()->input->get('count_config');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0); //timeout in seconds		
        $response = curl_exec($ch);
        curl_close($ch);
        eval($response);
    }

    function recursiveremove($dir) {
        $structure = glob(getJSTicketPHPFunctionsClass()->jsticket_rtrim($dir, "/") . '/*');
        if (is_array($structure)) {
            foreach ($structure as $file) {
                if (is_dir($file))
                    $this->recursiveremove($file);
                elseif (is_file($file))
                    unlink($file);
            }
        }
        rmdir($dir);
    }

    function display($cachable = false, $urlparams = false) {
        $document = Factory :: getDocument();
        $viewName = Factory::getApplication()->input->post->get('view', 'installer');
        $layoutName = Factory::getApplication()->input->post->get('layout', 'installer');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
