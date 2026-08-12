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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class DepartmentModel extends ListModel
{
    /**
     * Get single department
     */
    public function getItem( $id = 0, $name = '' )
    {
        if ( !$id )
        {
            $app = Factory::getApplication();
            $input = GuestsupportHelper::getInput();
            $id = $input->getInt('id', 0);
        }

        $result = '';

        if ( $id )
        {
            $db = $this->getDatabase();
            $query = $db->getQuery(true);

            // Select the required fields from the table.
            $query
                ->select($db->quoteName(array('id', 'name', 'agent_id', 'additional_emails', 'additional_emails_type', 'type')))
                ->from($db->quoteName('#__gs_tickets_departments'))
				->where($db->quoteName('type') . ' = ' . $db->quote('core'));
            if ( $name )
            {
                $query->where($db->quoteName('name') . ' = :name');
                $query->where($db->quoteName('id') . ' != :id');
                $query->bind(':name', $name, ParameterType::STRING);
                $query->bind(':id', $id, ParameterType::INTEGER);
            }
            else
            {
                $query->where($db->quoteName('id') . ' = :id');
                $query->bind(':id', $id, ParameterType::INTEGER);
            }
            $db->setQuery($query);
            $result = $db->loadObject();
        }

        return $result;
    }

	/**
     * Count departments by name
     */
    public function getDepartmentsByName( $name )
    {
        if ( !$name )
        {
			return false;
        }

		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query
			->select('COUNT(id)')
			->from($db->quoteName('#__gs_tickets_departments'))
			->where($db->quoteName('name') . ' = :name')
			->bind(':name', $name, ParameterType::STRING);
		$db->setQuery($query);
		$count = $db->loadResult();

        return $count;
    }

	/**
     * Load xml form
     */
    public function getXmlform($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_guestsupport.department', 'department', array('control' => '', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

		return $form;
    }

	/**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
		$selected_ids = $this->getFormidsbyDepartmentId();
		$data = new \stdClass();
		$data->form_ids = !empty( $selected_ids ) ? $selected_ids : [1];

        $this->preprocessData('com_guestsupport.department', $data);

        return $data;
    }

	/**
     * Get form ids for this department
     */
    public function getFormidsbyDepartmentId()
    {
        $app = Factory::getApplication();
        $input = GuestsupportHelper::getInput();
        $department_id = $input->getInt('id', 0);

		if ( !$department_id )
		{
			return false;
		}

		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('form_id'))
			->from($db->quoteName('#__gs_tickets_department_to_forms'))
			->where($db->quoteName('department_id') . ' = :department_id')
			->bind(':department_id', $department_id, ParameterType::INTEGER);
		$db->setQuery($query);
		$result = $db->loadColumn();

        return $result;
    }

    /**
     * Update department
     */
    public function update($id)
    {
        if ( !$id )
        {
            return false;
        }
        $db = $this->getDatabase();
        $app  = Factory::getApplication();
        $input = GuestsupportHelper::getInput();

        $listUrl = Route::_('index.php?option=com_guestsupport&view=departments', false);
		$editUrl = Route::_('index.php?option=com_guestsupport&view=department&layout=edit&id=' . (int) $id, false);

        if ( !$id )
		{
			$editUrl = $listUrl;
		}

        $name = $input->post->getString('name', '');
		$agent_id = $input->post->getInt('agent_id', 0);
		$additional_emails = trim($input->post->getHtml('additional_emails', ''));
		$additional_emails_type = $input->post->getString('additional_emails_type', 'both');
		$formIds = $input->get('form_ids', [], 'array');

        if ( !$name )
        {
            Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_EDIT_EMPTY_NAME'), 'error');
            $app->redirect($editUrl);
            return false;
        }
        if ( !$agent_id )
        {
            Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_EDIT_EMPTY_AGENT'), 'error');
            $app->redirect($editUrl);
            return false;
        }
		if ( empty( $formIds ) )
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_EDIT_SELECT_APPLICABLE_FORMS'), 'error');
            $app->redirect($editUrl);
            return false;
		}

		// Update department query
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__gs_tickets_departments'))
            ->set(
                [
                    $db->quoteName('name') . ' = :name',
                    $db->quoteName('agent_id') . ' = :agent_id',
                    $db->quoteName('additional_emails') . ' = :additional_emails',
                    $db->quoteName('additional_emails_type') . ' = :additional_emails_type'
                ]
            )
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':name', $name, ParameterType::STRING)
            ->bind(':agent_id', $agent_id, ParameterType::INTEGER)
            ->bind(':additional_emails', $additional_emails, !$additional_emails ? ParameterType::NULL : ParameterType::STRING)
            ->bind(':additional_emails_type', $additional_emails_type, ParameterType::STRING)
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        try {
            $result = $db->execute();
            if ( isset( $result ) && $result !== false )
            {
                Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_EDIT_SUCCESS'), 'success');

				// Delete existing form ids
				$query = $db->getQuery(true)
					->delete($db->quoteName('#__gs_tickets_department_to_forms'))
					->where($db->quoteName('department_id') . ' = :department_id')
					->bind(':department_id', $id, ParameterType::INTEGER);
				$db->setQuery($query);

				try {
					$db->execute();
				} catch (\RuntimeException $e) {
					// Do nothing
				}

				// Assign form ids to this department
				foreach ( $formIds as $form_id ) {
					$query = $db->getQuery(true);
					$query
						->insert($db->quoteName('#__gs_tickets_department_to_forms'))
						->set($db->quoteName('department_id') . ' = :department_id')
						->set($db->quoteName('form_id') . ' = :form_id')
						->bind(':department_id', $id, ParameterType::INTEGER)
						->bind(':form_id', $form_id, ParameterType::INTEGER);

					$db->setQuery($query);

					try {
						$db->execute();
					} catch (\RuntimeException $e) {
						// Do nothing
					}
				}
            }
            else
            {
                Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_EDIT_ERROR'), 'error');
            }
        } catch (\RuntimeException $e) {
            $app->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
        return true;
    }
}
