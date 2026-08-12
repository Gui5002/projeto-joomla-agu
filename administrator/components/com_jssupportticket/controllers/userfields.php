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

jimport('joomla.application.component.controller');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class JSSupportticketControllerUserFields extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function saveuserfield() {
        $this->storeUserFields('saveandclose');
    }

    function saveuserfieldsave() {
        $this->storeUserFields('save');
    }

    function saveuserfieldandnew() {
        $this->storeUserFields('saveandnew');
    }

    function storeUserFields() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $data = Factory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('userfields')->storeUserField();
        if ($result == SAVED) {
            $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering&ff='.$data['fieldfor'];
        }else{
            $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering&ff='.$data['fieldfor'];
        }
        $msg = JSSupportticketMessage::getMessage($result,'USER_FIELD');
        $this->setRedirect($link, $msg);
    }

    function fieldpublished() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $cid = Factory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldPublished($fieldid, 1); // published
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = Text::_('Field mark as published');
        $this->setRedirect($link, $msg);
    }

    function fieldunpublished() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $cid = Factory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldPublished($fieldid, 0); // unpublished
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = Text::_('Field mark as unpublished');
        $this->setRedirect($link, $msg);
    }

    function fieldrequired() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $cid = Factory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldRequired($fieldid, 1); // required
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = Text::_('Field mark as required');
        $this->setRedirect($link, $msg);
    }

    function fieldnotrequired() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $cid = Factory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldRequired($fieldid, 0); // not required
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = Text::_('Field mark as not required');
        $this->setRedirect($link, $msg);
    }

    function fieldorderingup() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $cid = Factory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldOrderingUp($fieldid);
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = Text::_('Field ordering down');
        $this->setRedirect($link, $msg);
    }

    function fieldorderingdown() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $cid = Factory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldOrderingDown($fieldid);
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = Text::_('Field ordering up');
        $this->setRedirect($link, $msg);
    }

    function adduserfield() {
        $fieldfor = Factory::getApplication()->input->get('ff',1);
        Factory::getApplication()->input->set('ff',$fieldfor);
        $layoutName = Factory::getApplication()->input->set('layout', 'formuserfield');
        $this->display();
    }


    function removeuserfields() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $cid = Factory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->deleteUserField($fieldid );
        $msg = JSSupportticketMessage::getMessage($result,'USER_FIELD');
        if ($result == DELETE_ERROR){
            $msg = JSSupportticketMessage::$recordid. ' ' . $msg;
        }
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $this->setRedirect($link, $msg);
    }

    function canceluserfield() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'USER_FIELD');
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $this->setRedirect($link, $msg);
    }

    // new
    function getfieldsforcombobyfieldfor() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $fieldfor = Factory::getApplication()->input->get('fieldfor','','string');
        $parentfield = Factory::getApplication()->input->get('parentfield');
        $result = $this->getJSModel('userfields')->getFieldsForComboByFieldFor( $fieldfor, $parentfield );
        $result = json_encode($result);
        echo $result;
        Factory::getApplication()->close();
    }

    function datafordepandantfield() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $val = Factory::getApplication()->input->get('fvalue','','string'); 
        $childfield = Factory::getApplication()->input->get('child'); 
        $result = $this->getJSModel('userfields')->dataForDepandantField( $val , $childfield);
        $result = json_encode($result);
        echo $result;
        Factory::getApplication()->close();
    }

    function getoptionsforfieldedit() {
        $field = Factory::getApplication()->input->get('field');
        $result = $this->getJSModel('userfields')->getOptionsForFieldEdit( $field);
        $result = json_encode($result);
        echo $result;
        Factory::getApplication()->close();
    }

    function getsectiontofillvalues() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $field = Factory::getApplication()->input->get('pfield');
        $result = $this->getJSModel('userfields')->getSectionToFillValues( $field );
        $result = json_encode($result);
        echo $result;
        Factory::getApplication()->close();
    }        

    function display($cachable = false, $urlparams = false) {
        $document = Factory::getDocument();
        $viewName = 'userfields';
        $layoutName = Factory::getApplication()->input->get('layout', 'userfields');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>