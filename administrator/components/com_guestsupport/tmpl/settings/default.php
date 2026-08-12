<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Filter\InputFilter;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

$config = GuestsupportHelper::config();
$filter = InputFilter::getInstance();

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

$view_attachments = GuestsupportHelper::getConfig( 'view_attachments', 'browser' );
?>

<div class="r-guest-support r-gs-settings">
	<form action="<?php echo Route::_('index.php?option=com_guestsupport&view=settings'); ?>" method="post" name="adminForm" id="r_gs_settings_form" class="form-validate">
		<!-- <h1 class="r-gs-form-title"><?php //echo Text::_('COM_GUESTSUPPORT_SETTINGS_FORM_TITLE'); ?></h1> -->
		<div class="r-gs-settings-form-wrapper">
			<div class="r-gs-settings-form-content">
				<div class="r-gs-settings-form-fields r-gs-grid">
					<div class="r-gs-block r-gs-size-50">
						<div class="r-gs-settings-fields-block">
							<ul class="r-gs-settings-fields-list">
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_RECAPTCHA_V3_OPTIONS'); ?></h3>
										<div class="r-gs-form-field form-required">
											<label for="recaptcha_site_key"><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_RECAPTCHA_SITE_KEY_LABEL'); ?></label>
											<input name="recaptcha_site_key" id="recaptcha_site_key" class="form-control" type="text" value="<?php echo $filter->clean( $config->recaptcha_site_key, 'STRING' ); ?>" size="40">
										</div>
										<div class="r-gs-form-field">
											<label for="recaptcha_secret_key"><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_RECAPTCHA_SECRET_KEY_LABEL'); ?></label>
											<input name="recaptcha_secret_key" id="recaptcha_secret_key" class="form-control" type="text" value="<?php echo $filter->clean( $config->recaptcha_secret_key, 'STRING' ); ?>" size="40">
										</div>
										<div class="r-gs-form-field">
											<label for="recaptcha_score"><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_RECAPTCHA_SCORE_LABEL'); ?></label>
											<input name="recaptcha_score" id="recaptcha_score" type="number" class="form-control" min="0" max="1" step="0.1" value="<?php echo $filter->clean( $config->recaptcha_score, 'STRING' ); ?>" size="40">
										</div>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_KB_OPTIONS'); ?></h3>
										<div class="r-gs-form-field">
											<p class="r-gs-color-red"><strong><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FEATURES'); ?></strong></p>
											<label for="kbs_ignore_words"><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_KB_IGNORE_WORDS_LABEL'); ?></label>
											<textarea name="kbs_ignore_words" id="kbs_ignore_words" class="form-control" rows="3" cols="40"><?php echo $filter->clean( $config->kbs_ignore_words, 'STRING' ); ?></textarea>
											<p><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_KB_IGNORE_WORDS_DESC'); ?></p>
										</div>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_TICKETS_REOPEN_OPTIONS'); ?></h3>
										<div class="r-gs-form-field">
											<ul class="r-gs-field-checkbox r-gs-field-checkbox-block" >
												<li><input type="radio" name="reopen_closed_ticket" id="reopen_closed_ticket_no" value="no"<?php echo $config->reopen_closed_ticket == 'no' ? ' checked' : ''; ?>><label for="reopen_closed_ticket_no" class="r-gs-field-checkbox-label"> <?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_TICKETS_REOPEN_CANT'); ?></label></li>
												<li><input type="radio" name="reopen_closed_ticket" id="reopen_closed_ticket_agent" value="agent"<?php echo $config->reopen_closed_ticket == 'agent' ? ' checked' : ''; ?>><label for="reopen_closed_ticket_agent" class="r-gs-field-checkbox-label"> <?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_TICKETS_REOPEN_AGENT'); ?></label></li>
												<li><input type="radio" name="reopen_closed_ticket" id="reopen_closed_ticket_user" value="user"<?php echo $config->reopen_closed_ticket == 'user' ? ' checked' : ''; ?>><label for="reopen_closed_ticket_user" class="r-gs-field-checkbox-label"> <?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_TICKETS_REOPEN_USER'); ?></label></li>
												<li><input type="radio" name="reopen_closed_ticket" id="reopen_closed_ticket_both" value="both"<?php echo $config->reopen_closed_ticket == 'both' ? ' checked' : ''; ?>><label for="reopen_closed_ticket_both" class="r-gs-field-checkbox-label"> <?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_TICKETS_REOPEN_BOTH'); ?></label></li>
											</ul>
										</div>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CREATE_TICKET_TITLE'); ?></h3>
										<div class="r-gs-form-field">
											<p class="r-gs-color-red"><strong><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FEATURES'); ?></strong></p>
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_AGENTS_CREATE_TICKET'); ?></h4>
											<select name="tickets_by_agent" class="form-select">
												<option value="yes"<?php echo $config->tickets_by_agent == 'yes' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_YES'); ?></option>
												<option value="no"<?php echo $config->tickets_by_agent == 'no' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_NO'); ?></option>
											</select>
										</div>
										<div class="r-gs-form-field">
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CREATE_TICKET_URL'); ?></h4>
											<input type="text" name="create_ticket_url" id="create_ticket_url" class="form-control" value="<?php echo $filter->clean( $config->create_ticket_url, 'STRING' ); ?>" placeholder="https://example.com/submit-ticket" >
											<p><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CREATE_TICKET_URL_DESC'); ?></p>
										</div>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_ACTIONS_AFTER_TICKET_CREATION'); ?></h3>
										<div class="r-gs-form-field">
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_AFTER_TICKET_CREATED'); ?></h4>
											<select name="after_new_ticket_created" class="form-select">
												<option value="redirect"<?php echo $config->after_new_ticket_created == 'redirect' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_AFTER_TICKET_CREATED_REDIRECT'); ?></option>
												<option value="show_message"<?php echo $config->after_new_ticket_created == 'show_message' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_AFTER_TICKET_CREATED_SHOW_MESSAGE'); ?></option>
											</select>
										</div>
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_NEW_TICKET_CONFIRMATION_MESSAGE'); ?></h3>
										<p><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_NEW_TICKET_CONFIRMATION_MESSAGE_DESC'); ?></p>
										<div class="form-field r-gs-settings-placeholders">
											<h3><?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TITLE' ); ?></h3>
											<p><span>{subject}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_SUBJECT' ); ?></p>
											<p><span>{ticket_id}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_ID' ); ?></p>
											<p><span>{short_ticket_id}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_SHORT_TICKET_ID' ); ?></p>
											<p><span>{ticket_link}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_LINK_URL' ); ?></p>
											<p><span>{department}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_DEPARTMENT_NAME' ); ?></p>
											<p><span>{form}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_FORM_NAME' ); ?></p>
											<p><span>{name}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_SUBMITTER_NAME' ); ?></p>
											<p><span>{email}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_SUBMITTER_EMAIL' ); ?></p>
											<p><span>{is_registered_user}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_IS_REGISTERED_USER' ); ?></p>
											<p><span>{user_id}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_USER_ID' ); ?></p>
											<p><span>{ip_address}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_IP_ADDRESS' ); ?></p>
											<p><span>{agent_name}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_AGENT_NAME' ); ?></p>
											<p><span>{agent_email}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_AGENT_EMAIL' ); ?></p>
											<p><span>{sitename}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_SITE_NAME' ); ?></p>
											<p><span>{siteurl}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_SITE_URL' ); ?></p>
										</div>
										<div class="r-gs-form-field">
											<?php echo $this->form->getInput('new_ticket_confirmation_message'); ?>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<div class="r-gs-block r-gs-size-50">
						<div class="r-gs-settings-fields-block">
							<ul class="r-gs-settings-fields-list">
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_TICKETS_DELETE_OPTIONS'); ?></h3>
										<div class="r-gs-form-field">
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_AGENTS_DELETE_TICKETS'); ?></h4>
											<select name="can_delete_tickets" class="form-select">
												<option value="yes"<?php echo $config->can_delete_tickets == 'yes' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_YES'); ?></option>
												<option value="no"<?php echo $config->can_delete_tickets == 'no' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_NO'); ?></option>
											</select>
										</div>
										<div class="r-gs-form-field">
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_AGENTS_DELETE_REPLIES'); ?></h4>
											<select name="can_delete_replies" class="form-select">
												<option value="yes"<?php echo $config->can_delete_replies == 'yes' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_YES'); ?></option>
												<option value="no"<?php echo $config->can_delete_replies == 'no' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_NO'); ?></option>
											</select>
											<p><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_TICKETS_DELETE_INFO'); ?></p>
										</div>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_EDIT_MESSAGE_OPTIONS'); ?></h3>
										<div class="r-gs-form-field">
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_WHO_CAN_EDIT_MESSAGE'); ?></h4>
											<select name="can_edit_replies" class="form-select">
												<option value="none"<?php echo $config->can_edit_replies == 'none' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_EDIT_MESSAGE_NO_ONE'); ?></option>
												<option value="agent"<?php echo $config->can_edit_replies == 'agent' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_EDIT_MESSAGE_AGENTS'); ?></option>
												<option value="user"<?php echo $config->can_edit_replies == 'user' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_EDIT_MESSAGE_USERS'); ?></option>
												<option value="both"<?php echo $config->can_edit_replies == 'both' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_EDIT_MESSAGE_BOTH'); ?></option>
											</select>
										</div>
										<div class="r-gs-form-field">
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_EDIT'); ?></h4>
											<select name="edit_reply_type" class="form-select">
												<option value="all"<?php echo $config->edit_reply_type == 'all' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_EDIT_ALL'); ?></option>
												<option value="last"<?php echo $config->edit_reply_type == 'last' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_EDIT_LAST'); ?></option>
											</select>
										</div>
										<div class="r-gs-form-field">
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_CAN_EDIT_GLOBALLY'); ?></h4>
											<select name="edit_replies_globally" class="form-select">
												<option value="yes"<?php echo $config->edit_replies_globally == 'yes' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_YES'); ?></option>
												<option value="no"<?php echo $config->edit_replies_globally == 'no' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_NO'); ?></option>
											</select>
										</div>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_DEFAULT_SIGNATURE'); ?></h3>
										<div class="r-gs-form-field">
											<p class="r-gs-color-red"><strong><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FEATURES'); ?></strong></p>
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_DEFAULT_SIGNATURE_FOR'); ?></h4>
											<select name="enable_signature" class="form-select">
												<option value="both"<?php echo $config->enable_signature == 'both' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_DEFAULT_SIGNATURE_FOR_BOTH'); ?></option>
												<option value="agent"<?php echo $config->enable_signature == 'agent' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_DEFAULT_SIGNATURE_FOR_AGENTS'); ?></option>
												<option value="user"<?php echo $config->enable_signature == 'user' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_DEFAULT_SIGNATURE_FOR_USERS'); ?></option>
												<option value="none"<?php echo $config->enable_signature == 'none' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_DEFAULT_SIGNATURE_DISABLE'); ?></option>
											</select>
											<p><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_DEFAULT_SIGNATURE_DESC'); ?></p>
										</div>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_TICKET_VIEW_PAGE'); ?></h3>
										<div class="r-gs-form-field">
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_MESSAGE_DIRECTION'); ?></h4>
											<select name="message_direction" class="form-select">
												<option value="top"<?php echo $config->message_direction == 'top' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_TOP_TO_BOTTOM'); ?></option>
												<option value="bottom"<?php echo $config->message_direction == 'bottom' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_BOTTOM_TO_TOP'); ?></option>
											</select>
										</div>
										<div class="r-gs-form-field">
											<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_SCROLL_TO_LAST_REPLY'); ?></h4>
											<select name="scroll_to_last" class="form-select">
												<option value="yes"<?php echo $config->scroll_to_last == 'yes' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_YES'); ?></option>
												<option value="no"<?php echo $config->scroll_to_last == 'no' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_NO'); ?></option>
											</select>
										</div>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_AUTO_CLOSE_TICKETS_TITLE'); ?></h3>
										<p class="r-gs-color-red"><strong><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FEATURES'); ?></strong></p>
										<div class="r-gs-form-field">
											<label for="auto_close_tickets"><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_AUTO_CLOSE_TICKETS_DAYS'); ?></label>
											<input type="number" name="auto_close_tickets" id="auto_close_tickets" class="form-control" value="<?php echo $filter->clean( $config->auto_close_tickets, 'INT' ); ?>" placeholder="7" >
											<p><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_AUTO_CLOSE_TICKETS_DESC'); ?></p>
										</div>
										<div class="r-gs-form-field">
											<label for="pre_close_email"><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_PRE_CLOSE_EMAIL_DAYS'); ?></label>
											<input type="number" name="pre_close_email" id="pre_close_email" class="form-control" value="<?php echo $filter->clean( $config->pre_close_email, 'INT' ); ?>" placeholder="6" >
											<p><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_PRE_CLOSE_EMAIL_DESC'); ?></p>
										</div>
										<p><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_AUTO_CLOSE_TICKETS_NOTE'); ?> <a href="https://www.rcatheme.com/docs/joomla-extensions/guest-support-for-joomla/setup-cron-job-to-send-pre-close-email-notifications-and-auto-close-tickets" target="_blank">https://www.rcatheme.com/docs/joomla-extensions/guest-support-for-joomla/setup-cron-job-to-send-pre-close-email-notifications-and-auto-close-tickets</a></p>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h3><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_VIEWING_ATTACHMENTS_TITLE'); ?></h3>
										<div class="r-gs-form-field">
										<h4><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_VIEWING_ATTACHMENTS_LABEL'); ?></h4>
											<select name="view_attachments" class="form-select">
												<option value="browser"<?php echo $view_attachments == 'browser' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_VIEWING_ATTACHMENTS_OPTION_BROWSER'); ?></option>
												<option value="download"<?php echo $view_attachments == 'download' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_VIEWING_ATTACHMENTS_OPTION_DOWNLOAD'); ?></option>
											</select>
											<p><?php echo Text::_('COM_GUESTSUPPORT_SETTINGS_VIEWING_ATTACHMENTS_DESCRIPTION'); ?></p>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="">
		<input type="hidden" name="settingstype" value="settings">
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>

<!-- Setup guide -->
<?php if ( $this->guide === true ) : ?>
    <div id="modal_setup_guide_settings" class="r-gs-modal r-gs-modal-active r-gs-setup-guide">
        <div class="r-gs-modal-wrapper">
            <div class="r-gs-modal-container">
                <div class="r-gs-modal-container-wrapper">
                    <div class="r-gs-modal-content">
                        <h4><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_TITLE'); ?></h4>
                        <div class="r-gs-divider"></div>
                        <h3><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_SETTINGS_TITLE'); ?></h3>
                        <p><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_SETTINGS_DESC'); ?></p>
                        <p><em><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_SETTINGS_SAVE_HINT'); ?></em></p>
                        <p><a href="javascript:;" id="r_gs_modal_close" class="button button-cancel r-gs-link-u"><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_LETS_DO_IT'); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- END Setup guide -->

