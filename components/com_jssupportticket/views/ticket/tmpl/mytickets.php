<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 * Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Project:     JS Tickets
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$escape = function ($value) {
    return htmlspecialchars((string) $value, ENT_COMPAT, 'UTF-8');
};

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

$ticketinfo = is_array($this->ticketinfo ?? null) ? $this->ticketinfo : array();
$allTickets = (int) ($ticketinfo['allticket'] ?? 0);
$openCount = (int) ($ticketinfo['open'] ?? 0);
$closedCount = (int) ($ticketinfo['close'] ?? 0);
$answeredCount = (int) ($ticketinfo['answered'] ?? 0);

$countText = function ($count) {
    return '(' . (int) $count . ')';
};

$percent = function ($count) use ($allTickets) {
    if ($allTickets <= 0) {
        return 0;
    }
    return max(0, min(100, getJSTicketPHPFunctionsClass()->jsticket_round(((int) $count / $allTickets) * 100)));
};

$statusUrl = function ($listType) {
    $emailParam = '';
    if (isset($this->email) && $this->email !== '') {
        $emailParam = '&email=' . rawurlencode((string) $this->email);
    }

    $sortOn = $this->sortlinks['sorton'] ?? 'created';
    $sortOrder = getJSTicketPHPFunctionsClass()->jsticket_strtolower($this->sortlinks['sortorder'] ?? 'desc');

    return 'index.php?option=com_jssupportticket&c=ticket&layout=mytickets'
        . $emailParam
        . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars('&lt') . '=' . (int) $listType
        . '&sortby=' . rawurlencode((string) $sortOn) . rawurlencode((string) $sortOrder)
        . '&Itemid=' . (int) $this->Itemid;
};

$statusLabel = function ($row) {
    if (!empty($row->lock)) {
        return Text::_('Locked');
    }

    $status = (int) ($row->status ?? 0);
    if ($status === 0) {
        return Text::_('New');
    }
    if ($status === 1) {
        return Text::_('Waiting for Reply');
    }
    if ($status === 2) {
        return Text::_('In Progress');
    }
    if ($status === 3) {
        return Text::_('Replied');
    }
    if ($status === 4) {
        return Text::_('Closed');
    }
    if ($status === 5) {
        return Text::_('Closed by Merge');
    }

    return Text::_('Open');
};

$statusClass = function ($row) {
    if (!empty($row->lock)) {
        return 'is-locked';
    }

    $status = (int) ($row->status ?? 0);
    if ($status === 4 || $status === 5) {
        return 'is-closed';
    }
    if ($status === 1 || $status === 3) {
        return 'is-replied';
    }
    if ($status === 2) {
        return 'is-progress';
    }
    return 'is-open';
};

$lastReply = function ($row) use ($escape) {
    $value = $row->lastreply ?? '';
    if ($value === '' || $value === '0000-00-00 00:00:00') {
        return Text::_('No Last Reply');
    }

    return $escape(HTMLHelper::_('date', $value, $this->config['date_format']));
};

$dueDate = function ($row) use ($escape) {
    $value = $row->duedate ?? '';
    if ($value === '' || $value === '0000-00-00 00:00:00') {
        return Text::_('JNONE');
    }

    return $escape(HTMLHelper::_('date', $value, $this->config['date_format']));
};

$compactCustomFieldValue = function ($field, $rawValue) {
    if (is_object($rawValue)) {
        $rawValue = (array) $rawValue;
    }

    if (is_string($rawValue)) {
        $trimmed = trim($rawValue);
        if ($trimmed !== '' && ($trimmed[0] === '[' || $trimmed[0] === '{')) {
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $rawValue = $decoded;
            }
        }
    }

    if (is_array($rawValue)) {
        $parts = array();
        foreach ($rawValue as $part) {
            if (is_scalar($part)) {
                $part = trim((string) $part);
                if ($part !== '') {
                    $parts[] = $part;
                }
            }
        }
        $rawValue = implode(', ', $parts);
    }

    if (!is_scalar($rawValue)) {
        return '';
    }

    $value = trim(html_entity_decode(strip_tags((string) $rawValue), ENT_QUOTES, 'UTF-8'));
    if ($value === '') {
        return '';
    }

    if (($field->userfieldtype ?? '') === 'date') {
        try {
            $value = HTMLHelper::_('date', $value, $this->config['date_format']);
        } catch (Throwable $e) {
            // Preserve the stored value if it cannot be parsed as a Joomla date.
        }
    }

    $limit = 96;
    $length = function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    if ($length > $limit) {
        $value = (function_exists('mb_substr') ? mb_substr($value, 0, $limit - 1, 'UTF-8') : substr($value, 0, $limit - 1)) . '…';
    }

    return $value;
};

