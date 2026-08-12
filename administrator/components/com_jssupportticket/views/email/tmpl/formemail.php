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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;


use Joomla\CMS\Editor\Editor;
$conf   = Factory::getConfig();
$editor = Editor::getInstance($conf->get('editor'));

jimport('joomla.html.pane');
HTMLHelper::_('behavior.formvalidator');
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/custom.boots.css');
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
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
            alert("<?php echo Text::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
        return true;
    }
</script>

<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo Text::_('Dashboard'); ?></a></li>
                        <li><?php echo Text::_('Add Email'); ?></li>
                    </ul>
                </div>
            </div>
            <div id="jsstadmin-wrapper-top-right">
                <div id="jsstadmin-config-btn">
                    <a title="Configuration" href="index.php?option=com_jssupportticket&c=config&layout=config">
                        <img alt="Configuration" src="components/com_jssupportticket/include/images/config.png">
                    </a>
                </div>
                <div id="jsstadmin-vers-txt">
                    <?php echo Text::_('Version').Text::_(' : '); ?>
                    <span class="jsstadmin-ver">
                        <?php $version = str_split($this->version);
                        $version = implode('.', $version);
                        echo $version; ?>
                    </span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo Text::_('Add Email'); ?></h1></div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
            <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="email"><?php echo Text::_('Email'); ?><font color="red">*</font></label></div>
                    <div class="js-value"><input class="inputbox required validate-email" type="text" id="email" name="email" size="40" maxlength="255" value="<?php if (isset($this->email)) echo $this->email->email; ?>" /></div>
                </div>
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
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
