<?php
/**
 * @package     Bluecoder.TagFilter
 *
 * @copyright   Copyright © 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

/**
 * Layout variables. Declared in the Dispatcher.
 *
 * @var array $list
 * @var bool $display_count
 * @var int $categoryId
 * @var bool $useInPage
 * @var string $display
 * @var string $alignment
 */
?>
<?php if (count($list) && $useInPage) :?>
        <?php
        $displayLayout = in_array($display, ['_buttons_single', '_buttons_multi']) ? '_buttons' : $display;
        require ModuleHelper::getLayoutPath('mod_simple_tag_filter', $displayLayout);?>
<?php endif; ?>