<?php
/**
 * @package     Bluecoder.TagFilter
 *
 * @copyright   Copyright © 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Module\SimpleTagFilter\Site\Field;

\defined('_JEXEC') or die();

use Bluecoder\Module\SimpleTagFilter\Site\Helper\SimpleTagFilterHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;

class StflistField extends ListField
{

    use SetupTrait;
    use RenderFieldTrait;

    /**
     * Method to get the field options with custom attributes.
     *
     * @return array
     * @throws \ReflectionException
     * @since 1.0.0
     */
    public function getOptions()
    {
        $options = parent::getOptions();

        foreach ($this->element->xpath('option') as $key => $optionArray) {
            $options[$key]->edition = $optionArray['edition']  ? (string) $optionArray['edition'] : 0;
        }
        $this->adjustProOptions($options);
        return $options;
    }

    /**
     * Disable the PRO options in FREE edition and add the 'PRO' label besides each PRO option.
     *
     * @param $options
     *
     * @return $this
     * @since 1.0.0
     */
    protected function adjustProOptions($options): StflistField
    {
        /** @var SimpleTagFilterHelper $helper */
        $helper = Factory::getApplication()->bootModule('mod_simple_tag_filter', 'site')->getHelper('SimpleTagFilterHelper');

        foreach ($options as &$option) {
            // Disable options in no Pro edition.
            if ($helper && !$helper->isPro() && isset($option->edition) && (int)$option->edition == 100) {
                $option->disable = true;
                $option->text .= ' [PRO]';
            }
        }

        return $this;
    }
}
