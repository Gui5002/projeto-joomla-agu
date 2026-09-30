<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company: Buruj Solutions
 * Project: JS Tickets
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('behavior.formvalidator');

$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/jsst-ticket-list-v2.css?v=89');
if (!function_exists('jsst_ticketlist_escape')) {
    function jsst_ticketlist_escape($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('jsst_ticketlist_url')) {
    function jsst_ticketlist_url($url) {
        return htmlspecialchars((string) $url, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('jsst_ticketlist_status')) {
    function jsst_ticketlist_status($status) {
        switch ((int) $status) {
            case 0:
                return array('label' => Text::_('New'), 'class' => 'is-new');
            case 1:
                return array('label' => Text::_('Waiting reply'), 'class' => 'is-waiting');
            case 2:
                return array('label' => Text::_('In progress'), 'class' => 'is-progress');
            case 3:
                return array('label' => Text::_('Replied'), 'class' => 'is-replied');
            case 4:
                return array('label' => Text::_('Closed'), 'class' => 'is-closed');
            case 5:
                return array('label' => Text::_('Close due to Merge'), 'class' => 'is-merged');
            default:
                return array('label' => Text::_('Open'), 'class' => 'is-open');
        }
    }
}

if (!function_exists('jsst_ticketlist_initials')) {
    function jsst_ticketlist_initials($name, $email = '') {
        $name = trim((string) $name);
        if ($name !== '') {
            $parts = preg_split('/\s+/', $name);
            $letters = '';
            foreach ($parts as $part) {
                if ($part !== '') {
                    $letters .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
                }
                if (mb_strlen($letters, 'UTF-8') >= 2) {
                    break;
                }
            }
            return $letters !== '' ? $letters : 'U';
        }
        $email = trim((string) $email);
        return $email !== '' ? mb_strtoupper(mb_substr($email, 0, 1, 'UTF-8'), 'UTF-8') : 'U';
    }
}

if (!function_exists('jsst_ticketlist_color')) {
    function jsst_ticketlist_color($color) {
        $color = trim((string) $color);
        if ($color === '') {
            return '#2563eb';
        }
        return preg_replace('/[^#a-zA-Z0-9(),.%\s-]/', '', $color);
    }
}

$dash = '-';
$dateformat = $this->config['date_format'];
$firstdash = getJSTicketPHPFunctionsClass()->jsticket_strpos($dateformat, $dash, 0);
$firstvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, 0, $firstdash);
$firstdash = $firstdash + 1;
$seconddash = getJSTicketPHPFunctionsClass()->jsticket_strpos($dateformat, $dash, $firstdash);
$secondvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, $firstdash, $seconddash - $firstdash);
$seconddash = $seconddash + 1;
$thirdvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, $seconddash, getJSTicketPHPFunctionsClass()->jsticket_strlen($dateformat) - $seconddash);
$js_dateformat = '%' . $firstvalue . $dash . '%' . $secondvalue . $dash . '%' . $thirdvalue;
$useruid = Factory::getApplication()->input->get('uid');
$useruid = ($useruid) != "" ? $useruid : 0;

$showCount = isset($this->config['show_count_tickets']) ? (int) $this->config['show_count_tickets'] : 1;
$totalTickets = isset($this->ticketinfo['mytickets']) ? (int) $this->ticketinfo['mytickets'] : 0;
$openTickets = isset($this->ticketinfo['open']) ? (int) $this->ticketinfo['open'] : 0;
$closedTickets = isset($this->ticketinfo['close']) ? (int) $this->ticketinfo['close'] : 0;
$overdueTickets = isset($this->ticketinfo['isoverdue']) ? (int) $this->ticketinfo['isoverdue'] : 0;
$answeredTickets = isset($this->ticketinfo['isanswered']) ? (int) $this->ticketinfo['isanswered'] : 0;

