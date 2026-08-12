<?php
/**
 * @package     Bluecoder.TagFilter
 *
 * @copyright   Copyright © 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

/**
 * Layout variables. Declared in the parent layout.
 *
 * @var array $list
 * @var bool $display_count
 * @var int $categoryId
 * @var string $context
 * @var string $display
 * @var bool $isPro
 * @var string $styling (tags|tabs)
 * @var string $alignment
 */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('mod_simple_tag_filter');
$wa->usePreset('mod_simple_tag_filter');
$baseURL = $context === 'com_content.category' ? 'index.php?option=com_content&view=category&layout=blog&id=' . $categoryId : 'index.php?option=com_ochsubscriptions&view=products';
$multiSelect = $display === '_buttons_multi' && $isPro ? true : false;
$selectedTags = Factory::getApplication()->getInput()->get('filter_tag', [], 'array');
$selectedTags = array_map('intval', $selectedTags);
$selectedTags = array_filter($selectedTags);
$styling = $styling === 'tabs' && $display === '_buttons_single' ? 'tabs' : 'tags';
?>

<div class="simple-tag-filter d-flex gap-2 mb-2 <?= $alignment === 'center' ? 'simple-tag-filter--center' : 'simple-tag-filter--start' ?> <?= $styling === 'tabs' ? 'simple-tag-filter--tabs border' : 'simple-tag-filter--tags py-2' ?>">
    <?php
    if ($styling === 'tabs') : ?>
        <span class="simple-tag-filter__glider"></span>
    <?php
    endif; ?>
    <?php
    foreach ($list as $item) :
        if ($multiSelect && $selectedTags) {
            // Create the toggle effect in the links
            if (in_array($item->tag_id, $selectedTags)) {
                $selectedTags2 = array_diff($selectedTags, [$item->tag_id]);
            } else {
                $selectedTags2 = $selectedTags;
                $selectedTags2 [] = $item->tag_id;
            }
            $tagURL = implode('&filter_tag[]=', $selectedTags2);
            $tagURL = $tagURL ? '&filter_tag[]=' . $tagURL : '';
        } else {
            $tagURL = !in_array($item->tag_id, $selectedTags) ? '&filter_tag=' . $item->tag_id : '';
        }
        ?>
        <a class="btn <?= $styling === 'tags' ? 'btn-outline-info' : ''; ?> d-flex justify-content-between align-items-center <?= $item->active ? 'active' . ($styling === 'tabs' ? ' btn-info' : '') : '' ?>"
           href="<?= Route::_($baseURL . $tagURL); ?>" <?= $item->active ? 'data-active' : ''; ?> >
            <span class="simple-tag-filter__text"><?= htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8'); ?></span>
            <?php
            if ($display_count) : ?>
                <span class="simple-tag-filter__counter badge uk-badge bg-info-subtle text-black ms-2"><?= $item->count; ?></span>
            <?php
            endif; ?>
        </a>
    <?php
    endforeach; ?>
</div>
