<?php
/**
 * @package     Bluecoder.TagFilter
 *
 * @copyright   Copyright © 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Module\SimpleTagFilter\Site\Field;

use Bluecoder\Module\SimpleTagFilter\Site\Helper\SimpleTagFilterHelper;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die();

/**
 * Trait SetupTrait
 * Used to provide additional functionality to the fields setup
 *
 */
trait SetupTrait
{

    /**
     * We override that function to provide custom attributes to the fields
     *
     * @param \SimpleXMLElement $element
     * @param mixed $value
     * @param null $group
     * @return bool
     * @since 1.0.0
     */
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $created = parent::setup($element, $value, $group);

        $attributeNameEdition = 'data-edition';
        if ($created && $element[$attributeNameEdition]) {
            $this->__set($attributeNameEdition, $element[$attributeNameEdition]);
        }

        /** @var SimpleTagFilterHelper $helper */
        $helper = Factory::getApplication()->bootModule('mod_simple_tag_filter', 'site')->getHelper('SimpleTagFilterHelper');

        if ($this->__get('data-edition') && !$helper->isPro() && (int)$this->__get('data-edition') == 100) {
            $this->__set('data-locked', true);
            $this->disabled = true;
        }
        return $created;
    }
}