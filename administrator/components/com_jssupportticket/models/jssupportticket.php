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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;

class JSSupportticketModelJSSupportticket extends JSSupportTicketModel{
    function __construct() {
        parent::__construct();
    }

    function getControlPanelData(){
      $curdate = date('Y-m-d');
      $fromdate = date('Y-m-d', getJSTicketPHPFunctionsClass()->jsticket_strtotime("now -1 month"));
      $db = Factory::getDbo();
      $result = array();
      // $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND status = 0 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate)." ) AS totalticket
      //             FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
      // $db->setQuery($query);
      // $openticket_pr = $db->loadObjectList();
      // $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
      //             FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
      // $db->setQuery($query);
      // $answeredticket_pr = $db->loadObjectList();
      // $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
      //             FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
      // $db->setQuery($query);
      // $pendingticket_pr = $db->loadObjectList();

      //   $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id  AND status = 4 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
      //               FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
      //   $db->setQuery($query);
      //   $closeticket_pr = $db->loadObjectList();

      //   $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id  AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
      //               FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
      //   $db->setQuery($query);
      //   $totalticket_pr = $db->loadObjectList();


        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets`
                    WHERE priorityid = priority.id AND  status != 4 AND status != 5) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $openticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets`
                    WHERE priorityid = priority.id AND status = 3 ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $answeredticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets`
                    WHERE priorityid = priority.id AND isoverdue = 1 AND status != 4 AND status != 5 ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $overdueticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets`
                    WHERE priorityid = priority.id  AND isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $pendingticket_pr = $db->loadObjectList();

          $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id  AND status = 4 ) AS totalticket
                      FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
          $db->setQuery($query);
          $closeticket_pr = $db->loadObjectList();

        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets`
                    WHERE priorityid = priority.id ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $totalticket_pr = $db->loadObjectList();


        $result['stack_chart_horizontal']['title'] = "['".Text::_("Tickets")."',";
        $result['stack_chart_horizontal']['data'] = "['".Text::_("Close")."',";
        foreach($closeticket_pr AS $pr){
            $result['stack_chart_horizontal']['title'] .= "'".Text::_($pr->priority)."',";
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }
        $result['stack_chart_horizontal']['title'] .= "]";
        $result['stack_chart_horizontal']['data'] .= "],['".Text::_("Pending")."',";

        foreach($pendingticket_pr AS $pr){
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }

        $result['stack_chart_horizontal']['data'] .= "],['".Text::_("Answered")."',";

        foreach($answeredticket_pr AS $pr){
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }

        $result['stack_chart_horizontal']['data'] .= "],['".Text::_("New")."',";

        foreach($openticket_pr AS $pr){
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }

        $result['stack_chart_horizontal']['data'] .= "]";

        //To show priority colors on chart
        $jsonColorList = "[";

        $query = "SELECT prioritycolour FROM `#__js_ticket_priorities` ORDER BY priority ";
        $db->setQuery($query);
        foreach($db->loadObjectList() as $priority){
            $jsonColorList.= "'".$priority->prioritycolour."',";
        }
        $jsonColorList .= "]";
        $result['stack_chart_horizontal']['colors'] = $jsonColorList;
        //end priority colors

        $result['ticket_total']['openticket'] = 0;
        $result['ticket_total']['closeticket'] = 0;
        $result['ticket_total']['pendingticket'] = 0;
        $result['ticket_total']['answeredticket'] = 0;
        $result['ticket_total']['totalticket'] = 0;

        $count = getJSTicketPHPFunctionsClass()->jsticket_count($openticket_pr);
        for($i = 0;$i < $count; $i++){
            $result['ticket_total']['openticket'] += $openticket_pr[$i]->totalticket;
            $result['ticket_total']['closeticket'] += $closeticket_pr[$i]->totalticket;
            $result['ticket_total']['pendingticket'] += $pendingticket_pr[$i]->totalticket;
            $result['ticket_total']['answeredticket'] += $answeredticket_pr[$i]->totalticket;
            $result['ticket_total']['totalticket'] += $totalticket_pr[$i]->totalticket;
        }

