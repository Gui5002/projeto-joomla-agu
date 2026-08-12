<?php

/**
 * @package     Bluecoder.TagFilter
 *
 * @copyright   Copyright © 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Module\SimpleTagFilter\Site\Dispatcher;

use Bluecoder\Module\SimpleTagFilter\Site\Helper\SimpleTagFilterHelper;
use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;
use Joomla\CMS\Helper\ModuleHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Dispatcher class for mod_tags_popular
 *
 * @since  5.1.0
 */
class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Runs the dispatcher.
     *
     * @return  void
     *
     * @since   5.1.0
     */
    public function dispatch()
    {
        $displayData = $this->getLayoutData();

        if (!\count($displayData['list'])) {
            return;
        }

        parent::dispatch();
    }

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   5.1.0
     */
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        $cacheparams = new \stdClass();
        /** @var SimpleTagFilterHelper $cacheparams::class */
        $cacheparams->class = $this->getHelperFactory()->getHelper('SimpleTagFilterHelper');
        $cacheparams->method = 'getTags';
        $cacheparams->methodparams = $data['params'];
        // If the cached are based on the url (safeuri) or on the id (id)
        $cacheparams->cachemode = 'safeuri';
        // These are used for the cache key
        $cacheparams->modeparams = ['id' => 'array', 'catid' => 'int', 'Itemid' => 'int'];
        $tagList = ModuleHelper::moduleCache($this->module, $data['params'], $cacheparams);

        /*
         * We set the active property to the items we fetched from cache
         * If we did that in the helper, it would be ignored
         * and if we add the 'filter_tag' in the cache key, the cache should be recreated after a selection change
         */
        if ($tagList) {
            $selectedTags = $this->input->get('filter_tag', [], 'array');
            $tagList = array_map(function ($tag) use ($selectedTags) {
                $tag->active = in_array($tag->tag_id, $selectedTags);
                return $tag;
            }, (array)$tagList);
        }
        $data['list'] = $tagList;
        $data['isPro'] = $cacheparams->class->isPro();
        $data['categoryId'] = $cacheparams->class->getCategoryId();
        $data['display'] = $data['params']->get('display', 'links');
        $data['context'] = $data['params']->get('context', 'com_content.category');
        $data['styling'] = $data['params']->get('styling', 'tags');
        $data['alignment'] = $data['params']->get('alignment', 'start');
        $data['display_count'] = $data['params']->get('display_count', 0);
        $data['useInPage'] = $cacheparams->class->useInPage($data['params']);

        return $data;
    }
}
