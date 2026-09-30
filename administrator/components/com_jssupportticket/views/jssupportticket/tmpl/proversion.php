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

/*
 * NOTE ON VARIABLE NAMES
 * This template includes views/menu.php further down, and that file declares its
 * own $groups, $group, $item, $base and $is in the same scope. Everything owned
 * by this page is therefore prefixed with jsstPro so the include cannot clobber
 * it (an earlier version lost $groups to the menu and warned on every section).
 */

$jsstProUrl    = 'https://www.joomsky.com/index.php/products/js-support-ticket-1/js-supprot-ticket-pro-joomla';
$jsstProUpdate = 'index.php?option=com_jssupportticket&c=proinstaller&layout=step1';
$jsstProImg    = 'components/com_jssupportticket/include/images/pro_page/';

// Which menu entry sent the user here. Every Pro menu item passes its slug so the
// matching feature can be pulled to the top and called out by name.
$jsstProRequested = Factory::getApplication()->input->getCmd('feature', '');

/**
 * The Pro feature set, grouped to mirror the admin menu so a visitor recognises
 * where each item lives. Keys match the "pro" slugs used in views/menu.php.
 */
$jsstProGroups = array(
    array(
        'label' => Text::_('Tickets'),
        'features' => array(
            'ticketviaemail' => array('via_email.png', Text::_('Email Tickets'), Text::_('Read a mailbox and turn incoming email into tickets automatically, with replies threaded back onto the original ticket.')),
            'export' => array('export-joomla.png', Text::_('Export Tickets'), Text::_('Download ticket records for reporting, auditing or migration, filtered by the criteria you choose.')),
        ),
    ),
    array(
        'label' => Text::_('People'),
        'features' => array(
            'staff' => array('staff.png', Text::_('Staff Members'), Text::_('Create staff accounts, assign them to departments and let them work tickets from the front end or the administrator.')),
            'roles' => array('acl.png', Text::_('Roles and Permissions'), Text::_('Build permission roles that control exactly which tickets and actions each staff member can reach.')),
        ),
    ),
    array(
        'label' => Text::_('Workflow'),
        'features' => array(
            'feedback' => array('feedback.png', Text::_('Customer Feedback'), Text::_('Collect satisfaction ratings and written feedback after a ticket closes, using your own feedback form fields.')),
        ),
    ),
    array(
        'label' => Text::_('Content'),
        'features' => array(
            'knowledgebase' => array('knowledgebase.png', Text::_('Knowledge Base'), Text::_('Publish categorised support articles so customers can solve common problems without opening a ticket.')),
            'faqs' => array('faqs.png', Text::_('FAQs'), Text::_('Answer the questions you get most often in a browsable, searchable list.')),
            'downloads' => array('downloads.png', Text::_('Downloads'), Text::_('Share manuals, drivers and release files with your customers from inside the support area.')),
            'announcements' => array('announcements.png', Text::_('Announcements'), Text::_('Post maintenance windows, release notes and notices where every customer will see them.')),
        ),
    ),
    array(
        'label' => Text::_('Email'),
        'features' => array(
            'mail' => array('internalmail.png', Text::_('Internal Mail'), Text::_('Send and receive messages between staff and customers without leaving the ticket system.')),
            'bannedemail' => array('banned_email.png', Text::_('Banned Emails'), Text::_('Block abusive or spam senders and keep a log of everything the ban list has stopped.')),
        ),
    ),
    array(
        'label' => Text::_('Reports'),
        'features' => array(
            'reports' => array('report-staff.png', Text::_('Reports'), Text::_('Overall, staff, department, user and satisfaction reports covering workload, response times and ratings.')),
        ),
    ),
);

// Additional Pro capabilities that do not have their own menu entry.
$jsstProExtras = array(
    array('stafftime.png', Text::_('Staff Time Tracking'), Text::_('Log time and notes against each ticket and report on it per staff member.')),
    array('internalnote.png', Text::_('Internal Notes'), Text::_('Leave private notes on a ticket that the customer never sees.')),
    array('stafftransfer.png', Text::_('Staff Transfer'), Text::_('Hand a ticket to another agent along with the reason for the transfer.')),
    array('departmenttransfer.png', Text::_('Department Transfer'), Text::_('Move a ticket to the team that owns it, keeping the full history.')),
    array('lockticket.png', Text::_('Lock Ticket'), Text::_('Freeze a ticket so no further replies can be posted.')),
    array('overdue.png', Text::_('Overdue Tickets'), Text::_('Flag tickets that have passed their due date so nothing is missed.')),
    array('activity_log.png', Text::_('Activity Log'), Text::_('A complete audit trail of every action taken on every ticket.')),
    array('visitorticketopen.png', Text::_('Visitor Tickets'), Text::_('Let visitors open and track tickets without creating an account.')),
);

// Pull the requested feature out so it can be shown as the headline callout.
$jsstProHighlight = null;
foreach ($jsstProGroups as $jsstProG) {
    if (isset($jsstProG['features'][$jsstProRequested])) {
        $jsstProHighlight = $jsstProG['features'][$jsstProRequested];
        break;
    }
}

