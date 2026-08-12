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

class JSSupportTicketControllerjssupportticket extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
	function logout() {
        $url = Factory::getApplication()->input->get('return', '');
        $url = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($url);
        Factory::getApplication()->logout(Factory::getUser()->id);
        if(empty($url)) $url = "index.php";
        Factory::getApplication()->redirect($url);
        }

	function display($cachable = false, $urlparams = false){
		$document =  Factory::getDocument();
		$viewName = Factory::getApplication()->input->post->get('view','jssupportticket');
		$layoutName = Factory::getApplication()->input->post->get('layout','controlpanel');
		$viewType = $document->getType();		
		$view = $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>

