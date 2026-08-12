<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Site\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\IpHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Event;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class CreateticketController extends BaseController
{
    /**
     * Create new ticket
     */
    public function submit()
    {
		$this->checkToken();

		$app = $this->app;
		$config = GuestsupportHelper::config();
		$jconfig = Factory::getApplication()->getConfig();
		$user = $app->getIdentity();
		$guest = $user->get('guest');
		$filter = InputFilter::getInstance();
		$ip = IpHelper::getIp();
		$session = Factory::getApplication()->getSession();
		$input = GuestsupportHelper::getInput();

		$form_id = $input->post->getInt('form_id', 0);
		$Itemid = $input->getInt('Itemid');
		$menu = $Itemid ? '&Itemid=' . $Itemid : '';
		$redirectUrl = Route::_('index.php?option=com_guestsupport&view=createticket&id=' . (int) $form_id . $menu, false);

		// Import Guestsupport plugins
        PluginHelper::importPlugin('guestsupport');

		// Get all post data, will sanitize later
		$data = filter_var_array( $_POST );

		// Get the form
		$form = $this->getModel()->getForm( (int) $form_id );
		$formFields = array();

		if( !empty( $form ) )
		{
			$formFields = unserialize( $form->form_fields );
		}
		else
		{
			// Incase the form id was modified
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_INVALID_FORM_ID'), 'error');
			$this->setRedirect($redirectUrl);
			return false;
		}

		// Check email
		$data['email'] = isset( $data['email'] ) ? GuestsupportHelper::sanitizeEmail( $data['email'] ) : '';

		if ( !$data['email'] )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_INVALID_EMAIL'), 'error');
			$this->setRedirect($redirectUrl);
			return false;
		}

		// Verify reCAPTCHA
		if ( $form->recaptcha == 1 && $config->recaptcha_secret_key && $config->recaptcha_site_key )
		{
			$recaptcha_action = $input->post->getString('_recaptcha_action', '');
			$recaptcha_token = $input->post->getString('_recaptcha_token', '');

			if ( !$recaptcha_action || !$recaptcha_token )
			{
				$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_RECAPTCHA_VERIFICATION_FAILED'), 'error');
				$this->setRedirect($redirectUrl);
				return false;
			}
			$verify_recaptcha = $this->verifyReCaptcha( $recaptcha_action, $recaptcha_token );

			if ( $verify_recaptcha === false )
			{
				$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_RECAPTCHA_VERIFICATION_FAILED'), 'error');
				$this->setRedirect($redirectUrl);
				return false;
			}
		}

		// Get attachments
		$attachments = $this->getAttachments();

		// Unset unnecessary input
		unset(
			$data['form_id'],
			$data['attachments'],
			$data['_recaptcha_action'],
			$data['_recaptcha_token'],
			$data[Session::getFormToken()]
		);

		if ( empty( $data ) )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_CREATE_TICKET_NO_DATA'), 'error');
			$this->setRedirect($redirectUrl);
			return false;
		}

		// Trigger an event, in case a plugin wishes to apply additional verifications
		$event = new Event(
            'onGuestsupportBeforeCreatingTicket',
            [
				'context' => 'guestsupport.createticket',
				'form_id' => (int) $form_id,
            	'data'  => &$data
            ]
        );

		$response = $app->getDispatcher()->dispatch( 'onGuestsupportBeforeCreatingTicket', $event );

		if ( $response->getArgument('cancelTicket', '') === true )
		{
			// There should be a message on the plugin, to describe why it was cancelled.
			$this->setRedirect($redirectUrl);
			return false;
		}

		// Get data again from the plugin event,
		// so we can use the updated data if any.
		$data = $response->getArgument('data', []);

		// Sanitize data
		$data = $this->sanitizeAndVerify( $formFields, $data, $form_id, $redirectUrl );

		if ( empty( $data ) || $data === false )
		{
			$this->setRedirect($redirectUrl);
			return false;
		}

		// Save data to session, so we can retrieve later in case there was a redirect with errors
		$app->setUserState( 'com_guestsupport.createticket.data', $data );

		// Get user id by email
		$userby_email = false;
		if ( isset( $data['email'] ) && $data['email'] )
		{
			$userby_email = $this->getModel()->getUserByEmail( $data['email'] );
		}

		// Get inputs
		if ( !$guest )
		{
			$data['name'] = $user->name;
			$data['email'] = $user->email;
			$user_id = $user->id;
			$update_user_id = 0;
		}
		elseif ( $userby_email !== false )
		{
			$data['name'] = $userby_email->name;
			$data['email'] = $userby_email->email;
			$user_id = $userby_email->id;
			$update_user_id = 0;
		}
		else
		{
			$user_id = 0;
			$update_user_id = 1;
		}

		// Ticket info
		$ticket_id = $this->ticketId();
		$short_ticket_id = GuestsupportHelper::shortTicketId();
		$ticket_token = $this->randomString(19);
		$ticket_email_hash = md5( $data['email'] );
		$message_type = 'ticket';
		$status = 'open';
		$autoreply = 1;
		$published = 1;
		$date = Factory::getDate()->toSql();

		if ( GuestsupportHelper::isAgent() )
		{
			$user_type = 'agent';
		}
		else
		{
			$user_type = 'user';
		}

		// Get department id
		$department_id = $this->getModel()->getDefaultDepartment();

		if ( !$department_id )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_CREATE_TICKET_NO_DEPARTMENT'), 'error');
			$this->setRedirect($redirectUrl);
			return false;
		}

		// Get Agent Id
		$agent_id = $this->getModel()->getAgentIdByDepartmentId( $department_id );

		if ( $agent_id === false )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_CREATE_TICKET_NO_AGENT'), 'error');
			$this->setRedirect($redirectUrl);
			return false;
		}

		// Prepare inputs
		$tc_name = $data['name'];
		$tc_email = $data['email'];
		$tc_subject = $data['subject'];
		$tc_message = $data['message'];

		// Unset used inputs
		unset(
			$data['name'],
			$data['email'],
			$data['department'],
			$data['subject'],
			$data['message']
		);

		// Prepare ticket data
		$newTicketdata = [
			'ticket_id' => $ticket_id,
			'short_ticket_id' => $short_ticket_id,
			'ticket_token' => $ticket_token,
			'form_id' => $form_id,
			'ticket_email_hash' => $ticket_email_hash,
			'ip' => $ip,
			'user_id' => $user_id,
			'agent_id' => $agent_id,
			'name' => $tc_name,
			'email' => $tc_email,
			'message_type' => $message_type,
			'department_id' => $department_id,
			'subject' => $tc_subject,
			'message' => $tc_message,
			'custom_fields' => '',
			'encrypted_message' => '',
			'status' => $status,
			'autoreply' => $autoreply,
			'published' => $published,
			'update_user_id' => $update_user_id,
			'user_type' => $user_type,
			'created_date' => $date,
			'last_update_date' => $date,
			'created_by' => 'user'
		];

		// Check and upload attachments and early return if any errors before creating ticket
		$filedata = '';
		if ( $form->fileupload == 1 && !empty( $attachments ) )
		{
			// Trigger an event, in case a plugin wishes to modify uploaded files.
			$event = new Event(
				'onGuestsupportBeforeFilesUpload',
				[
					'context' => 'guestsupport.createticket',
					'form_id' => (int) $form_id,
					'attachments'  => $attachments
				]
			);

			$response = $app->getDispatcher()->dispatch( 'onGuestsupportBeforeFilesUpload', $event );
			$attachments = $response->getArgument('attachments', []);

			if ( !empty( $attachments ) )
			{
				$allowed_extensions_raw = preg_replace('/\s+/', '', $form->allowed_filetypes);
				$allowed_extensions = explode( ',', $allowed_extensions_raw );
				$filedata = GuestsupportHelper::uploadFiles( $attachments, $allowed_extensions, $form->upload_filesize, $form->upload_filelimit, $ticket_id );
				if ( $filedata === false )
				{
					$this->setRedirect($redirectUrl);
					return false;
				}
			}
		}

		// Insert ticket to Database and get new ticket id
		$insertId = $this->getModel()->createTicket( $newTicketdata );

		if ( $insertId !== false )
		{
			// Clear form data from the session
			$app->setUserState( 'com_guestsupport.createticket.data', null );

			// Add attachments to database
			$filestodb = array();
			if ( !empty( $filedata ) && is_array( $filedata ) )
			{
				$filestodb = GuestsupportHelper::filesToDb( $filedata, $ticket_id, $insertId );
				if ( $filestodb === false )
				{
					$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_UPLOAD_ADD_TO_DB_FAILED'), 'error');
				}
			}

			/**
			 * Prepare email data
			 * Send email
			 */

			// Add Attachments links
			$email_body_attachments = '';
			if ( $filestodb !== false && !empty( $filestodb ) )
			{
				$file_url = 'index.php?option=com_guestsupport&task=ticket.downloadfile&id=' . $ticket_id . '&auth=' . $ticket_token . '&fileid=';

				$email_body_attachments = '<h3>' . Text::_('COM_GUESTSUPPORT_ATTACHMENTS') . '</h3>';
				$email_body_attachments .= '<ul>';
				foreach ($filestodb as $file) {
					$email_body_attachments .= '<li><a href="' . Route::_( $file_url . GuestsupportHelper::encrypt( $file['id'] ), false, 0, true ) . '" target="_blank">' . $file['name'] . ' (' . GuestsupportHelper::bytesToReadable( $file['size'] ) . ')</a></li>';
				}
				$email_body_attachments .= '</ul>';
			}

			// Agent info
			$agent_id_enc = '';
			$agent_name = '';
			$agent_email = '';
			$agentinfo = new User( $agent_id );
			if ( !empty($agentinfo) )
			{
				$agent_name = $agentinfo->name;
				$agent_email = $agentinfo->email;
				$agent_id_enc = GuestsupportHelper::encrypt( $agent_id );
			}

			$ticket_base_url = 'index.php?option=com_guestsupport&view=ticket&id=' . $ticket_id . '&auth=' . $ticket_token;

			$ticket_url_user = Route::_($ticket_base_url, false, 0, true);
			$ticket_url_agent = Route::_($ticket_base_url . '&agent=' . $agent_id_enc, false, 0, true);

			$departmentData = $this->getModel()->getDepartment( $department_id );

			// Send user email
			$sitename = $jconfig->get('sitename');
			$email_body_placeholder = [
				'{name}'				=> $tc_name,
				'{email}'				=> $tc_email,
				'{is_registered_user}'	=> $userby_email !== false ? Text::_('COM_GUESTSUPPORT_YES') : Text::_('COM_GUESTSUPPORT_NO'),
				'{user_id}'				=> $user->id,
				'{ip_address}'			=> $ip,
				'http://{ticket_link}'	=> $ticket_url_user,
				'https://{ticket_link}'	=> $ticket_url_user,
				'{ticket_link}'			=> $ticket_url_user,
				'{ticket_id}'			=> $ticket_id,
				'{short_ticket_id}'		=> $short_ticket_id,
				'{form}'				=> $form->form_name,
				'{subject}'				=> $tc_subject,
				'{message}'				=> $tc_message,
				'{ticket_message}'		=> $tc_message,
				'{reply_message}'		=> $tc_message,
				'{custom_fields}'		=> '',
				'{attachments}'			=> $email_body_attachments,
				'{agent_name}'			=> $agent_name,
				'{agent_email}'			=> $agent_email,
				'{department}'			=> !empty( $departmentData ) ? $departmentData->name : '',
				'{SiteName}'			=> $sitename,
				'{sitename}'			=> $sitename,
				'{siteurl}'				=> Uri::root(),
				'{email_ref}'			=> $short_ticket_id . '-' . time(),
			];

			// Get email templates
			$emailTemplatesList = GuestsupportHelper::emailTemplates();

			// Send email to user
			$emailTemplate = $emailTemplatesList['ticket_email_user_message'] ?? [];
			if ( !empty( $emailTemplate ) )
			{
				$user_email_subject = strtr( $emailTemplate->subject, $email_body_placeholder );
				$user_email_subject = $filter->clean($user_email_subject, 'STRING');
				$user_email_body = strtr( $emailTemplate->template, $email_body_placeholder );
				$mailtouser = GuestsupportHelper::sendMail( $tc_email, $user_email_subject, $user_email_body );
			}

			// Send email to agent
			$emailTemplate = $emailTemplatesList['ticket_email_agent_message'] ?? [];
			if ( !empty( $emailTemplate ) && $agent_email )
			{
				// Change ticket url for agent
				$email_body_placeholder['http://{ticket_link}'] = $ticket_url_agent;
				$email_body_placeholder['https://{ticket_link}'] = $ticket_url_agent;
				$email_body_placeholder['{ticket_link}'] = $ticket_url_agent;

				$agent_email_subject = strtr( $emailTemplate->subject, $email_body_placeholder );
				$agent_email_subject = $filter->clean($agent_email_subject, 'STRING');
				$agent_email_body = strtr( $emailTemplate->template, $email_body_placeholder );
				$mailtoagent = GuestsupportHelper::sendMail( $agent_email, $agent_email_subject, $agent_email_body );
			}

			// Send email to additional email addresses
			if ( $departmentData !== false && !empty( $departmentData ) )
			{
				if ( $departmentData->additional_emails && ( $departmentData->additional_emails_type == 'tickets' || $departmentData->additional_emails_type == 'both' ) )
				{
					$emailTemplate = $emailTemplatesList['ticket_email_additional_message'] ?? [];
					if ( !empty( $emailTemplate ) )
					{
						$additional_email_addresses = preg_split( "/\r\n|\n|\r/", $departmentData->additional_emails );

						$additional_email_subject = strtr( $emailTemplate->subject, $email_body_placeholder );
						$additional_email_subject = $filter->clean($additional_email_subject, 'STRING');
						$additional_email_body = strtr( $emailTemplate->template, $email_body_placeholder );
						$mailtoadditional = GuestsupportHelper::sendMail( $additional_email_addresses, $additional_email_subject, $additional_email_body );
					}
				}
			}

			/**
			 * END Send emails
			 */

			/**
			 * New ticket has been created successfully
			 */

			// Trigger an event, in case a plugin wishes to do something with the submitted ticket data
			$event = new Event(
				'onGuestsupportAfterCreatingTicket',
				[
					'context' => 'guestsupport.createticket',
					'form_id' => (int) $form_id,
					'data'  => $newTicketdata
				]
			);

			$app->getDispatcher()->dispatch( 'onGuestsupportAfterCreatingTicket', $event );

			/**
			 * What to do now? - Either redirect or show message based on settings
			 */

			$after_new_ticket_created = $config->after_new_ticket_created;
			$new_ticket_confirmation_message = $config->new_ticket_confirmation_message;
			if ( $after_new_ticket_created === 'show_message' && $new_ticket_confirmation_message )
			{
				// Change ticket url for user
				$email_body_placeholder['http://{ticket_link}'] = $ticket_url_user;
				$email_body_placeholder['https://{ticket_link}'] = $ticket_url_user;
				$email_body_placeholder['{ticket_link}'] = $ticket_url_user;

				// Show message
				// Set ticket created status on session
				$session->set('com_guestsupport.ticketcreated', true);
				$new_ticket_confirmation_message = strtr( $new_ticket_confirmation_message, $email_body_placeholder );
				$session->set('com_guestsupport.new_ticket_confirmation_message', $new_ticket_confirmation_message);

				// Redirect to show message
				$this->setRedirect($redirectUrl);
				return true;
			}
			else
			{
				$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_NEW_TICKET_CONFIRMATION_ON_TICKET_VIEW'), 'success');
				// Redirect to ticket view page
				$this->setRedirect($ticket_url_user);
				return true;
			}
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_CREATE_TICKET_FAILED'), 'error');
		}

		// Redirect to the ticket submission page if there is any error or if redirection has not already occurred.
		$this->setRedirect($redirectUrl);
		return true;
    }

	/**
     * Create new reply
     */
    public function reply()
    {
		$this->checkToken();

		$app = $this->app;
		$config = GuestsupportHelper::config();
		$jconfig = Factory::getApplication()->getConfig();
		$user = $app->getIdentity();
		$guest = $user->get('guest');
		$filter = InputFilter::getInstance();
		$ip = IpHelper::getIp();
		$input = GuestsupportHelper::getInput();

		// Import Guestsupport plugins
        PluginHelper::importPlugin('guestsupport');

		// Get necessary ids
		$ticket_id = $input->post->getString('ticket_id', '');
		$ticket_token = $input->post->getString('ticket_auth', '');
		$agent_enc = $input->getString('agent', '');

		// Check we have necessary ids
		if ( !$ticket_id || !$ticket_token )
		{
			die( Text::_('COM_GUESTSUPPORT_INVALID_REQUEST') );
		}

		// Ticket url
		$ticket_url = Route::_('index.php?option=com_guestsupport&view=ticket&id=' . $ticket_id . '&auth=' . $ticket_token . ($agent_enc ? '&agent=' . $agent_enc : ''), false);

		// Get the ticket
		$thisticket = $this->getModel()->getTicket( $ticket_id, $ticket_token );

		// Check if this ticket exist
		if ( empty( $thisticket ) )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_TICKET_NOT_FOUND_TO_REPLY'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		// Get and verify Agent
		$isagent = GuestsupportHelper::isAgent();
		$agent_id = $this->getModel()->getAgentIdByDepartmentId( $thisticket->department_id );
		$agent = new User( $agent_id );
		$administrator = $user->authorise('core.admin');

		if ( !$administrator )
		{
			if ( ( $isagent && $isagent != $agent_id ) || $agent === false || empty( $agent ) )
			{
				$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_INVALID_SUPPORT_AGENT'), 'error');
				$this->setRedirect($ticket_url);
				return false;
			}
		}

		// Get the form
		$form = $this->getModel()->getForm( (int) $thisticket->form_id );
		$formFields = array();

		if( !empty( $form ) )
		{
			$formFields = unserialize( $form->form_fields );
		}
		else
		{
			// Incase the form id was modified
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_INVALID_FORM_ID'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		/**
		 * Get reply inputs
		 */
		$input_message = $input->post->getRaw('message', '');
		$message = GuestsupportHelper::sanitizeHtmlEditor( $input_message );
		$closeticket = $input->post->getInt('closeticket', 0);

		if ( !$message )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_REPLY_ENTER_MESSAGE'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		// Get all post data, will sanitize later
		$data = filter_var_array( $_POST );

		// Trigger an event, in case a plugin wishes to apply additional verifications
		$event = new Event(
            'onGuestsupportBeforeCreatingReply',
            [
				'context' => 'guestsupport.createticket',
				'form_id' => (int) $thisticket->form_id,
            	'data'  => &$data,
				'is_agent'  => $isagent
            ]
        );

		$response = $app->getDispatcher()->dispatch( 'onGuestsupportBeforeCreatingReply', $event );

		if ( $response->getArgument('cancelReply', '') === true )
		{
			// There should be a message on the plugin, to describe why it was cancelled.
			$this->setRedirect($ticket_url);
			return false;
		}

		// Get data again from the plugin event,
		// so we can use the updated data if any.
		$data = $response->getArgument('data', []);

		// Unset used inputs
		unset(
			$data['ticket_id'],
			$data['ticket_auth'],
			$data['message'],
			$data['closeticket'],
			$data[Session::getFormToken()]
		);

		// Sanitize data
		if ( !empty( $data ) )
		{
			$data = $this->sanitizeAndVerify( $formFields, $data, $thisticket->form_id, $ticket_url );
		}

		// Get attachments
		$attachments = $this->getAttachments();

		// Set user id, name, email and update_user_id
		$name_todb = $thisticket->name;
		$email_todb = $thisticket->email;

		$user_name_toemail = $thisticket->name;
		$user_email_toemail = $thisticket->email;

		$agent_name_toemail = $agent->name;
		$agent_email_toemail = $agent->email;
		$created_by = 'user';

		if ( $isagent )
		{
			$name_todb = $agent->name;
			$email_todb = $agent->email;
			$user_id = $agent->id;
			$update_user_id = 0;
			$status = 'pending';
			$user_type = 'agent';
			$created_by = 'agent';
			$agent_id_todb = $isagent;
		}
		else
		{
			$agent_id_todb = 0;
			if ( $thisticket->created_by == 'agent' )
			{
				$userdata = new User( $thisticket->user_id );
				if ( $userdata !== false && !empty( $userdata ) )
				{
					$name_todb = $userdata->name;
					$email_todb = $userdata->email;
					$user_name_toemail = $userdata->name;
					$user_email_toemail = $userdata->email;
				}
			}
			$user_id = $thisticket->user_id;
			$update_user_id = 0;
			$status = 'open';
			$user_type = 'user';
		}

		// Change ticket status to close if Ticket Close checked
		if ( $closeticket )
		{
			$status = 'closed';
		}

		// Ticket info
		$ticket_email_hash = md5( $email_todb );
		$message_type = 'reply';
		$department_id = $thisticket->department_id;
		$autoreply = 0;
		$published = 1;
		$date = Factory::getDate()->toSql();

		// Prepare Ticket Reply data
		$newReplydata = array(
			'ticket_id' => $ticket_id,
			'short_ticket_id' => $thisticket->short_ticket_id,
			'ticket_token' => $ticket_token,
			'form_id' => $thisticket->form_id,
			'ticket_email_hash' => $ticket_email_hash,
			'ip' => $ip,
			'user_id' => $user_id,
			'agent_id' => $agent_id_todb,
			'name' => $name_todb,
			'email' => $email_todb,
			'message_type' => $message_type,
			'department_id' => $department_id,
			'subject' => '',
			'message' => $message,
			'custom_fields' => '',
			'encrypted_message' => '',
			'status' => $status,
			'autoreply' => $autoreply,
			'published' => $published,
			'update_user_id' => $update_user_id,
			'user_type' => $user_type,
			'created_date' => $date,
			'last_update_date' => $date,
			'created_by' => $created_by
		);

		// Check and upload attachments and early return if any errors before creating ticket
		$filedata = '';
		if ( $form->fileupload == 1 && !empty( $attachments ) )
		{
			// Trigger an event, in case a plugin wishes to modify uploaded files.
			$event = new Event(
				'onGuestsupportBeforeFilesUpload',
				[
					'context' => 'guestsupport.replyticket',
					'form_id' => (int) $thisticket->form_id,
					'attachments'  => $attachments
				]
			);

			$response = $app->getDispatcher()->dispatch( 'onGuestsupportBeforeFilesUpload', $event );
			$attachments = $response->getArgument('attachments', []);

			if ( !empty( $attachments ) )
			{
				$allowed_extensions_raw = preg_replace('/\s+/', '', $form->allowed_filetypes);
				$allowed_extensions = explode( ',', $allowed_extensions_raw );
				$filedata = GuestsupportHelper::uploadFiles( $attachments, $allowed_extensions, $form->upload_filesize, $form->upload_filelimit, $ticket_id );
				if ( $filedata === false )
				{
					$this->setRedirect($ticket_url);
					return false;
				}
			}
		}

		// Insert reply to Database and get new reply id
		$insertId = $this->getModel()->createTicket( $newReplydata );

		if ( $insertId === false || !$insertId )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_CREATE_REPLY_FAILED'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		// Update ticket status
		$updateData = array(
			'status'	=> $status,
			'last_update_date'	=> $date
		);
		$updateCondition = array(
			'ticket_id'	=> $ticket_id,
			'ticket_token'	=> $ticket_token
		);
		$this->getModel()->updateTicket( $updateData, $updateCondition );

		// Add attachments to database
		$filestodb = array();
		if ( !empty( $filedata ) && is_array( $filedata ) )
		{
			$filestodb = GuestsupportHelper::filesToDb( $filedata, $ticket_id, $insertId );
			if ( $filestodb === false )
			{
				$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_UPLOAD_ADD_TO_DB_FAILED'), 'error');
			}
		}

		/**
		 * Prepare email data
		 * Send email
		 */

		// Add Attachments links
		$email_body_attachments = '';
		if ( $filestodb !== false && !empty( $filestodb ) )
		{
			$file_url = 'index.php?option=com_guestsupport&task=ticket.downloadfile&id=' . $ticket_id . '&auth=' . $ticket_token . '&fileid=';

			$email_body_attachments = '<h3>' . Text::_('COM_GUESTSUPPORT_ATTACHMENTS') . '</h3>';
			$email_body_attachments .= '<ul>';
			foreach ($filestodb as $file) {
				$email_body_attachments .= '<li><a href="' . Route::_( $file_url . GuestsupportHelper::encrypt( $file['id'] ), false, 0, true ) . '" target="_blank">' . $file['name'] . ' (' . GuestsupportHelper::bytesToReadable( $file['size'] ) . ')</a></li>';
			}
			$email_body_attachments .= '</ul>';
		}

		// Ticket urls for user and agent
		$agent_id_enc = GuestsupportHelper::encrypt( $agent_id );

		$email_ticket_url = 'index.php?option=com_guestsupport&view=ticket&id=' . $ticket_id . '&auth=' . $ticket_token;
		$ticket_url_user = Route::_($email_ticket_url, false, 0, true);
		$ticket_url_agent = Route::_($email_ticket_url . '&agent=' . $agent_id_enc, false, 0, true);

		// Set necessary info
		$sitename = $jconfig->get('sitename');
		$departmentData = $this->getModel()->getDepartment( $department_id );
		$userby_email = $this->getModel()->getUserByEmail( $user_email_toemail );

		$email_body_placeholder = [
			'{name}'				=> $user_name_toemail,
			'{email}'				=> $user_email_toemail,
			'{is_registered_user}'	=> $userby_email !== false ? Text::_('COM_GUESTSUPPORT_YES') : Text::_('COM_GUESTSUPPORT_NO'),
			'{user_id}'				=> $user->id,
			'{ip_address}'			=> $ip,
			'http://{ticket_link}'	=> $ticket_url_user,
			'https://{ticket_link}'	=> $ticket_url_user,
			'{ticket_link}'			=> $ticket_url_user,
			'{ticket_id}'			=> $ticket_id,
			'{short_ticket_id}'		=> $thisticket->short_ticket_id,
			'{form}'				=> $form->form_name,
			'{subject}'				=> $thisticket->subject,
			'{message}'				=> $message,
			'{ticket_message}'		=> $message,
			'{reply_message}'		=> $message,
			'{custom_fields}'		=> '',
			'{attachments}'			=> $email_body_attachments,
			'{agent_name}'			=> $agent_name_toemail,
			'{agent_email}'			=> $agent_email_toemail,
			'{department}'			=> !empty( $departmentData ) ? $departmentData->name : '',
			'{SiteName}'			=> $sitename,
			'{sitename}'			=> $sitename,
			'{siteurl}'				=> Uri::root(),
			'{email_ref}'			=> $thisticket->short_ticket_id . '-' . time(),
		];

		// Get email templates
		$emailTemplatesList = GuestsupportHelper::emailTemplates();

		// Send emails
		if ( $user_type == 'agent' )
		{
			// Send email to user if replied by Agent
			$emailTemplate = $emailTemplatesList['reply_email_user_message'] ?? [];
			if ( !empty( $emailTemplate ) )
			{
				$user_email_subject = strtr( $emailTemplate->subject, $email_body_placeholder );
				$user_email_subject = $filter->clean($user_email_subject, 'STRING');
				$user_email_body = strtr( $emailTemplate->template, $email_body_placeholder );
				$mailtouser = GuestsupportHelper::sendMail( $user_email_toemail, 'RE: ' . $user_email_subject, $user_email_body );
			}
		}
		elseif ( $user_type == 'user' )
		{
			// Change ticket url for agent
			$email_body_placeholder['http://{ticket_link}'] = $ticket_url_agent;
			$email_body_placeholder['https://{ticket_link}'] = $ticket_url_agent;
			$email_body_placeholder['{ticket_link}'] = $ticket_url_agent;

			// Send email to agent if replied by User
			$emailTemplate = $emailTemplatesList['reply_email_agent_message'] ?? [];
			if ( !empty( $emailTemplate ) )
			{
				$agent_email_subject = strtr( $emailTemplate->subject, $email_body_placeholder );
				$agent_email_subject = $filter->clean($agent_email_subject, 'STRING');
				$agent_email_body = strtr( $emailTemplate->template, $email_body_placeholder );
				$mailtouser = GuestsupportHelper::sendMail( $agent_email_toemail, 'RE: ' . $agent_email_subject, $agent_email_body );
			}
		}

		// Send emails to additional email addresses
		if ( $departmentData !== false && !empty( $departmentData ) )
		{
			if ( $departmentData->additional_emails && ( $departmentData->additional_emails_type == 'replies' || $departmentData->additional_emails_type == 'both' ) )
			{
				$emailTemplate = $emailTemplatesList['reply_email_additional_message'] ?? [];
				if ( !empty( $emailTemplate ) )
				{
					$additional_email_addresses = preg_split( "/\r\n|\n|\r/", $departmentData->additional_emails, -1, PREG_SPLIT_NO_EMPTY);
					$additional_email_subject = strtr( $emailTemplate->subject, $email_body_placeholder );
					$additional_email_subject = $filter->clean($additional_email_subject, 'STRING');
					$additional_email_body = strtr( $emailTemplate->template, $email_body_placeholder );
					$mailtoadditional = GuestsupportHelper::sendMail( $additional_email_addresses, 'RE: ' . $additional_email_subject, $additional_email_body );
				}
			}
		}

		/**
		 * END Send emails
		 */

		// Trigger an event, in case a plugin wishes to do something with the submitted reply data
		$event = new Event(
			'onGuestsupportAfterCreatingReply',
			[
				'context' => 'guestsupport.createticket',
				'form_id' => (int) $thisticket->form_id,
				'data'  => $newReplydata,
				'is_agent'  => $isagent
			]
		);

		$app->getDispatcher()->dispatch( 'onGuestsupportAfterCreatingReply', $event );

		// Redirect back to the ticket with success message.
		$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_REPLY_ADDED_SUCCESSFULLY'), 'success');
		$this->setRedirect($ticket_url);
		return true;
	}

	/**
	 * Verify reCAPTCHA
	 */
	private function verifyReCaptcha( $action, $token )
	{
		$config = GuestsupportHelper::config();
		$minscore = (float) $config->recaptcha_score;
		if ( $minscore < 0.1 || $minscore > 1 )
		{
			$minscore = 0.5;
		}

		// Call curl to POST request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $config->recaptcha_secret_key, 'response' => $token)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$arrResponse = json_decode($response, true);

		// Verify the response
		if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= $minscore) {
			return true; // Valid
		} else {
			return false; // Not valid
		}
	}

	/**
	 * Sanitize data
	 */
	public function sanitizeAndVerify( $formFields, $data, $form_id, $redirectUrl )
	{
		$filter = InputFilter::getInstance();
		$error = 0;
		$required_empty = 0;

		if ( empty( $formFields ) || empty( $data ) )
		{
			return false;
		}

		// Sanitize inputs
		foreach ( $data as $name => $value ) {

			if ( !in_array( $name, ['name', 'email', 'subject', 'department', 'message'] ) )
			{
				return false;
			}

			$inputname = 'field_' . $name;
			// Sanitize
			if ( is_array( $value ) )
			{
				$data[$name] = array();
				foreach ($value as $valueitem) {
					if ( !empty( $valueitem ) )
					{
						$data[$name][] = trim( $filter->clean($valueitem, 'STRING') );
					}
				}
			}
			else
			{
				if ( $formFields[$inputname]['inputtype'] == 'email' )
				{
					$data[$name] = $value ? GuestsupportHelper::sanitizeEmail( $value ) : '';
				}
				elseif ( $formFields[$inputname]['inputtype'] == 'textarea' && $name == 'message' )
				{
					// Check if a message remains after removing empty tags.
					if ( $value )
					{
						// Remove all HTML tags
						$sanitized = strip_tags($value);

						// Remove new lines and carriage returns
						$sanitized = str_replace(["\n", "\r"], '', $sanitized);

						// Trim leading and trailing whitespace
						$sanitized = $sanitized ? trim($sanitized) : '';

						$data[$name] = $sanitized ? GuestsupportHelper::sanitizeHtmlEditor( $value ) : '';
					}
				}
				elseif ( $formFields[$inputname]['inputtype'] == 'textarea' )
				{
					$data[$name] = $value ? trim( $filter->clean($value, 'STRING') ) : '';
				}
				else
				{
					$data[$name] = $value ? trim( $filter->clean($value, 'STRING') ) : '';
				}
			}

			if ( isset( $formFields[$inputname]['required'] ) && $formFields[$inputname]['required'] == 1 )
			{
				if ( is_array( $data[$name] ) && empty( $data[$name] ) )
				{
					$required_empty = 1;
				}
				elseif ( !$data[$name] && $data[$name] !== '0' )
				{
					$required_empty = 1;
				}
			}

			// Check if any required field was empty
			if ( $required_empty )
			{
				if ( !empty( $formFields[$inputname]['error_message'] ) )
				{
					$noticeMsg = $formFields[$inputname]['error_message'];
				}
				else
				{
					$noticeMsg = "'" . $formFields[$inputname]['label'] . "' field is required.";
				}

				$this->app->enqueueMessage( $filter->clean($noticeMsg, 'STRING'), 'error');
				$this->setRedirect($redirectUrl);
				return false;
			}
		}
		if ( $error || $required_empty )
		{
			return false;
		}
		return $data;
	}

	/**
	 * Random string
	 */
	public function randomString( $length, $random_lenght = true )
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
	 * Generate Ticket Id
	 */
	public function ticketId()
	{
		$id = $this->randomString(17);

		// Check if the id already exist
		$checkId = $this->getModel()->getTicketById( $id );
		if ( $checkId !== false )
		{
			$id = $this->ticketId();
		}
		return $id;
	}

	/**
	 * Get attachments
	 */
	public function getAttachments()
	{
		$attachments = [];
		if (isset($_FILES['attachments']) && $_FILES['attachments']['error'][0] != UPLOAD_ERR_NO_FILE) {
			// Restructure the $_FILES array into a more usable format
			$files = $_FILES['attachments'];
			
			if (is_array($files['name'])) {
				// Multiple files uploaded
				$fileCount = count($files['name']);
				
				for ($i = 0; $i < $fileCount; $i++) {
					if ($files['error'][$i] != UPLOAD_ERR_NO_FILE) {
						$attachments[] = [
							'name' => $files['name'][$i],
							'type' => $files['type'][$i],
							'tmp_name' => $files['tmp_name'][$i],
							'error' => $files['error'][$i],
							'size' => $files['size'][$i]
						];
					}
				}
			} else {
				// Single file uploaded
				if ($files['error'] != UPLOAD_ERR_NO_FILE) {
					$attachments[] = $files;
				}
			}
		}

		return $attachments;
	}
}
