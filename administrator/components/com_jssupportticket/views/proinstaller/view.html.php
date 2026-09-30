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
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewProInstaller extends JSSupportTicketView
{
        function display($tpl = null){

                require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
                ToolbarHelper::title(Text::_('JS Support Ticket Pro Installer'));
                if($layoutName == 'step1'){
                	$result = $this->getJSModel('proinstaller')->getServerValidate();
                        $configs = $this->getJSModel('config')->getConfigs();
                        $config_count = $this->getJSModel('proinstaller')->getCountConfig();
                	$this->result=$result;
                        $this->config_count=$config_count;
                        $this->config=$configs;
                        if(isset($_SESSION['response'])){
                                $response = $_SESSION['response'];
                                $response = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($response);
                                $response = json_decode($response);
                                if(isset($response[1])) $this->response=$response[1];
                                unset($_SESSION['response']);
                        }
                }elseif($layoutName == 'step2') {
                        /*
                         * Read without consuming - deliberately different from step 1.
                         *
                         * On step 1 the session value is a one-shot error message, so
                         * unsetting it on read is correct. On step 2 the response IS the
                         * page: it carries the version dropdown and every hidden field the
                         * install form needs. Unsetting it meant the first render worked
                         * and any refresh produced a step 2 with no version form and an
                         * empty activation key - a dead end with no way forward.
                         *
                         * Keeping the values cannot serve stale data for a different key:
                         * getversionlist() rewrites $_SESSION['response'] on every submit,
                         * and the model rewrites $_SESSION['transactionkey'] alongside it.
                         */
                        if(isset($_SESSION['response'])){
                                $this->response=$_SESSION['response'];
                        }
                        if(isset($_SESSION['transactionkey'])){
                                $this->transactionkey=$_SESSION['transactionkey'];
                        }

                        /*
                         * No response at all means step 2 was reached without going through
                         * step 1 - a bookmarked URL, or a session that has since expired.
                         * Send them back rather than render the key field on its own.
                         */
                        if(!isset($this->response) || $this->response === ''){
                                Factory::getApplication()->redirect(
                                        'index.php?option=com_jssupportticket&c=proinstaller&layout=step1'
                                );

                                return;
                        }
                }
                parent::display($tpl);
        }
}
?>
