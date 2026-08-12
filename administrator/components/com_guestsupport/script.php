<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;
use Joomla\CMS\Version;

class com_GuestsupportInstallerScript
{
    /**
     * Get database connection compatible with both Joomla 4 and 5
     *
     * @return \Joomla\Database\DatabaseInterface
     */
    private function getDatabase()
    {
        $version = new Version();
        if ((int) $version::MAJOR_VERSION < 5) {
            // Joomla 4.x
            return Factory::getDbo();
        } else {
            // Joomla 5.x
            return Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        }
    }

    /**
     * This method is called after a component is installed.
     *
     * @param  \stdClass $adapter - Parent object calling this method.
     *
     * @return void
     */
    public function install(InstallerAdapter $adapter)
    {
		$db = $this->getDatabase();

		/**
		 * Configurations
		 */
		// Check configurations
		$query = $db->getQuery(true);
		$query
			->select('COUNT(*)')
            ->from($db->quoteName('#__gs_tickets_config'));
		$db->setQuery($query);
		$results = (int) $db->loadResult();

		if ( $results < 1 )
		{
			// Insert configurations
			$dataTypes = [
				ParameterType::NULL,
				ParameterType::NULL
			];
			$query->clear()
				->insert($db->quoteName('#__gs_tickets_config'))
				->columns(
					[
						$db->quoteName('name'),
						$db->quoteName('value')
					]
				);
				$query->values(implode(',', $query->bindArray(['recaptcha_secret_key', ''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['recaptcha_site_key', ''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['recaptcha_version', '3'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['recaptcha_score','0.5'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['reopen_closed_ticket','both'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['kbs_ignore_words','how,to,what,is,can,i,do,you,add,edit'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['can_edit_replies','both'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['edit_reply_type','last'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['edit_replies_globally','yes'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['can_delete_tickets','yes'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['can_delete_replies','yes'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['tickets_per_page','20'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['create_ticket_url','submit-ticket/'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['mailer','joomla'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['mail_from_name','Support'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['mail_from_email',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['smtp_username',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['smtp_password',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['smtp_host',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['smtp_port','465'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['smtp_security','ssl'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['enable_signature','both'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['email_signature','both'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['encryption_key',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['scroll_to_last','yes'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['message_direction','top'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['guide',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['auto_close_tickets','7'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['pre_close_email','6'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['tickets_by_agent','yes'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['ask_for_review','yes'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['after_new_ticket_created','show_message'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['new_ticket_confirmation_message','<p>Thank you {name}, for submitting your ticket. Your Ticket ID is <strong>{short_ticket_id}</strong>. You can view your ticket and its status by following <a href="http://{ticket_link}">this link</a>.</p>'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['imap_security','ssl'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['imap_host','imap.gmail.com'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['imap_username',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['imap_password',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['imap_port','993'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['imap_connected',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['imap_ticket_form_id','1'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['imap_ticket_department_id','1'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['replyto_email',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['replyto_name',''], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['view_attachments','browser'], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['imap_processing_limit','10'], $dataTypes)));

			$db->setQuery($query);
			$db->execute();
		}

		/**
		 * Departments
		 */
		// Check Departments
		$query->clear()
			->select('COUNT(*)')
            ->from($db->quoteName('#__gs_tickets_departments'));
		$db->setQuery($query);
		$results = (int) $db->loadResult();

		if ( $results < 1 )
		{
			// Insert Department
			$dataTypes = [
				ParameterType::STRING,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING
			];
			$query->clear()
				->insert($db->quoteName('#__gs_tickets_departments'))
				->columns(
					[
						$db->quoteName('name'),
						$db->quoteName('agent_id'),
						$db->quoteName('additional_emails'),
						$db->quoteName('additional_emails_type'),
						$db->quoteName('type')
					]
				);
				$query->values(implode(',', $query->bindArray(['Customer Support',0,'','both','core'], $dataTypes)));

			$db->setQuery($query);
			$db->execute();
		}

		/**
		 * Email Templates
		 */
		// Check Email Templates
		$query->clear()
			->select('COUNT(*)')
            ->from($db->quoteName('#__gs_tickets_email_templates'));
		$db->setQuery($query);
		$results = (int) $db->loadResult();

		if ( $results < 1 )
		{
			// Insert Email Templates
			$dataTypes = [
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::NULL,
				ParameterType::NULL,
				ParameterType::STRING,
				ParameterType::INTEGER
			];
			$query->clear()
				->insert($db->quoteName('#__gs_tickets_email_templates'))
				->columns(
					[
						$db->quoteName('name'),
						$db->quoteName('type'),
						$db->quoteName('subject'),
						$db->quoteName('template'),
						$db->quoteName('lang'),
						$db->quoteName('active')
					]
				);
				$query->values(implode(',', $query->bindArray(['After new ticket created - Send to User','ticket_email_user_message','[{short_ticket_id}] {subject}','<p>Dear {name}, This is a confirmation that, we received your support ticket "{subject}" successfully. One of our support Agent will get back to you as soon as possible.</p><h4>Your Message:</h4><p>{ticket_message} {custom_fields} {attachments}</p><p><strong>Best regards,<br></strong>{SiteName} Support Team</p><p><a href="http://{ticket_link}">View and reply here</a>.</p><p>Ref: {email_ref}</p>','en',1], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['After new ticket created - Send to Agent','ticket_email_agent_message','[{short_ticket_id}] {subject}','<p>Dear {agent_name}, A support ticket needs your attention. Please attend to this ticket as soon as possible.</p><h4>Ticket Subject:</h4><p>{subject}<strong> </strong></p><h4>Ticket Message:</h4><p>{message} {custom_fields} {attachments}</p><p><strong>Best regards,<br></strong>{SiteName} Support Team</p><p><a href="http://{ticket_link}">View and reply here</a>.</p><p>Ref: {email_ref}</p>','en',1], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['After new ticket created - Send to Additional email addresses','ticket_email_additional_message','[{short_ticket_id}] {subject}','<p>A support ticket needs your attention. Please attend to this ticket as soon as possible.</p><h4>Ticket Subject:</h4><p>{subject}<strong> </strong></p><h4>Ticket Message:</h4><p>{message} {custom_fields} {attachments}</p><p><strong>Best regards,<br></strong>{SiteName} Support Team</p><p><a href="http://{ticket_link}">View and reply here</a>.</p><p>Ref: {email_ref}</p>','en',1], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['After ticket reply - Send to User','reply_email_user_message','[{short_ticket_id}] {subject}','<p>Dear {name}, Your support ticket "{subject}" has been replied by {agent_name}.</p><h4>Reply Message:</h4><p>{message} {custom_fields} {attachments}</p><p><strong>Best regards,<br></strong>{SiteName} Support Team</p><p><a href="http://{ticket_link}">View and reply here</a>.</p><p>Ref: {email_ref}</p>','en',1], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['After ticket reply - Send to Agent','reply_email_agent_message','[{short_ticket_id}] {subject}','<p>Dear {agent_name}, Support ticket "{subject}" replied by {name}. Please attend to this ticket as soon as possible.</p><h4>Ticket Subject:</h4><p>{subject}<strong> </strong></p><h4>Reply Message:</h4><p>{message} {custom_fields} {attachments}</p><p><strong>Best regards,<br></strong>{SiteName} Support Team</p><p><a href="http://{ticket_link}">View and reply here</a>.</p><p>Ref: {email_ref}</p>','en',1], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['After ticket reply - Send to Additional email addresses','reply_email_additional_message','[{short_ticket_id}] {subject}','<p>Support ticket "{subject}" replied by {name}. Please attend to this ticket as soon as possible.</p><h4>Ticket Subject:</h4><p>{subject}<strong> </strong></p><h4>Reply Message:</h4><p>{message} {custom_fields} {attachments}</p><p><strong>Best regards,<br></strong>{SiteName} Support Team</p><p><a href="http://{ticket_link}">View and reply here</a>.</p><p>Ref: {email_ref}</p>','en',1], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['Send pre ticket closing notification to User','user_pre_ticket_close_message','[{short_ticket_id}] {subject}','<p>Dear {name},</p><p>Your support ticket "{subject}" will close automatically after 24 hours due to inactivity if no action taken.</p><p>If your requested issue was not resolved, you can <a href="http://{ticket_link}">reply here</a>.</p><p><strong>Best regards,<br></strong>{SiteName} Support Team</p><p>Ref: {email_ref}</p>','en',1], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['Send pre ticket closing notification to Agent','agent_pre_ticket_close_message','[{short_ticket_id}] {subject}','<p>Dear {name},</p><p>Support ticket "{subject}" is still waiting for your reply. If no action taken it will close automatically after 24 hours due to inactivity.</p><p>You can <a href="http://{ticket_link}">view and reply here</a>.</p><p><strong>Best regards,<br /></strong>{SiteName} Support Team</p><p>Ref: {email_ref}</p>','en',1], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['Send to User after closing tickets automatically','ticket_close_message','[{short_ticket_id}] {subject}','<p>Dear {name},</p><p>Your support ticket "{subject}" has been closed automatically due to inactivity.</p><p>If your requested issue was not resolved, please submit new ticket and we will get back to you asap.</p><p><strong>Best regards,<br /></strong>{SiteName} Support Team</p><p>Ref: {email_ref}</p>','en',1], $dataTypes)));
				$query->values(implode(',', $query->bindArray(['OTP Email','otp_email','Your {sitename} OTP','<p>Hello,</p><p>Your {sitename} email verification OTP is {otp}.<br>This OTP is valid for the next 10 minutes.</p><p>If you didn\'t request this verification, please ignore this email.</p><p>Best regards,<br>{sitename} - {siteurl}</p>','en',1], $dataTypes)));

			$db->setQuery($query);
			$db->execute();
		}

		/**
		 * Forms
		 */
		// Check Forms
		$query->clear()
			->select('COUNT(*)')
            ->from($db->quoteName('#__gs_tickets_forms'));
		$db->setQuery($query);
		$results = (int) $db->loadResult();

		if ( $results < 1 )
		{
			// Insert Form
			$dataTypes = [
				ParameterType::STRING,
				ParameterType::NULL,
				ParameterType::STRING,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::STRING,
				ParameterType::INTEGER,
				ParameterType::INTEGER,
				ParameterType::STRING,
				ParameterType::NULL
			];
			$query->clear()
				->insert($db->quoteName('#__gs_tickets_forms'))
				->columns(
					[
						$db->quoteName('form_name'),
						$db->quoteName('form_fields'),
						$db->quoteName('form_type'),
						$db->quoteName('recaptcha'),
						$db->quoteName('input_class'),
						$db->quoteName('submit_text'),
						$db->quoteName('verify_otp_text'),
						$db->quoteName('submit_class'),
						$db->quoteName('fileupload'),
						$db->quoteName('upload_filelimit'),
						$db->quoteName('upload_filesize'),
						$db->quoteName('allowed_filetypes'),
						$db->quoteName('fileupload_label'),
						$db->quoteName('fileupload_help'),
						$db->quoteName('verify_email'),
						$db->quoteName('suggest_docs'),
						$db->quoteName('suggest_docs_cats'),
						$db->quoteName('params')
					]
				);
				$query->values(implode(',', $query->bindArray(['Default Form','a:5:{s:10:"field_name";a:12:{s:4:"name";s:4:"name";s:5:"label";s:9:"Your Name";s:9:"inputtype";s:4:"text";s:9:"fieldtype";s:4:"core";s:7:"colsize";s:2:"50";s:11:"placeholder";s:15:"Write your name";s:7:"options";s:0:"";s:12:"hidden_value";s:0:"";s:10:"customtext";s:0:"";s:8:"required";s:1:"1";s:11:"description";s:0:"";s:13:"error_message";s:23:"Please enter your name.";}s:11:"field_email";a:12:{s:4:"name";s:5:"email";s:5:"label";s:13:"Email Address";s:9:"inputtype";s:5:"email";s:9:"fieldtype";s:4:"core";s:7:"colsize";s:2:"50";s:11:"placeholder";s:16:"user@example.com";s:7:"options";s:0:"";s:12:"hidden_value";s:0:"";s:10:"customtext";s:0:"";s:8:"required";s:1:"1";s:11:"description";s:0:"";s:13:"error_message";s:24:"Email field is required.";}s:13:"field_subject";a:12:{s:4:"name";s:7:"subject";s:5:"label";s:7:"Subject";s:9:"inputtype";s:4:"text";s:9:"fieldtype";s:4:"core";s:7:"colsize";s:3:"100";s:11:"placeholder";s:20:"Enter ticket subject";s:7:"options";s:0:"";s:12:"hidden_value";s:0:"";s:10:"customtext";s:0:"";s:8:"required";s:1:"1";s:11:"description";s:0:"";s:13:"error_message";s:26:"Subject field is required.";}s:16:"field_department";a:8:{s:5:"value";s:1:"1";s:4:"name";s:10:"department";s:5:"label";s:10:"Department";s:9:"inputtype";s:11:"departments";s:9:"fieldtype";s:4:"core";s:7:"colsize";s:3:"100";s:10:"showonform";s:1:"1";s:18:"default_department";s:1:"1";}s:13:"field_message";a:12:{s:4:"name";s:7:"message";s:5:"label";s:7:"Message";s:9:"inputtype";s:8:"textarea";s:9:"fieldtype";s:4:"core";s:7:"colsize";s:3:"100";s:11:"placeholder";s:18:"Enter your message";s:7:"options";s:0:"";s:12:"hidden_value";s:0:"";s:10:"customtext";s:0:"";s:8:"required";s:1:"1";s:11:"description";s:0:"";s:13:"error_message";s:26:"Message field is required.";}}','core',1,'r-gs-input','Create Ticket','Continue','r-gs-button r-gs-submit-button',1,5,1000,'jpg,jpeg,png,gif','Upload Files','Maximum 5 images in jpg, jpeg, png and gif format and total images size not more than 1 MB.',0,0,'a:0:{}',''], $dataTypes)));

			$db->setQuery($query);
			$db->execute();
		}
    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $adapter - Parent object calling this method.
     *
     * @return void
     */
    /* public function uninstall(InstallerAdapter $adapter) 
    {
        echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    } */

    /**
     * This method is called after a component is updated.
     *
     * @param  \stdClass $adapter - Parent object calling object.
     *
     * @return void
     */
    public function update(InstallerAdapter $adapter) 
    {
		/**
		 * Update table columns
		 */
		$this->updateTableColumns();

		$db = $this->getDatabase();

		/**
		 * Add 'ask_for_review' on the Config
		 */
		// Is this 'ask_for_review' already exist
		$query = $db->getQuery(true);
		$query
			->select('COUNT(' . $db->quoteName('name') . ')')
            ->from($db->quoteName('#__gs_tickets_config'))
			->where($db->quoteName('name') . ' = ' . $db->quote('ask_for_review'));
		$db->setQuery($query);
		$results = $db->loadResult();
		if ( (int) $results < 1 )
		{
			// Not found, now insert
			$query = $db->getQuery(true)
				->insert($db->quoteName('#__gs_tickets_config'))
				->set([
					$db->quoteName('name') . ' = ' . $db->quote('ask_for_review'),
					$db->quoteName('value') . ' = ' . $db->quote('yes')
				]);
			$db->setQuery($query);

			try {
				$db->execute();
			} catch (\RuntimeException $e) {
				// Do nothing
			}
		}

		// New settings from version 1.1.0
		$new_settings = [
			'after_new_ticket_created' => 'show_message',
			'new_ticket_confirmation_message' => '<p>Thank you {name}, for submitting your ticket. Your Ticket ID is <strong>{short_ticket_id}</strong>. You can view your ticket and its status by following <a href="http://{ticket_link}">this link</a>.</p>'
		];

		foreach ( $new_settings as $name => $value )
		{
			$query = $db->getQuery(true);
			$query
				->select('COUNT(' . $db->quoteName('name') . ')')
				->from($db->quoteName('#__gs_tickets_config'))
				->where($db->quoteName('name') . ' = :name')
				->bind(':name', $name, ParameterType::STRING);
			$db->setQuery($query);
			$results = $db->loadResult();
			if ( (int) $results < 1 )
			{
				// Not found, new insert
				$query = $db->getQuery(true)
					->insert($db->quoteName('#__gs_tickets_config'))
					->set([
						$db->quoteName('name') . ' = :name',
						$db->quoteName('value') . ' = :value'
					])
					->bind(':name', $name, ParameterType::STRING)
					->bind(':value', $value, ParameterType::NULL);
				$db->setQuery($query);

				try {
					$db->execute();
				} catch (\RuntimeException $e) {
					// Do nothing
				}
			}
		}

		/**
		 * Add email templates subject if not exist
		 */
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName(array('id', 'type', 'subject')))
            ->from($db->quoteName('#__gs_tickets_email_templates'));
		$db->setQuery($query);
		$results = $db->loadObjectList();
		if ( !empty( $results ) )
		{
			foreach ( $results as $template ) {
				if ( !$template->subject )
				{
					$subject = '[{short_ticket_id}] {subject}';
					if ( $template->type === 'otp_email' )
					{
						$subject = 'Your {sitename} OTP';
					}

					$query = $db->getQuery(true)
						->update($db->quoteName('#__gs_tickets_email_templates'))
						->set($db->quoteName('subject') . ' = :subject')
						->where($db->quoteName('id') . ' = :id')
						->bind(':subject', $subject, ParameterType::STRING)
						->bind(':id', $template->id, ParameterType::INTEGER);

					$db->setQuery($query);

					try {
						$db->execute();
					} catch (\RuntimeException $e) {
						// Do nothing
					}
				}
			}
		}
		// END updating email templates subject

		/**
		 * Add 'otp_email' on the Email templates
		 */
		// Is this 'otp_email' already exist
		$query = $db->getQuery(true);
		$query
			->select('COUNT(' . $db->quoteName('id') . ')')
            ->from($db->quoteName('#__gs_tickets_email_templates'))
			->where($db->quoteName('type') . ' = ' . $db->quote('otp_email'));
		$db->setQuery($query);
		$results = $db->loadResult();
		if ( (int) $results < 1 )
		{
			$name = 'OTP Email';
			$type = 'otp_email';
			$subject = 'Your {sitename} OTP';
			$template = '<p>Hello,</p><p>Your {sitename} email verification OTP is {otp}.<br>This OTP is valid for the next 10 minutes.</p><p>If you didn\'t request this verification, please ignore this email.</p><p>Best regards,<br>{sitename} - {siteurl}</p>';
			// Not found, new insert
			$query = $db->getQuery(true)
				->insert($db->quoteName('#__gs_tickets_email_templates'))
				->set([
					$db->quoteName('name') . ' = :name',
					$db->quoteName('type') . ' = :type',
					$db->quoteName('subject') . ' = :subject',
					$db->quoteName('template') . ' = :template',
					$db->quoteName('lang') . ' = ' . $db->quote('en'),
					$db->quoteName('active') . ' = ' . $db->quote(1)
				])
				->bind(':name', $name, ParameterType::STRING)
				->bind(':type', $type, ParameterType::STRING)
				->bind(':subject', $subject, ParameterType::NULL)
				->bind(':template', $template, ParameterType::NULL);
			$db->setQuery($query);

			try {
				$db->execute();
			} catch (\RuntimeException $e) {
				// Do nothing
			}
		}
		// END insert OTP email template

		/**
		 * Add departments to forms
		 */
		$query = $db->getQuery(true);
		$query
			->select('COUNT(' . $db->quoteName('id') . ')')
            ->from($db->quoteName('#__gs_tickets_department_to_forms'));
		$db->setQuery($query);
		$results = $db->loadResult();
		if ( (int) $results < 1 )
		{
			// Not found
			// Get departments
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('id'))
				->from($db->quoteName('#__gs_tickets_departments'));
			$db->setQuery($query);
			$result_departments = $db->loadColumn();

			// Get forms
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName('id'))
				->from($db->quoteName('#__gs_tickets_forms'));
			$db->setQuery($query);
			$result_forms = $db->loadColumn();

			// Assign departments to forms
			if ( !empty( $result_departments ) && !empty( $result_forms ) )
			{
				foreach ( $result_departments as $department_id ) {
					foreach ( $result_forms as $form_id ) {
						$query = $db->getQuery(true)
							->insert($db->quoteName('#__gs_tickets_department_to_forms'))
							->set($db->quoteName('department_id') . ' = :department_id')
							->set($db->quoteName('form_id') . ' = :form_id')
							->bind(':department_id', $department_id, ParameterType::INTEGER)
							->bind(':form_id', $form_id, ParameterType::INTEGER);
						$db->setQuery($query);

						try {
							$db->execute();
						} catch (\RuntimeException $e) {
							// Do nothing
						}
					}
				}
			}
		}
		// ====== END add departments to forms
	}

	/**
	 * Update table columns
	 */
	private function updateTableColumns()
	{
		$db = $this->getDatabase();

		// ***
		// Table `#__gs_tickets_email_templates`
		$columns = $db->getTableColumns('#__gs_tickets_email_templates');

		// If the column is not in the list, add it
		if ( !empty( $columns ) && !array_key_exists( 'subject', $columns ) )
		{
			$db->setQuery("
				ALTER TABLE `#__gs_tickets_email_templates`
				ADD COLUMN `subject` TEXT DEFAULT NULL AFTER `type`
			");
			$db->execute();
		}

		// ***
		// Table `#__gs_tickets_forms`
		$columns = $db->getTableColumns('#__gs_tickets_forms');

		// If the column is not in the list, add it
		if ( !empty( $columns ) && !array_key_exists( 'params', $columns ) )
		{
			$db->setQuery("
				ALTER TABLE `#__gs_tickets_forms`
				ADD COLUMN `params` TEXT DEFAULT NULL AFTER `suggest_docs_cats`
			");
			$db->execute();
		}

		// ***
		// Table `#__gs_tickets`
		$columns = $db->getTableColumns('#__gs_tickets');

		// If the column is not in the list, add it
		if ( !empty( $columns ) && !array_key_exists( 'short_ticket_id', $columns ) )
		{
			$db->setQuery("
				ALTER TABLE `#__gs_tickets`
				ADD COLUMN `short_ticket_id` VARCHAR(100) DEFAULT NULL AFTER `ticket_id`
			");
			$db->execute();
		}
	}
}