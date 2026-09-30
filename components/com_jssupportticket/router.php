<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
 ^
 + Project:     JS Tickets
 ^
*/
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Factory;


/**
 * Routing class of com_phocagallery
 *
 * @since  3.3
 */


class JSSupportTicketRouter extends RouterView
{
    protected $noIDs = false;

    /**
     * Content Component router constructor
     *
     * @param   JApplicationCms  $app   The application object
     * @param   JMenu            $menu  The menu object to work with
     */
    public function __construct($app = null, $menu = null)
    {

        $params = ComponentHelper::getParams('com_jsjobs');

        parent::__construct($app, $menu);

    }



    public function build(&$query){
        $segments = array();
        $segments = $this->JSSupportTicketBuildRoute($query);
        return $segments;
    }

    public function parse(&$segments){
        $vars = array();
        $vars = $this->JSSupportTicketParseRoute($segments);

        //print_r($vars);
        //die();
        $segments = [];
        return $vars;
    }

    public function preprocess($query){
        return $query;
    }

    function JSSupportTicketBuildRoute( &$query ){
        $segments = array();
        $router = new JSSupportTicketOldRouter;

        if(isset( $query['layout'] )) {
            if(isset($query['c'])){
                $controller = $query['c'];
                unset($query['c']);
            }else{
                $controller = '';
            }
            $segments[] = $router->buildLayout($query['layout'],$controller); unset($query['layout']);
        };
        if(isset( $query['task'] )) {
            $task = 'tk-'.$query['task']; unset($query['task']);
            if(isset($query['c'])){
                if($query['c'] != 'ticket')
                    $task .= '-'.$query['c'];
                unset($query['c']);
            }
            $segments[] = $task;
            if(isset($query['name'])){
                $segments[] = 'file-'.$query['name'];
                unset($query['name']);
            }
        };
        if(isset( $query['id'] )) {
            switch($segments[0]){ //layout name
                case 'faq':$segments[] = $router->getFaqTitleById($query['id']).'-'.$query['id'];break;
                case 'announcement':$segments[] = $router->getAnnouncementTitleById($query['id']).'-'.$query['id'];break;
                case 'article':$segments[] = $router->getArticleTitleById($query['id']).'-'.$query['id'];break;
                case 'mytickets':
                case 'myticketsstaff':
                    $segments[] = $query['id'];
                break;
                default:$segments[] = 'id-'.$query['id'];break;
                /*
                case 'faq':$segments[] = 'faq-'.$router->getFaqTitleById($query['id']).'-'.$query['id'];break;
                case 'announcement':$segments[] = 'announcement-'.$router->getAnnouncementTitleById($query['id']).'-'.$query['id'];break;
                case 'article':$segments[] = 'article-'.$router->getArticleTitleById($query['id']).'-'.$query['id'];break;
                case 'mytickets':
                case 'myticketsstaff':
                    $segments[] = 'ticketid-'.$query['id'];
                break;
                default:$segments[] = 'id-'.$query['id'];break;
                */
            }
            unset($query['id']);
        };
        if(isset( $query['email'] )) { $segments[] = 'email-'.$query['email']; unset($query['email']);};
        if(isset( $query['date_start'] )) { $segments[] = 'date_start-'.$query['date_start']; unset($query['date_start']);};
        if(isset( $query['date_end'] )) { $segments[] = 'date_end-'.$query['date_end']; unset($query['date_end']);};
        if(isset( $query['lt'] )) { $segments[] = 'listing-'.$router->buildListingFor($query['lt']); unset($query['lt']);};
        //for sorting
        if(isset( $query['sort'] )) { $segments[] = 'sort-'.$query['sort']; unset($query['sort']);};
        if(isset( $query['sortby'] )) { $segments[] = 'sortby-'.$query['sortby']; unset($query['sortby']);};
        // printticket
        if(isset( $query['tmpl'] )) { $segments[] = 'tmpl-'.$query['tmpl']; unset($query['tmpl']);};
        if(isset( $query['print'] )) { $segments[] = 'print-'.$query['print']; unset($query['print']);};

        //  echo '<br> item '.$query['Itemid'];
        if(isset( $query['Itemid'] )) {
            $_SESSION['JSItemid'] = $query['Itemid'];
        };

        return $segments;
    }

