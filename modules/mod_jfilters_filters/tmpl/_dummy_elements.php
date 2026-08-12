<?php
/**
 * This file, generates the html output for a number of layouts.
 *
 * Since our radios, checkboxes and buttons are just links, we do not need to repeat ourselves.
 * We just use css modifiers to style them accordingly.
 *
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2021-2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Model\Filter\Option\Collection;
use Bluecoder\Component\Jfilters\Administrator\Model\FilterInterface;
use Joomla\CMS\Router\Route;

/**
 * Layout variables, Declared in the parent layout.
 *
 * @var stdClass $module
 * @var FilterInterface $filter
 * @var Collection $options
 * @var string $display
 * @var string $elementModifierClass Declared in the display layout that calls that and defines the element type (modifier).
 * @var string $chevronSVG
 * @var bool $excludeParents
 */

// The list modifier class. Remove '_' if exist.
$displayTmp = strpos($display, '_') === 0 ? substr($display, 1) : $display;
$listModifierClass = 'jfilters-filter-list--' . $displayTmp;

// The link modifier class
$linkModifierClass = $elementModifierClass;
$linkModifierClass .= !empty($prependDummyInput) ? ' jfilters-item-link--dummy-input' : '';
$filterParams = $filter->getAttributes();
?>

<ul <?= !empty($listId) ? 'id="' . $listId . '"' : '' ?> class="jfilters-filter-list <?= $listModifierClass?>" <?= !empty($ariaHidden) ? $ariaHidden : '' ?>>
    <?php
    /** @var \Bluecoder\Component\Jfilters\Administrator\Model\Filter\OptionInterface $option */
    foreach ($options as $option) {
        $isParent = false;
        $parentListItemClass = '';
        $ariaControls = '';
        $ariaExpanded = '';

        // nested list properties
        if ($option->isNested() && $option->getChildren() !== null) {
            $isParent = true;
            $parentListItemClass = 'jfilters-filter-list__item--parent';
            $listId = 'jfilters-filter-list-' . $module->id . '-' . $filter->getId() . '-'. bin2hex($option->getValue());
            $ariaExpandedState = $filterParams->get('nested_toggle_state',
                'collapsed') == 'expanded' || $option->isSelected() || $option->hasChildSelected() ? true : false;
            $ariaControls = 'aria-controls="' . $listId . '"';
            $ariaExpanded = $ariaExpandedState ? 'aria-expanded="true"' : 'aria-expanded="false"';
            // aria hidden is always the opposite to aria-expanded
            $ariaHidden = $ariaExpandedState ? 'aria-hidden="false"' : 'aria-hidden="true"';
        }
        ?>
        <li class="jfilters-filter-list__item  <?= $parentListItemClass ?: '' ?>">
            <?php
            // if parent element, show the toggle button
            if ($isParent) { ?>
            <button class="jfilters-item__toggle-btn" type="button" aria-label="<?= htmlspecialchars($option->getLabel()); ?>"
                <?= $ariaExpanded . ' ' . $ariaControls ?>>
                <?= $chevronSVG ?>
                </button><?php
            } ?>
            <a class="jfilters-item-link <?= $option->isSelected() ? 'jfilters-item-link--selected' : '' ?> <?= !$isParent || !$excludeParents ? $linkModifierClass : ''?>"
                <?= $filterParams->get('hide_links_from_bots', !$filterParams->get('follow_links', false)) ? 'href="#" data-goto' : 'href'; ?>="<?= Route::_($option->getLink()) ?>"
                <?= !$filterParams->get('follow_links', false) || $filterParams->get('hide_links_from_bots', !$filterParams->get('follow_links', false)) ? 'rel="nofollow"' : ''; ?>
                <?= !$filterParams->get('parent_node_linkable',true) ? $ariaExpanded . ' ' . $ariaControls : '' ?>
                <?= $option->isSelected() ? 'aria-current="true"' : '' ?>>
                <span class="jfilters-item__text">
                    <span class="jfilters-item__label-text"><?= htmlspecialchars($option->getLabel()); ?></span><?php

                // show the counter
                if ($option->getCount() !== null && $filterParams->get('show_option_counter', true)) {
                    ?><span class="jfilters-item__counter">(<?= (int)$option->getCount(); ?>)</span>
                    <?php
                } ?>
                </span>
            </a>
            <?php
            // load the child elements and create a nested list.
            if ($isParent) {
                $tmpOptions = $options;
                $options = $option->getChildren();
                // We intentionally use the entire namespace here, to avoid conflicts if that file's contents used inside overwritten layouts.
                require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_jfilters_filters', $display);
                $options = $tmpOptions;
                $listId = '';
                $ariaHidden = '';
            } ?>
        </li>
        <?php
    }
    ?>
</ul>
