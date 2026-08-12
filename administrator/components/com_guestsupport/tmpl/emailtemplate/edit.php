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
?>

<div class="r-guest-support r-gs-email-template">
	<form action="<?php echo Route::_('index.php?option=com_guestsupport&view=emailtemplate&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="r_gs_email_template_form" class="form-validate">
		<div class="r-gs-settings-form-wrapper">
			<div class="r-gs-grid">
				<div class="r-gs-block r-gs-size-60">
					<div class="r-gs-form-field">
						<label for="name"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_TEMPLATES_NAME_LABEL' ); ?></label>
						<input name="name" id="name" class="form-control" type="text" value="<?php echo $this->escape( $this->item->name ); ?>" size="40" placeholder="<?php echo Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_NAME_PLACEHOLDER'); ?>">
					</div>
					<div class="r-gs-form-field">
						<label for="subject"><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_TEMPLATES_SUBJECT_LABEL' ); ?></label>
						<input name="subject" id="subject" class="form-control" type="text" value="<?php echo $this->escape( $this->item->subject ); ?>" size="40" placeholder="<?php echo Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_NAME_PLACEHOLDER'); ?>">
						<?php if ( $this->item->type !== 'otp_email' ) : ?>
							<p><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_TEMPLATES_SUBJECT_DESCRIPTION' ) ; ?></p>
						<?php endif; ?>
					</div>
					<h3><?php echo Text::_( 'COM_GUESTSUPPORT_EMAIL_TEMPLATES_EDITOR_TITLE' ) ; ?></h3>
					<div class="r-gs-form-field">
						<?php echo $this->form->getInput('template'); ?>
					</div>
				</div>
				<div class="r-gs-block r-gs-size-5"></div>
				<div class="r-gs-block r-gs-size-35">
					<h3><?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TITLE' ); ?></h3>
					<div class="r-gs-form-field r-gs-settings-placeholders">
						<?php if ( $this->item->type === 'otp_email' ) : ?>
							<p><span>{email}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_SUBMITTER_EMAIL' ); ?></p>
							<p><span>{otp}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_OTP' ); ?></p>
							<p><span>{sitename}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_SITE_NAME' ); ?></p>
							<p><span>{siteurl}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_SITE_URL' ); ?></p>
						<?php elseif ( in_array( $this->item->type, ['user_pre_ticket_close_message', 'agent_pre_ticket_close_message', 'ticket_close_message'] ) ) : ?>
							<p><span>{subject}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_SUBJECT' ); ?></p>
							<p><span>{ticket_id}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_ID' ); ?></p>
							<p><span>{short_ticket_id}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_SHORT_TICKET_ID' ); ?></p>
							<p><span>{ticket_link}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_LINK_URL' ); ?></p>
							<p><span>{department}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_DEPARTMENT_NAME' ); ?></p>
							<p><span>{form}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_FORM_NAME' ); ?></p>
							<p><span>{name}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_SUBMITTER_NAME' ); ?></p>
							<p><span>{email}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_SUBMITTER_EMAIL' ); ?></p>
							<p><span>{agent_name}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_AGENT_NAME' ); ?></p>
							<p><span>{agent_email}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_AGENT_EMAIL' ); ?></p>
							<p><span>{sitename}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_SITE_NAME' ); ?></p>
							<p><span>{siteurl}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_SITE_URL' ); ?></p>
							<p><span>{email_ref}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_EMAIL_REF' ); ?></p>
						<?php else : ?>
							<p><span>{subject}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_SUBJECT' ); ?></p>
							<p><span>{message}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_MESSAGE' ); ?></p>
							<p><span>{ticket_id}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_ID' ); ?></p>
							<p><span>{short_ticket_id}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_SHORT_TICKET_ID' ); ?></p>
							<p><span>{ticket_link}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_TICKET_LINK_URL' ); ?></p>
							<p><span>{custom_fields}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_CUSTOM_FIELDS' ); ?></p>
							<p><span>{attachments}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_ATTACHMENTS' ); ?></p>
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
							<p><span>{email_ref}</span> = <?php echo Text::_( 'COM_GUESTSUPPORT_PLACEHOLDER_EMAIL_REF' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="">
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