$openPercentage = $totalTickets > 0 ? getJSTicketPHPFunctionsClass()->jsticket_round(($openTickets / $totalTickets) * 100) : 0;
$closedPercentage = $totalTickets > 0 ? getJSTicketPHPFunctionsClass()->jsticket_round(($closedTickets / $totalTickets) * 100) : 0;
$overduePercentage = $totalTickets > 0 ? getJSTicketPHPFunctionsClass()->jsticket_round(($overdueTickets / $totalTickets) * 100) : 0;
$answeredPercentage = $totalTickets > 0 ? getJSTicketPHPFunctionsClass()->jsticket_round(($answeredTickets / $totalTickets) * 100) : 0;
$allTicketPercentage = $totalTickets > 0 ? 100 : 0;

$sortOrderForUrl = $this->sortlinks['sorton'] . getJSTicketPHPFunctionsClass()->jsticket_strtolower($this->sortlinks['sortorder']);
$ticketListBaseUrl = 'index.php?option=com_jssupportticket&c=ticket&layout=tickets&lt=';
$statusCards = array(
    array('class' => 'is-open', 'label' => Text::_('Open'), 'value' => $openTickets, 'percent' => $openPercentage, 'listtype' => 1),
    array('class' => 'is-closed', 'label' => Text::_('Closed'), 'value' => $closedTickets, 'percent' => $closedPercentage, 'listtype' => 4),
    array('class' => 'is-overdue', 'label' => Text::_('Overdue'), 'value' => $overdueTickets, 'percent' => $overduePercentage, 'listtype' => 3),
    array('class' => 'is-answered', 'label' => Text::_('Answered'), 'value' => $answeredTickets, 'percent' => $answeredPercentage, 'listtype' => 2),
    array('class' => 'is-all', 'label' => Text::_('Ticket List'), 'value' => $totalTickets, 'percent' => $allTicketPercentage, 'listtype' => 5),
);

if ($this->sortorder == 'ASC') {
    $sortImage = 'components/com_jssupportticket/include/images/sort0.png';
} else {
    $sortImage = 'components/com_jssupportticket/include/images/sort1.png';
}

// Resolved in views/common.php - the manifest version when Joomla knows it.
$version = isset($this->versionDisplay) ? (string) $this->versionDisplay : '';
?>

<script>
    function confirmdelete(deletefor) {
        var msg = '';
        if (deletefor == 0) {
            msg = "<?php echo Text::_('Are you sure to delete'); ?>";
        } else if (deletefor == 1) {
            msg = "<?php echo Text::_('Are you sure to enforce delete'); ?>";
        }
        return confirm(msg) == true;
    }

    function getDataForDepandantField(parentf, childf, type) {
        var val = '';
        if (type == 1) {
            val = jQuery('select#' + parentf).val();
        } else if (type == 2) {
            val = jQuery('input[name=' + parentf + ']:checked').val();
        }
        jQuery.post('index.php?option=com_jssupportticket&c=userfields&task=datafordepandantfield&<?php echo Factory::getSession()->getFormToken(); ?>=1', {fvalue: val, child: childf}, function (data) {
            if (data) {
                var d = jQuery.parseJSON(data);
                jQuery('select#' + childf).replaceWith(d);
            }
        });
    }
