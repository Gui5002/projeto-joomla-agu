<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\User\User;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\CMS\Version;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Guestsupport component helper.
 */
class GuestsupportHelper
{
	private static $isagent = 'undefined';
	private static $config = 'undefined';
	private static $emailtemplates = 'undefined';
	private static $_getforms = 'undefined';
	private static $_uploadDirectory = null;
	private static $_uploadDirectory_picture = null;
	private static $database = null;

	/**
	 * Get the appropriate File class for the current Joomla version
	 * 
	 * @return string The File class name to use
	 */
	public static function getFileClass()
	{
		if (class_exists('Joomla\CMS\Filesystem\File')) {
			// Joomla 4.x
			return 'Joomla\CMS\Filesystem\File';
		} else {
			// Joomla 5.x
			return 'Joomla\Filesystem\File';
		}
	}

	/**
	 * Method to get getInput()
	 */
	public static function getInput()
	{
		if ( (int) Version::MAJOR_VERSION < 5 )
		{
			return Factory::getApplication()->input;
		}
		else
		{
			return Factory::getApplication()->getInput();
		}
	}

	/**
	 * Method to get getDatabase() with caching
	 */
	public static function getDatabase()
	{
		if (self::$database === null) {
			if ((int) Version::MAJOR_VERSION < 5) {
				self::$database = Factory::getDbo();
			} else {
				self::$database = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
			}
		}
		
		return self::$database;
	}

	/**
	 * Get list of departments
	 * @param int $form_id Provide a form id if you need to get only departments assigned to a form.
	 */
	public static function departments( $form_id = 0 )
	{
		$db = self::getDatabase();

		$result = false;

		if ( $form_id )
		{
			$query = $db->getQuery(true);
			$query
				->select(
					[
						$db->quoteName('a.department_id', 'id'),
						$db->quoteName('b.name'),
						$db->quoteName('b.agent_id'),
						$db->quoteName('b.additional_emails'),
						$db->quoteName('b.additional_emails_type'),
						$db->quoteName('b.type')
					]
				)
				->from($db->quoteName('#__gs_tickets_department_to_forms', 'a'))
				->join(
					'INNER', 
					$db->quoteName('#__gs_tickets_departments', 'b'), 
					$db->quoteName('b.id') . ' = ' . $db->quoteName('a.department_id')
					. ' AND ' . $db->quoteName('b.type') . ' = ' . $db->quote('core')
					)
				->where($db->quoteName('a.form_id') . ' = :form_id')
				->bind(':form_id', $form_id, ParameterType::INTEGER);

			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		else
		{
			$query = $db->getQuery(true);
			$query
				->select(
					[
						$db->quoteName('id'),
						$db->quoteName('name'),
						$db->quoteName('agent_id'),
						$db->quoteName('additional_emails'),
						$db->quoteName('additional_emails_type'),
						$db->quoteName('type')
					]
				)
				->from($db->quoteName('#__gs_tickets_departments'));

			$db->setQuery($query);
			$result = $db->loadObjectList();
		}

		return $result;
	}

	/**
	 * Get Settings
	 */
	public static function config()
	{
		if ( self::$config != 'undefined' ) {
			return self::$config;
		}

		$db = self::getDatabase();
		$query = $db->getQuery(true);
		$query
            ->select('*')
            ->from($db->quoteName('#__gs_tickets_config'));
		$db->setQuery($query);
		$results = $db->loadObjectList();

		$config = new \stdClass();

		if ( !empty( $results ) )
		{
			foreach ($results as $item) {
				$configName = $item->name;
				$config->$configName = $item->value;
			}
		}

		self::$config = $config;

		return self::$config;
	}

	/**
	 * Get a single Settings and create one if not exist
	 */
	public static function getConfig( $config_name, $default_value = '' )
	{
		$config = self::config();
		if ( isset( $config->$config_name ) )
		{
			return $config->$config_name;
		}

		// Create new config
		$db = self::getDatabase();
		$query = $db->getQuery(true);
		$query
			->insert($db->quoteName('#__gs_tickets_config'))
			->set($db->quoteName('name') . ' = :name')
			->set($db->quoteName('value') . ' = :value')
			->bind(':name', $config_name, ParameterType::STRING)
			->bind(':value', $default_value, ParameterType::STRING);

		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			return false;
		}

		return $default_value;
	}

