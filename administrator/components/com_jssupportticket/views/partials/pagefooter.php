<?php
/**
 * Shared admin page footer: branding / copyright block.
 *
 * Optional variables, set by the including template before this include:
 * - $jsstFooterClass (string, optional) extra class(es) added alongside
 *   jsst-admin-footer, for screens (e.g. the dashboard) whose own CSS
 *   targets a more specific footer selector.
 * - $jsstFooterId (string, optional) overrides the default "js-tk-copyright"
 *   id; pass '' to omit the id entirely (e.g. the dashboard footer has no
 *   id today, and #js-tk-copyright carries legacy positioning rules in
 *   jsticketadmin.css that must not start applying to it).
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
$jsstFooterClass = trim('jsst-admin-footer ' . (isset($jsstFooterClass) ? $jsstFooterClass : ''));
$jsstFooterId = isset($jsstFooterId) ? $jsstFooterId : 'js-tk-copyright';
?>
<div<?php echo $jsstFooterId !== '' ? ' id="' . $jsstFooterId . '"' : ''; ?> class="<?php echo $jsstFooterClass; ?>">
    <div class="jsst-admin-footer__inner">
        <div class="jsst-admin-footer__brand">
            <span class="jsst-admin-footer__mark">
                <img alt="<?php echo htmlspecialchars(Text::_('JS Support Ticket'), ENT_QUOTES, 'UTF-8'); ?>" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">
            </span>
            <span class="jsst-admin-footer__name"><?php echo Text::_('JS Support Ticket'); ?></span>
        </div>
        <div class="jsst-admin-footer__meta">
            <span><?php echo Text::_('Powered by'); ?> <a target="_blank" rel="noopener noreferrer" href="https://www.joomsky.com">JoomSky</a></span>
            <span aria-hidden="true">&bull;</span>
            <span>&copy; 2008 - <?php echo date('Y'); ?> <a target="_blank" rel="noopener noreferrer" href="https://www.burujsolutions.com">Buruj Solutions</a>. <?php echo Text::_('All rights reserved.'); ?></span>
        </div>
    </div>
</div>
