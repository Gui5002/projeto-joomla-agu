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
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');
$departments = GuestsupportHelper::departments();
?>
<div class="r-gs-filter">
	<ul>
		<?php
			echo '<li><a href="' . Route::_('index.php?option=com_guestsupport&view=tickets') . '" class="' . ( !$this->filters->status ? 'current' : '' ) . '">' . Text::_('COM_GUESTSUPPORT_ALL') . ' <span>(' . $this->escape($this->countStatus->all_tickets) . ')</span></a>&nbsp;|&nbsp;</li>';
			echo '<li><a href="' . Route::_('index.php?option=com_guestsupport&view=tickets&status=open') . '" class="' . ( $this->filters->status == 'open' ? 'current' : '' ) . '">' . Text::_('COM_GUESTSUPPORT_OPEN') . ' <span>(' . $this->escape($this->countStatus->open_tickets) . ')</span></a>&nbsp;|&nbsp;</li>';
			echo '<li><a href="' . Route::_('index.php?option=com_guestsupport&view=tickets&status=pending') . '" class="' . ( $this->filters->status == 'pending' ? 'current' : '' ) . '">' . Text::_('COM_GUESTSUPPORT_PENDING') . ' <span>(' . $this->escape($this->countStatus->pending_tickets) . ')</span></a>&nbsp;|&nbsp;</li>';
			echo '<li><a href="' . Route::_('index.php?option=com_guestsupport&view=tickets&status=closed') . '" class="' . ( $this->filters->status == 'closed' ? 'current' : '' ) . '">' . Text::_('COM_GUESTSUPPORT_CLOSED') . ' <span>(' . $this->escape($this->countStatus->closed_tickets) . ')</span></a></li>';
		?>
	</ul>
	<form action="<?php echo Route::_('index.php?option=com_guestsupport&view=tickets' . ($this->filters->status ? '&status=' . $this->filters->status : '')); ?>" method="post">
		<div class="r-gs-grid">
			<div class="r-gs-filter-option">
				<select name="sortby" id="r-gs-ticket-sortby" class="form-select">
					<option value=""><?php echo Text::_('COM_GUESTSUPPORT_FILTER_SORT_BY'); ?></option>
					<option value="created_asc"<?php echo $this->filters->sortby == 'created_asc' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_FILTER_CREATED_AZ'); ?></option>
					<option value="created_desc"<?php echo $this->filters->sortby == 'created_desc' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_FILTER_CREATED_ZA'); ?></option>
					<option value="updated_asc"<?php echo $this->filters->sortby == 'updated_asc' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_FILTER_REPLY_AZ'); ?></option>
					<option value="updated_desc"<?php echo $this->filters->sortby == 'updated_desc' ? ' selected' : ''; ?>><?php echo Text::_('COM_GUESTSUPPORT_FILTER_REPLY_ZA'); ?></option>
				</select>
			</div>
			<?php if ( !empty( $this->forms ) ) : ?>
				<div class="r-gs-filter-option">
					<select name="form" id="r-gs-ticket-form" class="form-select">
						<option value=""><?php echo Text::_('COM_GUESTSUPPORT_FILTER_BY_FORM'); ?></option>
						<?php
							foreach ($this->forms as $item) {
		            			echo '<option value="' . $item->id . '"' . ( $this->filters->form == $item->id ? ' selected' : '' ) .'>' . $item->form_name . '</option>';
							}
							echo '<option value="-1"' . ( $this->filters->form == -1 ? ' selected' : '' ) .'>' . Text::_('COM_GUESTSUPPORT_FILTER_BY_FORM_TICKETS_BY_AGENT') . '</option>';
						?>
					</select>
				</div>
			<?php endif; ?>
			<?php if ( $departments !== false && count($departments) > 1 ) : ?>
				<div class="r-gs-filter-option">
					<select name="department" id="r-gs-ticket-department" class="form-select">
						<option value=""><?php echo Text::_('COM_GUESTSUPPORT_FILTER_BY_DEPARTMENT'); ?></option>
						<?php
							foreach ($departments as $item) {
		            			echo '<option value="' . $item->id . '"' . ( $this->filters->department == $item->id ? ' selected' : '' ) .'>' . $item->name . '</option>';
							}
						?>
					</select>
				</div>
			<?php endif; ?>
			<div class="r-gs-filter-option">
				<input type="text" name="s" id="filter_search" value="<?php echo $this->filters->searchstring; ?>" class="form-control" placeholder="<?php echo Text::_('COM_GUESTSUPPORT_FILTER_SEARCH_PLACEHOLDER'); ?>" inputmode="search">
			</div>
			<div class="r-gs-filter-submit">
				<button type="submit" class="filter-search-bar__button btn btn-primary">
					<span class="filter-search-bar__button-icon icon-search" aria-hidden="true"></span>
					<?php echo Text::_('COM_GUESTSUPPORT_SEARCH'); ?>
				</button>
				<a href="<?php echo Route::_('index.php?option=com_guestsupport&task=tickets.clearsearch'); ?>" class="btn btn-secondary"><?php echo Text::_('COM_GUESTSUPPORT_CLEAR'); ?></a>
			</div>
		</div>
		<input type="hidden" name="task" value="">
	</form>
