<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

jimport('joomla.html.pane');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.renderModal');
$document = Factory::getDocument();
global $mainframe;
?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'savehelptopic' || task == 'savehelptopicandnew' || task == 'savehelptopicsave') {
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
    function validate_form(f) {
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
$jsstPageTitle = isset($this->helptopic) ? 'Edit Help Topic' : 'Add Help Topic';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_(isset($this->helptopic) ? 'Edit Help Topic' : 'Add Help Topic'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="topic"><?php echo Text::_('Help Topic'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" type="text" id="topic" name="topic" value="<?php if (isset($this->helptopic)) echo $this->helptopic->topic; ?>"/></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="departmentid"><?php echo Text::_('Department'); ?><font color="red">*</font></label></div>
                <div class="js-value"><?php echo $this->lists['department']; ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo Text::_('Status'); ?></div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" name="status" id="active" value="1" <?php if (isset($this->helptopic)) {if ($this->helptopic->status == 1) echo "checked="; } else echo "checked="; ?>/><label for="active"><?php echo Text::_('Active'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" name="status" id="disable" value="0" <?php if (isset($this->helptopic)) if ($this->helptopic->status == 0) echo "checked="; ?>/><label for="disable"><?php echo Text::_('Disabled'); ?></label>
                    </div>
                </div>
            </div>
            <?php /*
            <div class="js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Auto Response'); ?>:&nbsp;</div>
            <div class="js-col-xs-12 js-col-md-10 js-value"><input type='checkbox' name='autoresponce' id='autoresponce' value='1' <?php if (isset($this->helptopic)) {echo ($this->helptopic->autoresponce == 1) ? "checked='checked'" : ""; } ?> /> <label for="autoresponce"><?php echo Text::_('Auto Response For This Topic') . ' (' . Text::_('override department setting') . ')'; ; ?></label></div>
            
*/ ?>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo Text::_('Save Help Topic'); ?>" /></div></div>
            <input type="hidden" name="created" value="<?php if (isset($this->helptopic)) {echo $this->helptopic->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
            <input type="hidden" name="update" value="<?php if (isset($this->helptopic)) {$update = date('Y-m-d H:i:s'); echo $update; } ?>" />
            <input type="hidden" name="id" value="<?php echo $this->helptopicid; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="c" value="helptopic" />
            <input type="hidden" name="layout" value="formhelptopic" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="task" value="savehelptopic" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
