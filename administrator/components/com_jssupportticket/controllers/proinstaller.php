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
defined('_JEXEC') or die('Not Allowed');
use Joomla\CMS\Factory;

jimport('joomla.application.component.controller');

class JSSupportticketControllerProinstaller extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function getversionlist() {
        $data =  Factory::getApplication()->input->post->getArray();
        $response = $this->getJSModel('proinstaller')->getmyversionlist($data);
        $response = getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($response);
        $_SESSION['response'] = $response;
        $response = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($response);
        $response = json_decode($response);
        if($response[0] == true){
            $url = "index.php?option=com_jssupportticket&c=proinstaller&layout=step2";    
        }else{
            $url = "index.php?option=com_jssupportticket&c=proinstaller&layout=step1";
        }
        $this->setRedirect($url);
    }

    function display($cachable = false, $urlparams = false) {
        $document = Factory::getDocument();
        $jinput = Factory::getApplication()->input;
        $viewName = $jinput->get('view', 'proinstaller');
        $layoutName = $jinput->get('layout', 'step1');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
