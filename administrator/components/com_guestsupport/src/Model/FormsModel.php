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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

class FormsModel extends ListModel
{
	/**
	 * Get form
	 */
	public function getForm()
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select($db->quoteName(['a.id', 'a.form_name']))
			->select(
				[
                    'COUNT(' . $db->quoteName('b.id') . ') AS ' . $db->quoteName('total_tickets'),
					'COUNT(CASE WHEN ' . $db->quoteName('b.status') . ' = ' . $db->quote('open') . ' THEN 1 END) AS ' . $db->quoteName('open_tickets')
                ]
			)
			->from($db->quoteName('#__gs_tickets_forms', 'a'))
			->join(
				'LEFT', 
				$db->quoteName('#__gs_tickets', 'b'), 
				$db->quoteName('b.form_id') . ' = ' . $db->quoteName('a.id') . ' AND ' . 
				$db->quoteName('b.message_type') . ' = ' . $db->quote('ticket') . ' AND ' . 
				$db->quoteName('b.published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('form_type') . ' = ' . $db->quote('core'))
			->group($db->quoteName('a.id'));

		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}
}