	/**
	 * Update a Setting and create one if not exist
	 */
	public static function setConfig( $config_name, $config_value )
	{
		if ( !$config_name )
		{
			return false;
		}

		$db = self::getDatabase();

		// Checking for existing record
		$query = $db->getQuery(true);
		$query
			->select('COUNT(' . $db->quoteName('name') . ')')
            ->from($db->quoteName('#__gs_tickets_config'))
			->where($db->quoteName('name') . ' = :name')
			->bind(':name', $config_name, ParameterType::STRING);
		$db->setQuery($query);
		$results = $db->loadResult();

		if ( (int) $results < 1 )
		{
			// Not found, insert new
			$query = $db->getQuery(true);
			$query
				->insert($db->quoteName('#__gs_tickets_config'))
				->set($db->quoteName('name') . ' = :name')
				->set($db->quoteName('value') . ' = :value')
				->bind(':name', $config_name, ParameterType::STRING)
				->bind(':value', $config_value, ParameterType::STRING);

			$db->setQuery($query);

			try {
				$db->execute();
			} catch (\RuntimeException $e) {
				return false;
			}
		}
		else
		{
			// Found, update records
			$query = $db->getQuery(true)
				->update($db->quoteName('#__gs_tickets_config'))
				->set($db->quoteName('value') . ' = :value')
				->where($db->quoteName('name') . ' = :name')
				->bind(':value', $config_value, ParameterType::STRING)
				->bind(':name', $config_name, ParameterType::STRING);

			$db->setQuery($query);

			try {
				$db->execute();
			} catch (\RuntimeException $e) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get Forms
	 */
	public static function getForms()
	{
		if ( self::$_getforms != 'undefined' ) {
			return self::$_getforms;
		}

		$db = self::getDatabase();
		$query = $db->getQuery(true);
		$query
            ->select($db->quoteName(array('id', 'form_name')))
            ->from($db->quoteName('#__gs_tickets_forms'));
		$db->setQuery($query);
		self::$_getforms = $db->loadObjectList();

		return self::$_getforms;
	}

	/**
	 * Get Email Templates
	 */
	public static function emailTemplates()
	{
		if ( self::$emailtemplates != 'undefined' ) {
			return self::$emailtemplates;
		}

		$db = self::getDatabase();
		$query = $db->getQuery(true);
		$query
            ->select($db->quoteName(array('id', 'name', 'type', 'subject', 'template', 'lang', 'active')))
            ->from($db->quoteName('#__gs_tickets_email_templates'))
			->where($db->quoteName('active') . ' = ' . $db->quote(1));
		$db->setQuery($query);
		$results = $db->loadObjectList();

		$templates = [];
		if ( !empty( $results ) )
		{
			foreach ($results as $item) {
				$templates[$item->type] = $item;
			}
		}

		self::$emailtemplates = $templates;

		return self::$emailtemplates;
	}

	/**
	 * Sanitize Email
	 */
	public static function sanitizeEmail( $email )
	{
		// Test for the minimum length the email can be.
		if ( strlen( $email ) < 6 ) {
			return '';
		}

		// Test for an @ character after the first position.
		if ( strpos( $email, '@', 1 ) === false ) {
			return '';
		}

		// Split out the local and domain parts.
		list( $local, $domain ) = explode( '@', $email, 2 );

		// LOCAL PART
		// Test for invalid characters.
		$local = preg_replace( '/[^a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]/', '', $local );
		if ( '' === $local ) {
			return '';
		}

		// DOMAIN PART
		// Test for sequences of periods.
		$domain = preg_replace( '/\.{2,}/', '', $domain );
		if ( '' === $domain ) {
			return '';
		}

		// Test for leading and trailing periods and whitespace.
		$domain = trim( $domain, " \t\n\r\0\x0B." );
		if ( '' === $domain ) {
			return '';
		}

		// Split the domain into subs.
		$subs = explode( '.', $domain );

		// Assume the domain will have at least two subs.
		if ( 2 > count( $subs ) ) {
			return '';
		}

		// Create an array that will contain valid subs.
		$new_subs = array();

		// Loop through each sub.
		foreach ( $subs as $sub ) {
			// Test for leading and trailing hyphens.
			$sub = trim( $sub, " \t\n\r\0\x0B-" );

			// Test for invalid characters.
			$sub = preg_replace( '/[^a-z0-9-]+/i', '', $sub );

			// If there's anything left, add it to the valid subs.
			if ( '' !== $sub ) {
				$new_subs[] = $sub;
			}
		}

		// If there aren't 2 or more valid subs.
		if ( 2 > count( $new_subs ) ) {
			return '';
		}

		// Join valid subs into the new domain.
		$domain = implode( '.', $new_subs );

		// Put the email back together.
		$sanitized_email = $local . '@' . $domain;

		// Verify MX records
		if ( (bool) checkdnsrr($domain, 'MX')===FALSE ) {
			return '';
		}

		// Check disposable email services
		if (preg_match("/(ThrowAwayMail|DeadAddress|10MinuteMail|20MinuteMail|AirMail|Dispostable|Email Sensei|EmailThe|FilzMail|Guerrillamail|IncognitoEmail|Koszmail|Mailcatch|Mailinator|Mailnesia|MintEmail|MyTrashMail|NoClickEmail|
			SpamSpot|Spamavert|Spamfree24|TempEmail|Thrashmail.ws|Yopmail|EasyTrashMail|Jetable|MailExpire|MeltMail|Spambox|empomail|33Mail|
			E4ward|GishPuppy|InboxAlias|MailNull|Spamex|Spamgourmet|BloodyVikings|SpamControl|MailCatch|Tempomail|EmailSensei|Yopmail|
			Trasmail|Guerrillamail|Yopmail|boximail|ghacks|Maildrop|MintEmail|fixmail|gelitik.in|ag.us.to|mobi.web.id
			|fansworldwide.de|privymail.de|gishpuppy|spamevader|temp-mail.org|tempmailo|disposablemail|tempail|fakemail|10minutemail|mail.tm|emailfake|fakemailgenerator|generator.email|email-fake.com|uroid|tempmail|soodo|deadaddress|trbvm)/i", $domain)) {
				return '';
		}

		// Return email
		return $sanitized_email;
	}

	/**
	 * Update guide on config
	 */
	public static function updateGuide( $status )
	{

		$value = self::config()->guide;

		if ( $value === 'done' )
		{
			return true;
		}

		// Generate encryption key if not exist
		$app  = Factory::getApplication();

		$db = self::getDatabase();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__gs_tickets_config'))
			->set($db->quoteName('value') . ' = :value')
			->where($db->quoteName('name') . ' = ' . $db->quote('guide'))
			->bind(':value', $status, ParameterType::STRING);

		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			$app->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
     * Get User Settings
     */
    public static function userSettings( $user_id )
    {
        if ( ! $user_id )
		{
			return false;
		}

		$db = self::getDatabase();
		$query = $db->getQuery(true);
		$query
            ->select($db->quoteName(array('user_id', 'picture', 'signature')))
            ->from($db->quoteName('#__gs_tickets_user_settings'))
            ->where($db->quoteName('user_id') . ' = :user_id')
			->bind(':user_id', $user_id, ParameterType::INTEGER);
		$db->setQuery($query);
		$result = $db->loadObject();
		return !empty($result) ? $result : false;
    }

	/**
	 * Set pro
	 */
	public static function isPro()
	{
		return false;
	}

	/**
	 * Sanitize HTML
	 * Allow certain tags, it won't break the output
	 */
	public static function sanitizeHtml($html)
	{
		if ( !$html )
		{
			return $html;
		}

		$texts = strip_tags($html, '<a><br><em><strong><b><i><u><span>');
		$texts = trim( $texts );
		return $texts;
	}

	/**
	 * Sanitize HTML Editor
	 * Allow certain tags, it won't break the output
	 */
	public static function sanitizeHtmlEditor($html)
	{
		if (!$html) {
			return $html;
		}

		// Step 1: Remove unwanted <select> elements
		$pattern = '/<select[^>]*>.*?<\/select>/si';
		$html = preg_replace($pattern, '', $html);

		// Step 2: Get allowed tags
		$allowedTags = self::allowed_tags();

		// Step 3: Sanitize input
		$sanitizedInput = strip_tags($html, $allowedTags);

		// Step 4: Parse the HTML
		// Suppress DOMDocument warnings
		libxml_use_internal_errors(true);

		// Convert to UTF-8 HTML entities
		// $sanitizedInput = htmlspecialchars_decode(htmlentities($sanitizedInput, ENT_QUOTES, 'UTF-8', false));

		// Load the email content into DOMDocument
		$dom = new \DOMDocument();
		$dom->loadHTML('<?xml encoding="UTF-8">' . '<html><body>' . $sanitizedInput . '</body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();

		// Step 5: Process <div class="ql-code-block"> elements
		$xpath = new \DOMXPath($dom);
		$codeBlocks = $xpath->query('//div[@class="ql-code-block"]');
		$codeBlockContents = [];

		foreach ($codeBlocks as $codeBlock) {
			// Save the code block content and replace it temporarily
			$placeholder = '{GUEST_SUPPORT_CODE_BLOCK_PLACEHOLDER_' . count($codeBlockContents) . '}';

			$codeBlockContents[] = $dom->saveHTML($codeBlock);
			$codeBlock->parentNode->replaceChild($dom->createTextNode($placeholder), $codeBlock);
		}

		// Step 6: Extract content inside <body>
		$body = $dom->getElementsByTagName('body')->item(0);
		$bodyContent = $dom->saveHTML($body);
		$processedHtml = preg_replace('/^<body>|<\/body>$/is', '', $bodyContent);

		// Step 7: Convert URLs to links (excluding placeholders for code blocks)
		$processedHtml = self::convert_urls_to_links($processedHtml);

		// Step 8: Restore code block content
		foreach ($codeBlockContents as $index => $content) {
			$placeholder = '{GUEST_SUPPORT_CODE_BLOCK_PLACEHOLDER_' . $index . '}';
			$processedHtml = str_replace($placeholder, $content, $processedHtml);
		}

		// Step 9: Editor-specific fixes
		// $processedHtml = self::EditorToHtml($processedHtml);

		return $processedHtml;
	}

	/**
	 * Sanitize ticket and reply messages before display
	 */
	public static function sanitizeMessageOutput($html)
	{
		if ( !$html )
		{
			return $html;
		}

		$allowedTags = self::allowed_tags();
		return strip_tags($html, $allowedTags);
	}

	/**
	 * Allowed tags
	 */
	public static function allowed_tags()
	{
		return '<h1><h2><h3><h4><h5><h6><div><p><a><br><em><strong><b><i><u><span><ul><ol><li><code><pre><blockquote>';
	}

	/**
	 * Sanitize HTML Output
	 * Allow certain tags, it won't break the output
	 */
	public static function sanitizeHtmlOutput($html)
	{
		if ( !$html )
		{
			return $html;
		}

		return self::sanitizeMessageOutput( $html );
	}

	/**
	 * Sanitize Base64
	 */
	public static function sanitizeBase64( $string )
	{
		$pattern = '/[^A-Z0-9\/+=]/i';
		$base64_string = str_replace( ' ', '+', $string );
		return (string) preg_replace($pattern, '', $base64_string);
	}

	/**
	 * Select field placeholder
	 */
	public static function selectPlaceholder( $haystack, $needle = '--' ) {
		$length = strlen( $needle );
		$result = substr( $haystack, 0, $length ) === $needle;
		if ( $result )
		{
			$length = strlen( $needle );
			if( !$length ) {
				return true;
			}
			$result = substr( $haystack, -$length ) === $needle;
		}
		return $result;
	}

	/**
	 * Check if user is support agent
	 */
	public static function isAgent()
	{
		if ( self::$isagent != 'undefined' ) {
			return self::$isagent;
		}
		else
		{
			$agents = self::getAgents();
			$app  = Factory::getApplication();
			$user = $app->getIdentity();
			$guest = $user->get('guest');
			$administrator = $user->authorise('core.admin');

			// Check if user logged in and is agent
			if ( !$guest )
			{
				$user_id = $user->id;

				// Early return if it's an administrator
				if ( $administrator )
				{
					self::$isagent = $user_id;
				}
				elseif ( $agents === false )
				{
					// Retun if no agent found on the database
					self::$isagent = false;
				}
				elseif ( in_array( $user_id, $agents ) )
				{
					// Check if logged in user is an agent
					self::$isagent = $user_id;
				}
				else
				{
					// User is not agent.
					self::$isagent = false;
				}
			}
			else
			{
				// Get agent id from url
				$input = self::getInput();
				$get_agentid = $input->getString('agent', '');

				if ( $get_agentid )
				{
					$get_agentid = self::sanitizeBase64( $get_agentid );
					$agentid = self::decrypt( $get_agentid );
					$agent_user = new User($agentid);
					$agent_administrator = isset( $agent_user ) && !empty( $agent_user ) ? $agent_user->authorise('core.admin') : false;

					// Check if the agen id is valid
					if ( $agentid && ( in_array( $agentid, $agents ) || $agent_administrator ) )
					{
						self::$isagent = $agentid;
					}
					else
					{
						self::$isagent = false;
					}
				}
				else
				{
					self::$isagent = false;
				}
			}
			return self::$isagent;
		}
	}

	/**
	 * Get Agent user id by Department id
	 */
	public static function AgentByDepartmentId( $department_id )
	{
		$db = self::getDatabase();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('agent_id'))
			->from($db->quoteName('#__gs_tickets_departments'))
            ->where($db->quoteName('id') . ' = :id')
			->bind(':id', $department_id, ParameterType::INTEGER);
		$db->setQuery($query);
		$result = $db->loadResult();

		if ( !empty( $result ) && $result )
		{
			return self::encrypt( $result );
		}

        return false;
	}

	/**
	 * Get list of agents
	 */
	public static function getAgents()
	{
		$db = self::getDatabase();
		$query = $db->getQuery(true);
		$query
            ->select($db->quoteName('agent_id'))
            ->from($db->quoteName('#__gs_tickets_departments'));
		$db->setQuery($query);
		$result = $db->loadColumn();
		return !empty( $result ) ? $result : false;
	}

	/**
	 * Encrtypted Agent Id
	 */
	public static function AgentIdEncrypt()
	{
		if ( self::isAgent() )
		{
			$agent_id = self::isAgent();
			return self::encrypt( $agent_id );
		}
		else
		{
			return false;
		}
	}

	/**
	 * Encrtypted Agent Id
	 */
	public static function AgentIdDecrypt( $encrypted_agent_id )
	{
		return self::decrypt( $encrypted_agent_id );
	}

	/**
	 * Encrypt data
	 */
	public static function encrypt( $string )
	{
		$encryption_key = self::encryptionKey();

		if ( $encryption_key === false )
		{
			return false;
		}

		$cipher     = 'AES-256-CBC';
        $options    = OPENSSL_RAW_DATA;
        $hash_algo  = 'sha256';
        $sha2len    = 32;
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($string, $cipher, $encryption_key, $options, $iv);
        $hmac = hash_hmac($hash_algo, $ciphertext_raw, $encryption_key, true);

		$result = base64_encode( $iv . $hmac . $ciphertext_raw );
		return $result;
	}

	/**
	 * Decrypt data
	 */
	public static function decrypt( $encrypted_user_string )
	{
		$encrypted_string = base64_decode( $encrypted_user_string );
		$encryption_key = self::encryptionKey();

		if ( $encryption_key === false )
		{
			return false;
		}

		$cipher     = 'AES-256-CBC';
        $options    = OPENSSL_RAW_DATA;
        $hash_algo  = 'sha256';
        $sha2len    = 32;
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($encrypted_string, 0, $ivlen);
        $hmac = substr($encrypted_string, $ivlen, $sha2len);
        $ciphertext_raw = substr($encrypted_string, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $encryption_key, $options, $iv);
        $calcmac = hash_hmac($hash_algo, $ciphertext_raw, $encryption_key, true);

		if ( function_exists( 'hash_equals' ) ) {
            if ( hash_equals( $hmac, $calcmac ) )
			{
				return $original_plaintext;
			}
        } else {
            if ( self::verifyDecryptHash( $hmac, $calcmac ) )
			{
				return $original_plaintext;
			}
        }
	}

	// Verify decrypt hash
	private static function verifyDecryptHash($knownString, $userString)
	{
		if (function_exists('mb_strlen')) {
            $kLen = mb_strlen($knownString, '8bit');
            $uLen = mb_strlen($userString, '8bit');
        } else {
            $kLen = strlen($knownString);
            $uLen = strlen($userString);
        }
        if ($kLen !== $uLen) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < $kLen; $i++) {
            $result |= (ord($knownString[$i]) ^ ord($userString[$i]));
        }
        return 0 === $result;
	}

	/**
     * Generate secure password
     */
    public static function password()
    {
		$lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '1234567890987654321';
        $special = '!#^*!#^**^#!';
		$keys = $lowercase . $special . $uppercase . $numbers;
		$limit = rand(15, 19);
		return substr( str_shuffle( $keys ), 0, $limit );
    }

	/**
	 * Encryption key
	 */
	public static function encryptionKey()
	{
		$encryption_key = self::config()->encryption_key;

		if ( $encryption_key )
		{
			return $encryption_key;
		}

		// Generate encryption key if not exist
		$app  = Factory::getApplication();
		$new_key = self::password();

		$db = self::getDatabase();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__gs_tickets_config'))
			->set($db->quoteName('value') . ' = :value')
			->where($db->quoteName('name') . ' = ' . $db->quote('encryption_key'))
			->bind(':value', $new_key, ParameterType::STRING);

		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			$app->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return $new_key;
	}

	/**
	 * Upload files
	 */
	public static function uploadFiles( $files, $allowed_extensions, $allowed_total_filesize, $maximum_allowed_files, $ticket_id = '', $redirectUrl = '', $isprofile_picture = false )
	{
		$app  = Factory::getApplication();

		// Validate necessary data
		if ( !is_array( $files ) || !is_array( $allowed_extensions ) )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_CANNOT_SUBMIT_FILES'), 'error');
			return false;
		}

		// Make sure that file uploads are enabled in php.
        if (!(bool) ini_get('file_uploads')) {
            $app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_UPLOAD_DISABLED'), 'error');
            return false;
        }

		$upload_dir = self::uploadDirectory();
		$filedata = [];

		if ( $isprofile_picture === true )
		{
			$upload_dir = self::uploadDirectory(true);
		}

		// Check upload directory
		if ( $upload_dir === false )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_NO_UPLOAD_DIRECTORY'), 'error');
            return false;
		}

		// Check files size
		$uploaded_total_filesize = 0;
		$file_uploaded = 0;
		foreach ( $files as $file ) {
			if ($file['error'] != 4) {
				$uploaded_total_filesize += $file['size'];
				$file_uploaded++;
			}
		}

		// Check maximum allowed file uploads
		if ( $file_uploaded > $maximum_allowed_files )
		{
			$app->enqueueMessage(Text::sprintf('COM_GUESTSUPPORT_FILE_UPLOAD_FILE_LIMIT_EXCEDED', $maximum_allowed_files), 'error');
            return false;
		}

		$allowed_filesize_inbyte = $allowed_total_filesize * 1000;

		if ( $uploaded_total_filesize > $allowed_filesize_inbyte ) {
			$readable_filesize = self::kbtomb( $allowed_total_filesize );
			$app->enqueueMessage(Text::sprintf('COM_GUESTSUPPORT_FILE_UPLOAD_FILE_SIZE_EXCEDED', $readable_filesize), 'error');
            return false;
		}

		// Process each file
		foreach ($files as $file) {
			// proceed if has file
			if ($file['error'] != 4) {
				$file_errors = 0;
				$tmp_path = $file['tmp_name'];
				$original_name = $file['name'];
				$fileClass = self::getFileClass();
				$filename = $fileClass::makeSafe( $original_name );

				$file_name_noext = preg_replace('#\.[^.]*$#', '', $filename);
				$file_ext = strtolower($fileClass::getExt( $filename ));

				// If file isn't safe
				if ( $file_ext !== 'zip' && InputFilter::isSafeFile( $file ) === false )
				{
					$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_UPLOAD_NOT_SAFE'), 'error');
            		return false;
				}

				// If php maximum upload limit exceded
				if ( $file['error'] == 1 )
				{
					$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_UPLOAD_SERVER_SIZE_EXCEDED'), 'error');
            		return false;
				}

				// Check file extension after sanitization
				if ( !$file_ext )
				{
					$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_UPLOAD_INVALID_EXTENSION'), 'error');
            		return false;
				}

				// Check if file extension is in allowed file types
				if ( !in_array( $file_ext, $allowed_extensions ) )
				{
					$valid_extensions = implode( ', ', $allowed_extensions );
					$app->enqueueMessage(Text::sprintf('COM_GUESTSUPPORT_FILE_UPLOAD_INVALID_AND_ALLOWED_EXTENSION', $valid_extensions), 'error');
					return false;
				}

				// Neglect other than non-alphanumeric characters, hyphens & underscores.
				$safeFileName = preg_replace(array("/[\\s]/", '/[^a-zA-Z0-9_\-]/'), array('_', ''), $file_name_noext);

				// Check if filename remains after sanitizing
				if ( !$safeFileName )
				{
					$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_UPLOAD_INVALID_FILE_NAME'), 'error');
            		return false;
				}

				// Check for broken files
				if ($file['error'] == 3) {
					$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_UPLOAD_BROKEN_FILES'), 'error');
            		return false;
				}

				// Everything Okay, now try to upload file
				$file_name_raw = $safeFileName . '.' . $file_ext;
				$time = time();
				$otp = self::otp(8);

				if ( !$ticket_id )
				{
					$ticket_id = self::randomString(11);
				}

				$unique_name = $safeFileName . $time . $otp  . $ticket_id . '.' . $file_ext;
				$file_name_enc = md5( $unique_name ) . '.' . $file_ext;
				$upload_dest = $upload_dir . '/' . $file_name_enc;

				// Prepare files data
				$filedata[] = array(
					'file_name_raw' => $file_name_raw,
					'file_name_enc' => $file_name_enc,
					'file_size' => $file['size'],
				);

				if ( !move_uploaded_file( $tmp_path, $upload_dest ) )
				{
					$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_UPLOAD_FAILED'), 'error');
            		return false;
				}
			}
		}
		return $filedata;
	}

