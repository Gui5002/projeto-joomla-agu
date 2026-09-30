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

jimport('joomla.html.pane');
HTMLHelper::_('behavior.formvalidator');
$document = Factory::getDocument();
// 
if (JVERSION >= 3) {
    HTMLHelper::_('bootstrap.framework');
    HTMLHelper::_('jquery.framework');
}
$overduetype_array = array(
    '0' => array('value' => '1',
        'text' => Text::_('Days')),
    '1' => array('value' => '2',
        'text' => Text::_('Hours')));
// $overduetype = HTMLHelper::_('select.genericList', $overduetype_array, 'ticket_overdue_type', 'class="inputbox" ' . '', 'value', 'text', $this->configuration['ticket_overdue_type']);

$priorityColor = isset($this->priority) ? trim((string) $this->priority->prioritycolour) : '#00a650';
if ($priorityColor === '') {
    $priorityColor = '#00a650';
}
if ($priorityColor[0] !== '#') {
    $priorityColor = '#' . $priorityColor;
}
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $priorityColor)) {
    $priorityColor = '#00a650';
}
?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'savepriority' || task == 'savepriorityandnew' || task == 'saveprioritysave') {
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
    jQuery(document).ready(function(){
        var colorInput = jQuery('input#color1');
        var colorPicker = jQuery('input#prioritycolour_picker');
        var normalizeColor = function(value){
            value = (value || '').toString().trim();
            if (value.charAt(0) !== '#') {
                value = '#' + value;
            }
            if (!/^#[0-9a-fA-F]{6}$/.test(value)) {
                value = '#00a650';
            }
            return value.toLowerCase();
        };
        var startColor = normalizeColor(colorInput.val() || colorPicker.val());
        colorInput.val(startColor);
        colorPicker.val(startColor);
        colorPicker.on('input change', function(){
            colorInput.val(normalizeColor(this.value));
        });
        colorInput.on('input change', function(){
            var normalized = normalizeColor(this.value);
            colorInput.val(normalized);
            colorPicker.val(normalized);
        });
    });
</script>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-form">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = isset($this->priority) ? 'Edit Priority' : 'Add Priority';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_(isset($this->priority) ? 'Edit Priority' : 'Add Priority'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?> 
        <div id="jsstadmin-data-wrp" class="js-ticket-pagination-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="priority-title"><?php echo Text::_('Title'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" id="priority-title" type="text" name="priority" size="40" maxlength="255" value="<?php if (isset($this->priority)) echo $this->priority->priority; ?>" /></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="color1"><?php echo Text::_('Color'); ?><font color="red">*</font></label></div>
                <div class="js-value" id="color1_div">
                    <div class="jsst-priority-color-control">
                        <input id="prioritycolour_picker" type="color" value="<?php echo htmlspecialchars($priorityColor, ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?php echo Text::_('Choose priority color'); ?>" />
                        <input id="color1" class="inputbox required jsst-priority-color-text" name="prioritycolour" type="text" value="<?php echo htmlspecialchars($priorityColor, ENT_QUOTES, 'UTF-8'); ?>" maxlength="7" pattern="#[0-9a-fA-F]{6}" />
                    </div>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="overdueinterval"><?php echo Text::_('Overdue').' '.Text::_('type'); ?>:</label></div>
                <div class="js-value"><?php echo HTMLHelper::_('select.genericList', $overduetype_array, 'overduetypeid', 'class="inputbox" ' . '', 'value', 'text', isset($this->priority) ? $this->priority->overduetypeid : 0); ?></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="title"><?php echo Text::_('Overdue').' '.Text::_('interval'); ?>:</label></div>
                <div class="js-value"><input class="inputbox required" id="overdueinterval" type="text" name="overdueinterval" size="10" maxlength="10" value="<?php if (isset($this->priority)) echo $this->priority->overdueinterval; else echo '5' ?>" /></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo Text::_('Type'); ?>:&nbsp;</div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="1" id="public" name="ispublic"<?php if (isset($this->priority)) {if ($this->priority->ispublic == 1) echo "checked=''"; } else echo "checked=''"; ?> /> <label for="public"><?php echo Text::_('Public'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="0" id="private" name="ispublic"<?php if (isset($this->priority)) {if ($this->priority->ispublic == 0) echo "checked=''"; } ?> /><label for="private"><?php echo Text::_('Private'); ?></label>
                    </div>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo Text::_('Default'); ?></div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="1" id="yes" name="isdefault"<?php if (isset($this->priority)) {if ($this->priority->isdefault == 1) echo "checked=''"; } else echo "checked=''"; ?> /> <label for="yes"><?php echo Text::_('JYES'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="0" id="no" name="isdefault"<?php if (isset($this->priority)) {if ($this->priority->isdefault == 0) echo "checked=''"; } ?> /><label for="no"><?php echo Text::_('JNO'); ?></label>
                    </div>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo Text::_('Status'); ?>:&nbsp;</div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="1" id="active" name="status"<?php if (isset($this->priority)) {if ($this->priority->status == 1) echo "checked=''"; } else echo "checked=''"; ?> /><label for="active"> <?php echo Text::_('Active'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="0" id="disable" name="status"<?php if (isset($this->priority)) {if ($this->priority->status == 0) echo "checked=''"; } ?> /><label for="disable"> <?php echo Text::_('Disabled'); ?></label>
                    </div>
                </div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo Text::_('Save Priority'); ?>" /></div></div>

            <input type="hidden" name="id" value="<?php if (isset($this->priority)) echo $this->priority->id; ?>" />
            <input type="hidden" name="c" value="priority" />
            <input type="hidden" name="task" value="savepriority" />
            <input type="hidden" name="layout" value="formpriority" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="created" value="<?php if (!isset($this->department)) echo $curdate = date('Y-m-d H:i:s'); else echo $this->editgroup[0]->created; ?>"/>
            <input type="hidden" name="update" value="<?php if (isset($this->department)) echo $update = date('Y-m-d H:i:s'); ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
