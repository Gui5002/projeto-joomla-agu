<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Guestsupport\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class SettingsController extends AdminController
{
	/**
	 * Apply - Save and redirect to the edit page
	 */
	public function apply()
	{
		$this->checkToken();

		$input = GuestsupportHelper::getInput();
		$id = $input->post->getInt('form_id', 0);
		$settingstype = $input->post->getString('settingstype', 'settings');
		if ( $settingstype == 'settings' )
		{
			$editUrl = Route::_('index.php?option=com_guestsupport&view=settings', false);
		}
		elseif ( $settingstype == 'emailintegration' )
		{
			$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FEATURES'), 'error');
			$redirect_url = Route::_('index.php?option=com_guestsupport&view=emailintegration', false);
			$this->setRedirect($redirect_url);
			return false;
		}
		else
		{
			$editUrl = Route::_('index.php?option=com_guestsupport&view=emailsettings', false);
		}

		$this->process();
		$this->setRedirect($editUrl);
		return true;
	}

	/**
	 * Save & Close - Save and redirect to the listing page
	 */
	public function save()
	{
		$this->checkToken();
		$input = GuestsupportHelper::getInput();
		$id = $input->post->getInt('form_id', 0);
		$settingstype = $input->post->getString('settingstype', 'settings');
		if ( $settingstype == 'settings' )
		{
			$editUrl = Route::_('index.php?option=com_guestsupport&view=settings', false);
		}
		elseif ( $settingstype == 'emailintegration' )
		{
			$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FEATURES'), 'error');
			$redirect_url = Route::_('index.php?option=com_guestsupport&view=emailintegration', false);
			$this->setRedirect($redirect_url);
			return false;
		}
		else
		{
			$editUrl = Route::_('index.php?option=com_guestsupport&view=emailsettings', false);
		}
		$listUrl = Route::_('index.php?option=com_guestsupport&view=tickets', false);

		if ( $this->process() === true )
		{
			$this->setRedirect($listUrl);
			return true;
		}
		else
		{
			$this->setRedirect($editUrl);
			return false;
		}
	}

	/**
	 * Redirect to listing page on cancel
	 */
	public function cancel()
	{
		$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=tickets', false));
	}

	/**
	 * Process data
	 */
	private function process()
	{
		$input = GuestsupportHelper::getInput();
		$canEdit = $this->app->getIdentity()->authorise('core.edit', 'com_guestsupport');
		$settingstype = $input->post->getString('settingstype', 'settings');
		if ( $settingstype == 'settings' )
		{
			$editUrl = Route::_('index.php?option=com_guestsupport&view=settings', false);
		}
		else
		{
			$editUrl = Route::_('index.php?option=com_guestsupport&view=emailsettings', false);
		}

		// Check permission
		if ( !$canEdit )
		{
			$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_SETTINGS_EDIT_NOT_ALLOWED'), 'error');
			$this->setRedirect($editUrl);
			return false;
		}
		// Update and get result
		$result = $this->getModel()->update();

		// Update guide status
		if ( $settingstype == 'settings' )
		{
			GuestsupportHelper::updateGuide('emailsettings');
		}
		else
		{
			GuestsupportHelper::updateGuide('emailtemplates');
		}

		return $result;
	}
}