	/**
	 * Add file informations to database
	 */
	public static function filesToDb( $filedata, $ticket_id, $ticket_message_id )
	{
		if ( !is_array( $filedata ) )
		{
			return false;
		}

		$db = self::getDatabase();

		$created = Factory::getDate()->toSql();
		$attach_error = 0;
		$uploadData = array();
		$count = 0;
		$created_by = self::isAgent() ? 'agent' : 'user';
		foreach ( $filedata as $file ) {
			$count++;
			$query = $db->getQuery(true);

			$columns = [
				'file_ticket_id',
				'file_ticket_message_id',
				'file_name_raw',
				'file_name_enc',
				'file_size',
				'file_created',
				'created_by'
			];

			$values = [
				':file_ticket_id',
				':file_ticket_message_id',
				':file_name_raw',
				':file_name_enc',
				':file_size',
				':file_created',
				':created_by'
			];

			$query
				->insert($db->quoteName('#__gs_tickets_attachments'), false)
				->columns($db->quoteName($columns))
				->values(implode(', ', $values))
				->bind(':file_ticket_id', $ticket_id, ParameterType::STRING)
				->bind(':file_ticket_message_id', $ticket_message_id, ParameterType::INTEGER)
				->bind(':file_name_raw', $file['file_name_raw'], ParameterType::STRING)
				->bind(':file_name_enc', $file['file_name_enc'], ParameterType::STRING)
				->bind(':file_size', $file['file_size'], ParameterType::INTEGER)
				->bind(':file_created', $created)
				->bind(':created_by', $created_by, ParameterType::STRING);

			$db->setQuery($query);

			try {
				$db->execute();
			} catch (ExecutionFailureException $e) {
				return false;
			}

			$insertid = (int) $db->insertid();

			if ( isset( $insertid ) && !empty( $insertid ) && $insertid )
			{
				$arrid = 'data_' . $count;
				$uploadData[$arrid]['id'] = $insertid;
				$uploadData[$arrid]['name'] = $file['file_name_raw'];
				$uploadData[$arrid]['size'] = $file['file_size'];
			}
			else
			{
				$attach_error++;
			}
		}

		if ( $attach_error )
		{
			return false;
		}
		return $uploadData;
	}

