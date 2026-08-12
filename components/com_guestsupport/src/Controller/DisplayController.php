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
use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


class DisplayController extends BaseController
{
    /**
     * Method to display a view.
     *
     * @param   boolean        $cachable   If true, the view output will be cached
     * @param   mixed|boolean  $urlparams  An array of safe URL parameters and their
     *                                     variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return  static  This object to support chaining.
     *
     * @since   3.1
     */
    public function display($cachable = false, $urlparams = false)
    {
        // Set the default view name and format from the Request.
        $input = GuestsupportHelper::getInput();
        $vName = $input->get('view', 'form');
        $input->set('view', $vName);

        if ($input->getMethod() === 'POST' || $vName === 'ticket') {
            $cachable = false;
        }

        $safeurlparams = array(
            'id'               => 'ARRAY',
            'auth'             => 'STRING',
            'agent'            => 'STRING',
            'type'             => 'ARRAY',
            'limit'            => 'UINT',
            'limitstart'       => 'UINT',
            'lang'             => 'CMD'
        );

		if ( $vName === 'ticket' )
		{
			$safeurlparams['id'] = 'STRING';
		}

		// Update user id
		$user = $this->app->getIdentity();
		$guest = $user->guest;

		if ( !$guest )
		{
			$db = GuestsupportHelper::getDatabase();
			$query = $db->getQuery(true);

			// Create the base update statement.
			$query->update($db->quoteName('#__gs_tickets'))
				->set($db->quoteName('user_id') . ' = :user_id')
				->where(
					[
						$db->quoteName('email') . ' = :email',
						$db->quoteName('user_id') . ' = ' . $db->quote(0)
					]
				)
				->bind(':user_id', $user->id, ParameterType::INTEGER)
				->bind(':email', $user->email, ParameterType::STRING);

			// Set the query and execute the update.
			$db->setQuery($query);

			try {
				$db->execute();
			} catch (\RuntimeException $e) {
				// Do nothing
			}
		}

        return parent::display($cachable, $safeurlparams);
    }
}
