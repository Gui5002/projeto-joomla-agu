<?php

/**
 * JS Support Ticket - translation delivery.
 *
 * Fetches a signed manifest from a CDN (GitHub + jsDelivr by default), then
 * downloads and installs individual language .ini files on request.
 *
 * Security model:
 *   - the manifest is signed; the signature is verified with
 *     jssupportticket_update_pubkey.pem (RSA + SHA-256) before a single byte
 *     of it is trusted
 *   - every language file is verified against the SHA-256 recorded in the
 *     signed manifest, so a compromised CDN cannot swap file contents
 *   - TLS verification is never disabled; the CA bundle Joomla ships is pinned
 *   - downloaded content is INI-validated and sanitised before it is written:
 *     language values are echoed into admin HTML, so untrusted markup in a
 *     value would be stored XSS
 *   - only ever writes <admin>/language/<code>/<code>.com_jssupportticket.ini
 *
 * @license GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

class JSSupportTicketTranslations
{
    /** Signed index of available translations. */
    const DEFAULT_MANIFEST_URL = 'https://cdn.jsdelivr.net/gh/Joomsky/jssupportticket-translations@main/manifest.json';

    const PUBKEY_FILE   = 'jssupportticket_update_pubkey.pem';
    const CACHE_MINUTES = 720;           // 12h - the manifest changes rarely
    const MAX_BYTES     = 2097152;       // 2 MB ceiling for a language file
    const TIMEOUT       = 20;

    /** @var string */
    public $error = '';

    // ---------------------------------------------------------------- manifest

    /**
     * Return the verified manifest as an array, or false on failure.
     * Cached so the CDN is not hit on every page load.
     */
    public function getManifest($force = false)
    {
        // Deliberately not Factory::getCache(): it is deprecated in Joomla 5
        // and emits an E_USER_DEPRECATED notice, which can print into a JSON
        // response and break the admin page. A small file cache stays clean.
        if (!$force) {
            $cached = $this->cacheRead();
            if (is_array($cached)) {
                return $cached;
            }
        }

        $url = $this->manifestUrl();
        $raw = $this->httpGet($url, 262144);
        if ($raw === false) {
            return false;
        }

        // Detached signature lives next to the manifest.
        $sig = $this->httpGet($url . '.sig', 8192);
        if ($sig === false) {
            $this->error = Text::_('The translation manifest signature could not be downloaded.');
            return false;
        }

        if (!$this->verifySignature($raw, trim($sig))) {
            $this->error = Text::_('The translation manifest failed signature verification and was rejected.');
            return false;
        }

        $manifest = json_decode($raw, true);
        if (!is_array($manifest) || empty($manifest['languages']) || !is_array($manifest['languages'])) {
            $this->error = Text::_('The translation manifest is not valid.');
            return false;
        }

        $this->cacheWrite($manifest);
        return $manifest;
    }

    /**
     * Languages on offer, annotated with what is installed locally.
     * Returns a plain array safe to json_encode for the browser.
     */
    public function getAvailable($force = false)
    {
        $manifest = $this->getManifest($force);
        if ($manifest === false) {
            return false;
        }

        $out = array();

        foreach ($manifest['languages'] as $lang) {
            if (empty($lang['code']) || empty($lang['file']) || empty($lang['sha256'])) {
                continue;
            }
            $code = $this->sanitiseLangCode($lang['code']);
            if ($code === '') {
                continue;
            }

            $target    = $this->targetPath($code);
            $installed = is_file($target);

            $out[] = array(
                'code'      => $code,
                'name'      => isset($lang['name']) ? strip_tags((string) $lang['name']) : $code,
                'keys'      => isset($lang['keys']) ? (int) $lang['keys'] : 0,
                'updated'   => isset($lang['updated']) ? preg_replace('/[^0-9\-]/', '', (string) $lang['updated']) : '',
                'installed' => $installed,
                // when installed, does the local file already match the published one?
                'current'   => $installed && hash_file('sha256', $target) === strtolower((string) $lang['sha256']),
                'joomla'    => is_dir(JPATH_ADMINISTRATOR . '/language/' . $code),
            );
        }

        return $out;
    }

    // ---------------------------------------------------------------- install

    /**
     * Download, verify, validate and install one language.
     * Returns true on success; $this->error explains a false.
     */
    public function install($code)
    {
        $code     = $this->sanitiseLangCode($code);
        $manifest = $this->getManifest();

        if ($code === '' || $manifest === false) {
            if ($this->error === '') {
                $this->error = Text::_('Invalid language code.');
            }
            return false;
        }

        $entry = null;
        foreach ($manifest['languages'] as $lang) {
            if (isset($lang['code']) && $this->sanitiseLangCode($lang['code']) === $code) {
                $entry = $lang;
                break;
            }
        }
        if ($entry === null) {
            $this->error = Text::_('That translation is not available.');
            return false;
        }

        $target = $this->targetPath($code);
        $dir    = dirname($target);
        if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
            $this->error = Text::sprintf('Could not create the folder %s', $dir);
            return false;
        }
        if (!is_writable($dir)) {
            $this->error = Text::sprintf('Dir is not writeable %s', $dir);
            return false;
        }

        // Never clobber a file the site has customised. A file already
        // byte-identical to the published one has nothing to preserve.
        if (is_file($target)
            && !$this->wasInstalledByUs($target)
            && hash_file('sha256', $target) !== strtolower((string) $entry['sha256'])) {
            $this->error = Text::_('The local translation file has been modified, so it was not overwritten.');
            return false;
        }

        $raw = $this->httpGet($this->fileUrl($entry['file']), self::MAX_BYTES);
        if ($raw === false) {
            return false;
        }

        if (hash('sha256', $raw) !== strtolower((string) $entry['sha256'])) {
            $this->error = Text::sprintf('%s failed checksum verification - rejected.', $code);
            return false;
        }

        $clean = $this->sanitiseIni($raw);
        if ($clean === false) {
            return false;
        }

        if (@file_put_contents($target, $clean) === false) {
            $this->error = Text::sprintf('Could not write %s', $target);
            return false;
        }

        $this->rememberInstall($target);
        return true;
    }

    // ---------------------------------------------------------------- validation

    /**
     * Parse-check and sanitise a downloaded .ini.
     *
     * A single malformed key makes parse_ini_string() fail for the WHOLE file,
     * which would blank out every translation, so unparseable payloads are
     * rejected outright. HTML is stripped from values because language strings
     * are echoed into admin pages unescaped in places.
     */
    private function sanitiseIni($raw)
    {
        $raw = str_replace(array("\r\n", "\r"), "\n", (string) $raw);

        if (strpos($raw, "\0") !== false) {
            $this->error = Text::_('The translation file is not valid.');
            return false;
        }

        $parsed = @parse_ini_string($raw, false, INI_SCANNER_RAW);
        if ($parsed === false || !is_array($parsed) || count($parsed) === 0) {
            $this->error = Text::_('The translation file could not be parsed and was rejected.');
            return false;
        }

        $english = $this->englishStrings();
        $lines   = array();

        foreach ($parsed as $key => $value) {
            // a purely numeric key ("2") comes back from parse_ini_string() as an
            // int, so normalise before testing - it is still a valid key
            $key = (string) $key;
            if (!$this->keyIsSafe($key)) {
                continue;
            }
            $value = (string) $value;
            // strip markup, collapse whitespace, drop quotes that would break the ini
            $value = strip_tags($value);
            $value = str_replace(array('"', "\n", "\t"), array("'", ' ', ' '), $value);
            $value = trim(preg_replace('/\s{2,}/', ' ', $value));
            if ($value === '') {
                continue;
            }
            // placeholder parity: a translation that loses %s breaks sprintf at runtime
            if (isset($english[$key])) {
                preg_match_all('/%[sd]/', $english[$key], $a);
                preg_match_all('/%[sd]/', $value, $b);
                if ($a[0] !== $b[0]) {
                    continue; // keep the English fallback instead of a broken message
                }
            }
            $lines[] = $key . '="' . $value . '"';
        }

        if (count($lines) === 0) {
            $this->error = Text::_('The translation file contained no usable entries.');
            return false;
        }

        // Final gate: what we are about to write must itself parse.
        $outRaw = implode("\n", $lines) . "\n";
        if (@parse_ini_string($outRaw, false, INI_SCANNER_RAW) === false) {
            $this->error = Text::_('The translation file could not be parsed and was rejected.');
            return false;
        }

        return "; JS Support Ticket translation - installed " . gmdate('Y-m-d H:i') . " UTC\n"
             . "; Managed by JS Support Ticket. Put local changes in Joomla language overrides instead.\n\n"
             . $outRaw;
    }

    /**
     * Is this key safe to write back out as `KEY="value"`?
     *
     * The keys in this component are the uppercased English phrases themselves
     * ("ADMIN > SYSTEM EMAILS", "TICKET ID #", "CURL + SSL"), so a hand-written
     * character allowlist gets it wrong in both directions - it drops legitimate
     * punctuation and still admits characters INI reserves. Ask the parser
     * instead: round-trip the key on its own and require it back verbatim. That
     * rejects `= ; [ " { } | ~ ! ( ) & $ ^`, leading/trailing space and the
     * reserved words (TRUE/YES/ON/...), and nothing else. It matters because one
     * unparseable line makes parse_ini_file() return false for the WHOLE file,
     * and Joomla then silently loads zero strings.
     */
    private function keyIsSafe($key)
    {
        if (!is_string($key) || $key === '' || preg_match('/[\x00-\x1F\x7F]/', $key)) {
            return false;
        }
        $probe = @parse_ini_string($key . '="1"', false, INI_SCANNER_RAW);

        return is_array($probe) && count($probe) === 1 && (string) key($probe) === $key;
    }

    /** en-GB strings, used for %s placeholder parity checks. */
    private function englishStrings()
    {
        static $en = null;
        if ($en === null) {
            $f  = JPATH_ADMINISTRATOR . '/language/en-GB/en-GB.com_jssupportticket.ini';
            $en = is_file($f) ? (array) @parse_ini_file($f, false, INI_SCANNER_RAW) : array();
        }
        return $en;
    }

    // ---------------------------------------------------------------- helpers

    private function manifestUrl()
    {
        return self::DEFAULT_MANIFEST_URL;
    }

    /** Resolve a manifest-relative file name against the manifest location. */
    private function fileUrl($file)
    {
        $file = ltrim(str_replace('\\', '/', (string) $file), '/');
        // no traversal, no absolute URLs from the manifest
        $file = str_replace('..', '', $file);
        return rtrim(dirname($this->manifestUrl()), '/') . '/' . $file;
    }

    private function targetPath($code)
    {
        return JPATH_ADMINISTRATOR . '/language/' . $code . '/' . $code . '.com_jssupportticket.ini';
    }

    private function sanitiseLangCode($code)
    {
        $code = trim((string) $code);
        return preg_match('/^[a-z]{2,3}-[A-Z]{2}$/', $code) ? $code : '';
    }

    private function cacheFile()
    {
        return JPATH_ADMINISTRATOR . '/cache/com_jssupportticket_translations.json';
    }

    private function cacheRead()
    {
        $f = $this->cacheFile();
        if (!is_file($f) || (time() - filemtime($f)) > (self::CACHE_MINUTES * 60)) {
            return null;
        }
        $data = json_decode((string) @file_get_contents($f), true);
        return is_array($data) ? $data : null;
    }

    private function cacheWrite(array $manifest)
    {
        // Best effort only - a read-only cache dir must not break the feature.
        @file_put_contents($this->cacheFile(), json_encode($manifest));
    }

    /** Track what we wrote so a later update never overwrites hand edits. */
    private function rememberInstall($target)
    {
        @file_put_contents($target . '.sha256', hash_file('sha256', $target));
    }

    private function wasInstalledByUs($target)
    {
        $stamp = $target . '.sha256';
        if (!is_file($stamp)) {
            return false;
        }
        return trim((string) @file_get_contents($stamp)) === hash_file('sha256', $target);
    }

    private function verifySignature($data, $signatureB64)
    {
        if (!function_exists('openssl_verify')) {
            $this->error = Text::_('The PHP OpenSSL extension is required to verify updates.');
            return false;
        }
        $pubPath = __DIR__ . '/../' . self::PUBKEY_FILE;
        if (!is_file($pubPath)) {
            $this->error = Text::_('The update public key is missing.');
            return false;
        }
        $pub = @file_get_contents($pubPath);
        $sig = base64_decode($signatureB64, true);
        if ($pub === false || $sig === false || $sig === '') {
            return false;
        }
        return openssl_verify($data, $sig, $pub, OPENSSL_ALGO_SHA256) === 1;
    }

    /** HTTPS GET with TLS verification on and a hard size ceiling. */
    private function httpGet($url, $maxBytes)
    {
        if (!preg_match('#^https://#i', $url)) {
            $this->error = Text::_('Only https:// translation sources are allowed.');
            return false;
        }
        if (!function_exists('curl_init')) {
            $this->error = Text::_('The PHP cURL extension is required.');
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JSSupportTicket-Translations');
        $ca = $this->caBundle();
        if ($ca !== '') {
            curl_setopt($ch, CURLOPT_CAINFO, $ca);
        }
        // abort oversized responses mid-flight
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($res, $dlTotal, $dlNow) use ($maxBytes) {
            return ($dlTotal > $maxBytes || $dlNow > $maxBytes) ? 1 : 0;
        });

        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false || $body === '') {
            $this->error = $err !== ''
                ? Text::sprintf('Could not reach the translation server: %s', $err)
                : Text::_('Unable to connect to server');
            return false;
        }
        if ($code !== 200) {
            $this->error = Text::sprintf(
                'The update server returned an unexpected response. (HTTP %s from %s)',
                $code,
                $url
            );
            return false;
        }
        if (strlen($body) > $maxBytes) {
            $this->error = Text::_('The download exceeded the maximum allowed size.');
            return false;
        }
        return $body;
    }

    private function caBundle()
    {
        static $ca = null;
        if ($ca !== null) {
            return $ca;
        }
        $ca = '';
        $bundled = JPATH_LIBRARIES . '/vendor/composer/ca-bundle/res/cacert.pem';
        if (is_file($bundled)) {
            $ca = $bundled;
        } elseif (class_exists('\Composer\CaBundle\CaBundle')) {
            $path = \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();
            if (is_string($path) && $path !== '' && is_file($path)) {
                $ca = $path;
            }
        }
        return $ca;
    }
}
