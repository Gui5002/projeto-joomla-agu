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

class JSSupportticketControllerConfig extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function saveconf() {
        Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
        $return_value = $this->getJSModel('config')->storeConfig();
        if ($return_value == 1) {
            $type = 'message';
            $msg = Text::_('The configuration has been stored');
        }elseif($return_value == 2){
            $msg = Text::_('Invalid data directory');
            $type = 'error';
        }elseif($return_value == 3){
            $msg = Text::_('Data directory in not writable');
            $type = 'error';
        }else {
            $msg = Text::_('Configuration not has been stored');
            $type = 'error';
        }
        $link = 'index.php?option=com_jssupportticket&c=config&layout=config';
        $this->setRedirect($link, $msg, $type);
    }

    function cancelconfig() {
        $link = "index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel";
        $msg = Text::_('Operation Cancel');
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = Factory::getDocument();
        $viewName = 'config';
        $layoutName = Factory::getApplication()->input->get('layout', 'config');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
