<?php
/**
 * JS Support Ticket Pro entitlement guard.
 *
 * Verifies the server-signed entitlement created by the v1 installer endpoint.
 *
 * LICENSING MODEL
 * ---------------
 * - A licence covers 1 or 5 sites. The entitlement lists every activated domain.
 * - Pro features stay enabled for the life of the install: a lapsed subscription
 *   does NOT switch the component off. Expiry only closes updates and raises a
 *   renewal notice, so a customer's live helpdesk can never go dark over billing.
 * - Development hosts (localhost, *.test, *.local, private IPs) are allowed WITHOUT
 *   consuming a domain slot, but only for an install that already carries a valid
 *   signed entitlement. There is deliberately no anonymous localhost bypass: that
 *   turned "copy the folder to any .local host" into a free Pro licence, and since
 *   the host was read from the client-controlled Host header it could be spoofed
 *   remotely with a single request.
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Uri\Uri;

class JSSTProLicense
{
    /** Entitlement schema this build understands. */
    private const SCHEMA = 2;

    private const PUBLIC_KEY = <<<'KEY'
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz9uqF1ZS/DVe07FqtANz
c/+lfy4StqyEnGKUOrxLdvM/USIBb012pZWSMad9mHPC89giNauSuMsOnM/vLdv7
TLw+Gtq0mLzO1YwSNur4KAtVcec3hqZ5KzmMAnWslF0ZnZfdeqcupdQtxvFhYe/C
T9QItQ/W4MOcLszn3ViGSKrUInWjNJF1BKVRU0eCaJAC8xHqco8WAIZNXORxklk7
5XwlyQVcqSMNnIYZlNu4xn6XDQBorAcfXzRYVYZ7YzzK0pjjymRfQkbOqCX4ENwM
0xVuc46hsDe7BOLW8lK7Gnu6iVhMk9etu9ppWTKxhEYzJ+u952TRbHpyo4WE3R8S
DQIDAQAB
-----END PUBLIC KEY-----
KEY;

    /** Per-request memo: openssl_verify() ran on every page load before this. */
    private static $cache = null;

    public static function entitlementPath(): string
    {
        return JPATH_ADMINISTRATOR . '/components/com_jssupportticket/.license/entitlement.json';
    }

    /**
     * True when Pro features may run. Deliberately NOT tied to the subscription
     * date - see the licensing model note above.
     */
    public static function isValid(): bool
    {
        $result = self::validate();
        return (bool) ($result['valid'] ?? false);
    }

    /**
     * True when the subscription still covers updates. Callers that download or
     * install packages must gate on this, not on isValid().
     */
    public static function canUpdate(): bool
    {
        $result = self::validate();
        if (empty($result['valid'])) {
            return false;
        }
        return empty($result['expired']);
    }

    /**
     * Subscription end date as a unix timestamp, or 0 when the entitlement does
     * not carry one (schema 1 entitlements issued before this field existed).
     */
    public static function updatesUntil(): int
    {
        $result = self::validate();
        return (int) ($result['updates_until'] ?? 0);
    }

    public static function validate(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        return self::$cache = self::evaluate();
    }

    /** Drop the memo - used by the installer straight after writing a new entitlement. */
    public static function flush(): void
    {
        self::$cache = null;
    }

    private static function evaluate(): array
    {
        $path = self::entitlementPath();
        if (!is_file($path)) {
            return ['valid' => false, 'reason' => 'missing_entitlement'];
        }

        $entitlement = json_decode((string) file_get_contents($path), true);
        if (!is_array($entitlement) || empty($entitlement['data']) || empty($entitlement['signature'])) {
            return ['valid' => false, 'reason' => 'invalid_entitlement_format'];
        }

        $data = $entitlement['data'];
        if (!is_array($data)) {
            return ['valid' => false, 'reason' => 'invalid_entitlement_data'];
        }

        $signature = base64_decode((string) $entitlement['signature'], true);
        if ($signature === false) {
            return ['valid' => false, 'reason' => 'invalid_signature_encoding'];
        }

        // Signature first: nothing inside $data may be trusted until this passes.
        $ok = openssl_verify(self::canonicalJson($data), $signature, self::PUBLIC_KEY, OPENSSL_ALGO_SHA256);
        if ($ok !== 1) {
            return ['valid' => false, 'reason' => 'signature_failed'];
        }

        if ((string) ($data['product'] ?? '') !== 'jssupportticket') {
            return ['valid' => false, 'reason' => 'product_mismatch'];
        }

        if ((int) ($data['schema'] ?? 1) > self::SCHEMA) {
            // Newer entitlement than this build understands. Fail closed rather
            // than silently ignoring constraints added in a later schema.
            return ['valid' => false, 'reason' => 'entitlement_schema_too_new'];
        }

        $host      = self::currentHost();
        $licensed  = self::licensedDomains($data);
        $isDevHost = self::isDevelopmentHost($host);

        // A development host rides on the customer's existing entitlement without
        // consuming one of their domain slots. It still required a valid signature
        // to get here, so it cannot be reached by copying files alone.
        if (!$isDevHost && ($host === '' || !in_array($host, $licensed, true))) {
            return [
                'valid'            => false,
                'reason'           => 'domain_mismatch',
                'current_domain'   => $host,
                'licensed_domains' => $licensed,
            ];
        }

        $updatesUntil = self::timestamp($data['updates_until'] ?? '');
        $expired      = $updatesUntil > 0 && time() > $updatesUntil;

        return [
            'valid'         => true,
            'data'          => $data,
            'domains'       => $licensed,
            'seats'         => (int) ($data['seats'] ?? count($licensed)),
            'dev_host'      => $isDevHost,
            'updates_until' => $updatesUntil,
            'expired'       => $expired,
        ];
    }

    /**
     * Every domain the licence covers. Schema 2 ships a "domains" list (1 or 5
     * entries); schema 1 only had a single "domain", so fall back to it.
     */
    private static function licensedDomains(array $data): array
    {
        $domains = array();

        if (!empty($data['domains']) && is_array($data['domains'])) {
            foreach ($data['domains'] as $domain) {
                $normalized = self::normalizeDomain((string) $domain);
                if ($normalized !== '') {
                    $domains[] = $normalized;
                }
            }
        }

        if (!empty($data['domain'])) {
            $normalized = self::normalizeDomain((string) $data['domain']);
            if ($normalized !== '' && !in_array($normalized, $domains, true)) {
                $domains[] = $normalized;
            }
        }

        return $domains;
    }

    public static function writeEntitlement(array $entitlement): bool
    {
        $dir = dirname(self::entitlementPath());
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!is_file($dir . '/index.html')) {
            file_put_contents($dir . '/index.html', '');
        }

        // Apache 2.2 and 2.4 both, since "Require all denied" is 2.4-only and a
        // 2.2 host would otherwise serve the entitlement to anyone who asks.
        // Web servers that read neither (nginx) are covered by the deny rule the
        // installer documents for the vhost.
        if (!is_file($dir . '/.htaccess')) {
            file_put_contents(
                $dir . '/.htaccess',
                "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n"
                . "<IfModule !mod_authz_core.c>\n    Order allow,deny\n    Deny from all\n</IfModule>\n"
            );
        }

        self::flush();

        return (bool) file_put_contents(
            self::entitlementPath(),
            json_encode($entitlement, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );
    }

    public static function canonicalJson(array $data): string
    {
        $normalize = function ($value) use (&$normalize) {
            if (is_array($value)) {
                $isList = array_keys($value) === range(0, count($value) - 1);
                if (!$isList) {
                    ksort($value);
                }
                foreach ($value as $k => $v) {
                    $value[$k] = $normalize($v);
                }
            }
            return $value;
        };
        return json_encode($normalize($data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function timestamp($value): int
    {
        if ($value === '' || $value === null) {
            return 0;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        $time = strtotime((string) $value);
        return $time === false ? 0 : $time;
    }

    private static function normalizeDomain(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return '';
        }
        return self::normalizeHost($host);
    }

    /**
     * The host this install is actually served from.
     *
     * Uri::root() is used first because it honours Joomla's configured $live_site
     * where the administrator has set one. $_SERVER['HTTP_HOST'] is only a last
     * resort: it is supplied by the client, so it must never be the value that
     * decides an entitlement outcome on its own.
     */
    private static function currentHost(): string
    {
        $host = (string) parse_url(Uri::root(), PHP_URL_HOST);
        if ($host === '') {
            $host = (string) ($_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? '');
        }
        return self::normalizeHost($host);
    }

    private static function normalizeHost(string $host): string
    {
        $host = trim(strtolower($host));
        if ($host === '') {
            return '';
        }

        if (preg_match('/^\[([^\]]+)\](?::\d+)?$/', $host, $matches)) {
            $host = $matches[1];
        } elseif (strpos($host, ':') !== false && substr_count($host, ':') === 1) {
            $host = explode(':', $host, 2)[0];
        }

        return preg_replace('/^www\./', '', $host) ?: '';
    }

    /**
     * Development and staging hosts, which do not consume a domain slot.
     * Reaching this still requires a valid signed entitlement.
     */
    private static function isDevelopmentHost(string $host): bool
    {
        if ($host === '') {
            return false;
        }

        if (in_array($host, array('localhost', '127.0.0.1', '::1'), true)) {
            return true;
        }

        if (preg_match('/\.(localhost|local|test|example|invalid)$/', $host)) {
            return true;
        }

        // RFC1918 / loopback / link-local literals.
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return !filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            );
        }

        return false;
    }
}
