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
use Joomla\CMS\Session\Session;
use Joomla\CMS\Filter\InputFilter;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class SettingsModel extends ListModel
{
    /**
     * Update Settings
     */
    public function update()
    {
        $db = $this->getDatabase();
		$filter = InputFilter::getInstance();
		$config = GuestsupportHelper::config();
		$app  = Factory::getApplication();
        $input = GuestsupportHelper::getInput();
		$settingstype = $input->post->getString('settingstype', 'settings');

		// Get all post data
		$data = filter_var_array( $_POST );

		// Unset unnecessary inputs
		unset(  
			$data['task'],
			$data['settingstype'],
			$data[Session::getFormToken()]
		);

		// Sanitize inputs
		foreach ( $data as $name => $value ) {
			$data[$name] = $value ? trim( $filter->clean( $value, 'STRING' ) ) : '';
		}

		if ( $settingstype == 'settings' ) {
			// new_ticket_confirmation_message Should be RAW
			$data['new_ticket_confirmation_message'] = $input->post->getRaw('new_ticket_confirmation_message', '');

			// Check pre ticket close and ticket close days
			$auto_close_tickets = $input->post->getInt('auto_close_tickets', 0);
			$pre_close_email = $input->post->getInt('pre_close_email', 0);
			if ( $auto_close_tickets && $pre_close_email )
			{
				if ( $pre_close_email >= $auto_close_tickets )
				{
					Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_SETTINGS_VERIFY_PRE_CLOSE_DAYS'), 'error');
					$redirectUrl = Route::_('index.php?option=com_guestsupport&view=settings', false);
					$app->redirect($redirectUrl);
					return false;
				}
			}
		}

		$error = 0;

		// Process edits
		foreach ($data as $name => $value) {
			if ( $config->$name != $value )
			{
				$query = $db->getQuery(true)
					->update($db->quoteName('#__gs_tickets_config'))
					->set(
						[
							$db->quoteName('value') . ' = :value'
						]
					)
					->where($db->quoteName('name') . ' = :name')
					->bind(':value', $value)
					->bind(':name', $name, ParameterType::STRING);

				$db->setQuery($query);

				try {
					$result = $db->execute();
					if ( !isset( $result ) || $result !== true )
					{
						$error++;
					}
				} catch (\RuntimeException $e) {
					// Do nothing
				}
			}
		}

		if ( $error )
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_SETTINGS_SAVE_ERROR'), 'error');
			return false;
		}
		else
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_SETTINGS_SAVE_SUCCESS'), 'success');
			return true;
		}
    }

	/**
     * Load xml form
     */
    public function getXmlform($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_guestsupport.settings', 'settings', array('control' => '', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

		$config = GuestsupportHelper::config();

		if ( isset( $config->new_ticket_confirmation_message ) )
		{
			$form->setValue( 'new_ticket_confirmation_message', '', $config->new_ticket_confirmation_message );
		}

		return $form;
    }
}