	/**
	 * AttachmentsBaseUrl
	 */
	public static function AttachmentsBaseUrl()
	{
		return Uri::root() . 'images/com_guestsupport/attachments/';
	}

	/**
	 * Create Upload Directory
	 */
	public static function uploadDirectory( $picture = false )
	{
		// Check if $_uploadDirectory is already set and return it
		if ( $picture === false && isset( self::$_uploadDirectory ) && self::$_uploadDirectory !== null ) {
			return self::$_uploadDirectory;
		} elseif ( $picture === true && isset( self::$_uploadDirectory_picture ) && self::$_uploadDirectory_picture !== null ) {
			return self::$_uploadDirectory_picture;
		}

		// Define upload directory path
		$dir_path = JPATH_SITE . '/images/com_guestsupport/attachments';

		// Create upload directory if not exists
		if ( ! is_dir( $dir_path ) ) {
			if ( ! mkdir( $dir_path, 0755, true ) ) {
				return false;
			}
		}

		// Ensure directory is writable
		if ( ! is_writable( $dir_path ) ) {
			if ( ! chmod( $dir_path, 0755 ) ) {
				return false;
			}
		}

		// Create index.html file inside upload directory
		if ( ! file_exists( $dir_path . '/index.html' ) ) {
			$data = "<!DOCTYPE html><title></title>";

			$file = fopen( $dir_path . '/index.html', 'w' );
			if ( $file ) {
				fwrite( $file, $data );
				fclose( $file );
			} else {
				return false; // Handle file creation failure
			}
		}

		// Create .htaccess file inside upload directory
		$view_attachments = self::getConfig( 'view_attachments', 'browser' );
		$htaccess_file = $dir_path . '/.htaccess';
		if ( $view_attachments === 'download' )
		{
			if ( ! file_exists( $htaccess_file ) ) {
				$data = 'Deny from all';

				$file = fopen( $htaccess_file, 'w' );
				if ( $file ) {
					fwrite( $file, $data );
					fclose( $file );
				} else {
					return false; // Handle file creation failure
				}
			}
		}
		elseif ( file_exists( $htaccess_file ) )
		{
			unlink( $htaccess_file );
		}

		// Final check to ensure directory exists and is writable
		if ( ! is_dir( $dir_path ) || ! is_writable( $dir_path ) ) {
			return false;
		}

		// Set the upload directory variable
		self::$_uploadDirectory = $dir_path;

		// Handle picture directory if $picture is true
		if ( $picture === true ) {
			$dir_path = JPATH_SITE . '/images/com_guestsupport';

			// Ensure the directory exists, create if not
			if ( ! is_dir( $dir_path ) ) {
				if ( ! mkdir( $dir_path, 0755, true ) ) {
					return false; // Handle failure to create directory
				}
			}

			// Ensure the directory is writable
			if ( ! is_writable( $dir_path ) ) {
				if ( ! chmod( $dir_path, 0755 ) ) {
					return false; // Handle failure to change permissions
				}
			}

			self::$_uploadDirectory_picture = $dir_path;
		}

		return $dir_path;
	}

