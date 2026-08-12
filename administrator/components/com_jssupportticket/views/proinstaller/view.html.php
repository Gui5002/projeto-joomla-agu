<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
 ^
 + Project: 	JS Tickets
 ^ 
*/
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;


class JSSupportticketViewProInstaller extends JSSupportTicketView
{
	function display($tpl = null){
        
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        ToolbarHelper::title(Text::_('JS Support Ticket Pro Installer'));
        if($layoutName == 'step1'){
                $result = $this->getJSModel('proinstaller')->getServerValidate();
                $configs = $this->getJSModel('config')->getConfigs();
                $config_count = $this->getJSModel('proinstaller')->getCountConfig();
                $this->result = $result;
                $this->config_count = $config_count;
                $this->config = $configs;
                if(isset($_SESSION['response'])){
                        $response = $_SESSION['response'];
                        $response = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($response);
                        $response = json_decode($response);
                         if(isset($response[1])) $this->response=$response[1];
                        unset($_SESSION['response']);
                }
        }elseif($layoutName == 'step2') {
                if(isset($_SESSION['response'])){
                        $this->response=$_SESSION['response'];
                        unset($_SESSION['response']);
                }
                if(isset($_SESSION['transactionkey'])){
                        $this->transactionkey=$_SESSION['transactionkey'];
                        unset($_SESSION['transactionkey']);
                }   
        }
        parent::display($tpl);
	}
}
?>
