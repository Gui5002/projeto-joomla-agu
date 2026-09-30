<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company: Buruj Solutions
 * Contact: www.burujsolutions.com , info@burujsolutions.com
 * Created on: May 22, 2015
 * Project: JS Tickets
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$jinput = Factory::getApplication()->input;
$c = $jinput->getCmd('c', 'jssupportticket');
$layout = $jinput->getCmd('layout', 'controlpanel');
$tf = $jinput->getCmd('tf', '');
$ff = $jinput->getCmd('ff', '');

$base = 'index.php?option=com_jssupportticket';
$iconBase = 'components/com_jssupportticket/include/images/c_p/left-icons/';

$is = static function ($controller, $layouts = null, $extra = null) use ($c, $layout, $tf, $ff) {
    if (is_array($controller)) {
        if (!in_array($c, $controller, true)) {
            return false;
        }
    } elseif ($controller !== null && $c !== $controller) {
        return false;
    }

    if ($layouts !== null) {
        $layouts = (array) $layouts;
        if (!in_array($layout, $layouts, true)) {
            return false;
        }
    }

    if (is_array($extra)) {
        foreach ($extra as $key => $value) {
            if ($key === 'tf' && (string) $tf !== (string) $value) {
                return false;
            }
            if ($key === 'ff' && (string) $ff !== (string) $value) {
                return false;
            }
        }
    }

    return true;
};

// Pro-only entries keep their place in the menu so the structure matches the Pro
// edition, but they carry a "*" marker and lead to the Pro features page instead
// of a controller this edition does not ship.
$proBase = $base . '&c=jssupportticket&layout=proversion';

$item = static function ($label, $href, $icon, $active = false, $note = '', $pro = '') use ($iconBase, $proBase) {
    $isPro = $pro !== '';

    return array(
        'label' => Text::_($label),
        // A Pro item never reaches its own controller, so it can never be the active one.
        'href' => $isPro ? $proBase . '&feature=' . rawurlencode($pro) : $href,
        'icon' => $iconBase . $icon,
        'active' => $isPro ? false : (bool) $active,
        'note' => $note !== '' ? Text::_($note) : '',
        'pro' => $isPro ? $pro : '',
    );
};

