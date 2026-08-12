<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;

\defined('_JEXEC') or die;

class EmailtemplatesModel extends ListModel
{
	/**
	 * Get list of templates
	 */
	protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select($db->quoteName(array('id', 'name', 'type', 'subject', 'template', 'lang', 'active')))
            ->from($db->quoteName('#__gs_tickets_email_templates'));

        return $query;
    }
}
