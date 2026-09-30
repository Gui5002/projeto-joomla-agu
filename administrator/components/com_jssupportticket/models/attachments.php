<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');
use Joomla\CMS\Factory;

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelattachments extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getAttachmentForForm($id) {
        if (!is_numeric($id))
            return false;
        $db = Factory::getDbo();
        $query = "SELECT filename,filesize,id
                    FROM `#__js_ticket_attachments`
                    WHERE ticketid = " . $id . " and replyattachmentid = 0";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getAttachmentForReply($id, $replyattachmentid) {
        if (!is_numeric($id))
            return false;
        if (!is_numeric($replyattachmentid))
            return false;
        $db = Factory::getDbo();
        $query = "SELECT filename,filesize,id AS attachmentid,id 
                    FROM `#__js_ticket_attachments`
                    WHERE ticketid = " . $id . " AND replyattachmentid = " . $replyattachmentid;
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }
    
    function uploadAttchments($i, $id, $action, $isdeletefile, $filefor){
        if (is_numeric($id) == false) return false;
        $isupload = false;
        $config = $this->getJSModel('config')->getConfigs();
        $datadirectory = $config['data_directory'];
        $base = JPATH_BASE;
        if(Factory::getApplication()->isClient('administrator')){
            $base = getJSTicketPHPFunctionsClass()->jsticket_substr($base, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($base) - 14); //remove administrator
        }  
        $path = $base.'/'.$datadirectory;
        if (!file_exists($path)){ // create user directory
            $this->makeDir($path);
        }
        $path = $path . '/attachmentdata';
        if (!file_exists($path)){ // create user directory
            $this->makeDir($path);
        }
        $path = $path . '/'.$filefor;
        if (!file_exists($path)){ // create user directory
            $this->makeDir($path);
        }
        if ($action == true) {
            if($_FILES['filename']['size'][$i] > 0){
                $file_name = getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                $file_tmp = $_FILES['filename']['tmp_name'][$i]; // actual location
                if($filefor == 'ticket'){
                    $db = Factory::getDbo();
                    $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = ".$id;
                    $db->setQuery($query);
                    $foldername = $db->loadResult();
                }else{
                    $foldername = $filefor.'_'.$id;
                }
                $userpath = $path . '/'.$foldername;
                if (!file_exists($userpath)) { // create user directory
                    $this->makeDir($userpath);
                }
                $isupload = true;
            }
        }
        if ($isupload == true && $isdeletefile == false){
            move_uploaded_file($file_tmp, $userpath.'/' . $file_name);
            return true;
        }
        if ($isdeletefile == true){
            $userpath= $path .'/'.$filefor.'_'.$id;
            $files = glob($userpath.'/*.*');
            array_map('unlink', $files); // delete all file in the direcoty
            return true;
        }
        return false;
    }
    function uploadFile($id, $action, $delete_old, $filefor){
        if (is_numeric($id) == false) return false;
        $isupload = false;
        $config = $this->getJSModel('config')->getConfigs();
        $datadirectory = $config['data_directory'];
        $base = JPATH_BASE;
        if(Factory::getApplication()->isClient('administrator')){
            $base = getJSTicketPHPFunctionsClass()->jsticket_substr($base, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($base) - 14); //remove administrator
        }  
        $path = $base.'/'.$datadirectory;
        if (!file_exists($path)){ // create user directory
            $this->makeDir($path);
        }
        $path = $path . '/attachmentdata';
        if (!file_exists($path)){ // create user directory
            $this->makeDir($path);
        }
        $path = $path . '/'.$filefor;
        if (!file_exists($path)){ // create user directory
            $this->makeDir($path);
        }
        if ($action == true) {
            if($_FILES['filename']['size'] > 0){
                $file_name = getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ', '_', $_FILES['filename']['name']);
                $file_tmp = $_FILES['filename']['tmp_name']; // actual location
                $userpath = $path . '/'.$filefor.'_' . $id;
                if (!file_exists($userpath)) { // create user directory
                    $this->makeDir($userpath);
                }
                $isupload = true;
            }
        }
        if ($isupload == true && $delete_old == false){
            if($filefor == 'category'){
                require_once JPATH_COMPONENT_ADMINISTRATOR . '/include/lib/class.upload.php';
                $handle = new upload($_FILES['filename']);
                if ($handle->uploaded) {
                    $handle->file_new_name_body = 'catlogo_' . $id;
                    $handle->image_resize = true;
                    $handle->image_x = 77;
                    $handle->image_y = 77;
                    $handle->image_ratio_fill = true;
                    $handle->process($userpath);
                    if ($handle->processed) {
                        $handle->clean();
                        $result = $handle->file_dst_name;
                    } else {
                        $result = false;
                    }
                    if($result != false){
                        $db = Factory::getDbo();
                        $db->setQuery("UPDATE `#__js_ticket_categories` SET logo = '" . $result . "' WHERE id = $id");
                        $db->execute();
                    }
                }
                if($result != false) return true; else return false;
            }else{
                move_uploaded_file($file_tmp, $userpath.'/' . $file_name);
                return true;
            }
            
        }else if($isupload == true && $delete_old == true){
            $userpath = $path .'/'.$filefor.'_'.$id;
            $files = glob($userpath.'/*.*');
            array_map('unlink', $files); // delete all file in the direcoty
            if($filefor == 'category'){
                require_once JPATH_COMPONENT_ADMINISTRATOR . '/include/lib/class.upload.php';
                $handle = new upload($_FILES['filename']);
                if ($handle->uploaded) {
                    $handle->file_new_name_body = 'catlogo_' . $id;
                    $handle->image_resize = true;
                    $handle->image_x = 77;
                    $handle->image_y = 77;
                    $handle->image_ratio_fill = true;
                    $handle->process($userpath);
                    if ($handle->processed) {
                        $handle->clean();
                        $result = $handle->file_dst_name;
                    } else {
                        $result = false;
                    }
                    if($result != false){
                        $db = Factory::getDbo();
                        $db->setQuery("UPDATE `#__js_ticket_categories` SET logo = '" . $result . "' WHERE id = $id");
                        $db->execute();
                    }
                }
                if($result != false) return true; else return false;
            }else{
                move_uploaded_file($file_tmp, $userpath.'/' . $file_name);
                return true;
            }
            return true;
        }
        return false;
    }

    function makeDir($path) {
        if (!file_exists($path)) { // create directory
            mkdir($path, 0755);
            $ourFileName = $path . '/index.html';
            $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
            fclose($ourFileHandle);
        }
    }

    function checkExtension($filename) {
        $i = strrpos($filename, ".");
        if (!$i) {
            return 'N';
        }
        $l = getJSTicketPHPFunctionsClass()->jsticket_strlen($filename) - $i;
        $ext = getJSTicketPHPFunctionsClass()->jsticket_substr($filename, $i + 1, $l);
        $config = $this->getJSModel('config')->getConfigByFor('default');
        $extensions = getJSTicketPHPFunctionsClass()->jsticket_explode(",", $config['fileextension']);
        $match = 'N';

        if( getJSTicketPHPFunctionsClass()->jsticket_strtolower($ext) == 'php' )
            return $match;
        
        foreach ($extensions as $extension) {
            if (strtolower($extension) == getJSTicketPHPFunctionsClass()->jsticket_strtolower($ext)) {
                $match = 'Y';
                break;
            }
        }
        return $match;
    }

    function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = getJSTicketPHPFunctionsClass()->jsticket_strlen($str) - $i;
        $ext = getJSTicketPHPFunctionsClass()->jsticket_substr($str, $i + 1, $l);
        return getJSTicketPHPFunctionsClass()->jsticket_strtolower($ext);
    }

    function removeAttachment($id,$ticketid) {
        if (!is_numeric($id))
            return false;
        if(!is_numeric($ticketid)) return false;
        $db = Factory::getDbo();
        $query = "SELECT filename FROM `#__js_ticket_attachments` WHERE id = " . $id;
        $db->setQuery($query);
        $filename = $db->loadResult();
        
        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = $ticketid";
        $db->setQuery($query);
        $dir = $db->loadResult();

        $query = "DELETE FROM `#__js_ticket_attachments` WHERE id = $id";
        $db->setQuery($query);
        if ($db->execute()) {
            $config = $this->getJSModel('config')->getConfigByFor('default');
            $base = JPATH_BASE;
            if(Factory::getApplication()->isClient('administrator')){
                $base = getJSTicketPHPFunctionsClass()->jsticket_substr($base, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($base) - 14); //remove administrator
            }  
            $path = $base.'/'.$config['data_directory'];
            $path = $path . '/attachmentdata';
            $path = $path . '/ticket/' . $dir . '/' . $filename;
            unlink($path);
            return true;
        }
        return false;
    }
    
    function removeTicketAttachments($dir) {
        if(empty($dir))
            return;

        $config = $this->getJSModel('config')->getConfigByFor('default');
        $base = JPATH_BASE;
        if(Factory::getApplication()->isClient('administrator')){
            $base = getJSTicketPHPFunctionsClass()->jsticket_substr($base, 0, getJSTicketPHPFunctionsClass()->jsticket_strlen($base) - 14); //remove administrator
        }  
        $path = $base.'/'.$config['data_directory'];
        $path = $path . '/attachmentdata';
        $path = $path . '/ticket/'.$dir;
        $files = glob($path.'/*.*');
        array_map('unlink', $files); // delete all file in the direcoty
        rmdir($path);
    }

}?>
