<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('bootstrap.tooltip');

//HTMLHelper::_('bootstrap.tooltip');
//HTMLHelper::_('behavior.multiselect');
use Joomla\CMS\HTML\Helpers\Select;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;


?>

<script language=Javascript>
    function confirmdelete() {
        if (confirm("<?php echo Text::_('Are you sure to delete'); ?>") == true) {
            return true;
        } else
            return false;
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
                        <li><?php echo Text::_('JS Support Ticket Pro Installer'); ?></li>
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
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo Text::_('JS Support Ticket Pro Installer'); ?></h1>
        </div>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <div style="display:none;" id="jsjob_installer_waiting_div"></div>
        <span style="display:none;" id="jsjob_installer_waiting_span"><?php echo Text::_("Please wait installation in progress"); ?></span>
        <div id="jsst-main-wrapper" >
            <div id="jsst-lower-wrapper">
                <div class="jsst_installer_wrapper" id="jsst-installer_id">    
                    <div class="jsst_top">
                        <div class="jsst_logo_wrp">
                            <img src="components/com_jssupportticket/include/images/installerlogo.png">
                        </div>
                        <div class="jsst_heading_text"><?php echo Text::_("JS Support Ticket Pro"); ?></div>
                        <div class="jsst_subheading_text"><?php echo Text::_("Most Powerful Joomla Help Desk Plugin"); ?></div>
                    </div>
                    <form id="proinstaller_form" action="index.php" method="post">
                        <div class="jsst_middle" id="jsst_middle">
                            <div class="jsst_form_field_wrp">
                                <div class="jsst_bg_overlay">
                                    <input type="text" name="transactionkey" id="transactionkey" class="jsst_key_field" value="" placeholder="<?php echo Text::_('Please Insert Your Activation Key'); ?>"/>
                                </div>
                            </div>
                            <?php if(isset($this->response)){ ?>
                                <div id="invalid_activation_key" class="jsst_error_messages">
                                    <span class="jsst_msg"><?php echo $this->response; ?></span>
                                </div>
                            <?php } ?>
                            <?php if ($this->result['phpversion'] < 5) { ?>
                                <div class="jsst_error_messages">
                                    <span class="jsst_msg"><?php echo Text::_('PHP version smaller then recomended'); ?></span>
                                </div>
                            <?php } ?>
                            <?php if ($this->result['curlexist'] != 1) { ?>
                                <div class="jsst_error_messages">
                                    <span class="jsst_msg"><?php echo Text::_('CURL not exist'); ?></span>
                                </div>
                            <?php } ?>  
                            <?php if ($this->result['gdlib'] != 1) { ?>
                                <div class="jsst_error_messages">
                                    <span class="jsst_msg"><?php echo Text::_('GD library not exist'); ?></span>
                                </div>
                            <?php } ?>
                            <?php if ($this->result['ziplib'] != 1) { ?>
                                <div class="jsst_error_messages">
                                    <span class="jsst_msg"><?php echo Text::_('Zip library not exist'); ?></span>
                                </div>
                            <?php } ?>  
                            <?php if ($this->result['admin_dir'] < 755 || $this->result['site_dir'] < 755 || $this->result['tmp_dir'] < 755) { ?>
                                <div class="jsst_error_messages">
                                    <span class="jsst_msg"><?php echo Text::_('Directory permissions error'); ?></span>
                                    <?php if($this->result['admin_dir'] < 755){ ?>
                                          <span class="jsst_msg">"<?php echo JPATH_ROOT."/administrator/components/com_jssupportticket"; ?>"&nbsp;<?php echo Text::_('is not writable'); ?></span>
                                    <?php } ?>
                                    <?php if($this->result['site_dir'] < 755){ ?>
                                          <span class="jsst_msg">"<?php echo JPATH_ROOT."/components/com_jssupportticket"; ?>"&nbsp;<?php echo Text::_('is not writable'); ?></span>
                                    <?php } ?>
                                    <?php if($this->result['tmp_dir'] < 755){ ?>
                                          <span class="jsst_msg">"<?php echo JPATH_ROOT."/tmp"; ?>"&nbsp;<?php echo Text::_('is not writable'); ?></span>
                                    <?php } ?>
                                </div>    
                            <?php } ?>
                            <?php if ($this->result['create_table'] != 1) { ?>
                                    <div class="jsst_error_messages">    
                                        <span class="jsst_msg"><?php echo Text::_('Database create table not allowed'); ?></span>
                                    </div>
                                <?php } ?>
                                <?php if ($this->result['insert_record'] != 1) { ?>
                                    <div class="jsst_error_messages">    
                                        <span class="jsst_msg"><?php echo Text::_('Database insert record not allowed'); ?></span>
                                    </div>
                                <?php } ?>
                                <?php if ($this->result['update_record'] != 1) { ?>
                                    <div class="jsst_error_messages">    
                                        <span class="jsst_msg"><?php echo Text::_('Database update record not allowed'); ?></span>
                                    </div>
                                <?php } ?>
                                <?php if ($this->result['delete_record'] != 1) { ?>
                                    <div class="jsst_error_messages">    
                                        <span class="jsst_msg"><?php echo Text::_('Database delete record not allowed'); ?></span>
                                    </div>
                                <?php } ?>
                                <?php if ($this->result['drop_table'] != 1) { ?>
                                    <div class="jsst_error_messages">    
                                        <span class="jsst_msg"><?php echo Text::_('Database drop table not allowed'); ?></span>
                                    </div>
                                <?php } ?>
                                <?php if ($this->result['file_downloaded'] != 1) { ?>
                                    <div class="jsst_error_messages">    
                                        <span class="jsst_msg"><?php echo Text::_('Error file not downloaded'); ?></span>
                                    </div>
                            <?php } ?>
                        </div>
                        <?php if (($this->result['phpversion'] > 5) && ($this->result['curlexist'] == 1) && ($this->result['gdlib'] == 1) && ($this->result['ziplib'] == 1) && ($this->result['admin_dir'] >= 755 && $this->result['site_dir'] >= 755 && $this->result['tmp_dir'] >= 755 ) && ($this->result['create_table'] == 1) && ($this->result['insert_record'] == 1) && ($this->result['update_record'] == 1 ) && ($this->result['delete_record'] == 1 ) && ($this->result['drop_table'] == 1 ) && ($this->result['file_downloaded'] == 1 )) { ?>
                            <div class="jsst_bottom">
                                <div class="jsst_submit_btn">
                                    <button type="submit" id="startpress" class="jsst_btn" role="submit"><?php echo Text::_("Start"); ?></button>
                                </div>
                            </div>
                        <?php } ?>    
                        <input type="hidden" name="productcode" id="productcode" value="<?php echo isset($this->config['productcode']) ? $this->config['productcode'] : 'jssupportticket'; ?>" />
                        <input type="hidden" name="productversion" id="productversion" value="<?php echo isset($this->config['version']) ? $this->config['version'] : '100'; ?>" />
                        <input type="hidden" name="producttype" id="producttype" value="<?php echo isset($this->config['producttype']) ? $this->config['producttype'] : 'free'; ?>" />
                        <input type="hidden" name="domain" id="domain" value="<?php echo Uri::root(); ?>" />
                        <input type="hidden" name="JVERSION" id="JVERSION" value="<?php echo JVERSION; ?>" />
                        <input type="hidden" name="config_count" id="config_count" value="<?php echo $this->config_count; ?>" />
                        <input type="hidden" name="c" value="proinstaller">
                        <input type="hidden" name="layout" value="step1">
                        <input type="hidden" name="task" value="getversionlist">
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    </form>
                    <div id="jsst_next_form" style="display: none"></div> 
                </div>
            </div>
        </div> 
        </div>       
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
