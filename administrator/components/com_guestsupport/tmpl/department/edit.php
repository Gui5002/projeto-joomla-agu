<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\User\User;

$isnew = ($this->item->id == 0);

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>

<div class="r-guest-support r-gs-department-form">
    <form action="<?php echo Route::_('index.php?option=com_guestsupport&view=department&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="department_form" class="form-validate">
        <div class="r-gs-container r-gs-padded">
            <div class="r-gs-content">
                <h1 class="r-gs-form-title"><?php echo $isnew ? Text::_('COM_GUESTSUPPORT_DEPARTMENTS_FORM_ADD_NEW') : Text::_('COM_GUESTSUPPORT_DEPARTMENTS_FORM_EDIT') . ' <span>' . $this->escape($this->item->name); ?></span></h1>
                <div class="r-gs-form-wrap">
                    <div class="r-gs-form-field dept-name">
                        <label for="dept-name"><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_FORM_NAME_LABEL'); ?>*</label>
                        <input name="name" id="dept-name" class="form-control" type="text" value="<?php echo $this->escape($this->item->name); ?>" size="40" placeholder="<?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_FORM_NAME_PLACEHOLDER'); ?>" aria-required="true" required>
                    </div>

					<div class="r-gs-form-field dept-form-ids">
                        <label for="form-ids"><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENT_SELECT_FORMS'); ?>*</label>
						<?php echo $this->xmlfields->getInput('form_ids'); ?>
                    </div>

                    <div class="r-gs-form-field dept-agent">
                        <label><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_FORM_AGENT_LABEL'); ?>*</label>
                        <input type="hidden" name="agent_id" id="r_gs_dropdown_search_main" value="<?php echo $this->item->agent_id; ?>">
                        <div class="r-gs-dropdown-search">
                            <?php
                                if ( $this->item->agent_id )
                                {
                                    $agentuser = new User($this->item->agent_id);
                                    $agentinfo = $this->escape($agentuser->name) . ' (' . PunycodeHelper::emailToUTF8($this->escape($agentuser->email)) . ')';
                                }
                                else
                                {
                                    $agentinfo = '';
                                }
                            ?>
                            <input type="text" id="r_gs_dropdown_search_box" class="form-control r-gs-dropdown-search-box" size="40" placeholder="<?php echo Text::_('COM_GUESTSUPPORT_USER_SEARCH_FORM_PLACEHOLDER'); ?>" value="<?php echo $agentinfo; ?>" autocomplete="off">
                            <div class="r-gs-dropdown-search-text"><?php echo $agentinfo; ?></div>
                            <div id="r_gs_dropdown_search_menu" class="r-gs-dropdown-search-menu"></div>
                        </div>
                        <p><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_FORM_AGENT_HELP'); ?></p>
                    </div>

                    <div class="r-gs-form-field additional-emails">
                        <label for="dept-additional-emails"><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_LABEL'); ?></label>
                        <textarea name="additional_emails" id="dept-additional-emails" class="form-control" placeholder="user1@example.com&#10;user2@example.com&#10;user3@example.com" rows="5" cols="40"><?php echo $this->item->additional_emails; ?></textarea>
                        <p><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_HELP'); ?></p>
                    </div>

                    <div class="r-gs-form-field additional-emails-type">
                        <label for="dept-additional-emails-type"><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_TYPE_LABEL'); ?></label>
                        <select name="additional_emails_type" id="dept-additional-emails-type" class="form-select">
                            <option value="both"<?php echo $this->item->additional_emails_type == 'both' ? ' selected' : '';?>><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_TYPE_BOTH'); ?></option>
                            <option value="tickets"<?php echo $this->item->additional_emails_type == 'tickets' ? ' selected' : '';?>><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_TYPE_TICKETS'); ?></option>
                            <option value="replies"<?php echo $this->item->additional_emails_type == 'replies' ? ' selected' : '';?>><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_TYPE_REPLIES'); ?></option>
                        </select>
                        <p><?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_TYPE_HELP'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="">
        <input type="hidden" name="dept_id" value="<?php echo $this->item->id; ?>">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
    <script>
        jQuery(document).ready(function($) {
            $.rgsAddAgent("<?php echo Uri::base() . 'index.php?option=com_guestsupport&view=ajax'; ?>");
        });
    </script>
</div>