</script>
<script>
    jQuery(document).ready(function ($) {
        var combinesearch = "<?php echo isset($this->filter_data['iscombinesearch']) ? $this->filter_data['iscombinesearch'] : ''; ?>";
        jQuery('#js-filter-wrapper-toggle-area').hide();
        jQuery('#js-filter-wrapper-toggle-minus').hide();
        if (combinesearch) {
            doVisible();
            jQuery('#js-filter-wrapper-toggle-area').show();
        }
        jQuery('#js-filter-wrapper-toggle-btn').click(function (e) {
            e.preventDefault();
            if (jQuery('#js-filter-wrapper-toggle-plus').is(':visible')) {
                doVisible();
            } else {
                jQuery('#js-filter-wrapper-toggle-ticketid').hide();
                jQuery('#js-filter-wrapper-toggle-minus').hide();
                jQuery('#js-filter-wrapper-toggle-plus').show();
            }
            jQuery('#js-filter-wrapper-toggle-area').toggle();
        });

        var sortby = jQuery('select.js-ticket-sorting-select').val();
        if (sortby != '') {
            jQuery('input#sortby').val(sortby);
        }

        jQuery('select.js-ticket-sorting-select').on('change', function () {
            var sortby = jQuery(this).val();
            jQuery('input#sortby').val(sortby);
            jQuery('form#adminForm').submit();
        });
        jQuery('#jssortbtn').on('click', function () {
            var sortby = jQuery('select.js-ticket-sorting-select').val();
            switch (sortby) {
                case 'subjectdesc': sortby = 'subjectasc'; break;
                case 'subjectasc': sortby = 'subjectdesc'; break;
                case 'prioritydesc': sortby = 'priorityasc'; break;
                case 'priorityasc': sortby = 'prioritydesc'; break;
                case 'ticketiddesc': sortby = 'ticketidasc'; break;
                case 'ticketidasc': sortby = 'ticketiddesc'; break;
                case 'answereddesc': sortby = 'answeredasc'; break;
                case 'answeredasc': sortby = 'answereddesc'; break;
                case 'createddesc': sortby = 'createdasc'; break;
                case 'createdasc': sortby = 'createddesc'; break;
                case 'statusdesc': sortby = 'statusasc'; break;
                case 'statusasc': sortby = 'statusdesc'; break;
            }
            jQuery('input#sortby').val(sortby);
            jQuery('form#adminForm').submit();
        });
        function doVisible() {
            jQuery('#js-filter-wrapper-toggle-ticketid').show();
            jQuery('#js-filter-wrapper-toggle-minus').show();
            jQuery('#js-filter-wrapper-toggle-plus').hide();
        }
    });
</script>

