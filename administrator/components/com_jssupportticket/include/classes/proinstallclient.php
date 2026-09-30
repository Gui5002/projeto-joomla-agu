<?php
/**
 * v1 Pro installer client. Uses signed JSON and package hashes, no eval.
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Language\Text;

require_once JPATH_ADMINISTRATOR . '/components/com_jssupportticket/include/classes/prolicense.php';

class JSSTProInstallClient
{
    private const ENDPOINT = 'https://setup.joomsky.com/jssupportticketjm/v1/pro/index.php';

    public function getVersions(array $data): array
    {
        $payload = $this->request(array_merge($this->basePayload($data), ['action' => 'version_list']));
        return $payload['data'] ?? [];
    }

    public function prepareAndInstall(array $data): void
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
        $payload = $this->request(array_merge($this->basePayload($data), [
            'action' => 'prepare_install',
            'productversioninstall' => (string)($data['productversioninstall'] ?? ''),
            'target_version' => (string)($data['productversioninstall'] ?? ''),
            'level' => 'level2',
            'installnew' => (string)($data['installnew'] ?? '0'),
        ]));
        $plan = $payload['data'] ?? [];
        if (empty($plan['packages']) || empty($plan['entitlement'])) {
            throw new RuntimeException('Installer server returned an incomplete install plan.');
        }
        $this->debugLog('Install plan received', [
            'target_version' => (string)($plan['target_version'] ?? ''),
            'mode' => (string)($plan['mode'] ?? ''),
            'folder' => (string)($plan['folder'] ?? ''),
            'package_count' => is_array($plan['packages'] ?? null) ? count($plan['packages']) : 0,
        ]);

        $this->installPlan($plan);
        $this->complete($plan, $data);

        $this->debugLog('Installer flow finished successfully', [
            'target_version' => (string)($plan['target_version'] ?? ''),
        ]);
    }

    private function basePayload(array $data): array
    {
        return [
            'transactionkey' => (string)($data['transactionkey'] ?? ''),
            'domain' => (string)($data['domain'] ?? ''),
            'producttype' => (string)($data['producttype'] ?? 'free'),
            'productcode' => 'jssupportticket',
            'productversion' => (string)($data['productversion'] ?? ''),
            'JVERSION' => (string)($data['JVERSION'] ?? JVERSION),
            'count' => (string)($data['count_config'] ?? $data['count'] ?? '0'),
            'count_config' => (string)($data['count_config'] ?? $data['count'] ?? '0'),
        ];
    }

    private function request(array $payload): array
    {
        $this->debugLog('Installer request started', [
            'endpoint' => self::ENDPOINT,
            'payload' => $payload,
        ]);

        $ch = curl_init(self::ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $this->configureCurlSsl($ch);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response === false || $response === '') {
            $this->debugLog('Installer request failed before response', [
                'status' => $status,
                'curl_error' => $error,
            ]);
            throw new RuntimeException('Unable to connect to the installer server. ' . $this->formatCurlError($error));
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            $this->debugLog('Installer server returned invalid JSON', [
                'status' => $status,
                'response_preview' => substr((string)$response, 0, 1200),
            ]);
            throw new RuntimeException('Installer server returned an invalid response. Please check the local installer log.');
        }

        $this->debugLog('Installer response received', [
            'status' => $status,
            'success' => !empty($decoded['success']),
            'code' => (string)($decoded['code'] ?? ''),
            'request_id' => (string)($decoded['request_id'] ?? ''),
            'message' => (string)($decoded['message'] ?? ''),
        ]);

        if (empty($decoded['success'])) {
            throw new RuntimeException($this->serverErrorMessage($decoded));
        }
        if (empty($decoded['data']) || empty($decoded['signature'])) {
            throw new RuntimeException('Installer server response is not signed.');
        }
        $this->verifySignature($decoded['data'], (string)$decoded['signature']);
        return $decoded;
    }

    private function verifySignature(array $data, string $signature): void
    {
        $sig = base64_decode($signature, true);
        if ($sig === false) {
            throw new RuntimeException('Installer response signature is invalid.');
        }
        $ok = openssl_verify(JSSTProLicense::canonicalJson($data), $sig, $this->publicKey(), OPENSSL_ALGO_SHA256);
        if ($ok !== 1) {
            throw new RuntimeException('Installer response signature verification failed.');
        }
    }

    private function publicKey(): string
    {
        $ref = new ReflectionClass('JSSTProLicense');
        $const = $ref->getConstant('PUBLIC_KEY');
        return (string)$const;
    }

    private function installPlan(array $plan): void
    {
        $root = JPATH_ROOT;
        $tmp = $root . '/tmp/jssupportticket';
        $backup = $root . '/tmp/jssupportticket-backup-' . date('Ymd-His');
        $this->removeDir($tmp);
        $this->mkdir($tmp);
        $downloaded = [];

        $this->debugLog('Package download stage started', [
            'tmp' => $tmp,
            'package_count' => is_array($plan['packages'] ?? null) ? count($plan['packages']) : 0,
        ]);

        foreach ($plan['packages'] as $package) {
            $name = (string)($package['name'] ?? '');
            $url = (string)($package['url'] ?? '');
            $sha = (string)($package['sha256'] ?? '');
            if ($name === '' || $url === '' || $sha === '') {
                continue;
            }
            $target = $tmp . '/' . $name . '.zip';
            $this->download($url, $target);
            $actualHash = hash_file('sha256', $target);
            if (!hash_equals($sha, $actualHash)) {
                $this->debugLog('Package hash verification failed', [
                    'name' => $name,
                    'expected' => $sha,
                    'actual' => $actualHash,
                ]);
                throw new RuntimeException('Package hash verification failed for ' . $name . '.');
            }

            $this->debugLog('Package hash verified', [
                'name' => $name,
                'size' => is_file($target) ? filesize($target) : 0,
            ]);

            $downloaded[$name] = $target;
        }

        $this->debugLog('Package download stage completed', [
            'downloaded' => array_keys($downloaded),
        ]);

        $adminDest = JPATH_ADMINISTRATOR . '/components/com_jssupportticket';
        $siteDest = JPATH_ROOT . '/components/com_jssupportticket';
        $this->debugLog('Backup stage started', [
            'backup' => $backup,
            'admin_dest' => $adminDest,
            'site_dest' => $siteDest,
        ]);

        $this->mkdir($backup);
        if (is_dir($adminDest)) {
            $this->copyDir($adminDest, $backup . '/admin');
            $this->debugLog('Admin backup completed', ['path' => $backup . '/admin']);
        }
        if (is_dir($siteDest)) {
            $this->copyDir($siteDest, $backup . '/site');
            $this->debugLog('Site backup completed', ['path' => $backup . '/site']);
        }

        try {
            if (!empty($downloaded['admin'])) {
                $this->debugLog('Admin component extraction started', ['zip' => $downloaded['admin'], 'destination' => $adminDest]);
                $this->extractComponentZip($downloaded['admin'], $adminDest);
                $this->debugLog('Admin component extraction completed', ['destination' => $adminDest]);
            }
            if (!empty($downloaded['site'])) {
                $this->debugLog('Site component extraction started', ['zip' => $downloaded['site'], 'destination' => $siteDest]);
                $this->extractComponentZip($downloaded['site'], $siteDest);
                $this->debugLog('Site component extraction completed', ['destination' => $siteDest]);
            }
            if (!empty($downloaded['extensions'])) {
                $this->debugLog('Extensions installation started', ['zip' => $downloaded['extensions']]);
                $this->installExtensions($downloaded['extensions'], $tmp . '/extensions');
                $this->debugLog('Extensions installation completed');
            }
            if (!empty($downloaded['lang'])) {
                $this->debugLog('Language installation started', ['zip' => $downloaded['lang']]);
                $this->installLanguage($downloaded['lang'], $tmp . '/lang');
                $this->debugLog('Language installation completed');
            }
            if (!empty($downloaded['sql'])) {
                $this->debugLog('SQL installation started', ['zip' => $downloaded['sql']]);
                $this->runSql($downloaded['sql'], $tmp . '/sql');
                $this->debugLog('SQL installation completed');
            }

            $this->debugLog('Entitlement write started');
            JSSTProLicense::writeEntitlement($plan['entitlement']);
            $this->debugLog('Entitlement write completed');

            $this->debugLog('Host config update started');
            $this->storeHostConfig($plan['entitlement']['data'] ?? []);
            $this->debugLog('Host config update completed');

            $this->debugLog('Manifest version update started', ['version' => (string)($plan['target_version'] ?? '')]);
            $this->updateManifestVersion((string)($plan['target_version'] ?? ''));
            $this->debugLog('Manifest version update completed');
        } catch (Throwable $e) {
            $this->debugLog('Local installation failed, restoring backup', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if (is_dir($backup . '/admin')) {
                $this->removeDir($adminDest);
                $this->copyDir($backup . '/admin', $adminDest);
            }
            if (is_dir($backup . '/site')) {
                $this->removeDir($siteDest);
                $this->copyDir($backup . '/site', $siteDest);
            }
            throw $e;
        } finally {
            $this->debugLog('Local tmp cleanup started', ['tmp' => $tmp]);
            $this->removeDir($tmp);
            $this->debugLog('Local tmp cleanup completed', ['tmp' => $tmp]);
        }
    }

    private function complete(array $plan, array $data): void
    {
        try {
            $this->debugLog('Server complete cleanup started', [
                'folder' => (string)($plan['folder'] ?? ''),
                'target_version' => (string)($plan['target_version'] ?? ''),
            ]);

            $this->request([
                'action' => 'complete_install',
                'install_token' => (string)($plan['install_token'] ?? ''),
                'folder' => (string)($plan['folder'] ?? ''),
                'transactionkey' => (string)($data['transactionkey'] ?? ''),
                'productversion' => (string)($plan['target_version'] ?? ''),
                'target_version' => (string)($plan['target_version'] ?? ''),
                'versiontype' => (string)($plan['version_type'] ?? 'professional'),
                'JVERSION' => (string)($data['JVERSION'] ?? JVERSION),
            ]);

            $this->debugLog('Server complete cleanup completed', [
                'folder' => (string)($plan['folder'] ?? ''),
            ]);
        } catch (Throwable $e) {
            $this->debugLog('Server complete cleanup failed', [
                'error' => $e->getMessage(),
                'folder' => (string)($plan['folder'] ?? ''),
            ]);
            // Installation is already complete locally. Server cleanup can be retried manually.
        }
    }

    private function serverErrorMessage(array $decoded): string
    {
        $message = trim((string)($decoded['message'] ?? 'Installer server rejected the request.'));
        $requestId = trim((string)($decoded['request_id'] ?? ''));

        if ($requestId !== '' && stripos($message, $requestId) === false) {
            $message .= ' Request ID: ' . $requestId . '.';
        }

        $message .= ' Local log: ' . $this->debugLogPath() . '.';

        return $message;
    }

    private function shouldWriteInstallerLog(string $message): bool
    {
        if (defined('JSST_PRO_INSTALLER_VERBOSE_LOGS') && JSST_PRO_INSTALLER_VERBOSE_LOGS) {
            return true;
        }

        // Production default: keep the local Joomla log clean.
        // Write only failures and one final success line.
        return (bool)preg_match(
            '/failed|error|invalid|unable|rejected|exception|Extension install summary|Installer flow finished successfully/i',
            $message
        );
    }

    private function maskSensitiveUrl(string $url): string
    {
        return preg_replace_callback('/([?&](?:token|transactionkey|key|signature)=)([^&]+)/i', function (array $matches): string {
            $value = (string)$matches[2];
            $length = strlen($value);

            if ($length <= 8) {
                $masked = str_repeat('*', $length);
            } else {
                $masked = substr($value, 0, 4) . str_repeat('*', max(4, $length - 8)) . substr($value, -4);
            }

            return $matches[1] . $masked;
        }, $url) ?? $url;
    }

    private function debugLog(string $message, array $context = []): void
    {
        if (!$this->shouldWriteInstallerLog($message)) {
            return;
        }

        try {
            $path = $this->debugLogPath();
            $dir = dirname($path);

            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            $line = date('Y-m-d H:i:s') . ' ' . $message;
            $context = $this->sanitizeLogContext($context);

            if ($context) {
                $encoded = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                if (is_string($encoded)) {
                    $line .= ' ' . $encoded;
                }
            }

            @file_put_contents($path, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (Throwable $e) {
            // Never break installer flow because of debug logging.
        }
    }

    private function debugLogPath(): string
    {
        try {
            $config = Factory::getConfig();
            $logPath = (string)$config->get('log_path', '');
            if ($logPath !== '') {
                return rtrim($logPath, '/\\') . '/jssupportticket-pro-installer.log';
            }
        } catch (Throwable $e) {
            // Fallback below.
        }

        return JPATH_ADMINISTRATOR . '/logs/jssupportticket-pro-installer.log';
    }

    private function sanitizeLogContext(mixed $value): mixed
    {
        if (is_array($value)) {
            $clean = [];

            foreach ($value as $key => $item) {
                $keyString = strtolower((string)$key);

                if (preg_match('/(key|token|secret|password|signature|authorization)/', $keyString)) {
                    $clean[$key] = $this->maskValue((string)$item);
                } else {
                    $clean[$key] = $this->sanitizeLogContext($item);
                }
            }

            return $clean;
        }

        if (is_object($value)) {
            return '[object ' . get_class($value) . ']';
        }

        if (is_string($value)) {
            if (preg_match('/[?&](token|transactionkey|key|signature)=/i', $value)) {
                $value = $this->maskSensitiveUrl($value);
            }

            if (strlen($value) > 600) {
                return substr($value, 0, 600) . '...[truncated]';
            }
        }

        return $value;
    }

    private function maskValue(string $value): string
    {
        $value = trim($value);
        $length = strlen($value);

        if ($length === 0) {
            return '';
        }

        if ($length <= 8) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, 4) . str_repeat('*', max(4, $length - 8)) . substr($value, -4);
    }

    private function formatCurlError(string $error): string
    {
        $error = trim($error);
        if ($error === '') {
            return '';
        }

        if (stripos($error, 'certificate') !== false && $this->isLocalDevelopmentSite()) {
            return $error . ' Local development mode detected, so SSL verification has been relaxed for installer requests. Please refresh and try again.';
        }

        if (stripos($error, 'certificate') !== false) {
            return $error . ' Please check the server PHP curl.cainfo / openssl.cafile setting or install a valid CA bundle.';
        }

        return $error;
    }

    private function configureCurlSsl($ch): void
    {
        if ($this->isLocalDevelopmentSite()) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            return;
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $customCa = $this->findUsableCaBundle();
        if ($customCa !== '') {
            curl_setopt($ch, CURLOPT_CAINFO, $customCa);
        }
    }

    private function isLocalDevelopmentSite(): bool
    {
        if (defined('JSST_PRO_INSTALLER_DEV_SSL_SKIP') && JSST_PRO_INSTALLER_DEV_SSL_SKIP) {
            return true;
        }

        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
        $host = preg_replace('/:\d+$/', '', $host);

        if ($host === '' && defined('JPATH_ROOT')) {
            $host = strtolower((string)parse_url((string)\Joomla\CMS\Uri\Uri::root(), PHP_URL_HOST));
        }

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
            return true;
        }

        if (str_ends_with($host, '.test') || str_ends_with($host, '.local') || str_ends_with($host, '.localhost')) {
            return true;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        return false;
    }

    private function findUsableCaBundle(): string
    {
        $candidates = [
            (string)ini_get('curl.cainfo'),
            (string)ini_get('openssl.cafile'),
            JPATH_ROOT . '/cacert.pem',
            JPATH_ROOT . '/tmp/cacert.pem',
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim($candidate);
            if ($candidate !== '' && is_file($candidate) && is_readable($candidate)) {
                return $candidate;
            }
        }

        return '';
    }

    private function download(string $url, string $target): void
    {
        $this->debugLog('Package download started', [
            'url' => $url,
            'target' => $target,
        ]);

        $fp = fopen($target, 'w+b');
        if (!$fp) {
            throw new RuntimeException('Cannot write download file: ' . $target);
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        $this->configureCurlSsl($ch);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        $ok = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);
        if (!$ok || $status >= 400 || !is_file($target) || filesize($target) === 0) {
            $this->debugLog('Package download failed', [
                'status' => $status,
                'curl_error' => $error,
                'target_exists' => is_file($target),
                'target_size' => is_file($target) ? filesize($target) : 0,
            ]);
            throw new RuntimeException('Package download failed. ' . $this->formatCurlError($error));
        }

        $this->debugLog('Package download completed', [
            'status' => $status,
            'target' => $target,
            'size' => is_file($target) ? filesize($target) : 0,
        ]);
    }

    private function extractComponentZip(string $zip, string $destination): void
    {
        $tmp = dirname($zip) . '/extract-' . pathinfo($zip, PATHINFO_FILENAME);
        $this->removeDir($tmp);
        $this->mkdir($tmp);
        $this->extractZip($zip, $tmp);
        $source = $tmp;
        if (is_dir($tmp . '/com_jssupportticket')) {
            $source = $tmp . '/com_jssupportticket';
        }
        $this->removeDir($destination);
        $this->copyDir($source, $destination);
        $this->removeDir($tmp);
    }

    /**
     * Installs the modules and plugins bundled in extensions.zip.
     *
     * Two things were wrong here, and they hid each other.
     *
     * 1. Installer::install() takes a DIRECTORY containing a manifest - never a
     *    zip. Handing it a .zip path just returns false. The bundle ships one
     *    zip per extension, so every install attempt failed.
     * 2. The search only collected files ending in .zip and did nothing at all
     *    when it found none, so a bundle in the other shape (already-extracted
     *    directories, which is how the JS Jobs bundle is laid out) installed
     *    nothing and still reported overall success.
     *
     * Both shapes are handled now, because the layout has differed between
     * products and between releases and is not worth guessing at.
     *
     * Each extension gets its own Installer with the database set explicitly:
     * Installer::__construct() does not set one on Joomla 4+ (only the
     * getInstance() singleton does), and the object carries per-install state,
     * so one instance reused across a loop misbehaves.
     *
     * A bundle that yields nothing installable is now an error rather than a
     * silent no-op - that silence is what let a release ship without its
     * modules and still look clean.
     */
    private function installExtensions(string $zip, string $targetDir): void
    {
        $this->removeDir($targetDir);
        $this->mkdir($targetDir);
        $this->extractZip($zip, $targetDir);

        $sources   = $this->findExtensionSources($targetDir);
        $installed = [];
        $failed    = [];

        foreach ($sources as $source) {
            $name    = basename($source);
            $dir     = $source;
            $unpacked = '';

            // A zip has to be unpacked before Joomla will look at it.
            if (is_file($source)) {
                $package = InstallerHelper::unpack($source);

                if (!$package || empty($package['dir'])) {
                    $failed[] = $name . ' (could not unpack)';
                    continue;
                }

                $dir = $unpacked = $package['dir'];
            }

            $installer = new Installer();
            if (method_exists($installer, 'setDatabase')) {
                $installer->setDatabase(Factory::getContainer()->get('DatabaseDriver'));
            }
            $installer->setOverwrite(true);

            try {
                if ($installer->install($dir)) {
                    $installed[] = $name;
                } else {
                    $failed[] = $name;
                }
            } catch (Throwable $e) {
                // Keep going: one bad extension should not cost the customer the
                // other nineteen, and the summary below reports every failure.
                $failed[] = $name . ' (' . $e->getMessage() . ')';
            }

            if ($unpacked !== '' && is_dir($unpacked)) {
                $this->removeDir($unpacked);
            }
        }

        $this->debugLog('Extension install summary', [
            'found'     => count($sources),
            'installed' => count($installed),
            'failed'    => count($failed),
            'names'     => $installed,
            'errors'    => $failed,
        ]);

        if (!$installed) {
            throw new RuntimeException(
                'Extension installation failed: no installable extension was found in the package.'
            );
        }

        if ($failed) {
            throw new RuntimeException('Extension installation failed for: ' . implode(', ', $failed));
        }
    }

    /**
     * Everything installable inside an extracted bundle: nested .zip files, and
     * directories carrying their own manifest.
     *
     * Zips win when a bundle contains both, because in that case the directories
     * are almost always the zips' own extracted contents and installing both
     * would do the same work twice.
     *
     * @return string[]
     */
    private function findExtensionSources(string $dir): array
    {
        $zips = [];
        $dirs = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST      // default skips directories
        );

        foreach ($iterator as $file) {
            $path = $file->getPathname();

            if ($file->isFile() && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'zip') {
                $zips[] = $path;
            } elseif ($file->isDir() && $this->hasManifest($path)) {
                $dirs[] = $path;
            }
        }

        sort($zips);
        sort($dirs);

        return $zips ?: $dirs;
    }

    private function hasManifest(string $dir): bool
    {
        foreach (glob($dir . '/*.xml') ?: [] as $xml) {
            $head = (string) @file_get_contents($xml, false, null, 0, 4096);

            if (stripos($head, '<extension') !== false) {
                return true;
            }
        }

        return false;
    }

    private function installLanguage(string $zip, string $targetDir): void
    {
        $this->removeDir($targetDir);
        $this->mkdir($targetDir);
        $this->extractZip($zip, $targetDir);
        $admin = $targetDir . '/lang/admin/en-GB.com_jssupportticket.ini';
        $site = $targetDir . '/lang/site/en-GB.com_jssupportticket.ini';
        if (is_file($admin)) {
            @copy($admin, JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_jssupportticket.ini');
        }
        if (is_file($site)) {
            @copy($site, JPATH_ROOT . '/language/en-GB/en-GB.com_jssupportticket.ini');
        }
    }

    private function runSql(string $zip, string $targetDir): void
    {
        $this->removeDir($targetDir);
        $this->mkdir($targetDir);
        $this->extractZip($zip, $targetDir);
        $sqlFile = $targetDir . '/sql/install.sql';
        if (!is_file($sqlFile)) {
            return;
        }
        $sql = file_get_contents($sqlFile);
        $db = Factory::getContainer()->get('DatabaseDriver');
        $queries = method_exists($db, 'splitSql') ? $db->splitSql($sql) : preg_split('/;\s*
/', $sql);
        $executed = 0;
        foreach ((array)$queries as $query) {
            $query = trim($query);
            if ($query !== '') {
                $db->setQuery($query);
                $db->execute();
                $executed++;
            }
        }

        $this->debugLog('SQL queries executed', ['count' => $executed]);
    }

    private function storeHostConfig(array $data): void
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        foreach (['serialnumber', 'hostdata', 'zvdk'] as $key) {
            if (empty($data[$key])) {
                continue;
            }
            $query = "INSERT INTO `#__js_ticket_config` (`configname`, `configvalue`, `configfor`) VALUES (" . $db->quote($key) . ", " . $db->quote((string)$data[$key]) . ", 'hostdata') ON DUPLICATE KEY UPDATE `configvalue` = VALUES(`configvalue`)";
            $db->setQuery($query);
            $db->execute();
        }
    }

    private function updateManifestVersion(string $version): void
    {
        if ($version === '') {
            return;
        }
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = "SELECT manifest_cache FROM `#__extensions` WHERE element = 'com_jssupportticket'";
        $db->setQuery($query);
        $cache = $db->loadResult();
        $data = json_decode((string)$cache);
        if (is_object($data)) {
            $data->version = $version;
            $query = "UPDATE `#__extensions` SET manifest_cache = " . $db->quote(json_encode($data)) . " WHERE element = 'com_jssupportticket'";
            $db->setQuery($query);
            $db->execute();
        }
    }

    private function extractZip(string $zip, string $destination): void
    {
        $this->mkdir($destination);

        $extractErrors = [];

        if ($this->extractZipWithJoomlaArchive($zip, $destination, $extractErrors)) {
            return;
        }

        if ($this->extractZipWithZipArchive($zip, $destination, $extractErrors)) {
            return;
        }

        if ($this->extractZipWithPurePhp($zip, $destination, $extractErrors)) {
            return;
        }

        throw new RuntimeException('Unable to extract installer package. ' . implode(' ', array_filter($extractErrors)));
    }

    private function extractZipWithJoomlaArchive(string $zip, string $destination, array &$errors): bool
    {
        if (!class_exists('\\Joomla\\CMS\\Filesystem\\Archive')) {
            $errors[] = 'Joomla archive extractor is not available.';
            return false;
        }

        try {
            $result = \Joomla\CMS\Filesystem\Archive::extract($zip, $destination);

            if ($result) {
                $this->debugLog('Zip package extracted with Joomla archive API', [
                    'zip' => $zip,
                    'destination' => $destination,
                ]);
                return true;
            }

            $errors[] = 'Joomla archive extractor returned false.';
        } catch (Throwable $e) {
            $errors[] = 'Joomla archive extractor failed: ' . $e->getMessage();
            $this->debugLog('Joomla archive extractor failed', [
                'zip' => $zip,
                'destination' => $destination,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    private function extractZipWithZipArchive(string $zip, string $destination, array &$errors): bool
    {
        if (!class_exists('ZipArchive')) {
            $errors[] = 'PHP ZipArchive extension is not available.';
            return false;
        }

        try {
            $archive = new ZipArchive();
            $open = $archive->open($zip);

            if ($open !== true) {
                $errors[] = 'ZipArchive could not open package: ' . basename($zip);
                return false;
            }

            $destinationReal = realpath($destination);
            if ($destinationReal === false) {
                $archive->close();
                $errors[] = 'Unable to prepare extraction directory: ' . $destination;
                return false;
            }

            $destinationBase = rtrim(str_replace('\\', '/', $destinationReal), '/') . '/';
            $extracted = 0;

            for ($i = 0; $i < $archive->numFiles; $i++) {
                $stat = $archive->statIndex($i);
                $entryName = (string)($stat['name'] ?? '');

                if (!$this->safeZipEntryName($entryName)) {
                    $this->debugLog('Skipped unsafe ZipArchive entry', ['entry' => $entryName]);
                    continue;
                }

                $normalized = ltrim(str_replace('\\', '/', $entryName), '/');
                $target = $destination . '/' . $normalized;

                if ($this->isZipDirectory($normalized)) {
                    $this->mkdir(rtrim($target, '/\\'));
                    continue;
                }

                $targetDir = dirname($target);
                $this->mkdir($targetDir);

                if (!$this->isInsidePath($targetDir, $destinationBase)) {
                    $this->debugLog('Skipped ZipArchive entry outside destination', ['entry' => $entryName]);
                    continue;
                }

                $stream = $archive->getStream($entryName);
                if (!is_resource($stream)) {
                    $archive->close();
                    $errors[] = 'Unable to read zip entry: ' . $entryName;
                    return false;
                }

                $output = @fopen($target, 'wb');
                if (!is_resource($output)) {
                    fclose($stream);
                    $archive->close();
                    $errors[] = 'Unable to write extracted file: ' . $target;
                    return false;
                }

                stream_copy_to_stream($stream, $output);
                fclose($output);
                fclose($stream);
                $extracted++;
            }

            $archive->close();

            if ($extracted === 0) {
                $errors[] = 'ZipArchive did not extract any files.';
                return false;
            }

            $this->debugLog('Zip package extracted with ZipArchive', [
                'zip' => $zip,
                'destination' => $destination,
                'files' => $extracted,
            ]);

            return true;
        } catch (Throwable $e) {
            $errors[] = 'ZipArchive extractor failed: ' . $e->getMessage();
            $this->debugLog('ZipArchive extractor failed', [
                'zip' => $zip,
                'destination' => $destination,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function extractZipWithPurePhp(string $zip, string $destination, array &$errors): bool
    {
        if (!function_exists('gzinflate')) {
            $errors[] = 'PHP zlib/gzinflate is not available for pure PHP zip extraction.';
            return false;
        }

        $handle = @fopen($zip, 'rb');
        if (!is_resource($handle)) {
            $errors[] = 'Unable to open package file for reading.';
            return false;
        }

        try {
            $size = filesize($zip);
            if ($size === false || $size < 22) {
                fclose($handle);
                $errors[] = 'Invalid zip package size.';
                return false;
            }

            $eocd = $this->findZipEndOfCentralDirectory($handle, $size);
            if (!$eocd) {
                fclose($handle);
                $errors[] = 'Could not locate zip central directory.';
                return false;
            }

            $entries = $this->readZipCentralDirectory($handle, $eocd['central_offset'], $eocd['central_size'], $eocd['entries']);
            if (!$entries) {
                fclose($handle);
                $errors[] = 'Zip central directory did not contain readable entries.';
                return false;
            }

            $destinationReal = realpath($destination);
            if ($destinationReal === false) {
                fclose($handle);
                $errors[] = 'Unable to prepare extraction directory: ' . $destination;
                return false;
            }

            $destinationBase = rtrim(str_replace('\\', '/', $destinationReal), '/') . '/';
            $extracted = 0;

            foreach ($entries as $entry) {
                $name = (string)$entry['name'];

                if (!$this->safeZipEntryName($name)) {
                    $this->debugLog('Skipped unsafe pure PHP zip entry', ['entry' => $name]);
                    continue;
                }

                $normalized = ltrim(str_replace('\\', '/', $name), '/');
                $target = $destination . '/' . $normalized;

                if ($this->isZipDirectory($normalized)) {
                    $this->mkdir(rtrim($target, '/\\'));
                    continue;
                }

                $targetDir = dirname($target);
                $this->mkdir($targetDir);

                if (!$this->isInsidePath($targetDir, $destinationBase)) {
                    $this->debugLog('Skipped pure PHP zip entry outside destination', ['entry' => $name]);
                    continue;
                }

                $content = $this->readZipEntryContent($handle, $entry);

                if ($content === null) {
                    fclose($handle);
                    $errors[] = 'Unable to read zip entry content: ' . $name;
                    return false;
                }

                if (@file_put_contents($target, $content, LOCK_EX) === false) {
                    fclose($handle);
                    $errors[] = 'Unable to write extracted file: ' . $target;
                    return false;
                }

                $extracted++;
            }

            fclose($handle);

            if ($extracted === 0) {
                $errors[] = 'Pure PHP extractor did not extract any files.';
                return false;
            }

            $this->debugLog('Zip package extracted with pure PHP extractor', [
                'zip' => $zip,
                'destination' => $destination,
                'files' => $extracted,
            ]);

            return true;
        } catch (Throwable $e) {
            if (is_resource($handle)) {
                fclose($handle);
            }

            $errors[] = 'Pure PHP extractor failed: ' . $e->getMessage();
            $this->debugLog('Pure PHP zip extractor failed', [
                'zip' => $zip,
                'destination' => $destination,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function findZipEndOfCentralDirectory($handle, int $size): ?array
    {
        $readSize = min($size, 65557);
        fseek($handle, $size - $readSize);
        $data = fread($handle, $readSize);

        if ($data === false) {
            return null;
        }

        $position = strrpos($data, "PK\x05\x06");
        if ($position === false || strlen($data) < $position + 22) {
            return null;
        }

        $eocd = substr($data, $position, 22);

        return [
            'entries' => $this->le16(substr($eocd, 10, 2)),
            'central_size' => $this->le32(substr($eocd, 12, 4)),
            'central_offset' => $this->le32(substr($eocd, 16, 4)),
        ];
    }

    private function readZipCentralDirectory($handle, int $offset, int $size, int $expectedEntries): array
    {
        $entries = [];
        fseek($handle, $offset);
        $end = $offset + $size;

        while (ftell($handle) < $end && count($entries) < $expectedEntries) {
            $header = fread($handle, 46);
            if ($header === false || strlen($header) < 46 || substr($header, 0, 4) !== "PK\x01\x02") {
                break;
            }

            $method = $this->le16(substr($header, 10, 2));
            $compressedSize = $this->le32(substr($header, 20, 4));
            $uncompressedSize = $this->le32(substr($header, 24, 4));
            $nameLength = $this->le16(substr($header, 28, 2));
            $extraLength = $this->le16(substr($header, 30, 2));
            $commentLength = $this->le16(substr($header, 32, 2));
            $localOffset = $this->le32(substr($header, 42, 4));

            $name = $nameLength > 0 ? fread($handle, $nameLength) : '';
            if ($extraLength > 0) {
                fread($handle, $extraLength);
            }
            if ($commentLength > 0) {
                fread($handle, $commentLength);
            }

            if ($name === false) {
                continue;
            }

            $entries[] = [
                'name' => $name,
                'method' => $method,
                'compressed_size' => $compressedSize,
                'uncompressed_size' => $uncompressedSize,
                'local_offset' => $localOffset,
            ];
        }

        return $entries;
    }

    private function readZipEntryContent($handle, array $entry): ?string
    {
        fseek($handle, (int)$entry['local_offset']);
        $local = fread($handle, 30);

        if ($local === false || strlen($local) < 30 || substr($local, 0, 4) !== "PK\x03\x04") {
            return null;
        }

        $nameLength = $this->le16(substr($local, 26, 2));
        $extraLength = $this->le16(substr($local, 28, 2));
        $dataOffset = (int)$entry['local_offset'] + 30 + $nameLength + $extraLength;

        fseek($handle, $dataOffset);
        $data = ((int)$entry['compressed_size'] > 0) ? fread($handle, (int)$entry['compressed_size']) : '';

        if ($data === false) {
            return null;
        }

        $method = (int)$entry['method'];

        if ($method === 0) {
            return $data;
        }

        if ($method === 8) {
            $inflated = @gzinflate($data);

            if ($inflated === false) {
                return null;
            }

            return $inflated;
        }

        $this->debugLog('Unsupported zip compression method', [
            'entry' => (string)$entry['name'],
            'method' => $method,
        ]);

        return null;
    }

    private function safeZipEntryName(string $entryName): bool
    {
        if ($entryName === '') {
            return false;
        }

        $normalized = ltrim(str_replace('\\', '/', $entryName), '/');

        if ($normalized === '' || strpos($normalized, "\0") !== false) {
            return false;
        }

        if (preg_match('#(^|/)\.\.(/|$)#', $normalized)) {
            return false;
        }

        if (preg_match('#^[a-zA-Z]:/#', $normalized)) {
            return false;
        }

        return true;
    }

    private function isZipDirectory(string $entryName): bool
    {
        return substr($entryName, -1) === '/';
    }

    private function isInsidePath(string $path, string $base): bool
    {
        $real = realpath($path);

        if ($real === false) {
            return false;
        }

        return strpos(rtrim(str_replace('\\', '/', $real), '/') . '/', $base) === 0;
    }

    private function le16(string $bytes): int
    {
        $data = unpack('v', $bytes);

        return (int)($data[1] ?? 0);
    }

    private function le32(string $bytes): int
    {
        $data = unpack('V', $bytes);

        return (int)($data[1] ?? 0);
    }

    private function mkdir(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
            throw new RuntimeException('Unable to create directory: ' . $path);
        }
        if (!is_file($path . '/index.html')) {
            @file_put_contents($path . '/index.html', '');
        }
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeDir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    private function copyDir(string $source, string $destination): void
    {
        $this->mkdir($destination);
        $items = scandir($source) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $src = $source . '/' . $item;
            $dst = $destination . '/' . $item;
            if (is_dir($src)) {
                $this->copyDir($src, $dst);
            } else {
                @copy($src, $dst);
            }
        }
    }
}
