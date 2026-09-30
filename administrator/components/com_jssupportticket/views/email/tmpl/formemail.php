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
$emailtype = array('0' => array('value' => '0', 'text' => Text::_('Default')));
$truefalse = array(
    '0' => array('value' => '1',
        'text' => Text::_('JTRUE')),
    '1' => array('value' => '0',
        'text' => Text::_('JFALSE')),);

?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'saveemail' || task == 'saveemailandnew' || task == 'saveemailsave') {
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
            alert("<?php echo Text::_('Some values are not acceptable. Please retry'); ?>");
            return false;
        }
        return true;
    }
</script>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-form jsst-email-form-v84">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'Add Email';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('Add Email'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="email"><?php echo Text::_('Email'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required validate-email" type="text" id="email" name="email" size="40" maxlength="255" value="<?php if (isset($this->email)) echo $this->email->email; ?>" /></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="email"><?php echo Text::_('Send Email by'); ?>&nbsp;<font color="red">*</font></label></div>
                <div class="js-value">
                <?php echo HTMLHelper::_('select.genericList', $emailtype, 'smtpemailauth', 'class="inputbox" ' . '', 'value', 'text', isset($this->email) ? $this->email->smtpemailauth : ''); ?>
                <?php echo Text::_('Send email by').' '.Text::_('SMTP'); ?>
                </div>
            </div>

            <?php /*
            <div class="js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Auto Response'); ?></div>
            <div class="js-col-xs-12 js-col-md-10 js-value"><input type="radio" value="1" name="autoresponce"<?php if (isset($this->email)) {if ($this->email->autoresponce == 1) echo "checked=''"; } else echo "checked=''"; ?> /><?php echo Text::_('JYES'); ?> <input type="radio" value="0" name="autoresponce"<?php if (isset($this->email)) {if ($this->email->autoresponce == 0) echo "checked=''"; } ?> /><?php echo Text::_('JNO'); ?></div>
            <div class="js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Priority'); ?>:&nbsp;</div>
            <div class="js-col-xs-12 js-col-md-10 js-value"><?php echo $this->lists['priority']; ?></div>
                */ ?>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo Text::_('Status'); ?>:&nbsp;</div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="1" name="status"<?php if (isset($this->email)) {if ($this->email->status == 1) echo "checked=''"; } else echo "checked=''"; ?> /><?php echo Text::_('Active'); ?>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" value="0" name="status"<?php if (isset($this->email)) {if ($this->email->status == 0) echo "checked=''"; } ?> /><?php echo Text::_('Disabled'); ?></div>
                    </div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" id="submit_app" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo Text::_('Save Email'); ?>" /></div></div>
            <input type="hidden" name="id" value="<?php if (isset($this->email)) echo $this->email->id; ?>" />
            <input type="hidden" name="c" value="email" />
            <input type="hidden" name="task" value="saveemail" />
            <input type="hidden" name="layout" value="formemail" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="created" value="<?php if (!isset($this->email)) echo $curdate = date('Y-m-d H:i:s'); else echo $this->email->created; ?>"/>
            <input type="hidden" name="update" value="<?php if (isset($this->email)) echo $update = date('Y-m-d H:i:s'); ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>

