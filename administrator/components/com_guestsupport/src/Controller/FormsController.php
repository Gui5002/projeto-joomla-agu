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

class FormsController extends AdminController
{
	/**
	 * Redirect to editing page
	 */
	public function edit()
	{
		$app  = $this->app;
		$input = GuestsupportHelper::getInput();
		$ids = $input->post->get('forms_ids', array(), 'array');
		$editUrl = Route::_('index.php?option=com_guestsupport&view=form&layout=edit&id=' . (int) $ids[0], false);
		$this->setRedirect($editUrl);
		return true;
	}

	/**
	 * Delete Forms
	 */
	public function delete()
	{
		$listUrl = Route::_('index.php?option=com_guestsupport&view=forms', false);

		$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_DELETE_CORE_FORMS_ERROR'), 'error');
		$this->setRedirect($listUrl);
		return false;
	}
}
