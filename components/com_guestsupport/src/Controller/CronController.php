<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class CronController extends BaseController
{
	/**
	 * Send pre-close ticket email
	 * Close tickets automatically
	 */
	public function automation()
	{
		echo "The pro version is required to use this feature.";
		exit;
	}

	/**
	 * Email Ticket Handler
	 */
	public function emailtickethandler()
	{
		echo "The pro version is required to use this feature.";
		exit;
	}
}
