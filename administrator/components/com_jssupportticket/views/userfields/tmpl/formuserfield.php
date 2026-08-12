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

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/custom.boots.css');
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
if (JVERSION < 3) {
    HTMLHelper::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} else {
    HTMLHelper::_('bootstrap.framework');
    HTMLHelper::_('jquery.framework');
}
/*
HTMLHelper::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
HTMLHelper::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
*/
HTMLHelper::_('behavior.formvalidator');
$yesno = array(
    '0' => array('value' => 1, 'text' => Text::_('Yes')),
    '1' => array('value' => 0, 'text' => Text::_('No')),);
$fieldsize = array(
    '0' => array('value' => 50, 'text' => Text::_('50%')),
    '1' => array('value' => 100, 'text' => Text::_('100%')));

$fieldtype = array(
    '0' => array('value' => 'text', 'text' => Text::_('Text field')),
    '1' => array('value' => 'checkbox', 'text' => Text::_('Check box')),
    '2' => array('value' => 'date', 'text' => Text::_('Date')),
    '3' => array('value' => 'combo', 'text' => Text::_('Drop down')),
    '4' => array('value' => 'email', 'text' => Text::_('Email address')),
    '6' => array('value' => 'textarea', 'text' => Text::_('Text area')),
    '7' => array('value' => 'radio', 'text' => Text::_('Radio button')),
    '8' => array('value' => 'depandant_field', 'text' => Text::_('Depandent field')),
    '9' => array('value' => 'file', 'text' => Text::_('Upload file')),
    '10' => array('value' => 'multiple', 'text' => Text::_('Multi select')),
    '11' => array('value' => 'termsandconditions', 'text' => Text::_('Terms and Conditions')));

