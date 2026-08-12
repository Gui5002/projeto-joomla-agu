<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2021-2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Model\FilterInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_jfilters.jfilters');

Text::script('COM_JFILTERS_FIELD_MAX_PATH_NESTING_WARNING', true);
$app = Factory::getApplication();
$input = $app->getInput();

//required to use the ui functions such as the tabs
$this->useCoreUI = true;

/** @var \Bluecoder\Component\Jfilters\Administrator\Model\FilterInterface $filterItem */
$filterItem = $this->filterItem;
?>

<form action="<?= Route::_('index.php?option=com_jfilters&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

    <div class="row title-alias form-vertical mb-3">
        <div class="col-12 col-md-6">
            <?= $this->form->renderField('label'); ?>
        </div>
        <div class="col-12 col-md-6">
            <?= $this->form->renderField('alias'); ?>
        </div>
    </div>

    <div>
        <?= HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general', 'recall' => true, 'breakpoint' => 768]); ?>

        <?= HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_JFILTERS_EDIT_FILTER')); ?>
        <div class="row">
            <div class="col-md-9">
                <fieldset class="adminform">
                    <?= $this->form->renderField('display'); ?>
                    <?= $this->form->renderField('date_format'); ?>
                    <?= $this->form->renderField('root'); ?>
                    <?= $this->form->renderField('config_type'); ?>
                    <?= $this->form->renderField('name'); ?>
                    <?= $this->form->renderField('context_alias'); ?>
                </fieldset>
            </div>
            <div class="col-md-3">
                <div class="card card-light">
                    <div class="card-body">
                        <?= LayoutHelper::render('joomla.edit.global', $this); ?>
                    </div>
                </div>
            </div>
        </div>

        <?= HTMLHelper::_('uitab.endTab'); ?>

        <?= HTMLHelper::_('uitab.addTab', 'myTab', 'basic-attribs', Text::_('COM_JFILTERS_BASIC_ATTRIBS_FIELDSET_LABEL')); ?>
        <fieldset id="fieldset-basic-attribs" class="options-form">
            <legend><?= Text::_('COM_JFILTERS_BASIC_ATTRIBS_FIELDSET_LABEL'); ?></legend>
            <div class="column-count-md-2 column-count-lg-3">
                <?= $this->form->renderFieldset('basic-attribs'); ?>
            </div>
        </fieldset>
        <?= HTMLHelper::_('uitab.endTab'); ?>

        <?php
        if ($filterItem->getConfig()->getValue()->getIsTree()) :?>
            <?= HTMLHelper::_('uitab.addTab', 'myTab', 'tree-attribs', Text::_('COM_JFILTERS_TREE_ATTRIBS_FIELDSET_LABEL')); ?>
            <fieldset id="fieldset-tree-attribs" class="options-form">
                <legend><?= Text::_('COM_JFILTERS_TREE_ATTRIBS_FIELDSET_LABEL'); ?></legend>
                <div class="column-count-md-2 column-count-lg-3">
                    <?= $this->form->renderFieldset('tree-attribs'); ?>
                </div>
            </fieldset>
            <?= HTMLHelper::_('uitab.endTab'); ?>
        <?php endif;?>

        <?= HTMLHelper::_('uitab.addTab', 'myTab', 'seo-attribs', Text::_('COM_JFILTERS_SEO_ATTRIBS_FIELDSET_LABEL')); ?>
        <fieldset id="fieldset-seo-attribs" class="options-form">
            <legend><?= Text::_('COM_JFILTERS_SEO_ATTRIBS_FIELDSET_LABEL'); ?></legend>
            <div class="column-count-md-2 column-count-lg-3">
                <?= $this->form->renderFieldset('seo-attribs'); ?>
            </div>
        </fieldset>
        <?= HTMLHelper::_('uitab.endTab'); ?>

        <?= HTMLHelper::_('uitab.addTab', 'myTab', 'advanced-attribs', Text::_('COM_JFILTERS_ADVANCED_FIELDSET_LABEL')); ?>
        <fieldset id="fieldset-advanced-attribs" class="options-form">
            <legend><?= Text::_('COM_JFILTERS_ADVANCED_FIELDSET_LABEL'); ?></legend>
            <div class="column-count-md-2 column-count-lg-3">
                <?php
                // Global cache is disabled - Show a warning
                if ($this->filterItem->getRoot() && !Factory::getApplication()->get('caching')) : ?>
                    <joomla-alert type="warning" dismiss="true" role="alert">
                        <div class="alert-wrapper">
                            <div class="alert-message">
                                <?= Text::_('COM_JFILTERS_FIELD_JOOMLA_CACHE_WARNING_LABEL'); ?>
                            </div>
                        </div>
                    </joomla-alert>
                <?php endif; ?>
                <?= $this->form->renderFieldset('advanced-attribs'); ?>
            </div>
        </fieldset>
        <?= HTMLHelper::_('uitab.endTab'); ?>


        <?= HTMLHelper::_('uitab.endTabSet'); ?>

        <?php $hidden_fields = $this->form->getInput('id'); ?>
        <div class="hidden"><?= $hidden_fields; ?></div>
        <input type="hidden" name="task" value="">
        <input type="hidden" name="forcedLanguage" value="<?= $input->get('forcedLanguage', '', 'cmd'); ?>">
        <?= HTMLHelper::_('form.token'); ?>
    </div>
</form>
