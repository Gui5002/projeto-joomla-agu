<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\View\Form;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Guestsupport\Administrator\Model\FormModel;
use Joomla\CMS\Router\Route;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
	protected $item;
	protected $xmlfields;
	protected $params;
	protected $categories;

    public function display($tpl = null): void
    {
		// Check permission
		$app  = Factory::getApplication();
		$user  = Factory::getApplication()->getIdentity();
		$canEdit = $user->authorise('core.edit', 'com_guestsupport');

		if ( !$canEdit )
		{
            $app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORMS_EDIT_NOT_ALLOWED'), 'error');
            $app->redirect(Route::_('index.php?option=com_guestsupport&view=forms', false));
		}

		$model              = $this->getModel();
        $this->item         = $model->getItem();
        $this->categories	= $model->getCategories();
		$this->xmlfields	= $model->getXmlform();

		if ( empty( $this->item ) )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FORM_NOT_FOUND'), 'error');
            $app->redirect(Route::_('index.php?option=com_guestsupport&view=forms', false));
		}

		if ( !empty( $this->item->params ) )
		{
			$this->params = unserialize( $this->item->params );
		}
		else
		{
			$this->params = [
				'top_text' => '',
				'display_ticket_id' => 'short',
				'display_ticket_status' => 1,
				'display_email' => 1,
				'display_form_name' => 'agents',
				'display_department' => 1,
				'display_ticket_creation_date' => 1,
				'ticket_creation_date_format' => 'M d, Y h:iA',
				'display_last_updated_date' => 1,
				'last_updated_date_format' => 'M d, Y h:iA',
				'display_custom_fields' => 1,
				'display_other_tickets' => 'both',
				'noof_other_tickets' => 10,
				'bottom_text' => ''
			];
		}
		$this->item->form_fields = unserialize( $this->item->form_fields );

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

        ToolbarHelper::title($isNew ? Text::_('COM_GUESTSUPPORT_FORMS_NEW') : Text::_('COM_GUESTSUPPORT_FORMS_EDIT') . ' - ' . $this->item->form_name, 'bookmark guestsupport');

        ToolbarHelper::apply('form.apply');
        ToolbarHelper::save('form.save');
        ToolbarHelper::cancel('form.cancel');
    }
}
