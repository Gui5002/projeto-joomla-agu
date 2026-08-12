<?php
/**
 * @package     Bluecoder.TagFilter
 *
 * @copyright   Copyright © 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Module\SimpleTagFilter\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper for mod_tag_filter
 *
 * @since  1.0.0
 */
class SimpleTagFilterHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * @var int|null
     * @since 1.0.0
     */
    protected ?int $categoryId =  null;

    /**
     * @var bool|null
     * @since 1.0.0
     */
    private ?bool $isPro = null;

    /**
     * @var bool|null
     * @since 1.0.0
     */
    protected ?bool $useInPage = null;

    /**
     * Get a list of tags
     *
     * @param Registry  &$params module parameters
     *
     * @return  mixed
     *
     * @throws \Exception
     * @since   1.0.0
     */
    public function getTags(&$params)
    {
        $app = Factory::getApplication();
        $categoryId = $this->getCategoryId();
        $context = $params->get('context', 'com_content.category');

        // No category, no tags in the category blog layout.
        if (!$this->useInPage($params) || ($context === 'com_content.category' && !$categoryId)) {
            return [];
        }

        $db = $this->getDatabase();
        $user = $app->getIdentity();
        $groups = $user->getAuthorisedViewLevels();
        $timeframe = $params->get('timeframe', 'alltime');
        $maximum = (int)$params->get('maximum', 5);
        $order_value = $params->get('order_value', 'title');
        $order_value =  $this->isPro() ? $order_value : 'title';
        $nowDate = Factory::getDate()->toSql();
        $nullDate = $db->getNullDate();

        $query = $db->getQuery(true)
            ->select(
                [
                    'MAX(' . $db->quoteName('tag_id') . ') AS ' . $db->quoteName('tag_id'),
                    'COUNT(*) AS ' . $db->quoteName('count'),
                    'MAX(' . $db->quoteName('t.title') . ') AS ' . $db->quoteName('title'),
                    'MAX(' . $db->quoteName('t.access') . ') AS ' . $db->quoteName('access'),
                    'MAX(' . $db->quoteName('t.alias') . ') AS ' . $db->quoteName('alias'),
                    'MAX(' . $db->quoteName('t.params') . ') AS ' . $db->quoteName('params'),
                    'MAX(' . $db->quoteName('t.language') . ') AS ' . $db->quoteName('language'),
                ]
            )
            ->group($db->quoteName(['tag_id', 't.title', 't.access', 't.alias']))
            ->from($db->quoteName('#__contentitem_tag_map', 'm'))
            ->whereIn($db->quoteName('t.access'), $groups);

        // Only return published tags
        $query->where($db->quoteName('t.published') . ' = 1 ');

        // Filter by Parent Tag
        $parentTags = $params->get('parentTag', []);

        if ($parentTags) {
            $query->whereIn($db->quoteName('t.parent_id'), $parentTags);
        }

        if ($context === 'com_content.category') {
            // Filter on category state
            $query->join(
                'INNER',
                $db->quoteName('#__ucm_content', 'ucm'),
                $db->quoteName('m.content_item_id') . ' = ' . $db->quoteName('ucm.core_content_item_id') .
                ' AND ' . $db->quoteName('m.type_id') . ' = ' . $db->quoteName('ucm.core_type_id')
            );

            $query->join(
                'INNER',
                $db->quoteName('#__categories', 'cat'),
                $db->quoteName('ucm.core_catid') . ' = ' . $db->quoteName('cat.id')
            );

            $query->where($db->quoteName('cat.published') . ' > 0');
            $query->where($db->quoteName('cat.id') . '= :categoryId')
                ->bind(':categoryId', $categoryId, ParameterType::INTEGER);;
        }

        elseif ($context === 'com_ochsubscriptions.products') {
            // Filter by type
            $value = 'com_ochsubscriptions.product';
            $query->where($db->quoteName('m.type_alias') . '= :type_alias')
                ->bind(':type_alias', $value);
        }

        if (Multilanguage::isEnabled()) {
            $language = ContentHelper::getCurrentLanguage();
            $query->whereIn($db->quoteName('t.language'), [$language, '*'], ParameterType::STRING);
        }

        if ($timeframe !== 'alltime') {
            $query->where(
                $db->quoteName('tag_date') . ' > ' . $query->dateAdd($db->quote($nowDate), '-1', strtoupper($timeframe))
            );
        }

        $query->join('INNER', $db->quoteName('#__tags', 't'), $db->quoteName('tag_id') . ' = ' . $db->quoteName('t.id'))
            ->join(
                'INNER',
                $db->quoteName('#__ucm_content', 'c'),
                $db->quoteName('m.core_content_id') . ' = ' . $db->quoteName('c.core_content_id')
            );

        $query->where($db->quoteName('m.type_alias') . ' = ' . $db->quoteName('c.core_type_alias'));

        // Only return tags connected to published and authorised items
        $query->where($db->quoteName('c.core_state') . ' = 1')
            ->where(
                '(' . $db->quoteName('c.core_access') . ' IN (' . implode(',', $query->bindArray($groups)) . ')'
                . ' OR ' . $db->quoteName('c.core_access') . ' = 0)'
            )
            ->where(
                '(' . $db->quoteName('c.core_publish_up') . ' IS NULL'
                . ' OR ' . $db->quoteName('c.core_publish_up') . ' = :nullDate2'
                . ' OR ' . $db->quoteName('c.core_publish_up') . ' <= :nowDate2)'
            )
            ->where(
                '(' . $db->quoteName('c.core_publish_down') . ' IS NULL'
                . ' OR ' . $db->quoteName('c.core_publish_down') . ' = :nullDate3'
                . ' OR ' . $db->quoteName('c.core_publish_down') . ' >= :nowDate3)'
            )
            ->bind([':nullDate2', ':nullDate3'], $nullDate)
            ->bind([':nowDate2', ':nowDate3'], $nowDate);

        // Set query depending on order_value param
        if ($order_value === 'rand()') {
            $query->order($query->rand());
        } else {
            $order_direction = $params->get('order_direction', 1) ? 'DESC' : 'ASC';

            if ($params->get('order_value', 'title') === 'title') {
                // Backup bound parameters array of the original query
                $bounded = $query->getBounded();

                if ($maximum > 0) {
                    $query->setLimit($maximum);
                }

                $query->order($db->quoteName('count') . ' DESC');
                $equery = $db->getQuery(true)
                    ->select(
                        $db->quoteName(
                            [
                                'a.tag_id',
                                'a.count',
                                'a.title',
                                'a.access',
                                'a.alias',
                                'a.language',
                            ]
                        )
                    )
                    ->from('(' . (string)$query . ') AS ' . $db->quoteName('a'))
                    ->order($db->quoteName('a.title') . ' ' . $order_direction);

                $query = $equery;

                // Rebind parameters
                foreach ($bounded as $key => $obj) {
                    $query->bind($key, $obj->value, $obj->dataType);
                }
            } elseif ($order_value === 'order') {
                $query->order([$db->quoteName('t.lft')  . ' ' . $order_direction, $db->quoteName('t.rgt') . ' ' . $order_direction]);
            } else {
                $query->order($db->quoteName($order_value) . ' ' . $order_direction);
            }
        }

        if ($maximum > 0) {
            $query->setLimit($maximum);
        }
        $q = (string)$query;
            $db->setQuery($query);

        try {
            $results = $db->loadObjectList();
        } catch (\RuntimeException $e) {
            $results = [];
            $app->enqueueMessage($e->getMessage(), 'error');
        }

        return $results;
    }

    public function useInPage(Registry $params)
    {
        if ($this->useInPage === null) {
            $context = $params->get('context', 'com_content.category');
            $contextParts = explode('.', $context);
            $input = Factory::getApplication()->getInput();
            $option = $input->get('option', '', 'cmd');
            $view = $input->get('view', '', 'cmd');
            $this->useInPage = $contextParts[0] === $option && $contextParts[1] === $view;
        }
        return $this->useInPage;
    }

    public function getCategoryId(): int
    {
        if (!isset($this->categoryId) || !is_numeric($this->categoryId)) {
            $input = Factory::getApplication()->getInput();
            $option = $input->get('option', '', 'cmd');
            $view = $input->get('view', '', 'cmd');
            $categoryId = $option == 'com_content' ? ($view == 'category' ? $input->getInt('id', 0) : $input->getInt(
                'catid',
                0
            )) : 0;
            $this->categoryId = (int)$categoryId;
        }
        return $this->categoryId;
    }

    public function isPro() : bool
    {
        if ($this->isPro === null) {
            $this->isPro = false;
            $evnVars = @include __DIR__ . '/../../env.php';

            if (is_array($evnVars) && isset($evnVars['version']) && isset($evnVars['md5Hash'])) {
                $proVersionMd5Hash = md5($evnVars['version'] . 'PRO');
                $currentVersionHash = trim($evnVars['md5Hash'], " -");

                /*
                 * Do not use the entire HASH placeholder or will be replaced during build.
                 */
                if ($proVersionMd5Hash === $currentVersionHash || str_starts_with($evnVars['md5Hash'], '##HASH#')) {
                    $this->isPro = true;
                }
            }
        }

        return $this->isPro;
    }
}
