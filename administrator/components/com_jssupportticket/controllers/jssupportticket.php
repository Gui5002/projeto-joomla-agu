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
jimport('joomla.application.component.controller');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class JSSupportticketControllerJSSupportticket extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function getusersearchajax() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('ticket')->getusersearchajax();
        echo $returnvalue;
        Factory::getApplication()->close();
    }

    function getlisttranslations(){
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('jssupportticket')->getListTranslations();
        echo $result;
        Factory::getApplication()->close();
    }
    
    function validateandshowdownloadfilename(){
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $langname = Factory::getApplication()->input->getString('langname');
        $result = $this->getJSModel('jssupportticket')->validateAndShowDownloadFileName( $langname );
        echo $result;
        Factory::getApplication()->close();
    }

    function getlanguagetranslation(){
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $langname = Factory::getApplication()->input->getString('langname');
        $filename = Factory::getApplication()->input->getString('filename');
        $result = $this->getJSModel('jssupportticket')->getLanguageTranslation( $langname , $filename);
        echo $result;
        Factory::getApplication()->close();
    }


    function display($cachable = false, $urlparams = false) {
        $document = Factory::getDocument();
        $viewName = 'jssupportticket';
        $jinput = Factory::getApplication()->input;
        $layoutName = $jinput->get('layout', 'controlpanel');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }


}

?>
