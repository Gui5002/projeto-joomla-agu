<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2021-2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Component\Jfilters\Administrator\Table;

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Model\FilterModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

class FilterTable extends Table
{
    /**
     * Class constructor.
     *
     * @param DatabaseDriver $db DatabaseDriver object.
     *
     * @since   1.0.0
     */
    public function __construct($db = null)
    {
        parent::__construct('#__jfilters_filters', 'id', $db);

        $this->setColumnAlias('published', 'state');
    }

    /**
     * The columns we want to fetch from the main table
     *
     * @return array
     * @var array The columns that should be excluded
     * @since 1.0.0
     */
    public function getMainTableFields($excludedFields = [])
    {
        $fields = array_keys($this->getFields());
        foreach ($excludedFields as $excludedField) {
            $position = array_search($excludedField, $fields);
            if ($position !== false) {
                unset($fields[$position]);
            }
        }
        array_walk($fields, array($this, 'addPrefix'));
        return $fields;
    }

    /**
     * Adds a prefix to a string e.g. database table name
     *
     * @param $value
     * @param string $prefix
     * @return string
     * @since 1.0.0
     */
    protected function addPrefix(&$value, $key, $prefix = 'main.')
    {
        if (strpos($value, '.') !== false) {
            return $value;
        }
        $value = $prefix . $value;
        return $value;
    }

    /**
     * Overloaded bind function
     *
     * @param array|object $data
     * @param string $ignore
     * @return bool
     * @since 1.0.0
     */
    public function bind($data, $ignore = '')
    {
        //convert the attributes to json string
        if (is_array($data) && isset($data['attribs'])) {
            $newAttributes = new Registry($data['attribs']);
            $data['attribs'] = $newAttributes->toString();
        }

        /*
         * Clear cache on settings change
         * This regards settings that affect the shown options
         */
        if ($this->getId() > 0 && !empty($newAttributes) && $this->attribs) {
            $existingAttributes =  new Registry($this->attribs);
            if ($this->root &&
                (
                    $existingAttributes->get('root_option', '') != $newAttributes->get('root_option', '')  ||
                    $existingAttributes->get('parent_node_linkable', '') != $newAttributes->get('parent_node_linkable', '') ||
                    $existingAttributes->get('show_sub_node_contents_on_parent', '') != $newAttributes->get('show_sub_node_contents_on_parent', '')
                )
            ) {
                /** @var FilterModel $filterModel */
                $filterModel = Factory::getApplication()->bootComponent('com_jfilters')->getMVCFactory()->createModel('Filter', 'Administrator', ['ignore_request' => true]);
                $filterModel->clearCache($this->getId());
            }
        }
        return parent::bind($data, $ignore);
    }
}
