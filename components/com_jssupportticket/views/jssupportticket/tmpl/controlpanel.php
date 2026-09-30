<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company: Buruj Solutions
 * Project: JS Tickets
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if ($this->config['offline'] == '1') {
    messageslayout::getSystemOffline($this->config['title'], $this->config['offline_text']);
    return;
}

require_once JPATH_COMPONENT_SITE . '/views/header.php';

$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/circle.css');
$document->addScript('components/com_jssupportticket/include/js/circle.js');

$language = Factory::getLanguage();

$isLoggedIn = !$this->user->getIsGuest();
$stats = is_array($this->userticketstats ?? null) ? $this->userticketstats : array();
$totalTickets = isset($stats['allticket']) ? (int) $stats['allticket'] : 0;
$openTickets = isset($stats['openticket']) ? (int) $stats['openticket'] : 0;
$closedTickets = isset($stats['closedticket']) ? (int) $stats['closedticket'] : 0;
$answeredTickets = isset($stats['answeredticket']) ? (int) $stats['answeredticket'] : 0;
$latestTickets = is_array($this->latest_tickets ?? null) ? $this->latest_tickets : array();
$latestAnnouncements = is_array($this->latest_announcements ?? null) ? $this->latest_announcements : array();
$latestKnowledgebase = is_array($this->latest_knowledgebase ?? null) ? $this->latest_knowledgebase : array();
$latestDownloads = is_array($this->latest_downloads ?? null) ? $this->latest_downloads : array();

/*
 * Panel visibility. In the Pro edition each of these is derived from whether the
 * viewer is a staff member and the matching cplink_* setting; this edition has no
 * staff module, so they collapse to their non-staff values. They were previously
 * read without ever being assigned, which raised a notice per panel and, because
 * an undefined variable is falsy, silently hid the stat cards and the latest
 * tickets list on this page.
 */
$showStatCards     = true;
$showLatestTickets = true;

// The ticket statistics chart is a staff-only panel in this component.
$showTicketChart = false;

// The side column lists announcements, knowledge base articles and downloads -
// all Pro features - so it can never have content here. Hiding it lets the
// latest tickets panel use the full width via jsst-cp-latest-grid-full.
$showLatestSidePanels = false;

$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$getValue = static function ($object, $key, $default = '') {
    if (is_object($object) && isset($object->{$key})) {
        return $object->{$key};
    }
    if (is_array($object) && isset($object[$key])) {
        return $object[$key];
    }
    return $default;
};

$componentLink = function ($controller, $layout, array $extra = array()) {
    $query = array_merge(array(
        'option' => 'com_jssupportticket',
        'c' => $controller,
        'layout' => $layout,
        'Itemid' => $this->Itemid,
    ), $extra);

    return Route::_('index.php?' . http_build_query($query));
};

$imageBase = Uri::root() . 'components/com_jssupportticket/include/images/';
$dashboardIconBase = $imageBase . 'dashboard-icon/';

$statusLabel = static function ($status) {
    switch ((int) $status) {
        case 0:
            return Text::_('New');
        case 1:
            return Text::_('Waiting for Reply');
        case 2:
            return Text::_('In Progress');
        case 3:
            return Text::_('Replied');
        case 4:
            return Text::_('Closed');
        case 5:
            return Text::_('Closed by Merge');
        default:
            return Text::_('Open');
    }
};

$statusClass = static function ($status) {
    switch ((int) $status) {
        case 0:
            return 'new';
        case 1:
            return 'waiting';
        case 2:
            return 'progress';
        case 3:
            return 'replied';
        case 4:
        case 5:
            return 'closed';
        default:
            return 'open';
    }
};

$percent = static function ($value, $total) {
    $total = (int) $total;
    if ($total <= 0) {
        return 0;
    }
    return min(100, max(0, (int) getJSTicketPHPFunctionsClass()->jsticket_round(((int) $value / $total) * 100)));
};

