<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.tooltip');

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
global $mainframe;
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
                        <li><?php echo Text::_('System Errors'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo Text::_('System Errors'); ?></h1>      
        </div>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
            <a href="index.php?option=com_jssupportticket&c=systemerrors&view=systemerrors&layout=systemerrors"><?php echo Text::_('System Errors'); ?></a>
            <div class="js-col-md-12 js-error-wrapper">
                <div class="js-col-md-2 js-col-xs-12 js-error-title">
                    <?php echo Text::_('From'); ?>
                </div>
                <div class="js-col-md-10 js-col-xs-12 js-error-value">
                    <?php if($this->error->staffname) echo $this->error->staffname; else echo Text::_("User"); ?>
                </div>
            </div>
            <div class="js-col-md-12 js-error-wrapper">
                <div class="js-col-md-2 js-col-xs-12 js-error-title">
                    <?php echo Text::_('Date'); ?>
                </div>
                <div class="js-col-md-10 js-col-xs-12 js-error-value">
                    <?php echo HTMLHelper::_('date',$this->error->created,$this->config['date_format']); ?>
                </div>
            </div>
            <div class="js-col-md-12 js-error-wrapper">
                <div class="js-col-md-2 js-col-xs-12 js-error-title">
                    <?php echo Text::_('View'); ?>
                </div>
                <div class="js-col-md-10 js-col-xs-12 js-error-value">
                    <?php if ($this->error->isview == 1) echo Text::_('Yes'); else echo Text::_('No'); ?>
                </div>
            </div>
            <div class="js-col-md-12 js-error-wrapper">
                <div class="js-col-md-2 js-col-xs-12 js-error-title">
                    <?php echo Text::_('Error'); ?>
                </div>
                <div class="js-col-md-10 js-col-xs-12 js-error-value">
                    <?php echo $this->error->error; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
