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
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Filter\InputFilter;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

$config = GuestsupportHelper::config();
$filter = InputFilter::getInstance();

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

?>

<div class="r-guest-support r-gs-settings">
	<form action="<?php echo Route::_('index.php?option=com_guestsupport&view=emailsettings'); ?>" method="post" name="adminForm" id="r_gs_settings_form" class="form-validate">
		<div class="r-gs-settings-form-wrapper">
			<div class="r-gs-settings-form-content">
				<div class="r-gs-settings-r-gs-form-fields r-gs-grid">
					<div class="r-gs-block r-gs-size-50">
						<div class="r-gs-settings-fields-block">
							<ul class="r-gs-settings-fields-list">
								<li>
									<div class="r-gs-settings-fields-content">
										<h2><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_SETTINGS_MAILER_SETTINGS_TITLE') ; ?></h2>
										<div class="r-gs-form-field form-required">
											<label for="mailer"><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_SETTINGS_MAILER_LABEL'); ?></label>
											<select name="mailer" id="r_gs_select_mailer" class="form-select">
												<option value="joomla"<?php echo $config->mailer == 'joomla' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_SETTINGS_MAILER_JOOMLA'); ?></option>
												<option value="smtp"<?php echo $config->mailer == 'smtp' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_SETTINGS_MAILER_SMTP'); ?></option>
											</select>
										</div>
										<div class="r-gs-form-field">
											<label for="mail_from_name"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_FROM_NAME_LABEL' ); ?></label>
											<input name="mail_from_name" id="mail_from_name" class="form-control" type="text" value="<?php echo $filter->clean( $config->mail_from_name, 'STRING' ); ?>" size="40" placeholder="Example Site Name">
										</div>
										<div class="r-gs-form-field">
											<label for="mail_from_email"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_FROM_ADDRESS_LABEL' ); ?></label>
											<input name="mail_from_email" id="mail_from_email" class="form-control" type="email" value="<?php echo PunycodeHelper::emailToUTF8( $this->escape( $config->mail_from_email ) ); ?>" size="40" placeholder="noreply@example.com">
										</div>
										<div id="r_gs_settings_smtp_credentials" class="r-gs-smtp-credentials"<?php echo $config->mailer != 'smtp' ? ' style="display:none;"' : ''; ?>>
											<hr class="r-gs-settings-hr">
											<div class="r-gs-form-field">
												<label for="smtp_username"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_USERNAME_LABEL' ); ?></label>
												<input name="smtp_username" id="smtp_username" class="form-control" type="text" value="<?php echo $filter->clean( $config->smtp_username, 'STRING' ); ?>" size="40">
											</div>
											<div class="r-gs-form-field">
												<label for="smtp_password"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_PASSWORD_LABEL' ); ?></label>
												<input name="smtp_password" id="smtp_password" class="form-control" type="password" value="<?php echo $config->smtp_password; ?>" size="40">
											</div>
											<div class="r-gs-form-field">
												<label for="smtp_host"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_HOST_LABEL' ); ?></label>
												<input name="smtp_host" id="smtp_host" class="form-control" type="text" value="<?php echo $filter->clean( $config->smtp_host, 'STRING' ); ?>" size="40" placeholder="smtp.gmail.com">
											</div>
											<div class="r-gs-form-field">
												<label for="smtp_port"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_PORT_LABEL' ); ?></label>
												<input name="smtp_port" id="smtp_port" class="form-control" type="number" value="<?php echo $filter->clean( $config->smtp_port, 'INT' ); ?>" size="40" placeholder="465">
											</div>
											<div class="r-gs-form-field">
												<label for="smtp_security"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_SECURITY_LABEL' ); ?></label>
												<select name="smtp_security" id="smtp_security" class="form-select">
													<option value="none"<?php echo $config->smtp_security == 'none' ? ' selected' : ''; ?>><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_SECURITY_NONE' ); ?></option>
													<option value="ssl"<?php echo $config->smtp_security == 'ssl' ? ' selected' : ''; ?>><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_SECURITY_SSL' ); ?></option>
													<option value="tls"<?php echo $config->smtp_security == 'tls' ? ' selected' : ''; ?>><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_SETTINGS_SMTP_SECURITY_STARTTLS' ); ?></option>
												</select>
											</div>
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
		<input type="hidden" name="settingstype" value="emailsettings">
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>

<!-- Setup guide -->
<?php if ( $this->guide === true ) : ?>
    <div id="modal_setup_guide_emailsettings" class="r-gs-modal r-gs-modal-active r-gs-setup-guide">
        <div class="r-gs-modal-wrapper">
            <div class="r-gs-modal-container">
                <div class="r-gs-modal-container-wrapper">
                    <div class="r-gs-modal-content">
                        <h4><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_TITLE'); ?></h4>
                        <div class="r-gs-divider"></div>
                        <h3><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_EMAIL_SETTINGS_TITLE'); ?></h3>
                        <p><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_EMAIL_SETTINGS_DESC'); ?></p>
                        <p><em><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_EMAIL_SETTINGS_SAVE_HINT'); ?></em></p>
                        <p><a href="javascript:;" id="r_gs_modal_close" class="button button-cancel r-gs-link-u"><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_LETS_DO_IT'); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- END Setup guide -->