$hasGraphData = static function ($graph) {
    if (!is_array($graph) || empty($graph['data'])) {
        return false;
    }

    preg_match_all('/,\s*(-?\d+(?:\.\d+)?)(?=\s*(?:,|\]))/', (string) $graph['data'], $matches);
    if (empty($matches[1])) {
        return false;
    }

    foreach ($matches[1] as $number) {
        if (abs((float) $number) > 0.00001) {
            return true;
        }
    }
    return false;
};

$dashboardActions = array();
$addAction = static function (&$actions, $enabled, $title, $description, $url, $icon, $group = 'primary') {
    if ((int) $enabled !== 1) {
        return;
    }
    $actions[] = array(
        'title' => $title,
        'description' => $description,
        'url' => $url,
        'icon' => $icon,
        'group' => $group,
    );
};


    $addAction($dashboardActions, $this->config['cplink_openticket_user'] ?? 0, Text::_('Submit Ticket'), Text::_('Create a new support request.'), $componentLink('ticket', 'formticket'), 'add-ticket-icon.png', 'primary');
    $addAction($dashboardActions, $this->config['cplink_myticket_user'] ?? 0, Text::_('My Tickets'), Text::_('Track your submitted support requests.'), $componentLink('ticket', 'mytickets'), 'tickets.png', 'primary');
    // `cplink_checkticketstatus_user` is the pre-1.0.3 name; 1.0.3.sql renamed it
    // to `cplink_checkstatus_user`, so the old key no longer exists in the config
    // table. Reading it fell through to `?? 0` and hid Ticket Status permanently,
    // even though install.mysql.sql seeds the setting enabled ('1').
    $addAction($dashboardActions, $this->config['cplink_checkstatus_user'] ?? 0, Text::_('Ticket Status'), Text::_('Check ticket progress by ticket ID.'), $componentLink('ticket', 'ticketstatus'), 'report.png', 'primary');
    $addAction($dashboardActions, $this->config['cplink_userdata_user'] ?? 0, Text::_('User Data'), Text::_('Request personal data erasure.'), $componentLink('gdpr', 'adderasedatarequest'), 'user-data.png', 'account');

    $redirect = Route::_(Uri::root() . 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=' . $this->Itemid, false);
    $returnCode = getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($redirect);
    $encodedRedirect = '&amp;return=' . $returnCode;
    if ($isLoggedIn && !empty($stats)) {
        $dashboardActions[] = array(
            'title' => Text::_('Log Out'),
            'description' => Text::_('End this support center session.'),
            'url' => 'index.php?option=com_jssupportticket&c=jssupportticket&task=logout&Itemid=' . (int) $this->Itemid,
            'icon' => 'logout.png',
            'group' => 'account',
        );
    } else {
        $dashboardActions[] = array(
            'title' => Text::_('Log In'),
            'description' => Text::_('Sign in to view your ticket history.'),
            'url' => 'index.php?option=com_users&view=login' . $encodedRedirect,
            'icon' => 'login.png',
            'group' => 'account',
        );
    }


$groupTitles = array(
    'primary' => Text::_('Quick Actions'),
    'people' => Text::_('People And Access'),
    'account' => Text::_('Account'),
);

$groupedActions = array();
foreach ($dashboardActions as $action) {
    $groupedActions[$action['group']][] = $action;
}

$heroActions = array();
foreach ($dashboardActions as $action) {
    if ($action['group'] === 'primary') {
        $heroActions[] = $action;
    }
    if (count($heroActions) >= 2) {
        break;
    }
}

$ticketChart = $this->result_graph['stack_chart_horizontal'] ?? array();

// Staff-only dashboard toggles. Both are seeded '1' in install.mysql.sql (and in
// 1.1.5.sql for upgrades), so a missing key defaults to visible - hiding core
// dashboard content on a stale config row is the worse failure. Non-staff are
// unaffected: these are *_staff settings and never applied to the customer view.

$statCards = array(
    array('label' => Text::_('Open'), 'count' => $openTickets, 'percent' => $percent($openTickets, $totalTickets), 'class' => 'open'),
    array('label' => Text::_('Closed'), 'count' => $closedTickets, 'percent' => $percent($closedTickets, $totalTickets), 'class' => 'closed'),
    array('label' => Text::_('Answered'), 'count' => $answeredTickets, 'percent' => $percent($answeredTickets, $totalTickets), 'class' => 'answered'),
    array('label' => Text::_('All Tickets'), 'count' => $totalTickets, 'percent' => $totalTickets > 0 ? 100 : 0, 'class' => 'all'),
);
?>

<?php if ($showTicketChart) { ?>
<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}" defer></script>
<script>
    window.addEventListener('load', function () {
        if (!window.google || !google.charts) {
            return;
        }
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(function () {
            var target = document.getElementById('jsst-cp-ticket-chart');
            if (!target) {
                return;
            }
            var data = google.visualization.arrayToDataTable([
                <?php
                echo $this->result_graph['stack_chart_horizontal']['title'] . ',';
                echo $this->result_graph['stack_chart_horizontal']['data'];
                ?>
            ]);
            var view = new google.visualization.DataView(data);
            var options = {
                height: 360,
                legend: { position: 'top', maxLines: 3 },
                bar: { groupWidth: '70%' },
                isStacked: true,
                chartArea: { left: 90, top: 55, width: '78%', height: '68%' },
                colors: <?php echo $this->result_graph['stack_chart_horizontal']['colors']; ?>
            };
            var chart = new google.visualization.BarChart(target);
            chart.draw(view, options);
        });
    });
</script>
<?php } ?>

