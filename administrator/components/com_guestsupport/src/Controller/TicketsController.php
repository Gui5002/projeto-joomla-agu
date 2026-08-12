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

\defined('_JEXEC') or die;

class TicketsController extends AdminController
{
	/**
	 * Clear search on tickets listing page
	 */
	public function clearsearch()
	{
		// Clear filters
		$session = Factory::getApplication()->getSession();

		$session->set('com_guestsupport.b_filter_status', '');
		$session->set('com_guestsupport.b_filter_sortby', '');
		$session->set('com_guestsupport.b_filter_searchstring', '');
		$session->set('com_guestsupport.b_filter_department', 0);
		$session->set('com_guestsupport.b_filter_form', 0);

		// Redirect to tickets listing page
		$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=tickets', false));
	}

	/**
	 * Delete tickets
	 */
	public function delete()
	{
		$canDelete = $this->app->getIdentity()->authorise('core.delete', 'com_guestsupport');
		$listUrl = Route::_('index.php?option=com_guestsupport&view=tickets', false);

		// Check permission
		if ( !$canDelete )
		{
			$this->app->enqueueMessage(Text::_('COM_GUESTSUPPORT_DELETE_NOT_ALLOWED'), 'error');
			$this->setRedirect($listUrl);
			return false;
		}
		// Update and get result
		$this->getModel()->delete();
		$this->setRedirect($listUrl);
	}

	/**
	 * Redirect to new ticket creation form
	 */
	public function create()
	{
		$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=createticket&layout=edit', false));
	}

	/**
	 * Hide ask for review notice
	 */
	public function hidereviewnotice()
	{
		$canEdit = $this->app->getIdentity()->authorise('core.edit', 'com_guestsupport');
		$listUrl = Route::_('index.php?option=com_guestsupport&view=tickets', false);

		// Check permission
		if ( !$canEdit )
		{
			$this->app->enqueueMessage("Access denied.", 'error');
			$this->setRedirect($listUrl);
			return false;
		}

		// Update and get result
		$this->getModel()->hideReviewNoticeQuery();
		$this->setRedirect($listUrl);
		return true;
	}
}
