<?php
/**
 * JS Support Ticket 1.7.1 feature foundation model.
 *
 * This model intentionally uses add-only tables and does not change existing
 * ticket workflow behavior. It gives the next UI rebuild a safe data layer for
 * timeline, tags, watchers, SLA, saved views, notifications and modern logs.
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelFeaturefoundation extends JSSupportTicketModel {

    private $lastSlaApplyError = '';
    private $lastWatcherAddError = '';
    private $lastSavedViewError = '';

    public function __construct() {
        parent::__construct();
    }


    public function getLastSavedViewError() {
        return $this->lastSavedViewError;
    }

    public function storeActivityEvent(array $data) {
        try {
            $db = Factory::getDbo();
            $now = Factory::getDate()->toSql();
            $currentUser = $this->getCurrentUserData();

            $ticketid = isset($data['ticketid']) ? (int) $data['ticketid'] : null;
            $columns = array(
                'ticketid', 'referenceid', 'uid', 'staffid', 'actor_name', 'actor_email',
                'source', 'event_key', 'event_label', 'old_value', 'new_value', 'details',
                'metadata', 'visibility', 'created'
            );

            $values = array(
                $ticketid ?: 'NULL',
                isset($data['referenceid']) && $data['referenceid'] !== null ? (int) $data['referenceid'] : 'NULL',
                isset($data['uid']) && $data['uid'] !== null ? (int) $data['uid'] : ($currentUser['uid'] ?: 'NULL'),
                isset($data['staffid']) && $data['staffid'] !== null ? (int) $data['staffid'] : ($currentUser['staffid'] ?: 'NULL'),
                $db->quote($this->stringOrNull($data, 'actor_name', $currentUser['name'])),
                $db->quote($this->stringOrNull($data, 'actor_email', $currentUser['email'])),
                $db->quote($this->stringOrNull($data, 'source', 'system')),
                $db->quote($this->stringOrNull($data, 'event_key', 'activity')),
                $db->quote($this->stringOrNull($data, 'event_label', 'Activity')),
                $db->quote($this->stringOrNull($data, 'old_value', null)),
                $db->quote($this->stringOrNull($data, 'new_value', null)),
                $db->quote($this->stringOrNull($data, 'details', null)),
                $db->quote($this->jsonOrNull(isset($data['metadata']) ? $data['metadata'] : null)),
                $db->quote($this->stringOrNull($data, 'visibility', 'staff')),
                $db->quote(isset($data['created']) && $data['created'] ? $data['created'] : $now)
            );

            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__js_ticket_activity_events'))
                ->columns(array_map(array($db, 'quoteName'), $columns))
                ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
            $activityId = (int) $db->insertid();

            if ($ticketid) {
                $this->touchTicketActivity($ticketid, $currentUser['uid'], $now);
            }

            return $activityId;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function storeActivityEventLegacy($referenceid, $eventfor, $eventtype, $message, $messagetype) {
        $eventKey = $this->slugify((string) $eventtype);
        if ($eventKey === '') {
            $eventKey = 'legacy_activity';
        }

        return $this->storeActivityEvent(array(
            'ticketid' => (int) $referenceid,
            'referenceid' => (int) $referenceid,
            'source' => 'legacy_activity_log',
            'event_key' => $eventKey,
            'event_label' => (string) $eventtype,
            'details' => (string) $message,
            'metadata' => array(
                'legacy_eventfor' => $eventfor,
                'legacy_messagetype' => $messagetype,
            ),
            'visibility' => 'staff'
        ));
    }

    public function storeErrorLog(array $data) {
        try {
            $db = Factory::getDbo();
            $now = Factory::getDate()->toSql();
            $currentUser = $this->getCurrentUserData();

            $columns = array(
                'uid', 'staffid', 'ticketid', 'source', 'context', 'severity',
                'message', 'technical_detail', 'metadata', 'is_read', 'created'
            );

            $values = array(
                isset($data['uid']) && $data['uid'] !== null ? (int) $data['uid'] : ($currentUser['uid'] ?: 'NULL'),
                isset($data['staffid']) && $data['staffid'] !== null ? (int) $data['staffid'] : ($currentUser['staffid'] ?: 'NULL'),
                isset($data['ticketid']) && $data['ticketid'] !== null ? (int) $data['ticketid'] : 'NULL',
                $db->quote($this->stringOrNull($data, 'source', 'system')),
                $db->quote($this->stringOrNull($data, 'context', null)),
                $db->quote($this->stringOrNull($data, 'severity', 'error')),
                $db->quote($this->stringOrNull($data, 'message', null)),
                $db->quote($this->stringOrNull($data, 'technical_detail', null)),
                $db->quote($this->jsonOrNull(isset($data['metadata']) ? $data['metadata'] : null)),
                isset($data['is_read']) ? (int) $data['is_read'] : 0,
                $db->quote(isset($data['created']) && $data['created'] ? $data['created'] : $now)
            );

            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__js_ticket_error_logs'))
                ->columns(array_map(array($db, 'quoteName'), $columns))
                ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
            return (int) $db->insertid();
        } catch (Throwable $e) {
            return false;
        }
    }


    public function storeEmailLog(array $data) {
        try {
            $db = Factory::getDbo();
            $now = Factory::getDate()->toSql();
            $currentUser = $this->getCurrentUserData();
            $columns = array('uid','staffid','ticketid','recipient_email','sender_email','subject','action','status','transport','error_message','metadata','created');
            $values = array(
                isset($data['uid']) && $data['uid'] !== null ? (int)$data['uid'] : ($currentUser['uid'] ?: 'NULL'),
                isset($data['staffid']) && $data['staffid'] !== null ? (int)$data['staffid'] : ($currentUser['staffid'] ?: 'NULL'),
                isset($data['ticketid']) && $data['ticketid'] !== null ? (int)$data['ticketid'] : 'NULL',
                $db->quote($this->stringOrNull($data, 'recipient_email', null)),
                $db->quote($this->stringOrNull($data, 'sender_email', null)),
                $db->quote($this->truncateText($this->stringOrNull($data, 'subject', null), 255)),
                $db->quote($this->stringOrNull($data, 'action', null)),
                $db->quote($this->stringOrNull($data, 'status', 'unknown')),
                $db->quote($this->stringOrNull($data, 'transport', 'default')),
                $db->quote($this->stringOrNull($data, 'error_message', null)),
                $db->quote($this->jsonOrNull(isset($data['metadata']) ? $data['metadata'] : null)),
                $db->quote(isset($data['created']) && $data['created'] ? $data['created'] : $now)
            );
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__js_ticket_email_logs'))
                ->columns(array_map(array($db, 'quoteName'), $columns))
                ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
            return (int)$db->insertid();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function addNotification(array $data) {
        try {
            $db = Factory::getDbo();
            $now = Factory::getDate()->toSql();
            $columns = array('uid', 'staffid', 'ticketid', 'activityid', 'title', 'message', 'type', 'url', 'is_read', 'created');
            $values = array(
                isset($data['uid']) && $data['uid'] !== null ? (int) $data['uid'] : 'NULL',
                isset($data['staffid']) && $data['staffid'] !== null ? (int) $data['staffid'] : 'NULL',
                isset($data['ticketid']) && $data['ticketid'] !== null ? (int) $data['ticketid'] : 'NULL',
                isset($data['activityid']) && $data['activityid'] !== null ? (int) $data['activityid'] : 'NULL',
                $db->quote($this->stringOrNull($data, 'title', null)),
                $db->quote($this->stringOrNull($data, 'message', null)),
                $db->quote($this->stringOrNull($data, 'type', 'ticket')),
                $db->quote($this->stringOrNull($data, 'url', null)),
                isset($data['is_read']) ? (int) $data['is_read'] : 0,
                $db->quote(isset($data['created']) && $data['created'] ? $data['created'] : $now)
            );
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__js_ticket_notifications'))
                ->columns(array_map(array($db, 'quoteName'), $columns))
                ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
            return (int) $db->insertid();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function getTicketTimeline($ticketid, $limitstart = 0, $limit = 50, $includePrivate = true) {
        $ticketid = (int) $ticketid;
        if ($ticketid <= 0) {
            return array();
        }

        try {
            $db = Factory::getDbo();
            $visibility = $includePrivate ? '' : " AND visibility = 'public'";
            $query = "SELECT id, ticketid, uid, staffid, actor_name, actor_email, source, event_key, event_label, details, metadata, visibility, created"
                . " FROM `#__js_ticket_activity_events` WHERE ticketid = " . $ticketid . $visibility . " ORDER BY created DESC, id DESC";
            $db->setQuery($query, (int) $limitstart, (int) $limit);
            return $db->loadObjectList();
        } catch (Throwable $e) {
            return array();
        }
    }


    public function getTicketFeatureContext($ticket) {
        $ticketid = is_object($ticket) && isset($ticket->id) ? (int) $ticket->id : (int) $ticket;
        if ($ticketid <= 0) {
            return array(
                'tags' => array(),
                'available_tags' => array(),
                'watchers' => array(),
                'timeline' => array(),
                'sla' => null,
                'matched_sla' => null,
            );
        }

        $sla = null;
        $matchedSla = null;
        if (is_object($ticket)) {
            $sla = $this->getTicketSlaStatus($ticket);
            $matchedSla = $this->findMatchingSlaRule(
                isset($ticket->departmentid) ? (int) $ticket->departmentid : 0,
                isset($ticket->priorityid) ? (int) $ticket->priorityid : 0
            );
        }

        return array(
            'tags' => $this->getTicketTags($ticketid),
            'available_tags' => $this->getActiveTagsForSelect(),
            'watchers' => $this->getTicketWatchers($ticketid),
            'timeline' => $this->getTicketTimeline($ticketid, 0, 12, true),
            'sla' => $sla,
            'matched_sla' => $matchedSla,
        );
    }



    private function getTicketOwnerEmails($ticketid) {
        $ticketid = (int) $ticketid;
        if ($ticketid <= 0) {
            return null;
        }
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select('ticket.email AS ticket_email, juser.email AS user_email')
                ->from($db->quoteName('#__js_ticket_tickets', 'ticket'))
                ->join('LEFT', $db->quoteName('#__users', 'juser') . ' ON ' . $db->quoteName('juser.id') . ' = ' . $db->quoteName('ticket.uid'))
                ->where($db->quoteName('ticket.id') . ' = ' . $ticketid);
            $db->setQuery($query);
            $ticket = $db->loadObject();
            if (!$ticket) {
                return null;
            }
            $emails = array();
            foreach (array('ticket_email', 'user_email') as $field) {
                if (isset($ticket->{$field})) {
                    $value = strtolower(trim((string) $ticket->{$field}));
                    if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $emails[$value] = true;
                    }
                }
            }
            return array_keys($emails);
        } catch (Throwable $e) {
            return array();
        }
    }

    public function getActiveSavedViewsForSelect() {
        try {
            $db = Factory::getDbo();
            $currentUser = $this->getCurrentUserData();
            $where = array($db->quoteName('status') . ' = 1');
            $visibility = array($db->quoteName('visibility') . ' = ' . $db->quote('public'));
            if (!empty($currentUser['staffid'])) {
                $visibility[] = '(' . $db->quoteName('visibility') . ' = ' . $db->quote('private') . ' AND ' . $db->quoteName('staffid') . ' = ' . (int)$currentUser['staffid'] . ')';
            }
            if (!empty($currentUser['uid'])) {
                $visibility[] = '(' . $db->quoteName('visibility') . ' = ' . $db->quote('private') . ' AND ' . $db->quoteName('uid') . ' = ' . (int)$currentUser['uid'] . ')';
            }
            $where[] = '(' . implode(' OR ', $visibility) . ')';
            $query = $db->getQuery(true)
                ->select($db->quoteName(array('id','title','filters','sortby','visibility','is_default')))
                ->from($db->quoteName('#__js_ticket_saved_views'))
                ->where($where)
                ->order($db->quoteName('is_default') . ' DESC, ' . $db->quoteName('title') . ' ASC');
            $db->setQuery($query);
            return $db->loadObjectList();
        } catch (Throwable $e) {
            return array();
        }
    }

    public function getSavedViewForTicketList($id) {
        $id = (int)$id;
        if ($id <= 0) {
            return null;
        }
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__js_ticket_saved_views'))
                ->where($db->quoteName('id') . ' = ' . $id)
                ->where($db->quoteName('status') . ' = 1');
            $db->setQuery($query);
            return $db->loadObject();
        } catch (Throwable $e) {
            return null;
        }
    }

    public function storeTicketSavedView($title, array $filters, $sortby = '', $visibility = 'private') {
        $this->lastSavedViewError = '';
        $title = trim((string)$title);
        if ($title === '') {
            $this->lastSavedViewError = 'missing_title';
            return false;
        }
        try {
            $this->ensureSavedViewsTable();
            $db = Factory::getDbo();
            $currentUser = $this->getCurrentUserData();
            $now = Factory::getDate()->toSql();
            $visibility = $visibility === 'public' ? 'public' : 'private';
            $columns = array('uid','staffid','title','visibility','filters','columns','sortby','is_default','status','created','updated');
            $values = array(
                $currentUser['uid'] ? (int)$currentUser['uid'] : 'NULL',
                $currentUser['staffid'] ? (int)$currentUser['staffid'] : 'NULL',
                $db->quote($title),
                $db->quote($visibility),
                $db->quote($this->jsonOrNull($filters)),
                'NULL',
                $db->quote($this->truncateText((string)$sortby, 100)),
                0,
                1,
                $db->quote($now),
                $db->quote($now)
            );
            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__js_ticket_saved_views'))
                ->columns(array_map(array($db, 'quoteName'), $columns))
                ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
            $id = (int)$db->insertid();
            $this->storeActivityEvent(array(
                'source' => 'saved_views',
                'event_key' => 'saved_view_created',
                'event_label' => 'Saved view created',
                'details' => 'Saved ticket view created: ' . $title,
                'visibility' => 'staff'
            ));
            return $id;
        } catch (Throwable $e) {
            $this->lastSavedViewError = 'schema';
            $this->storeErrorLog(array(
                'source' => 'saved_views',
                'context' => 'storeTicketSavedView',
                'severity' => 'warning',
                'message' => 'Saved ticket view could not be stored.',
                'technical_detail' => $e->getMessage(),
            ));
            return false;
        }
    }


    private function ensureSavedViewsTable() {
        $db = Factory::getDbo();
        $query = "CREATE TABLE IF NOT EXISTS `#__js_ticket_saved_views` ("
            . " `id` int(11) NOT NULL AUTO_INCREMENT,"
            . " `uid` int(11) DEFAULT NULL,"
            . " `staffid` int(11) DEFAULT NULL,"
            . " `title` varchar(150) DEFAULT NULL,"
            . " `visibility` varchar(30) DEFAULT 'private',"
            . " `filters` longtext DEFAULT NULL,"
            . " `columns` longtext DEFAULT NULL,"
            . " `sortby` varchar(100) DEFAULT NULL,"
            . " `is_default` tinyint(1) NOT NULL DEFAULT '0',"
            . " `status` tinyint(1) NOT NULL DEFAULT '1',"
            . " `created` datetime DEFAULT NULL,"
            . " `updated` datetime DEFAULT NULL,"
            . " PRIMARY KEY (`id`),"
            . " KEY `idx_staff_visibility` (`staffid`,`visibility`),"
            . " KEY `idx_uid_visibility` (`uid`,`visibility`),"
            . " KEY `idx_status` (`status`)"
            . ") ENGINE=MyISAM DEFAULT CHARSET=utf8";
        $db->setQuery($query);
        $db->execute();

        $columns = $db->getTableColumns('#__js_ticket_saved_views', false);
        $columnMap = array(
            'uid' => 'int(11) DEFAULT NULL',
            'staffid' => 'int(11) DEFAULT NULL',
            'title' => 'varchar(150) DEFAULT NULL',
            'visibility' => "varchar(30) DEFAULT 'private'",
            'filters' => 'longtext DEFAULT NULL',
            'columns' => 'longtext DEFAULT NULL',
            'sortby' => 'varchar(100) DEFAULT NULL',
            'is_default' => "tinyint(1) NOT NULL DEFAULT '0'",
            'status' => "tinyint(1) NOT NULL DEFAULT '1'",
            'created' => 'datetime DEFAULT NULL',
            'updated' => 'datetime DEFAULT NULL',
        );
        foreach ($columnMap as $columnName => $definition) {
            if (!array_key_exists($columnName, $columns)) {
                $query = 'ALTER TABLE ' . $db->quoteName('#__js_ticket_saved_views') .
                    ' ADD ' . $db->quoteName($columnName) . ' ' . $definition;
                $db->setQuery($query);
                $db->execute();
            }
        }
    }

    public function findMatchingSlaRule($departmentid, $priorityid) {
        try {
            $db = Factory::getDbo();
            $departmentid = (int) $departmentid;
            $priorityid = (int) $priorityid;
            $where = array($db->quoteName('status') . ' = 1');
            $where[] = '(' . $db->quoteName('departmentid') . ' IS NULL OR ' . $db->quoteName('departmentid') . ' = 0 OR ' . $db->quoteName('departmentid') . ' = ' . $departmentid . ')';
            $where[] = '(' . $db->quoteName('priorityid') . ' IS NULL OR ' . $db->quoteName('priorityid') . ' = 0 OR ' . $db->quoteName('priorityid') . ' = ' . $priorityid . ')';
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__js_ticket_sla_rules'))
                ->where($where)
                ->order('CASE WHEN ' . $db->quoteName('departmentid') . ' = ' . $departmentid . ' THEN 2 WHEN ' . $db->quoteName('departmentid') . ' IS NULL OR ' . $db->quoteName('departmentid') . ' = 0 THEN 0 ELSE -10 END DESC')
                ->order('CASE WHEN ' . $db->quoteName('priorityid') . ' = ' . $priorityid . ' THEN 2 WHEN ' . $db->quoteName('priorityid') . ' IS NULL OR ' . $db->quoteName('priorityid') . ' = 0 THEN 0 ELSE -10 END DESC')
                ->order($db->quoteName('ordering') . ' ASC, ' . $db->quoteName('id') . ' ASC');
            $db->setQuery($query, 0, 1);
            return $db->loadObject();
        } catch (Throwable $e) {
            return null;
        }
    }

    public function applySlaToTicket($ticketid) {
        $this->lastSlaApplyError = '';
        $ticketid = (int) $ticketid;
        if ($ticketid <= 0) {
            $this->lastSlaApplyError = 'invalid_ticket';
            return false;
        }
        try {
            if (!$this->ensureTicketSlaColumnsSafely()) {
                $this->lastSlaApplyError = 'schema';
                return false;
            }

            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select('id, departmentid, priorityid, created')
                ->from($db->quoteName('#__js_ticket_tickets'))
                ->where($db->quoteName('id') . ' = ' . $ticketid);
            $db->setQuery($query);
            $ticket = $db->loadObject();
            if (!$ticket) {
                $this->lastSlaApplyError = 'ticket_not_found';
                return false;
            }
            $rule = $this->findMatchingSlaRule((int) $ticket->departmentid, (int) $ticket->priorityid);
            if (!$rule) {
                $this->lastSlaApplyError = 'no_rule';
                return false;
            }
            $created = !empty($ticket->created) && $ticket->created !== '0000-00-00 00:00:00' ? $ticket->created : Factory::getDate()->toSql();
            $firstDue = !empty($rule->first_response_minutes) ? $this->calculateDueDate($created, (int) $rule->first_response_minutes) : null;
            $resolutionDue = !empty($rule->resolution_minutes) ? $this->calculateDueDate($created, (int) $rule->resolution_minutes) : null;
            $now = Factory::getDate()->toSql();
            $breached = 0;
            if (($firstDue && $firstDue < $now) || ($resolutionDue && $resolutionDue < $now)) {
                $breached = 1;
            }
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__js_ticket_tickets'))
                ->set($db->quoteName('sla_ruleid') . ' = ' . (int) $rule->id)
                ->set($db->quoteName('first_response_due') . ' = ' . ($firstDue ? $db->quote($firstDue) : 'NULL'))
                ->set($db->quoteName('resolution_due') . ' = ' . ($resolutionDue ? $db->quote($resolutionDue) : 'NULL'))
                ->set($db->quoteName('sla_breached') . ' = ' . (int) $breached)
                ->where($db->quoteName('id') . ' = ' . $ticketid);
            $db->setQuery($query);
            $db->execute();
            $this->storeActivityEvent(array(
                'ticketid' => $ticketid,
                'source' => 'ticket_feature_wiring',
                'event_key' => 'sla_applied',
                'event_label' => 'SLA applied',
                'details' => 'SLA rule applied: ' . $rule->title,
                'metadata' => array('sla_ruleid' => (int) $rule->id, 'first_response_due' => $firstDue, 'resolution_due' => $resolutionDue),
                'visibility' => 'staff'
            ));
            return true;
        } catch (Throwable $e) {
            $this->lastSlaApplyError = 'database';
            try {
                $this->storeErrorLog(array(
                    'ticketid' => $ticketid,
                    'source' => 'sla',
                    'context' => 'apply_sla_to_ticket',
                    'severity' => 'warning',
                    'message' => 'Unable to apply SLA to ticket',
                    'technical_detail' => $e->getMessage(),
                    'metadata' => array('ticketid' => $ticketid),
                    'is_read' => 0
                ));
            } catch (Throwable $ignored) {
            }
            return false;
        }
    }

    public function getLastSlaApplyError() {
        return $this->lastSlaApplyError;
    }

    private function ensureTicketSlaColumnsSafely() {
        try {
            $db = Factory::getDbo();
            $columns = $db->getTableColumns('#__js_ticket_tickets', false);
            $columnMap = array(
                'first_response_due' => 'datetime DEFAULT NULL',
                'resolution_due' => 'datetime DEFAULT NULL',
                'sla_ruleid' => 'int(11) DEFAULT NULL',
                'sla_breached' => 'tinyint(1) DEFAULT NULL',
                'last_activity_at' => 'datetime DEFAULT NULL',
                'last_activity_by' => 'int(11) DEFAULT NULL',
            );
            foreach ($columnMap as $columnName => $definition) {
                if (!array_key_exists($columnName, $columns)) {
                    $query = 'ALTER TABLE ' . $db->quoteName('#__js_ticket_tickets') .
                        ' ADD ' . $db->quoteName($columnName) . ' ' . $definition;
                    $db->setQuery($query);
                    $db->execute();
                }
            }
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function getTicketSlaStatus($ticket) {
        if (!is_object($ticket)) {
            return null;
        }
        $rule = null;
        if (!empty($ticket->sla_ruleid)) {
            try {
                $db = Factory::getDbo();
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from($db->quoteName('#__js_ticket_sla_rules'))
                    ->where($db->quoteName('id') . ' = ' . (int) $ticket->sla_ruleid);
                $db->setQuery($query);
                $rule = $db->loadObject();
            } catch (Throwable $e) {
                $rule = null;
            }
        }
        return array(
            'rule' => $rule,
            'first_response_due' => isset($ticket->first_response_due) ? $ticket->first_response_due : null,
            'resolution_due' => isset($ticket->resolution_due) ? $ticket->resolution_due : null,
            'sla_breached' => isset($ticket->sla_breached) ? (int) $ticket->sla_breached : 0,
        );
    }

    private function getTagTitle($tagid) {
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__js_ticket_tags'))
                ->where($db->quoteName('id') . ' = ' . (int) $tagid);
            $db->setQuery($query);
            return (string) $db->loadResult();
        } catch (Throwable $e) {
            return '';
        }
    }


    private function getWatcherMailSender($ticketid) {
        $sender = array('email' => '', 'name' => '');
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select('email.email, email.name')
                ->from($db->quoteName('#__js_ticket_tickets', 'ticket'))
                ->join('LEFT', $db->quoteName('#__js_ticket_departments', 'department') . ' ON department.id = ticket.departmentid')
                ->join('LEFT', $db->quoteName('#__js_ticket_email', 'email') . ' ON email.id = department.emailid')
                ->where('ticket.id = ' . (int) $ticketid);
            $db->setQuery($query);
            $row = $db->loadObject();
            if ($row && !empty($row->email)) {
                $sender['email'] = (string) $row->email;
                $sender['name'] = !empty($row->name) ? (string) $row->name : '';
            }
        } catch (Throwable $e) {
        }

        if (empty($sender['email'])) {
            try {
                $emailConfig = $this->getJSModel('config')->getConfigByFor('email');
                $emailid = isset($emailConfig['alert_email']) ? (int) $emailConfig['alert_email'] : 0;
                if ($emailid > 0) {
                    $db = Factory::getDbo();
                    $query = $db->getQuery(true)
                        ->select($db->quoteName(array('email', 'name')))
                        ->from($db->quoteName('#__js_ticket_email'))
                        ->where($db->quoteName('id') . ' = ' . $emailid);
                    $db->setQuery($query);
                    $row = $db->loadObject();
                    if ($row && !empty($row->email)) {
                        $sender['email'] = (string) $row->email;
                        $sender['name'] = !empty($row->name) ? (string) $row->name : '';
                    }
                }
            } catch (Throwable $e) {
            }
        }

        if (empty($sender['email'])) {
            try {
                $joomlaConfig = Factory::getConfig();
                $sender['email'] = (string) $joomlaConfig->get('mailfrom');
                $sender['name'] = (string) $joomlaConfig->get('fromname');
            } catch (Throwable $e) {
            }
        }

        if (empty($sender['name'])) {
            try {
                $sender['name'] = (string) $this->getJSModel('config')->getConfigurationByName('title');
            } catch (Throwable $e) {
                $sender['name'] = 'Support Team';
            }
        }

        if (empty($sender['name'])) {
            $sender['name'] = 'Support Team';
        }

        return $sender;
    }

    private function buildWatcherReplyEmailBody($watcherName, $actorName, $subjectText, $trackingId, $replySummary, $ticketUrl) {
        $safeWatcher = htmlspecialchars((string) $watcherName, ENT_QUOTES, 'UTF-8');
        $safeActor = htmlspecialchars((string) $actorName, ENT_QUOTES, 'UTF-8');
        $safeSubject = htmlspecialchars((string) $subjectText, ENT_QUOTES, 'UTF-8');
        $safeTracking = htmlspecialchars((string) $trackingId, ENT_QUOTES, 'UTF-8');
        $safeSummary = nl2br(htmlspecialchars((string) $replySummary, ENT_QUOTES, 'UTF-8'));
        $safeUrl = htmlspecialchars((string) $ticketUrl, ENT_QUOTES, 'UTF-8');

        $body = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.55;color:#1f2937;">';
        $body .= '<p>Hello ' . $safeWatcher . ',</p>';
        $body .= '<p>A new support reply has been posted by <strong>' . $safeActor . '</strong>.</p>';
        $body .= '<p><strong>Ticket:</strong> ' . $safeSubject . '<br><strong>Ticket ID:</strong> ' . $safeTracking . '</p>';
        if ($safeSummary !== '') {
            $body .= '<div style="margin:16px 0;padding:14px 16px;border-left:4px solid #2563eb;background:#f8fafc;">' . $safeSummary . '</div>';
        }
        if ($safeUrl !== '') {
            $body .= '<p><a href="' . $safeUrl . '" target="_blank" rel="noopener" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:6px;padding:10px 14px;">Open ticket</a></p>';
        }
        $body .= '<p>You are receiving this because you are added as a watcher / CC on this ticket.</p>';
        $body .= '</div>';
        return $body;
    }

    private function buildWatcherTicketUrl($trackingId, $watcherEmail) {
        try {
            $token = (string) $trackingId . ',' . (string) $watcherEmail;
            $encoded = getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($token);
            return rtrim(\Joomla\CMS\Uri\Uri::root(), '/') . '/index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&jsticket=' . rawurlencode($encoded);
        } catch (Throwable $e) {
            return '';
        }
    }

    private function makePlainTextPreview($value, $maxLength = 900) {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }
        $maxLength = (int) $maxLength;
        if ($maxLength > 0 && strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength - 3) . '...';
        }
        return $text;
    }

    private function calculateDueDate($from, $minutes) {
        $timestamp = strtotime((string) $from);
        if (!$timestamp) {
            $timestamp = time();
        }
        return date('Y-m-d H:i:s', $timestamp + ((int) $minutes * 60));
    }

    private function touchTicketActivity($ticketid, $uid, $date) {
        try {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__js_ticket_tickets'))
                ->set($db->quoteName('last_activity_at') . ' = ' . $db->quote($date))
                ->set($db->quoteName('last_activity_by') . ' = ' . ($uid ? (int) $uid : 'NULL'))
                ->where($db->quoteName('id') . ' = ' . (int) $ticketid);
            $db->setQuery($query);
            $db->execute();
        } catch (Throwable $e) {
            // Column/table may not exist yet on very old installs. Never break ticket workflow.
        }
    }

    private function getCurrentUserData() {
        $data = array('uid' => 0, 'staffid' => 0, 'name' => null, 'email' => null);
        try {
            $identity = Factory::getApplication()->getIdentity();
            if ($identity) {
                $data['uid'] = isset($identity->id) ? (int) $identity->id : 0;
                $data['name'] = isset($identity->name) ? (string) $identity->name : null;
                $data['email'] = isset($identity->email) ? (string) $identity->email : null;
            }
        } catch (Throwable $e) {
            try {
                $identity = Factory::getUser();
                $data['uid'] = isset($identity->id) ? (int) $identity->id : 0;
                $data['name'] = isset($identity->name) ? (string) $identity->name : null;
                $data['email'] = isset($identity->email) ? (string) $identity->email : null;
            } catch (Throwable $ignored) {
            }
        }

        try {
            $current = JSSupportticketCurrentUser::getInstance();
            if ($current && method_exists($current, 'getStaffid')) {
                $data['staffid'] = (int) $current->getStaffid();
            }
        } catch (Throwable $e) {
        }

        return $data;
    }

    private function stringOrNull(array $data, $key, $default = null) {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return $default;
        }
        return (string) $data[$key];
    }


    private function truncateText($value, $maxLength) {
        if ($value === null) {
            return null;
        }
        $value = (string) $value;
        $maxLength = (int) $maxLength;
        if ($maxLength <= 0) {
            return $value;
        }
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($value, 'UTF-8') > $maxLength ? mb_substr($value, 0, $maxLength, 'UTF-8') : $value;
        }
        return strlen($value) > $maxLength ? substr($value, 0, $maxLength) : $value;
    }

    private function jsonOrNull($data) {
        if ($data === null || $data === '') {
            return null;
        }
        if (is_string($data)) {
            return $data;
        }
        $json = json_encode($data);
        return $json === false ? null : $json;
    }

    private function slugify($value) {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
        $value = trim((string) $value, '-');
        return $value;
    }
}
