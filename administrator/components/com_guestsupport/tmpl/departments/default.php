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
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\User\User;

$app 			= Factory::getApplication();
$user 			= $app->getIdentity();
$userId			= $user->get('id');
$canEdit		= $user->authorise('core.edit', 'com_guestsupport');
$canEditUser	= $user->authorise('core.edit', 'com_users');

$edit_link = Route::_('index.php?option=com_guestsupport&view=department&layout=edit&id=' . (int) $this->department->id);
?>
<form action="<?php echo Route::_('index.php?option=com_guestsupport&view=departments'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php if (empty($this->department)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table" id="departmentsList">
						<thead>
							<tr>
								<td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
								<th scope="col">
									<?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_LIST_NAME'); ?>
								</th>
								<th scope="col">
									<?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_LIST_AGENT'); ?>
								</th>
								<th scope="col" class="r-gs-dept-cell-emails">
									<?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_LIST_ADDITIONAL_EMAILS'); ?>
								</th>
								<th scope="col" class="r-gs-dept-cell-emails-type">
									<?php echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_LIST_ADDITIONAL_EMAILS_TYPE'); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr class="row0">
								<td class="text-center">
									<?php echo HTMLHelper::_('grid.id', 0, $this->department->id, false, 'depts_ids', 'cb', $this->department->name); ?>
								</td>
								<th scope="row">
									<div class="break-word">
										<?php if ($canEdit) : ?>
											<a href="<?php echo $edit_link; ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($this->department->name); ?>">
												<?php echo $this->escape($this->department->name); ?></a>
										<?php else : ?>
											<?php echo $this->escape($this->department->name); ?>
										<?php endif; ?>
									</div>
								</th>
								<td class="small">
									<?php
										$agentinfo = new User($this->department->agent_id);
										if ( !empty( $agentinfo ) )
										{
											if ( $canEditUser )
											{
												echo '<a href="' . Route::_('index.php?option=com_users&task=user.edit&id=' . (int) $this->department->agent_id) . '">';
											}
											echo $this->escape($agentinfo->name) . '<br>';
											echo PunycodeHelper::emailToUTF8($this->escape($agentinfo->email));
											if ( $canEditUser )
											{
												echo '</a>';
											}
										}
									?>
								</td>
								<td class="small r-gs-dept-cell-emails">
									<?php echo nl2br($this->department->additional_emails); ?>
								</td>
								<td class="small r-gs-dept-cell-emails-type">
									<?php 
										if ( $this->department->additional_emails_type == 'both' )
										{
											echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_TYPE_BOTH');
										}
										elseif ( $this->department->additional_emails_type == 'tickets' )
										{
											echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_TYPE_TICKETS');
										}
										elseif ( $this->department->additional_emails_type == 'replies' )
										{
											echo Text::_('COM_GUESTSUPPORT_DEPARTMENTS_ADDITIONAL_EMAILS_TYPE_REPLIES');
										}
									?>
								</td>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>

				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>

<div id="wt_pro_info_modal" class="modal fade">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 id="shortcutOverviewModalLabel" class="modal-title">
					<?php echo Text::_('COM_GUESTSUPPORT_PRO_VERSION_REQUIRED_TITLE'); ?>
				</h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-3">
				<?php echo Text::sprintf('COM_GUESTSUPPORT_PRO_VERSION_REQUIRED_DEPARTMENTS_CONTENT', '<a href="https://www.rcatheme.com/item/guest-support-pro-for-joomla" target="_blank">', '</a>'); ?>
			</div>
		</div>
	</div>
</div>

<!-- Setup guide -->
<?php if ( $this->guide === true ) : ?>
    <div id="modal_setup_guide_department" class="r-gs-modal r-gs-modal-active r-gs-setup-guide">
        <div class="r-gs-modal-wrapper">
            <div class="r-gs-modal-container">
                <div class="r-gs-modal-container-wrapper">
                    <div class="r-gs-modal-content">
                        <h4><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_TITLE'); ?></h4>
                        <div class="r-gs-divider"></div>
                        <h3><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_DEPARTMENT_TITLE'); ?></h3>
                        <p><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_DEPARTMENT_DESC'); ?></p>
                        <p><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_DEPARTMENT_EDIT_HINT'); ?></p>
                        <p><em><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_DEPARTMENT_SAVE_HINT'); ?></em></p>
                        <p><a href="javascript:;" id="r_gs_modal_close" class="button button-cancel r-gs-link-u"><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_LETS_DO_IT'); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- END Setup guide -->