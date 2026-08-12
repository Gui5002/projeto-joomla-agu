<?php
defined('_JEXEC') or die;

use Joomla\CMS\Version;

if (version_compare(JVERSION, '4.0', '<')) {
    jimport('joomla.form.field');
    require_once JPATH_LIBRARIES . '/joomla/form/fields/subform.php';
    class JFormFieldMysubform extends JFormFieldSubform
    {
        protected $type = 'Mysubform';

        public function setup(SimpleXMLElement $element, $value, $group = null)
        {
            $result = parent::setup($element, $value, $group);

            if ($result) {
                $version = new Version();
                $shortVersion = $version->getShortVersion();
                if (version_compare($shortVersion, '6.1', '>=')) {
                    $this->layout = 'joomla.form.field.subform.repeatable-grid';
                } else {
                    $this->layout = 'joomla.form.field.subform.repeatable';
                }
            }

            return $result;
        }
    }
} else {
    class JFormFieldMysubform extends \Joomla\CMS\Form\Field\SubformField
    {
        protected $type = 'Mysubform';

        public function setup(SimpleXMLElement $element, $value, $group = null)
        {
            $result = parent::setup($element, $value, $group);

            if ($result) {
                $version = new Version();
                $shortVersion = $version->getShortVersion();
                if (version_compare($shortVersion, '6.1', '>=')) {
                    $this->layout = 'joomla.form.field.subform.repeatable-grid';
                } else {
                    $this->layout = 'joomla.form.field.subform.repeatable';
                }
            }

            return $result;
        }
    }
}
?>