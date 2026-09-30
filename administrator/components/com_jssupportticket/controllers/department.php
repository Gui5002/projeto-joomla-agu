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
defined('_JEXEC') or die('Not Allowed');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.controller');

class JSSupportticketControllerDepartment extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function savedepartment() {
        $this->storedepartment('saveandclose');
    }

    function savedepartmentsave() {
        $this->storedepartment('save');
    }

    function savedepartmentandnew() {
        $this->storedepartment('saveandnew');
    }

    function storedepartment($callfrom) {
        Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
        $data = Factory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('department')->storeDepartment($data);
        if ($result == SAVED) {
            if($callfrom == 'saveandclose') {
                $link = "index.php?option=com_jssupportticket&c=department&layout=departments";
            }elseif ($callfrom == 'save') {
                $link = "index.php?option=com_jssupportticket&c=department&layout=formdepartment&cid[]=" .JSSupportticketMessage::$recordid;
            }elseif ($callfrom == 'saveandnew') {
                $link = "index.php?option=com_jssupportticket&c=department&layout=formdepartment";
            }
        }else{
            $link = 'index.php?option=com_jssupportticket&c=department&layout=formdepartment';
        }
        $msg = JSSupportticketMessage::getMessage($result,'DEPARTMENT');
        $this->setRedirect($link, $msg);
    }

    function deletedepartment() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('department')->deleteDepartmentAdmin();
        $msg = JSSupportticketMessage::getMessage($result,'DEPARTMENT');
        $link = "index.php?option=com_jssupportticket&c=department&layout=departments";
        $this->setRedirect($link, $msg);
    }

    function canceldepartment() {
        $link = "index.php?option=com_jssupportticket&c=department&layout=departments";
        $msg = JSSupportticketMessage::getMessage(CANCEL,'DEPARTMENT');
        $this->setRedirect($link, $msg);
    }

    function addnewdepartment() {
        $layoutName =Factory::getApplication()->input->set('layout', 'formdepartment');
        $this->display();
    }
    function display($cachable = false, $urlparams = false) {
        $document = Factory::getDocument();
        $viewName = 'department';
        $layoutName = Factory::getApplication()->input->get('layout', 'department');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