        //today tickets for chart
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND date(created) = '".$curdate."')  AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $priorities = $db->loadObjectList();
        $result['today_ticket_chart']['title'] = "['".Text::_('Priority')."',";
        $result['today_ticket_chart']['data'] = "['',";
        foreach($priorities AS $pr){
            $result['today_ticket_chart']['title'] .= "'".Text::_($pr->priority)."',";
            $result['today_ticket_chart']['data'] .= $pr->totalticket.",";
        }
        $result['today_ticket_chart']['title'] .= "]";
        $result['today_ticket_chart']['data'] .= "]";

    $query = "SELECT ticket.id,ticket.ticketid,ticket.subject,ticket.name,ticket.created,priority.priority,priority.prioritycolour,ticket.status,department.departmentname
            FROM `#__js_ticket_tickets` AS ticket
          JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid
          LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
        ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 5";
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();
        return $result;
    }

    function getUserTicketStatsForCP(){
        $db = Factory::getDbo();

        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsGuest())
            return false;

        // $all_ticket = $user->checkUserPermission('All Tickets');
        $allticket_query = "ticket.uid = ". $user->getId();


        $result = array();

        $query = "SELECT COUNT(ticket.id)
                FROM `#__js_ticket_tickets` AS ticket
                JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                WHERE $allticket_query AND (ticket.status != 4 AND ticket.status != 5)";
        $db->setQuery($query);
        $result['openticket'] = $db->loadResult();

        $query = "SELECT COUNT(ticket.id)
                FROM `#__js_ticket_tickets` AS ticket
                LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                WHERE $allticket_query AND ticket.status = 3 ";
        $db->setQuery($query);
        $result['answeredticket'] = $db->loadResult();

        $query = "SELECT COUNT(ticket.id)
                FROM `#__js_ticket_tickets` AS ticket
                LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                WHERE $allticket_query AND (ticket.status = 4 OR ticket.status = 5)";
        $db->setQuery($query);
        $result['closedticket'] = $db->loadResult();

        $query = "SELECT COUNT(ticket.id)
                  FROM `#__js_ticket_tickets` AS ticket
                  LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                 JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                 WHERE $allticket_query";
      $db->setQuery($query);
      $result['allticket'] = $db->loadResult();
      return $result;
    }

