<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 * Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Project:     JS Tickets
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

// Resolved in views/common.php - the manifest version when Joomla knows it.
$version = isset($this->versionDisplay) ? (string) $this->versionDisplay : '';
?>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-jssupportticket-aboutus">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
        $jsstPageTitle = 'About JS Support Ticket';
        $jsstBreadcrumb = array(
            array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
            array('label_raw' => Text::_('About Us'), 'link' => null),
        );
        include_once('components/com_jssupportticket/views/partials/pageheader.php');
        ?>

        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow jsst-about-card">
            <section class="jsst-about-hero" aria-label="<?php echo Text::_('About JS Support Ticket'); ?>">
                <div class="jsst-about-hero__content">
                    <span class="jsst-about-eyebrow"><?php echo Text::_('Joomla Support System'); ?></span>
                    <h2><?php echo Text::_('JS Support Ticket for Joomla'); ?></h2>
                    <p>
                        <?php echo Text::_('JS Support Ticket helps Joomla site owners manage customer support requests, staff replies, departments, priorities, email notifications, knowledge base content, and reporting from one organized admin area.'); ?>
                    </p>
                    <div class="jsst-about-actions">
                        <a class="jsst-about-btn jsst-about-btn--primary" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel"><?php echo Text::_('Open Control Panel'); ?></a>
                        <a class="jsst-about-btn" href="index.php?option=com_jssupportticket&c=config&layout=config"><?php echo Text::_('Open Configuration'); ?></a>
                    </div>
                </div>
                <div class="jsst-about-hero__panel">
                    <div class="jsst-about-logo-mark" aria-hidden="true">JS</div>
                    <strong><?php echo Text::_('JS Support Ticket'); ?></strong>
                    <span><?php echo Text::_('Professional ticket management for Joomla'); ?></span>
                </div>
            </section>

            <section class="jsst-about-grid" aria-label="<?php echo Text::_('Component Details'); ?>">
                <div class="jsst-about-info">
                    <span><?php echo Text::_('Component'); ?></span>
                    <strong><?php echo Text::_('JS Support Ticket'); ?></strong>
                </div>
                <div class="jsst-about-info">
                    <span><?php echo Text::_('Created By'); ?></span>
                    <strong><?php echo Text::_('Ahmad Bilal'); ?></strong>
                </div>
                <div class="jsst-about-info">
                    <span><?php echo Text::_('Company'); ?></span>
                    <strong><?php echo Text::_('JoomSky'); ?></strong>
                </div>
                <div class="jsst-about-info">
                    <span><?php echo Text::_('Version'); ?></span>
                    <strong><?php echo $version; ?></strong>
                </div>
            </section>

            <section class="jsst-about-section">
                <div class="jsst-about-section__head">
                    <span><?php echo Text::_('What it includes'); ?></span>
                    <h3><?php echo Text::_('Built for daily support operations'); ?></h3>
                </div>
                <div class="jsst-about-feature-grid">
                    <div class="jsst-about-feature">
                        <span class="jsst-about-feature__icon">T</span>
                        <h4><?php echo Text::_('Ticket Queue'); ?></h4>
                        <p><?php echo Text::_('Track open, answered, overdue, closed, and assigned tickets with clear status visibility.'); ?></p>
                    </div>
                    <div class="jsst-about-feature">
                        <span class="jsst-about-feature__icon">D</span>
                        <h4><?php echo Text::_('COM_JSSUPPORTTICKET_DEPARTMENTS_AND_STAFF'); ?></h4>
                        <p><?php echo Text::_('Route requests to departments and manage staff responsibilities from the administrator area.'); ?></p>
                    </div>
                    <div class="jsst-about-feature">
                        <span class="jsst-about-feature__icon">E</span>
                        <h4><?php echo Text::_('Email Workflow'); ?></h4>
                        <p><?php echo Text::_('Use email templates, notifications, and ticket via email options to keep customers updated.'); ?></p>
                    </div>
                    <div class="jsst-about-feature">
                        <span class="jsst-about-feature__icon">R</span>
                        <h4><?php echo Text::_('Reports'); ?></h4>
                        <p><?php echo Text::_('Review ticket activity, department workload, staff performance, and customer feedback.'); ?></p>
                    </div>
                </div>
            </section>

            <section class="jsst-about-product">
                <div>
                    <span class="jsst-about-eyebrow"><?php echo Text::_('Other Joomla product'); ?></span>
                    <h3><?php echo Text::_('JS Jobs for Joomla'); ?></h3>
                    <p>
                        <?php echo Text::_('JoomSky also provides JS Jobs for Joomla, a recruitment and job board extension for managing employers, job seekers, resumes, jobs, applications, and hiring workflows inside Joomla.'); ?>
                    </p>
                </div>
                <a class="jsst-about-btn jsst-about-btn--primary" target="_blank" rel="noopener noreferrer" href="https://www.joomsky.com"><?php echo Text::_('Visit JoomSky'); ?></a>
            </section>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