$document = Factory::getDocument();
?>
<div class="js-row js-null-margin jsst-user-mytickets-page jsst-staff-mytickets-page jsst-customer-mytickets-modern">
<?php
if ($this->config['offline'] != '1') {
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $language = Factory::getLanguage();
    ?>
    <?php if ($this->config['cur_location'] == 1) { ?>
        <div id="jsst-wrapper-top">
            <div id="jsst-wrapper-top-left">
                <div id="jsst-breadcrunbs">
                    <ul>
                        <li>
                            <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo (int) $this->Itemid; ?>" title="<?php echo $escape(Text::_('Dashboard')); ?>">
                                <?php echo Text::_('Dashboard'); ?>
                            </a>
                        </li>
                        <li><?php echo Text::_('My Tickets'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if (!$this->user->getIsGuest()) { ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var combinesearch = "<?php echo isset($this->filter_data['iscombinesearch']) ? (int) $this->filter_data['iscombinesearch'] : 0; ?>";
                var advanced = $('#jsst-myticket-advanced-filters');
                var toggle = $('#jsst-myticket-filter-toggle');

                function setAdvanced(open) {
                    if (open) {
                        advanced.addClass('is-open').show();
                        toggle.text('<?php echo Text::_('Show Less'); ?>');
                    } else {
                        advanced.removeClass('is-open').hide();
                        toggle.text('<?php echo Text::_('Show All'); ?>');
                    }
                }

                setAdvanced(!!combinesearch);
                toggle.on('click', function (event) {
                    event.preventDefault();
                    setAdvanced(!advanced.is(':visible'));
                });

                var sortby = $('select.jsst-myticket-sort-select').val();
                if (sortby !== '') {
                    $('input#sortby').val(sortby);
                }

                $('select.jsst-myticket-sort-select').on('change', function () {
                    $('input#sortby').val($(this).val());
                    $('form#jssupportticketform').submit();
                });

                $('#jssortbtn').on('click', function () {
                    var sortby = $('select.jsst-myticket-sort-select').val();
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
                    $('input#sortby').val(sortby);
                    $('form#jssupportticketform').submit();
                });
            });

            function getDataForDepandantField(parentf, childf, type) {
                var val = '';
                if (type == 1) {
                    val = jQuery('select#' + parentf).val();
                } else if (type == 2) {
                    val = jQuery('input[name=' + parentf + ']:checked').val();
                    if (val === undefined) {
                        val = jQuery('input[name="' + parentf + '[]"]:checked').val();
                    }
                }
                jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=datafordepandantfield&<?php echo Factory::getSession()->getFormToken(); ?>=1', {fvalue: val, child: childf}, function (data) {
                    if (data) {
                        var d = jQuery.parseJSON(data);
                        jQuery('select#' + childf).replaceWith(d);
                    }
                });
            }
        </script>

        <div class="jsst-mytickets-stats-grid" aria-label="<?php echo $escape(Text::_('Ticket Statistics')); ?>">
            <a class="jsst-myticket-stat is-open <?php echo ($this->lt == 1) ? 'is-active' : ''; ?>" href="<?php echo $statusUrl(1); ?>" style="--jsst-stat-color:#16a34a;--jsst-stat-percent:<?php echo (int) $percent($openCount); ?>%;">
                <span class="jsst-myticket-ring" aria-hidden="true"></span>
                <span class="jsst-myticket-stat-copy">
                    <span class="jsst-myticket-stat-label"><?php echo Text::_('Open'); ?></span>
                    <span class="jsst-myticket-stat-count"><?php echo ($this->config['show_count_tickets'] == 1) ? $countText($openCount) : ''; ?></span>
                </span>
            </a>
            <a class="jsst-myticket-stat is-closed <?php echo ($this->lt == 4) ? 'is-active' : ''; ?>" href="<?php echo $statusUrl(4); ?>" style="--jsst-stat-color:#ef4444;--jsst-stat-percent:<?php echo (int) $percent($closedCount); ?>%;">
                <span class="jsst-myticket-ring" aria-hidden="true"></span>
                <span class="jsst-myticket-stat-copy">
                    <span class="jsst-myticket-stat-label"><?php echo Text::_('Closed'); ?></span>
                    <span class="jsst-myticket-stat-count"><?php echo ($this->config['show_count_tickets'] == 1) ? $countText($closedCount) : ''; ?></span>
                </span>
            </a>
            <a class="jsst-myticket-stat is-answered <?php echo ($this->lt == 2) ? 'is-active' : ''; ?>" href="<?php echo $statusUrl(2); ?>" style="--jsst-stat-color:#8b5cf6;--jsst-stat-percent:<?php echo (int) $percent($answeredCount); ?>%;">
                <span class="jsst-myticket-ring" aria-hidden="true"></span>
                <span class="jsst-myticket-stat-copy">
                    <span class="jsst-myticket-stat-label"><?php echo Text::_('Answered'); ?></span>
                    <span class="jsst-myticket-stat-count"><?php echo ($this->config['show_count_tickets'] == 1) ? $countText($answeredCount) : ''; ?></span>
                </span>
            </a>
            <a class="jsst-myticket-stat is-all <?php echo ($this->lt == 5) ? 'is-active' : ''; ?>" href="<?php echo $statusUrl(5); ?>" style="--jsst-stat-color:#06a9d6;--jsst-stat-percent:<?php echo ($allTickets > 0) ? 100 : 0; ?>%;">
                <span class="jsst-myticket-ring" aria-hidden="true"></span>
                <span class="jsst-myticket-stat-copy">
                    <span class="jsst-myticket-stat-label"><?php echo Text::_('All Tickets'); ?></span>
                    <span class="jsst-myticket-stat-count"><?php echo ($this->config['show_count_tickets'] == 1) ? $countText($allTickets) : ''; ?></span>
                </span>
            </a>
        </div>

        <form class="jsst-mytickets-filter-card jsst-staff-filter-card jsst-customer-filter-card" action="index.php" method="post" name="adminForm" id="jssupportticketform">
            <div class="jsst-mytickets-filter-main jsst-staff-filter-main">
                <input type="text" name="filter_ticketid" id="filter_ticketid" value="<?php echo isset($this->filter_data['ticketid']) ? $escape($this->filter_data['ticketid']) : ''; ?>" class="js-ticket-input-field" placeholder="<?php echo $escape(Text::_('Ticket ID')); ?>" />
                <input type="text" name="filter_from" id="filter_from" value="<?php echo isset($this->filter_data['from']) ? $escape($this->filter_data['from']) : ''; ?>" class="js-ticket-input-field" placeholder="<?php echo $escape(Text::_('Username')); ?>" />
                <input type="text" name="filter_email" id="filter_email" value="<?php echo isset($this->filter_data['email']) ? $escape($this->filter_data['email']) : ''; ?>" class="js-ticket-input-field" placeholder="<?php echo $escape(Text::_('Email')); ?>" />
                <input type="text" name="filter_subject" id="filter_subject" class="js-ticket-input-field" value="<?php echo isset($this->filter_data['subject']) ? $escape($this->filter_data['subject']) : ''; ?>" placeholder="<?php echo $escape(Text::_('Subject')); ?>" />
                <div class="jsst-mytickets-filter-actions jsst-staff-filter-actions">
                    <a href="#" class="jsst-mytickets-filter-toggle" id="jsst-myticket-filter-toggle"><?php echo Text::_('Show All'); ?></a>
                    <button type="submit" class="jsst-mytickets-search-btn"><?php echo Text::_('Search'); ?></button>
                    <button type="button" class="jsst-mytickets-reset-btn" onclick="resetJsForm();this.form.submit();"><?php echo Text::_('Reset'); ?></button>
                </div>
            </div>
            <div class="jsst-mytickets-advanced jsst-staff-advanced" id="jsst-myticket-advanced-filters">
                <div class="jsst-mytickets-field"><?php echo $this->lists['departments']; ?></div>
                <div class="jsst-mytickets-field"><?php echo $this->lists['priorities']; ?></div>
                <div class="jsst-mytickets-field jsst-date-filter-field">
                    <?php echo HTMLHelper::_('calendar', isset($this->filter_data['datestart']) ? $this->filter_data['datestart'] : '', 'filter_datestart', 'filter_datestart', $js_dateformat, array('class' => 'js-ticket-input-field', 'size' => '10', 'maxlength' => '19', 'placeholder' => Text::_('Start Date'), 'showtime' => false, 'todaybutton' => true)); ?>
                </div>
                <div class="jsst-mytickets-field jsst-date-filter-field">
                    <?php echo HTMLHelper::_('calendar', isset($this->filter_data['dateend']) ? $this->filter_data['dateend'] : '', 'filter_dateend', 'filter_dateend', $js_dateformat, array('class' => 'js-ticket-input-field', 'size' => '10', 'maxlength' => '19', 'placeholder' => Text::_('End Date'), 'showtime' => false, 'todaybutton' => true)); ?>
                </div>
                <?php
                $params = isset($this->filter_data['params']) ? $this->filter_data['params'] : null;
                $customfields = getCustomFieldClass()->userFieldsForSearch(1);
                if (!empty($customfields)) {
                    $k = 1;
                    foreach ($customfields as $field) {
                        ob_start();
                        getCustomFieldClass()->formCustomFieldsForSearch($field, $k, $params);
                        $fieldHtml = (string) ob_get_clean();
                        $fieldType = preg_replace('/[^a-z0-9_-]/', '', strtolower((string) ($field->userfieldtype ?? 'field')));
                        $typedClass = 'jsst-custom-filter-field jsst-custom-filter-' . ($fieldType !== '' ? $fieldType : 'field');
                        $fieldHtml = preg_replace(
                            '/class="js-col-md-3 js-filter-field-wrp"/',
                            'class="js-col-md-3 js-filter-field-wrp ' . $typedClass . '"',
                            $fieldHtml,
                            1
                        );
                        echo $fieldHtml;
                    }
                    if (sizeof($customfields) == 1 && $customfields[0]->userfieldtype == 'termsandconditions') {
                        // The terms field manages its own wrapper.
                    } else {
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <input type="hidden" name="sortby" id="sortby" value="" />
            <input type="hidden" name="sortorder" id="sortorder" value="" />
            <input type="hidden" name="option" value="com_jssupportticket" />
            <input type="hidden" name="c" value="ticket" />
            <input type="hidden" name="layout" value="mytickets" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="lt" value="<?php echo (int) $this->lt; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo (int) $this->Itemid; ?>" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>

        <?php
        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=mytickets&email=' . rawurlencode((string) ($this->email ?? '')) . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars('&lt') . '=' . (int) $this->lt . '&Itemid=' . (int) $this->Itemid;
        $img = (($this->sortlinks['sortorder'] ?? '') == 'ASC') ? 'components/com_jssupportticket/include/images/sort1.png' : 'components/com_jssupportticket/include/images/sort2.png';
        ?>
        <div class="jsst-mytickets-list-head jsst-staff-list-head jsst-customer-list-head">
            <div>
                <h2><?php echo Text::_('My Tickets'); ?></h2>
                <p><?php echo Text::_('Track your requests, current status, priority, and latest reply.'); ?></p>
            </div>
            <div class="jsst-mytickets-sort">
                <select class="jsst-myticket-sort-select" aria-label="<?php echo $escape(Text::_('Sort By')); ?>">
                    <option value="<?php echo (($this->sortlinks['sortorder'] ?? '') == 'ASC') ? 'subjectasc' : 'subjectdesc'; ?>" <?php if (($this->sortlinks['sorton'] ?? '') == 'subject') echo 'selected'; ?>><?php echo Text::_('Subject'); ?></option>
                    <option value="<?php echo (($this->sortlinks['sortorder'] ?? '') == 'ASC') ? 'priorityasc' : 'prioritydesc'; ?>" <?php if (($this->sortlinks['sorton'] ?? '') == 'priority') echo 'selected'; ?>><?php echo Text::_('Priority'); ?></option>
                    <option value="<?php echo (($this->sortlinks['sortorder'] ?? '') == 'ASC') ? 'ticketidasc' : 'ticketiddesc'; ?>" <?php if (($this->sortlinks['sorton'] ?? '') == 'ticketid') echo 'selected'; ?>><?php echo Text::_('Ticket ID'); ?></option>
                    <option value="<?php echo (($this->sortlinks['sortorder'] ?? '') == 'ASC') ? 'answeredasc' : 'answereddesc'; ?>" <?php if (($this->sortlinks['sorton'] ?? '') == 'answered') echo 'selected'; ?>><?php echo Text::_('Answered'); ?></option>
                    <option value="<?php echo (($this->sortlinks['sortorder'] ?? '') == 'ASC') ? 'statusasc' : 'statusdesc'; ?>" <?php if (($this->sortlinks['sorton'] ?? '') == 'status') echo 'selected'; ?>><?php echo Text::_('Status'); ?></option>
                    <option value="<?php echo (($this->sortlinks['sortorder'] ?? '') == 'ASC') ? 'createdasc' : 'createddesc'; ?>" <?php if (($this->sortlinks['sorton'] ?? '') == 'created') echo 'selected'; ?>><?php echo Text::_('Created'); ?></option>
                </select>
                <a href="javascript:void(0)" id="jssortbtn" class="jsst-mytickets-sort-btn" title="<?php echo $escape(Text::_('Sort')); ?>">
                    <img src="<?php echo $escape($img); ?>" alt="<?php echo $escape(Text::_('Sort')); ?>" />
                </a>
            </div>
        </div>

        <div class="jsst-mytickets-list jsst-staff-ticket-list jsst-customer-ticket-list">
        <?php
        if (!empty($this->result) && is_array($this->result)) {
            $forlisting = $this->getJSModel('userfields')->getFieldsForListing(1);
            $customfields = getCustomFieldClass()->userFieldsData(1, 1);
            foreach ($this->result as $row) {
                $ticketLink = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id=' . (int) $row->id . '&Itemid=' . (int) $this->Itemid;
                $priorityLabel = trim((string) Text::_($row->priority ?? ''));
                $priorityKey = getJSTicketPHPFunctionsClass()->jsticket_strtolower(trim((string) ($row->priority ?? $priorityLabel)));
                $priorityLabelKey = getJSTicketPHPFunctionsClass()->jsticket_strtolower($priorityLabel);
                $priorityColor = isset($row->prioritycolour) ? trim((string) $row->prioritycolour) : '#16a34a';
                if ($priorityKey === 'high' || $priorityLabelKey === getJSTicketPHPFunctionsClass()->jsticket_strtolower(Text::_('High'))) {
                    $priorityColor = '#ef4444';
                } elseif ($priorityKey === 'low' || $priorityLabelKey === getJSTicketPHPFunctionsClass()->jsticket_strtolower(Text::_('Low'))) {
                    $priorityColor = '#06a9d6';
                } elseif ($priorityKey === 'normal' || $priorityLabelKey === getJSTicketPHPFunctionsClass()->jsticket_strtolower(Text::_('Normal'))) {
                    $priorityColor = '#16a34a';
                } elseif (!preg_match('/^#[0-9a-fA-F]{3,8}$/', $priorityColor)) {
                    $priorityColor = '#16a34a';
                }

                $assignedTo = trim((string) ($row->staffname ?? ''));
                if ($assignedTo === '') {
                    $assignedTo = Text::_('Unassigned');
                }

                $ticketCustomFieldItems = array();
                if (!empty($customfields)) {
                    $ticketParams = json_decode((string) ($row->params ?? ''), true);
                    if (is_array($ticketParams)) {
                        foreach ($customfields as $field) {
                            $fieldName = (string) ($field->field ?? '');
                            if ($fieldName === '' || !array_key_exists($fieldName, $ticketParams)) {
                                continue;
                            }

                            $fieldValue = $compactCustomFieldValue($field, $ticketParams[$fieldName]);
                            if ($fieldValue === '') {
                                continue;
                            }

                            $ticketCustomFieldItems[] = array(
                                'label' => Text::_((string) ($field->fieldtitle ?? $fieldName)),
                                'value' => $fieldValue,
                            );
                        }
                    }
                }
                ?>
                <article class="jsst-myticket-card jsst-staff-ticket-card jsst-customer-ticket-card">
                    <div class="jsst-myticket-main">
                        <div class="jsst-myticket-avatar">
                            <?php if (!empty($row->staffphoto)) { ?>
                                <img src="<?php echo Uri::root() . $escape($this->config['data_directory'] . '/staffdata/staff_' . (int) $row->staffid . '/' . $row->staffphoto); ?>" alt="" />
                            <?php } else { ?>
                                <img src="components/com_jssupportticket/include/images/user.png" alt="" />
                            <?php } ?>
                        </div>
                        <div class="jsst-myticket-summary">
                            <div class="jsst-myticket-name">
                                <?php echo $escape($row->name ?? ''); ?>
                                <?php if (!empty($row->email)) { ?><span><strong><?php echo Text::_('Email'); ?>:</strong> <?php echo $escape($row->email); ?></span><?php } ?>
                            </div>
                            <h3><a href="<?php echo $ticketLink; ?>"><?php echo $escape($row->subject ?? ''); ?></a></h3>
                            <div class="jsst-myticket-department">
                                <strong><?php echo Text::_('Department'); ?> :</strong>
                                <span><?php echo $escape($row->departmentname ?? ''); ?></span>
                            </div>
                            <?php if (!empty($ticketCustomFieldItems)) {
                                $visibleCustomFields = array_slice($ticketCustomFieldItems, 0, 3);
                                $hiddenCustomFields = array_slice($ticketCustomFieldItems, 3);
                                ?>
                                <div class="jsst-ticket-custom-preview" aria-label="<?php echo $escape(Text::_('Custom Fields')); ?>">
                                    <?php foreach ($visibleCustomFields as $customFieldItem) { ?>
                                        <div class="jsst-ticket-custom-preview-item">
                                            <span><?php echo $escape($customFieldItem['label']); ?></span>
                                            <strong title="<?php echo $escape($customFieldItem['value']); ?>"><?php echo $escape($customFieldItem['value']); ?></strong>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($hiddenCustomFields)) { ?>
                                        <details class="jsst-ticket-custom-more">
                                            <summary>
                                                <span><?php echo Text::_('Show more'); ?></span>
                                                <b>+<?php echo count($hiddenCustomFields); ?></b>
                                            </summary>
                                            <div class="jsst-ticket-custom-more-grid">
                                                <?php foreach ($hiddenCustomFields as $customFieldItem) { ?>
                                                    <div class="jsst-ticket-custom-preview-item">
                                                        <span><?php echo $escape($customFieldItem['label']); ?></span>
                                                        <strong title="<?php echo $escape($customFieldItem['value']); ?>"><?php echo $escape($customFieldItem['value']); ?></strong>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </details>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="jsst-myticket-side jsst-staff-ticket-side">
                        <div class="jsst-myticket-badges jsst-staff-ticket-badges">
                            <span class="jsst-myticket-status-pill <?php echo $statusClass($row); ?>"><?php echo $statusLabel($row); ?></span>
                            <span class="jsst-myticket-priority-pill" style="--jsst-priority-color:<?php echo $escape($priorityColor); ?>;"><?php echo $escape($priorityLabel); ?></span>
                            <?php if (!empty($row->ticketviaemail)) { ?>
                                <span class="jsst-myticket-mini-badge is-email"><?php echo Text::_('Email Tickets'); ?></span>
                            <?php } ?>
                            <?php if (!empty($row->isoverdue)) { ?>
                                <span class="jsst-myticket-mini-badge is-overdue"><?php echo Text::_('Overdue'); ?></span>
                            <?php } ?>
                        </div>
                        <div class="jsst-myticket-meta jsst-ticket-meta-grid">
                            <div><strong><?php echo Text::_('Ticket ID'); ?> :</strong><span><?php echo $escape($row->ticketid ?? ''); ?></span></div>
                            <div><strong><?php echo Text::_('Last Reply'); ?> :</strong><span><?php echo $lastReply($row); ?></span></div>
                            <?php if (($forlisting['assignto'] ?? 0) == 1) { ?>
                                <div><strong><?php echo Text::_('Assigned To'); ?> :</strong><span><?php echo $escape(Text::_($assignedTo)); ?></span></div>
                            <?php } ?>
                            <div><strong><?php echo Text::_('Due Date'); ?> :</strong><span><?php echo $dueDate($row); ?></span></div>
                        </div>
                        <div class="jsst-ticket-actions">
                            <a href="<?php echo $ticketLink; ?>" class="jsst-ticket-action is-primary"><?php echo Text::_('View'); ?></a>
                            <?php if (empty($row->lock) && !in_array((int) ($row->status ?? 0), array(4, 5), true)) { ?>
                                <a href="<?php echo $ticketLink; ?>#reply" class="jsst-ticket-action"><?php echo Text::_('Reply'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </article>
                <?php
            }
            ?>
            <form class="jsst-pagination-form" action="<?php echo Route::_('index.php?option=com_jssupportticket&c=ticket&layout=mytickets&email=' . rawurlencode((string) ($this->email ?? '')) . getJSTicketPHPFunctionsClass()->jsticket_htmlspecialchars('&lt') . '=' . (int) $this->lt . '&Itemid=' . (int) $this->Itemid); ?>" method="post">
                <div id="jl_pagination" class="pagination jsst-mytickets-pagination">
                    <div id="jl_pagination_box"><?php echo $this->pagination->getLimitBox(); ?></div>
                    <div id="jl_pagination_counter"><?php echo $this->pagination->getResultsCounter(); ?></div>
                    <div id="jl_pagination_pageslink"><?php echo $this->pagination->getPagesLinks(); ?></div>
                </div>
            </form>
            <?php
        } else {
            ?>
            <div class="jsst-mytickets-empty">
                <span class="jsst-mytickets-empty-icon" aria-hidden="true"></span>
                <h3><?php echo Text::_('No tickets found'); ?></h3>
                <p><?php echo Text::_('There are no tickets for the current filters.'); ?></p>
            </div>
            <?php
        }
        ?>
        </div>
        <?php
    } else {
        messageslayout::getUserGuest($this->layoutname, $this->Itemid);
    }
} else {
    messageslayout::getSystemOffline($this->config['title'], $this->config['offline_text']);
}
?>
</div>
<script type="text/javascript">
    function resetJsForm() {
        var form = jQuery('form#jssupportticketform');
        form.find('input[type=text], input[type=email], input[type=password], textarea').val('');
        form.find('input:checkbox').prop('checked', false);
        form.find('select').prop('selectedIndex', 0);
        form.find('input[type="radio"]').prop('checked', false);
        if (!form.find('input[name="jsresetbutton"]').length) {
            jQuery('<input type="hidden" value="1" />')
                .attr('id', 'jsresetbutton')
                .attr('name', 'jsresetbutton')
                .appendTo(form);
        }
    }
</script>
