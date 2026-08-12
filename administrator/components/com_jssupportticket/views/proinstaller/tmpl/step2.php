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
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

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
                    <div class="jsst_middle" id="jsst_middle">
                        <div class="jsst_form_field_wrp">
                            <div class="jsst_bg_overlay">
                                <input type="text" name="transactionkey" id="transactionkey" class="jsst_key_field" value="<?php if(isset($this->transactionkey)) echo $this->transactionkey; ?>" placeholder="<?php echo Text::_('Please Insert Your Activation Key'); ?>"/>
                            </div>
                        </div>
                        <div id="jsst_error_message" class="jsst_error_messages" style="display: none">
                            
                        </div>
                    </div>
                    <?php 
                        if(isset($this->response) && $this->response != ''){
                            $response = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($this->response);
                            $response = json_decode($response); ?>
                            <div class="jslm_error_messages">
                                <?php if($response[0] != true){ ?>
                                    <span class="jsst_error_messages" id="jsst_error_message"><span class="jsst_msg"><?php echo $response[1]; ?></span></span>
                                <?php  
                                }else{ ?>
                                    <div id="jsst_next_form"><?php echo $response[2]; ?></div><?php 
                                } ?>
                            </div>                     
                    <?php  } ?>
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
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('span#jsjob_installer_helptext').hide();
        $('div#jsjob_installer_formlabel').hide();
    });    
</script>
