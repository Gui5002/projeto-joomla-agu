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
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelJSSupportticket extends JSSupportTicketModel{
    function __construct() {
        parent::__construct();
    }

    function getTicketsSummaryForAdminModule($month_back = 1){
        if(!is_numeric($month_back)){
            $month_back = 1;
        }

        $db = Factory::getDbo();
        $result = array();
        $curdate = date('Y-m-d');
        $fromdate = date('Y-m-d', getJSTicketPHPFunctionsClass()->jsticket_strtotime("now -".$month_back." month"));

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0 AND (lastreply IS NULL OR lastreply < '1971-01-01') AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate);
        $db->setQuery($query);
        $openticket = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate);
        $db->setQuery($query);
        $answeredticket = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate);
        $db->setQuery($query);
        $overdueticket = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4 AND (lastreply IS NOT NULL AND lastreply >= '1971-01-01') AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate);
        $db->setQuery($query);
        $pendingticket = $db->loadResult();

        $result = array();
        $result['new'] = $openticket;
        $result['answered'] = $answeredticket;
        $result['overdue'] = $overdueticket;
        $result['pending'] = $pendingticket;

        return $result;
    }

    function getLatestTicketsAdminModule(){
        $db = Factory::getDbo();
        $query = "SELECT ticket.id,ticket.ticketid,ticket.subject,ticket.name,ticket.created,priority.priority,priority.prioritycolour,ticket.status
            FROM `#__js_ticket_tickets` AS ticket
            JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid
            ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 5";
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();
        $result['date_format'] = $this->getJSModelForAdminMP('config')->getConfigurationByName('date_format');
        return $result;
    }

    function getControlPanelData(){
    	$db = Factory::getDbo();
    	$result = array();
        $curdate = date('Y-m-d');
        $fromdate = date('Y-m-d', getJSTicketPHPFunctionsClass()->jsticket_strtotime("now -1 month"));

        // $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND status = 0 AND (lastreply IS NULL OR lastreply < '1971-01-01') AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate)." ) AS totalticket
        //             FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        // $db->setQuery($query);
        // $openticket_pr = $db->loadObjectList();
        // $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
        //             FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        // $db->setQuery($query);
        // $answeredticket_pr = $db->loadObjectList();
        // $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isoverdue = 1 AND status != 4 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
        //             FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        // $db->setQuery($query);
        // $overdueticket_pr = $db->loadObjectList();
        // $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id  AND isanswered != 1 AND status != 4 AND (lastreply IS NOT NULL AND lastreply >= '1971-01-01') AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
        //             FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        // $db->setQuery($query);
        // $pendingticket_pr = $db->loadObjectList();

        // $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id  AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
        //             FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        // $db->setQuery($query);
        // $totalticket_pr = $db->loadObjectList();

        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets`
                    WHERE priorityid = priority.id AND  status != 4 AND isanswered = 0 AND status != 5) AS totalticket
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
                    WHERE priorityid = priority.id  AND isanswered != 1 AND status != 4 AND (lastreply IS NOT NULL AND lastreply >= '1971-01-01') ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $pendingticket_pr = $db->loadObjectList();

        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets`
                    WHERE priorityid = priority.id ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $totalticket_pr = $db->loadObjectList();


        $result['stack_chart_horizontal']['title'] = "['".Text::_("Tickets")."',";
        $result['stack_chart_horizontal']['data'] = "['".Text::_("Overdue")."',";
        foreach($overdueticket_pr AS $pr){
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
        $result['ticket_total']['overdueticket'] = 0;
        $result['ticket_total']['pendingticket'] = 0;
        $result['ticket_total']['answeredticket'] = 0;
        $result['ticket_total']['totalticket'] = 0;

        $count = getJSTicketPHPFunctionsClass()->jsticket_count($openticket_pr);
        for($i = 0;$i < $count; $i++){
            $result['ticket_total']['openticket'] += $openticket_pr[$i]->totalticket;
            $result['ticket_total']['overdueticket'] += $overdueticket_pr[$i]->totalticket;
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

        $query = "SELECT ticket.id,ticket.ticketid,ticket.subject,ticket.name,ticket.created,priority.priority,priority.prioritycolour,ticket.status
        		FROM `#__js_ticket_tickets` AS ticket
        		JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid
        		ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 5";
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();

        // Admin dashboard operational data.
        // These lightweight summaries make the control panel useful even when the chart has very little data.
        $query = "SELECT
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status != 4 AND status != 5) AS active,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4) AS closed,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE (staffid IS NULL OR staffid = 0) AND status != 4 AND status != 5) AS unassigned,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND COALESCE(isoverdue, 0) != 1 AND status != 4 AND status != 5 AND lastreply IS NOT NULL AND lastreply >= '1971-01-01') AS pending,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND status != 5) AS overdue,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE ticketviaemail = 1) AS viaemail,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE mergestatus = 1) AS merged";
        $db->setQuery($query);
        $result['admin_snapshot'] = (array) $db->loadAssoc();

        $query = "SELECT
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE DATE(created) = " . $db->quote($curdate) . ") AS new_today,
                    (SELECT COUNT(id) FROM `#__js_ticket_replies` WHERE DATE(created) = " . $db->quote($curdate) . ") AS replies_today,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE DATE(closed) = " . $db->quote($curdate) . ") AS closed_today,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND status != 5 AND DATE(created) = " . $db->quote($curdate) . ") AS overdue_today";
        $db->setQuery($query);
        $result['today_summary'] = (array) $db->loadAssoc();

        $query = "SELECT
                    (SELECT COUNT(id) FROM `#__js_ticket_departments`) AS departments,
                    (SELECT COUNT(id) FROM `#__js_ticket_departments` WHERE status = 1) AS active_departments";
        $db->setQuery($query);
        $result['people_summary'] = (array) $db->loadAssoc();

        $query = "SELECT IFNULL(department.departmentname, 'Unassigned') AS departmentname,
                         COUNT(ticket.id) AS totalticket,
                         SUM(CASE WHEN ticket.status != 4 AND ticket.status != 5 THEN 1 ELSE 0 END) AS active
                    FROM `#__js_ticket_tickets` AS ticket
                    LEFT JOIN `#__js_ticket_departments` AS department ON department.id = ticket.departmentid
                    GROUP BY department.id, department.departmentname
                    ORDER BY active DESC, totalticket DESC
                    LIMIT 0, 5";
        $db->setQuery($query);
        $result['department_activity'] = $db->loadObjectList();

        $query = "SELECT priority.priority, priority.prioritycolour,
                         COUNT(ticket.id) AS totalticket,
                         SUM(CASE WHEN ticket.status != 4 AND ticket.status != 5 THEN 1 ELSE 0 END) AS active
                    FROM `#__js_ticket_priorities` AS priority
                    LEFT JOIN `#__js_ticket_tickets` AS ticket ON ticket.priorityid = priority.id
                    GROUP BY priority.id, priority.priority, priority.prioritycolour
                    ORDER BY priority.priority";
        $db->setQuery($query);
        $result['priority_breakdown'] = $db->loadObjectList();

        $query = "SELECT ticket.id, ticket.ticketid, ticket.subject, ticket.name, ticket.created, ticket.duedate, ticket.lastreply, ticket.status, ticket.isoverdue,
                         priority.priority, priority.prioritycolour, department.departmentname
                    FROM `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid
                    LEFT JOIN `#__js_ticket_departments` AS department ON department.id = ticket.departmentid
                    WHERE ticket.status != 4 AND ticket.status != 5
                    ORDER BY ticket.created ASC
                    LIMIT 0, 5";
        $db->setQuery($query);
        $result['attention_tickets'] = $db->loadObjectList();

        return $result;
    }
    function storeTheme($data) {
        $colors = $this->sanitizeThemeColors($data);
        if ($colors === false) {
            $this->setError(Text::_('Please enter valid hex colors. Example: #2563eb'));
            $this->logThemeError('Theme validation failed while saving.', array('submitted_colors' => $this->redactThemeData($data)));
            return false;
        }

        $contrastIssues = $this->getThemeContrastIssues($colors);
        if (!empty($contrastIssues)) {
            $this->setError(Text::_('Theme colors were not saved because some color pairs are not readable:') . ' ' . implode(' ', $contrastIssues));
            $this->logThemeError('Theme readability validation failed while saving.', array(
                'submitted_colors' => $colors,
                'contrast_issues' => $contrastIssues,
            ));
            return false;
        }

        $filepath = $this->getThemeColorFilePath(true);
        if (!$filepath) {
            $this->setError(Text::_('Theme color file path could not be prepared.'));
            $this->logThemeError('Theme color file path could not be prepared.', array('submitted_colors' => $colors));
            return false;
        }

        $filestring = is_file($filepath) ? file_get_contents($filepath) : false;
        if ($filestring === false || trim($filestring) === '') {
            $filestring = $this->getDefaultColorFileString();
        }

        for ($i = 1; $i <= 7; $i++) {
            $this->replaceString($filestring, $i, $colors);
        }

        if (file_put_contents($filepath, $filestring, LOCK_EX) !== false) {
            return true;
        }

        $this->setError(Text::_('Unable to write theme color file. Please check file permissions.'));
        $this->logThemeError('Unable to write theme color file.', array(
            'path' => $filepath,
            'submitted_colors' => $colors,
            'is_writable' => is_writable($filepath),
            'dir_writable' => is_dir(dirname($filepath)) ? is_writable(dirname($filepath)) : false,
        ));
        return false;
    }

    function sanitizeThemeColors($data) {
        $defaults = $this->getDefaultThemeColors();
        $colors = array();
        for ($i = 1; $i <= 7; $i++) {
            $key = 'color' . $i;
            $value = isset($data[$key]) ? trim((string) $data[$key]) : $defaults[$key];
            if ($value !== '' && $value[0] !== '#') {
                $value = '#' . $value;
            }
            if (!preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                return false;
            }
            $colors[$key] = strtolower($value);
        }
        return $colors;
    }

    private function getThemeContrastIssues($colors) {
        $issues = array();
        $checks = array(
            array('foreground' => 'color7', 'background' => 'color1', 'minimum' => 3.0, 'label' => Text::_('header and action text against top menu background')),
            array('foreground' => 'color7', 'background' => 'color2', 'minimum' => 4.5, 'label' => Text::_('header and action text against dark section bars')),
            array('foreground' => 'color2', 'background' => 'color3', 'minimum' => 3.0, 'label' => Text::_('heading color against content background')),
            array('foreground' => 'color4', 'background' => 'color3', 'minimum' => 4.5, 'label' => Text::_('content text against content background')),
        );

        foreach ($checks as $check) {
            $ratio = $this->getThemeContrastRatio($colors[$check['foreground']], $colors[$check['background']]);
            if ($ratio < $check['minimum']) {
                $issues[] = $check['label'] . ' (' . number_format($ratio, 2) . ':1, ' . Text::_('minimum') . ' ' . rtrim(rtrim(number_format($check['minimum'], 1), '0'), '.') . ':1).';
            }
        }

        return $issues;
    }

    private function getThemeContrastRatio($foreground, $background) {
        $first = $this->getThemeColorLuminance($foreground);
        $second = $this->getThemeColorLuminance($background);
        $light = max($first, $second);
        $dark = min($first, $second);
        return ($light + 0.05) / ($dark + 0.05);
    }

    private function getThemeColorLuminance($hex) {
        $hex = ltrim((string) $hex, '#');
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return 0;
        }

        $channels = array(
            hexdec(substr($hex, 0, 2)) / 255,
            hexdec(substr($hex, 2, 2)) / 255,
            hexdec(substr($hex, 4, 2)) / 255,
        );

        foreach ($channels as $index => $channel) {
            $channels[$index] = ($channel <= 0.03928) ? ($channel / 12.92) : pow(($channel + 0.055) / 1.055, 2.4);
        }

        return (0.2126 * $channels[0]) + (0.7152 * $channels[1]) + (0.0722 * $channels[2]);
    }

    function getDefaultThemeColors() {
        return array(
            'color1' => '#2563eb',
            'color2' => '#1e293b',
            'color3' => '#f8fafc',
            'color4' => '#334155',
            'color5' => '#dbe4ef',
            'color6' => '#eff6ff',
            'color7' => '#ffffff',
        );
    }

    function getThemeColorFilePath($prepare = false) {
        $paths = array(
            JPATH_ROOT . '/components/com_jssupportticket/include/css/color.php',
            JPATH_COMPONENT_ADMINISTRATOR . '/include/css/color.php',
        );

        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        if ($prepare) {
            $preferred = $paths[0];
            $dir = dirname($preferred);
            if (is_dir($dir) && is_writable($dir)) {
                return $preferred;
            }

            $fallback = $paths[1];
            $fallbackDir = dirname($fallback);
            if (is_dir($fallbackDir) && is_writable($fallbackDir)) {
                return $fallback;
            }
        }

        return $paths[0];
    }

    function getDefaultColorFileString() {
        $colors = $this->getDefaultThemeColors();
        return "<?php\n"
            . "defined('_JEXEC') or die('Restricted access');\n"
            . '$color1 = "' . $colors['color1'] . '";' . "\n"
            . '$color2 = "' . $colors['color2'] . '";' . "\n"
            . '$color3 = "' . $colors['color3'] . '";' . "\n"
            . '$color4 = "' . $colors['color4'] . '";' . "\n"
            . '$color5 = "' . $colors['color5'] . '";' . "\n"
            . '$color6 = "' . $colors['color6'] . '";' . "\n"
            . '$color7 = "' . $colors['color7'] . '";' . "\n"
            . "?>\n";
    }

    function replaceString(&$filestring, $colorNo, $data) {
        $key = 'color' . $colorNo;
        $value = isset($data[$key]) ? $data[$key] : $this->getDefaultThemeColors()[$key];
        $replacement = '$color' . $colorNo . ' = "' . $value . '";';

        if (preg_match('/\$color' . (int) $colorNo . '\s*=\s*[\"\'][^\"\']*[\"\']\s*;/', $filestring)) {
            $filestring = preg_replace('/\$color' . (int) $colorNo . '\s*=\s*[\"\'][^\"\']*[\"\']\s*;/', $replacement, $filestring, 1);
            return;
        }

        if (getJSTicketPHPFunctionsClass()->jsticket_strpos($filestring, '?>') !== false) {
            $filestring = str_replace('?>', $replacement . "\n?>", $filestring);
        } else {
            $filestring .= "\n" . $replacement . "\n";
        }
    }

    function getColorCode($filestring, $colorNo) {
        if (!is_string($filestring) || $filestring === '') {
            $defaults = $this->getDefaultThemeColors();
            return $defaults['color' . $colorNo];
        }

        if (preg_match('/\$color' . (int) $colorNo . '\s*=\s*[\"\'](#[0-9a-fA-F]{6})[\"\']\s*;/', $filestring, $matches)) {
            return strtolower($matches[1]);
        }

        $defaults = $this->getDefaultThemeColors();
        return $defaults['color' . $colorNo];
    }

    function getCurrentTheme() {
        $filepath = $this->getThemeColorFilePath(false);
        $filestring = is_file($filepath) ? file_get_contents($filepath) : '';
        $defaults = $this->getDefaultThemeColors();
        $theme = array();
        for ($i = 1; $i <= 7; $i++) {
            $theme['color' . $i] = $this->getColorCode($filestring, $i);
            if ($theme['color' . $i] === '') {
                $theme['color' . $i] = $defaults['color' . $i];
            }
        }
        $theme['color_file_path'] = $filepath;
        $theme['color_file_exists'] = is_file($filepath) ? 1 : 0;
        $theme['color_file_writable'] = is_file($filepath) ? (is_writable($filepath) ? 1 : 0) : (is_dir(dirname($filepath)) && is_writable(dirname($filepath)) ? 1 : 0);
        $result[0] = $theme;
        return $result;
    }

    private function redactThemeData($data) {
        $clean = array();
        for ($i = 1; $i <= 7; $i++) {
            $key = 'color' . $i;
            if (isset($data[$key])) {
                $clean[$key] = (string) $data[$key];
            }
        }
        return $clean;
    }

    private function logThemeError($message, $metadata = array()) {
        try {
            $this->getJSModel('systemerrors')->updateSystemErrors(array(
                'severity' => 'error',
                'source' => 'theme',
                'context' => 'jssupportticket.storeTheme',
                'message' => $message,
                'type' => 'ThemeSaveError',
                'metadata' => $metadata,
            ));
        } catch (Throwable $e) {
        }
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

    function getUserTicketStatsForCP(){
        $db = Factory::getDbo();

        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsGuest())
            return false;

        $allticket_query = "ticket.uid = ". (int) $user->getId();


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