<div class="jsst-cp-modern">
    <?php if ((int) ($this->config['cur_location'] ?? 0) === 1) { ?>
        <div id="jsst-wrapper-top" class="jsst-cp-breadcrumb-card">
            <div id="jsst-wrapper-top-left">
                <div id="jsst-breadcrunbs">
                    <ul>
                        <li><?php echo Text::_('Dashboard'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    <?php } ?>

    <section class="jsst-cp-hero" aria-label="<?php echo Text::_('Dashboard'); ?>">
        <div class="jsst-cp-hero-content">
            <span class="jsst-cp-eyebrow"><?php echo Text::_('Support Center'); ?></span>
            <h1><?php echo Text::_('Dashboard'); ?></h1>
            <p><?php echo Text::_('Create tickets, track replies, check ticket status and find support resources from one place.'); ?></p>
        </div>
        <?php if (!empty($heroActions)) { ?>
            <div class="jsst-cp-hero-actions">
                <?php foreach ($heroActions as $index => $action) { ?>
                    <a class="<?php echo $index === 0 ? 'jsst-cp-primary-btn' : 'jsst-cp-secondary-btn'; ?>" href="<?php echo $escape($action['url']); ?>"><?php echo $action['title']; ?></a>
                <?php } ?>
            </div>
        <?php } ?>
    </section>

    <?php if ($showStatCards) { ?>
    <section class="jsst-cp-stats" aria-label="<?php echo Text::_('Ticket Statistics'); ?>">
        <?php foreach ($statCards as $card) { ?>
            <a class="jsst-cp-stat jsst-cp-stat-<?php echo $escape($card['class']); ?>" href="<?php echo $componentLink('ticket', 'mytickets'); ?>">
                <span class="jsst-cp-stat-icon"><?php echo mb_strtoupper(mb_substr($card['label'], 0, 1, 'UTF-8'), 'UTF-8'); ?></span>
                <span class="jsst-cp-stat-label"><?php echo $card['label']; ?></span>
                <strong><?php echo (int) $card['count']; ?></strong>
                <span class="jsst-cp-stat-bar"><span style="width:<?php echo (int) $card['percent']; ?>%"></span></span>
            </a>
        <?php } ?>
    </section>
    <?php } ?>

    <?php foreach ($groupTitles as $groupKey => $groupTitle) {
        if (empty($groupedActions[$groupKey])) {
            continue;
        }
    ?>
        <section class="jsst-cp-section jsst-cp-actions-section jsst-cp-section-<?php echo $escape($groupKey); ?>">
            <div class="jsst-cp-section-head">
                <div>
                    <span class="jsst-cp-mini-label"><?php echo Text::_('Dashboard Links'); ?></span>
                    <h2><?php echo $groupTitle; ?></h2>
                </div>
            </div>
            <div class="jsst-cp-action-grid">
                <?php foreach ($groupedActions[$groupKey] as $action) { ?>
                    <a class="jsst-cp-action-card" href="<?php echo $escape($action['url']); ?>">
                        <span class="jsst-cp-action-icon"><img src="<?php echo $dashboardIconBase . $escape($action['icon']); ?>" alt="" loading="lazy" /></span>
                        <span class="jsst-cp-action-copy">
                            <strong><?php echo $action['title']; ?></strong>
                            <small><?php echo $action['description']; ?></small>
                        </span>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <?php if ($showTicketChart) { ?>
        <section class="jsst-cp-section jsst-cp-chart-section">
            <div class="jsst-cp-section-head">
                <div>
                    <span class="jsst-cp-mini-label"><?php echo Text::_('Reports'); ?></span>
                    <h2><?php echo Text::_('Ticket Statistics'); ?></h2>
                </div>
            </div>
            <div class="jsst-cp-chart-wrap">
                <div id="jsst-cp-ticket-chart"></div>
            </div>
        </section>
    <?php } ?>

    <section class="jsst-cp-section jsst-cp-latest-section">
        <div class="jsst-cp-section-head">
            <div>
                <span class="jsst-cp-mini-label"><?php echo Text::_('Recent Activity'); ?></span>
                <h2><?php echo Text::_('Latest Updates'); ?></h2>
            </div>
        </div>
        <div class="jsst-cp-latest-grid <?php echo $showLatestSidePanels ? '' : 'jsst-cp-latest-grid-full'; ?>">
            <?php if ($showLatestTickets) { ?>
            <div class="jsst-cp-panel jsst-cp-latest-tickets">
                <div class="jsst-cp-panel-head">
                    <h3><?php echo Text::_('Latest Tickets'); ?></h3>
                    <a href="<?php echo $componentLink('ticket', 'mytickets'); ?>"><?php echo Text::_('View All Tickets'); ?></a>
                </div>
                <?php if (!empty($latestTickets)) { ?>
                    <div class="jsst-cp-ticket-list">
                        <?php foreach ($latestTickets as $ticket) {
                            $ticketId = (int) $getValue($ticket, 'ticketid', (int) $getValue($ticket, 'id', 0));
                            $ticketLink = $ticketId > 0 ? $componentLink('ticket', 'ticketdetail', array('id' => $ticketId)) : '#';
                            $status = $getValue($ticket, 'status', 0);
                            $priorityColour = $getValue($ticket, 'prioritycolour', '#2563eb');
                        ?>
                            <article class="jsst-cp-ticket-item">
                                <div class="jsst-cp-ticket-avatar"><img src="<?php echo $imageBase; ?>user.png" alt="" loading="lazy" /></div>
                                <div class="jsst-cp-ticket-body">
                                    <a class="jsst-cp-ticket-title" href="<?php echo $escape($ticketLink); ?>"><?php echo $escape(Text::_($getValue($ticket, 'subject', Text::_('Ticket')))); ?></a>
                                    <span><?php echo Text::_('Department'); ?>: <?php echo $escape(Text::_($getValue($ticket, 'departmentname', '-'))); ?></span>
                                    <span><?php echo $escape(Text::_($getValue($ticket, 'name', ''))); ?></span>
                                </div>
                                <div class="jsst-cp-ticket-meta">
                                    <span class="jsst-cp-status jsst-cp-status-<?php echo $escape($statusClass($status)); ?>"><?php echo $statusLabel($status); ?></span>
                                    <?php if ($getValue($ticket, 'priority', '') !== '') { ?>
                                        <span class="jsst-cp-priority" style="--jsst-priority-color:<?php echo $escape($priorityColour); ?>"><?php echo $escape(Text::_($getValue($ticket, 'priority'))); ?></span>
                                    <?php } ?>
                                    <?php if ($getValue($ticket, 'created', '') !== '') { ?>
                                        <small><?php echo HTMLHelper::_('date', $getValue($ticket, 'created'), 'd M, Y'); ?></small>
                                    <?php } ?>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="jsst-cp-empty">
                        <span class="jsst-cp-empty-icon" aria-hidden="true"><img src="<?php echo $imageBase; ?>error/no-record-icon.png" alt="" loading="lazy" /></span>
                        <strong><?php echo Text::_('No tickets found'); ?></strong>
                        <p><?php echo Text::_('There are no recent tickets to show.'); ?></p>
                    </div>
                <?php } ?>
            </div>
            <?php } ?>

            <?php if ($showLatestSidePanels) { ?>
            <div class="jsst-cp-side-panels">
                <?php if (!empty($latestAnnouncements)) { ?>
                    <div class="jsst-cp-panel">
                        <div class="jsst-cp-panel-head">
                            <h3><?php echo Text::_('Latest Announcements'); ?></h3>
                            <a href="<?php echo $componentLink('announcements', 'userannouncements'); ?>"><?php echo Text::_('View All'); ?></a>
                        </div>
                        <div class="jsst-cp-link-list">
                            <?php foreach (array_slice($latestAnnouncements, 0, 5) as $announcement) { ?>
                                <a href="<?php echo $componentLink('announcements', 'userannouncementdetail', array('id' => (int) $getValue($announcement, 'id', 0))); ?>"><?php echo $escape(Text::_($getValue($announcement, 'title', Text::_('Announcement')))); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!empty($latestKnowledgebase)) { ?>
                    <div class="jsst-cp-panel">
                        <div class="jsst-cp-panel-head">
                            <h3><?php echo Text::_('Latest Knowledge Base'); ?></h3>
                            <a href="<?php echo $componentLink('knowledgebase', 'userarticles'); ?>"><?php echo Text::_('View All'); ?></a>
                        </div>
                        <div class="jsst-cp-link-list">
                            <?php foreach (array_slice($latestKnowledgebase, 0, 5) as $knowledge) { ?>
                                <a href="<?php echo $componentLink('knowledgebase', 'usercatarticledetails', array('id' => (int) $getValue($knowledge, 'id', 0))); ?>"><?php echo $escape(Text::_($getValue($knowledge, 'subject', Text::_('Knowledge Base')))); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!empty($latestDownloads)) { ?>
                    <div class="jsst-cp-panel">
                        <div class="jsst-cp-panel-head">
                            <h3><?php echo Text::_('Latest Downloads'); ?></h3>
                            <a href="<?php echo $componentLink('downloads', 'userdownloads'); ?>"><?php echo Text::_('View All'); ?></a>
                        </div>
                        <div class="jsst-cp-link-list">
                            <?php foreach (array_slice($latestDownloads, 0, 5) as $download) { ?>
                                <a href="<?php echo $componentLink('downloads', 'userdownloads'); ?>"><?php echo $escape(Text::_($getValue($download, 'title', Text::_('Download')))); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

            </div>
            <?php } ?>
        </div>
    </section>

    <div id="js-ticket-main-black-background" style="display:none;" aria-hidden="true"></div>
    <div id="js-ticket-main-popup" style="display:none;" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="js-ticket-popup-title">
        <span id="js-ticket-popup-title"></span>
        <button type="button" id="js-ticket-popup-close-button" aria-label="<?php echo Text::_('Close'); ?>"><img src="components/com_jssupportticket/include/images/popup-close.png" alt="" aria-hidden="true" /></button>
        <div id="js-ticket-main-content"></div>
        <div id="js-ticket-main-downloadallbtn"></div>
    </div>
</div>
