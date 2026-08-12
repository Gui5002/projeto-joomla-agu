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

class FormController extends AdminController
{
	/**
	 * Apply - Save and redirect to the edit page
	 */
	public function apply()
	{
		$this->checkToken();

		$input = GuestsupportHelper::getInput();
		$id = $input->post->getInt('form_id', 0);
		$editUrl = Route::_('index.php?option=com_guestsupport&view=form&layout=edit&id=' . (int) $id, false);

		if ( $id )
		{
			// Edit
			$this->process();
		}
		else
		{
			$editUrl = Route::_('index.php?option=com_guestsupport&view=forms', false);
		}

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
		$listUrl = Route::_('index.php?option=com_guestsupport&view=forms', false);

		if ( $id )
		{
			// Edit
			$editUrl = Route::_('index.php?option=com_guestsupport&view=form&layout=edit&id=' . (int) $id, false);
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

		// Redirect to listing page if already didn't
		$this->setRedirect($listUrl);
		return true;
	}

	/**
	 * Redirect to listing page on cancel
	 */
	public function cancel()
	{
		$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=forms', false));
	}

	/**
	 * Process data
	 */
	private function process()
	{
		$input = GuestsupportHelper::getInput();
		$id = $input->post->getInt('form_id', 0);
		$form_name = trim($input->post->getString('form_name', ''));
		$canEdit = $this->app->getIdentity()->authorise('core.edit', 'com_guestsupport');
		$listUrl = Route::_('index.php?option=com_guestsupport&view=forms', false);
		$editUrl = Route::_('index.php?option=com_guestsupport&view=form&layout=edit&id=' . (int) $id, false);

		if ( !$id )
		{
			$this->setRedirect($listUrl);
			return false;
		}

		// Check name first
		if ( !$form_name )
		{
			$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORMS_EDIT_NO_NAME'), 'error');
			$this->setRedirect($listUrl);
			return false;
		}

		/**
		 * Edit form
		 */

		// Check permission
		if ( !$canEdit )
		{
			$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORMS_EDIT_NOT_ALLOWED'), 'error');
			$this->setRedirect($listUrl);
			return false;
		}

		// Check if form exist
		elseif ( empty( $this->getModel()->getItem( (int) $id ) ) )
		{
			$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORMS_EDIT_NOT_FOUND'), 'error');
			$this->setRedirect($listUrl);
			return false;
		}

		// Check if another department with same name already exist
		elseif ( (int) $this->getModel()->getFormsByName( $form_name, $id ) > 0 )
		{
			$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORMS_EDIT_NAME_EXIST'), 'error');
			return false;
		}

		// Update and get result
		elseif ( $this->getModel()->save($id) === true )
		{
			// Update guide status
			GuestsupportHelper::updateGuide('settings');
			return true;
		}
		else
		{
			return false;
		}
	}
}
