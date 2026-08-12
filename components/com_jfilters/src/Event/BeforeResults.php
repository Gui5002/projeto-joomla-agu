<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2021-2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Component\Jfilters\Site\Event;

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Model\Filter\Collection;
use Joomla\CMS\Event\AbstractImmutableEvent;

/**
 * Results Model event, to be dispatched before the query execution
 *
 * Can you be used to alter the database query, e.g, add additional where clauses
 *
 * @since 1.18.0
 */
class BeforeResults extends AbstractImmutableEvent
{
    public function __construct($name, array $arguments = [])
    {

        parent::__construct($name, $arguments);

        if (!\array_key_exists('filters', $this->arguments) || !$this->arguments['filters'] instanceof Collection) {
            throw new \BadMethodCallException("Argument 'query' of event {$name} is required but has not been provided or is of wrong type.");
        }

        if (!\array_key_exists('context', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'context' of event {$name} is required but has not been provided.");
        }
    }

    /**
     * Called by the parent `setArgument($name, $value)` function, when we set an argument
     * Can be used for pre-processing (e.g.sanitizing) the passed $value
     *
     * @param Collection $filters
     * @return Collection
     * @since 1.18.0
     */
    public function onSetFilters(Collection $filters): Collection
    {
        return $filters;
    }

    /**
     * @return Collection
     * @since 1.18.0
     */
    public function getFilters() : Collection
    {
        return $this->arguments['filters'];
    }

    /**
     * @return string
     * @since 1.18.0
     */
    public function getContext() : string
    {
        return $this->arguments['context'];
    }

}