	/**
	 * KB to MB
	 */
	public static function kbtomb($kb)
	{
		$kb = (int) $kb;
		if ( $kb < 1000 )
		{
			return $kb . ' KB';
		}
		else
		{
			$mb = $kb / 1000;
			return $mb . ' MB';
		}
	}

	/**
	 * Bytes to KB and MB
	 */
	public static function bytesToReadable($bytes)
	{
		$kb = (int) $bytes / 1000;
		if ( $kb < 1000 )
		{
			return round( $kb ) . ' KB';
		}
		else
		{
			$mb = $kb / 1000;
			return round( $mb ) . ' MB';
		}
	}

	/**
	 * Generate OTP
	 */
	public static function otp($length = 6)
	{
		$lengthMinus = $length - 1;
		$firstNumber = 1 . str_repeat(0, $lengthMinus);
		$secondNumber = str_repeat(9, $length);
		return rand($firstNumber, $secondNumber);
	}

	/**
	 * Send mail
	 */
	public static function sendMail( $to, $subject, $message )
	{
		$config = self::config();
		$jconfig = Factory::getApplication()->getConfig();

		$replyto_name = self::getConfig('replyto_name', '');
		$replyto_email = self::getConfig('replyto_email', '');

		// Get from name and email
		$from_name = $config->mail_from_name ? $config->mail_from_name : $jconfig->get('fromname');
		$from_email = $config->mail_from_email ? $config->mail_from_email : $jconfig->get('mailfrom');
		$replyto_name = $replyto_name ? $replyto_name : $jconfig->get('replytoname');
		$replyto_email = $replyto_email ? $replyto_email : $jconfig->get('replyto');

		$cc = null;
        $bcc = null;
        $attachment = null;

		if ( $config->mailer == 'joomla' )
		{
			// Joomla! default
			// $result = Factory::getMailer()->sendMail($from_email, $from_name, $to, $subject, $message, 1, $cc, $bcc, $attachment, $replyto_email, $replyto_name);
			$mailer = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
		
			try 
			{
				$mailer->sendMail($from_email, $from_name, $to, $subject, $message, 1, $cc, $bcc, $attachment, $replyto_email, $replyto_name);
			}
			catch (\Exception $e)
			{
				Factory::getApplication()->enqueueMessage("Failed to send mail, " . $e->getMessage(), 'error');
				return false;
			}
		}
		else
		{
			// SMTP
			$mailer = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();

			// Set sender
			$mailer->setSender($from_email, $from_name);

			// Set recepient
			$mailer->addRecipient( $to );

			// Set replyto
			if ( $replyto_email )
			{
				$mailer->addReplyTo($replyto_email, $replyto_name);
			}

			// Set subject
			$mailer->setSubject( $subject );

			// Set body
			$mailer->setBody($message);
			$mailer->isHtml(true);
			$mailer->Encoding = 'base64';

			// Set STMP
			$mailer->useSmtp(true, $config->smtp_host, $config->smtp_username, $config->smtp_password, $config->smtp_security, $config->smtp_port);

			// Send mail
			try 
			{
				$mailer->Send();
			}
			catch (\Exception $e)
			{
				Factory::getApplication()->enqueueMessage("Failed to send mail, " . $e->getMessage(), 'error');
				return false;
			}
		}

		return true;
	}

