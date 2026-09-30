<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Editor\Editor;

$conf   = Factory::getConfig();
$editor = Editor::getInstance($conf->get('editor'));
HTMLHelper::_('behavior.formvalidator');
$document = Factory::getDocument();
?>

<script type="text/javascript">
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'savedepartment' || task == 'savedepartmentandnew' || task == 'savedepartmentsave') {
                returnvalue = validate_form(document.adminForm);
            } else
                returnvalue = true;
            if (returnvalue) {
                Joomla.submitform(task);
                return true;
            } else
                return false;
        }
    }
    function validate_form(f)
    {
        if (document.formvalidator.isValid(f)) {
            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo Factory::getSession()->getFormToken(); ?>';//send token
        } else {
            alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
        return true;
    }
</script>
<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-form">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = isset($this->department) ? 'Edit Department' : 'Add Department';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_(isset($this->department) ? 'Edit Department' : 'Add Department'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="departmentname"><?php echo Text::_('Title'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" type="text" id="departmentname" name="departmentname" size="40" maxlength="255" value="<?php if (isset($this->department)) echo $this->department->departmentname; ?>" /></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="emailid"><?php echo Text::_('Outgoing Email'); ?><font color="red">*</font></label></div>
                <div class="js-value"><?php echo $this->lists['emaillist'] ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="sendemail-yes"><?php echo Text::_('Receive Email'); ?></label></div>
                <div class="js-value-radio-btn"> 
                <div class="jsst-formfield-status-radio-button-wrap">
                <label><input type="radio" id="sendemail-yes" <?php if(isset($this->department)){ if($this->department->sendemail == 1) echo "checked='true'"; }else{ echo "checked='true'"; } ?> name="sendemail" value="1"><?php echo Text::_('JYES'); ?></label></div>
                <div class="jsst-formfield-status-radio-button-wrap">
                <label><input type="radio" id="sendemail-no" <?php if(isset($this->department) && $this->department->sendemail == 0){  echo "checked='true'"; } ?> name="sendemail" value="0"><?php echo Text::_('JNO'); ?></label></div>
                </div>
            </div>
            <div class="js-form-wrapper fullwidth">
                <div class="js-title"><?php echo Text::_('Signature'); ?></div>
                <div class="js-value"><?php  if (isset($this->department->departmentsignature)) echo $editor->display('departmentsignature', $this->department->departmentsignature, '', '300', '60', '20', false); else echo $editor->display('departmentsignature', '', '', '300', '60', '20', false); ?> </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo Text::_('Append Signature'); ?></div>
                <div class="jsst-formfield-radio-button-wrap"><label><input type="checkbox" name="canappendsignature" value="1"<?php if (isset($this->department->canappendsignature)) echo "checked=''"; ?> /><?php echo Text::_('Append Signature'); ?></label></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo Text::_('Default'); ?></div>
                <div class="js-value"><?php echo $this->lists['isdefault']; ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo Text::_('Status'); ?></div>
                <div class="js-value"><?php echo $this->lists['status']; ?></div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo Text::_('Save Department'); ?>" /></div></div>

            <input type="hidden" name="id" value="<?php if (isset($this->department)) echo $this->department->id; ?>" />
            <input type="hidden" name="ispublic" value="1" />
            <input type="hidden" name="c" value="department" />
            <input type="hidden" name="task" value="savedepartment" />
            <input type="hidden" name="layout" value="formdepartment" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="created" value="<?php if (!isset($this->department)) echo $curdate = date('Y-m-d H:i:s'); else echo $this->department->created; ?>"/>
            <input type="hidden" name="update" value="<?php if (isset($this->department)) echo $update = date('Y-m-d H:i:s'); ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
