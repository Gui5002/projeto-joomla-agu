<?php
/**
 * JS Support Ticket runtime error logger.
 *
 * Captures component runtime errors with safe request context and mirrors them
 * into the legacy system error table plus the newer structured error log table.
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class JSSupportTicketSystemErrorLogger {

    private static $registered = false;
    private static $isLogging = false;
    private static $loggedHashes = array();
    private static $previousErrorHandler = null;

    public static function register() {
        if (self::$registered) {
            return;
        }
        self::$registered = true;

        self::$previousErrorHandler = set_error_handler(array('JSSupportTicketSystemErrorLogger', 'handlePhpError'));
        register_shutdown_function(array('JSSupportTicketSystemErrorLogger', 'handleShutdown'));
    }

    public static function handlePhpError($errno, $errstr, $errfile = '', $errline = 0) {
        if (!(error_reporting() & $errno)) {
            return self::passToPreviousHandler($errno, $errstr, $errfile, $errline);
        }

        if (!self::shouldCapture($errno, $errfile)) {
            return self::passToPreviousHandler($errno, $errstr, $errfile, $errline);
        }

        self::log(array(
            'severity' => self::severityFromNumber($errno),
            'source' => 'php_runtime',
            'context' => self::routeContext(),
            'message' => $errstr,
            'type' => self::phpErrorName($errno),
            'file' => $errfile,
            'line' => (int) $errline,
            'metadata' => array(
                'errno' => (int) $errno,
            ),
        ));

        return self::passToPreviousHandler($errno, $errstr, $errfile, $errline);
    }

    private static function passToPreviousHandler($errno, $errstr, $errfile, $errline) {
        if (is_callable(self::$previousErrorHandler)) {
            return call_user_func(self::$previousErrorHandler, $errno, $errstr, $errfile, $errline);
        }
        return false;
    }

    public static function handleShutdown() {
        $error = error_get_last();
        if (!$error || empty($error['type'])) {
            return;
        }

        $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
        if (!in_array((int) $error['type'], $fatalTypes, true)) {
            return;
        }

        $file = isset($error['file']) ? $error['file'] : '';
        if (!self::isComponentPath($file)) {
            return;
        }

        self::log(array(
            'severity' => 'fatal',
            'source' => 'php_shutdown',
            'context' => self::routeContext(),
            'message' => isset($error['message']) ? $error['message'] : 'Fatal error',
            'type' => self::phpErrorName((int) $error['type']),
            'file' => $file,
            'line' => isset($error['line']) ? (int) $error['line'] : 0,
            'metadata' => array(
                'errno' => (int) $error['type'],
            ),
        ));
    }

    public static function log($payload) {
        if (self::$isLogging) {
            return false;
        }

        self::$isLogging = true;

        try {
            $normalized = self::normalizePayload($payload);
            $hash = self::requestHash($normalized);
            if (isset(self::$loggedHashes[$hash])) {
                self::$isLogging = false;
                return false;
            }
            self::$loggedHashes[$hash] = true;

            $db = Factory::getDbo();
            $now = Factory::getDate()->toSql();
            $user = Factory::getApplication()->getIdentity();
            $uid = isset($user->id) ? (int) $user->id : 0;
            $legacy = self::formatLegacyMessage($normalized);

            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__js_ticket_system_errors'))
                ->columns($db->quoteName(array('uid', 'error', 'isview', 'created')))
                ->values(($uid ?: 'NULL') . ',' . $db->quote($legacy) . ',0,' . $db->quote($now));
            $db->setQuery($query);
            $db->execute();

            self::storeModernLog($db, $uid, $normalized, $legacy, $now);
            self::$isLogging = false;
            return true;
        } catch (Throwable $e) {
            self::$isLogging = false;
            return false;
        }
    }

    public static function normalizePayload($payload) {
        if ($payload instanceof Throwable) {
            return array(
                'severity' => 'exception',
                'source' => 'exception',
                'context' => self::routeContext(),
                'message' => $payload->getMessage(),
                'type' => get_class($payload),
                'file' => $payload->getFile(),
                'line' => (int) $payload->getLine(),
                'technical_detail' => $payload->getTraceAsString(),
                'metadata' => self::requestMetadata(),
            );
        }

        if (!is_array($payload)) {
            $payload = array('message' => (string) $payload);
        }

        return array(
            'severity' => isset($payload['severity']) ? (string) $payload['severity'] : 'error',
            'source' => isset($payload['source']) ? (string) $payload['source'] : 'system',
            'context' => isset($payload['context']) ? (string) $payload['context'] : self::routeContext(),
            'message' => isset($payload['message']) ? self::stringify($payload['message']) : 'Unknown error',
            'type' => isset($payload['type']) ? (string) $payload['type'] : 'Error',
            'file' => isset($payload['file']) ? (string) $payload['file'] : '',
            'line' => isset($payload['line']) ? (int) $payload['line'] : 0,
            'technical_detail' => isset($payload['technical_detail']) ? self::stringify($payload['technical_detail']) : '',
            'metadata' => isset($payload['metadata']) && is_array($payload['metadata']) ? array_merge(self::requestMetadata(), $payload['metadata']) : self::requestMetadata(),
        );
    }

    public static function formatLegacyMessage($payload) {
        $payload = self::normalizePayload($payload);
        $meta = self::requestMetadata();
        $metadata = array_merge($meta, isset($payload['metadata']) ? $payload['metadata'] : array());
        $metadata = self::redactArray($metadata);

        $lines = array();
        $lines[] = 'Severity: ' . $payload['severity'];
        $lines[] = 'Source: ' . $payload['source'];
        $lines[] = 'Context: ' . $payload['context'];
        $lines[] = 'Type: ' . $payload['type'];
        $lines[] = 'Message: ' . self::redactString($payload['message']);

        if (!empty($payload['file'])) {
            $lines[] = 'File: ' . self::safePath($payload['file']);
        }
        if (!empty($payload['line'])) {
            $lines[] = 'Line: ' . (int) $payload['line'];
        }

        if (!empty($payload['technical_detail'])) {
            $lines[] = 'Technical detail: ' . self::truncate(self::redactString($payload['technical_detail']), 4000);
        }

        $lines[] = 'Request: ' . self::truncate(json_encode($metadata), 2000);
        $lines[] = 'Hash: ' . substr(sha1(implode('|', $lines)), 0, 16);

        return self::truncate(implode("\n", $lines), 12000);
    }

    private static function storeModernLog($db, $uid, $payload, $legacy, $now) {
        try {
            $tables = $db->getTableList();
            $prefix = $db->getPrefix();
            if (!in_array($prefix . 'js_ticket_error_logs', $tables, true)) {
                return false;
            }

            $metadata = array_merge(self::requestMetadata(), isset($payload['metadata']) ? $payload['metadata'] : array());
            $metadata = self::redactArray($metadata);
            $message = self::truncate(self::redactString($payload['message']), 4000);
            $technical = $payload['technical_detail'] ? self::truncate(self::redactString($payload['technical_detail']), 12000) : $legacy;

            $query = $db->getQuery(true)
                ->insert($db->quoteName('#__js_ticket_error_logs'))
                ->columns($db->quoteName(array('uid', 'staffid', 'ticketid', 'source', 'context', 'severity', 'message', 'technical_detail', 'metadata', 'is_read', 'created')))
                ->values(
                    ($uid ?: 'NULL') . ',NULL,NULL,' .
                    $db->quote(self::truncate($payload['source'], 100)) . ',' .
                    $db->quote(self::truncate($payload['context'], 255)) . ',' .
                    $db->quote(self::truncate($payload['severity'], 30)) . ',' .
                    $db->quote($message) . ',' .
                    $db->quote($technical) . ',' .
                    $db->quote(json_encode($metadata)) . ',0,' .
                    $db->quote($now)
                );
            $db->setQuery($query);
            $db->execute();
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    private static function shouldCapture($errno, $file) {
        if (!self::isComponentPath($file)) {
            return false;
        }

        $captured = array(
            E_ERROR, E_WARNING, E_PARSE, E_CORE_ERROR, E_CORE_WARNING,
            E_COMPILE_ERROR, E_COMPILE_WARNING, E_USER_ERROR, E_USER_WARNING,
            E_RECOVERABLE_ERROR
        );

        return in_array((int) $errno, $captured, true);
    }

    private static function isComponentPath($file) {
        $file = str_replace('\\', '/', (string) $file);
        return $file !== '' && (strpos($file, '/com_jssupportticket/') !== false || strpos($file, 'components/com_jssupportticket/') !== false);
    }

    private static function routeContext() {
        try {
            $input = Factory::getApplication()->input;
            $option = $input->getCmd('option', 'com_jssupportticket');
            $controller = $input->getCmd('c', 'jssupportticket');
            $task = $input->getCmd('task', 'display');
            $layout = $input->getCmd('layout', '');
            return trim($option . '.' . $controller . '.' . $task . ($layout !== '' ? '.' . $layout : ''), '.');
        } catch (Throwable $e) {
            return 'com_jssupportticket';
        }
    }

    private static function requestMetadata() {
        $metadata = array();
        try {
            $app = Factory::getApplication();
            $input = $app->input;
            $user = $app->getIdentity();
            $metadata['url'] = Uri::getInstance()->toString(array('path', 'query'));
            $metadata['method'] = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
            $metadata['option'] = $input->getCmd('option', 'com_jssupportticket');
            $metadata['controller'] = $input->getCmd('c', 'jssupportticket');
            $metadata['task'] = $input->getCmd('task', 'display');
            $metadata['layout'] = $input->getCmd('layout', '');
            $metadata['uid'] = isset($user->id) ? (int) $user->id : 0;
            $metadata['ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $metadata['user_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $metadata['php_version'] = PHP_VERSION;
            $metadata['joomla_version'] = defined('JVERSION') ? JVERSION : '';
        } catch (Throwable $e) {
        }
        return $metadata;
    }

    private static function requestHash($payload) {
        return sha1($payload['severity'] . '|' . $payload['source'] . '|' . $payload['context'] . '|' . $payload['message'] . '|' . $payload['file'] . '|' . $payload['line']);
    }

    private static function phpErrorName($errno) {
        $map = array(
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            // 2048 is E_STRICT. The constant itself is deprecated as of PHP 8.4,
            // so naming it here made the error logger emit a deprecation notice
            // of its own. PHP 8 never raises E_STRICT, but the number is kept so
            // a log written by an older PHP still resolves to a readable name.
            2048 => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        );
        return isset($map[$errno]) ? $map[$errno] : 'E_UNKNOWN';
    }

    private static function severityFromNumber($errno) {
        $fatal = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR);
        if (in_array((int) $errno, $fatal, true)) {
            return 'fatal';
        }
        return 'warning';
    }

    private static function stringify($value) {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }
        if ($value instanceof Throwable) {
            return $value->getMessage();
        }
        return print_r($value, true);
    }

    private static function safePath($path) {
        $path = str_replace('\\', '/', (string) $path);
        $root = defined('JPATH_ROOT') ? str_replace('\\', '/', JPATH_ROOT) : '';
        if ($root !== '' && strpos($path, $root) === 0) {
            return '[site-root]' . substr($path, strlen($root));
        }
        return $path;
    }

    private static function redactArray($value) {
        if (!is_array($value)) {
            return self::redactString((string) $value);
        }
        $clean = array();
        foreach ($value as $key => $item) {
            $lowerKey = strtolower((string) $key);
            if (preg_match('/(password|passwd|secret|token|apikey|api_key|key|authorization|cookie|session)/i', $lowerKey)) {
                $clean[$key] = '[redacted]';
            } elseif (is_array($item)) {
                $clean[$key] = self::redactArray($item);
            } else {
                $clean[$key] = self::redactString((string) $item);
            }
        }
        return $clean;
    }

    private static function redactString($value) {
        $value = (string) $value;
        $value = preg_replace('/(password|passwd|secret|token|apikey|api_key|authorization|cookie|session)(\s*[=:]\s*)([^\s&]+)/i', '$1$2[redacted]', $value);
        $value = preg_replace('/([?&](?:cron_token|token|key|secret|password)\=)[^&\s]+/i', '$1[redacted]', $value);
        return $value;
    }

    private static function truncate($value, $length) {
        $value = (string) $value;
        if (function_exists('mb_strlen') && mb_strlen($value) > $length) {
            return mb_substr($value, 0, $length) . '...';
        }
        if (strlen($value) > $length) {
            return substr($value, 0, $length) . '...';
        }
        return $value;
    }
}
