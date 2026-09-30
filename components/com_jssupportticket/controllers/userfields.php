<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');
use Joomla\CMS\Factory;

jimport('joomla.application.component.controller');

class JSSupportticketControllerUserFields extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
    }

    function datafordepandantfield() {
        Factory::getSession()->checkToken( 'get' ) or die( 'Invalid Token' );
        $val = Factory::getApplication()->input->get('fvalue','','string');
        $childfield = Factory::getApplication()->input->get('child'); 
        $result = $this->getJSModel('userfields')->dataForDepandantField( $val , $childfield);
        $result = json_encode($result);
        echo $result;
        Factory::getApplication()->close();
    }

    function display($cachable = false, $urlparams = false) {
        $document = Factory::getDocument();
        $viewName = 'userfields';
        $layoutName = Factory::getApplication()->input->post->get('layout', 'userfields');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
