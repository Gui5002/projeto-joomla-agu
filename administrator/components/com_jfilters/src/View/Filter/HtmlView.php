<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2021-2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Component\Jfilters\Administrator\View\Filter;

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Model\Filter\Collection;
use Bluecoder\Component\Jfilters\Administrator\Model\FilterInterface;
use Bluecoder\Component\Jfilters\Administrator\Model\FilterModel;
use Bluecoder\Component\Jfilters\Administrator\ObjectManager;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    /**
     * @var \Joomla\CMS\Form\Form
     * @since 1.0.0
     */
    protected $form;

    /**
     * @var \stdClass
     * @since 1.0.0
     */
    protected $item;

    /**
     * @var \Joomla\CMS\Object\CMSObject
     * @since 1.0.0
     */
    protected $state;

    /**
     * @var FilterInterface
     * @since 1.0.0
     */
    protected $filterItem;

    /**
     * Display the template
     *
     * @param null $tpl
     * @throws \Exception
     * @see HtmlView::loadTemplate()
     * @since 1.0.0
     */
    public function display($tpl = null)
    {
        /** @var FilterModel $model */
        $model = $this->getModel();
        if (method_exists($model, 'setUseExceptions' )) {
            $model->setUseExceptions(true);
        }

        try {
            $this->form = $model->getForm();
            $this->item = $model->getItem();
            $this->state = $model->getState();

            $objectManager = ObjectManager::getInstance();
            /** @var  Collection $filterCollection */
            $filterCollection = $objectManager->getObject(Collection::class);
            $filterCollection->addCondition('filter.id', $this->item->id);
            if ($filterCollection->getSize() == 1) {
                $items = $filterCollection->getItems();
                /** @var FilterInterface $item */
                $this->filterItem = reset($items);
            }
        }
        // Check for errors.
        catch (\Exception $e) {
            throw new GenericDataException($e->getMessage(), 500, $e);
        }

        Factory::getApplication()->getInput()->set('hidemainmenu', true);
        $this->addToolbar();

        return parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @todo add acl permissions and remove comment on $canDo->get('core.edit')
     * @throws \Exception
     * @since 1.0.0
     */
    protected function addToolbar()
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);

        $user = Factory::getApplication()->getIdentity();
        $userId = $user->id;
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

        // Since we don't track these assets at the item level, use the category id.
        $canDo = ContentHelper::getActions('com_jfilters');

        ToolbarHelper::title(Text::_('COM_JFILTERS_FILTER_EDIT'), 'jfilters');

        // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
        $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);
        $toolbarButtons = [];
        // Can't save the record if it's checked out and editable
        if (!$checkedOut && $itemEditable) {
            ToolbarHelper::apply('filter.apply');
            ToolbarHelper::save('filter.save');
        }

        ToolbarHelper::cancel('filter.cancel', 'JTOOLBAR_CLOSE');
    }
}
