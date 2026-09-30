<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , ahmad@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
    

$db = Factory::getDbo();
$query = "SHOW TABLES LIKE '".$db->getPrefix()."js_ticket_userfields'";
$db->setQuery($query);
$table = $db->loadResult();
if($table){    

    $query = "SELECT * FROM `#__js_ticket_userfields`";
    $db->setQuery($query);
    $uf = $db->loadObjectList();
    $userfields = array();
    foreach($uf AS $f){
        $query = "SELECT * FROM `#__js_ticket_userfieldvalues` WHERE field = ".$f->id;
        $db->setQuery($query);
        $fv = $db->loadObjectList();
        $field = array('field' => $f, 'fieldvalues' => $fv);
        $userfields[] = $field;
    }
    $config = JSSupportTicketModel::getJSModel('config')->getConfigurationByName('last_step_updater');
    if($config < '1151'){
        // Insert into the fieldordering
        $query = "SELECT MAX(ordering) FROM `#__js_ticket_fieldsordering`";
        $db->setQuery($query);
        $ordering = $db->loadResult();
        $ordering = $ordering + 1;
        foreach($userfields AS $field){
            $f = $field['field'];
            $fv = $field['fieldvalues'];
            $params = '';
            if($f->type == 'select'){
                $p = array();
                foreach($fv AS $pv){
                    if(!empty($pv->fieldtitle)){
                        $p[] = $pv->fieldtitle;
                    }
                }
                if(!empty($p)){
                    $params = json_encode($p);                        
                }
            }
            if($f->type == 'select'){
                $f->type = 'combo';
            }
            $query = "INSERT INTO `#__js_ticket_fieldsordering`
                        (field,fieldtitle,fieldfor,section,ordering,published,isvisitorpublished,search_visitor,isuserfield,userfieldtype,userfieldparams,sys,cannotunpublish) VALUES
                        ('js_".getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ','',$f->name)."','".$f->title."',1,10,".$ordering.",".$f->published.",".$f->published.",0,1,'".$f->type."','".$params."',0,0)";
            $db->setQuery($query);
            $db->execute();
            $ordering = $ordering + 1;
            // Deleting extra records from fieldsordering
            $query = "DELETE FROM `#__js_ticket_fieldsordering` WHERE field = '".$f->id."'";
            $db->setQuery($query);
            $db->execute();
        }
        $query = "UPDATE `#__js_ticket_config` SET configvalue = '1151' WHERE configname = 'last_step_updater'";
        $db->setQuery($query);
        $db->execute();
    }
    $query = "SELECT referenceid AS id FROM `#__js_ticket_userfield_data` GROUP BY referenceid";
    $db->setQuery($query);
    $tickets = $db->loadObjectList();
    if($tickets){
        foreach($tickets AS $t){
            $p = array();
            $query = "SELECT * FROM `#__js_ticket_userfield_data` WHERE referenceid = ".$t->id;
            $db->setQuery($query);
            $ufv = $db->loadObjectList();
            if($ufv){
                foreach($ufv AS $tufv){
                    foreach($userfields AS $uf){
                        $f = $uf['field'];
                        if($tufv->field == $f->id){
                            $v = $uf['fieldvalues'];
                            $ft = "js_".getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ', '', $f->name);
                            $fv = '';
                            if($f->type == 'combo' OR $f->type == 'select'){
                                foreach($v AS $vfv){
                                    if($vfv->id == $tufv->data){
                                        $fv = $vfv->fieldtitle;
                                        break;
                                    }
                                }
                            }else{
                                $fv = $tufv->data;
                            }
                            $p[$ft] = $fv;
                        }
                    }
                }
                if(!empty($p)){
                    $params = json_encode($p);
                    $params = mysql_real_escape_string($params);
                    $query = "UPDATE `#__js_ticket_tickets` SET params = '".$params."' WHERE id = ".$t->id;
                    $db->setQuery($query);
                    $db->execute();
                }
                $query = "DELETE FROM `#__js_ticket_userfield_data` WHERE referenceid = ".$t->id;
                $db->setQuery($query);
                $db->execute();
            }
        }
        
    }
    $query = "DROP TABLE IF EXISTS `#__js_ticket_userfields`";
    $db->setQuery($query);
    $db->execute();
    $query = "DROP TABLE IF EXISTS `#__js_ticket_userfieldvalues`";
    $db->setQuery($query);
    $db->execute();
    $query = "DROP TABLE IF EXISTS `#__js_ticket_userfield_data`";
    $db->setQuery($query);
    $db->execute();
    $query = "UPDATE `#__js_ticket_config` SET configvalue = '105' WHERE configname = 'last_version'";
    $db->setQuery($query);
    $db->execute();
    $query = "UPDATE `#__js_ticket_config` SET configvalue = '1152' WHERE configname = 'last_step_updater'";
    $db->setQuery($query);
    $db->execute();
}        
?>