    function JSSupportTicketParseRoute( $segments ){
        $value = "";
        $vars = array();
        $count = count($segments);
        $router = new JSSupportTicketOldRouter;
        //echo '<br> count '.$count;
        //print_r($segments);

        $site= Factory::getApplication()->getMenu();
        $item   = $site->getActive();
        $firstSegment = isset($segments[0]) ? (string) $segments[0] : '';
        if($firstSegment !== '' && strpos($firstSegment, 'tk-') === 0){
            $result = $router->parseTask($firstSegment);
            if (is_array($result) && isset($result['controller'], $result['task'])) {
                $vars['c'] = $result['controller'];
                $vars['task'] = $result['task'];
            } else {
                $fallback = $router->parseTaskFallback($firstSegment);
                $vars['c'] = $fallback['controller'];
                $vars['task'] = $fallback['task'];
            }
        }else{
            $result = $router->parseLayout($firstSegment);
            if (is_array($result) && isset($result['controller'], $result['layout'])) {
                $vars['c'] = $result['controller'];
                $vars['layout'] = $result['layout'];
            }

        }

          //echo '<br> layout '.$segments[0];print_r($segments);
        $i = 0;
        foreach ($segments AS $seg) {
            if ($i >= 1) {
                //$array = explode(":", $seg);
                $array = explode("-", $seg);
                $index = $array[0];
                //unset the current index
                unset($array[0]);
                if (isset($array[1])) $value = implode("-", $array);

                switch ($index) {
                    case "task": $vars['tk'] = $value; break;
                    /*
                    case "ticketid":
                    case "id":
                    case "faq":
                    case "announcement":
                    case "article": $vars['id'] = $router->parseId($value); break;
                    */
                    default: $vars['id'] = $router->parseId($value); break;
                    case "email": $vars['email'] = $value; break;
                    case "date_start": $vars['date_start'] = $value; break;
                    case "date_end": $vars['date_end'] = $value; break;
                    case "listing": $vars['lt'] = $router->parseListingFor($value); break;
                    case "sort": $vars['sort'] = $value; break;
                    case "sortby": $vars['sortby'] = $value; break;
                    case "tmpl": $vars['tmpl'] = $value; break;
                    case "print": $vars['print'] = $value; break;
                    case "file": $vars['name'] = $value; break;
                }
            }
            $i++;
        }
        if(isset( $_SESSION['JSItemid'] )) {
            $vars['Itemid'] = $_SESSION['JSItemid'];
        }

        return $vars;

    }



}





class JSSupportTicketOldRouter {

    function buildLayout($layout, $controller) {
        $returnvalue = "";
        //echo '<br> layout ='.$layout;
        //echo '<br> controller ='.$controller;
        switch ($layout) {
            case "announcements":$returnvalue = "staffannouncements";break;
            case "formannouncement":$returnvalue = "addannouncement";break;
            case "userannouncementdetail":$returnvalue = "announcement";break;
            case "userannouncements":$returnvalue = "announcements";break;
            case "departments":$returnvalue = "staffdepartments";break;
            case "formdepartment":$returnvalue = "adddepartment";break;
            case "downloads":$returnvalue = "staffdownloads";break;
            case "formdownload":$returnvalue = "adddownload";break;
            case "userdownloads":$returnvalue = "downloads";break;
            case "faqs":$returnvalue = "stafffaqs";break;
            case "formfaq":$returnvalue = "addfaq";break;
            case "userfaqs":$returnvalue = "faqs";break;
            case "userfaqdetail":$returnvalue = "faq";break;
            case "controlpanel":$returnvalue = "controlpanel";break;
            case "articles":$returnvalue = "staffarticles";break;
            case "categories":$returnvalue = "staffcategories";break;
            case "formarticle":$returnvalue = "addarticle";break;
            case "formcategory":$returnvalue = "addcategory";break;
            case "userarticles":$returnvalue = "articles";break;
            case "userarticlesdetails":$returnvalue = "article";break;
            case "usercatarticledetails":$returnvalue = "article";break;
            case "usercatarticles":$returnvalue = "categories";break;
            case "formmessage":$returnvalue = "sendmessage";break;
            case "inbox":$returnvalue = "inbox";break;
            case "message":$returnvalue = "message";break;
            case "outbox":$returnvalue = "outbox";break;
            case "rolepermissions":$returnvalue = "rolepermissions";break;
            case "formrole":$returnvalue = "addrole";break;
            case "roles":$returnvalue = "roles";break;
            case "formstaff":$returnvalue = "addstaff";break;
            case "staff":$returnvalue = "staffs";break;
            case "staffprofile":$returnvalue = "profile";break;
            case "users":$returnvalue = "users";break;
            case "formticket": $returnvalue = "addticket"; break;
            case "mytickets": $returnvalue = "mytickets"; break;
            case "myticketsstaff": $returnvalue = "staffmytickets"; break;
            case "ticketstatus": $returnvalue = "status"; break;
            case "ticketdetail": $returnvalue = "viewticket"; break;
            case "print_ticket": $returnvalue = "printticket"; break;
            case "userpermissions": $returnvalue = "userpermissions"; break;
            case "staffreports": $returnvalue = "staffreports"; break;
            case "staffdetailreport": $returnvalue = "staffdetailreport"; break;
            case "departmentreports": $returnvalue = "departmentreports"; break;
            case "feedbacks": $returnvalue = "feedbacks"; break;
            case "formfeedback": $returnvalue = "addfeedback"; break;
            case "visitorsuccessmessage": $returnvalue = "visitorsuccessmessage"; break;
            case "adderasedatarequest": $returnvalue = "datacomplianceactions"; break;
        }
        return $returnvalue;
    }

