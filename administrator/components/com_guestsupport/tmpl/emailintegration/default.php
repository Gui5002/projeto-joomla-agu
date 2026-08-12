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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Filter\InputFilter;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

$config = GuestsupportHelper::config();
$filter = InputFilter::getInstance();

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

$imap_security = GuestsupportHelper::getConfig( 'imap_security', 'ssl' );
$imap_port = GuestsupportHelper::getConfig( 'imap_port', '993' );
$imap_ticket_form_id = GuestsupportHelper::getConfig( 'imap_ticket_form_id', 1 );
$imap_ticket_department_id = GuestsupportHelper::getConfig( 'imap_ticket_department_id', 1 );
$imap_processing_limit = GuestsupportHelper::getConfig( 'imap_processing_limit', 10 );
$forms = GuestsupportHelper::getForms();
$departments = GuestsupportHelper::departments();
?>

<div class="r-guest-support r-gs-settings">
	<form action="<?php echo Route::_('index.php?option=com_guestsupport&view=settings'); ?>" method="post" name="adminForm" id="r_gs_settings_form" class="form-validate">
		<div class="r-gs-settings-form-wrapper">
			<div class="r-gs-settings-form-content">
				<div class="r-gs-settings-r-gs-form-fields r-gs-grid">
					<div class="r-gs-block r-gs-size-100">
						<div class="r-gs-settings-fields-block">
							<ul class="r-gs-settings-fields-list">
								<li>
									<div class="r-gs-settings-fields-content">
										<p><span class="r-gs-color-red"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FEATURES'); ?></span></p>
										<?php
											echo '<h2>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION') . '</h2>';
											echo '<p>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION_DESC') . '</p>';
											echo '<ol>
												<li>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION_NEW_EMAILS') . '
													<ul>
														<li>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION_NEW_EMAILS_POINT1') . '</li>
														<li>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION_NEW_EMAILS_POINT2') . '</li>
													</ul>
												</li>
												<li>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION_REPLIES') . '
													<ul>
														<li>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION_REPLIES_POINT1') . '</li>
														<li>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION_REPLIES_POINT2') . '</li>
														<li>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION_REPLIES_POINT3') . '</li>
													</ul>
												</li>
											</ol>';
											echo '<p><br>' . Text::_('COM_GUESTSUPPORT_WHAT_IS_EMAIL_INTEGRATION_SUMMARY') . '</p>';
										?>
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
										<h2><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_CONNECTION_DETAILS'); ?></h2>
										<p class="text-danger"><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_INFO_DESCRIPTION'); ?></p>
										<div class="r-gs-spacer-mini"></div>
										<div class="r-gs-form-field">
											<label for="imap_host"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_HOST_LABEL' ); ?></label>
											<input name="imap_host" id="imap_host" class="form-control" type="text" value="<?php echo $filter->clean( GuestsupportHelper::getConfig('imap_host'), 'STRING' ); ?>" size="40" placeholder="imap.gmail.com">
										</div>
										<div class="r-gs-form-field">
											<label for="imap_username"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_USERNAME_LABEL' ); ?></label>
											<input name="imap_username" id="imap_username" class="form-control" type="text" value="<?php echo $filter->clean( GuestsupportHelper::getConfig('imap_username'), 'STRING' ); ?>" size="40">
										</div>
										<div class="r-gs-form-field">
											<label for="imap_password"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_PASSWORD_LABEL' ); ?></label>
											<input name="imap_password" id="imap_password" class="form-control" type="password" value="<?php echo GuestsupportHelper::getConfig('imap_password'); ?>" size="40">
										</div>
										<div class="r-gs-form-field">
											<label for="imap_port"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_PORT_LABEL' ); ?></label>
											<input name="imap_port" id="imap_port" class="form-control" type="number" value="<?php echo $filter->clean( $imap_port, 'INT' ); ?>" size="40" placeholder="993">
										</div>
										<div class="r-gs-form-field">
											<label for="imap_security"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_SECURITY_LABEL' ); ?></label>
											<select name="imap_security" id="imap_security" class="form-select">
												<option value="none"<?php echo $imap_security == 'none' ? ' selected' : ''; ?>><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_SECURITY_NONE' ); ?></option>
												<option value="ssl"<?php echo $imap_security == 'ssl' ? ' selected' : ''; ?>><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_SECURITY_SSL' ); ?></option>
												<option value="tls"<?php echo $imap_security == 'tls' ? ' selected' : ''; ?>><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_SECURITY_STARTTLS' ); ?></option>
											</select>
										</div>
										<div class="r-gs-form-field">
											<label for="imap_processing_limit"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_PROCESSING_LIMIT_LABEL' ); ?></label>
											<input name="imap_processing_limit" id="imap_processing_limit" class="form-control" type="number" value="<?php echo $filter->clean( $imap_processing_limit, 'INT' ); ?>" size="40" placeholder="10">
											<p><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_PROCESSING_LIMIT_DESCRIPTION' ); ?></p>
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
										<h2><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_TICKET_FORM_ID_LABEL'); ?></h2>
										<p for="imap_ticket_form_id"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_TICKET_FORM_ID_DESCRIPTION' ); ?></p>
										<div class="r-gs-spacer-mini"></div>
										<div class="r-gs-form-field">
											<label for="imap_ticket_form_id"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_TICKET_FORM_LABEL' ); ?></label>
											<select name="imap_ticket_form_id" id="imap_ticket_form_id" class="form-select">
												<?php
													if ( !empty( $forms ) )
													{
														$forms_options = '';
														foreach ($forms as $form) {
															$selected = ( $imap_ticket_form_id == $form->id ) ? ' selected' : '';
															$forms_options .= '<option value="' . $form->id . '"' . $selected . '>' . $form->form_name . '</option>';
														}
														echo $forms_options;
													}
												?>
											</select>
										</div>
										<div class="r-gs-form-field">
											<label for="imap_ticket_department_id"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_INTEGRATION_IMAP_TICKET_DEPARTMENT_LABEL' ); ?></label>
											<select name="imap_ticket_department_id" id="imap_ticket_department_id" class="form-select">
												<?php
													if ( !empty( $departments ) )
													{
														$departments_options = '';
														foreach ($departments as $department) {
															$selected = ( $imap_ticket_department_id == $department->id ) ? ' selected' : '';
															$departments_options .= '<option value="' . $department->id . '"' . $selected . '>' . $department->name . '</option>';
														}
														echo $departments_options;
													}
												?>
											</select>
										</div>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h2><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_CRON_JOB_TITLE'); ?></h2>
										<p><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_CRON_JOB_DESCRIPTION'); ?></p>
										<p><strong><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_CRON_JOB_URL'); ?></strong> <code><?php echo Uri::root() . 'index.php?option=com_guestsupport&task=cron.emailtickethandler'; ?></code></p>
										<p><?php echo Text::sprintf('COM_GUESTSUPPORT_EMAIL_INTEGRATION_CRON_JOB_DOCUMENTATION', '<a href="https://www.rcatheme.com/docs/joomla-extensions/guest-support-for-joomla/imap-email-integration-settings" target="_blank">', '</a>'); ?></p>
									</div>
								</li>
								<li>
									<div class="r-gs-settings-fields-content">
										<h2><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_TEST_TITLE'); ?></h2>
										<p><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_TEST_DESCRIPTION'); ?></p>
										<p><a href="<?php echo Uri::root() . 'index.php?option=com_guestsupport&task=cron.emailtickethandler'; ?>" target="_blank"><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_INTEGRATION_TEST_LINK_TEXT'); ?></a></p>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="">
		<input type="hidden" name="settingstype" value="emailintegration">
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
