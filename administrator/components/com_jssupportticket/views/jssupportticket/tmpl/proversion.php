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
use Joomla\CMS\Language\Text;

?>
<script src="components/com_jssupportticket/include/js/jquery.js"></script>
<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
         <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li>
                            <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard">
                                <?php echo Text::_('Dashboard'); ?>
                            </a>
                        </li>
                        <li>
                            <?php echo Text::_('Pro Version'); ?>
                        </li>
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
                    <span class="jsstadmin-ver"><?php
                        $this->version = $this->getJSModel('config')->getConfigByFor('version'); $version = str_split($this->version['version']);
                        $version = implode('.', $version);
                        echo $version;?></span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo Text::_('Pro Version'); ?></h1>
        </div>
        <div  id="jsstadmin-data-wrp" class="js-bg-null js-padding-all-null js-ticket-box-shadow">
        <div class="js-admin-propage">
            <div class="js-col-md-7"><img src="components/com_jssupportticket/include/images/pro_page/pro_led.png"></div>
            <div class="js-col-md-5">
                <span class="js-pro-title"><?php echo Text::_('Support Ticket Pro'); ?></span>
                <span class="js-pro-description"><?php echo Text::_('Feature available in pro version only'); ?></span>
                <a target="_blank" href="<?php echo 'https://www.joomsky.com/index.php/products/js-support-ticket-1/js-supprot-ticket-pro-joomla'; ?>" id="js-pro-link"></a>
            </div>
        </div>
        <span class="js-admin-propage-title"><?php echo Text::_('JS Support Ticket pro feature');?></span>
        <div class="js-row js-pro-feature-wrapper">
            <div class="js-col-md-6 js-col-xs-12 js-pro-feature">
                <img src="components/com_jssupportticket/include/images/pro_page/knowledgebase.png" class="js-knowledgebase"/>
                <span class="js-pro-feature-title"><?php echo Text::_('Knowledge Base'); ?></span>
                <span class="js-pro-feature-description"><?php echo Text::_('You can create and maintain knowledge Base for your ticket system'); ?></span>
            </div>
            <div class="js-col-md-6 js-col-xs-12 js-pro-feature">
                <img src="components/com_jssupportticket/include/images/pro_page/downloads.png" class="js-downloads"/>
                <span class="js-pro-feature-title"><?php echo Text::_('Downloads'); ?></span>
                <span class="js-pro-feature-description"><?php echo Text::_('You can add downloads for your customers'); ?></span>
            </div>
            <div class="js-col-md-6 js-col-xs-12 js-pro-feature">
                <img src="components/com_jssupportticket/include/images/pro_page/banned_email.png" class="js-banned_email"/>
                <span class="js-pro-feature-title"><?php echo Text::_('Banned Emails'); ?></span>
                <span class="js-pro-feature-description"><?php echo Text::_('You can ban and unabn any spam email address'); ?></span>
            </div>
            <div class="js-col-md-6 js-col-xs-12 js-pro-feature">
                <img src="components/com_jssupportticket/include/images/pro_page/via_email.png" class="js-viaemail"/>
                <span class="js-pro-feature-title"><?php echo Text::_('Ticket Via Email'); ?></span>
                <span class="js-pro-feature-description"><?php echo Text::_('System will read your email and create tickets'); ?></span>
            </div>
            <div class="js-col-md-6 js-col-xs-12 js-pro-feature">
                <img src="components/com_jssupportticket/include/images/pro_page/feedback.png" class="js-activitylog"/>
                <span class="js-pro-feature-title"><?php echo Text::_('Customer Feedback'); ?></span>
                <span class="js-pro-feature-description"><?php echo Text::_('Customer can provide feedback that can be used to improve services'); ?></span>
            </div>
            <div class="js-col-md-6 js-col-xs-12 js-pro-feature">
                <img src="components/com_jssupportticket/include/images/pro_page/stafftime.png" class="js-activitylog"/>
                <span class="js-pro-feature-title"><?php echo Text::_('Staff Time Tracking'); ?></span>
                <span class="js-pro-feature-description"><?php echo Text::_('Staff member can provide time and description while working on a ticket'); ?></span>
            </div>
        </div>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="http://www.burujsolutions.com">Buruj Solutions</a>
</div>