    function parseLayout($value) {
        //  $returnvalue = "";
        switch ($value) {
            case "staffannouncements":$returnvalue["layout"] = "announcements"; $returnvalue["controller"] = "announcements"; break;
            case "addannouncement":$returnvalue["layout"] = "formannouncement";$returnvalue["controller"]  = "announcements"; break;
            case "announcement":$returnvalue["layout"] = "userannouncementdetail";$returnvalue["controller"]  = "announcements"; break;
            case "announcements":$returnvalue["layout"] = "userannouncements";$returnvalue["controller"]  = "announcements"; break;
            case "staffdepartments":$returnvalue["layout"] = "departments";$returnvalue["controller"]  = "department"; break;
            case "adddepartment":$returnvalue["layout"] = "formdepartment";$returnvalue["controller"]  = "department"; break;
            case "staffdownloads":$returnvalue["layout"] = "downloads";$returnvalue["controller"]  = "downloads"; break;
            case "adddownload":$returnvalue["layout"] = "formdownload";$returnvalue["controller"]  = "downloads"; break;
            case "downloads":$returnvalue["layout"] = "userdownloads";$returnvalue["controller"]  = "downloads"; break;
            case "stafffaqs":$returnvalue["layout"] = "faqs";$returnvalue["controller"]  = "faqs"; break;
            case "addfaq":$returnvalue["layout"] = "formfaq";$returnvalue["controller"]  = "faqs"; break;
            case "faqs":$returnvalue["layout"] = "userfaqs";$returnvalue["controller"]  = "faqs"; break;
            case "faq":$returnvalue["layout"] = "userfaqdetail";$returnvalue["controller"]  = "faqs"; break;
            case "controlpanel":$returnvalue["layout"] = "controlpanel";$returnvalue["controller"]  = "jssupportticket"; break;
            case "staffarticles":$returnvalue["layout"] = "articles";$returnvalue["controller"]  = "knowledgebase"; break;
            case "staffcategories":$returnvalue["layout"] = "categories";$returnvalue["controller"]  = "knowledgebase"; break;
            case "addarticle":$returnvalue["layout"] = "formarticle";$returnvalue["controller"]  = "knowledgebase"; break;
            case "addcategory":$returnvalue["layout"] = "formcategory";$returnvalue["controller"]  = "knowledgebase"; break;
            case "articles":$returnvalue["layout"] = "userarticles";$returnvalue["controller"]  = "knowledgebase"; break;
            case "article":$returnvalue["layout"] = "usercatarticledetails";$returnvalue["controller"]  = "knowledgebase"; break;
            case "categories":$returnvalue["layout"] = "usercatarticles";$returnvalue["controller"]  = "knowledgebase"; break;
            case "sendmessage":$returnvalue["layout"] = "formmessage";$returnvalue["controller"]  = "mail"; break;
            case "inbox":$returnvalue["layout"] = "inbox";$returnvalue["controller"]  = "mail"; break;
            case "message":$returnvalue["layout"] = "message";$returnvalue["controller"]  = "mail"; break;
            case "outbox":$returnvalue["layout"] = "outbox";$returnvalue["controller"]  = "mail"; break;
            case "rolepermissions":$returnvalue["layout"] = "rolepermissions";$returnvalue["controller"]  = "rolepermissions"; break;
            case "addrole":$returnvalue["layout"] = "formrole";$returnvalue["controller"]  = "roles"; break;
            case "roles":$returnvalue["layout"] = "roles";$returnvalue["controller"]  = "roles"; break;
            case "addstaff":$returnvalue["layout"] = "formstaff";$returnvalue["controller"]  = "staff"; break;
            case "staffs":$returnvalue["layout"] = "staff";$returnvalue["controller"]  = "staff"; break;
            case "profile":$returnvalue["layout"] = "staffprofile";$returnvalue["controller"]  = "staff"; break;
            case "users":$returnvalue["layout"] = "users";$returnvalue["controller"]  = "staff"; break;
            case "addticket": $returnvalue["layout"] = "formticket";$returnvalue["controller"]  = "ticket"; break;
            case "mytickets": $returnvalue["layout"] = "mytickets";$returnvalue["controller"]  = "ticket"; break;
            case "staffmytickets": $returnvalue["layout"] = "myticketsstaff";$returnvalue["controller"]  = "ticket"; break;
            case "status": $returnvalue["layout"] = "ticketstatus";$returnvalue["controller"]  = "ticket"; break;
            case "viewticket": $returnvalue["layout"] = "ticketdetail";$returnvalue["controller"]  = "ticket"; break;
            case "printticket": $returnvalue["layout"] = "print_ticket";$returnvalue["controller"]  = "ticket"; break;
            case "userpermissions": $returnvalue["layout"] = "userpermissions";$returnvalue["controller"]  = "userpermissions"; break;
            case "staffreports": $returnvalue["layout"] = "staffreports";$returnvalue["controller"]  = "reports"; break;
            case "staffdetailreport": $returnvalue["layout"] = "staffdetailreport";$returnvalue["controller"]  = "reports"; break;
            case "departmentreports": $returnvalue["layout"] = "departmentreports";$returnvalue["controller"]  = "reports"; break;
            case "feedbacks": $returnvalue["layout"] = "feedbacks";$returnvalue["controller"]  = "feedback"; break;
            case "addfeedback": $returnvalue["layout"] = "formfeedback";$returnvalue["controller"]  = "feedback"; break;
            case "visitorsuccessmessage": $returnvalue["layout"] = "visitorsuccessmessage";$returnvalue["controller"]  = "ticket"; break;
            case "datacomplianceactions": $returnvalue["layout"] = "adderasedatarequest"; $returnvalue["controller"] = "gdpr"; break;
        }
        if (isset($returnvalue))
            return $returnvalue;
    }

