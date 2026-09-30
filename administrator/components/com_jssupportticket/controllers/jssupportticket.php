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

class JSSupportticketControllerJSSupportticket extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }


    function savetheme(){
        Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
        $data = Factory::getApplication()->input->post->getArray();
        $model = $this->getJSModel('jssupportticket');
        $result = $model->storeTheme($data);
        $link = 'index.php?option=com_jssupportticket&c=jssupportticket&layout=themes';

        if($result == true){
            $this->setRedirect($link, Text::_('Theme has been saved successfully.'), 'message');
            return;
        }

        $error = $model->getError();
        if ($error == '') {
            $error = Text::_('Theme could not be saved. Please check file permissions and try again.');
        }
        $this->setRedirect($link, $error, 'error');
    }

    /**
     * Translation delivery. Replaces the old joomsky.com API tasks
     * (getlisttranslations / validateandshowdownloadfilename /
     * getlanguagetranslation), which returned raw HTML that was injected
     * straight into the DOM. These return JSON only.
     */
    private function sendTranslationJson(array $payload)
    {
        $app = Factory::getApplication();

        // Anything already emitted - a PHP notice with display_errors on, a BOM,
        // stray template output - would make this response invalid JSON and the
        // browser would report a generic failure. Discard it first.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $app->setHeader('Content-Type', 'application/json', true);
        $app->setHeader('Cache-Control', 'no-store', true);
        $app->sendHeaders();
        echo json_encode($payload);
        $app->close();
    }

    private function getTranslationsService()
    {
        require_once JPATH_ADMINISTRATOR . '/components/com_jssupportticket/include/classes/jssupporttickettranslations.php';
        return new JSSupportTicketTranslations();
    }

    function translationslist(){
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get')
            or jexit(Text::_('JINVALID_TOKEN'));
        $force   = (int) Factory::getApplication()->input->getInt('refresh', 0) === 1;
        $service = $this->getTranslationsService();
        $list    = $service->getAvailable($force);

        if ($list === false) {
            $this->sendTranslationJson(array('error' => $service->error ?: Text::_('Unable to connect to server')));
        }
        $this->sendTranslationJson(array('error' => false, 'languages' => $list));
    }

    function translationsinstall(){
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get')
            or jexit(Text::_('JINVALID_TOKEN'));
        $code    = Factory::getApplication()->input->getCmd('code', '');
        $service = $this->getTranslationsService();

        if (!$service->install($code)) {
            $this->sendTranslationJson(array('error' => $service->error ?: Text::_('Operation Aborted')));
        }
        $this->sendTranslationJson(array(
            'error'   => false,
            'message' => Text::sprintf('%s has been saved successfully', $code),
        ));
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