	/**
	 * Get time elapsed from a date time
	 */
	public static function timeElapsed($datetime)
	{
		$ticket_date = new Date($datetime);
		$ticket_timestamp = $ticket_date->toUnix(); // 1354375200

		$date = new Date();
		$date->setTimestamp($ticket_timestamp);

		$now = new Date('now');
		$interval = $date->diff($now);

		$years = $interval->format('%y');
		$months = $interval->format('%m');
		$days = $interval->format('%d');
		$hours = $interval->format('%h');
		$minutes = $interval->format('%i');

		$yearstext		= ' ' . ( $years == 1 ? Text::_('COM_GUESTSUPPORT_YEAR') : Text::_('COM_GUESTSUPPORT_YEARS') ) . ' ';
		$monthstext		= ' ' . ( $months == 1 ? Text::_('COM_GUESTSUPPORT_MONTH') : Text::_('COM_GUESTSUPPORT_MONTHS') ) . ' ';
		$daystext		= ' ' . ( $days == 1 ? Text::_('COM_GUESTSUPPORT_DAY') : Text::_('COM_GUESTSUPPORT_DAYS') ) . ' ';
		$hourstext		= ' ' . ( $hours == 1 ? Text::_('COM_GUESTSUPPORT_HOUR') : Text::_('COM_GUESTSUPPORT_HOURS') ) . ' ';
		$minutestext		= ' ' . ( $minutes == 1 ? Text::_('COM_GUESTSUPPORT_MINUTE') : Text::_('COM_GUESTSUPPORT_MINUTES') ) . ' ';
		$agotext = Text::_('COM_GUESTSUPPORT_AGO');

		if ( $years < 1 )
		{
			if ( $months > 0 )
			{
				// If have month - 1 month 2 days ago
				$result = $months . $monthstext . $days . $daystext . $agotext;
			}
			elseif ( $days > 0 )
			{
				// Doesn't have month but have days - 2 days 5 minutes ago
				$result = $days . $daystext . $hours . $hourstext . $agotext;
			}
			elseif ( $hours > 0 )
			{
				// Doesn't have month and days but have hours - 2 hours 5 minutes ago
				$result = $hours . $hourstext . $minutes . $minutestext . $agotext;
			}
			elseif ( $minutes > 0 )
			{
				// Doesn't have month, days and hours but have minutes - 2 minutes ago
				$result = $minutes . $minutestext . $agotext;
			}
			else
			{
				// Doesn't have month, days, hours and minutes - Few seconds ago.
				$result = Text::_('COM_GUESTSUPPORT_FEW_SECONDS_AGO');
			}
		}
		else
		{
			$result = $years . $yearstext . $months . $monthstext . $agotext;
		}

		return $result;
	}

	/**
	 * Html to editor
	 */
	public static function HtmlToEditor($html)
	{
		return $html;
		/* $data = str_replace( '&', '&amp;', $html );
		return $data; */
	}

	/**
	 * Editor to Html
	 */
	public static function EditorToHtml($html)
	{
		return $html;
		/* $data = str_replace( '&amp;', '&', $html );
		return $data; */
	}

	/**
	 * Random string
	 */
	public static function randomString( $length, $random_lenght = true )
	{
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$chars_length = strlen($chars);
		$random_string = '';

		if ( $random_lenght === true )
		{
			$minlength = (int) $length - 4;
			$length = rand($minlength, $length);
		}

		for($i = 0; $i < $length; $i++) {
			$random_character = $chars[rand(0, $chars_length - 1)];
			$random_string .= $random_character;
		}

		return $random_string;
	}

