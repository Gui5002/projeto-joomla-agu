<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class EmailtemplateModel extends ListModel
{
	/**
	 * Get a single template
	 */
	public function getTemplate( $id = 0 )
    {
		if ( !$id )
        {
            $app = Factory::getApplication();
            $input = GuestsupportHelper::getInput();
            $id = $input->getInt('id', 0);
        }

		if ( !$id )
        {
			return false;
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select($db->quoteName(array('id', 'name', 'type', 'subject', 'template', 'lang', 'active')))
            ->from($db->quoteName('#__gs_tickets_email_templates'))
			->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

		$db->setQuery($query);
		$result = $db->loadObject();

        return $result;
    }

    /**
     * Load xml form
     */
    public function getXmlform($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_guestsupport.emailtemplate', 'emailtemplate', array('control' => '', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

		$template = $this->getTemplate();

		if ( $template !== false )
		{
			$form->setValue( 'template', '', $template->template );
		}

		return $form;
    }

	/**
     * Update email template
     */
    public function update($id)
    {
        if ( !$id )
        {
            return false;
        }
        $db = $this->getDatabase();
        $app  = Factory::getApplication();
        $input = GuestsupportHelper::getInput();

		$editUrl = Route::_('index.php?option=com_guestsupport&view=emailtemplate&layout=edit&id=' . (int) $id, false);

        $name = trim($input->post->getString('name', ''));
        $subject = trim($input->post->getString('subject', ''));
		$template = trim($input->post->getRaw('template', ''));

        if ( !$name )
        {
            Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATE_EMPTY_NAME'), 'error');
            $app->redirect($editUrl);
            return false;
        }
		if ( !$subject )
        {
            Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATE_EMPTY_SUBJECT'), 'error');
            $app->redirect($editUrl);
            return false;
        }
        if ( !$template )
        {
            Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_EDIT_EMPTY_TEMPLATE'), 'error');
            $app->redirect($editUrl);
            return false;
        }

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__gs_tickets_email_templates'))
            ->set(
                [
                    $db->quoteName('name') . ' = :name',
                    $db->quoteName('subject') . ' = :subject',
                    $db->quoteName('template') . ' = :template'
                ]
            )
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':name', $name, ParameterType::STRING)
            ->bind(':subject', $subject, ParameterType::STRING)
            ->bind(':template', $template)
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        try {
            $result = $db->execute();
            if ( isset( $result ) && $result !== false )
            {
                Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_UPDATE_SUCCESS'), 'success');
            }
            else
            {
                Factory::getApplication()->enqueueMessage(Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_UPDATE_ERROR'), 'error');
            }
        } catch (\RuntimeException $e) {
            $app->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
        return true;
    }
}
