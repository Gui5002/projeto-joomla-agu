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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;	

jimport('joomla.application.component.controller');

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
		$user = JSSupportTicketCurrentUser::getInstance();
		$link = 'index.php?option=com_jssupportticket&c=ticket&layout=mytickets&id='.$id.'&Itemid='.$Itemid;

		if($result == SAVE_ERROR || $result == MESSAGE_EMPTY || $result == TICKET_DUPLICATE){
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
		$model = $this->getJSModel('ticket');
		$action = isset($data['callfrom']) ? $data['callfrom'] : '';
		if ($action === 'action' && isset($data['callaction']) && (int) $data['callaction'] === 1 && !$this->jsstStaffCanUseTicketFeature(isset($data['id']) ? (int) $data['id'] : 0)) {
			$this->jsstTicketPermissionDeniedRedirect(isset($data['id']) ? (int) $data['id'] : 0, $Itemid);
			return;
		}
		switch($action){
			case 'savemessage':
				// The reply editor posts its content as "responce", but
				// storeUserReplies() reads the body from "message". Map it here
				// rather than renaming the editor: "message" is a common element
				// id on a Joomla page and collides with other modules.
				$input = Factory::getApplication()->input;
				if ($input->get('message', '', 'raw') === '') {
					$input->set('message', $input->get('responce', '', 'raw'));
				}
				$result = $model->storeUserReplies();
				$msg = JSSupportTicketMessage::getMessage($result,'MESSAGE');
				// The detail view reads "id" with getInt(), so it needs the numeric row id.
				// $ticketid here is the public tracking id (a random string) and would
				// resolve to 0, landing on "record not found" after a successful save.
				$rowid = isset($data['id']) ? (int) $data['id'] : 0;
				$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$rowid.'&email='.$data['email'].'&Itemid='.$Itemid;
				$this->setRedirect(Route::_($link , false), $msg);
                break;
			case 'action':
				/*
				 * The form posts BOTH identifiers: "id" is the numeric row id and
				 * "ticketid" is the public tracking string (e.g. Md4jtbQ8HCczw).
				 *
				 * Every model below guards with is_numeric() and every redirect is
				 * read back with getInt(), so both need the numeric one. Passing the
				 * tracking string made ticketClose() and reopenTicket() return false
				 * immediately - the buttons did nothing at all, silently.
				 */
				$rowid = (isset($data['id']) && is_numeric($data['id']) && (int) $data['id'] > 0)
					? (int) $data['id']
					: (int) $this->getJSModel('ticket')->getIdFromTrackingId(isset($data['ticketid']) ? $data['ticketid'] : '');

				switch ($data['callaction']){
					case 1://change priority
						$result = $model->changeTicketPriority($rowid,$data['priorityid'],$data['created']);
						$msg = JSSupportTicketMessage::getMessage($result,'PRIORITY');
						// Redirect back to this edition's ticket detail. The old target was the
						// staff controller, which does not exist here and would have thrown
						// "Unknown Controller" had this branch ever been reached.
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$rowid.'&Itemid='.$Itemid;
						$this->setRedirect(Route::_($link , false), $msg);
						break;
					case 3:
						$result = $model->ticketClose($rowid,$data['created']);
						$msg = JSSupportTicketMessage::getMessage($result,'CLOSE');
						// Note the '&Itemid=' - this read '&Itemid' with no equals sign,
						// producing "&Itemid5", so the menu item was lost on the way back.
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$rowid.'&email='.$data['email'].'&Itemid='.$Itemid;
						$this->setRedirect(Route::_($link , false), $msg);
						break;
					case 8:
						$result = $model->reopenTicket($rowid,$data['lastreply']);
						$msg = JSSupportTicketMessage::getMessage($result,'REOPEN');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$rowid.'&email='.$data['email'].'&Itemid='.$Itemid;
						$this->setRedirect(Route::_($link , false), $msg);
						break;
				}
			break;
		}
	}

	function getpremadeforinternalnote(){
		Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = Factory::getApplication();
		$val = Factory::getApplication()->input->get( 'val');
		$model = $this->getJSModel('premade') ;
		$returnvalue = $model->getPremadeForInternalNote($val);
		echo $returnvalue;
		$mainframe->close();
	}

	function listhelptopicandpremade(){
		Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = Factory::getApplication();
		$val=Factory::getApplication()->input->get( 'val');
		$model = $this->getJSModel('helptopic');
		$returnvalue = $model->listHelpTopicAndPremade($val);
		echo json_encode($returnvalue);
		$mainframe->close();
	}

	function saveresponceajax()  {
		Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = Factory::getApplication();
		$val = json_decode(Factory::getApplication()->input->get('val',"","string"),true);
		$id = $val[0];
		$responce = $val[1];
		$result = $this->getJSModel('ticket')->saveResponceAJAX($id,$responce);
		$msg = JSSupportTicketMessage::getMessage($result,'MESSAGE');
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

    function downloadbyname(){
    	Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        $id = Factory::getApplication()->input->get('id');
        $name = Factory::getApplication()->input->getString('name');
        $this->getJSModel('ticket')->getDownloadAttachmentByName( $name, $id );

        Factory::getApplication()->close();
    }


	function deleteresponceajax() {
		Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = Factory::getApplication();
		$id = Factory::getApplication()->input->get('id');
		$result = $this->getJSModel('ticket')->deleteResponceAJAX($id);
		$msg = JSSupportTicketMessage::getMessage($result,'MESSAGE');
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

	function listhelptopic(){
		Factory::getApplication();
		$val=Factory::getApplication()->input->get( 'val');
		$returnvalue = $this->getJSModel('helptopic')->listHelpTopic($val);
		echo json_encode($returnvalue);
		Factory::getApplication()->close();
	}

	function getdownloadbyid(){
		Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
		$id = Factory::getApplication()->input->get('id');
		$this->getJSModel('ticket')->getDownloadAttachmentById($id);
		Factory::getApplication()->close();
	}
	function downloadall(){
    	Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
    	$this->getJSModel('ticket')->getAllDownloadFiles();
    	Factory::getApplication()->close();
    }
    function downloadallforreply(){
    	Factory::getSession()->checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
    	$this->getJSModel('ticket')->getAllReplyDownloadsFiles();
    	Factory::getApplication()->close();
    }

    function datafordepandantfield() {
    	Factory::getSession()->checkToken( 'get' ) or die( 'Invalid Token' );
        $val = Factory::getApplication()->input->get('fvalue','','string');
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
