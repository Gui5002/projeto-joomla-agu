<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

$app 	= Factory::getApplication();
$user 	= $app->getIdentity();
$guest 	= $user->guest;

$formFields = unserialize( $this->form->form_fields );
?>
<?php if ( empty( $this->form ) ) :
	echo '<p>' . Text::_('COM_GUESTSUPPORT_FORM_NOT_FOUND') . '</p>';
else : ?>
	<div class="r-guest-support r-gs-create-ticket<?php echo $this->pageclass_sfx; ?>">
		<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" id="svg-icon-global">
			<symbol id="ticket_attachment" viewBox="0 0 448 512">
				<path d="M375 72.97C349.1 46.1 306.9 46.1 280.1 72.97L88.97 264.1C45.32 308.6 45.32 379.4 88.97 423C132.6 466.7 203.4 466.7 247 423L399 271C408.4 261.7 423.6 261.7 432.1 271C442.3 280.4 442.3 295.6 432.1 304.1L280.1 456.1C218.6 519.4 117.4 519.4 55.03 456.1C-7.364 394.6-7.364 293.4 55.03 231L247 39.03C291.7-5.689 364.2-5.689 408.1 39.03C453.7 83.75 453.7 156.3 408.1 200.1L225.2 384.7C193.6 416.3 141.6 413.4 113.7 378.6C89.88 348.8 92.26 305.8 119.2 278.8L271 127C280.4 117.7 295.6 117.7 304.1 127C314.3 136.4 314.3 151.6 304.1 160.1L153.2 312.7C143.5 322.4 142.6 337.9 151.2 348.6C161.2 361.1 179.9 362.2 191.2 350.8L375 167C401 141.1 401 98.94 375 72.97V72.97z" fill="#24292e"></path>
			</symbol>
			<symbol id="ticket_file_upload" viewBox="0 0 512 512">
				<path d="M448 304h-128V352h128c8.822 0 16 7.178 16 16V448c0 8.822-7.178 16-16 16H64c-8.822 0-16-7.178-16-16v-80C48 359.2 55.18 352 64 352h128V304H64c-35.35 0-64 28.65-64 64V448c0 35.35 28.65 64 64 64h384c35.35 0 64-28.65 64-64v-80C512 332.7 483.3 304 448 304zM136.1 176.1L232 81.94V352c0 13.25 10.75 24 24 24s24-10.75 24-24V81.94l95.03 95.03C379.7 181.7 385.8 184 392 184s12.28-2.344 16.97-7.031c9.375-9.375 9.375-24.56 0-33.94l-136-136c-9.375-9.375-24.56-9.375-33.94 0l-136 136c-9.375 9.375-9.375 24.56 0 33.94S127.6 186.3 136.1 176.1zM432 408c0-13.26-10.75-24-24-24S384 394.7 384 408c0 13.25 10.75 24 24 24S432 421.3 432 408z"></path>
			</symbol>
			<symbol id="ticket_file_remove" viewBox="0 0 512 512">
				<path d="M0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256zM168 232C154.7 232 144 242.7 144 256C144 269.3 154.7 280 168 280H344C357.3 280 368 269.3 368 256C368 242.7 357.3 232 344 232H168z" fill="#FFFFFF"></path>
			</symbol>
			<symbol id="ticket_file_plus" viewBox="0 0 448 512">
				<path d="M432 256c0 17.69-14.33 32.01-32 32.01H256v144c0 17.69-14.33 31.99-32 31.99s-32-14.3-32-31.99v-144H48c-17.67 0-32-14.32-32-32.01s14.33-31.99 32-31.99H192v-144c0-17.69 14.33-32.01 32-32.01s32 14.32 32 32.01v144h144C417.7 224 432 238.3 432 256z"/>
			</symbol>
			<symbol id="ticket_info" viewBox="0 0 512 512">
				<path d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0zM256 128c17.67 0 32 14.33 32 32c0 17.67-14.33 32-32 32S224 177.7 224 160C224 142.3 238.3 128 256 128zM296 384h-80C202.8 384 192 373.3 192 360s10.75-24 24-24h16v-64H224c-13.25 0-24-10.75-24-24S210.8 224 224 224h32c13.25 0 24 10.75 24 24v88h16c13.25 0 24 10.75 24 24S309.3 384 296 384z"/>
			</symbol>
		</svg>

		<div class="r-gs-create-ticket-wrapper">
			<h1 class="r-gs-create-ticket-title">
				<?php echo $this->escape( $this->page_title ); ?>
			</h1>
			<form id="r_gs_submit_ticket_form" action="<?php echo Route::_('index.php?option=com_guestsupport&task=createticket.submit', false); ?>" class="<?php echo ( $this->recaptcha_enabled ? 'r-gs-verify-captcha' : '' ) ; ?>" method="post" enctype="multipart/form-data">
				<div class="r-gs-grid r-gs-fields-wrapper">
					<?php echo $this->formFields; ?>

					<?php if ( $this->form->fileupload ) : ?>
						<div id="r-gs-block-attachments" class="r-gs-block r-gs-size-100">
							<div class="r-gs-field">
								<label for="attachment_1" class="r-gs-field-fileupload-label"><?php echo Text::_( $this->filter->clean($this->form->fileupload_label, 'STRING') ) . ' <span>(' . Text::_( $this->filter->clean($this->form->fileupload_help, 'STRING') ) . ')</span>'; ?></label>
								<ul id="r_gs_fileupload_block" class="r-gs-fileupload-block">
									<li>
										<div class="r-gs-field-file">
											<input type="file" name="attachments[]" id="attachment_1" class="r-gs-field-fileupload" value="" size="25">
											<label class="r-gs-label-fileupload r-gs-grid r-gs-vcenter" for="attachment_1"><svg class="r-gs-fileupload-icon" width="14px" height="14px"><use href="#ticket_file_upload"></use></svg><span class="r-gs-block r-gs-block-fixed"><?php echo Text::_('COM_GUESTSUPPORT_FORM_CHOOSE_A_FILE'); ?></span></label>
											<span class="r-gs-fileupload-remove"><svg class="r-gs-fileremove-icon" width="14px" height="14px"><use href="#ticket_file_remove"></use></svg><span class="r-gs-block r-gs-block-fixed"><?php echo Text::_('COM_GUESTSUPPORT_REMOVE'); ?></span></span>
										</div>
									</li>
									<?php if ( (int) $this->form->upload_filelimit > 1 ) : ?>
										<li class="r-gs-field-block-addnew">
											<div class="r-gs-field-file">
												<div class="r-gs-field-file-addnew"><svg class="r-gs-fileaddblock-icon" width="14px" height="14px"><use href="#ticket_file_plus"></use></svg></div>
											</div>
										</li>
									<?php endif; ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
					<?php echo $this->verify_email_html; ?>
				</div>
				<?php if ( $this->ispro && $this->verify_email_html ) : ?>
					<div id="r_gs_verify_notice" class="r-gs-notice r-gs-grid r-gs-vcenter r-gs-verify-notice" style="display: none;">
						<span><svg class="r-gs-tickets-info-icon" width="20px" height="20px"><use href="#ticket_info"></use></svg></span>
						<span id="r_gs_verify_notice_msg"></span>
					</div>
				<?php endif; ?>
				<div class="r-gs-create-ticket-submit">
					<?php echo HTMLHelper::_('form.token'); ?>
					<input type="hidden" id="r_gs_baseurl" value="<?php echo Uri::root(); ?>">
					<input type="hidden" name="form_id" id="form_id" value="<?php echo (int) $this->escape( $this->form->id ); ?>">
					<button type="submit" id="guest_support_submit_ticket" class="<?php echo $this->escape( $this->form->submit_class ); ?>"><?php echo ( $this->ispro && $this->form->verify_otp_text ? Text::_( $this->filter->clean($this->form->verify_otp_text, 'STRING') ) : Text::_( $this->filter->clean($this->form->submit_text, 'STRING') ) ); ?></button>
				</div>
			</form>
		</div>
	</div>
<?php endif; ?>