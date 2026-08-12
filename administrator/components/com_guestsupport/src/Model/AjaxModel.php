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
use Joomla\String\StringHelper;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

\defined('_JEXEC') or die;

class AjaxModel extends ListModel
{
    /**
     * Get users by email
     */
    public function getUsersByEmail()
    {
        $db = $this->getDatabase();
        $app  = Factory::getApplication();
        $input = GuestsupportHelper::getInput();

        $email = trim($input->post->getString('searchterm', ''));

        if ( !$email )
        {
            return false;
        }

        $email = '%' . $db->escape( StringHelper::strtolower($email), true ) . '%';

        $query = $db->getQuery(true);
        $query
            ->select($db->quoteName(array('id', 'name', 'email')))
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('block') . ' = 0')
            ->where($db->quoteName('email') . ' LIKE :email')
            ->bind(':email', $email, ParameterType::STRING)
            ->setLimit('5');

        $db->setQuery($query);
        $results = $db->loadObjectList();

        return $results;
    }
}