$jsstPageTitle = 'Pro Version';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('Pro Version'), 'link' => null),
);
?>
<div id="js-tk-admin-wrapper" class="jsst-screen jsst-jssupportticket-proversion">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php include_once('components/com_jssupportticket/views/partials/pageheader.php'); ?>

        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow jsst-pro-page">

            <?php if ($jsstProHighlight !== null) : ?>
                <div class="jsst-pro-callout" role="status">
                    <span class="jsst-pro-callout__icon">
                        <img alt="" src="<?php echo htmlspecialchars($jsstProImg . $jsstProHighlight[0], ENT_QUOTES, 'UTF-8'); ?>">
                    </span>
                    <div class="jsst-pro-callout__copy">
                        <strong><?php echo htmlspecialchars($jsstProHighlight[1], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span><?php echo Text::_('is available in the Pro version of JS Support Ticket.'); ?></span>
                    </div>
                    <div class="jsst-pro-callout__actions">
                        <a class="jsst-pro-btn jsst-pro-btn--upgrade" target="_blank" rel="noopener noreferrer" href="<?php echo htmlspecialchars($jsstProUrl, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo Text::_('Get Pro'); ?>
                        </a>
                        <a class="jsst-pro-btn jsst-pro-btn--key" href="<?php echo htmlspecialchars($jsstProUpdate, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo Text::_('I have a key'); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <section class="jsst-pro-hero">
                <div class="jsst-pro-hero__content">
                    <span class="jsst-pro-eyebrow"><?php echo Text::_('You are running the free edition'); ?></span>
                    <h2><?php echo Text::_('Everything in JS Support Ticket Pro'); ?></h2>
                    <p><?php echo Text::_('The menu items marked with an asterisk are part of the Pro version. Upgrading unlocks them in place, keeping the tickets, departments and settings you already have.'); ?></p>
                    <div class="jsst-pro-actions">
                        <a class="jsst-pro-btn jsst-pro-btn--onbrand" target="_blank" rel="noopener noreferrer" href="<?php echo htmlspecialchars($jsstProUrl, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo Text::_('View Pro Version'); ?>
                        </a>
                        <a class="jsst-pro-btn jsst-pro-btn--ghost" href="<?php echo htmlspecialchars($jsstProUpdate, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo Text::_('Already have a key? Update now'); ?>
                        </a>
                    </div>
                </div>
                <div class="jsst-pro-hero__badge">
                    <img alt="" src="<?php echo htmlspecialchars($jsstProImg . 'pro_led.png', ENT_QUOTES, 'UTF-8'); ?>">
                    <strong><?php echo Text::_('JS Support Ticket'); ?></strong>
                    <span><?php echo Text::_('Pro'); ?></span>
                </div>
            </section>

            <?php foreach ($jsstProGroups as $jsstProGroup) : ?>
                <section class="jsst-pro-section">
                    <div class="jsst-pro-section__head">
                        <h3><?php echo htmlspecialchars($jsstProGroup['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <span class="jsst-pro-section__rule" aria-hidden="true"></span>
                    </div>
                    <div class="jsst-pro-grid">
                        <?php foreach ($jsstProGroup['features'] as $jsstProSlug => $jsstProFeature) : ?>
                            <article class="jsst-pro-card <?php echo $jsstProSlug === $jsstProRequested ? 'is-highlighted' : ''; ?>">
                                <span class="jsst-pro-card__icon">
                                    <img alt="" src="<?php echo htmlspecialchars($jsstProImg . $jsstProFeature[0], ENT_QUOTES, 'UTF-8'); ?>">
                                </span>
                                <h4><?php echo htmlspecialchars($jsstProFeature[1], ENT_QUOTES, 'UTF-8'); ?></h4>
                                <p><?php echo htmlspecialchars($jsstProFeature[2], ENT_QUOTES, 'UTF-8'); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>

            <section class="jsst-pro-section">
                <div class="jsst-pro-section__head">
                    <h3><?php echo Text::_('Also included'); ?></h3>
                    <span class="jsst-pro-section__rule" aria-hidden="true"></span>
                </div>
                <div class="jsst-pro-extras">
                    <?php foreach ($jsstProExtras as $jsstProExtra) : ?>
                        <div class="jsst-pro-extra">
                            <span class="jsst-pro-extra__icon">
                                <img alt="" src="<?php echo htmlspecialchars($jsstProImg . $jsstProExtra[0], ENT_QUOTES, 'UTF-8'); ?>">
                            </span>
                            <div>
                                <strong><?php echo htmlspecialchars($jsstProExtra[1], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <small><?php echo htmlspecialchars($jsstProExtra[2], ENT_QUOTES, 'UTF-8'); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="jsst-pro-footer">
                <div class="jsst-pro-footer__copy">
                    <strong><?php echo Text::_('Ready to unlock the full ticket system?'); ?></strong>
                    <span><?php echo Text::_('Your existing data stays exactly where it is.'); ?></span>
                </div>
                <div class="jsst-pro-footer__actions">
                    <a class="jsst-pro-btn jsst-pro-btn--upgrade" target="_blank" rel="noopener noreferrer" href="<?php echo htmlspecialchars($jsstProUrl, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo Text::_('View Pro Version'); ?>
                    </a>
                    <a class="jsst-pro-btn jsst-pro-btn--key" href="<?php echo htmlspecialchars($jsstProUpdate, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo Text::_('I have a key - Update'); ?>
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
