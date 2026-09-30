<?php
/**
 * Shared postinstallation wizard header: centered title + close button
 * back to the dashboard.
 *
 * Expected variable, set by the including template before this include:
 * - $jsstWizardTitle (string, required) translation key for the heading
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
?>
<div class="js-admin-title-installtion">
    <span class="jsst_heading"><?php echo Text::_($jsstWizardTitle); ?></span>
    <div class="close-button-bottom">
        <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" class="close-button">
            <img src="components/com_jssupportticket/include/images/postinstallation/close-icon.png" />
        </a>
    </div>
</div>
