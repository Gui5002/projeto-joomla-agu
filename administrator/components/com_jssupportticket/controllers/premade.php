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
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.controller');

class JSSupportticketControllerPremade extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function savepremade() {
        $this->storepremade('saveandclose');
    }

    function savepremadeandnew() {
        $this->storepremade('saveandnew');
    }

    function savepremadesave() {
        $this->storepremade('save');
    }

    function storepremade($callfrom) {
        Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
        $data = Factory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('premade')->storeDepartmentPremade($data);
        if ($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = 'index.php?option=com_jssupportticket&c=premade&layout=departmentspremade';
            } elseif ($callfrom == 'save') {
                $link = 'index.php?option=com_jssupportticket&c=premade&layout=formpremade&cid[]='.JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = 'index.php?option=com_jssupportticket&c=premade&layout=formpremade';
            }
        }else{
            $link = 'index.php?option=com_jssupportticket&c=premade&layout=departmentspremade';
        }
        $msg = JSSupportticketMessage::getMessage($result,'DEPARTMENT_PREMADE');
        $this->setRedirect($link, $msg);
    }

    function editpremade() {
        Factory::getApplication()->input->set('layout', 'formpremade');
        $this->display();
    }

    function removedepartmentpremade() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('premade')->deleteDepartmentPremade();
        $msg = JSSupportticketMessage::getMessage($result,'DEPARTMENT_PREMADE');
        if ($result == DELETE_ERROR){
            $msg = JSSupportticketMessage::$recordid. ' ' . $msg;
        }
        $link = 'index.php?option=com_jssupportticket&c=premade&layout=departmentspremade';
        $this->setRedirect($link, $msg);
    }

    function cancelpremade() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'DEPARTMENT_PREMADE');
        $link = 'index.php?option=com_jssupportticket&c=premade&layout=departmentspremade';
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = Factory::getDocument();
        $viewName = 'premade';
        $layoutName = Factory::getApplication()->input->get('layout', 'premade');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
