<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

$picture_dirurl = Uri::root() . 'images/com_guestsupport/';
$show_reopen_link_to_both = $this->config->reopen_closed_ticket === 'both';
$show_reopen_link_to_users = $this->config->reopen_closed_ticket === 'user' || $show_reopen_link_to_both;
$show_reopen_link_to_agents = $this->config->reopen_closed_ticket === 'agent' || $show_reopen_link_to_both;

?>
<?php if ( empty( $this->ticket ) ) :
	echo '<p>' . Text::_('COM_GUESTSUPPORT_TICKET_NOT_FOUND') . '</p>';
else : ?>
<div class="r-guest-support r-gs-ticket<?php echo $this->pageclass_sfx; ?>">
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
		<symbol id="ticket_file_delete" viewBox="0 0 576 512">
			<path d="M432.1 208.1L385.9 256L432.1 303C442.3 312.4 442.3 327.6 432.1 336.1C423.6 346.3 408.4 346.3 399 336.1L352 289.9L304.1 336.1C295.6 346.3 280.4 346.3 271 336.1C261.7 327.6 261.7 312.4 271 303L318.1 256L271 208.1C261.7 199.6 261.7 184.4 271 175C280.4 165.7 295.6 165.7 304.1 175L352 222.1L399 175C408.4 165.7 423.6 165.7 432.1 175C442.3 184.4 442.3 199.6 432.1 208.1V208.1zM512 64C547.3 64 576 92.65 576 128V384C576 419.3 547.3 448 512 448H205.3C188.3 448 172 441.3 160 429.3L9.372 278.6C3.371 272.6 0 264.5 0 256C0 247.5 3.372 239.4 9.372 233.4L160 82.75C172 70.74 188.3 64 205.3 64L512 64zM528 128C528 119.2 520.8 112 512 112H205.3C201 112 196.9 113.7 193.9 116.7L54.63 256L193.9 395.3C196.9 398.3 201 400 205.3 400H512C520.8 400 528 392.8 528 384V128z"/>
        </symbol>
		<symbol id="ticket_lock" viewBox="0 0 448 512">
			<path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/>
        </symbol>
    </svg>
    <div class="r-gs-grid">
        <div class="r-gs-block r-gs-size-65 r-gs-ticket-messages">
            <div class="r-gs-ticket-content">
                <h1 class="r-gs-ticket-subject"><?php echo $this->escape($this->subject->subject); ?></h1>
                <div class="r-gs-ticket-messages-wrapper">
                    <?php foreach( $this->ticket as $message ) : ?>
                        <div <?php echo ($message === end($this->ticket) ? 'id="r-gs-ticket-message-last" ' : ''); ?>class="r-gs-ticket-message <?php echo $this->escape( $message->user_type ); ?>">
                            <div class="r-gs-ticket-message-spacer">&nbsp;</div>
                            <div class="r-gs-ticket-message-header r-gs-grid r-gs-vcenter">
                                <div class="r-gs-ticket-avatar">
                                    <?php 
                                        echo '<span class="r-gs-grid r-gs-vcenter">';
										if ( $message->user_type == 'agent' )
										{
											$agent_profile = GuestsupportHelper::userSettings( (int) $message->agent_id );
										}
										else
										{
											$agent_profile = '';
										}

                                        if ( $message->user_type == 'user' && !empty( $this->userProfile ) )
                                        {
                                            echo '<img src="' . $picture_dirurl . $this->userProfile->picture . '" alt="' . $this->escape( $message->name ) . '">';
                                        }
                                        elseif ( !empty( $agent_profile ) )
                                        {
											echo '<img src="' . $picture_dirurl . $agent_profile->picture . '" alt="' . $this->escape( $message->name ) . '">';
                                        }
                                        else
                                        {
                                            $first_char = substr($message->name, 0, 1);
                                            echo $first_char[0] ? strtoupper( $this->filter->clean($first_char[0], 'STRING') ) : '?';
                                        }
                                        echo '</span>';
                                    ?>
                                </div>
                                <div class="r-gs-ticket-message-name-time">
                                    <h4><?php echo $this->filter->clean( $message->name, 'STRING' ); ?></h4>
                                    <p>
                                        <span><?php echo $this->filter->clean( GuestsupportHelper::timeElapsed($message->created_date), 'STRING' ); ?></span>
                                        <?php
                                            if ( $this->config->can_edit_replies == 'both' || ( $this->config->can_edit_replies == 'agent' && $this->is_agent ) || ( $this->config->can_edit_replies == 'user' && !$this->is_agent ) )
                                            {
                                                if ( ( $this->is_agent && $this->config->edit_replies_globally == 'yes' ) || $this->config->edit_reply_type == 'all' )
                                                {
                                                    echo '&nbsp;|&nbsp;<span><a href="javascript:;" id="r_gs_open_modal" class="r_gs_open_edit_reply_modal" data-modal="r_gs_edit_reply_modal" data-ticketid="' . $this->escape( $message->ticket_id ) . '" data-ticketauth="' . $this->escape( $message->ticket_token ) . '" data-messageid="' . $this->escape( GuestsupportHelper::encrypt( $message->id ) ) . '" data-agent="' . $this->encrypted_agent_id . '">' . Text::_('COM_GUESTSUPPORT_EDIT') . '</a></span>';
                                                }
                                                elseif ( $this->config->edit_reply_type == 'last' && $message === end($this->ticket) && ( ( $message->status == 'pending' && $this->is_agent ) || ( $message->status == 'open' && !$this->is_agent ) ) ) {
                                                    echo '&nbsp;|&nbsp;<span><a href="javascript:;" id="r_gs_open_modal" class="r_gs_open_edit_reply_modal" data-modal="r_gs_edit_reply_modal" data-ticketid="' . $this->escape( $message->ticket_id ) . '" data-ticketauth="' . $this->escape( $message->ticket_token ) . '" data-messageid="' . $this->escape( GuestsupportHelper::encrypt( $message->id ) ) . '" data-agent="' . $this->encrypted_agent_id . '">' . Text::_('COM_GUESTSUPPORT_EDIT') . '</a></span>';
                                                }
                                            }
                                        ?>
                                        <?php
                                            if ( $this->is_agent && $message->message_type == 'reply' && $this->config->can_delete_replies == 'yes' )
                                            {
                                                echo '&nbsp;|&nbsp;<span><a id="r-gs-delete" href="' . Route::_( $this->ticket_url . '&task=ticket.deletereply&replyid=' . GuestsupportHelper::encrypt( $message->id ), false ) . '">' . Text::_('COM_GUESTSUPPORT_DELETE') . '</a></span>';
                                            }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="r-gs-ticket-message-content">
                                <div class="r-gs-ticket-message-contentblock">
                                    <?php echo GuestsupportHelper::sanitizeMessageOutput( $message->message ); ?>
                                </div>
                                <?php if ( isset( $message->files ) && !empty( $message->files ) ) : ?>
                                    <div class="r-gs-ticket-message-attachments">
										<?php echo $this->displayAttachments( $message->files, $message->user_type ); ?>
                                    </div>
                                <?php endif; ?>
                                <?php
                                    $reply_custom_fields = unserialize( $message->custom_fields );
                                    if ( !empty( $reply_custom_fields ) )
                                    {
										echo $this->displayCustomFields( $reply_custom_fields );
                                    }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
				<div id="r_gs_system_msg"></div>
                <div class="r-gs-ticket-reply-wrapper">
                    <?php if ( $this->subject->status != 'closed' ) : ?>
                        <h3><?php echo Text::_('COM_GUESTSUPPORT_REPLY_FORM_TITLE'); ?></h3>
                        <div class="r-gs-form-wrapper">
                            <form id="r_gs_reply_form" action="<?php echo Route::_('index.php?option=com_guestsupport&task=createticket.reply' . $this->agent_url_param, false); ?>" method="post" enctype="multipart/form-data">
                                <div class="r-gs-grid">
                                    <div class="r-gs-block r-gs-size-100">
                                        <div class="r-gs-field <?php echo $this->escape( $this->form->input_class ); ?>">
                                            <label for="r-gs-field-message"><?php echo Text::_('COM_GUESTSUPPORT_REPLY_YOUR_MESSAGE'); ?></label>
											<textarea name="message" id="r-gs-field-message" class="<?php echo $this->escape( $this->form->input_class ); ?>" cols="40" rows="10"></textarea>
                                        </div>
                                    </div>

                                    <?php echo $this->customFields; ?>

                                    <?php if ( $this->form->fileupload ) : ?>
                                        <div class="r-gs-block r-gs-size-100">
                                            <div class="r-gs-field">
                                                <label class="r-gs-field-fileupload-label"><?php echo Text::_( $this->filter->clean($this->form->fileupload_label, 'STRING') ) . ' <span>(' . Text::_( $this->filter->clean($this->form->fileupload_help, 'STRING') ) . ')</span>'; ?></label>
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

                                    <div class="r-gs-block r-gs-size-100">
                                        <div class="r-gs-field">
                                            <label id="closeticket-lbl" class="r-gs-label-with-input r-gs-grid r-gs-vcenter" for="closeticket">
                                                <input type="checkbox" name="closeticket" id="closeticket" value="1">
                                                &nbsp;&nbsp;<span><?php echo Text::_('COM_GUESTSUPPORT_CLOSE_THIS_TICKET'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="r-gs-form-submit">
									<?php echo HTMLHelper::_('form.token'); ?>
                                    <input type="hidden" name="ticket_id" value="<?php echo $this->escape( $this->subject->ticket_id ); ?>">
                                    <input type="hidden" name="ticket_auth" value="<?php echo $this->escape( $this->subject->ticket_token ); ?>">
                                    <button type="submit" id="r_gs_submit_reply" class="<?php echo $this->escape( $this->form->submit_class ); ?>"><?php echo Text::_('COM_GUESTSUPPORT_REPLY_SUBMIT'); ?></button>
                                </div>
                            </form>
                        </div>
                    <?php else : ?>
                        <div class="r-gs-ticket-closed-wrapper">
                            <h3><?php echo Text::_('COM_GUESTSUPPORT_TICKET_CLOSED'); ?></h3>
                            <p>
                                <?php
									$reopen_link = '<a href="' . Route::_( $this->ticket_url . '&task=ticket.status&status=reopen', false ) . '">' . Text::_('COM_GUESTSUPPORT_REOPEN_THIS_TICKET') . '</a>';
                                    if ( ( $show_reopen_link_to_users && !$this->is_agent ) || ( $this->is_agent && $show_reopen_link_to_agents ) )
									{
										echo $reopen_link;
									}
                                    else
                                    {
										echo Text::_('COM_GUESTSUPPORT_CREATE_ANOTHER_TICKET');
                                    }
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="r-gs-block r-gs-size-5"></div>
        <div class="r-gs-block r-gs-size-30 r-gs-ticket-info">
            <div class="r-gs-ticket-content">
				<?php if ( $this->sidebar_options['top_text'] ) : ?>
					<div class="r-gs-ticket-top-text"><?php echo GuestsupportHelper::sanitizeHtmlOutput( $this->sidebar_options['top_text'] ); ?></div>
				<?php endif; ?>

				<?php echo $this->displayExtraDataBeforeStatus(); ?>

				<?php if ( $this->sidebar_options['display_ticket_id'] === 'short' && $this->subject->short_ticket_id ) : ?>
					<div class="r-gs-ticket-info-id">
						<h4><?php echo Text::_('COM_GUESTSUPPORT_TICKET_ID'); ?></h4>
						<p><?php echo $this->subject->short_ticket_id; ?></p>
					</div>
				<?php elseif ( $this->sidebar_options['display_ticket_id'] === 'default' ) : ?>
					<div class="r-gs-ticket-info-id">
						<h4><?php echo Text::_('COM_GUESTSUPPORT_TICKET_ID'); ?></h4>
						<p><?php echo $this->subject->ticket_id; ?></p>
					</div>
				<?php endif; ?>

				<?php if ( (int) $this->sidebar_options['display_ticket_status'] === 1 ) : ?>
					<div class="r-gs-ticket-info-status">
						<h4><?php echo Text::_('COM_GUESTSUPPORT_TICKET_STATUS'); ?></h4>
						<p>
							<?php 
								$statusToUpper = strtoupper( $this->subject->status );
								$statusTextTag = 'COM_GUESTSUPPORT_TICKET_' . $statusToUpper;
								$translatedStatus = Text::_($statusTextTag);
								$showStatus = $translatedStatus == $statusTextTag ? ucwords( $this->subject->status ) : $translatedStatus;
								echo $this->filter->clean( $showStatus, 'STRING' );
								if ( $this->subject->status != 'closed' )
								{
									echo ' / <a href="' . Route::_( $this->ticket_url . '&task=ticket.status&status=closed', false ) . '">' . Text::_('COM_GUESTSUPPORT_CLOSE_TICKET') . '</a>';
								}
								else
								{
									$reopen_link = ' / <a href="' . Route::_( $this->ticket_url . '&task=ticket.status&status=reopen', false ) . '">' . Text::_('COM_GUESTSUPPORT_REOPEN') . '</a>';

									if ( ( $show_reopen_link_to_users && !$this->is_agent ) || ( $this->is_agent && $show_reopen_link_to_agents ) )
									{
										echo $reopen_link;
									}
								}
							?>
						</p>
						<?php
							if ( $this->is_agent && $this->config->can_delete_tickets == 'yes' )
							{
								echo '<p class="r-gs-ticket-info-delete"><a id="r-gs-delete" href="' . Route::_( $this->ticket_url . '&task=ticket.delete', false) . '" target="">' . Text::_('COM_GUESTSUPPORT_DELETE_TICKET') . '</a></p>';
							}
						?>
					</div>
				<?php endif; ?>

				<?php if ( (int) $this->sidebar_options['display_email'] === 1 && $this->is_agent ) : ?>
					<div class="r-gs-ticket-info-email">
						<h4><?php echo Text::_('COM_GUESTSUPPORT_TICKET_USER_EMAIL'); ?></h4>
						<p><?php echo $this->escape( $this->subject->email ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( $this->display_form_name ) : ?>
					<div class="r-gs-ticket-info-form">
						<h4><?php echo Text::_('COM_GUESTSUPPORT_TICKET_FORM'); ?></h4>
						<p><?php echo $this->escape( $this->subject->form_name ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( (int) $this->sidebar_options['display_department'] === 1 ) : ?>
					<div class="r-gs-ticket-info-department">
						<h4><?php echo Text::_('COM_GUESTSUPPORT_TICKET_DEPARTMENT'); ?></h4>
						<p><?php echo $this->escape( $this->subject->department_name ); ?></p>
					</div>
				<?php endif; ?>

				<?php echo $this->displayExtraDataAfterDepartment(); ?>

				<?php if ( (int) $this->sidebar_options['display_ticket_creation_date'] === 1 ) : ?>
					<div class="r-gs-ticket-info-created">
						<h4><?php echo Text::_('COM_GUESTSUPPORT_TICKET_CREATED_DATE'); ?></h4>
						<p><?php echo HtmlHelper::date($this->subject->created_date, $this->sidebar_options['ticket_creation_date_format']); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( (int) $this->sidebar_options['display_last_updated_date'] === 1 ) : ?>
					<div class="r-gs-ticket-info-last-updated">
						<h4><?php echo Text::_('COM_GUESTSUPPORT_TICKET_LAST_UPDATED_DATE'); ?></h4>
						<p><?php echo HtmlHelper::date($this->subject->last_update_date, $this->sidebar_options['last_updated_date_format']); ?></p>
					</div>
				<?php endif; ?>

                <?php
					if ( (int) $this->sidebar_options['display_custom_fields'] === 1 )
					{
						$custom_fields = unserialize( $this->subject->custom_fields );
						if ( !empty( $custom_fields ) )
						{
							echo $this->displayCustomFields( $custom_fields );
						}
					}
                ?>

				<?php 
					echo $this->displayExtraDataBeforeOtherTickets();
					echo $this->displayOtherTickets();
					echo $this->displayExtraDataAfterOtherTickets();
				?>

				<?php if ( $this->sidebar_options['bottom_text'] ) : ?>
					<div class="r-gs-ticket-bottom-text"><?php echo GuestsupportHelper::sanitizeHtmlOutput( $this->sidebar_options['bottom_text'] ); ?></div>
				<?php endif; ?>
            </div>
        </div>
    </div>
    <div id="r_gs_edit_reply_modal" class="r-gs-modal">
        <div class="r-gs-modal-wrapper">
            <div class="r-gs-modal-container">
                <div class="r-gs-modal-container-wrapper">
                    <div class="r-gs-modal-content">
                        <div id="r_gs_modal_main_content">
                            <div class="r-gs-editreply-field">
								<textarea name="edit-message" id="r-gs-edit-message" cols="30" rows="10"></textarea>
                            </div>
                            <div class="r-gs-editreply-notice"></div>
                            <div class="r-gs-editreply-notice-success" style="display:none" ><?php echo Text::_('COM_GUESTSUPPORT_MESSAGE_UPDATED_SUCCESSFULLY'); ?></div>
                            <div class="r-gs-editreply-submit">
								<input type="hidden" id="r-gs-baseurl" value="<?php echo Uri::root(); ?>">
                                <button id="r_gs_submit_editreply" class="<?php echo $this->escape( $this->form->submit_class ); ?>"><?php echo Text::_('COM_GUESTSUPPORT_CONFIRM_EDIT'); ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="r-gs-modal-footer">
                        <a href="javascript:;" id="r_gs_modal_close" class="r-gs-button button-cancel"><?php echo Text::_('COM_GUESTSUPPORT_CANCEL'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>