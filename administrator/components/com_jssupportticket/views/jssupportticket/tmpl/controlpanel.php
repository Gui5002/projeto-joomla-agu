<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company: Buruj Solutions
 * Project: JS Tickets
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/jsst-dashboard-v2.css?v=56');
$document->addScript('https://www.gstatic.com/charts/loader.js');

if (!function_exists('jsst_dashboard_escape')) {
    function jsst_dashboard_escape($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('jsst_dashboard_label')) {
    /**
     * Escapes a tile label and appends the Pro marker as real markup.
     * The marker cannot be baked into the label string itself, because every
     * label on this page is passed through jsst_dashboard_escape() on output
     * and the span would be printed literally.
     */
    function jsst_dashboard_label($value, $pro = false) {
        $out = jsst_dashboard_escape($value);
        if ($pro) {
            $out .= '<span class="jsst-pro-star" aria-hidden="true">*</span>';
        }
        return $out;
    }
}

if (!function_exists('jsst_dashboard_excerpt')) {
    function jsst_dashboard_excerpt($value, $length = 120) {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)));
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - 3) . '...';
    }
}

if (!function_exists('jsst_dashboard_count')) {
    function jsst_dashboard_count($value, $showCounts = 1) {
        if (!$showCounts) {
            return '-';
        }
        return number_format((int) $value);
    }
}

if (!function_exists('jsst_dashboard_color')) {
    function jsst_dashboard_color($value, $fallback = '#64748b') {
        $color = trim((string) $value);
        if (preg_match('/^#[0-9a-fA-F]{3,6}$/', $color)) {
            return $color;
        }
        return $fallback;
    }
}

if (!function_exists('jsst_dashboard_status')) {
    function jsst_dashboard_status($status) {
        switch ((int) $status) {
            case 0:
                return Text::_('New');
            case 1:
                return Text::_('Waiting for staff reply');
            case 2:
                return Text::_('In progress');
            case 3:
                return Text::_('Waiting for customer reply');
            case 4:
                return Text::_('Closed');
            default:
                return Text::_('Open');
        }
    }
}


$ticketTotal = isset($this->result['ticket_total']) && is_array($this->result['ticket_total']) ? $this->result['ticket_total'] : array();
$totalTickets = (int) ($ticketTotal['totalticket'] ?? 0);
$openTickets = (int) ($ticketTotal['openticket'] ?? 0);
$pendingTickets = (int) ($ticketTotal['pendingticket'] ?? 0);
$overdueTickets = (int) ($ticketTotal['overdueticket'] ?? 0);
$answeredTickets = (int) ($ticketTotal['answeredticket'] ?? 0);
$showCounts = isset($this->config['show_count_tickets']) ? (int) $this->config['show_count_tickets'] : 1;

$adminSnapshot = isset($this->result['admin_snapshot']) && is_array($this->result['admin_snapshot']) ? $this->result['admin_snapshot'] : array();
$todaySummary = isset($this->result['today_summary']) && is_array($this->result['today_summary']) ? $this->result['today_summary'] : array();
$peopleSummary = isset($this->result['people_summary']) && is_array($this->result['people_summary']) ? $this->result['people_summary'] : array();
$departmentActivity = isset($this->result['department_activity']) && is_array($this->result['department_activity']) ? $this->result['department_activity'] : array();
$priorityBreakdown = isset($this->result['priority_breakdown']) && is_array($this->result['priority_breakdown']) ? $this->result['priority_breakdown'] : array();
$staffActivity = isset($this->result['staff_activity']) && is_array($this->result['staff_activity']) ? $this->result['staff_activity'] : array();
$attentionTickets = isset($this->result['attention_tickets']) && is_array($this->result['attention_tickets']) ? $this->result['attention_tickets'] : array();

$percentage = function ($count) use ($totalTickets) {
    if ($totalTickets <= 0) {
        return 0;
    }
    return max(0, min(100, (int) round(((int) $count / $totalTickets) * 100)));
};

$dateFormat = $this->config['date_format'] ?? 'Y-m-d';
$curdate = HTMLHelper::_('date', date('Y-m-d'), 'Y-m-d');
$fromdate = HTMLHelper::_('date', date('Y-m-d', getJSTicketPHPFunctionsClass()->jsticket_strtotime('now -1 month')), 'Y-m-d');

