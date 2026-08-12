<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\View\Departments;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Guestsupport\Administrator\Model\DepartmentsModel;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
	protected $department;
    protected $guide;

    public function display($tpl = null): void
    {
        /** @var DepartmentsModel $model */
        $model = $this->getModel();

        try {
            $this->department = $model->getDepartment();

            $session = Factory::getApplication()->getSession();

            // Get guide status from session
            $this->guide = $session->get('com_guestsupport.showguide', '');
            // Empty guide status data on session
            $session->set('com_guestsupport.showguide', '');

        } catch (\Exception $e) {
            throw new GenericDataException($e->getMessage(), 500, $e);
        }

        // Add bootstrap modal script
        HTMLHelper::_('bootstrap.modal', '.selector', []);

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
        $canDo = ContentHelper::getActions('com_guestsupport');

        ToolbarHelper::title(Text::_('COM_GUESTSUPPORT_DEPARTMENTS_TITLE'), 'bookmark guestsupport');

        // Get the toolbar object instance - compatible with both Joomla 4 and 5
        $joomlaVersion = new \Joomla\CMS\Version();
        
        if (version_compare($joomlaVersion->getShortVersion(), '5.0', 'ge')) {
            // Joomla 5+ method
            $toolbar = Factory::getApplication()->getDocument()->getToolbar();
        } else {
            // Joomla 4 method
            $toolbar = Toolbar::getInstance('toolbar');
        }

        if ($canDo->get('core.create')) {
            ToolbarHelper::modal('wt_pro_info_modal', 'icon-new', 'New');
        }

		if ($canDo->get('core.edit.state'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fa fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);
			$childBar = $dropdown->getChildToolbar();
			if ($canDo->get('core.edit'))
            {
                $childBar->edit('departments.edit')
                    ->text('JTOOLBAR_EDIT')
                    ->listCheck(true);
            }
            if ($canDo->get('core.delete'))
            {
                $childBar->delete('departments.delete')
                    ->text('JTOOLBAR_DELETE')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }
		}
    }
}
