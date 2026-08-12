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
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Version;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Guestsupport master display controller.
 *
 * @since  1.6
 */
class DisplayController extends BaseController
{
    /**
     * The default view.
     *
     * @var    string
     * @since  1.6
     */
    protected $default_view = 'tickets';

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return  BaseController|boolean  This object to support chaining.
     *
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = array())
    {
		$input = GuestsupportHelper::getInput();
        $view   = $input->get('view', 'tickets');
        $layout = $input->get('layout', 'default');
		$guide = GuestsupportHelper::config()->guide;
		$session = Factory::getApplication()->getSession();

		// Add styles and scripts
		if ( $view !== 'ajax' )
		{
			$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

			if ( (int) Version::MAJOR_VERSION < 5 )
			{
				$wa->useStyle('com_guestsupport.admin-styles-j4');
			}
			else
			{
				$wa->useStyle('com_guestsupport.admin-styles');
			}

			$wa->useScript('com_guestsupport.admin-scripts');

			if ( $view === 'form' )
			{
				$wa->useScript('com_guestsupport.admin-dragndrop');
				$wa->useScript('com_guestsupport.admin-addfield');
			}
		}

		if ( $view !== 'ajax' && $guide !== 'done' )
		{
			if ( !$guide && $view !== 'departments' && $view !== 'department' )
			{
				// Just landed, guide to Departments page
				$session->set('com_guestsupport.showguide', true);
				$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=departments', false));

				return false;
			}
			elseif ( $guide === 'forms' && $view !== 'forms' && $view !== 'form' )
			{
				// Guide to Forms page
				$session->set('com_guestsupport.showguide', true);
				$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=forms', false));

				return false;
			}
			elseif ( $guide === 'settings' && $view !== 'settings' )
			{
				// Guide to Settings page
				$session->set('com_guestsupport.showguide', true);
				$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=settings', false));

				return false;
			}
			elseif ( $guide === 'emailsettings' && $view !== 'emailsettings' )
			{
				// Guide to Email Settings page
				$session->set('com_guestsupport.showguide', true);
				$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=emailsettings', false));

				return false;
			}
			elseif ( $guide === 'emailtemplates' && $view !== 'emailtemplates' )
			{
				// Guide to Email Templates page
				$session->set('com_guestsupport.showguide', true);
				$this->setRedirect(Route::_('index.php?option=com_guestsupport&view=emailtemplates', false));

				return false;
			}
		}

        return parent::display();
    }
}