$groups = array(
    array(
        'label' => Text::_('Dashboard'),
        'key' => 'dashboard',
        'icon' => $iconBase . 'dashboard.png',
        'items' => array(
            $item('Control Panel', $base . '&c=jssupportticket&layout=controlpanel', 'dashboard.png', $is('jssupportticket', array('controlpanel', '')), 'Overview and activity'),
            $item('Update', $base . '&c=proinstaller&layout=step1', 'download.png', $is('proinstaller', array('step1', 'step2')), 'Version and installer'),
            $item('About', $base . '&c=jssupportticket&layout=aboutus', 'address-data.png', $is('jssupportticket', 'aboutus'), 'Product information'),
        ),
    ),
    array(
        'label' => Text::_('Tickets'),
        'key' => 'tickets',
        'icon' => $iconBase . 'tickets.png',
        'items' => array(
            $item('All Tickets', $base . '&c=ticket&layout=tickets', 'tickets.png', $is('ticket', 'tickets'), 'Search and manage'),
            $item('Create Ticket', $base . '&c=ticket&layout=formticket', 'ad-ons.png', $is('ticket', 'formticket'), 'Open a support request'),
            $item('Email Tickets', '', 'message.png', false, 'Email-to-ticket setup', 'ticketviaemail'),
            $item('Export Tickets', '', 'download.png', false, 'Download records', 'export'),
            $item('Ticket Fields', $base . '&c=userfields&layout=fieldsordering&ff=1', 'settings.png', $is('userfields', 'fieldsordering', array('ff' => '1')), 'Custom ticket fields'),
        ),
    ),
    array(
        'label' => Text::_('People'),
        'key' => 'people',
        'icon' => $iconBase . 'users.png',
        'items' => array(
            $item('Staff Members', '', 'users.png', false, 'Team members', 'staff'),
            $item('Add Staff Member', '', 'ad-ons.png', false, 'Create staff account', 'staff'),
            $item('Roles', '', 'tags.png', false, 'Access permissions', 'roles'),
            $item('Add Role', '', 'ad-ons.png', false, 'Create permission role', 'roles'),
            $item('Departments', $base . '&c=department&layout=departments', 'department.png', $is('department', 'departments'), 'Support teams'),
            $item('Add Department', $base . '&c=department&layout=formdepartment', 'ad-ons.png', $is('department', 'formdepartment'), 'Create department'),
        ),
    ),
    array(
        'label' => Text::_('Workflow'),
        'key' => 'workflow',
        'icon' => $iconBase . 'priorities.png',
        'items' => array(
            $item('Help Topics', $base . '&c=helptopic&layout=helptopices', 'help-topic.png', $is('helptopic', 'helptopices'), 'Routing topics'),
            $item('Add Help Topic', $base . '&c=helptopic&layout=formhelptopic', 'ad-ons.png', $is('helptopic', 'formhelptopic'), 'Create help topic'),
            $item('Canned Responses', $base . '&c=premade&layout=departmentspremade', 'premade-messages.png', $is('premade', 'departmentspremade'), 'Saved replies'),
            $item('Add Canned Response', $base . '&c=premade&layout=formpremade', 'ad-ons.png', $is('premade', 'formpremade'), 'Create saved reply'),
            $item('Priorities', $base . '&c=priority&layout=priorities', 'priorities.png', $is('priority', 'priorities'), 'Urgency levels'),
            $item('Add Priority', $base . '&c=priority&layout=formpriority', 'ad-ons.png', $is('priority', 'formpriority'), 'Create priority'),
            $item('Feedback', '', 'feedback.png', false, 'Customer feedback', 'feedback'),
            $item('Feedback Fields', '', 'feedback.png', false, 'Feedback form fields', 'feedback'),
        ),
    ),
    array(
        'label' => Text::_('Content'),
        'key' => 'content',
        'icon' => $iconBase . 'kb.png',
        'items' => array(
            $item('Categories', '', 'category.png', false, 'Shared taxonomy', 'knowledgebase'),
            $item('Add Category', '', 'ad-ons.png', false, 'Create category', 'knowledgebase'),
            $item('Knowledge Base', '', 'kb.png', false, 'Support articles', 'knowledgebase'),
            $item('Add Article', '', 'ad-ons.png', false, 'Create article', 'knowledgebase'),
            $item('Downloads', '', 'download.png', false, 'Customer files', 'downloads'),
            $item('Add Download', '', 'ad-ons.png', false, 'Add customer file', 'downloads'),
            $item('Announcements', '', 'announcements.png', false, 'News and notices', 'announcements'),
            $item('Add Announcement', '', 'ad-ons.png', false, 'Create announcement', 'announcements'),
            $item('FAQs', '', 'faq.png', false, 'Common questions', 'faqs'),
            $item('Add FAQ', '', 'ad-ons.png', false, 'Create FAQ', 'faqs'),
        ),
    ),
    array(
        'label' => Text::_('Email'),
        'key' => 'email',
        'icon' => $iconBase . 'message.png',
        'items' => array(
            $item('Mail Inbox', '', 'message.png', false, 'Inbox and outbox', 'mail'),
            $item('Compose Message', '', 'ad-ons.png', false, 'Send a message', 'mail'),
            $item('System Emails', $base . '&c=email&layout=emails', 'system-email.png', $is('email', 'emails'), 'Outgoing accounts'),
            $item('Add Email', $base . '&c=email&layout=formemail', 'ad-ons.png', $is('email', 'formemail'), 'Add email account'),
            $item('Banned Emails', '', 'ban.png', false, 'Blocked senders', 'bannedemail'),
            $item('Banlist Log', '', 'ban.png', false, 'Blocked history', 'bannedemail'),
            $item('Email Templates', $base . '&c=emailtemplate&layout=emailtemplate&tf=tk-ew-ad', 'email-templates.png', $is('emailtemplate'), 'Notification templates'),
        ),
    ),
    array(
        'label' => Text::_('Reports'),
        'key' => 'reports',
        'icon' => $iconBase . 'report.png',
        'items' => array(
            $item('Overall Report', $base . '&c=reports&layout=overallreport', 'report.png', $is('overallreports', 'overallreports'), 'System activity'),
            $item('Staff Reports', '', 'users.png', false, 'Staff performance', 'reports'),
            $item('Department Reports', '', 'department.png', false, 'Team performance', 'reports'),
            $item('User Reports', '', 'users.png', false, 'Customer activity', 'reports'),
            $item('Satisfaction Reports', '', 'feedback.png', false, 'Feedback ratings', 'reports'),
        ),
    ),
    array(
        'label' => Text::_('System'),
        'key' => 'system',
        'icon' => $iconBase . 'settings.png',
        'items' => array(
            $item('Configurations', $base . '&c=config&layout=config', 'settings.png', $is('config', 'config'), 'Component settings'),
            $item('Themes', $base . '&c=jssupportticket&layout=themes', 'settings.png', $is('jssupportticket', 'themes'), 'Appearance'),
            $item('GDPR / User Data', $base . '&c=gdpr&layout=erasedatarequests', 'lock.png', $is('gdpr', 'erasedatarequests'), 'Export and erase'),
            $item('System Errors', $base . '&c=systemerrors&layout=systemerrors', 'system-error.png', $is('systemerrors', 'systemerrors'), 'Error records'),
            $item('Translations', $base . '&c=jssupportticket&layout=translation', 'language-icon.png', $is('jssupportticket', 'translation'), 'Language tools'),
        ),
    )
);