    function parseTask($value) {
        //  $returnvalue = "";
            switch ($value) {
            case "tk-downloadbyname":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "downloadbyname"; break;
            case "tk:downloadbyname":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "downloadbyname"; break;
            case "tk-getdownloadbyid":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "getdownloadbyid"; break;
            case "tk:getdownloadbyid":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "getdownloadbyid"; break;
            case "tk-downloadall":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "downloadall"; break;
            case "tk:downloadall":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "downloadall"; break;
            case "tk-downloadallforreply":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "downloadallforreply"; break;
            case "tk:downloadallforreply":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "downloadallforreply"; break;
            case "tk-getdownloadbyid-note":$returnvalue["controller"] = "note"; $returnvalue["task"] = "getdownloadbyid"; break;
            case "tk:getdownloadbyid-note":$returnvalue["controller"] = "note"; $returnvalue["task"] = "getdownloadbyid"; break;
            case "tk-getdownloadbyid-knowledgebase":$returnvalue["controller"] = "knowledgebase"; $returnvalue["task"] = "getdownloadbyid"; break;
            case "tk:getdownloadbyid-knowledgebase":$returnvalue["controller"] = "knowledgebase"; $returnvalue["task"] = "getdownloadbyid"; break;
            case "tk-getdownloadbyid-downloads":$returnvalue["controller"] = "downloads"; $returnvalue["task"] = "getdownloadbyid"; break;
            case "tk:getdownloadbyid-downloads":$returnvalue["controller"] = "downloads"; $returnvalue["task"] = "getdownloadbyid"; break;
            case "tk-downloadall-downloads":$returnvalue["controller"] = "downloads"; $returnvalue["task"] = "downloadall"; break;
            case "tk:downloadall-downloads":$returnvalue["controller"] = "downloads"; $returnvalue["task"] = "downloadall"; break;
            case "tk-deleteattachmentbyid-downloads":$returnvalue["controller"] = "downloads"; $returnvalue["task"] = "deleteattachmentbyid"; break;
            case "tk:deleteattachmentbyid-downloads":$returnvalue["controller"] = "downloads"; $returnvalue["task"] = "deleteattachmentbyid"; break;
            case "tk-deleteattachmentbyid-knowledgebase":$returnvalue["controller"] = "knowledgebase"; $returnvalue["task"] = "deleteattachmentbyid"; break;
            case "tk:deleteattachmentbyid-knowledgebase":$returnvalue["controller"] = "knowledgebase"; $returnvalue["task"] = "deleteattachmentbyid"; break;
            case "tk-removeusereraserequest-gdpr":$returnvalue["controller"] = "gdpr"; $returnvalue["task"] = "removeusereraserequest"; break;
            case "tk:removeusereraserequest-gdpr":$returnvalue["controller"] = "gdpr"; $returnvalue["task"] = "removeusereraserequest"; break;
            case "tk-exportusereraserequest-gdpr":$returnvalue["controller"] = "gdpr"; $returnvalue["task"] = "exportusereraserequest"; break;
            case "tk:exportusereraserequest-gdpr":$returnvalue["controller"] = "gdpr"; $returnvalue["task"] = "exportusereraserequest"; break;
            case "tk-logout-jssupportticket":$returnvalue["controller"] = "jssupportticket"; $returnvalue["task"] = "logout"; break;
            case "tk:logout-jssupportticket":$returnvalue["controller"] = "jssupportticket"; $returnvalue["task"] = "logout"; break;
            case "tk-deleteticket":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "deleteticket"; break;
            case "tk-addtickettag":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "addtickettag"; break;
            case "tk-removetickettag":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "removetickettag"; break;
            case "tk-addticketwatcher":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "addticketwatcher"; break;
            case "tk-removeticketwatcher":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "removeticketwatcher"; break;
            case "tk-applyticketsla":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "applyticketsla"; break;
            case "tk-saveticketsavedview":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "saveticketsavedview"; break;
            case "tk-applyticketsavedview":$returnvalue["controller"] = "ticket"; $returnvalue["task"] = "applyticketsavedview"; break;
        }
        if (isset($returnvalue))
            return $returnvalue;
    }

