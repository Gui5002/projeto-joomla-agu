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

defined ('_JEXEC') or die('Not Allowed');
jimport('joomla.application.component.controller');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

class JSSupportTicketControllerticket extends JSSupportTicketController{

	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}

	function saveTicket() {
		Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
		$Itemid =  Factory::getApplication()->input->get('Itemid');
		$data = Factory::getApplication()->input->post->getArray();
		if($data['id'] <> '')
			$id = $data['id'];
		$result = $this->getJSModel('ticket')->storeTicket($data);
		if($result == SAVED){
			$link = 'index.php?option=com_jssupportticket&c=ticket&layout=mytickets&id='.$id.'&Itemid='.$Itemid;
		}elseif($result == SAVE_ERROR || $result == MESSAGE_EMPTY || $result == FILE_EXTENTION_ERROR || $result == TICKET_DUPLICATE){
			Factory::getApplication()->setUserState('com_jssupportticket.data',$data);
			$link = 'index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid='.$Itemid;
		}
        $msg = JSSupportTicketMessage::getMessage($result,'TICKET');
        $this->setRedirect(Route::_($link , false), $msg);
    }

    function actionticket() {
    	Factory::getSession()->checkToken('post') or jexit(Text::_('JINVALID_TOKEN'));
		$data = Factory::getApplication()->input->post->getArray();
		$Itemid =  Factory::getApplication()->input->get('Itemid');
		$ticketid = $data['ticketid'];
		$action = $data['callfrom'];
		switch($action){
			case 'savemessage':
				//$message = Factory::getApplication()->input->get('responce', '', 'post', 'string', JREQUEST_ALLOWHTML);
				$message  = Factory::getApplication()->input->get('responce', '', 'raw');
				$data['message'] = $data['responce'];
				$result = $this->getJSModel('ticketreply')->storeTicketReplies($ticketid, $message, $data['created'], $data);
				$msg = JSSupportTicketMessage::getMessage($result,'REPLY');
				$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$ticketid.'&email='.$data['email'].'&Itemid='.$Itemid;
				$this->setRedirect(Route::_($link), $msg);
                break;
			case 'action':
				switch ($data['callaction']){
					case 3:
						$result = $this->getJSModel('ticket')->ticketClose($data['ticketid'],$data['created']);
						$msg = JSSupportTicketMessage::getMessage($result,'CLOSE');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['ticketid'].'&email='.$data['email'].'&Itemid'.$Itemid;
						$this->setRedirect(Route::_($link , false), $msg);
						break;
					case 8:
						$result = $this->getJSModel('ticket')->reopenTicket($data['ticketid'],$data['lastreply']);
						$msg = JSSupportTicketMessage::getMessage($result,'REOPEN');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['ticketid'].'&email='.$data['email'].'&Itemid'.$Itemid;
						$this->setRedirect(Route::_($link , false), $msg);
						break;
				}
			break;
		}
    }

	function saveresponceajax()  {
		Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = Factory::getApplication();
		$val = json_decode(Factory::getApplication()->input->get('val',"","string"),true);
		$id = $val[0];
		$responce = $val[1];
		$result = $this->getJSModel('ticket')->saveResponceAJAX($id,$responce);
		$msg = JSSupportTicketMessage::getMessage($result,'REPLY');
		if($result == SAVED){
			$result = 1;
		}else{
			$result = '<font color="red">'.$msg.'</font>';
		}
		echo $result;
		$mainframe->close();
	}

	function editresponce()  {
		Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = Factory::getApplication();
		$id = Factory::getApplication()->input->get('id');
		$result = $this->getJSModel('ticket')->editResponceAJAX($id);
		echo $result;
		$mainframe->close();
	}

	function deleteresponceajax() {
		Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = Factory::getApplication();
		$id = Factory::getApplication()->input->get('id');
		$result = $this->getJSModel('ticket')->deleteResponceAJAX($id);
		$msg = JSSupportTicketMessage::getMessage($result,'REPLY');
		if ($result == DELETED){
			$result = '<font color="green">'.$msg.'</font>';
		}elseif($result == PERMISSION_ERROR){
			$result = '<font color="red">'.$msg.'</font>';
		}else{
			$result = '<font color="red">'.$msg.'</font>';
		}
		echo $result;
		$mainframe->close();
	}

	function getmytickets(){
		$data = Factory::getApplication()->input->post->getArray();
		$Itemid =  Factory::getApplication()->input->get('Itemid');
		$email = $data['email'];
		$ticketid = $data['ticketid'];
		$model = $this->getJSModel('ticket');
		$result = $model->checkEmailAndTicketID($email,$ticketid);
	}

	function getdownloadbyid(){
		Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
		$id = Factory::getApplication()->input->get('id');
		$this->getJSModel('ticket')->getDownloadAttachmentById($id);
		Factory::getApplication()->close();
	}

    function downloadbyname(){

        $id = Factory::getApplication()->input->get('id');
        $name = Factory::getApplication()->input->get('name');
        $this->getJSModel('ticket')->getDownloadAttachmentByName( $name, $id );

        Factory::getApplication()->close();
    }

    function datafordepandantfield() {
    	Factory::getSession()->checkToken( 'get' ) or die( 'Invalid Token' );
        //Factory::getSession()->checkToken() or die( 'Invalid Token' );
        $val = Factory::getApplication()->input->post->get('fvalue','','STRING');
        
        $childfield = Factory::getApplication()->input->get('child');
        $result = $this->getJSModel('userfields')->dataForDepandantField( $val , $childfield);
        $result = json_encode($result);
        echo $result;
        Factory::getApplication()->close();
    }

	function display($cachable = false, $urlparams = false){
		$document = Factory::getDocument();
		$viewName = Factory::getApplication()->input->post->get('view','ticket');
		$layoutName = Factory::getApplication()->input->get('layout','mytickets');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