$compactLabels = array(
    'dashboard' => Text::_('Home'),
    'tickets' => Text::_('Tickets'),
    'people' => Text::_('People'),
    'workflow' => Text::_('Flow'),
    'content' => Text::_('Content'),
    'email' => Text::_('Email'),
    'reports' => Text::_('Reports'),
    'system' => Text::_('System'),
);

foreach ($groups as $groupIndex => $group) {
    $groups[$groupIndex]['short_label'] = isset($compactLabels[$group['key']]) ? $compactLabels[$group['key']] : $group['label'];
    $groups[$groupIndex]['active'] = false;
    foreach ($group['items'] as $navItem) {
        if (!empty($navItem['active'])) {
            $groups[$groupIndex]['active'] = true;
            break;
        }
    }
}

$quickItems = array(
    $item('Control Panel', $base . '&c=jssupportticket&layout=controlpanel', 'dashboard.png', $is('jssupportticket', array('controlpanel', '')), 'Overview and activity'),
    $item('All Tickets', $base . '&c=ticket&layout=tickets', 'tickets.png', $is('ticket', 'tickets'), 'Search and manage'),
    $item('Create Ticket', $base . '&c=ticket&layout=formticket', 'ad-ons.png', $is('ticket', 'formticket'), 'Open a support request'),
    $item('Reports', '', 'report.png', false, 'System activity', 'reports'),
);
?>
<nav id="jsst-modern-admin-nav" class="jsst-modern-admin-nav" aria-label="<?php echo htmlspecialchars(Text::_('JS Support Ticket administration navigation'), ENT_QUOTES, 'UTF-8'); ?>">
    <div class="jsst-modern-nav-head">
        <a class="jsst-modern-brand" href="<?php echo htmlspecialchars($base . '&c=jssupportticket&layout=controlpanel', ENT_QUOTES, 'UTF-8'); ?>">
            <span class="jsst-modern-brand-mark"><img alt="" src="components/com_jssupportticket/include/images/jsst-support-icon-v26.png"></span>
            <span class="jsst-modern-brand-text">
                <strong><?php echo Text::_('JS Support Ticket'); ?></strong>
                <small><?php echo Text::_('Administration'); ?></small>
            </span>
        </a>
        <button type="button" class="jsst-modern-nav-toggle" aria-expanded="false" aria-controls="jsst-modern-nav-groups" aria-label="<?php echo htmlspecialchars(Text::_('Open navigation menu'), ENT_QUOTES, 'UTF-8'); ?>">
            <span></span><span></span><span></span>
            <em><?php echo Text::_('Menu'); ?></em>
        </button>
    </div>

    <div class="jsst-modern-nav-body">
        <div class="jsst-modern-nav-quick" aria-label="<?php echo htmlspecialchars(Text::_('Quick links'), ENT_QUOTES, 'UTF-8'); ?>">
            <?php foreach ($quickItems as $quick) : ?>
                <a class="jsst-modern-quick-link <?php echo $quick['active'] ? 'is-active' : ''; ?> <?php echo !empty($quick['pro']) ? 'is-pro' : ''; ?>" href="<?php echo htmlspecialchars($quick['href'], ENT_QUOTES, 'UTF-8'); ?>">
                    <img alt="" src="<?php echo htmlspecialchars($quick['icon'], ENT_QUOTES, 'UTF-8'); ?>">
                    <span><?php echo $quick['label']; ?><?php if (!empty($quick['pro'])) : ?><span class="jsst-pro-star" aria-hidden="true">*</span><?php endif; ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div id="jsst-modern-nav-groups" class="jsst-modern-nav-groups">
            <?php foreach ($groups as $group) : ?>
                <details class="jsst-modern-nav-group jsst-modern-nav-group-<?php echo htmlspecialchars($group['key'], ENT_QUOTES, 'UTF-8'); ?> <?php echo !empty($group['active']) ? 'is-active' : ''; ?>" data-jsst-menu-group="<?php echo htmlspecialchars($group['key'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo !empty($group['active']) ? 'data-jsst-active="1"' : ''; ?>>
                    <summary title="<?php echo htmlspecialchars($group['label'], ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="jsst-modern-group-icon"><img alt="" src="<?php echo htmlspecialchars($group['icon'], ENT_QUOTES, 'UTF-8'); ?>"></span>
                        <span class="jsst-modern-group-label"><?php echo $group['short_label']; ?></span>
                        <i aria-hidden="true"></i>
                    </summary>
                    <div class="jsst-modern-submenu">
                        <?php foreach ($group['items'] as $navItem) : ?>
                            <?php
                                $itemIsPro = !empty($navItem['pro']);
                                $itemTitle = $itemIsPro
                                    ? $navItem['label'] . ' - ' . Text::_('available in the Pro version')
                                    : $navItem['label'];
                            ?>
                            <a class="jsst-modern-submenu-link <?php echo $navItem['active'] ? 'is-active' : ''; ?> <?php echo $itemIsPro ? 'is-pro' : ''; ?>" href="<?php echo htmlspecialchars($navItem['href'], ENT_QUOTES, 'UTF-8'); ?>" data-jsst-menu-item="<?php echo htmlspecialchars($navItem['label'], ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="jsst-modern-submenu-icon"><img alt="" src="<?php echo htmlspecialchars($navItem['icon'], ENT_QUOTES, 'UTF-8'); ?>"></span>
                                <span class="jsst-modern-submenu-copy">
                                    <strong><?php echo $navItem['label']; ?><?php if ($itemIsPro) : ?><span class="jsst-pro-star" aria-hidden="true">*</span><?php endif; ?></strong>
                                    <?php if (!empty($navItem['note'])) : ?>
                                        <small><?php echo $navItem['note']; ?><?php if ($itemIsPro) : ?> &middot; <?php echo Text::_('Pro'); ?><?php endif; ?></small>
                                    <?php endif; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<script type="text/javascript" id="jsst-modern-admin-menu-script">
