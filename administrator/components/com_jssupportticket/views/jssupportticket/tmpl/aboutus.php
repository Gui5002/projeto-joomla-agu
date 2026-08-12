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
use Joomla\CMS\Uri\Uri;

?>

<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
    <div class="aboutus">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo Text::_('Dashboard'); ?></a></li>
                        <li><?php echo Text::_('About Us'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo Text::_('About Us'); ?></h1></div>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
            <span class="js-admin-component"><?php echo Text::_('Component Detail'); ?></span>
            <span class="js-admin-component-detail"><?php echo Text::_('Component For Online Ticket Support System'); ?></span>
            <div class="js-admin-info-wrapper">
                <span class="js-admin-info-title"><?php echo Text::_('Created By'); ?></span>
                <span class="js-admin-info-vlaue"><?php echo Text::_('Ahmad Bilal'); ?></span>
            </div>
            <div class="js-admin-info-wrapper">
                <span class="js-admin-info-title"><?php echo Text::_('Company'); ?></span>
                <span class="js-admin-info-vlaue"><?php echo Text::_('Joom Sky'); ?></span>
            </div>
            <div class="js-admin-info-wrapper js-margin-bottom">
                <span class="js-admin-info-title"><?php echo Text::_('Plugin Name'); ?></span>
                <span class="js-admin-info-vlaue"><?php echo Text::_('Js Support Ticket'); ?></span>
            </div>
        <div class="js-col-md-12 js-mg-bottom">
            <a href="https://www.joomsky.com/index.php/products/js-jobs-1/js-jobs-pro" target="_blank" title="help desk">
                <img src="<?php echo Uri::root() ?>administrator/components/com_jssupportticket/include/images/aboutus_page/help-desk.jpg" />
            </a>
        </div>
        <div class="js-col-md-12 js-mg-bottom">
            <a href="https://www.joomsky.com/index.php/products/js-autoz-1/js-autoz-pro" target="_blank" title="job plugin">
                <img src="<?php echo Uri::root() ?>administrator/components/com_jssupportticket/include/images/aboutus_page/job-plugin.jpg" />
            </a>
        </div>
        <div class="js-col-md-12 js-mg-bottom">
            <a href="https://www.joomsky.com/index.php/products/js-support-ticket-1/js-supprot-ticket-pro-wp" target="_blank" title="vehicle manager">
                <img src="<?php echo Uri::root() ?>administrator/components/com_jssupportticket/include/images/aboutus_page/vehicle-manager.jpg" />
            </a>
        </div>
        <div class="js-col-md-12 js-mg-bottom">
            <a href="https://www.joomsky.com/index.php/products/js-jobs-1/js-jobs-pro" target="_blank" title="lms plugin">
                <img src="<?php echo Uri::root() ?>administrator/components/com_jssupportticket/include/images/aboutus_page/lms.jpg" />
            </a>
        </div>
        <div class="js-col-md-12 js-mg-bottom">
            <a href="https://www.joomsky.com/index.php/products/js-autoz-1/js-autoz-pro" target="_blank" title="car manager">
                <img src="<?php echo Uri::root() ?>administrator/components/com_jssupportticket/include/images/aboutus_page/car-manager.jpg" />
            </a>
        </div>
        <div class="js-col-md-12 js-mg-bottom">
            <a href="https://www.joomsky.com/index.php/products/js-support-ticket-1/js-supprot-ticket-pro-wp" target="_blank" title="job manager">
                <img src="components/com_jssupportticket/include/images/aboutus_page/job-manager.jpg" />
            </a>
        </div>                
        </div>        
    </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