    function parseTaskFallback($value) {
        $value = (string) $value;
        if (strpos($value, 'tk-') === 0) {
            $value = substr($value, 3);
        }
        $controller = 'ticket';
        $task = trim($value);
        if ($task === '') {
            return array('controller' => $controller, 'task' => '');
        }
        $parts = explode('-', $task);
        if (count($parts) > 1) {
            $possibleController = array_pop($parts);
            if ($possibleController !== '') {
                $controller = $possibleController;
            }
            $task = implode('-', $parts);
        }
        return array('controller' => $controller, 'task' => $task);
    }

    function buildListingFor($value){
        $returnvalue = '';
        switch ($value) {
            case '1':$returnvalue = 'open';break;
            case '2':$returnvalue = 'answered';break;
            case '3':$returnvalue = 'overdue';break;
            case '4':$returnvalue = 'closed';break;
            case '5':$returnvalue = 'all';break;
        }
        return $returnvalue;
    }

    function parseListingFor($value){
        $returnvalue = '';
        switch ($value) {
            case 'open':$returnvalue = '1';break;
            case 'answered':$returnvalue = '2';break;
            case 'overdue':$returnvalue = '3';break;
            case 'closed':$returnvalue = '4';break;
            case 'all':$returnvalue = '5';break;
        }
        return $returnvalue;
    }
    function getFaqTitleById($id){
        if(!is_numeric($id)) return false;
        $db = Factory::getDbo();
        $query = "SELECT subject FROM `#__js_ticket_faqs` WHERE id=$id";
        $db->setQuery($query);
        $name = $db->loadResult();
        return $this->clean($name);
    }
    function getAnnouncementTitleById($id){
        if(!is_numeric($id)) return false;
        $db = Factory::getDbo();
        $query = "SELECT title FROM `#__js_ticket_announcements` WHERE id=$id";
        $db->setQuery($query);
        $name = $db->loadResult();
        return $this->clean($name);
    }
    function getArticleTitleById($id){
        if(!is_numeric($id)) return false;
        $db = Factory::getDbo();
        $query = "SELECT subject FROM `#__js_ticket_articles` WHERE id=$id";
        $db->setQuery($query);
        $name = $db->loadResult();
        return $this->clean($name);
    }
    function parseId($value) {
        $id = explode("-", $value);
        $count = count($id);
        $id = (int) $id[($count - 1)];
        return $id;
    }
    function clean($string) {
//        $string = strtolower($string);
        $string = strip_tags($string, "");
        //Strip any unwanted characters
        // $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);

        $string = preg_replace("/[@!*%^(){}?&$\\\\#\\/]+/", "", $string);

        //Clean multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }
}
