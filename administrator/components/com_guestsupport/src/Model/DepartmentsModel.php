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
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

class DepartmentsModel extends ListModel
{
    /**
     * Get department data
     */
    public function getDepartment()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select($db->quoteName(array('id', 'name', 'agent_id', 'additional_emails', 'additional_emails_type', 'type')))
            ->from($db->quoteName('#__gs_tickets_departments'))
			->where($db->quoteName('type') . ' = ' . $db->quote('core'));

		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
    }
}