	/**
	 * Form fields
	 */
	public static function formField($type)
	{
		$label = '';
		$description = '';
		$placeholder_name = '';
		$placeholder_label = '';
		$placeholder_placeholder = '';

		switch ($type) {
			case 'text':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_TEXT_BOX_NAME');
				$placeholder_name = 'product_name';
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_TEXT_BOX_LABEL');
				$placeholder_placeholder = Text::_('COM_GUESTSUPPORT_FORM_FIELD_TEXT_BOX_PLACEHOLDER');
				break;
			case 'email':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_EMAIL_NAME');
				$placeholder_name = 'email';
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_EMAIL_LABEL');
				$placeholder_placeholder = Text::_('COM_GUESTSUPPORT_FORM_FIELD_EMAIL_PLACEHOLDER');
				break;
			case 'number':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_NUMBER_NAME');
				$placeholder_name = 'code';
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_NUMBER_LABEL');
				$placeholder_placeholder = Text::_('COM_GUESTSUPPORT_FORM_FIELD_NUMBER_PLACEHOLDER');
				break;
			case 'textarea':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_TEXTAREA_NAME');
				$placeholder_name = 'summery';
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_TEXTAREA_LABEL');
				$placeholder_placeholder = Text::_('COM_GUESTSUPPORT_FORM_FIELD_TEXTAREA_PLACEHOLDER');
				break;
			case 'select':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_SELECT_NAME');
				$placeholder_name = 'colors';
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_SELECT_LABEL');
				break;
			case 'radio':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_RADIO_NAME');
				$placeholder_name = 'gender';
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_RADIO_LABEL');
				break;
			case 'checkbox':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_CHECKBOX_NAME');
				$placeholder_name = 'buylist';
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_CHECKBOX_LABEL');
				break;
			case 'date':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_DATE_NAME');
				$placeholder_name = 'orderdate';
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_DATE_LABEL');
				break;
			case 'datetime':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_DATETIME_NAME');
				$placeholder_name = '';
				$placeholder_label = '';
				break;
			case 'pluginfield':
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_PLUGIN_FIELD_NAME');
				$placeholder_name = 'order_no';
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_PLUGIN_FIELD_LABEL');
				$placeholder_placeholder = '';
				break;
			case 'departments':
				$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_DEPARTMENT_PLACEHOLDER');
				break;

			default:
				$placeholder_name = '';
				$placeholder_label = '';
				$placeholder_placeholder = '';
				break;
		}

		$html = '<ul id="r_gs_field_type_' . $type . '" class="r-gs-field-contents r-gs-forms-form">';

		if ( $type == 'hidden' )
		{
			// Name
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_HIDDEN_NAME');
			$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_NAME_INFO');
			$html .= self::formFieldItem( 'text', 'name', $label, 'ticket_page', $description );

			// Label
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_LABEL');
			$placeholder_label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_HIDDEN_PLACEHOLDER');
			$html .= self::formFieldItem( 'text', 'label', $label, $placeholder_label );

			// Value
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_HIDDEN_VALUE');
			$html .= self::formFieldItem( 'text', 'value', $label, 'Contact page' );
		}
		elseif ( $type == 'content' )
		{
			// Name
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_CONTENT_NAME');
			$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_NAME_INFO');
			$html .= self::formFieldItem( 'text', 'name', $label, 'privacy', $description );

			// Value
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_CONTENT_LABEL');
			$html .= self::formFieldItem( 'textarea', 'content', $label, 'Before submitting this form, read our privacy policy <a href=&quot;privacy-policy&quot;>here</a>.' );
		}
		elseif ( $type == 'departments' )
		{
			// Label
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_LABEL');
			$html .= self::formFieldItem( 'text', 'label', $label, $placeholder_label );

			// Show on form
			$options = array(
				'1' => Text::_('COM_GUESTSUPPORT_YES'),
				'0' => Text::_('COM_GUESTSUPPORT_NO'),
			);
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_DEPARTMENT_SHOW_ON_FORM');
			$html .= self::formFieldItem( 'real_radio', 'showonform_departments', $label, '', '', '', $options );

			// Show list of departments
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_DEPARTMENT_LABEL');
			$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_DEPARTMENT_LABEL_DESC');
			$html .= self::formFieldItem( 'real_departments', 'default_department', $label, '', $description );
		}
		else
		{
			// Name
			$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_NAME_INFO');
			$class = $type === 'pluginfield' ? 'r_gs_field_pluginfield_name' : '';
			$html .= self::formFieldItem( 'text', 'name', $label, $placeholder_name, $description, $class );

			// Label
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_LABEL');
			$html .= self::formFieldItem( 'text', 'label', $label, $placeholder_label );

			// Placeholder
			if ( $type == 'text' || $type == 'email' || $type == 'number' || $type == 'textarea' )
			{
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_PLACEHOLDER');
				$html .= self::formFieldItem( 'text', 'placeholder', $label, $placeholder_placeholder );
			}

			if ( $type == 'select' )
			{
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_SELECT_OPTIONS');
				$placeholder = '--Select a color--&#10;Blue Color&#10;Red Color&#10;No extra color';
				$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_OPTIONS_DESC');
				$html .= self::formFieldItem( 'textarea', 'options', $label, $placeholder, $description );
			}

			if ( $type == 'radio' )
			{
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_RADIO_OPTIONS');
				$placeholder = 'Male&#10;Female';
				$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_OPTIONS_DESC');
				$html .= self::formFieldItem( 'textarea', 'options', $label, $placeholder, $description );
			}

			if ( $type == 'checkbox' )
			{
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_CHECKBOX_OPTIONS');
				$placeholder = 'T-Shirt&#10;Sunglass&#10;Shoes&#10;Watch';
				$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_OPTIONS_DESC');
				$html .= self::formFieldItem( 'textarea', 'options', $label, $placeholder, $description );
			}

			if ( $type == 'pluginfield' )
			{
				$class = $type === 'pluginfield' ? 'r_gs_field_pluginfield_placeholder' : '';
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_PLUGIN_FIELD_PLACEHOLDER_FOR_PLUGIN');
				$placeholder = '{guest_support_field_THIS_FIELD_NAME}';
				$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_PLUGIN_FIELD_PLACEHOLDER_FOR_PLUGIN_DESC');
				$html .= self::formFieldItem( 'text', 'placeholder', $label, $placeholder, $description, $class );
			}

			// Add minimum and maximum option for number field
			if ( $type == 'number' )
			{
				// Minimum allowed
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_MIN_VALUE');
				$html .= self::formFieldItem( 'number', 'min', $label, '1' );

				// Maximum allowed
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_MAX_VALUE');
				$html .= self::formFieldItem( 'number', 'max', $label, '99' );

				// Step
				$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_STEP');
				$html .= self::formFieldItem( 'number', 'step', $label, '0.05' );
			}

			// Required?
			$html .= self::formFieldItem( 'required' );

			// Description
			$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_DESCRIPTION');
			$html .= self::formFieldItem( 'description', '', '', '', $description );

			// Error Message
			$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_ERROR_MSG');
			$html .= self::formFieldItem( 'error_message', '', '', '', $description );
		}

		// Input type
		$html .= '<li class="r_gs_field_list_input_type r_gs_field_list_nopadding">';
		$html .= '<input type="hidden" id="r_gs_this_field_input_type" value="' . $type . '">';
		$html .= '</li>';

		// Column size
		$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_COLUMN_SIZE');
		$html .= self::formFieldItem( 'colsize', 'colsize', $label );

		// Field to Form
		$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_ADD_FIELD_ON');
		$html .= self::formFieldItem( 'fieldtoform', 'fieldtoform', $label );

		if ( $type != 'content' )
		{
			// Show to users
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_SHOW_TO_USER_LABEL');
			$html .= self::formFieldItem( 'showonticketview', 'showonticketview', $label );

			// Attach to email message
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_ATTACH_TO_EMAIL_LABEL');
			$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_ATTACH_TO_EMAIL_DESC');;
			$html .= self::formFieldItem( 'addtoemail', 'addtoemail', $label, '', $description );

			// Login required
			$label = Text::_('COM_GUESTSUPPORT_FORM_FIELD_LOGIN_REQUIRED_LABEL');
			$description = Text::_('COM_GUESTSUPPORT_FORM_FIELD_LOGIN_REQUIRED_DESC');
			$html .= self::formFieldItem( 'login_required', 'login_required', $label, '', $description );
		}

		$html .= '</ul>';
		return $html;
	}

	/**
	 * Form field item
	 */

