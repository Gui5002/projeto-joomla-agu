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
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;
use Joomla\Database\Exception\ExecutionFailureException;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class CreateticketModel extends ListModel
{
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
		$input = GuestsupportHelper::getInput();

        $params = $app->getParams();
        $this->setState('params', $params);

		// Form id
		$form_id = $input->get('id', 0, 'uint');
		$this->setState('formid', $form_id);
    }

	/**
	 * Get Form
	 */
	public function getForm( $id = 0 )
	{
		if ( !$id )
		{
			$id = $this->getState('formid');
		}

        $db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the form table.
		$query
			->select($db->quoteName(['id', 'form_name', 'form_fields', 'form_type', 'recaptcha', 'input_class', 'submit_text', 'verify_otp_text', 'submit_class', 'fileupload', 'upload_filelimit', 'upload_filesize', 'allowed_filetypes', 'fileupload_label', 'fileupload_help', 'verify_email', 'suggest_docs', 'suggest_docs_cats']))
			->from($db->quoteName('#__gs_tickets_forms'))
			->where($db->quoteName('form_type') . ' = ' . $db->quote('core'));
		$db->setQuery($query);
		$result = $db->loadObject();

        return $result;
	}

	/**
	 * Get Departments for this form
	 */
	public function getDepartmentsForThisForm()
	{
		$form_id = $this->getState('formid');
		if ( !$form_id )
		{
			return false;
		}

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query
			->select(
                [
                    $db->quoteName('a.department_id', 'id'),
					$db->quoteName('b.name')
                ]
            )
			->from($db->quoteName('#__gs_tickets_department_to_forms', 'a'))
			->join(
				'INNER', 
				$db->quoteName('#__gs_tickets_departments', 'b'), 
				$db->quoteName('b.id') . ' = ' . $db->quoteName('a.department_id'))
			->where($db->quoteName('a.form_id') . ' = :form_id')
			->bind(':form_id', $form_id, ParameterType::INTEGER);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Get Departments
	 */
	public function getDepartments()
	{
		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select($db->quoteName(array('id', 'name', 'agent_id', 'additional_emails', 'additional_emails_type', 'type')))
            ->from($db->quoteName('#__gs_tickets_departments'));
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
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
	 * Get user id by email
	 */
	public function getUserByEmail( $email )
	{
		if ( !$email )
		{
			return false;
		}

        $db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the form table.
		$query
			->select($db->quoteName(array('id', 'name', 'email')))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('block') . ' = 0')
			->where($db->quoteName('email') . ' = :email')
			->bind(':email', $email, ParameterType::STRING);
		$db->setQuery($query);
		$result = $db->loadObject();

        return !empty( $result ) ? $result : false;
	}

	/**
	 * 
	 * Get the ticket info without replies
	 * 
	 */
	public function getTicket( $ticket_id, $ticket_token )
	{
		if (!$ticket_id || !$ticket_token) {
			return false;
		}

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
			->select($db->quoteName([
				'a.id', 'a.ticket_id', 'a.short_ticket_id', 'a.ticket_token', 'a.form_id', 'a.user_id', 'a.agent_id', 'a.name', 'a.email', 'a.message_type', 'a.department_id', 'a.subject', 'a.message', 'a.custom_fields', 'a.encrypted_message', 'a.status', 'a.user_type', 'a.created_date', 'a.last_update_date', 'a.created_by'
			]))
            ->from($db->quoteName('#__gs_tickets', 'a'))
			->where($db->quoteName('a.published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('a.message_type') . ' = ' . $db->quote('ticket'))
			->where($db->quoteName('a.ticket_id') . ' = :ticket_id')
			->where($db->quoteName('a.ticket_token') . ' = :ticket_token')
			->bind(':ticket_id', $ticket_id, ParameterType::STRING)
			->bind(':ticket_token', $ticket_token, ParameterType::STRING);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Update ticket
	 */
	public function updateTicket( $data, $condition )
	{
		if ( !is_array( $data ) || !is_array( $condition ) )
		{
			return false;
		}

		$db = $this->getDatabase();
        $query = $db->getQuery(true);

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
			->bind(':status', $data['status'], ParameterType::STRING)
			->bind(':last_update_date', $data['last_update_date'])
			->bind(':ticket_id', $condition['ticket_id'], ParameterType::STRING)
			->bind(':ticket_token', $condition['ticket_token'], ParameterType::STRING);

		// Set the query and execute the update.
		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Get tickets with id and token
	 */
	public function getTicketById( $ticket_id, $ticket_token = '' )
	{
		if ( !$ticket_id )
		{
			return false;
		}

        $db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('id'))
			->from($db->quoteName('#__gs_tickets'))
			->where($db->quoteName('ticket_id') . ' = :ticket_id')
			->bind(':ticket_id', $ticket_id, ParameterType::STRING);
		if ( $ticket_token )
		{
			$query
				->where($db->quoteName('ticket_token') . ' = :ticket_token')
				->bind(':ticket_token', $ticket_token, ParameterType::STRING);
		}
		$db->setQuery($query);
		$result = $db->loadResult();

        return $result ? $result : false;
	}

	/**
	 * Get default department id
	 */
	public function getDefaultDepartment()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('id'))
			->from($db->quoteName('#__gs_tickets_departments'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('core'))
            ->setLimit('1');
		$db->setQuery($query);
		$result = $db->loadResult();

        return !empty( $result ) ? $result : 0;
	}

	/**
	 * Get Agent user id by Department id
	 */
	public function getAgentIdByDepartmentId( $department_id )
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('agent_id'))
			->from($db->quoteName('#__gs_tickets_departments'))
            ->where($db->quoteName('id') . ' = :id')
			->bind(':id', $department_id, ParameterType::INTEGER);
		$db->setQuery($query);
		$result = $db->loadResult();

        return !empty( $result ) ? (int) $result : false;
	}

	/**
	 * Create New Ticket
	 */
	public function createTicket( $data )
	{
		if ( !is_array( $data ) )
		{
			return false;
		}

		$db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $created = Factory::getDate()->toSql();

		$columns = [
            'ticket_id',
            'short_ticket_id',
			'ticket_token',
			'form_id',
			'ticket_email_hash',
			'ip',
			'user_id',
			'agent_id',
			'name',
			'email',
			'message_type',
			'department_id',
			'subject',
			'message',
			'custom_fields',
			'encrypted_message',
			'status',
			'autoreply',
			'published',
			'update_user_id',
			'user_type',
			'created_date',
			'last_update_date',
			'created_by'
        ];

        $values = [
            ':ticket_id',
            ':short_ticket_id',
			':ticket_token',
			':form_id',
			':ticket_email_hash',
			':ip',
			':user_id',
			':agent_id',
			':name',
			':email',
			':message_type',
			':department_id',
			':subject',
			':message',
			':custom_fields',
			':encrypted_message',
			':status',
			':autoreply',
			':published',
			':update_user_id',
			':user_type',
			':created_date',
			':last_update_date',
			':created_by'
        ];

		$query
            ->insert($db->quoteName('#__gs_tickets'), false)
            ->columns($db->quoteName($columns))
            ->values(implode(', ', $values))
            ->bind(':ticket_id', $data['ticket_id'], ParameterType::STRING)
            ->bind(':short_ticket_id', $data['short_ticket_id'], ParameterType::STRING)
			->bind(':ticket_token', $data['ticket_token'], ParameterType::STRING)
			->bind(':form_id', $data['form_id'], ParameterType::INTEGER)
			->bind(':ticket_email_hash', $data['ticket_email_hash'], ParameterType::STRING)
			->bind(':ip', $data['ip'], ParameterType::STRING)
			->bind(':user_id', $data['user_id'], ParameterType::INTEGER)
			->bind(':agent_id', $data['agent_id'], ParameterType::INTEGER)
			->bind(':name', $data['name'], ParameterType::STRING)
			->bind(':email', $data['email'], ParameterType::STRING)
			->bind(':message_type', $data['message_type'], ParameterType::STRING)
			->bind(':department_id', $data['department_id'], ParameterType::INTEGER)
			->bind(':subject', $data['subject'], ParameterType::STRING)
			->bind(':message', $data['message'])
			->bind(':custom_fields', $data['custom_fields'])
			->bind(':encrypted_message', $data['encrypted_message'])
			->bind(':status', $data['status'], ParameterType::STRING)
			->bind(':autoreply', $data['autoreply'], ParameterType::INTEGER)
			->bind(':published', $data['published'], ParameterType::INTEGER)
			->bind(':update_user_id', $data['update_user_id'], ParameterType::INTEGER)
			->bind(':user_type', $data['user_type'], ParameterType::STRING)
			->bind(':created_date', $created)
			->bind(':last_update_date', $created)
			->bind(':created_by', $data['created_by'], ParameterType::STRING);

        $db->setQuery($query);

		try {
            $db->execute();
        } catch (ExecutionFailureException $e) {
            return false;
        }

		return (int) $db->insertid();
	}
}
