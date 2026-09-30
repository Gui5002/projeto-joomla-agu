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
use Joomla\CMS\Language\Text;

?>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-jssupportticket-info">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
    <div class="aboutus">
        <?php
$jsstPageTitle = 'About Us';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label' => 'About Us', 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <div class="js-col-md-12 ">
            <span class="js-admin-component"><?php echo Text::_('Component Detail'); ?></span>
            <span class="js-admin-component-detail"><?php echo Text::_('Component for on-line ticket support system'); ?></span>
            <div class="js-admin-info-wrapper">
                <span class="js-admin-info-title"><?php echo Text::_('Created By'); ?></span>
                <span class="js-admin-info-vlaue">Ahmad Bilal</span>
            </div>
            <div class="js-admin-info-wrapper">
                <span class="js-admin-info-title"><?php echo Text::_('Company'); ?></span>
                <span class="js-admin-info-vlaue">Joom Sky</span>
            </div>
            <div class="js-admin-info-wrapper">
                <span class="js-admin-info-title"><?php echo Text::_('Plugin Name'); ?></span>
                <span class="js-admin-info-vlaue"><?php echo Text::_('JS Support Ticket'); ?></span>
            </div>
            <div class="js-admin-joomsky-wrapper">
                <span class="js-admin-title">
                    <img src="components/com_jssupportticket/include/images/aboutus_page/logo.png" />
                    Joom Sky
                </span>
                <div class="js-col-md-8">
                    Our philosophy on project development is quite simple. We deliver exactly what you need to ensure the growth and effective running of your business. To do this we undertake a complete analysis of your business needs with you, then conduct thorough research and use our knowledge and expertise of software development programs to identify the products that are most beneficial to your business projects.
                    <span class="js-joomsky-link">
                        <a href="https://www.joomsky.com" target="_blank"><?php echo Text::_('Goto Web'); ?></a>
                    </span>
                </div>
                <div class="js-col-md-4">
                    <img src="components/com_jssupportticket/include/images/aboutus_page/product-images.png" />
                </div>
            </div>
            <span class="js-admin-title"><?php echo Text::_('Our Products');?></span>
            <div class="js-col-md-4 js-margin-top">
                <a href="https://www.joomsky.com/index.php/products/js-jobs-1/js-jobs-pro" target="_blank">
                    <img src="components/com_jssupportticket/include/images/aboutus_page/jobs.jpg" />
                </a>
            </div>
            <div class="js-col-md-4 js-margin-top">
                <a href="https://www.joomsky.com/index.php/products/js-autoz-1/js-autoz-pro" target="_blank">
                    <img src="components/com_jssupportticket/include/images/aboutus_page/autoz.jpg" />
                </a>
            </div>
            <div class="js-col-md-4 js-margin-top">
                <a href="https://www.joomsky.com/index.php/products/js-support-ticket-1/js-supprot-ticket-pro-wp" target="_blank">
                    <img src="components/com_jssupportticket/include/images/aboutus_page/tickets.jpg" />
                </a>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
