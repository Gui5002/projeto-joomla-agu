<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\View\Emailintegration;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    protected $guide;

    public function display($tpl = null): void
    {
		// Check permission
		$user  = Factory::getApplication()->getIdentity();
		$canEdit = $user->authorise('core.edit', 'com_guestsupport');

		if ( !$canEdit )
		{
			$app  = Factory::getApplication();
            $app->enqueueMessage(Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_EDIT_NOT_ALLOWED'), 'error');
            $app->redirect(Route::_('index.php?option=com_guestsupport&view=emailintegration', false));
		}

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolbar(): void
    {
        $input = GuestsupportHelper::getInput();
        $input->set('hidemainmenu', true);

        ToolbarHelper::title(Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_TITLE'), 'bookmark guestsupport');

        ToolbarHelper::apply('settings.apply');
        ToolbarHelper::save('settings.save');
        ToolbarHelper::cancel('settings.cancel');

        // Get the toolbar object instance - compatible with both Joomla 4 and 5
        $joomlaVersion = new \Joomla\CMS\Version();
        
        if (version_compare($joomlaVersion->getShortVersion(), '5.0', 'ge')) {
            // Joomla 5+ method
            $toolbar = Factory::getApplication()->getDocument()->getToolbar();
        } else {
            // Joomla 4 method
            $toolbar = Toolbar::getInstance('toolbar');
        }
		$toolbar->link('Documentation', 'https://www.rcatheme.com/docs/joomla-extensions/guest-support-for-joomla/imap-email-integration-settings');
    }
}
