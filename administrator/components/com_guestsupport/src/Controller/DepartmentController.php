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

class DepartmentController extends AdminController
{
	/**
	 * Apply - Save and redirect to the edit page
	 */
	public function apply()
	{
		$this->checkToken();

		$input = GuestsupportHelper::getInput();
		$id = $input->post->getInt('dept_id', 0);
		$editUrl = Route::_('index.php?option=com_guestsupport&view=department&layout=edit&id=' . (int) $id, false);

		if ( $id )
		{
			// Edit
			$this->process();
			$this->setRedirect($editUrl);
			return true;
		}

		return true;
	}

	/**
	 * Save & Close - Save and redirect to the listing page
	 */
	public function save()
	{
		$this->checkToken();

		$input = GuestsupportHelper::getInput();
		$id = $input->post->getInt('dept_id', 0);
		$editUrl = Route::_('index.php?option=com_guestsupport&view=department&layout=edit&id=' . (int) $id, false);
		$listUrl = Route::_('index.php?option=com_guestsupport&view=departments', false);

		if ( $id )
		{
			// Edit
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
	}

	/**
	 * Redirect to listing page on cancel
	 */
	public function cancel()
	{
		$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=departments', false));
	}

	/**
	 * Redirect to new department form
	 */
	public function add()
	{
		$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=department&layout=edit', false));
	}

	/**
	 * Process edit data
	 */
	private function process()
	{
		$input = GuestsupportHelper::getInput();
		$id = $input->post->getInt('dept_id', 0);
		$name = $input->post->getString('name', '');
		$formIds = $input->get('form_ids', [], 'array');
		$canEdit = $this->app->getIdentity()->authorise('core.edit', 'com_guestsupport');
		$listUrl = Route::_('index.php?option=com_guestsupport&view=departments', false);
		$editUrl = Route::_('index.php?option=com_guestsupport&view=department&layout=edit&id=' . (int) $id, false);

		if ( $id )
		{
			/**
			 * Edit department
			 */

			// Check permission
			if ( !$canEdit )
			{
				$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_EDIT_NOT_ALLOWED'), 'error');
				// $this->setRedirect($listUrl);
				return false;
			}

			// Verify applicable forms
			elseif ( empty( $formIds ) )
			{
				$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_EDIT_SELECT_APPLICABLE_FORMS'), 'error');
				return false;
			}

			// Check if department exist
			elseif ( empty( $this->getModel()->getItem( (int) $id ) ) )
			{
				$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_EDIT_NOT_FOUND'), 'error');
				// $this->setRedirect($listUrl);
				return false;
			}

			// Check if another department with same name already exist
			elseif ( $name && !empty( $this->getModel()->getItem( (int) $id, $name ) ) )
			{
				$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_EDIT_NAME_EXIST'), 'error');
				// $this->setRedirect($editUrl);
				return false;
			}

			// Update and get result
			elseif ( $this->getModel()->update($id) === true )
			{
				// Update guide status
				GuestsupportHelper::updateGuide('forms');
				return true;
			}
		}

		return true;
	}
}