function getListTranslations() {

        $result = array();
        $result['error'] = false;

        $path = JPATH_ADMINISTRATOR.'/language';

        if( ! is_writeable($path)){
            $result['error'] = Text::_('Dir is not writeable').' '.$path;

        }else{

            if($this->isConnected()){

                $version = $this->getJSModel('config')->getConfigByFor('version');
                if(!isset($version['versiontype'])){
                    $version['versiontype'] = $this->getJSModel('config')->getConfigurationByName('versiontype');
                }

                $url = "https://www.joomsky.com/translations/api/1.0/index.php";
                $post_data['product'] ='js-support-ticket-joomla';
                $post_data['domain'] = Uri::root();
                $post_data['producttype'] = $version['versiontype'];
                $post_data['productcode'] = 'jssupportticket';
                $post_data['productversion'] = $version['version'];
                $post_data['JVERSION'] = JVERSION;
                $post_data['method'] = 'getTranslations';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                $response = curl_exec($ch);
                curl_close($ch);

                $result['data'] = $response;
            }else{
                $result['error'] = Text::_('Unable to connect to server');
            }
        }

        $result = json_encode($result);

        return $result;
    }

    function makeLanguageCode($lang_name , $path){

        if( getJSTicketPHPFunctionsClass()->jsticket_strpos($lang_name, '_') !== false ) {
            $lang_name = getJSTicketPHPFunctionsClass()->jsticket_str_replace('_', '-', $lang_name);
        }else{
            if($lang_name == 'en'){
                $lang_name = $lang_name.'-'.getJSTicketPHPFunctionsClass()->jsticket_strtoupper('gb');
            }elseif($lang_name == 'sv'){
                $lang_name = $lang_name.'-'.getJSTicketPHPFunctionsClass()->jsticket_strtoupper('se');
            }elseif($lang_name == 'ar'){
                $lang_folders = scandir($path);
                $n = getJSTicketPHPFunctionsClass()->jsticket_count($lang_folders);
                for ($i = 0; $i < $n; $i++) {
                    if($lang_folders[$i] == 'ar-SA'){
                        $lang_name = $lang_folders[$i];
                        $i = $n;
                    }elseif ($lang_folders[$i] == 'ar-EG'){
                        $lang_name = $lang_folders[$i];
                        $i = $n;
                    }elseif ($lang_folders[$i] == 'ar-AA'){
                        $lang_name = $lang_folders[$i];
                        $i = $n;
                    }
                }
            }else{
                $lang_name = $lang_name.'-'.getJSTicketPHPFunctionsClass()->jsticket_strtoupper($lang_name);
            }
        }
        return $lang_name;
    }

    function validateAndShowDownloadFileName( $lang_name ){

        if($lang_name == '')
            return '';
        $result = array();
        $path = JPATH_ADMINISTRATOR.'/language';

        $final_name = $this->makeLanguageCode($lang_name , $path);

        $result['error'] = false;
        if(!file_exists($path)){
            $result['error'] = Text::_('Dir not exist').': '.$path;
        }elseif(!is_writeable($path)){
            $result['error'] = Text::_('Dir is not writeable').': '.$path;
        }else{
            $result['input'] = '<input id="languagecode" class="text_area" type="text" value="'.$final_name.'" name="languagecode">';
            $result['path'] = $path;
        }
        $result = json_encode($result);
        return $result;
    }

    function getLanguageTranslation($lang_name , $language_code){

        $result = array();
        $result['error'] = false;
        $path = JPATH_ADMINISTRATOR.'/language';

        if($lang_name == '' || $language_code == ''){
            $result['error'] = Text::_('Empty values are not allowed');
            return json_encode($result);
        }

        $path = $path.'/'.$language_code;
        $final_path = $path.'/'.$language_code.'.com_jssupportticket.ini';

        if(!file_exists($path)){
            $result['error'] = Text::_('Required language is not installed').': '.$language_code;
            return json_encode($result);
        }

        if(!is_writeable($path)){
            $result['error'] = Text::_('Dir is not writeable').': '.$path;
            return json_encode($result);
        }

        if(!file_exists($final_path)){
            touch($final_path);
        }

        if(!is_writeable($final_path)){
            $result['error'] = Text::_('File is not writeable').': '.$final_path;
        }else{

            if($this->isConnected()){

                $version = $this->getJSModel('config')->getConfigByFor('version');

                $url = "https://www.joomsky.com/translations/api/1.0/index.php";
                $post_data['product'] ='js-support-ticket-joomla';
                $post_data['domain'] = Uri::root();
                $post_data['producttype'] = $version['versiontype'];
                $post_data['productcode'] = 'jssupportticket';
                $post_data['productversion'] = $version['version'];
                $post_data['JVERSION'] = JVERSION;
                $post_data['translationcode'] = $lang_name;
                $post_data['method'] = 'getTranslationFile';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                $response = curl_exec($ch);
                curl_close($ch);
                $array = json_decode($response, true);

                $ret = $this->writeLanguageFile( $final_path , $array['file']);

                if($ret != false){
                    $url = "https://www.joomsky.com/translations/api/1.0/index.php";
                    $post_data['product'] ='js-support-ticket-joomla';
                    $post_data['domain'] = Uri::root();
                    $post_data['producttype'] = $version['versiontype'];
                    $post_data['productcode'] = 'jssupportticket';
                    $post_data['productversion'] = $version['version'];
                    $post_data['JVERSION'] = JVERSION;
                    $post_data['folder'] = $array['foldername'];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    $response = curl_exec($ch);
                    curl_close($ch);
                }
                $result['data'] = Text::_('File Downloaded Successfully');
            }else{
                $result['error'] = Text::_('Unable to connect to server');
            }
        }

        $result = json_encode($result);

        return $result;

    }

    function writeLanguageFile( $path , $url ){
        $result = file_put_contents($path, fopen($url, 'r'));
        return $result;
    }

    function isConnected(){

        $connected = @fsockopen("www.google.com", 80);
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }

   function getArticleslistCombo(){
      // For getting articles page list
      $db = Factory::getDbo();
      $query = $db->getQuery(true);
      $query->select('*')
           ->from($db->quoteName('#__content'));
      $db->setQuery($query);
      $rows = $db->loadObjectList();
      $linktype = array(
         '0' => array('value' => 0, 'text' => Text::_('Select Article Page')),
      );
      foreach($rows AS $row => $data){
         $linktype[$row+1] = array('value' => $data->id , 'text' => Text::_($data->title));
      }

      return $linktype;
   }

    function stripslashesFull($input){// testing this function/.
      if (is_array($input)) {
          $input = array_map(array($this,'stripslashesFull'), $input);
      } elseif (is_object($input)) {
          $vars = get_object_vars($input);
          foreach ($vars as $k=>$v) {
              $input->{$k} = stripslashesFull($v);
          }
      } else {
          $input = getJSTicketPHPFunctionsClass()->jsticket_stripslashes($input);
      }
      return $input;
    } 

    function joomlaContentArticles(){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id AS value, title AS text');
        $query->from('#__content');

        $db->setQuery((string)$query);
        $res = $db->loadObjectList();
        return $res;
    }
    function getHtmlInput($htmlText){
        $app = Factory::getApplication();
        $text = ComponentHelper::filterText($app->input->get($htmlText, '', 'raw'));
        return $text;    
    }

}

?>
