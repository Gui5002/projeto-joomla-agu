<?php
/**
 * JS Support Ticket v1 pro installer controller.
 * Local installation logic replaces the old remote eval installer.
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

jimport('joomla.application.component.controller');

class JSSupportticketControllerinstaller extends JSSupportTicketController
{
    function __construct()
    {
        parent::__construct();
    }

    function installation()
    {
        Factory::getApplication()->input->set('layout', 'installer');
        Factory::getApplication()->input->set('view', 'installer');
        $this->display();
    }

    function startinstallation()
    {
        $this->installationnext();
    }

    function installationnext()
    {
        $app = Factory::getApplication();
        $data = $app->input->post->getArray();
        require_once JPATH_ADMINISTRATOR . '/components/com_jssupportticket/include/classes/proinstallclient.php';
        try {
            $client = new JSSTProInstallClient();
            $client->prepareAndInstall($data);
            $this->setRedirect('index.php?option=com_jssupportticket', Text::_('JS Support Ticket installation complete.'));
        } catch (Throwable $e) {
            $app->enqueueMessage($e->getMessage(), 'error');
            $this->setRedirect('index.php?option=com_jssupportticket&c=proinstaller&layout=step1');
        }
    }

    function display($cachable = false, $urlparams = false)
    {
        $document = Factory::getDocument();
        $viewName = Factory::getApplication()->input->post->get('view', 'installer');
        $layoutName = Factory::getApplication()->input->post->get('layout', 'installer');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
