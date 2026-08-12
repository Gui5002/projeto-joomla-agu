<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2021-2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Component\Jfilters\Administrator\Model\Filter\Option\Collection\Filtered;

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Model\Filter\Option\Collection\Filtered;
use Bluecoder\Component\Jfilters\Administrator\Model\Filter\Option\Boolean as BooleanOption;
class Boolean extends Filtered
{
    protected $itemObjectClass = BooleanOption::class;
}