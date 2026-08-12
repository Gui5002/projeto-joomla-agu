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
use Joomla\CMS\Session\Session;
use Joomla\CMS\Filter\InputFilter;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class FormModel extends ListModel
{
    /**
     * Get single form
     */
    public function getItem( $id = 0 )
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
                ->select($db->quoteName(['id', 'form_name', 'form_fields', 'form_type', 'recaptcha', 'input_class', 'submit_text', 'verify_otp_text', 'submit_class', 'fileupload', 'upload_filelimit', 'upload_filesize', 'allowed_filetypes', 'fileupload_label', 'fileupload_help', 'verify_email', 'suggest_docs', 'suggest_docs_cats', 'params']))
                ->from($db->quoteName('#__gs_tickets_forms'))
                ->where($db->quoteName('id') . ' = :id')
                ->where($db->quoteName('form_type') . ' = ' . $db->quote('core'))
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query);
            $result = $db->loadObject();
        }

        return $result;
    }

	/**
     * Load xml form
     */
    public function getXmlform($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_guestsupport.form', 'form', array('control' => '', 'load_data' => $loadData));

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
		$item = $this->getItem();

		if ( empty( $item ) )
		{
			return [];
		}

		$data = $item;

		$docsCategories = $item->suggest_docs_cats ? unserialize( $item->suggest_docs_cats ) : [];
		$data->suggest_docs_cats = $docsCategories;

		// Change params
		$params = $item->params ? unserialize( $item->params ) : '';
		$data->{'params[top_text]'} = $params['top_text'] ?? '';
		$data->{'params[display_ticket_id]'} = $params['display_ticket_id'] ?? '';
		$data->{'params[display_ticket_status]'} = $params['display_ticket_status'] ?? '';
		$data->{'params[display_email]'} = $params['display_email'] ?? '';
		$data->{'params[display_form_name]'} = $params['display_form_name'] ?? '';
		$data->{'params[display_department]'} = $params['display_department'] ?? '';
		$data->{'params[display_ticket_creation_date]'} = $params['display_ticket_creation_date'] ?? '';
		$data->{'params[ticket_creation_date_format]'} = $params['ticket_creation_date_format'] ?? '';
		$data->{'params[display_last_updated_date]'} = $params['display_last_updated_date'] ?? '';
		$data->{'params[last_updated_date_format]'} = $params['last_updated_date_format'] ?? '';
		$data->{'params[display_custom_fields]'} = $params['display_custom_fields'] ?? '';
		$data->{'params[display_other_tickets]'} = $params['display_other_tickets'] ?? '';
		$data->{'params[noof_other_tickets]'} = $params['noof_other_tickets'] ?? '';
		$data->{'params[bottom_text]'} = $params['bottom_text'] ?? '';

        $this->preprocessData('com_guestsupport.form', $data);

        return $data;
    }

	/**
     * Get Joomla Categories
     */
    public function getCategories()
    {
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select($db->quoteName(['id', 'title', 'level']))
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_content'))
			->where($db->quoteName('published') . ' = ' . $db->quote(1))
			->order('lft ASC');
		$db->setQuery($query);
		$result = $db->loadObjectList();

        return $result;
    }

	/**
     * Count forms by name
     */
    public function getFormsByName( $name, $id = 0 )
    {
        if ( !$name )
        {
			return false;
        }

		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query
			->select('COUNT(id)')
			->from($db->quoteName('#__gs_tickets_forms'))
			->where($db->quoteName('form_name') . ' = :name')
			->bind(':name', $name, ParameterType::STRING);
		if ( $id )
		{
			$query->where($db->quoteName('id') . ' != :id')
			->bind(':id', $id, ParameterType::INTEGER);
		}	
		$db->setQuery($query);
		$count = $db->loadResult();

        return $count;
    }

    /**
     * Save form
     */
    public function save($id)
    {
		if ( !$id )
		{
			return false;
		}

        $db = $this->getDatabase();
        $app  = Factory::getApplication();
        $input = GuestsupportHelper::getInput();
		$filter = InputFilter::getInstance();

		// Get inputs except fields
		$form_name = trim($input->post->getString('form_name', ''));
		$recaptcha = $input->post->getInt('recaptcha', 0);

		$submit_text = trim($input->post->getString('submit_text', 'Create Ticket'));
		$verify_otp_text = trim($input->post->getString('verify_otp_text', 'Continue'));
		$submit_class = trim($input->post->getString('submit_class', 'r-gs-submit-button'));
		$input_class = trim($input->post->getString('input_class', 'r-gs-input'));

		$fileupload = $input->post->getInt('fileupload', 0);
		$upload_filesize = trim($input->post->getInt('upload_filesize', 2000));
		$allowed_filetypes = trim($input->post->getString('allowed_filetypes', ''));

		$verify_email = $input->post->getInt('verify_email', 0);

		$upload_filelimit = $input->post->getInt('upload_filelimit', 0);
		$fileupload_label = $input->post->getString('fileupload_label', '');
		$fileupload_help = $input->post->getString('fileupload_help', '');

		$suggest_docs = $input->post->getInt('suggest_docs', 0);
		$suggest_docs_cats_arr  = $input->post->get('suggest_docs_cats', array(), 'array');

		// Process suggest_docs_cats
		$suggest_docs_cats = serialize( $suggest_docs_cats_arr );

		// Get all post data
		$data = filter_var_array( $_POST );

		// Get params
		$params = isset( $data['params'] ) && !empty( $data['params'] ) ? serialize( $data['params'] ) : '';

		// Unset above inputs to get form input fields only
		unset(  
			$data['form_name'], 
			$data['recaptcha'],
			$data['submit_text'],
			$data['verify_otp_text'],
			$data['submit_class'],
			$data['input_class'],
			$data['fileupload'],
			$data['upload_filesize'],
			$data['allowed_filetypes'],
			$data['verify_email'],
			$data['upload_filelimit'],
			$data['fileupload_label'],
			$data['fileupload_help'],
			$data['suggest_docs'],
			$data['suggest_docs_cats'],
			$data['params'],
			$data['task'],
			$data['form_id'],
			$data[Session::getFormToken()]
		);

		// Sanitize inputs
		if ( !empty( $data ) )
		{
			foreach ($data as $name => $value) {
				if ( !in_array( $name, ['field_name', 'field_email', 'field_subject', 'field_department', 'field_message'] ) )
				{
					return false;
				}

				if ( is_array( $value ) )
				{
					$tmpData[$name] = array();
					foreach ($value as $name2 => $value2) {
						if ( $name2 == 'customtext' || $name2 == 'description' )
						{
							$tmpData[$name][$name2] = GuestsupportHelper::sanitizeHtml( $filter->clean($value2, 'RAW') );
						}
						elseif ( $name2 == 'options' )
						{
							$tmpData[$name][$name2] = $filter->clean($value2, 'HTML');
						}
						else
						{
							$tmpData[$name][$name2] = $filter->clean($value2, 'STRING');
						}
					}
					if ( !empty( $tmpData[$name] ) )
					{
						$data[$name] = $tmpData[$name];
					}
				}
				else
				{
					if ( $name == 'customtext' || $name == 'description' )
					{
						$data[$name] = GuestsupportHelper::sanitizeHtml( $filter->clean($value, 'RAW') );
					}
					elseif ( $name == 'options' )
					{
						$data[$name] = $filter->clean($value, 'HTML');
					}
					else
					{
						$data[$name] = $filter->clean($value, 'STRING');
					}
				}
			}
		}

		// Set form fields
		$form_fields = serialize( $data );

		/**
		 * Update
		 */
		$query = $db->getQuery(true)
			->update($db->quoteName('#__gs_tickets_forms'))
			->set(
				[
					$db->quoteName('form_name') . ' = :form_name',
					$db->quoteName('form_fields') . ' = :form_fields',
					$db->quoteName('recaptcha') . ' = :recaptcha',
					$db->quoteName('input_class') . ' = :input_class',
					$db->quoteName('submit_text') . ' = :submit_text',
					$db->quoteName('verify_otp_text') . ' = :verify_otp_text',
					$db->quoteName('submit_class') . ' = :submit_class',
					$db->quoteName('fileupload') . ' = :fileupload',
					$db->quoteName('upload_filelimit') . ' = :upload_filelimit',
					$db->quoteName('upload_filesize') . ' = :upload_filesize',
					$db->quoteName('allowed_filetypes') . ' = :allowed_filetypes',
					$db->quoteName('fileupload_label') . ' = :fileupload_label',
					$db->quoteName('fileupload_help') . ' = :fileupload_help',
					$db->quoteName('verify_email') . ' = :verify_email',
					$db->quoteName('suggest_docs') . ' = :suggest_docs',
					$db->quoteName('suggest_docs_cats') . ' = :suggest_docs_cats',
					$db->quoteName('params') . ' = :params',
				]
			)
			->where($db->quoteName('id') . ' = :id')
			->bind(':form_name', $form_name, ParameterType::STRING)
			->bind(':form_fields', $form_fields)
			->bind(':recaptcha', $recaptcha, ParameterType::INTEGER)
			->bind(':input_class', $input_class, ParameterType::STRING)
			->bind(':submit_text', $submit_text, ParameterType::STRING)
			->bind(':verify_otp_text', $verify_otp_text, ParameterType::STRING)
			->bind(':submit_class', $submit_class, ParameterType::STRING)
			->bind(':fileupload', $fileupload, ParameterType::INTEGER)
			->bind(':upload_filelimit', $upload_filelimit, ParameterType::INTEGER)
			->bind(':upload_filesize', $upload_filesize, ParameterType::INTEGER)
			->bind(':allowed_filetypes', $allowed_filetypes, ParameterType::STRING)
			->bind(':fileupload_label', $fileupload_label, ParameterType::STRING)
			->bind(':fileupload_help', $fileupload_help, ParameterType::STRING)
			->bind(':verify_email', $verify_email, ParameterType::INTEGER)
			->bind(':suggest_docs', $suggest_docs, ParameterType::INTEGER)
			->bind(':suggest_docs_cats', $suggest_docs_cats)
			->bind(':params', $params)
			->bind(':id', $id, ParameterType::INTEGER);

		$db->setQuery($query);

		try {
			$result = $db->execute();
			if ( isset( $result ) && $result !== false )
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORMS_EDIT_SUCCESS'), 'success');
			}
			else
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORMS_EDIT_ERROR'), 'error');
			}
		} catch (\RuntimeException $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
		return true;
    }
}