</div>

<?php if ( isset( GuestsupportHelper::config()->ask_for_review ) && GuestsupportHelper::config()->ask_for_review === 'yes' ) : ?>
	<div id="system-message-container">
		<joomla-alert type="success" close-text="Close" dismiss="true" role="alert" style="animation-name: joomla-alert-fade-in;">
			<div class="alert-heading"><span class="info"></span><span class="visually-hidden">success</span></div>
			<div class="alert-wrapper"><div class="alert-message">
				<?php echo Text::sprintf('COM_GUESTSUPPORT_ASK_FOR_REVIEW_NOTICE', '<a href="https://extensions.joomla.org/extension/guest-support-complete-customer-support-ticket-system-for-joomla/" target="_blank">', '</a>'); ?>
				<br>
				<a href="<?php echo Route::_('index.php?option=com_guestsupport&task=tickets.hidereviewnotice'); ?>" class="button">Don't show again</a>
			</div></div>
		</joomla-alert>
	</div>
<?php endif; ?>

<form action="<?php echo Route::_('index.php?option=com_guestsupport&view=tickets' . ($this->filters->status ? '&status=' . $this->filters->status : '')); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
					<div class="r-gs-tickets-table-wrapper">
						<table class="table r-gs-tickets-table" id="TicketsList">
							<thead>
								<tr>
									<td class="w-1 r-gs-tickets-cell-sl text-center">
										<?php echo HTMLHelper::_('grid.checkall'); ?>
									</td>
									<th scope="col" class="r-gs-tickets-cell-subject">
										<?php echo Text::_('COM_GUESTSUPPORT_TICKETS_SUBJECT'); ?>
									</th>
									<th scope="col" class="w-7 r-gs-tickets-cell-status">
										<?php echo Text::_('COM_GUESTSUPPORT_TICKETS_STATUS'); ?>
									</th>
									<th scope="col" class="w-10 r-gs-tickets-cell-updated">
										<?php echo Text::_('COM_GUESTSUPPORT_TICKETS_LAST_REPLIED_DATE'); ?>
									</th>
									<th scope="col" class="w-12 r-gs-tickets-cell-createdby">
										<?php echo Text::_('COM_GUESTSUPPORT_TICKETS_CREATED_BY'); ?>
									</th>
									<th scope="col" class="w-10 r-gs-tickets-cell-form">
										<?php echo Text::_('COM_GUESTSUPPORT_TICKETS_FORM'); ?>
									</th>
									<th scope="col" class="w-10 r-gs-tickets-cell-department">
										<?php echo Text::_('COM_GUESTSUPPORT_TICKETS_DEPARTMENT'); ?>
									</th>
									<th scope="col" class="w-10 r-gs-tickets-cell-createddate">
										<?php echo Text::_('COM_GUESTSUPPORT_TICKETS_CREATED_DATE'); ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($this->items as $i => $item) :
									$ticket_url = Route::link( 'site', 'index.php?option=com_guestsupport&view=ticket&id=' . $this->escape($item->ticket_id) . '&auth=' . $this->escape($item->ticket_token) . '&agent=' . GuestsupportHelper::AgentByDepartmentId($item->department_id), false );
									?>
									<tr class="row<?php echo $i % 2; ?>">
										<td class="r-gs-tickets-cell-sl text-center">
											<?php echo HTMLHelper::_('grid.id', $i, $item->ticket_id, false, 'ticket_ids', 'cb', $item->name); ?>
										</td>
										<th scope="row" class="r-gs-tickets-cell-subject">
											<div class="break-word">
												<a href="<?php echo $ticket_url; ?>" target="_blank" class="r-gs-ticket-link">
													<?php echo $this->escape($item->subject) . ' (' . $this->escape($item->total_replies) . ' ' . ( (int) $item->total_replies > 1 ? Text::_('COM_GUESTSUPPORT_TICKETS_REPLIES') : Text::_('COM_GUESTSUPPORT_TICKETS_REPLY') ) . ')'; ?>
												</a>
											</div>
											<div class="small r-gs-ticket-hidden-info">
												<span class="r-gs-ticket-hidden-info-status"><?php echo ucfirst( strtolower( Text::_('COM_GUESTSUPPORT_TICKETS_STATUS') ) ) . ': ' . $this->escape($item->status); ?></span>
												<span class="r-gs-ticket-hidden-info-updated"><?php echo ucfirst( strtolower( Text::_('COM_GUESTSUPPORT_TICKETS_LAST_REPLIED_DATE') ) ) . ': ' . HTMLHelper::date($this->escape($item->last_update_date), "Y-m-d H:i:s"); ?></span>
												<span class="r-gs-ticket-hidden-info-createdby"><?php echo ucfirst( strtolower( Text::_('COM_GUESTSUPPORT_TICKETS_CREATED_BY') ) ) . ': ' . $this->escape($item->name); ?></span>
												<span class="r-gs-ticket-hidden-info-form"><?php echo ucfirst( strtolower( Text::_('COM_GUESTSUPPORT_TICKETS_FORM') ) ) . ': ' . $this->escape($item->form_name); ?></span>
												<span class="r-gs-ticket-hidden-info-department"><?php echo ucfirst( strtolower( Text::_('COM_GUESTSUPPORT_TICKETS_DEPARTMENT') ) ) . ': ' . $this->escape($item->department_name); ?></span>
												<span class="r-gs-ticket-hidden-info-createddate"><?php echo ucfirst( strtolower( Text::_('COM_GUESTSUPPORT_TICKETS_CREATED_DATE') ) ) . ': ' . HTMLHelper::date($this->escape($item->created_date), "Y-m-d H:i:s"); ?></span>
											</div>
										</th>
										<td class="small r-gs-tickets-cell-status">
											<?php
												$statusToUpper = strtoupper( $this->escape($item->status) );
												$statusTextTag = 'COM_GUESTSUPPORT_TICKET_' . $statusToUpper;
												$translatedStatus = Text::_($statusTextTag);
												$showStatus = $translatedStatus == $statusTextTag ? ucwords( $this->escape($item->status) ) : $translatedStatus;
												echo $showStatus;
											?>
										</td>
										<td class="small r-gs-tickets-cell-updated">
											<?php echo HTMLHelper::date($this->escape($item->last_update_date), "Y-m-d H:i:s"); ?>
										</td>
										<td class="small r-gs-tickets-cell-createdby">
											<?php echo $this->escape($item->name) . '<br>' . $this->escape($item->email); ?>
										</td>
										<td class="small r-gs-tickets-cell-form">
											<?php echo $this->escape($item->form_name); ?>
										</td>
										<td class="small r-gs-tickets-cell-department">
											<?php echo $this->escape($item->department_name); ?>
										</td>
										<td class="small r-gs-tickets-cell-createddate">
											<?php echo HTMLHelper::date($this->escape($item->created_date), "Y-m-d H:i:s"); ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

                    <?php // Load the pagination. ?>
                    <?php echo $this->pagination->getListFooter(); ?>
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
				<?php echo Text::sprintf('COM_GUESTSUPPORT_PRO_VERSION_REQUIRED_TICKETS_CONTENT', '<a href="https://www.rcatheme.com/item/guest-support-pro-for-joomla" target="_blank">', '</a>'); ?>
			</div>
		</div>
	</div>
</div>