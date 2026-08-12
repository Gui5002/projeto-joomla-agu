<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\View\Emailtemplate;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Guestsupport\Administrator\Model\EmailtemplateModel;
use Joomla\CMS\Router\Route;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
	protected $item;
	protected $form;

    public function display($tpl = null): void
    {
		// Check permission
		$user  = Factory::getApplication()->getIdentity();
		$canEdit = $user->authorise('core.edit', 'com_guestsupport');
		$app  = Factory::getApplication();

		if ( !$canEdit )
		{
            $app->enqueueMessage(Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_EDIT_NOT_ALLOWED'), 'error');
            $app->redirect(Route::_('index.php?option=com_guestsupport&view=emailtemplates', false));
		}

		/** @var BannerModel $model */
        $model       = $this->getModel();
        $this->form  = $model->getXmlform();
		$this->item  = $model->getTemplate();

        if ( empty( $this->item ) )
        {
            $app->enqueueMessage(Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_NOT_FOUND'), 'error');
            $app->redirect(Route::_('index.php?option=com_guestsupport&view=emailtemplates', false));
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

        $isNew      = ($this->item->id == 0);

        ToolbarHelper::title($isNew ? Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_ADD_NEW') : Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_EDIT'), 'bookmark guestsupport');

        ToolbarHelper::apply('emailtemplate.apply');
        ToolbarHelper::save('emailtemplate.save');
        ToolbarHelper::cancel('emailtemplate.cancel');
    }
}
