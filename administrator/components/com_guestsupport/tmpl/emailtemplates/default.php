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

$app 		= Factory::getApplication();
$user 		= $app->getIdentity();
$canEdit	= $user->authorise('core.edit', 'com_guestsupport');
?>
<form action="<?php echo Route::_('index.php?option=com_guestsupport&view=emailtemplates'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table" id="emailtemplatesList">
						<thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col">
									<?php echo Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_LIST_NAME'); ?>
                                </th>
								<th scope="col" class="w-10 text-center">
									<?php echo Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_LIST_ACTIVE'); ?>
                                </th>
                            </tr>
                        </thead>
						<tbody>
							<?php foreach ($this->items as $i => $item) :
								$item->edit_link = Route::_('index.php?option=com_guestsupport&view=emailtemplate&layout=edit&id=' . (int) $item->id);
								?>
								<tr class="row<?php echo $i % 2; ?>">
									<td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'tid', 'cb', $item->name); ?>
                                    </td>
									<th scope="row">
										<div class="break-word">
											<?php if ($canEdit) : ?>
												<a href="<?php echo $item->edit_link; ?>" title="<?php echo Text::_('COM_GUESTSUPPORT_EDIT'); ?>">
													<?php echo $this->escape($item->name); ?></a><br>
												<small><?php echo Text::_('COM_GUESTSUPPORT_EMAIL_TEMPLATES_LIST_SUBJECT') . ' ' . $item->subject; ?></small>
											<?php else : ?>
												<?php echo $this->escape($item->name); ?>
											<?php endif; ?>
										</div>
									</th>
									<td class="text-center">
										<?php echo (int) $this->escape($item->active) === 1 ? Text::_('COM_GUESTSUPPORT_YES') : Text::_('COM_GUESTSUPPORT_NO'); ?>
                                    </td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

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

<!-- Setup guide -->
<?php if ( $this->guide === true ) : ?>
    <div id="modal_setup_guide_email_templates" class="r-gs-modal r-gs-modal-active r-gs-setup-guide">
        <div class="r-gs-modal-wrapper">
            <div class="r-gs-modal-container">
                <div class="r-gs-modal-container-wrapper">
                    <div class="r-gs-modal-content">
                        <h4><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_TITLE'); ?></h4>
                        <div class="r-gs-divider"></div>
                        <h3><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_EMAIL_TEMPLATES_TITLE'); ?></h3>
                        <p><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_EMAIL_TEMPLATES_DESC'); ?></p>
                        <p><a href="javascript:;" id="r_gs_modal_close" class="button button-cancel r-gs-link-u"><?php echo Text::_('COM_GUESTSUPPORT_GUIDE_LETS_DO_IT'); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- END Setup guide -->