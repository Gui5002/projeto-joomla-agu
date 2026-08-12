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
use Joomla\CMS\Language\Text;

?>
<script src="components/com_jssupportticket/include/js/jquery.js"></script>
<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsst-main-wrapper" class="post-installation">
            <div class="js-admin-title-installtion">
                <span class="jsst_heading"><?php echo Text::_('JS Support Ticket Settings'); ?></span>
                <div class="close-button-bottom">
                    <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" class="close-button">
                        <img src="components/com_jssupportticket/include/images/postinstallation/close-icon.png" />
                    </a>
                </div>
            </div>
            <div class="post-installtion-content-wrapper">
                <div class="post-installtion-content-header">
                    <ul class="update-header-img step-1">
                        <li class="header-parts first-part">
                            <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepone" title="link" class="tab_icon">
                                <img class="start" src="components/com_jssupportticket/include/images/postinstallation/general-settings.png" />
                                <span class="text"><?php echo Text::_('General'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts second-part">
                           <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=steptwo" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/ticket.png" />
                                <span class="text"><?php echo Text::_('Ticket Setting'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts forth-part active">
                            <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=settingcomplete" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/complete.png" />
                                <span class="text"><?php echo Text::_('Complete'); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="post-installtion-content_wrapper_right">
                    <div class="jsst-config-topheading">
                        <span class="heading-post-ins jsst-configurations-heading"><?php echo Text::_('Setting Complete');?></span>
                        <span class="heading-post-ins jsst-config-steps"><?php echo Text::_('Step 3 of 3');?></span>
                    </div>
                    <div class="post-installtion-content">
                        <form id="jslearnmanager-form-ins" method="post" action="#">
                            <div class="jsst_setting_complete_heading"><h1 class="Jsst_heading"><?php echo Text::_('Setting Completed'); ?></h1></div>
                            <div class="jsst_img_wrp">
                                <img src="components/com_jssupportticket/include/images/postinstallation/complete-setting.png" alt="Seting Log" title="Seting Logo"> 
                            </div>
                            <div class="jsst_text_below_img">
                                <?php echo Text::_('Setting you have applied has been save successfully');?>
                            </div>
                            <div class="pic-button-part">
                                <a class="next-step finish" href="index.php?option=com_jssupportticket">
                                    <?php echo Text::_('Finish'); ?>
                                </a>
                                <a class="back" href="index.php?option=com_jssupportticket&c=postinstallation&layout=steptwo"> 
                                   <img src="components/com_jssupportticket/include/images/postinstallation/back-arrow.png">
                                    <?php echo Text::_('Back'); ?>
                                </a>
                            </div>
                        </form>
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