(function () {
    var root = document.getElementById('jsst-modern-admin-nav');
    if (!root) { return; }

    var toggle = root.querySelector('.jsst-modern-nav-toggle');
    var groupsWrap = root.querySelector('.jsst-modern-nav-groups');
    var groups = Array.prototype.slice.call(root.querySelectorAll('details.jsst-modern-nav-group'));

    function isMobile() {
        return window.matchMedia && window.matchMedia('(max-width: 900px)').matches;
    }

    function closeGroups(except) {
        groups.forEach(function (details) {
            if (details !== except) {
                details.removeAttribute('open');
            }
        });
    }

    function closeMenu() {
        root.classList.remove('is-open');
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        }
        if (isMobile()) {
            closeGroups(null);
        }
    }

    function openMenu() {
        root.classList.add('is-open');
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'true');
        }
        if (groupsWrap) {
            groupsWrap.scrollTop = 0;
        }
        if (isMobile()) {
            var active = root.querySelector('details.jsst-modern-nav-group[data-jsst-active="1"]');
            if (active) {
                active.setAttribute('open', 'open');
            }
        }
    }

    if (toggle) {
        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            if (root.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }

    groups.forEach(function (details) {
        details.addEventListener('toggle', function () {
            if (details.open) {
                closeGroups(details);
            }
        });
    });

    root.addEventListener('click', function (event) {
        var link = event.target.closest ? event.target.closest('a') : null;
        if (link && isMobile()) {
            closeMenu();
        }
    });

    document.addEventListener('click', function (event) {
        if (!root.contains(event.target)) {
            closeGroups(null);
            if (isMobile()) {
                closeMenu();
            }
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeGroups(null);
            closeMenu();
        }
    });

    window.addEventListener('resize', function () {
        if (!isMobile()) {
            root.classList.remove('is-open');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        }
    });
})();
</script>
