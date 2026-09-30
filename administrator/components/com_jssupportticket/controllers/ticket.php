<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;

jimport('joomla.application.component.controller');

class JSSupportticketControllerTicket extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function saveticket() {
        $this->storeticket('saveandclose');
    }

    function saveticketsave() {
        $this->storeticket('save');
    }

    function saveticketandnew() {
        $this->storeticket('saveandnew');
    }

    function storeticket($callfrom) {
        Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
        $data = Factory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('ticket')->storeTicket($data);
        if($result == SAVED) {
            switch ($callfrom) {
                case 'save':
                    $link = 'index.php?option=com_jssupportticket&c=ticket&layout=formticket&cid[]='.JSSupportticketMessage::$recordid;
                    break;
                case 'saveandnew':
                    $link = 'index.php?option=com_jssupportticket&c=ticket&layout=formticket';
                    break;
                case 'saveandclose':
                    $link = 'index.php?option=com_jssupportticket&c=ticket&layout=tickets';
                    break;
            }
        }else{
            Factory::getApplication()->setUserState('com_jssupportticket.data',$data);
            $link = 'index.php?option=com_jssupportticket&c=ticket&layout=formticket';
        }
        $msg = JSSupportticketMessage::getMessage($result,'TICKET');
        $this->setRedirect($link, $msg);
    }

    function actionticket() {
        Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
        $ticket = $this->getJSModel('ticket');
        $data = Factory::getApplication()->input->post->getArray();
        $action = $data['callfrom'];
        switch ($action) {
            case 'postreply':
                $data['responce'] = Factory::getApplication()->input->get('responce', '', 'raw');
                $result = $ticket->storeTicketReplies($data['id'],$data['responce'], $data['created'], $data);
                $msg = JSSupportticketMessage::getMessage($result,'REPLY');
                $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                $this->setRedirect($link, $msg);
                break;
            case 'departmenttransfer':
                $data['departmenttranfer'] = Factory::getApplication()->input->get('departmenttranfer', '', 'raw');
                $result = $ticket->ticketDepartmentTransfer($data['id'], $data['departmentid'], $data['departmenttranfer'], $data['created'], $data);
                $msg = JSSupportticketMessage::getMessage($result,'DEPARTMENT');
                $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                $this->setRedirect($link, $msg);
                break;
            case 'action':
                switch ($data['callaction']) {
                    case 1://change priority
                        $result = $ticket->changeTicketPriority($data['id'], $data['priorityid'], $data['created']);
                        $msg = JSSupportticketMessage::getMessage($result,'PRIORITY');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 3: //ticket close
                        $result = $ticket->ticketClose($data['id'], $data['created']);
                        $msg = JSSupportticketMessage::getMessage($result,'CLOSE');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 5: //ticket delete
                        $result = $ticket->delete_Ticket($data['id']);
                        $msg = JSSupportticketMessage::getMessage($result,'DELETE');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=tickets';
                        $this->setRedirect($link, $msg);
                        break;
                    case 8: //reopened ticket
                        $result = $ticket->reopenTicket($data['id'], $data['lastreply']);
                        $msg = JSSupportticketMessage::getMessage($result,'REOPEN');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                }
                break;
        }
    }
    
    function enforcedelete() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('ticket')->enforcedeleteTicket();
        $msg = JSSupportticketMessage::getMessage($result,'TICKET');
        $link = "index.php?option=com_jssupportticket&c=ticket&layout=tickets";
        $this->setRedirect($link, $msg);
    }

    function delete() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('ticket')->deleteTicket();
        $msg = JSSupportticketMessage::getMessage($result,'TICKET');
        $link = "index.php?option=com_jssupportticket&c=ticket&layout=tickets";
        $this->setRedirect($link, $msg);
    }


    function saveticketsavedview() {
        Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
        $input = Factory::getApplication()->input;
        $title = trim((string) $input->getString('saved_view_title', ''));
        if ($title === '') {
            $title = trim((string) $input->post->getString('saved_view_title', ''));
        }
        if ($title === '') {
            $title = trim((string) $input->getString('jsst_saved_view_title', ''));
        }
        $filters = array(
            'filter_ticketid' => $input->getString('filter_ticketid', ''),
            'filter_subject' => $input->getString('filter_subject', ''),
            'filter_from' => $input->getString('filter_from', ''),
            'filter_fromemail' => $input->getString('filter_fromemail', ''),
            'filter_datestart' => $input->getString('filter_datestart', ''),
            'filter_dateend' => $input->getString('filter_dateend', ''),
            'filter_department' => $input->getString('filter_department', ''),
            'filter_priority' => $input->getString('filter_priority', ''),
            'lt' => $input->getInt('lt', 1),
        );
        $sortby = $input->getString('sortby', '');
        $featureModel = $this->getJSModel('featurefoundation');
        $result = $featureModel->storeTicketSavedView($title, $filters, $sortby, 'private');
        if ($result) {
            $msg = Text::_('Saved view has been created');
        } else {
            $reason = method_exists($featureModel, 'getLastSavedViewError') ? $featureModel->getLastSavedViewError() : '';
            if ($title === '') {
                $msg = Text::_('Saved view has not been created. Please enter a title.');
            } elseif ($reason === 'schema') {
                $msg = Text::_('Saved view has not been created because the saved views database table is not ready. Install this update again, then retry.');
            } else {
                $msg = Text::_('Saved view has not been created. Please retry.');
            }
        }
        $this->setRedirect('index.php?option=com_jssupportticket&c=ticket&layout=tickets', $msg, $result ? 'message' : 'warning');
    }

    function applyticketsavedview() {
        Factory::getSession()->checkToken('post') or Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $input = Factory::getApplication()->input;
        $id = $input->getInt('saved_view_id', 0);
        $view = $this->getJSModel('featurefoundation')->getSavedViewForTicketList($id);
        if (!$view) {
            $this->setRedirect('index.php?option=com_jssupportticket&c=ticket&layout=tickets', Text::_('Saved view was not found'), 'warning');
            return;
        }
        $filters = json_decode((string)$view->filters, true);
        if (!is_array($filters)) {
            $filters = array();
        }
        $params = array(
            'option' => 'com_jssupportticket',
            'c' => 'ticket',
            'layout' => 'tickets',
            'jsresetbutton' => 1,
            'saved_view_id' => $id,
        );
        foreach ($filters as $key => $value) {
            if (is_scalar($value) && $value !== '') {
                $params[$key] = $value;
            }
        }
        if (!empty($view->sortby)) {
            $params['sortby'] = $view->sortby;
        }
        $query = array();
        foreach ($params as $key => $value) {
            $query[] = rawurlencode($key) . '=' . rawurlencode((string)$value);
        }
        $this->setRedirect('index.php?' . implode('&', $query), Text::_('Saved view has been applied'));
    }

    function addnewticket() {
        $layoutName = Factory::getApplication()->input->set('layout', 'formticket');
        $this->display();
    }

    function cancelticket() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'TICKET');
        $link = "index.php?option=com_jssupportticket&c=ticket&layout=tickets";
        $this->setRedirect($link, $msg);
    }

    function deleteattachment() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $jinput = Factory::getApplication()->input;
        $id = $jinput->get('id');
        $ticketid = $jinput->get('ticketid');

        $result = $this->getJSModel('attachments')->removeAttachment($id,$ticketid);
        if($result == true){
            $msg = Text::_("Attachment has been removed");
        }else{
            $msg = Text::_("Attachment has not been removed");
        }
        $link = "index.php?option=com_jssupportticket&c=ticket&task=addnewticket&cid[]=".$ticketid;
        $this->setRedirect($link, $msg);
    }

    function editresponce() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $id = Factory::getApplication()->input->get('id');
        $returnvalue = $this->getJSModel('ticket')->editResponceAJAX($id);
        echo $returnvalue;
        Factory::getApplication()->close();
    }

    function saveresponceajax() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        global $mainframe;
        $mainframe = Factory::getApplication();

        $id = Factory::getApplication()->input->get('id');
        //$responce = Factory::getApplication()->input->get('val', '', '', 'string', JREQUEST_ALLOWHTML);
        $responce = Factory::getApplication()->input->get('val', '', 'raw');
        $returnvalue = $this->getJSModel('ticket')->saveResponceAJAX($id, $responce);
        if ($returnvalue != 1)
            $returnvalue = Text::_('Mail has not been send');
        echo $responce;
        $mainframe->close();
    }

    function deleteresponceajax() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $id = Factory::getApplication()->input->get('id');
        $returnvalue = $this->getJSModel('ticket')->deleteResponceAJAX($id);
        if ($returnvalue == 1)
            $returnvalue = '<font color="green">' . Text::_('Mail has been deleted') . '</font>';
        else
            $returnvalue = '<font color="red">' . Text::_('Mail has not been deleted') . '</font>';
        echo $returnvalue;
        Factory::getApplication()->close();
    }

    function getpremadeforinternalnote() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        global $mainframe;
        $mainframe = Factory::getApplication();
        $val = Factory::getApplication()->input->get('val');
        $returnvalue = $this->getJSModel('premade')->getPremadeForInternalNote($val);
        $conf   = Factory::getConfig();
        $editor = Editor::getInstance($conf->get('editor'));
        echo $returnvalue;
        $mainframe->close();
    }

    function listhelptopicandpremade() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        global $mainframe;
        $mainframe = Factory::getApplication();
        $val = Factory::getApplication()->input->get('val');
        $returnvalue = $this->getJSModel('helptopic')->listHelpTopicAndPremade($val);
        echo json_encode($returnvalue);
        $mainframe->close();
    }

    function getdownloadbyid(){
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $id = Factory::getApplication()->input->get('id');
        $this->getJSModel('ticket')->getDownloadAttachmentById($id);
        Factory::getApplication()->close();
    }
    
    function downloadbyname(){
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $id = Factory::getApplication()->input->get('id');
        $name = Factory::getApplication()->input->get('name');
        $this->getJSModel('ticket')->getDownloadAttachmentByName( $name, $id );

        Factory::getApplication()->close();
    }

    function getReplyDataByID() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('ticket')->getReplyDataByID();
        echo $returnvalue;
        Factory::getApplication()->close();
    }


    function saveeditedreply() {
        Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
        $Itemid = Factory::getApplication()->input->get('Itemid');
        $data = Factory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('ticket')->editReply($data);
        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]='.$data['reply-tikcetid'];
        $msg = JSSupportTicketMessage::getMessage($result,'TICKET');
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = Factory::getDocument();
        $viewName = 'ticket';
        $layoutName = Factory::getApplication()->input->get('layout', 'tickets');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

    /**
     * AJAX endpoint for the "Select user" popup on the ticket form.
     *
     * Pro serves this from the staff controller. That controller is not part of
     * this edition, so the form's POST used to land on a controller that does
     * not exist and the popup never listed anybody.
     *
     * checkToken('get') matches how the form calls it: the token is appended to
     * the URL query string, not to the POST body.
     */
    function getusersearchajax() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

        echo $this->getJSModel('ticket')->getUserSearchAjax();

        Factory::getApplication()->close();
    }
}
?>
