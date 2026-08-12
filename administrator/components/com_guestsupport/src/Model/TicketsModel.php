<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Language\Text;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class TicketsModel extends ListModel
{
    /**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$db = $this->getDatabase();

		// Search, sort and filters
		$filters = $this->getUserOptions();
		$allowed_status = array( 'open', 'pending', 'closed' );
		$searchstring_escaped = $db->escape( $filters->searchstring, true );
		$searchstring_forlike = '%' . $searchstring_escaped . '%';

		$subQuery = $db->getQuery(true)
			->select([
				$db->quoteName('ticket_id'),
				'COUNT(CASE WHEN ' . $db->quoteName('message_type') . ' = ' . $db->quote('reply') . ' THEN 1 END) AS ' . $db->quoteName('total_replies')
			])
			->from($db->quoteName('#__gs_tickets'))
			->group($db->quoteName('ticket_id'));

		$query = $db->getQuery(true);
		$query
			->select(
				[
					$db->quoteName('a.id'),
					$db->quoteName('a.ticket_id'),
					$db->quoteName('a.short_ticket_id'),
					$db->quoteName('a.ticket_token'),
					$db->quoteName('a.form_id'),
					$db->quoteName('a.user_id'),
					$db->quoteName('a.agent_id'),
					$db->quoteName('a.name'),
					$db->quoteName('a.email'),
					$db->quoteName('a.message_type'),
					$db->quoteName('a.department_id'),
					$db->quoteName('a.subject'),
					$db->quoteName('a.message'),
					$db->quoteName('a.custom_fields'),
					$db->quoteName('a.encrypted_message'),
					$db->quoteName('a.status'),
					$db->quoteName('a.user_type'),
					$db->quoteName('a.created_date'),
					$db->quoteName('a.last_update_date'),
					$db->quoteName('a.created_by'),
					$db->quoteName('d.name', 'department_name'),
					$db->quoteName('f.form_name'),
					$db->quoteName('sub.total_replies')
				]
			)

			->from($db->quoteName('#__gs_tickets', 'a'))
			->join('LEFT', '(' . $subQuery . ') AS sub ON ' . $db->quoteName('sub.ticket_id') . ' = ' . $db->quoteName('a.ticket_id'))
			->join(
				'LEFT', 
				$db->quoteName('#__gs_tickets_departments', 'd'), 
				$db->quoteName('d.id') . ' = ' . $db->quoteName('a.department_id'))
			->join(
				'LEFT', 
				$db->quoteName('#__gs_tickets_forms', 'f'), 
				$db->quoteName('f.id') . ' = ' . $db->quoteName('a.form_id'))
			->where($db->quoteName('a.message_type') . ' = ' . $db->quote('ticket'))
			->where($db->quoteName('a.published') . ' = ' . $db->quote('1'));

		if ( $filters->status && in_array( $filters->status, $allowed_status ) )
		{
			$query->where($db->quoteName('a.status') . ' = :ticket_status')
			->bind(':ticket_status', $filters->status, ParameterType::STRING);
		}
		if ( $filters->searchstring )
		{
			$query->where(
				'(' . 
					$db->quoteName('a.ticket_id') . ' = :ticket_id OR ' . 
					$db->quoteName('a.short_ticket_id') . ' = :short_ticket_id OR ' . 
					$db->quoteName('a.email') . ' LIKE :email OR ' . 
					$db->quoteName('a.subject') . ' LIKE :subject' .
				')'
			)
			->bind(':ticket_id', $searchstring_escaped, ParameterType::STRING)
			->bind(':short_ticket_id', $searchstring_escaped, ParameterType::STRING)
			->bind(':email', $searchstring_forlike, ParameterType::STRING)
			->bind(':subject', $searchstring_forlike, ParameterType::STRING);
		}
		if ( $filters->department )
		{
			$query->where($db->quoteName('a.department_id') . ' = :department_id')
			->bind(':department_id', $filters->department, ParameterType::INTEGER);
		}
		if ( $filters->form )
		{
			$filters_form_id = $filters->form == -1 ? 0 : $filters->form;
			$query->where($db->quoteName('a.form_id') . ' = :form_id')
			->bind(':form_id', $filters_form_id, ParameterType::INTEGER);
		}

		// Tickets Sort
		if ( $filters->sortby == 'created_asc' )
		{
			$query->order('a.created_date ASC');
		}
		elseif ( $filters->sortby == 'created_desc' )
		{
			$query->order('a.created_date DESC');
		}
		elseif ( $filters->sortby == 'updated_asc' )
		{
			$query->order('a.last_update_date ASC');
		}
		elseif ( $filters->sortby == 'updated_desc' )
		{
			$query->order('a.last_update_date DESC');
		}
		else
		{
			$query->order(
				"CASE 
					WHEN a.status = 'open' THEN 1
					WHEN a.status = 'pending' THEN 2
					ELSE 3
				END ASC, 
				CASE 
					WHEN a.status = 'open' THEN a.last_update_date END ASC,
				CASE 
					WHEN a.status = 'pending' THEN a.last_update_date END DESC,
				CASE 
					WHEN a.status = 'closed' THEN a.last_update_date END DESC"
			);
		}

		return $query;
	}

	/**
	 * Get search, sort and filters
	 */
	public function getUserOptions()
	{
		$app  = Factory::getApplication();
        $input = GuestsupportHelper::getInput();

		// Search, sort and filters
		$status = $input->getString('status', '');
		$sortby = $input->getString('sortby', '');
		$searchstring = $input->getString('s', '');
		$department = $input->getInt('department', 0);
		$form = $input->getInt('form', 0);

		$session = Factory::getApplication()->getSession();

		// Store to session
		$session->set('com_guestsupport.b_filter_status', $status);

		if ( isset( $_POST['sortby'] ) )
		{
			$session->set('com_guestsupport.b_filter_sortby', $sortby);
		}
		if ( isset( $_POST['s'] ) )
		{
			$session->set('com_guestsupport.b_filter_searchstring', $searchstring);
		}
		if ( isset( $_POST['department'] ) )
		{
			$session->set('com_guestsupport.b_filter_department', $department);
		}
		if ( isset( $_POST['form'] ) )
		{
			$session->set('com_guestsupport.b_filter_form', $form);
		}

		// Get from session
		$s_status = $session->get('com_guestsupport.b_filter_status', '');
		$s_sortby = $session->get('com_guestsupport.b_filter_sortby', '');
		$s_searchstring = $session->get('com_guestsupport.b_filter_searchstring', '');
		$s_department = $session->get('com_guestsupport.b_filter_department', 0);
		$s_form = $session->get('com_guestsupport.b_filter_form', 0);

		// set options
		$filters = new \stdClass();
		$filters->status = $status ? $status : $s_status;
		$filters->sortby = $sortby ? $sortby : $s_sortby;
		$filters->searchstring = $searchstring ? $searchstring : $s_searchstring;
		$filters->department = $department ? $department : $s_department;
		$filters->form = $form ? $form : $s_form;

		return $filters;
	}

	/**
	 * Count tickets by status
	 */
	public function getCountStatus()
	{
		$db = $this->getDatabase();
		// Search, sort and filters
		$filters = $this->getUserOptions();
		$searchstring_escaped = $db->escape( $filters->searchstring, true );
		$searchstring_forlike = '%' . $searchstring_escaped . '%';

		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				[
                    'COUNT(' . $db->quoteName('id') . ') AS ' . $db->quoteName('all_tickets'),
					'COUNT(CASE WHEN ' . $db->quoteName('status') . ' = ' . $db->quote('open') . ' THEN 1 END) AS ' . $db->quoteName('open_tickets'),
					'COUNT(CASE WHEN ' . $db->quoteName('status') . ' = ' . $db->quote('pending') . ' THEN 1 END) AS ' . $db->quoteName('pending_tickets'),
					'COUNT(CASE WHEN ' . $db->quoteName('status') . ' = ' . $db->quote('closed') . ' THEN 1 END) AS ' . $db->quoteName('closed_tickets')
                ]
			)
			->from($db->quoteName('#__gs_tickets'))
			->where($db->quoteName('message_type') . ' = ' . $db->quote('ticket'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'));

		if ( $filters->searchstring )
		{
			$query->where(
				'(' . 
					$db->quoteName('ticket_id') . ' = :ticket_id OR ' . 
					$db->quoteName('email') . ' LIKE :email OR ' . 
					$db->quoteName('subject') . ' LIKE :subject' .
				')'
			)
			->bind(':ticket_id', $searchstring_escaped, ParameterType::STRING)
			->bind(':email', $searchstring_forlike, ParameterType::STRING)
			->bind(':subject', $searchstring_forlike, ParameterType::STRING);
		}
		if ( $filters->department )
		{
			$query->where($db->quoteName('department_id') . ' = :department_id')
			->bind(':department_id', $filters->department, ParameterType::INTEGER);
		}
		if ( $filters->form )
		{
			$filters_form_id = $filters->form == -1 ? 0 : $filters->form;
			$query->where($db->quoteName('form_id') . ' = :form_id')
			->bind(':form_id', $filters_form_id, ParameterType::INTEGER);
		}

		// Tickets Sort
		if ( $filters->sortby == 'created_asc' )
		{
			$query->order('created_date ASC');
		}
		elseif ( $filters->sortby == 'created_desc' )
		{
			$query->order('created_date DESC');
		}
		elseif ( $filters->sortby == 'updated_asc' )
		{
			$query->order('last_update_date ASC');
		}
		elseif ( $filters->sortby == 'updated_desc' )
		{
			$query->order('last_update_date DESC');
		}
		else
		{
			$query->order('status = ' . $db->quote('open') . ' DESC, last_update_date ASC');
		}

		$db->setQuery($query);
		$results = $db->loadObject();

		return $results;
	}

	/**
	 * Get forms
	 */
	public function getForms()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select($db->quoteName(['id', 'form_name']))
			->from($db->quoteName('#__gs_tickets_forms'));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}

	/**
	 * Delete tickets
	 */
	public function delete()
    {
		$app  = Factory::getApplication();
        $input = GuestsupportHelper::getInput();
        $ids = $input->post->get('ticket_ids', array(), 'array');

		if ( empty( $ids ) )
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_DELETE_NO_ID'), 'error');
			return false;
		}

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__gs_tickets'))
			->where($db->quoteName('ticket_id') . ' IN (' . implode(',', array_map([$db, 'quote'], $ids)) . ')');
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());

            return false;
        }

		Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_DELETE_SUCCESS'), 'success');

        return true;
    }

	/**
	 * Hide review notice
	 */
	public function hideReviewNoticeQuery()
    {
        $db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__gs_tickets_config'))
			->set($db->quoteName('value') . ' = ' . $db->quote('no'))
			->where($db->quoteName('name') . ' = ' . $db->quote('ask_for_review'));

		$db->setQuery($query);

		try {
			$db->execute();
		} catch (\RuntimeException $e) {
			return false;
		}

        return true;
    }
}