$stackChartTitle = $this->result['stack_chart_horizontal']['title'] ?? "['Status','High','Low','Normal']";
$stackChartData = $this->result['stack_chart_horizontal']['data'] ?? "['No Data',0,0,0]";
$stackChartColors = $this->result['stack_chart_horizontal']['colors'] ?? "['#ef4444','#0ea5e9','#22c55e']";
$todayChartTitle = $this->result['today_ticket_chart']['title'] ?? "['Status','Tickets']";
$todayChartData = $this->result['today_ticket_chart']['data'] ?? "['No Data',0]";

$quickLinks = array(
    array('icon' => '+', 'label' => Text::_('Create Ticket'), 'href' => 'index.php?option=com_jssupportticket&c=ticket&layout=formticket'),
    array('icon' => 'T', 'label' => Text::_('Tickets'), 'href' => 'index.php?option=com_jssupportticket&c=ticket&layout=tickets'),
    array('icon' => 'D', 'label' => Text::_('Departments'), 'href' => 'index.php?option=com_jssupportticket&c=department&layout=departments'),
    array('icon' => 'S', 'label' => Text::_('Staff Members'), 'pro' => true, 'href' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion&feature=staff'),
    array('icon' => 'R', 'label' => Text::_('Reports'), 'pro' => true, 'href' => 'index.php?option=com_jssupportticket&jssupportticket&layout=proversion&feature=reports'),
    array('icon' => '@', 'label' => Text::_('Email Templates'), 'href' => 'index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=ew-tk'),
);

$overviewCards = array(
    array('icon' => '●', 'label' => Text::_('Active Tickets'), 'value' => $adminSnapshot['active'] ?? $openTickets, 'meta' => Text::_('Currently open queue'), 'href' => 'index.php?option=com_jssupportticket&c=ticket&layout=tickets'),
    array('icon' => '↩', 'label' => Text::_('Pending Reply'), 'value' => $pendingTickets, 'meta' => Text::_('Waiting for staff action'), 'href' => 'index.php?option=com_jssupportticket&c=ticket&layout=tickets'),
    array('icon' => '!', 'label' => Text::_('Unassigned'), 'value' => $adminSnapshot['unassigned'] ?? 0, 'meta' => Text::_('Need owner assignment'), 'href' => 'index.php?option=com_jssupportticket&c=ticket&layout=tickets'),
    array('icon' => '✓', 'label' => Text::_('Closed'), 'value' => $adminSnapshot['closed'] ?? 0, 'meta' => Text::_('Resolved tickets'), 'href' => 'index.php?option=com_jssupportticket&c=ticket&layout=tickets'),
    array('icon' => 'D', 'label' => Text::_('Departments'), 'value' => $peopleSummary['departments'] ?? 0, 'meta' => Text::_('Support routing'), 'href' => 'index.php?option=com_jssupportticket&c=department&layout=departments'),
    array('icon' => 'S', 'label' => Text::_('Staff'), 'pro' => true, 'value' => $peopleSummary['staff'] ?? 0, 'meta' => Text::_('Team accounts'), 'href' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion&feature=staff'),
    array('icon' => '★', 'label' => Text::_('Feedback'), 'pro' => true, 'value' => $peopleSummary['feedback'] ?? 0, 'meta' => Text::_('Customer responses'), 'href' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion&feature=feedback'),
    array('icon' => '@', 'label' => Text::_('Email Tickets'), 'pro' => true, 'value' => $adminSnapshot['viaemail'] ?? 0, 'meta' => Text::_('Created via email'), 'href' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion&feature=ticketviaemail'),
);

$todayCards = array(
    array('label' => Text::_('New Today'), 'value' => $todaySummary['new_today'] ?? 0),
    array('label' => Text::_('Replies Today'), 'value' => $todaySummary['replies_today'] ?? 0),
    array('label' => Text::_('Closed Today'), 'value' => $todaySummary['closed_today'] ?? 0),
);

$modules = array(
    array('kicker' => Text::_('Tickets'), 'title' => Text::_('Ticket Queue'), 'desc' => Text::_('Review open, pending, answered, overdue, merged, and closed tickets.'), 'href' => 'index.php?option=com_jssupportticket&c=ticket&layout=tickets', 'cta' => Text::_('Open tickets')),
    array('kicker' => Text::_('People'), 'title' => Text::_('Staff Members'), 'pro' => true, 'desc' => Text::_('Manage staff accounts, roles, visibility, and ticket responsibilities.'), 'href' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion&feature=staff', 'cta' => Text::_('Manage staff')),
    array('kicker' => Text::_('Flow'), 'title' => Text::_('Departments'), 'desc' => Text::_('Control ticket routing, public departments, signatures, and email behavior.'), 'href' => 'index.php?option=com_jssupportticket&c=department&layout=departments', 'cta' => Text::_('Manage departments')),
    array('kicker' => Text::_('System'), 'title' => Text::_('Configurations'), 'desc' => Text::_('Configure ticket settings, email options, menus, and preferences.'), 'href' => 'index.php?option=com_jssupportticket&c=config&layout=config', 'cta' => Text::_('Open settings')),
);
?>
<script>
(function () {
    function drawJsstDashboardCharts() {
        if (!window.google || !google.visualization) {
            return;
        }
        var stackEl = document.getElementById('jsst_dashboard_stack_chart');
        if (stackEl) {
            var stackData = google.visualization.arrayToDataTable([
                <?php echo $stackChartTitle; ?>,
                <?php echo $stackChartData; ?>
            ]);
            var stackChart = new google.visualization.BarChart(stackEl);
            stackChart.draw(stackData, {
                height: 286,
                legend: { position: 'top', maxLines: 3, textStyle: { color: '#64748b', fontSize: 12 } },
                chartArea: { width: '74%', height: '70%', left: 90, top: 42 },
                bar: { groupWidth: '58%' },
                isStacked: true,
                backgroundColor: 'transparent',
                hAxis: { textStyle: { color: '#94a3b8' }, gridlines: { color: '#eef2f7' } },
                vAxis: { textStyle: { color: '#64748b' } },
                colors: <?php echo $stackChartColors; ?>
            });
        }
        var todayEl = document.getElementById('jsst_dashboard_today_chart');
        if (todayEl) {
            var todayData = google.visualization.arrayToDataTable([
                <?php echo $todayChartTitle; ?>,
                <?php echo $todayChartData; ?>
            ]);
            var todayChart = new google.visualization.ColumnChart(todayEl);
            todayChart.draw(todayData, {
                height: 142,
                legend: { position: 'right', textStyle: { color: '#64748b', fontSize: 12 } },
                chartArea: { width: '68%', height: '70%', left: 30, top: 16 },
                backgroundColor: 'transparent',
                hAxis: { textPosition: 'none', gridlines: { color: 'transparent' } },
                vAxis: { textStyle: { color: '#94a3b8' }, gridlines: { color: '#eef2f7' } },
                colors: <?php echo $stackChartColors; ?>
            });
        }
    }

    if (window.google && google.charts) {
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(drawJsstDashboardCharts);
        window.addEventListener('resize', function () {
            window.clearTimeout(window.jsstDashboardChartTimer);
            window.jsstDashboardChartTimer = window.setTimeout(drawJsstDashboardCharts, 180);
        });
    }
})();
</script>
<div id="js-tk-admin-wrapper" class="jsst-admin-dashboard-shell">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div class="jsst-dashboard-v2">
            <section class="jsst-dashboard-v2__hero" aria-label="<?php echo jsst_dashboard_escape(Text::_('Control Panel')); ?>">
                <div>
                    <div class="jsst-dashboard-v2__eyebrow"><?php echo Text::_('JS Support Ticket'); ?></div>
                    <h1 class="jsst-dashboard-v2__title"><?php echo Text::_('Control Panel'); ?></h1>
                    <p class="jsst-dashboard-v2__subtitle">
                        <?php echo Text::_('Monitor ticket activity, workload, departments, priorities, recent replies, and support settings from one place.'); ?>
                    </p>
                </div>
                <div class="jsst-dashboard-v2__actions">
                    <a class="jsst-dashboard-v2__btn jsst-dashboard-v2__btn--light" href="index.php?option=com_jssupportticket&c=ticket&layout=formticket">+ <?php echo Text::_('Create Ticket'); ?></a>
                    <a class="jsst-dashboard-v2__btn" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets"><?php echo Text::_('All Tickets'); ?></a>
                </div>
            </section>

            <section class="jsst-dashboard-v2__section jsst-dashboard-v2__stats" aria-label="<?php echo jsst_dashboard_escape(Text::_('Ticket Status')); ?>">
                <a class="jsst-dashboard-v2__stat jsst-dashboard-v2__stat--open" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets&lt=1">
                    <span class="jsst-dashboard-v2__stat-icon">O</span>
                    <div class="jsst-dashboard-v2__stat-label"><?php echo Text::_('Open'); ?></div>
                    <div class="jsst-dashboard-v2__stat-value"><?php echo jsst_dashboard_count($openTickets, $showCounts); ?></div>
                    <div class="jsst-dashboard-v2__progress"><span style="width: <?php echo $percentage($openTickets); ?>%"></span></div>
                </a>
                <a class="jsst-dashboard-v2__stat jsst-dashboard-v2__stat--overdue" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets">
                    <span class="jsst-dashboard-v2__stat-icon">!</span>
                    <div class="jsst-dashboard-v2__stat-label"><?php echo jsst_dashboard_label(Text::_('Overdue'), true); ?></div>
                    <div class="jsst-dashboard-v2__stat-value"><?php echo jsst_dashboard_count($overdueTickets, $showCounts); ?></div>
                    <div class="jsst-dashboard-v2__progress"><span style="width: <?php echo $percentage($overdueTickets); ?>%"></span></div>
                </a>
                <a class="jsst-dashboard-v2__stat jsst-dashboard-v2__stat--answered" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets&lt=2">
                    <span class="jsst-dashboard-v2__stat-icon">A</span>
                    <div class="jsst-dashboard-v2__stat-label"><?php echo Text::_('Answered'); ?></div>
                    <div class="jsst-dashboard-v2__stat-value"><?php echo jsst_dashboard_count($answeredTickets, $showCounts); ?></div>
                    <div class="jsst-dashboard-v2__progress"><span style="width: <?php echo $percentage($answeredTickets); ?>%"></span></div>
                </a>
                <a class="jsst-dashboard-v2__stat jsst-dashboard-v2__stat--all" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets&lt=5">
                    <span class="jsst-dashboard-v2__stat-icon">Σ</span>
                    <div class="jsst-dashboard-v2__stat-label"><?php echo Text::_('All Tickets'); ?></div>
                    <div class="jsst-dashboard-v2__stat-value"><?php echo jsst_dashboard_count($totalTickets, $showCounts); ?></div>
                    <div class="jsst-dashboard-v2__progress"><span style="width: <?php echo $totalTickets > 0 ? 100 : 0; ?>%"></span></div>
                </a>
            </section>

            <section class="jsst-dashboard-v2__section jsst-dashboard-v2__mini-stats" aria-label="<?php echo jsst_dashboard_escape(Text::_('Admin Snapshot')); ?>">
                <?php foreach ($overviewCards as $card) { ?>
                    <a class="jsst-dashboard-v2__mini-stat" href="<?php echo jsst_dashboard_escape($card['href']); ?>">
                        <span class="jsst-dashboard-v2__mini-stat-icon"><?php echo jsst_dashboard_escape($card['icon']); ?></span>
                        <span class="jsst-dashboard-v2__mini-stat-copy">
                            <span class="jsst-dashboard-v2__mini-stat-label"><?php echo jsst_dashboard_label($card['label'], !empty($card['pro'])); ?></span>
                            <span class="jsst-dashboard-v2__mini-stat-meta"><?php echo jsst_dashboard_escape($card['meta']); ?></span>
                        </span>
                        <span class="jsst-dashboard-v2__mini-stat-value"><?php echo jsst_dashboard_count($card['value'], $showCounts); ?></span>
                    </a>
                <?php } ?>
            </section>

            <section class="jsst-dashboard-v2__section jsst-dashboard-v2__grid">
                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head">
                        <div>
                            <h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('Ticket Statistics'); ?></h2>
                            <div class="jsst-dashboard-v2__panel-subtitle"><?php echo jsst_dashboard_escape($fromdate . ' - ' . $curdate); ?></div>
                        </div>
                        <a class="jsst-dashboard-v2__panel-link" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion&feature=reports"><?php echo Text::_('Open Reports'); ?> *</a>
                    </div>
                    <div class="jsst-dashboard-v2__panel-body">
                        <div id="jsst_dashboard_stack_chart" class="jsst-dashboard-v2__chart"></div>
                    </div>
                </div>

                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head">
                        <div>
                            <h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_("Today's Activity"); ?></h2>
                            <div class="jsst-dashboard-v2__panel-subtitle"><?php echo Text::_('New work for today'); ?></div>
                        </div>
                    </div>
                    <div class="jsst-dashboard-v2__panel-body">
                        <div class="jsst-dashboard-v2__today-metrics">
                            <?php foreach ($todayCards as $card) { ?>
                                <div class="jsst-dashboard-v2__today-metric">
                                    <span><?php echo jsst_dashboard_escape($card['label']); ?></span>
                                    <strong><?php echo jsst_dashboard_count($card['value'], $showCounts); ?></strong>
                                </div>
                            <?php } ?>
                        </div>
                        <div id="jsst_dashboard_today_chart" class="jsst-dashboard-v2__today-chart"></div>
                        <div class="jsst-dashboard-v2__quicklinks">
                            <?php foreach ($quickLinks as $link) { ?>
                                <a class="jsst-dashboard-v2__quicklink" href="<?php echo jsst_dashboard_escape($link['href']); ?>">
                                    <span class="jsst-dashboard-v2__quicklink-left"><span class="jsst-dashboard-v2__quicklink-icon"><?php echo jsst_dashboard_escape($link['icon']); ?></span><?php echo jsst_dashboard_label($link['label'], !empty($link['pro'])); ?></span>
                                    <span>→</span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="jsst-dashboard-v2__section jsst-dashboard-v2__module-grid">
                <?php foreach ($modules as $module) { ?>
                    <a class="jsst-dashboard-v2__module" href="<?php echo jsst_dashboard_escape($module['href']); ?>">
                        <span>
                            <span class="jsst-dashboard-v2__module-kicker"><?php echo jsst_dashboard_escape($module['kicker']); ?></span>
                            <span class="jsst-dashboard-v2__module-title"><?php echo jsst_dashboard_label($module['title'], !empty($module['pro'])); ?></span>
                            <span class="jsst-dashboard-v2__module-desc"><?php echo jsst_dashboard_escape($module['desc']); ?></span>
                        </span>
                        <span class="jsst-dashboard-v2__module-cta"><?php echo jsst_dashboard_escape($module['cta']); ?> →</span>
                    </a>
                <?php } ?>
            </section>

            <section class="jsst-dashboard-v2__section jsst-dashboard-v2__data-grid">
                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head">
                        <div>
                            <h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('Needs Attention'); ?></h2>
                            <div class="jsst-dashboard-v2__panel-subtitle"><?php echo Text::_('Overdue, unassigned, or waiting tickets'); ?></div>
                        </div>
                        <a class="jsst-dashboard-v2__panel-link" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets"><?php echo Text::_('View Queue'); ?> →</a>
                    </div>
                    <div class="jsst-dashboard-v2__panel-body jsst-dashboard-v2__list">
                        <?php if (!empty($attentionTickets)) { ?>
                            <?php foreach ($attentionTickets as $ticket) {
                                $ticketId = isset($ticket->id) ? (int) $ticket->id : 0;
                                $ticketLink = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $ticketId;
                                $priorityColour = jsst_dashboard_color($ticket->prioritycolour ?? '');
                                $departmentName = !empty($ticket->departmentname) ? $ticket->departmentname : Text::_('Unassigned Department');
                            ?>
                                <div class="jsst-dashboard-v2__attention-ticket">
                                    <div>
                                        <div class="jsst-dashboard-v2__ticket-title"><a href="<?php echo jsst_dashboard_escape($ticketLink); ?>"><?php echo jsst_dashboard_escape($ticket->subject ?? Text::_('Ticket')); ?></a></div>
                                        <div class="jsst-dashboard-v2__ticket-meta">
                                            <span><?php echo Text::_('From') . ': ' . jsst_dashboard_escape($ticket->name ?? ''); ?></span>
                                            <span><?php echo Text::_('Department') . ': ' . jsst_dashboard_escape($departmentName); ?></span>
                                            <?php if (!empty($ticket->created)) { ?><span><?php echo Text::_('Created') . ': ' . jsst_dashboard_escape(HTMLHelper::_('date', $ticket->created, $dateFormat)); ?></span><?php } ?>
                                        </div>
                                    </div>
                                    <div class="jsst-dashboard-v2__attention-badges">
                                        <?php if ((int) ($ticket->isoverdue ?? 0) === 1) { ?><span class="jsst-dashboard-v2__badge jsst-dashboard-v2__badge--danger"><?php echo Text::_('Overdue'); ?></span><?php } ?>
                                        <span class="jsst-dashboard-v2__badge"><?php echo jsst_dashboard_escape(jsst_dashboard_status($ticket->status ?? 0)); ?></span>
                                        <span class="jsst-dashboard-v2__priority" style="background-color: <?php echo jsst_dashboard_escape($priorityColour); ?>;"><?php echo jsst_dashboard_escape(Text::_($ticket->priority ?? '')); ?></span>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="jsst-dashboard-v2__empty"><span class="jsst-dashboard-v2__empty-icon">✓</span><strong><?php echo Text::_('No tickets need attention right now.'); ?></strong></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head">
                        <div>
                            <h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('Department Activity'); ?></h2>
                            <div class="jsst-dashboard-v2__panel-subtitle"><?php echo Text::_('Top active departments'); ?></div>
                        </div>
                    </div>
                    <div class="jsst-dashboard-v2__panel-body">
                        <?php if (!empty($departmentActivity)) { ?>
                            <div class="jsst-dashboard-v2__metric-list">
                                <?php foreach ($departmentActivity as $department) {
                                    $departmentTotal = max(1, (int) ($department->totalticket ?? 0));
                                    $departmentActive = (int) ($department->active ?? 0);
                                ?>
                                    <div class="jsst-dashboard-v2__metric-row">
                                        <div>
                                            <strong><?php echo jsst_dashboard_escape($department->departmentname ?? Text::_('Department')); ?></strong>
                                            <span><?php echo jsst_dashboard_count($department->overdue ?? 0, $showCounts) . ' ' . Text::_('overdue'); ?></span>
                                        </div>
                                        <em><?php echo jsst_dashboard_count($departmentActive, $showCounts); ?></em>
                                        <div class="jsst-dashboard-v2__bar"><span style="width: <?php echo min(100, round(($departmentActive / $departmentTotal) * 100)); ?>%"></span></div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="jsst-dashboard-v2__empty"><span class="jsst-dashboard-v2__empty-icon">D</span><strong><?php echo Text::_('No department activity yet.'); ?></strong></div>
                        <?php } ?>
                    </div>
                </div>
            </section>

            <section class="jsst-dashboard-v2__section jsst-dashboard-v2__content-grid jsst-dashboard-v2__content-grid--admin">
                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head"><h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('Team Workload'); ?></h2></div>
                    <div class="jsst-dashboard-v2__panel-body">
                        <?php if (!empty($staffActivity)) { ?>
                            <div class="jsst-dashboard-v2__metric-list">
                                <?php foreach ($staffActivity as $staff) { ?>
                                    <div class="jsst-dashboard-v2__metric-row jsst-dashboard-v2__metric-row--compact">
                                        <div>
                                            <strong><?php echo jsst_dashboard_escape(jsst_dashboard_staff_name($staff)); ?></strong>
                                            <span><?php echo jsst_dashboard_count($staff->overdue ?? 0, $showCounts) . ' ' . Text::_('overdue'); ?></span>
                                        </div>
                                        <em><?php echo jsst_dashboard_count($staff->active ?? 0, $showCounts); ?></em>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="jsst-dashboard-v2__empty"><span class="jsst-dashboard-v2__empty-icon">S</span><strong><?php echo Text::_('No staff activity yet.'); ?></strong></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head"><h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('Priority Breakdown'); ?></h2></div>
                    <div class="jsst-dashboard-v2__panel-body">
                        <?php if (!empty($priorityBreakdown)) { ?>
                            <div class="jsst-dashboard-v2__metric-list">
                                <?php foreach ($priorityBreakdown as $priority) { ?>
                                    <div class="jsst-dashboard-v2__metric-row jsst-dashboard-v2__metric-row--compact">
                                        <div>
                                            <strong><span class="jsst-dashboard-v2__dot" style="background-color: <?php echo jsst_dashboard_escape(jsst_dashboard_color($priority->prioritycolour ?? '')); ?>;"></span><?php echo jsst_dashboard_escape(Text::_($priority->priority ?? Text::_('Priority'))); ?></strong>
                                            <span><?php echo jsst_dashboard_count($priority->totalticket ?? 0, $showCounts) . ' ' . Text::_('total'); ?></span>
                                        </div>
                                        <em><?php echo jsst_dashboard_count($priority->active ?? 0, $showCounts); ?></em>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="jsst-dashboard-v2__empty"><span class="jsst-dashboard-v2__empty-icon">P</span><strong><?php echo Text::_('No priority data yet.'); ?></strong></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head"><h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('System Setup'); ?></h2></div>
                    <div class="jsst-dashboard-v2__panel-body">
                        <div class="jsst-dashboard-v2__setup-grid">
                            <a href="index.php?option=com_jssupportticket&c=department&layout=departments"><span><?php echo Text::_('Active Departments'); ?></span><strong><?php echo jsst_dashboard_count($peopleSummary['active_departments'] ?? 0, $showCounts); ?></strong></a>
                            <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion&feature=staff"><span><?php echo jsstProCfg('Active Staff'); ?></span><strong><?php echo jsst_dashboard_count($peopleSummary['active_staff'] ?? 0, $showCounts); ?></strong></a>
                            <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion&feature=feedback"><span><?php echo jsstProCfg('Average Rating'); ?></span><strong><?php echo $showCounts ? jsst_dashboard_escape($peopleSummary['average_rating'] ?? '-') : '-'; ?></strong></a>
                            <a href="index.php?option=com_jssupportticket&c=ticket&layout=tickets"><span><?php echo jsst_dashboard_label(Text::_('Merged Tickets'), true); ?></span><strong><?php echo jsst_dashboard_count($adminSnapshot['merged'] ?? 0, $showCounts); ?></strong></a>
                        </div>
                    </div>
                </div>
            </section>

            <?php if (!empty($this->result['tickets']) && is_array($this->result['tickets'])) { ?>
                <section class="jsst-dashboard-v2__section jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head">
                        <div>
                            <h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('Latest Tickets'); ?></h2>
                            <div class="jsst-dashboard-v2__panel-subtitle"><?php echo Text::_('Recent ticket activity'); ?></div>
                        </div>
                        <a class="jsst-dashboard-v2__badge" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets"><?php echo Text::_('View All Tickets'); ?></a>
                    </div>
                    <div class="jsst-dashboard-v2__panel-body jsst-dashboard-v2__list">
                        <?php foreach ($this->result['tickets'] as $ticket) {
                            $ticketId = isset($ticket->id) ? (int) $ticket->id : 0;
                            $ticketLink = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $ticketId;
                            $priorityColour = jsst_dashboard_color($ticket->prioritycolour ?? '');
                        ?>
                            <div class="jsst-dashboard-v2__ticket">
                                <div>
                                    <div class="jsst-dashboard-v2__ticket-title"><a href="<?php echo jsst_dashboard_escape($ticketLink); ?>"><?php echo jsst_dashboard_escape($ticket->subject ?? Text::_('Ticket')); ?></a></div>
                                    <div class="jsst-dashboard-v2__ticket-meta">
                                        <span><?php echo Text::_('From') . ': ' . jsst_dashboard_escape($ticket->name ?? ''); ?></span>
                                        <?php if (!empty($ticket->created)) { ?><span><?php echo Text::_('Created') . ': ' . jsst_dashboard_escape(HTMLHelper::_('date', $ticket->created, $dateFormat)); ?></span><?php } ?>
                                    </div>
                                </div>
                                <span class="jsst-dashboard-v2__badge"><?php echo jsst_dashboard_escape(jsst_dashboard_status($ticket->status ?? 0)); ?></span>
                                <span class="jsst-dashboard-v2__priority" style="background-color: <?php echo jsst_dashboard_escape($priorityColour); ?>;"><?php echo jsst_dashboard_escape(Text::_($ticket->priority ?? '')); ?></span>
                            </div>
                        <?php } ?>
                    </div>
                </section>
            <?php } ?>

            <section class="jsst-dashboard-v2__section jsst-dashboard-v2__content-grid">
                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head"><h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('Latest Downloads'); ?></h2></div>
                    <div class="jsst-dashboard-v2__panel-body">
                        <?php if (!empty($this->latestdownloads)) { ?>
                            <div class="jsst-dashboard-v2__list">
                                <?php foreach ($this->latestdownloads as $download) { ?>
                                    <div class="jsst-dashboard-v2__content-item">
                                        <div class="jsst-dashboard-v2__content-title"><?php echo jsst_dashboard_escape($download->title ?? ''); ?></div>
                                        <div class="jsst-dashboard-v2__content-desc"><?php echo jsst_dashboard_escape(jsst_dashboard_excerpt($download->description ?? '')); ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="jsst-dashboard-v2__empty"><span class="jsst-dashboard-v2__empty-icon">↓</span><strong><?php echo Text::_('No Data'); ?></strong></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head"><h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('Latest Knowledge Base'); ?></h2></div>
                    <div class="jsst-dashboard-v2__panel-body">
                        <?php if (!empty($this->latestknowledgebase)) { ?>
                            <div class="jsst-dashboard-v2__list">
                                <?php foreach ($this->latestknowledgebase as $latestknowledgebase) { ?>
                                    <div class="jsst-dashboard-v2__content-item">
                                        <div class="jsst-dashboard-v2__content-title"><?php echo jsst_dashboard_escape($latestknowledgebase->subject ?? ''); ?></div>
                                        <div class="jsst-dashboard-v2__content-desc"><?php echo jsst_dashboard_escape(jsst_dashboard_excerpt($latestknowledgebase->content ?? '')); ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="jsst-dashboard-v2__empty"><span class="jsst-dashboard-v2__empty-icon">?</span><strong><?php echo Text::_('No Data'); ?></strong></div>
                        <?php } ?>
                    </div>
                </div>

                <div class="jsst-dashboard-v2__panel">
                    <div class="jsst-dashboard-v2__panel-head"><h2 class="jsst-dashboard-v2__panel-title"><?php echo Text::_('Latest Announcements'); ?></h2></div>
                    <div class="jsst-dashboard-v2__panel-body">
                        <?php if (!empty($this->latestannouncement)) { ?>
                            <div class="jsst-dashboard-v2__list">
                                <?php foreach ($this->latestannouncement as $latestannouncement) { ?>
                                    <div class="jsst-dashboard-v2__content-item">
                                        <div class="jsst-dashboard-v2__content-title"><?php echo jsst_dashboard_escape($latestannouncement->title ?? ''); ?></div>
                                        <div class="jsst-dashboard-v2__content-desc"><?php echo jsst_dashboard_escape(jsst_dashboard_excerpt($latestannouncement->description ?? '')); ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="jsst-dashboard-v2__empty"><span class="jsst-dashboard-v2__empty-icon">i</span><strong><?php echo Text::_('No Data'); ?></strong></div>
                        <?php } ?>
                    </div>
                </div>
            </section>

            <section class="jsst-dashboard-v2__section jsst-dashboard-v2__resources">
                <div class="jsst-dashboard-v2__resource">
                    <div class="jsst-dashboard-v2__resource-title"><?php echo Text::_('Enjoying JS Support Ticket?'); ?></div>
                    <div class="jsst-dashboard-v2__resource-text"><?php echo Text::_('Please consider leaving a review on the Joomla Extensions Directory.'); ?></div>
                    <a href="https://extensions.joomla.org/extension/js-support-ticket/" target="_blank" rel="noopener noreferrer"><?php echo Text::_('Joomla Extension Directory'); ?> →</a>
                </div>
                <div class="jsst-dashboard-v2__resource">
                    <div class="jsst-dashboard-v2__resource-title"><?php echo Text::_('Useful Links'); ?></div>
                    <div class="jsst-dashboard-v2__resource-text"><?php echo Text::_('Quick access to important administration areas.'); ?></div>
                    <a href="index.php?option=com_jssupportticket&c=systemerrors&layout=systemerrors"><?php echo Text::_('System Errors'); ?> →</a>
                </div>
            </section>

            <?php
$jsstFooterClass = 'jsst-dashboard-v2__footer';
$jsstFooterId = '';
include_once('components/com_jssupportticket/views/partials/pagefooter.php');
?>
        </div>
    </div>
</div>
