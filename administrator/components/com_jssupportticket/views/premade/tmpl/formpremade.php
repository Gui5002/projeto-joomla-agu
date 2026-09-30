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
use Joomla\CMS\Editor\Editor;

jimport('joomla.html.pane');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.renderModal');
$document = Factory::getDocument();
if (JVERSION >= 3) {
    HTMLHelper::_('bootstrap.framework');
    HTMLHelper::_('jquery.framework');
}
?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'savepremade' || task == 'savepremadeandnew' || task == 'savepremadesave') {
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
$jsstPageTitle = 'Add Premade Message';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('Add Premade Message'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?> 
        <div id="jsstadmin-data-wrp" class="js-ticket-pagination-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="title"><?php echo Text::_('Title'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input  class="inputbox required" type="text" name="title" id="title" value="<?php if (isset($this->premade)) {echo $this->premade->title; } ?>" /></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="departmentid"><?php echo Text::_('Department'); ?><font color="red">*</font></label></div>
                <div class="js-value"><?php echo $this->lists['departments'] ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="active"><?php echo Text::_('Status'); ?><font color="red">*</font></label></div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" name="isenabled" id="active" value="1" <?php if (isset($this->premade)) {if ($this->premade->isenabled == 1) echo "checked="; } else echo "checked="; ?>/><label for="active"><?php echo Text::_('Active'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" name="isenabled" id="disable" value="-1" <?php if (isset($this->premade)) if ($this->premade->isenabled == -1) echo "checked="; ?>/><label for="disable"><?php echo Text::_('Offline'); ?></label>
                    </div>
                </div>
            </div>
            <div class="js-form-wrapper fullwidth">
                <div class="js-title"><?php echo Text::_('Department Under Which The Answer Will Be Made Available'); ?>:&nbsp;</div>
                <div class="js-value"><?php 
                    $conf   = Factory::getConfig();
                    $editor = Editor::getInstance($conf->get('editor'));
                    if (isset($this->premade)) echo $editor->display('answer', $this->premade->answer, '', '300', '60', '20', false); else echo $editor->display('answer', '', '', '300', '60', '20', false); ?></div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo Text::_('Save Premade Message'); ?>" /></div></div>

            <input type="hidden" name="created" value="<?php if (isset($this->premade)) {echo $this->premade->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" /> <input type="hidden" name="update" value="<?php if (isset($this->premade)) {$update = date('Y-m-d H:i:s'); echo $update; } ?>" />
            <input type="hidden" name="id" value="<?php echo $this->premadeid; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="c" value="premade" />
            <input type="hidden" name="layout" value="formpremade" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="status" value="1" />
            <input type="hidden" name="task" value="savepremade" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
