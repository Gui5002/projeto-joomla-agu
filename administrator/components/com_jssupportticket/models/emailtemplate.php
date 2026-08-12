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

jimport('joomla.application.component.model');
jimport('joomla.html.html');
use Joomla\CMS\Factory;

class JSSupportticketModelEmailtemplate extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function storeEmailTemplate() {
        $row = $this->getTable('emailtemplates');

        $data = Factory::getApplication()->input->post->getArray();
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        $data['body'] = Factory::getApplication()->input->get('body', '', 'raw');

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->store()) {
           $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        return SAVED;
    }

    function getTemplate($tempfor) {
        $db = Factory::getDBO();
        switch ($tempfor) {
            case 'ew-tk' : $tempatefor = 'ticket-new';
                break;
            case 'sntk-tk' : $tempatefor = 'ticket-staff';
                break;
            case 'ew-md' : $tempatefor = 'department-new';
                break;
            case 'ew-sm' : $tempatefor = 'staff-new';
                break;
            case 'ew-ht' : $tempatefor = 'helptopic-new';
                break;
            case 'rs-tk' : $tempatefor = 'reassign-tk';
                break;
            case 'cl-tk' : $tempatefor = 'close-tk';
                break;
            case 'dl-tk' : $tempatefor = 'delete-tk';
                break;
            case 'lk-tk' : $tempatefor = 'lock-tk';
                break;
            case 'ulk-tk' : $tempatefor = 'unlock-tk';
                break;
            case 'minp-tk' : $tempatefor = 'markprgs-tk';
                break;
            case 'pc-tk' : $tempatefor = 'pchnge-tk';
                break;
            case 'ml-ew' : $tempatefor = 'mlnew-tk';
                break;
            case 'ml-rp' : $tempatefor = 'mlrep-tk';
                break;
            case 'mo-tk' : $tempatefor = 'moverdue-tk';
                break;
            case 'be-tk' : $tempatefor = 'banemail-tk';
                break;
            case 'be-trtk' : $tempatefor = 'banemail-trtk';
                break;
            case 'dt-tk' : $tempatefor = 'deptrans-tk';
                break;
            case 'ebct-tk' : $tempatefor = 'banemailcloseticket-tk';
                break;
            case 'ube-tk' : $tempatefor = 'unbanemail-tk';
                break;
            case 'rsp-tk' : $tempatefor = 'responce-tk';
                break;
            case 'rpy-tk' : $tempatefor = 'reply-tk';
                break;
            case 'tk-ew-ad' : $tempatefor = 'ticket-new-admin';
                break;
            case 'fd-bk' : $tempatefor = 'mail-feedback';
                break;
            case 'no-rp' : $tempatefor = 'mail-rpy-closed';
                break;
            case 'd-us-da' : $tempatefor = 'delete-user-data';
                break;
            case 'd-us-da-ad' : $tempatefor = 'delete-user-data-admin';
                break;
            case 'u-da-de' : $tempatefor = 'user-data-deleted';
                break;
        }
        $query = "SELECT * FROM `#__js_ticket_emailtemplates` WHERE templatefor = " . $db->Quote($tempatefor);
        $db->setQuery($query);
        $template = $db->loadObject();
        return $template;
    }

    function getEmailTemplate($title) {
        $db = $this->getDbo();
        $query = "SELECT id, title FROM `#__js_ticket_email_templates` WHERE status = 1 ORDER BY title ASC";
        
        try{
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            $emailtmpl = array();
            if ($title)
                $emailtmpl[] = array('value' => '', 'text' => $title);
            foreach ($rows as $row) {
                $emailtmpl[] = array('value' => $row->id, 'text' => $row->title);
            }
            return $emailtmpl;
        }
        catch (RuntimeException $e){
            echo $db->stderr();
            return false;
        }
    }
}
?>
