<?php
namespace MyCompany\Module\MoodleCourses\Site\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\Registry\Registry;
use MyCompany\Module\MoodleCourses\Site\Helper\MoodleHelper;

class Dispatcher extends AbstractModuleDispatcher
{
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();

        /** @var Registry $params */
        $params = $data['params'] ?? (isset($this->params) ? $this->params : new Registry($data['module']->params ?? []));
        
        $helper = new MoodleHelper();

        $data['courses']     = $helper->getCourses($params);
        $data['moodleUrl']   = rtrim($params->get('moodle_url', ''), '/');
        $data['moodleToken'] = trim($params->get('moodle_token', ''));
        $data['showSummary'] = (bool) $params->get('show_summary', 1);
        $data['params']      = $params;

        return $data;
    }
}