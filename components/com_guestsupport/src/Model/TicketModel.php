<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class TicketModel extends ListModel
{
	protected $ticket_subject = null;
	protected $ticket_form = null;

	/**
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @note Calling getState in this method will result in recursion.
     *
     * @since   3.1
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();

        $params = $app->getParams();
        $this->setState('params', $params);
		$input = GuestsupportHelper::getInput();

		$ticket_id = $input->getString('id');
		$ticket_token = $input->getString('auth');
		$this->setState('ticket_id', $ticket_id);
		$this->setState('ticket_token', $ticket_token);
    }

	/**
	 * Get Ticket
	 */
	public function getTicket()
	{
		$ticket_id = $this->getState('ticket_id');
		$ticket_token = $this->getState('ticket_token');
		$config = GuestsupportHelper::config();

		if ( !$ticket_id || !$ticket_token )
		{
			return false;
		}

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
			->select($db->quoteName([
				'id', 'ticket_id', 'short_ticket_id', 'ticket_token', 'form_id', 'user_id', 'agent_id', 'name', 'email', 'message_type', 'department_id', 'subject', 'message', 'custom_fields', 'encrypted_message', 'status', 'user_type', 'created_date', 'last_update_date', 'created_by'
			]))
            ->from($db->quoteName('#__gs_tickets'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('ticket_id') . ' = :ticket_id')
			->where($db->quoteName('ticket_token') . ' = :ticket_token')
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':ticket_token', $ticket_token, ParameterType::STRING);

		if ( $config->message_direction == 'bottom' )
		{
			$query->order($db->quoteName('id') . 'DESC');
		}
		else
		{
			$query->order($db->quoteName('id') . 'ASC');
		}

		$db->setQuery($query);
		$messages = $db->loadObjectList('id');

		if ( empty($messages) )
		{
			return false;
		}

		$query->clear();
		$query->select($db->quoteName([
			'file_id', 'file_ticket_id', 'file_ticket_message_id', 'file_name_raw', 'file_name_enc', 'file_size', 'file_created'
		]))
			->from($db->quoteName('#__gs_tickets_attachments'))
			->where($db->quoteName('file_ticket_id') . '=' . $db->quote($ticket_id));
		$db->setQuery($query);

		if ($files = $db->loadObjectList())
		{
			foreach ($files as $file)
			{
				$message_id = $file->file_ticket_message_id;

				if (!empty($messages[$message_id]))
				{
					$message = &$messages[$message_id];

					// add the file to the array
					if (!isset($message->files))
					{
						$message->files = array();
					}
					$message->files[] = $file;
				}
			}
		}

		return !empty($messages) ? $messages : false;
	}

	/**
	 * 
	 * Get the ticket info without replies
	 * 
	 */
	public function getSubject($ticket_id = '', $ticket_token = '')
	{
		if ( $this->ticket_subject !== null )
		{
			return $this->ticket_subject;
		}

		if ( !$ticket_id || !$ticket_token )
		{
			$ticket_id = $this->getState('ticket_id');
			$ticket_token = $this->getState('ticket_token');
		}

		if (!$ticket_id || !$ticket_token) {
			$this->ticket_subject = false;
			return $this->ticket_subject;
		}

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
		$query
			->select(
				[
					$db->quoteName('a.id'),
					$db->quoteName('a.ticket_id'),
					$db->quoteName('a.short_ticket_id'),
					$db->quoteName('a.ticket_token'),
					$db->quoteName('a.form_id'),
					$db->quoteName('a.ip'),
					$db->quoteName('a.user_id'),
					$db->quoteName('a.agent_id'),
					$db->quoteName('a.name'),
					$db->quoteName('a.email'),
					$db->quoteName('a.message_type'),
					$db->quoteName('a.department_id'),
					$db->quoteName('a.subject'),
					$db->quoteName('a.message'),
					$db->quoteName('a.custom_fields'),
					$db->quoteName('a.status'),
					$db->quoteName('a.user_type'),
					$db->quoteName('a.created_date'),
					$db->quoteName('a.last_update_date'),
					$db->quoteName('a.created_by'),
					$db->quoteName('b.name', 'department_name'),
					$db->quoteName('c.form_name')
				]
			)
            ->from($db->quoteName('#__gs_tickets', 'a'))
			->join(
				'LEFT', 
				$db->quoteName('#__gs_tickets_departments', 'b'), 
				$db->quoteName('b.id') . ' = ' . $db->quoteName('a.department_id'))
			->join(
				'LEFT', 
				$db->quoteName('#__gs_tickets_forms', 'c'), 
				$db->quoteName('c.id') . ' = ' . $db->quoteName('a.form_id'))
			->where([
				$db->quoteName('a.published') . ' = ' . $db->quote('1'),
				$db->quoteName('a.message_type') . ' = ' . $db->quote('ticket'),
				$db->quoteName('a.ticket_id') . ' = :ticket_id',
				$db->quoteName('a.ticket_token') . ' = :ticket_token'
			])
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':ticket_token', $ticket_token, ParameterType::STRING);

		$db->setQuery($query);
		$results = $db->loadObject();
		$this->ticket_subject = !empty( $results ) ? $results : false;

		return $this->ticket_subject;
	}

	/**
	 * Get Agent user id by Department id
	 */
	public function getAgentIdByDepartmentId()
	{
		$ticket = $this->getSubject();

		if ( empty($ticket) )
		{
			return false;
		}

		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('agent_id'))
			->from($db->quoteName('#__gs_tickets_departments'))
            ->where($db->quoteName('id') . ' = :id')
			->bind(':id', $ticket->department_id, ParameterType::INTEGER);
		$db->setQuery($query);
		$result = $db->loadResult();

        return !empty( $result ) ? (int) $result : false;
	}

	/**
	 * 
	 * Get the ticket info without replies
	 * 
	 */
	public function getOthertickets()
	{
		$user = $this->getCurrentUser();
		$guest = $user->get('guest');

		$ticket = $this->getSubject();

		if ( empty($ticket) )
		{
			return false;
		}

		$form = $this->getForm();
		if ( empty( $form ) )
		{
			return false;
		}

		$form_params = $form->params ? unserialize( $form->params ) : [];
		$noof_other_tickets = $form_params['noof_other_tickets'] ?? 10;

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
			->select($db->quoteName([
				'a.ticket_id', 'a.short_ticket_id', 'a.ticket_token', 'a.subject'
			]))
            ->from($db->quoteName('#__gs_tickets', 'a'))
			->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('a.message_type') . ' = ' . $db->quote('ticket'))
			->where($db->quoteName('a.id') . ' != :id')
			->bind(':id', $ticket->id, ParameterType::INTEGER);

		if ( (int) $ticket->user_id > 0 )
		{
			$query
				->where($db->quoteName('a.user_id') . ' = :user_id')
				->bind(':user_id', $ticket->user_id, ParameterType::INTEGER);
		}
		else
		{
			$query
				->where($db->quoteName('a.email') . ' = :email')
				->bind(':email', $ticket->email, ParameterType::STRING);
		}

		$query->setLimit($noof_other_tickets);

		$db->setQuery($query);

		$results = $db->loadObjectList();
		return !empty( $results ) ? $results : false;
	}

	/**
	 * Get Form
	 */
	public function getForm()
	{
		if ( $this->ticket_form !== null )
		{
			return $this->ticket_form;
		}
		$ticket = $this->getSubject();

		if ( empty($ticket) )
		{
			$this->ticket_form = false;
			return $this->ticket_form;
		}

        $db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the form table.
		$query
			->select($db->quoteName(['id', 'form_name', 'form_fields', 'form_type', 'recaptcha', 'input_class', 'submit_text', 'verify_otp_text', 'submit_class', 'fileupload', 'upload_filelimit', 'upload_filesize', 'allowed_filetypes', 'fileupload_label', 'fileupload_help', 'verify_email', 'suggest_docs', 'suggest_docs_cats', 'params']))
			->from($db->quoteName('#__gs_tickets_forms'));
		if ( $ticket->form_id )
		{
			$query->where($db->quoteName('id') . ' = :id')
			->bind(':id', $ticket->form_id, ParameterType::INTEGER);
		}
		else
		{
			$query->where($db->quoteName('form_type') . ' = ' . $db->quote('core'));
		}
		$db->setQuery($query);

		$results = $db->loadObject();
		$this->ticket_form = !empty( $results ) ? $results : false;
		return $this->ticket_form;
	}

	/**
	 * Get Single Department by Id
	 */
	public function getDepartment( $id )
	{
		if ( !$id )
		{
			return false;
		}

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select($db->quoteName(array('id', 'name', 'agent_id', 'additional_emails', 'additional_emails_type', 'type')))
            ->from($db->quoteName('#__gs_tickets_departments'))
			->where($db->quoteName('id') . ' = :id')
			->bind(':id', $id, ParameterType::INTEGER);

		$db->setQuery($query);
		$result = $db->loadObject();

		return !empty( $result ) ? $result : false;
	}

	/**
	 * Get user's signature and profile photo
	 */
	public function getUserProfile()
	{
		return false;
	}

	/**
	 * Get user id by email
	 */
	public function getUserIdByEmail( $email )
	{
		if ( !$email )
		{
			return false;
		}

        $db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the form table.
		$query
			->select($db->quoteName('id'))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('block') . ' = 0')
			->where($db->quoteName('email') . ' = :email')
			->bind(':email', $email, ParameterType::STRING);
		$db->setQuery($query);
		$result = $db->loadResult();

        return !empty( $result ) ? $result : false;
	}

	/**
	 * Update ticket status
	 */
	public function updateTicketStatus( $ticket_id, $ticket_token, $status )
	{
		if ( !$ticket_id || !$ticket_token || !$status )
		{
			return false;
		}

        $db = $this->getDatabase();
        $query = $db->getQuery(true);
		$date = Factory::getDate()->toSql();

		// Create the base update statement.
		$query->update($db->quoteName('#__gs_tickets'))
			->set(
				[
					$db->quoteName('status') . ' = :status',
					$db->quoteName('last_update_date') . ' = :last_update_date',
				]
			)
			->where(
				[
					$db->quoteName('ticket_id') . ' = :ticket_id',
					$db->quoteName('ticket_token') . ' = :ticket_token',
				]
			)
			->bind(':status', $status, ParameterType::STRING)
			->bind(':last_update_date', $date)
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':ticket_token', $ticket_token, ParameterType::STRING);

		// Set the query and execute the update.
		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}
		return true;
	}

	/**
	 * Get last reply user type of a ticket
	 */
	public function lastReplyUserType($ticket_id, $ticket_token)
	{
		if (!$ticket_id || !$ticket_token) {
			return false;
		}

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
			->select($db->quoteName('user_type'))
            ->from($db->quoteName('#__gs_tickets'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('ticket_id') . ' = :ticket_id')
			->where($db->quoteName('ticket_token') . ' = :ticket_token')
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':ticket_token', $ticket_token, ParameterType::STRING)
			->order('created_date DESC')
			->setLimit('1');

		$db->setQuery($query);
		$result = $db->loadResult();
		return !empty( $result ) ? $result : false;
	}

	/**
	 * Delete tickets
	 */
	public function delete( $ticket_id, $ticket_token )
    {
		if ( !$ticket_id || !$ticket_token )
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_INVALID_REQUEST'), 'error');
			return false;
		}

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__gs_tickets'))
			->where($db->quoteName('ticket_id') . ' = :ticket_id')
			->where($db->quoteName('ticket_token') . ' = :ticket_token')
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':ticket_token', $ticket_token, ParameterType::STRING);
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }

	/**
	 * Delete ticket replies
	 */
	public function deletereply( $ticket_id, $ticket_token, $reply_id )
    {
		if ( !$ticket_id || !$ticket_token || !$reply_id )
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_INVALID_REQUEST'), 'error');
			return false;
		}

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__gs_tickets'))
			->where($db->quoteName('id') . ' = :id')
			->where($db->quoteName('ticket_id') . ' = :ticket_id')
			->where($db->quoteName('ticket_token') . ' = :ticket_token')
			->bind(':id', $reply_id, ParameterType::INTEGER)
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':ticket_token', $ticket_token, ParameterType::STRING);
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }

	/**
	 * Get a single file informations
	 */
	public function getFileInfo( $ticket_id, $file_id )
	{
		if (!$ticket_id || !$file_id) {
			return false;
		}

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
			->select($db->quoteName([
				'file_id', 'file_ticket_id', 'file_ticket_message_id', 'file_name_raw', 'file_name_enc', 'file_size', 'file_created'
			]))
            ->from($db->quoteName('#__gs_tickets_attachments'))
			->where($db->quoteName('file_ticket_id') . ' = :ticket_id')
			->where($db->quoteName('file_id') . ' = :file_id')
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':file_id', $file_id, ParameterType::INTEGER);

		$db->setQuery($query);
		$result = $db->loadObject();
		return !empty( $result ) ? $result : false;
	}

	/**
	 * Delete file
	 */
	public function deleteFile( $ticket_id, $file_id, $request_by )
	{
		if (!$ticket_id || !$file_id || !$request_by) {
			return false;
		}

		$db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__gs_tickets_attachments'))
			->where($db->quoteName('file_ticket_id') . ' = :ticket_id')
			->where($db->quoteName('file_id') . ' = :file_id')
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':file_id', $file_id, ParameterType::INTEGER);
		if ( $request_by == 'user' )
		{
			$query->where($db->quoteName('created_by') . ' = ' . $db->quote('user'));
		}
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
	}

	/**
	 * Get last message from a ticket
	 */
	public function getLastMessage( $ticket_id, $ticket_token )
	{
		if (!$ticket_id || !$ticket_token) {
			return false;
		}

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
			->select($db->quoteName([
				'id', 'status'
			]))
            ->from($db->quoteName('#__gs_tickets'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('ticket_id') . ' = :ticket_id')
			->where($db->quoteName('ticket_token') . ' = :ticket_token')
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':ticket_token', $ticket_token, ParameterType::STRING)
			->order('id DESC')
			->setLimit('1');

		$db->setQuery($query);
		$result = $db->loadObject();
		return !empty( $result ) ? $result : false;
	}

	/**
	 * Update ticket message
	 */
	public function editMessage( $ticket_id, $ticket_token, $message_id, $message )
	{
		if ( !$ticket_id || !$ticket_token || !$message_id || !$message )
		{
			return false;
		}

        $db = $this->getDatabase();
        $query = $db->getQuery(true);
		$date = Factory::getDate()->toSql();

		// Create the base update statement.
		$query->update($db->quoteName('#__gs_tickets'))
			->set(
				[
					$db->quoteName('message') . ' = :message'
				]
			)
			->where(
				[
					$db->quoteName('ticket_id') . ' = :ticket_id',
					$db->quoteName('ticket_token') . ' = :ticket_token',
					$db->quoteName('id') . ' = :id',
				]
			)
			->bind(':message', $message)
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':ticket_token', $ticket_token, ParameterType::STRING)
			->bind(':id', $message_id, ParameterType::INTEGER);

		// Set the query and execute the update.
		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}
		return true;
	}
}
