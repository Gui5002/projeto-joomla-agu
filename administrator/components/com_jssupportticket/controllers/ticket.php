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
jimport('joomla.application.component.controller');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

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
                //$data['responce'] = Factory::getApplication()->input->get('responce', '', 'post', 'string', JREQUEST_ALLOWHTML);
                $data['responce']  = Factory::getApplication()->input->get('responce', '', 'raw');
                $result = $this->getJSModel('ticketreply')->storeTicketReplies($data['id'],$data['responce'], $data['created'], $data);
                $msg = JSSupportTicketMessage::getMessage($result,'REPLY');
                $link = 'index.php?option=com_jssupportticket&c=ticket&view=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                $this->setRedirect($link, $msg);
                break;
            case 'action':
                switch ($data['callaction']) {
                    case 1://change priority
                        $result = $this->getJSModel('ticket')->changeTicketPriority($data['id'], $data['priorityid'], $data['created']);
                        $msg = JSSupportTicketMessage::getMessage($result,'PRIORITY');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&view=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 3: //ticket close
                        $result = $this->getJSModel('ticket')->ticketClose($data['id'], $data['created']);
                        $msg = JSSupportTicketMessage::getMessage($result,'CLOSE');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&view=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 8: //reopened ticket
                        $result = $this->getJSModel('ticket')->reopenTicket($data['id'], $data['lastreply']);
                        $msg = JSSupportTicketMessage::getMessage($result,'REOPEN');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&view=ticket&layout=ticketdetails&cid[]=' . $data['id'];
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

    function addnewticket() {
        $layoutName = Factory::getApplication()->input->set('layout', 'formticket');
        $this->display();
    }

    function cancelticket() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'TICKET');
        $link = "index.php?option=com_jssupportticket&c=ticket&layout=tickets";
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = Factory::getDocument();
        $viewName = Factory::getApplication()->input->get('view', 'ticket');
        $layoutName = Factory::getApplication()->input->get('layout', 'tickets');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
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
    
    function deleteattachment() {
        Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $id = Factory::getApplication()->input->get('id');
        $ticketid = Factory::getApplication()->input->get('ticketid');
        
        $result = $this->getJSModel('attachments')->removeAttachment($id,$ticketid);
        if($result == true){
            $msg = Text::_("Attachment has been removed");
        }else{
            $msg = Text::_("Attachment has not been removed");
        }
        $link = "index.php?option=com_jssupportticket&c=ticket&task=addnewticket&cid[]=".$ticketid;
        $this->setRedirect($link, $msg);
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

}
?>