if (isset($this->userfield)) {
    $lstype = HTMLHelper::_('select.genericList', $fieldtype, 'userfieldtype', 'class="inputbox" ' . 'onchange="toggleType(this.options[this.selectedIndex].value);"', 'value', 'text', $this->userfield->userfieldtype);
    $lsrequired = HTMLHelper::_('select.genericList', $yesno, 'required', 'class="inputbox" ' . '', 'value', 'text', $this->userfield->required);
    $lspublished = HTMLHelper::_('select.genericList', $yesno, 'published', 'class="inputbox" ' . '', 'value', 'text', $this->userfield->published);
    $isvisitorpublished = HTMLHelper::_('select.genericList', $yesno, 'isvisitorpublished', 'class="inputbox" ' . '', 'value', 'text', $this->userfield->isvisitorpublished);
    $search_user = HTMLHelper::_('select.genericList', $yesno, 'search_user', 'class="inputbox" ' . '', 'value', 'text', $this->userfield->search_user);
    $search_visitor = HTMLHelper::_('select.genericList', $yesno, 'search_visitor', 'class="inputbox" ' . '', 'value', 'text', $this->userfield->search_visitor);
    $showonlisting = HTMLHelper::_('select.genericList', $yesno, 'showonlisting', 'class="inputbox" ' . '', 'value', 'text', $this->userfield->showonlisting);
    $fieldsize = HTMLHelper::_('select.genericList', $fieldsize, 'size', 'class="inputbox" ' . '', 'value', 'text', $this->userfield->size);
} else {
    $lstype = HTMLHelper::_('select.genericList', $fieldtype, 'userfieldtype', 'class="inputbox" ' . 'onchange="toggleType(this.options[this.selectedIndex].value);"', 'value', 'text', 0);
    $lsrequired = HTMLHelper::_('select.genericList', $yesno, 'required', 'class="inputbox" ' . '', 'value', 'text', 0);
    $lspublished = HTMLHelper::_('select.genericList', $yesno, 'published', 'class="inputbox" ' . '', 'value', 'text', 1);
    $isvisitorpublished = HTMLHelper::_('select.genericList', $yesno, 'isvisitorpublished', 'class="inputbox" ' . '', 'value', 'text', 1);
    $search_user = HTMLHelper::_('select.genericList', $yesno, 'search_user', 'class="inputbox" ' . '', 'value', 'text', 1);
    $search_visitor = HTMLHelper::_('select.genericList', $yesno, 'search_visitor', 'class="inputbox" ' . '', 'value', 'text', 1);
    $showonlisting = HTMLHelper::_('select.genericList', $yesno, 'showonlisting', 'class="inputbox" ' . '', 'value', 'text', 0);
    $fieldsize = HTMLHelper::_('select.genericList', $fieldsize, 'size', 'class="inputbox" ' . '', 'value', 'text', 0);
}
?>

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
                        <li><?php echo Text::_('Add User Field'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo Text::_('Add User Field'); ?></h1></div>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" name="adminForm" id="adminForm" >
        <!-- old -->
        <div class="js-form-wrapper">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Field Type'); ?><font class="required-notifier">*</font></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo $lstype; ?></div>
        </div>
        <div class="js-form-wrapper">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><label for="fieldtitle"><?php echo Text::_('Field Title'); ?><font class="required-notifier">*</font></label></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value">
            <input required="true" type="text" id="fieldtitle" name="fieldtitle" class="inputbox" value="<?php if (isset($this->userfield)) echo $this->userfield->fieldtitle; ?>"/></div>
        </div>
        <div class="js-form-wrapper for-terms-condtions-hide" id="for-combo-wrapper" style="display:none;">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Select','js-jobs') .'&nbsp;'. Text::_('Parent Field'); ?><font class="required-notifier">*</font></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value" id="for-combo"></div>
        </div>
        <div class="js-form-wrapper for-terms-condtions-hide">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Show on listing'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo $showonlisting; ?></div>
        </div>
        <div class="js-form-wrapper for-terms-condtions-hide">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('User Published'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo $lspublished; ?></div>
        </div>
        <div class="js-form-wrapper for-terms-condtions-hide">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Visitor Published'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo $isvisitorpublished; ?></div>
        </div>
        <div class="js-form-wrapper for-terms-condtions-hide">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('User Search'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo $search_user; ?></div>
        </div>
        <div class="js-form-wrapper for-terms-condtions-hide">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Visitor Search'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo $search_visitor; ?></div>
        </div>
        <div class="js-form-wrapper for-terms-condtions-hide">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Required'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo $lsrequired; ?></div>
        </div>
        <div class="js-form-wrapper for-terms-condtions-hide">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Field Size'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo $fieldsize; ?></div>
        </div>

        <div id="for-combo-options" >
            <?php
            $arraynames = '';
            $comma = '';
            if (isset($this->userfieldparams) && $this->userfield->userfieldtype == 'depandant_field') {
                foreach ($this->userfieldparams as $key => $val) {
                    $textvar = $key;
                    $textvar .='[]';
                    $arraynames .= $comma . "$key";
                    $comma = ',';
                    ?>
                    <div class="js-form-wrapper">
                        <div class="js-form-title js-col-xs-12 js-col-md-2 js-title">
                            <?php echo $key; ?>
                        </div>
                        <div class="js-col-lg-9 js-col-md-9 no-padding combo-options-fields" id="<?php echo $key; ?>">
                            <?php
                            if (!empty($val)) {
                                foreach ($val as $each) {
                                    ?>
                                    <span class="input-field-wrapper">
                                        <input name="<?php echo $textvar; ?>" id="<?php echo $textvar; ?>" value="<?php echo $each; ?>" class="inputbox one user-field" type="text">
                                        <img class="input-field-remove-img" src="components/com_jssupportticket/include/images/delete.png">
                                    </span><?php
                                }
                            }
                            ?>
                            <input id="depandant-field-button" onclick="getNextField( '<?php echo $key; ?>',this );" value="Add More" type="button">
                        </div>
                    </div><?php
                }
            }
            ?>
        </div>
        <div id="divText" class="js-form-wrapper">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Max Length'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"> 
                <input type="text" id="maxlength" name="maxlength" class="inputbox" value="<?php if (isset($this->userfield)) echo $this->userfield->maxlength; ?>" />
            </div>
        </div>
        <div class="js-form-wrapper divColsRows">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Columns'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><input type="text" id="cols" name="cols" class="inputbox" value="<?php if (isset($this->userfield)) echo $this->userfield->cols; ?>" /></div>
        </div>
        <div class="js-form-wrapper divColsRows">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Rows'); ?></div>
            <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><input type="text" id="rows" name="rows" class="inputbox" value="<?php if (isset($this->userfield)) echo $this->userfield->rows; ?>" /></div>
        </div>
        <div id="divValues" class="js-form-wrapper divColsRowsno-margin">
            <span class="js-admin-title"><?php echo Text::_('Use The Table Below To Add New Values'); ?></span>
            <div class="page-actions no-margin">
                <div id="user-field-values" class="no-padding">
                    <?php
                    if (isset($this->userfield) && $this->userfield->userfieldtype != 'depandant_field') {
                        if (isset($this->userfieldparams) && !empty($this->userfieldparams)) {
                            foreach ($this->userfieldparams as $key => $val) {
                                ?>
                                <span class="input-field-wrapper">
                                <input type="text" class="inputbox one user-field" id="values" name="values[]" class="inputbox" value="<?php echo isset($val) ? $val : ''; ?>" />
                                    <img class="input-field-remove-img" src="components/com_jssupportticket/include/images/delete.png" />
                                </span>
                            <?php
                            }
                        } else {
                            ?>
                            <span class="input-field-wrapper">
                            <input type="text" class="inputbox one user-field" id="values" name="values[]" class="inputbox" value="<?php echo isset($val) ? $val : ''; ?>" />
                                <img class="input-field-remove-img" src="components/com_jssupportticket/include/images/delete.png" />
                            </span>
                        <?php
                        }
                    }
                    ?>
                    <a class="js-button-link button user-field-val-button" id="user-field-val-button" onclick="insertNewRow();"><?php echo Text::_('Add Value') ?></a>
                </div>  
            </div>
        </div>
        <div id="for-terms-condtions-show" class="for-terms-condtions-show">
            <?php
            $termsandconditions_text = '';
            $termsandconditions_linktype = '';
            $termsandconditions_link = '';
            $termsandconditions_page = '';
            if( isset($this->userfieldparams) && $this->userfieldparams != '' && is_array($this->userfieldparams) && !empty($this->userfieldparams)){
                $termsandconditions_text = isset($this->userfieldparams['termsandconditions_text']) ? $this->userfieldparams['termsandconditions_text'] : '' ;
                $termsandconditions_linktype = isset($this->userfieldparams['termsandconditions_linktype']) ? $this->userfieldparams['termsandconditions_linktype'] :'' ;
                $termsandconditions_link = isset($this->userfieldparams['termsandconditions_link']) ? $this->userfieldparams['termsandconditions_link'] :'' ;
                $termsandconditions_page = isset($this->userfieldparams['termsandconditions_page']) ? $this->userfieldparams['termsandconditions_page'] :'' ;
            } ?>
            <div class="js-form-wrapper">
                <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Terms and Conditions Text'); ?></div>
                <div class="js-form-field js-col-xs-12 js-col-md-10 js-value">
                    <input type="text" id="termsandconditions_text" name="termsandconditions_text" class="inputbox" value="<?php echo $termsandconditions_text; ?>" />
                </div>
                <div class="js-form-desc">
                    e.g "  I have read and agree to the [link] Terms and Conditions[/link].  " The text between [link] and [/link] will be linked to provided url or Joomla page.
                </div>
            </div>
            <div class="js-form-wrapper">
            <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Terms and Conditions Link Type'); ?><font class="required-notifier">*</font></div>
                <?php
                $linktype = array(
                    '0' => array('value' => 1, 'text' => Text::_('Direct Link')),
                    '1' => array('value' => 2, 'text' => Text::_('Joomla Article Page')));
                $selectlinktype = HTMLHelper::_('select.genericList', $linktype, 'termsandconditions_linktype', 'class="inputbox"', 'value', 'text', $termsandconditions_linktype);
                ?>
                <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo $selectlinktype; ?></div>
            </div>
            <div class="js-form-wrapper for-terms-condtions-linktype1" style="display: none;">
                <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Terms and Conditions Link'); ?></div>
                <div class="js-form-field js-col-xs-12 js-col-md-10 js-value">
                    <input type="text" id="termsandconditions_link" name="termsandconditions_link" class="inputbox" value="<?php echo $termsandconditions_link; ?>" /></div>
                </div>
            </div>
            <div class="js-form-wrapper for-terms-condtions-linktype2" style="display: none;">
                <div class="js-form-title js-col-xs-12 js-col-md-2 js-title"><?php echo Text::_('Terms and Conditions Page'); ?></div>
                <div class="js-form-field js-col-xs-12 js-col-md-10 js-value"><?php echo HTMLHelper::_('select.genericList', $this->joomlaarticles, 'termsandconditions_page', 'class="inputbox" ', 'value', 'text', $termsandconditions_page); ?></div>
            </div>
        </div>


        <input type="hidden" id="id" name="id" value="<?php if (isset($this->userfield->id)) echo $this->userfield->id; ?>" />
        <input type="hidden" id="fieldfor" name="fieldfor" value="1" />
        <input type="hidden" id="field" name="field" value="<?php if (isset($this->userfield->field)) echo $this->userfield->field; ?>" />
        <input type="hidden" id="ordering" name="ordering" value="<?php echo isset($this->userfield->ordering) ? $this->userfield->ordering : ''; ?>" />
        <input type="hidden" id="c" name="c" value="userfields" />
        <input type="hidden" id="layout" name="layout" value="formuserfield" />
        <input type="hidden" id="task" name="task" value="saveuserfield" />
        <input type="hidden" id="isuserfield" name="isuserfield" value="1" />
        <input type="hidden" id="option" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" id="arraynames2" name="arraynames2" value="<?php echo $arraynames; ?>" />
        <input type="hidden" id="fieldname" name="fieldname" value="<?php echo isset($this->userfield->field) ? $this->userfield->field : ''; ?>" />
        <div class="js-form-button">
            <input type="submit" name="save" id="save" value="<?php echo Text::_('Save Field')?>" class="button js-form-save">
        </div>
        <?php echo HTMLHelper::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            toggleType(jQuery('#userfieldtype').val());
            jQuery('#termsandconditions_linktype').on('change', function() {
                if(this.value == 1){
                    jQuery('.for-terms-condtions-linktype1').slideDown();
                    jQuery('.for-terms-condtions-linktype2').hide();
                }else{
                    jQuery('.for-terms-condtions-linktype1').hide();
                    jQuery('.for-terms-condtions-linktype2').slideDown();
                }
            });

            var intial_val = jQuery('#termsandconditions_linktype').val();
            if(intial_val == 1){
                jQuery('.for-terms-condtions-linktype1').slideDown();
                jQuery('.for-terms-condtions-linktype2').hide();
            }else{
                jQuery('.for-terms-condtions-linktype1').hide();
                jQuery('.for-terms-condtions-linktype2').slideDown();
            }
        });
        function disableAll() {
            jQuery("#divValues").slideUp();
            jQuery(".divColsRows").slideUp();
            jQuery("#divText").slideUp();
        }
        function toggleType(type) {
            disableAll();
            //prep4SQL(document.forms['adminForm'].elements['field']);
            selType(type);
        }
        function prep4SQL(field) {
            if (field.value != '') {
                field.value = field.value.replace('js_', '');
                field.value = 'js_' + field.value.replace(/[^a-zA-Z]+/g, '');
            }
        }
        function selType(sType) {
            var elem;
            /*
             text
             checkbox
             date
             combo
             email
             textarea
             radio
             editor
             depandant_field
             multiple*/

            switch (sType) {
                case 'editor':
                    jQuery("div.for-terms-condtions-hide").show();
                    jQuery("#divText").slideUp();
                    jQuery("#divValues").slideUp();
                    jQuery(".divColsRows").slideUp();
                    jQuery("div#for-combo-wrapper").hide();
                    jQuery("div#for-combo-options").hide();
                    jQuery("div#for-combo-options-head").hide();
                    jQuery("div.for-terms-condtions-show").slideUp();
                    break;
                case 'textarea':
                    jQuery("div.for-terms-condtions-hide").show();
                    jQuery("#divText").slideUp();
                    jQuery(".divColsRows").slideDown();
                    jQuery("#divValues").slideUp();
                    jQuery("div#for-combo-wrapper").hide();
                    jQuery("div#for-combo-options").hide();
                    jQuery("div#for-combo-options-head").hide();
                    jQuery("div.for-terms-condtions-show").slideUp();
                    break;
                case 'email':
                case 'password':
                case 'text':
                    jQuery("div.for-terms-condtions-hide").show();
                    jQuery("#divText").slideDown();
                    jQuery("div#for-combo-wrapper").hide();
                    jQuery("div#for-combo-options").hide();
                    jQuery("div#for-combo-options-head").hide();
                    jQuery("div.for-terms-condtions-show").slideUp();
                    break;
                case 'termsandconditions':
                    jQuery("div#for-combo-wrapper").hide();
                    jQuery("div#for-combo-options").hide();
                    jQuery("div#for-combo-options-head").hide();
                    jQuery("#divText").slideUp();
                    jQuery(".divColsRows").slideUp();
                    jQuery("#divValues").slideUp();
                    jQuery("div#for-combo-wrapper").hide();
                    jQuery("div#for-combo-options").hide();
                    jQuery("div#for-combo-options-head").hide();
                    jQuery("div.for-terms-condtions-hide").hide();
                    jQuery("div.for-terms-condtions-show").slideDown();
                    break;
                case 'combo':
                case 'multiple':
                    jQuery("div.for-terms-condtions-hide").show();
                    jQuery("#divValues").slideDown();
                    jQuery("div#for-combo-wrapper").hide();
                    jQuery("div#for-combo-options").hide();
                    jQuery("div#for-combo-options-head").hide();
                    jQuery("div.for-terms-condtions-show").slideUp();
                    break;
                case 'depandant_field':
                    jQuery("div.for-terms-condtions-hide").show();
                    comboOfFields();
                    jQuery("div.for-terms-condtions-show").slideUp();
                    break;
                case 'radio':
                case 'checkbox':
                    //jQuery(".divColsRows").slideDown();
                    jQuery("div.for-terms-condtions-hide").show();
                    jQuery("#divValues").slideDown();
                    jQuery("div#for-combo-wrapper").hide();
                    jQuery("div#for-combo-options").hide();
                    jQuery("div#for-combo-options-head").hide();
                    jQuery("div.for-terms-condtions-show").slideUp();
                    /*
                     if (elem=getObject('jsNames[0]')) {
                     elem.setAttribute('mosReq',1);
                     }
                     */
                    break;
                case 'delimiter':
                default:
            }
        }

        function comboOfFields() {
            ajaxurl = 'index.php?option=com_jssupportticket&c=userfields&task=getfieldsforcombobyfieldfor&<?php echo Factory::getSession()->getFormToken(); ?>=1';
            var ff = jQuery("input#fieldfor").val();
            var pf = jQuery("input#field").val();
            jQuery.post(ajaxurl, { fieldfor: ff, parentfield:pf}, function (data) {
				if (data) {					
                    var d = jQuery.parseJSON(data);
                    jQuery("div#for-combo").html(d);
                    jQuery("div#for-combo-wrapper").show();
                }
            });
        }

        function getDataOfSelectedField() {
            ajaxurl = 'index.php?option=com_jssupportticket&c=userfields&task=getsectiontofillvalues&<?php echo Factory::getSession()->getFormToken(); ?>=1';
            var field = jQuery("select#parentfield").val();
            jQuery.post(ajaxurl, { pfield: field}, function (data) {
                if (data) {
                    var d = jQuery.parseJSON(data);
                    jQuery("div#for-combo-options-head").show();
                    jQuery("div#for-combo-options").html(d);
                    jQuery("div#for-combo-options").show();
                }
            });
        }

        function getNextField(divid,object) {
            var textvar = divid + '[]';
            var fieldhtml = "<span class='input-field-wrapper' ><input type='text' name='" + textvar + "' class='inputbox one user-field'  /><img class='input-field-remove-img' src='components/com_jssupportticket/include/images/delete.png' /></span>";
            jQuery(object).before(fieldhtml);
        }

        function getObject(obj) {
            var strObj;
            if (document.all) {
                strObj = document.all.item(obj);
            } else if (document.getElementById) {
                strObj = document.getElementById(obj);
            }
            return strObj;
        }

        function insertNewRow() {
            var fieldhtml = '<span class="input-field-wrapper" ><input name="values[]" id="values" value="" class="inputbox one user-field" type="text" /><img class="input-field-remove-img" src="components/com_jssupportticket/include/images/delete.png" /></span>';
            jQuery("#user-field-val-button").before(fieldhtml);
        }
        jQuery(document).ready(function () {
            jQuery("body").delegate("img.input-field-remove-img", "click", function () {
                jQuery(this).parent().remove();
            });
        });

        Joomla.submitbutton = function (task) {
            if (task == '') {
                return false;
            } else {
                if (task == 'saveuserfield') {
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
