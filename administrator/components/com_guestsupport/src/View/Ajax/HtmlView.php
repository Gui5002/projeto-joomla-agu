<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\View\Ajax;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    public function display($tpl = null): void
    {
        $app  = Factory::getApplication();
        $input = GuestsupportHelper::getInput();
        $request = trim($input->post->getString('request', ''));
		$model = $this->getModel();
        $returnData = '';

        if ( !$request )
        {
            echo json_encode($returnData);
            exit;
        }
        elseif ( $request == 'usersbyemail' )
        {
            /**
             * Search and get users by email address 
             */
            $users = $model->getUsersByEmail();
            if ( empty( $users ) )
            {
                $returnData = '<div class="item message">' . Text::_('COM_GUESTSUPPORT_DEPARTMENTS_NO_USER_FOUND') . '</div>';
            }
            else
            {
                foreach ($users as $user) {
                    $returnData .= '<div class="item" data-value="' . $this->escape($user->id) . '">' . $this->escape($user->name) . ' (' . $this->escape($user->email) . ')</div>';
                }
            }
        }

        // parent::display($tpl);
        // Return result
        echo json_encode($returnData);
        exit;
    }
}
