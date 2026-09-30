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

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelSystemErrors extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function updateSystemErrors($error) {
        $row = $this->getTable('systemerrors');
        $user = Factory::getApplication()->getIdentity();
        $uid = isset($user->id) ? (int) $user->id : 0;
        $normalized = $this->normalizeErrorPayload($error);
        $legacyMessage = $this->formatErrorForLegacyTable($normalized);

        $data = array();
        $data['uid'] = $uid;
        $data['error'] = $legacyMessage;
        $data['isview'] = 0; // 0 = not viewed, 1 = viewed
        $data['created'] = Factory::getDate()->toSql();

        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return 2;
        }
        if (!$row->store()) {
            $this->setError($row->getError());
            return false;
        }

        // Mirror legacy system errors into the modern error_logs table without
        // interrupting existing behavior. Values are already redacted and safe.
        try {
            $this->getJSModel('featurefoundation')->storeErrorLog(array(
                'uid' => $uid,
                'source' => $normalized['source'],
                'context' => $normalized['context'],
                'severity' => $normalized['severity'],
                'message' => $normalized['message'],
                'technical_detail' => $legacyMessage,
                'metadata' => $normalized['metadata'],
            ));
        } catch (Throwable $e) {
        }

        return true;
    }

    private function normalizeErrorPayload($error) {
        if (class_exists('JSSupportTicketSystemErrorLogger', false)) {
            return JSSupportTicketSystemErrorLogger::normalizePayload($error);
        }

        if ($error instanceof Throwable) {
            return array(
                'severity' => 'exception',
                'source' => 'legacy_system_errors',
                'context' => $this->getRequestContext(),
                'message' => $error->getMessage(),
                'type' => get_class($error),
                'file' => $error->getFile(),
                'line' => (int) $error->getLine(),
                'technical_detail' => $error->getTraceAsString(),
                'metadata' => $this->getRequestMetadata(),
            );
        }

        if (is_array($error)) {
            return array(
                'severity' => isset($error['severity']) ? (string) $error['severity'] : 'error',
                'source' => isset($error['source']) ? (string) $error['source'] : 'legacy_system_errors',
                'context' => isset($error['context']) ? (string) $error['context'] : $this->getRequestContext(),
                'message' => isset($error['message']) ? $this->stringify($error['message']) : 'Unknown error',
                'type' => isset($error['type']) ? (string) $error['type'] : 'Error',
                'file' => isset($error['file']) ? (string) $error['file'] : '',
                'line' => isset($error['line']) ? (int) $error['line'] : 0,
                'technical_detail' => isset($error['technical_detail']) ? $this->stringify($error['technical_detail']) : '',
                'metadata' => isset($error['metadata']) && is_array($error['metadata']) ? $this->redactArray($error['metadata']) : $this->getRequestMetadata(),
            );
        }

        return array(
            'severity' => 'error',
            'source' => 'legacy_system_errors',
            'context' => $this->getRequestContext(),
            'message' => $this->stringify($error),
            'type' => 'Error',
            'file' => '',
            'line' => 0,
            'technical_detail' => '',
            'metadata' => $this->getRequestMetadata(),
        );
    }

    private function formatErrorForLegacyTable($payload) {
        if (class_exists('JSSupportTicketSystemErrorLogger', false)) {
            return JSSupportTicketSystemErrorLogger::formatLegacyMessage($payload);
        }

        $lines = array();
        $lines[] = 'Severity: ' . $payload['severity'];
        $lines[] = 'Source: ' . $payload['source'];
        $lines[] = 'Context: ' . $payload['context'];
        $lines[] = 'Type: ' . $payload['type'];
        $lines[] = 'Message: ' . $this->redactString($payload['message']);
        if (!empty($payload['file'])) {
            $lines[] = 'File: ' . $payload['file'];
        }
        if (!empty($payload['line'])) {
            $lines[] = 'Line: ' . (int) $payload['line'];
        }
        if (!empty($payload['technical_detail'])) {
            $lines[] = 'Technical detail: ' . $this->truncate($this->redactString($payload['technical_detail']), 4000);
        }
        $lines[] = 'Request: ' . $this->truncate(json_encode($this->redactArray($payload['metadata'])), 2000);
        $lines[] = 'Hash: ' . substr(sha1(implode('|', $lines)), 0, 16);
        return $this->truncate(implode("\n", $lines), 12000);
    }

    private function getRequestContext() {
        try {
            $input = Factory::getApplication()->input;
            return 'com_jssupportticket.' . $input->getCmd('c', 'jssupportticket') . '.' . $input->getCmd('task', 'display') . '.' . $input->getCmd('layout', '');
        } catch (Throwable $e) {
            return 'com_jssupportticket';
        }
    }

    private function getRequestMetadata() {
        $metadata = array();
        try {
            $app = Factory::getApplication();
            $input = $app->input;
            $user = $app->getIdentity();
            $metadata['method'] = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
            $metadata['option'] = $input->getCmd('option', 'com_jssupportticket');
            $metadata['controller'] = $input->getCmd('c', 'jssupportticket');
            $metadata['task'] = $input->getCmd('task', 'display');
            $metadata['layout'] = $input->getCmd('layout', '');
            $metadata['uid'] = isset($user->id) ? (int) $user->id : 0;
            $metadata['php_version'] = PHP_VERSION;
            $metadata['joomla_version'] = defined('JVERSION') ? JVERSION : '';
        } catch (Throwable $e) {
        }
        return $metadata;
    }

    private function stringify($value) {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }
        return print_r($value, true);
    }

    private function redactArray($value) {
        if (!is_array($value)) {
            return $this->redactString((string) $value);
        }
        $clean = array();
        foreach ($value as $key => $item) {
            if (preg_match('/(password|passwd|secret|token|apikey|api_key|key|authorization|cookie|session)/i', (string) $key)) {
                $clean[$key] = '[redacted]';
            } elseif (is_array($item)) {
                $clean[$key] = $this->redactArray($item);
            } else {
                $clean[$key] = $this->redactString((string) $item);
            }
        }
        return $clean;
    }

    private function redactString($value) {
        $value = (string) $value;
        $value = preg_replace('/(password|passwd|secret|token|apikey|api_key|authorization|cookie|session)(\s*[=:]\s*)([^\s&]+)/i', '$1$2[redacted]', $value);
        $value = preg_replace('/([?&](?:cron_token|token|key|secret|password)\=)[^&\s]+/i', '$1[redacted]', $value);
        return $value;
    }

    private function truncate($value, $length) {
        $value = (string) $value;
        if (function_exists('mb_strlen') && mb_strlen($value) > $length) {
            return mb_substr($value, 0, $length) . '...';
        }
        if (strlen($value) > $length) {
            return substr($value, 0, $length) . '...';
        }
        return $value;
    }
    
    function getSystemErrors($limitstart, $limit) {
        $db = $this->getDbo();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_system_errors` ";
        $db->setQuery($query);
        $total = $db->loadResult();

        $query = "SELECT error.*
					FROM `#__js_ticket_system_errors` AS error
                    ORDER BY error.created DESC, error.id DESC";
        $db->setQuery($query, $limitstart, $limit);
        $systemerrors = $db->loadObjectList();
        $result[0] = $systemerrors;
        $result[1] = $total;
        return $result;
    }

    function getErrorDetail($id) {
        if (!is_numeric($id))
            return False;
        $db = $this->getDbo();
        $this->updateIsView($id);
        $query = "SELECT error.*
					FROM `#__js_ticket_system_errors` AS error
					WHERE error.id = " . (int) $id;
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }

    function updateIsView($id) {
        if (!is_numeric($id))
            return False;
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_system_errors` set isview = 1 WHERE id = " . (int) $id;
        $db->setQuery($query);
        $db->execute();
    }

}

?>
