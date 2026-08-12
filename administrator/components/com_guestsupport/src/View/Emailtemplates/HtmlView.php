<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\View\Emailtemplates;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Guestsupport\Administrator\Model\EmailtemplatesModel;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
	protected $items;
    protected $pagination;
    protected $guide;

    public function display($tpl = null): void
    {
        $model               = $this->getModel();

        try {
            $this->items         = $model->getItems();
            $this->pagination    = $model->getPagination();

            $session = Factory::getApplication()->getSession();

            // Get guide status from session
            $this->guide = $session->get('com_guestsupport.showguide', '');
            // Empty guide status data on session
            $session->set('com_guestsupport.showguide', '');

            // Complete the guide
            GuestsupportHelper::updateGuide('done');
        } catch (\Exception $e) {
            throw new GenericDataException($e->getMessage(), 500, $e);
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
        ToolbarHelper::title(Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_TITLE'), 'bookmark guestsupport');

		// Check permission and show edit button
		$user  = Factory::getApplication()->getIdentity();
		$canEdit = $user->authorise('core.edit', 'com_guestsupport');

        // Get the toolbar object instance - compatible with both Joomla 4 and 5
        $joomlaVersion = new \Joomla\CMS\Version();
        
        if (version_compare($joomlaVersion->getShortVersion(), '5.0', 'ge')) {
            // Joomla 5+ method
            $toolbar = Factory::getApplication()->getDocument()->getToolbar();
        } else {
            // Joomla 4 method
            $toolbar = Toolbar::getInstance('toolbar');
        }

		if ( $canEdit )
		{
			$toolbar->edit('emailtemplate.edit')
                    ->text('JTOOLBAR_EDIT')
                    ->listCheck(true);
		}
    }
}