<div id="js-tk-admin-wrapper" class="jsst-ticket-list-shell">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div<?php echo ((int) $this->listtype === 5) ? '' : ' class="jsst-ticket-list-v2"'; ?>>
            <div class="jsst-ticket-hero">
                <div class="jsst-ticket-hero-body">
                    <div>
                        <span class="jsst-ticket-kicker"><?php echo Text::_('Ticket Desk'); ?></span>
                        <h1><?php echo Text::_('Tickets'); ?></h1>
                        <p><?php echo Text::_('Search, filter, and manage customer support tickets.'); ?></p>
                    </div>
                    <div class="jsst-ticket-hero-actions">
                        <?php $createLink = 'index.php?option=' . $this->option . '&c=ticket&task=addnewticket'; ?>
                        <a class="jsst-ticket-primary-action" href="<?php echo jsst_ticketlist_url($createLink); ?>">+ <?php echo Text::_('Create Ticket'); ?></a>
                    </div>
                </div>
            </div>

            <form class="jsst-ticket-admin-form" action="index.php" method="post" name="adminForm" id="adminForm">
                <div class="jsst-ticket-status-grid">
                    <?php foreach ($statusCards as $card) {
                        $statusUrl = $ticketListBaseUrl . (int) $card['listtype'] . '&Itemid=' . $this->Itemid . '&sortby=' . $sortOrderForUrl . '&uid=' . $useruid;
                        $selectedClass = ((int) $this->listtype === (int) $card['listtype']) ? ' is-selected' : '';
                    ?>
                        <a class="jsst-ticket-status-card <?php echo jsst_ticketlist_escape($card['class'] . $selectedClass); ?>" href="<?php echo jsst_ticketlist_url($statusUrl); ?>">
                            <span class="jsst-ticket-status-icon"><?php echo jsst_ticketlist_escape(mb_substr($card['label'], 0, 1, 'UTF-8')); ?></span>
                            <span class="jsst-ticket-status-label"><?php echo jsst_ticketlist_escape($card['label']); ?></span>
                            <strong><?php echo $showCount === 1 ? (int) $card['value'] : '&mdash;'; ?></strong>
                            <span class="jsst-ticket-status-bar"><span style="width: <?php echo (int) $card['percent']; ?>%;"></span></span>
                        </a>
                    <?php } ?>
                </div>

                <div class="jsst-ticket-filter-card">
                    <div class="jsst-ticket-section-head">
                        <div>
                            <h2><?php echo Text::_('Search Tickets'); ?></h2>
                            <p><?php echo Text::_('Use filters or saved views to find tickets quickly.'); ?></p>
                        </div>
                        <span id="js-filter-wrapper-toggle-btn" class="jsst-ticket-filter-toggle">
                            <span id="js-filter-wrapper-toggle-plus"><a href="#"><?php echo Text::_('Show all filters'); ?></a></span>
                            <span id="js-filter-wrapper-toggle-minus"><a href="#"><?php echo Text::_('Show Less'); ?></a></span>
                        </span>
                    </div>

                    <div class="jsst-ticket-saved-views">
                        <div class="jsst-ticket-saved-apply">
                            <select name="saved_view_id" id="saved_view_id">
                                <option value=""><?php echo Text::_('Select saved view'); ?></option>
                                <?php if (!empty($this->savedViews)) { foreach ($this->savedViews as $savedView) { ?>
                                    <option value="<?php echo (int)$savedView->id; ?>"><?php echo jsst_ticketlist_escape($savedView->title); ?></option>
                                <?php } } ?>
                            </select>
                            <button type="button" class="jsst-ticket-saved-btn" onclick="jsstApplySavedView();"><?php echo Text::_('Apply View'); ?></button>
                        </div>
                        <div class="jsst-ticket-saved-create">
                            <input type="text" name="saved_view_title" id="saved_view_title" placeholder="<?php echo Text::_('Saved view name'); ?>" />
                            <button type="button" class="jsst-ticket-saved-btn is-green" onclick="jsstSaveCurrentView();"><?php echo Text::_('Save View'); ?></button>
                        </div>
                    </div>
                    <div class="jsst-ticket-filter-grid">
                        <label class="jsst-ticket-filter-field">
                            <span><?php echo Text::_('Ticket ID'); ?></span>
                            <input type="text" name="filter_ticketid" id="filter_ticketid" placeholder="<?php echo Text::_('Ticket ID'); ?>" value="<?php if (isset($this->lists['searchticket'])) echo jsst_ticketlist_escape($this->lists['searchticket']); ?>" class="text_area" />
                        </label>
                        <label class="jsst-ticket-filter-field">
                            <span><?php echo Text::_('Subject'); ?></span>
                            <input type="text" name="filter_subject" id="filter_subject" placeholder="<?php echo Text::_('Subject'); ?>" value="<?php if (isset($this->lists['searchsubject'])) echo jsst_ticketlist_escape($this->lists['searchsubject']); ?>" class="text_area" />
                        </label>
                        <label class="jsst-ticket-filter-field">
                            <span><?php echo Text::_('From'); ?></span>
                            <input type="text" name="filter_from" id="filter_from" placeholder="<?php echo Text::_('From'); ?>" value="<?php if (isset($this->lists['searchfrom'])) echo jsst_ticketlist_escape($this->lists['searchfrom']); ?>" class="text_area" />
                        </label>

                        <div id="js-filter-wrapper-toggle-area">
                            <div class="jsst-ticket-filter-advanced">
                                <label class="jsst-ticket-filter-field">
                                    <span><?php echo Text::_('Email'); ?></span>
                                    <input type="text" name="filter_fromemail" id="filter_fromemail" placeholder="<?php echo Text::_('Email'); ?>" value="<?php if (isset($this->lists['searchfromemail'])) echo jsst_ticketlist_escape($this->lists['searchfromemail']); ?>" class="text_area" />
                                </label>
                                <label class="jsst-ticket-filter-field">
                                    <span><?php echo Text::_('Start Date'); ?></span>
                                    <?php echo HTMLHelper::_('calendar', isset($this->lists['datestart']) ? $this->lists['datestart'] : '', 'filter_datestart', 'filter_datestart', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19', 'placeholder' => Text::_('Start Date'))); ?>
                                </label>
                                <label class="jsst-ticket-filter-field">
                                    <span><?php echo Text::_('End Date'); ?></span>
                                    <?php echo HTMLHelper::_('calendar', isset($this->lists['dateend']) ? $this->lists['dateend'] : '', 'filter_dateend', 'filter_dateend', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19', 'placeholder' => Text::_('End Date'))); ?>
                                </label>
                                <label class="jsst-ticket-filter-field"><span><?php echo Text::_('Departments'); ?></span><?php echo $this->lists['departments']; ?></label>
                                <label class="jsst-ticket-filter-field"><span><?php echo Text::_('Priorities'); ?></span><?php echo $this->lists['priorities']; ?></label>
                                <?php
                                $params = null;
                                if (isset($this->lists['params'])) {
                                    $params = $this->lists['params'];
                                }
                                $k = 1;
                                $customfields = getCustomFieldClass()->userFieldsForSearch(1);
                                foreach ($customfields as $field) {
                                    echo '<div class="jsst-ticket-filter-field jsst-ticket-custom-filter"><span>' . Text::_($field->fieldtitle) . '</span>';
                                    getCustomFieldClass()->formCustomFieldsForSearch($field, $k, $params, 1);
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="jsst-ticket-filter-actions">
                            <button type="submit" class="jsst-ticket-search-btn"><?php echo Text::_('Search'); ?></button>
                            <button type="button" class="jsst-ticket-reset-btn" onclick="resetJsForm();this.form.submit();"><?php echo Text::_('Reset'); ?></button>
                        </div>
                        <input type="hidden" name="sortby" id="sortby" />
                    </div>
                </div>

                <div class="jsst-ticket-list-card">
                    <div class="jsst-ticket-list-head">
                        <div>
                            <h2><?php echo Text::_('Ticket List'); ?></h2>
                            <p><?php echo Text::_('Review ticket details, priority, status, and assignment.'); ?></p>
                        </div>
                        <div class="jsst-ticket-sort">
                            <label for="jsst-ticket-sorting-select"><?php echo Text::_('Sort by'); ?></label>
                            <select id="jsst-ticket-sorting-select" class="js-ticket-sorting-select">
                                <option value="<?php if ($this->sortlinks['sortorder'] == 'ASC') echo 'subjectasc'; else echo 'subjectdesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'subject') echo 'selected'; ?>><?php echo Text::_('Subject'); ?></option>
                                <option value="<?php if ($this->sortlinks['sortorder'] == 'ASC') echo 'priorityasc'; else echo 'prioritydesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'priority') echo 'selected'; ?>><?php echo Text::_('Priority'); ?></option>
                                <option value="<?php if ($this->sortlinks['sortorder'] == 'ASC') echo 'ticketidasc'; else echo 'ticketiddesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'ticketid') echo 'selected'; ?>><?php echo Text::_('Ticket ID'); ?></option>
                                <option value="<?php if ($this->sortlinks['sortorder'] == 'ASC') echo 'answeredasc'; else echo 'answereddesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'answered') echo 'selected'; ?>><?php echo Text::_('Answered'); ?></option>
                                <option value="<?php if ($this->sortlinks['sortorder'] == 'ASC') echo 'statusasc'; else echo 'statusdesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'status') echo 'selected'; ?>><?php echo Text::_('Status'); ?></option>
                                <option value="<?php if ($this->sortlinks['sortorder'] == 'ASC') echo 'createdasc'; else echo 'createddesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'created') echo 'selected'; ?>><?php echo Text::_('Created'); ?></option>
                            </select>
                            <a href="javascript:void(0)" id="jssortbtn" class="jsst-ticket-sort-direction" title="<?php echo Text::_('Sort'); ?>">
                                <img src="<?php echo jsst_ticketlist_url($sortImage); ?>" alt="<?php echo Text::_('Sort'); ?>" />
                            </a>
                        </div>
                    </div>

                    <?php if (!(empty($this->result)) && is_array($this->result)) { ?>
                        <div class="jsst-ticket-feed">
                            <?php
                            $i = 0;
                            foreach ($this->result as $row) {
                                $linkEdit = 'index.php?option=' . $this->option . '&c=ticket&task=addnewticket&cid[]=' . $row->id;
                                $linkEnforceDelete = 'index.php?option=' . $this->option . '&c=ticket&task=enforcedelete&cid=' . $row->id . '&' . Factory::getSession()->getFormToken() . '=1';
                                $linkDelete = 'index.php?option=' . $this->option . '&c=ticket&task=delete&cid=' . $row->id . '&' . Factory::getSession()->getFormToken() . '=1';
                                $linkDetail = 'index.php?option=' . $this->option . '&c=ticket&layout=ticketdetails&cid[]=' . $row->id;
                                $status = jsst_ticketlist_status($row->status);
                                $priorityColor = jsst_ticketlist_color($row->prioritycolour);
                                $avatarUrl = '';
                                if (!empty($row->staffphoto)) {
                                    $avatarUrl = Uri::root() . $this->config['data_directory'] . '/staffdata/staff_' . $row->staffid . '/' . $row->staffphoto;
                                }
                            ?>
                                <article class="jsst-ticket-item">
                                    <div class="jsst-ticket-avatar">
                                        <?php if ($avatarUrl !== '') { ?>
                                            <img src="<?php echo jsst_ticketlist_url($avatarUrl); ?>" alt="<?php echo jsst_ticketlist_escape($row->name); ?>" />
                                        <?php } else { ?>
                                            <span><?php echo jsst_ticketlist_escape(jsst_ticketlist_initials($row->name, $row->email)); ?></span>
                                        <?php } ?>
                                    </div>

                                    <div class="jsst-ticket-main">
                                        <div class="jsst-ticket-title-row">
                                            <div>
                                                <button type="button" class="jsst-ticket-customer" onclick="setFromNameFilter(<?php echo json_encode((string) $row->email); ?>);">
                                                    <?php echo jsst_ticketlist_escape($row->name); ?>
                                                </button>
                                                <a class="jsst-ticket-subject" href="<?php echo jsst_ticketlist_url($linkDetail); ?>"><?php echo jsst_ticketlist_escape($row->subject); ?></a>
                                            </div>
                                            <div class="jsst-ticket-badges">
                                                <?php if ((int) $row->ticketviaemail === 1) { ?><span class="jsst-ticket-badge is-email"><?php echo Text::_('Ticket via email'); ?></span><?php } ?>
                                                <?php if ((int) $row->lock === 1) { ?><span class="jsst-ticket-badge is-lock"><?php echo Text::_('Locked'); ?></span><?php } ?>
                                                <?php if ((int) $row->isoverdue === 1) { ?><span class="jsst-ticket-badge is-overdue"><?php echo Text::_('Overdue'); ?></span><?php } ?>
                                                <span class="jsst-ticket-badge <?php echo jsst_ticketlist_escape($status['class']); ?>"><?php echo jsst_ticketlist_escape($status['label']); ?></span>
                                                <span class="jsst-ticket-priority" style="--jsst-priority-color: <?php echo jsst_ticketlist_escape($priorityColor); ?>;"><?php echo jsst_ticketlist_escape(Text::_($row->priority)); ?></span>
                                            </div>
                                        </div>

                                        <div class="jsst-ticket-meta-grid">
                                            <button type="button" class="jsst-ticket-meta" onclick="setDepartmentFilter(<?php echo (int) $row->departmentid; ?>);">
                                                <span><?php echo Text::_('Department'); ?></span>
                                                <strong><?php echo jsst_ticketlist_escape(Text::_($row->departmentname)); ?></strong>
                                            </button>
                                            <div class="jsst-ticket-meta">
                                                <span><?php echo Text::_('Ticket ID'); ?></span>
                                                <strong><?php echo jsst_ticketlist_escape($row->ticketid); ?></strong>
                                            </div>
                                            <div class="jsst-ticket-meta">
                                                <span><?php echo Text::_('Created'); ?></span>
                                                <strong><?php echo HTMLHelper::_('date', $row->created, $this->config['date_format']); ?></strong>
                                            </div>
                                            <div class="jsst-ticket-meta">
                                                <span><?php echo Text::_('Last Reply'); ?></span>
                                                <strong><?php if ($row->lastreply == '' || $row->lastreply == '0000-00-00 00:00:00') echo Text::_('No last reply'); else echo HTMLHelper::_('date', $row->lastreply, $this->config['date_format']); ?></strong>
                                            </div>
                                            <?php
                                            $forlisting = $this->getJSModel('userfields')->getFieldsForListing(1);
                                            if ($forlisting['assignto'] == 1) {
                                            ?>
                                                <div class="jsst-ticket-meta">
                                                    <span><?php echo Text::_('Assign To'); ?></span>
                                                    <strong><?php echo jsst_ticketlist_escape(trim($row->stafffirstname . ' ' . $row->stafflastname)); ?></strong>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <?php
                                        $customfields = getCustomFieldClass()->userFieldsData(1, 1);
                                        if (!empty($customfields)) {
                                            echo '<div class="jsst-ticket-custom-fields">';
                                            foreach ($customfields as $field) {
                                                echo getCustomFieldClass()->showCustomFields($field, 4, $row->params, $row->id);
                                            }
                                            echo '</div>';
                                        }
                                        ?>

                                        <div class="jsst-ticket-actions">
                                            <a class="is-view" href="<?php echo jsst_ticketlist_url($linkDetail); ?>"><?php echo Text::_('View Ticket'); ?></a>
                                            <a href="<?php echo jsst_ticketlist_url($linkEdit); ?>"><?php echo Text::_('Edit Ticket'); ?></a>
                                            <a class="is-danger" href="<?php echo jsst_ticketlist_url($linkDelete); ?>" onclick="return confirmdelete(0)"><?php echo Text::_('Delete Ticket'); ?></a>
                                            <a class="is-danger-soft" href="<?php echo jsst_ticketlist_url($linkEnforceDelete); ?>" onclick="return confirmdelete(1)"><?php echo Text::_('Enforce delete'); ?></a>
                                        </div>
                                    </div>
                                </article>
                            <?php $i++; } ?>
                        </div>
                        <div class="jsst-ticket-pagination">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </div>
                    <?php } else { ?>
                        <div class="jsst-ticket-empty-state">
                            <?php messageslayout::getRecordNotFound(); ?>
                        </div>
                    <?php } ?>
                </div>

                <input type="hidden" name="option" value="<?php echo jsst_ticketlist_escape($this->option); ?>" />
                <input type="hidden" name="lt" value="<?php echo (int) $this->listtype; ?>" />
                <input type="hidden" name="c" value="ticket" />
                <input type="hidden" name="layout" value="tickets" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>

<script>
    function resetJsForm() {
        var form = jQuery('form#adminForm');
        form.find('input[type=text], input[type=email], input[type=password], textarea').val('');
        form.find('input:checkbox').removeAttr('checked');
        form.find('select').prop('selectedIndex', 0);
        form.find('input[type=radio]').prop('checked', false);
        jQuery('<input type="hidden" value="1" />')
            .attr('id', 'jsresetbutton')
            .attr('name', 'jsresetbutton')
            .appendTo(form);
    }


    function jsstSaveCurrentView() {
        var title = jQuery('#saved_view_title').val();
        if (!title || jQuery.trim(title) === '') {
            alert('<?php echo Text::_('Please enter a saved view title.'); ?>');
            return false;
        }
        var form = jQuery('form#adminForm');
        form.attr('method', 'post');
        form.attr('action', 'index.php');
        form.find('input[name=task]').val('saveticketsavedview');
        form.find('input[name=c]').val('ticket');
        form.find('input[name=layout]').val('tickets');
        form.find('input[name=jsst_saved_view_title]').remove();
        jQuery('<input type="hidden" />')
            .attr('name', 'jsst_saved_view_title')
            .val(jQuery.trim(title))
            .appendTo(form);
        form.submit();
    }

    function jsstApplySavedView() {
        var id = jQuery('#saved_view_id').val();
        if (!id) {
            alert('<?php echo Text::_('Please select a saved view.'); ?>');
            return false;
        }
        var form = jQuery('form#adminForm');
        form.find('input[name=task]').val('applyticketsavedview');
        form.submit();
    }

    function setDepartmentFilter(depid) {
        jQuery('#filter_department').val(depid);
        jQuery('form#adminForm').submit();
    }

    function setFromNameFilter(email) {
        jQuery('#filter_fromemail').val(email);
        jQuery('form#adminForm').submit();
    }
</script>