	private static function formFieldItem($type = 'text', $for = '', $label = '', $placeholder = '', $description = '', $class = '', $options = array())
	{
		$app = Factory::getApplication();
		$html = '';
		if ( $type == 'required' )
		{
			$for = 'required';
		}
		elseif ( $type == 'error_message' ) {
			$for = 'error_message';
		}
		$html .= '<li class="r_gs_field_list_' . $for . '">';
		if ( $type == 'text' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>';
			$html .= '<input type="text" name="data[]" id="field_' . $for . '" class="' . $class . '" size="40" placeholder="' . $placeholder . '">';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'number' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>';
			$html .= '<input type="number" name="data[]" id="field_' . $for . '" size="40" placeholder="' . $placeholder . '">';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'textarea' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>';
			$html .= '<textarea name="data[]" id="field_' . $for . '" placeholder="' . $placeholder . '" rows="5" cols="40"></textarea>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'required' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_required">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_REQUIRED') . '</label>';
			$html .= '<select name="data[]" id="field_required">';
			$html .= '<option value="1">' . Text::_('COM_GUESTSUPPORT_YES') . '</option>';
			$html .= '<option value="0">' . Text::_('COM_GUESTSUPPORT_NO') . '</option>';
			$html .= '</select>';
			$html .= '</div>';
		}
		elseif ( $type == 'description' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_description">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_DESCRIPTION_LABEL') . '</label>';
			$html .= '<textarea name="data[]" id="field_description" placeholder="' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_DESCRIPTION_DESC') . '" rows="3" cols="40"></textarea>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'error_message' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_error_message">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_ERROR_MESSAGE_LABEL') . '</label>';
			$html .= '<textarea name="data[]" id="field_error_message" placeholder="" rows="3" cols="40"></textarea>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'real_radio' )
		{
			$html .= '<div class="form-field r-gs-grid">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>&nbsp;&nbsp;&nbsp;';
			$html .= '<ul class="r-gs-field-checkbox">';
			if ( !empty( $options ) )
			{
				foreach ($options as $key => $value) {
					$html .= '<li><input type="radio" name="' . $for . '" id="' . $for . '_' . $key . '" value="' . $key . '"><label for="' . $for . '" class="r-gs-field-checkbox-label"> ' . $value . '</label></li>';
				}
			}
			$html .= '</ul>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'real_checkbox' )
		{
			$html .= '<div class="form-field r-gs-grid">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>&nbsp;&nbsp;&nbsp;';
			$html .= '<ul class="r-gs-field-checkbox">';
			if ( !empty( $options ) )
			{
				foreach ($options as $key => $value) {
					$html .= '<li><input type="checkbox" name="' . $for . '" id="' . $for . '_' . $key . '" value="' . $key . '"><label for="' . $for . '_' . $key . '" class="r-gs-field-checkbox-label"> ' . $value . '</label></li>';
				}
			}
			$html .= '</ul>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'real_departments' )
		{
			$input = self::getInput();
            $form_id = $input->getInt('id', 0);
			$departments = self::departments($form_id);
			$html .= '<div class="form-field list-departments" style="display:none;">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>';
			$html .= '<select name="' . $for . '" id="field_' . $for . '">';
			if ( !empty( $departments ) )
			{
				foreach ($departments as $item) {
					$html .= '<option value="' . $item->id . '">' . $item->name . '</option>';
				}
			}
			$html .= '</select>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'colsize' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>';
			$html .= '<select id="r_gs_field_list_colsize">';
			$html .= '<option value="100">100%</option>';
			$html .= '<option value="50">50%</option>';
			$html .= '<option value="33-3">33.333%</option>';
			$html .= '</select>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'fieldtoform' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>';
			$html .= '<select id="r_gs_field_list_fieldtoform">';
			$html .= '<option value="ticket">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_FIELD_TO_FORM_TICKET') . '</option>';
			$html .= '<option value="reply">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_FIELD_TO_FORM_REPLY') . '</option>';
			$html .= '<option value="both">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_FIELD_TO_FORM_BOTH') . '</option>';
			$html .= '</select>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'showonticketview' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>';
			$html .= '<select id="r_gs_field_list_showonticketview">';
			$html .= '<option value="hide">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_SHOW_ON_TICKET_VIEW_HIDE') . '</option>';
			$html .= '<option value="users">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_SHOW_ON_TICKET_VIEW_USERS') . '</option>';
			$html .= '<option value="agents">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_SHOW_ON_TICKET_VIEW_AGENTS') . '</option>';
			$html .= '<option value="both">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_SHOW_ON_TICKET_VIEW_BOTH') . '</option>';
			$html .= '</select>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'addtoemail' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>';
			$html .= '<select id="r_gs_field_list_addtoemail">';
			$html .= '<option value="ticket">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_ADD_TO_EMAIL_TICKET') . '</option>';
			$html .= '<option value="reply">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_ADD_TO_EMAIL_REPLY') . '</option>';
			$html .= '<option value="both" selected>' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_ADD_TO_EMAIL_BOTH') . '</option>';
			$html .= '<option value="none">' . Text::_('COM_GUESTSUPPORT_FORM_FIELD_ADD_TO_EMAIL_NONE') . '</option>';
			$html .= '</select>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		elseif ( $type == 'login_required' )
		{
			$html .= '<div class="form-field">';
			$html .= '<label for="field_' . $for . '">' . $label . '</label>';
			$html .= '<select id="r_gs_field_list_login_required">';
			$html .= '<option value="0" selected>' . Text::_('COM_GUESTSUPPORT_NO') . '</option>';
			$html .= '<option value="1">' . Text::_('COM_GUESTSUPPORT_YES') . '</option>';
			$html .= '</select>';
			if ( $description )
			{
				$html .= '<p>' . $description . '</p>';
			}
			$html .= '</div>';
		}
		$html .= '</li>';
		return $html;
	}

	/**
	 * Generate short ticket id
	 */
	public static function shortTicketId()
	{
		$db = self::getDatabase();

		$query = $db->getQuery(true);
		$query
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__gs_tickets'))
			->order($db->quoteName('id') . ' DESC')
			->setLimit(1);
		$db->setQuery($query);
		$lastticketid = $db->loadResult();

		$newid = (int) $lastticketid + 101;

		$random_numbers = self::otp(3);

		return $newid . $random_numbers;
	}

	/**
	 * Convert URLs to Links
	 */
	public static function convert_urls_to_links( $text )
	{
		// Split the text by existing anchor tags to avoid modifying them
		$parts = preg_split( '/(<a\s[^>]*?>.*?<\/a>)/i', $text, -1, PREG_SPLIT_DELIM_CAPTURE );

		foreach ( $parts as &$part )
		{
			// If the part contains an existing anchor tag, remove any inline style attribute
			if ( strpos( $part, '<a ' ) !== false )
			{
				$part = preg_replace( '/(<a\s[^>]*?)style=("|\')(.*?)\2([^>]*>)/i', '$1$4', $part );
			}
			else
			{
				// Regular expression to match various URL formats
				$pattern = '/\b((https?:\/\/(www\.)?)|(www\.))([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[^\s<]*)?\/?\b(?![\w\-])/i'; // Very Good
	
				// Callback function to create HTML anchor tags
				$part = preg_replace_callback( $pattern, function( $matches )
				{
					// Full matched URL
					$url = $matches[0];

					// If the URL doesn't have a protocol, add https:// by default
					if ( !preg_match( '/^https?:\/\//i', $url ) && !preg_match( '/^ftp:\/\//i', $url ) )
					{
						$url = 'https://' . $url;
					}

					// Return the anchor tag with the full URL as the href
					return '<a href="' . $url . '" target="_blank">' . $matches[0] . '</a>';
				}, $part );
			}
		}

		// Reassemble the parts back into a single string
		$final_text = implode( '', $parts );

		// Move the trailing slash to the correct position
		return str_replace( '</a>/', '/</a>', $final_text );
	}
}
