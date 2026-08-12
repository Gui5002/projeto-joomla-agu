<?php

/**
 * @package		Joomla.Administrator
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

$isnew = ($this->item->id == 0);
$departments = GuestsupportHelper::departments( $this->item->id );
$config = GuestsupportHelper::config();
$ispro = GuestsupportHelper::isPro();

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

?>

<div class="r-guest-support">
	<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" id="svg-icon-global">
		<symbol id="r_gs_icon_sort" viewBox="0 0 512 512">
			<path d="M512 255.1c0 6.755-2.844 13.09-7.844 17.62l-88 80.05C411.5 357.8 405.8 359.9 400 359.9c-13.27 0-24-10.76-24-24c0-6.534 2.647-13.04 7.844-17.78l42.07-38.28H280v146l38.25-42.1c4.715-5.2 11.21-7.849 17.74-7.849c13.23 0 24.01 10.71 24.01 24.03c0 5.765-2.061 11.55-6.25 16.15l-80 88.06C269.2 509.2 262.8 512 256 512s-13.22-2.846-17.75-7.849l-80-88.06c-4.189-4.603-6.25-10.39-6.25-16.15c0-13.38 10.83-24.03 23.1-24.03c6.526 0 13.02 2.649 17.75 7.849L232 425.9V279.8H86.09l42.07 38.28c5.196 4.735 7.844 11.24 7.844 17.78c0 13.22-10.71 24-24 24c-5.781 0-11.53-2.064-16.16-6.254l-88-80.05C2.844 269.1 0 262.7 0 255.1c0-6.755 2.844-13.37 7.844-17.9l88-80.05C100.5 153.8 106.2 151.8 112 151.8c13.26 0 23.99 10.74 23.99 23.99c0 6.534-2.647 13.04-7.844 17.78L86.09 231.8H232V85.8L193.8 127.9C189 133.1 182.5 135.7 175.1 135.7c-13.16 0-23.1-10.66-23.1-24.03c0-5.765 2.061-11.55 6.25-16.15l80-88.06C242.8 2.502 249.4 0 256 0s13.22 2.502 17.75 7.505l80 88.06c4.189 4.603 6.25 10.39 6.25 16.15c0 13.35-10.81 24.03-24 24.03c-6.531 0-13.03-2.658-17.75-7.849L280 85.8v146h145.9l-42.07-38.28c-5.196-4.735-7.844-11.24-7.844-17.78c0-13.25 10.74-23.99 23.98-23.99c5.759 0 11.55 2.061 16.18 6.242l88 80.05C509.2 242.6 512 249.2 512 255.1z" fill="#3c434a"/>
		</symbol>
		<symbol id="r_gs_icon_edit" viewBox="0 0 512 512">
			<path d="M386.7 22.63C411.7-2.365 452.3-2.365 477.3 22.63L489.4 34.74C514.4 59.74 514.4 100.3 489.4 125.3L269 345.6C260.6 354.1 249.9 359.1 238.2 362.7L147.6 383.6C142.2 384.8 136.6 383.2 132.7 379.3C128.8 375.4 127.2 369.8 128.4 364.4L149.3 273.8C152 262.1 157.9 251.4 166.4 242.1L386.7 22.63zM454.6 45.26C442.1 32.76 421.9 32.76 409.4 45.26L382.6 72L440 129.4L466.7 102.6C479.2 90.13 479.2 69.87 466.7 57.37L454.6 45.26zM180.5 281L165.3 346.7L230.1 331.5C236.8 330.2 242.2 327.2 246.4 322.1L417.4 152L360 94.63L189 265.6C184.8 269.8 181.8 275.2 180.5 281V281zM208 64C216.8 64 224 71.16 224 80C224 88.84 216.8 96 208 96H80C53.49 96 32 117.5 32 144V432C32 458.5 53.49 480 80 480H368C394.5 480 416 458.5 416 432V304C416 295.2 423.2 288 432 288C440.8 288 448 295.2 448 304V432C448 476.2 412.2 512 368 512H80C35.82 512 0 476.2 0 432V144C0 99.82 35.82 64 80 64H208z" fill="#3c434a"/>
		</symbol>
		<symbol id="r_gs_icon_delete" viewBox="0 0 448 512">
			<path d="M424 80C437.3 80 448 90.75 448 104C448 117.3 437.3 128 424 128H412.4L388.4 452.7C385.9 486.1 358.1 512 324.6 512H123.4C89.92 512 62.09 486.1 59.61 452.7L35.56 128H24C10.75 128 0 117.3 0 104C0 90.75 10.75 80 24 80H93.82L130.5 24.94C140.9 9.357 158.4 0 177.1 0H270.9C289.6 0 307.1 9.358 317.5 24.94L354.2 80H424zM177.1 48C174.5 48 171.1 49.34 170.5 51.56L151.5 80H296.5L277.5 51.56C276 49.34 273.5 48 270.9 48H177.1zM364.3 128H83.69L107.5 449.2C108.1 457.5 115.1 464 123.4 464H324.6C332.9 464 339.9 457.5 340.5 449.2L364.3 128z" fill="#3c434a"/>
		</symbol>
	</svg>

	<div class="r-gs-grid">
		<div class="r-gs-block r-gs-size-100">
			<form action="<?php echo Route::_('index.php?option=com_guestsupport&view=form&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="r_gs_forms_form" class="form-validate">
				<h1 class="r-gs-form-title"><?php echo $isnew ? Text::_('COM_GUESTSUPPORT_FORMS_ADD_NEW_FORM') : Text::_('COM_GUESTSUPPORT_FORMS_EDIT_FORM') . ' <span>' . $this->escape($this->item->form_name); ?></span></h1>
				<div class="r-gs-form-name-wrap">
					<label for="form_name"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_NAME_LABEL'); ?>*</label>
					<input name="form_name" type="text" id="form_name" class="form-control required" value="<?php echo $this->escape($this->item->form_name); ?>" size="40" aria-required="true" required>
				</div>

				<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>
				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'form_fields', Text::_('COM_GUESTSUPPORT_FORM_TAB_FORM_FIELDS')); ?>
					<div class="r-gs-form-tab-content r-gs-forms-form">
						<div class="r-gs-form-tab-content-block">
							<div class="r-gs-edit-form-header">
								<a href="javascript:;" class="btn btn-primary" id="r_gs_open_modal" data-modal="r_gs_modal_add_fields" ><?php echo Text::_('COM_GUESTSUPPORT_FORMS_ADD_NEW_FIELD'); ?></a>
							</div>
							<div class="r-gs-form-wrap">
								<ul id="r_gs_form_fields" class="r-gs-form-fields-container r-gs-grid">
									<?php if ( !$isnew && !empty( $this->item->form_fields ) ) : ?>
										<?php foreach ( $this->item->form_fields as $field ) : 
											if ( !in_array( $field['name'], ['name', 'email', 'subject', 'department', 'message'] ) )
											{
												continue;
											}
											?>
											<li id="r_gs_form_field_group_<?php echo $this->escape( $field['name'] ); ?>" class="r-gs-form-fields-item r-gs-block r-gs-size-<?php echo $field['inputtype'] == 'hidden' ? '100' : $field['colsize']; ?>">
												<div class="r-gs-form-fields-item-wrapper">
													<div class="r-gs-grid r-gs-vcenter">
														<div class="r-gs-block r-gs-block-fixed r-gs-form-field-reorder">
															<span id="r_gs_form_field_reorder" title="<?php echo Text::_('COM_GUESTSUPPORT_FORMS_REORDER'); ?>"><svg class="r_gs_icon_sort" width="28px" height="28px"><use href="#r_gs_icon_sort"></use></svg></span>
														</div>
														<div class="r-gs-block r-gs-form-field-items">
															<label for=""><?php echo $this->escape( $field['label'] ); ?></label>
															<?php if ( $field['inputtype'] == 'departments' && !empty( $departments ) ) :
																echo '<select id="r_gs_field_list_departments" name="field_' . $this->escape( $field['name'] ) . '[value]">';
																foreach ( $departments as $item ) {
																	$selected = $field['showonform'] == '0' && $field['default_department'] == $item->id ? ' selected' : '';
																	echo '<option value="' . $this->escape( $item->id ) . '"' . $selected . '>' . $this->escape( $item->name ) . '</option>';
																}
																echo '</select>';
															?>
															<?php elseif ( $field['inputtype'] == 'textarea' ) : ?>
																<textarea id="r_gs_field_list_visible" rows="5" cols="40" placeholder="<?php echo $this->escape( $field['placeholder'] ); ?>"></textarea>
															<?php elseif ( $field['inputtype'] == 'email' ) : ?>
																<input id="r_gs_field_list_visible" type="email" placeholder="<?php echo $this->escape( $field['placeholder'] ); ?>">
															<?php elseif ( $field['inputtype'] == 'text' ) : ?>
																<input id="r_gs_field_list_visible" type="text" placeholder="<?php echo $this->escape( $field['placeholder'] ); ?>">
															<?php endif; ?>
																<input id="r_gs_field_list_name" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[name]" value="<?php echo $this->escape( $field['name'] ); ?>">
																<input id="r_gs_field_list_label" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[label]" value="<?php echo $this->escape( $field['label'] ); ?>">
																<input id="r_gs_field_list_inputtype" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[inputtype]" value="<?php echo $this->escape( $field['inputtype'] ); ?>">
																<input id="r_gs_field_list_fieldtype" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[fieldtype]" value="<?php echo $this->escape( $field['fieldtype'] ); ?>">
																<input id="r_gs_field_list_colsize" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[colsize]" value="<?php echo $this->escape( $field['colsize'] ); ?>">
															<?php if ( $field['inputtype'] == 'departments' && !empty( $departments ) ) : ?>
																<input id="r_gs_field_list_showonform" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[showonform]" value="<?php echo $this->escape( $field['showonform'] ); ?>">
																<input id="r_gs_field_list_default_department" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[default_department]" value="<?php echo $this->escape( $field['default_department'] ); ?>">
																<p class="r-gs-field-item-labels r-gs-grid r-gs-vcenter">
																	<span><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_NAME'); ?>: <span id="r_gs_form_field_label_name" class="r-gs-form-field-label"><?php echo $this->escape( $field['name'] ); ?></span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																	<span><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_TYPE'); ?>: <span id="r_gs_form_field_label_core" class="r-gs-form-field-label"><?php echo $this->escape( $field['fieldtype'] ); ?></span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																	<span><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_INPUT_TYPE'); ?>: <span id="r_gs_form_field_label_inputtype" class="r-gs-form-field-label">departments</span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																	<span style="display: none;"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_SHOW_ON_FORMS'); ?>: <span id="r_gs_form_field_label_showonform" class="r-gs-form-field-label"><?php echo $field['showonform'] == '1' ? Text::_('COM_GUESTSUPPORT_YES') : Text::_('COM_GUESTSUPPORT_NO'); ?></span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																	<span><?php echo Text::_('COM_GUESTSUPPORT_FORMS_COLUMN_SIZE'); ?>: <span id="r_gs_form_field_label_colsize" class="r-gs-form-field-label"><?php echo $this->escape( $field['colsize'] ); ?>%</span></span>
																	<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																	<span class="r-gs-color-red"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FIELD'); ?></span>
																</p>
															<?php else : ?>
																<input id="r_gs_field_list_placeholder" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[placeholder]" value="<?php echo $this->escape( $field['placeholder'] ); ?>">
																<textarea id="r_gs_field_list_options" name="field_<?php echo $this->escape( $field['name'] ); ?>[options]" class="r_gs_form_field_hidden"><?php echo isset( $field['options'] ) ? $field['options'] : ''; ?></textarea>
																<input id="r_gs_field_list_hidden_value" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[hidden_value]" value="<?php echo isset( $field['hidden_value'] ) ? $this->escape( $field['hidden_value'] ) : ''; ?>">
																<textarea id="r_gs_field_list_customtext" name="field_<?php echo $this->escape( $field['name'] ); ?>[customtext]" class="r_gs_form_field_hidden"><?php echo isset( $field['customtext'] ) ? $field['customtext'] : ''; ?></textarea>
																<input id="r_gs_field_list_required" type="hidden" name="field_<?php echo $this->escape( $field['name'] ); ?>[required]" value="<?php echo $this->escape( $field['required'] ); ?>">
																<textarea id="r_gs_field_list_description" name="field_<?php echo $this->escape( $field['name'] ); ?>[description]" class="r_gs_form_field_hidden"><?php echo isset( $field['description'] ) ? $field['description'] : ''; ?></textarea>
																<textarea id="r_gs_field_list_error_message" name="field_<?php echo $this->escape( $field['name'] ); ?>[error_message]" class="r_gs_form_field_hidden"><?php echo $field['error_message']; ?></textarea>

																<p class="r-gs-field-item-labels r-gs-grid r-gs-vcenter">
																	<span><a href="javascript:;" class="r-gs-grid r-gs-vcenter" id="r_gs_edit_field" field-type="<?php echo $this->escape( $field['inputtype'] ); ?>" field-edit="<?php echo $this->escape( $field['fieldtype'] ); ?>"><svg class="r_gs_icon_edit" width="13px" height="13px"><use href="#r_gs_icon_edit"></use></svg><span>&nbsp;<?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_EDIT'); ?></span></a></span>
																	<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																	<span><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_NAME'); ?>: <span id="r_gs_form_field_label_name" class="r-gs-form-field-label"><?php echo $this->escape( $field['name'] ); ?></span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																	<span><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_TYPE'); ?>: <span id="r_gs_form_field_label_core" class="r-gs-form-field-label"><?php echo $this->escape( $field['fieldtype'] ); ?></span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																	<span><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_INPUT_TYPE'); ?>: <span id="r_gs_form_field_label_inputtype" class="r-gs-form-field-label"><?php echo $this->escape( $field['inputtype'] ); ?></span></span>
																	<?php if ( isset( $field['required'] ) ) : ?>
																		<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																		<span><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_INPUT_REQUIRED'); ?>: <span id="r_gs_form_field_label_required" class="r-gs-form-field-label"><?php echo $field['required'] == '1' ? Text::_('COM_GUESTSUPPORT_YES') : Text::_('COM_GUESTSUPPORT_NO'); ?></span></span>
																	<?php endif; ?>
																	<span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
																	<span><?php echo Text::_('COM_GUESTSUPPORT_FORMS_COLUMN_SIZE'); ?>: <span id="r_gs_form_field_label_colsize" class="r-gs-form-field-label"><?php echo $this->escape( $field['colsize'] ); ?>%</span></span>
																</p>
															<?php endif; ?>
														</div>
													</div>
												</div>
											</li>
										<?php endforeach; ?>
									<?php endif; ?>
									<li class="r_gs_form_field_group_hidden">
										<span id="r_gs_texthelper_reorder"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_REORDER'); ?></span>
										<span id="r_gs_texthelper_edit"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_EDIT'); ?></span>
										<span id="r_gs_texthelper_delete"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_DELETE'); ?></span>
										<span id="r_gs_texthelper_yes"><?php echo Text::_('COM_GUESTSUPPORT_YES'); ?></span>
										<span id="r_gs_texthelper_no"><?php echo Text::_('COM_GUESTSUPPORT_NO'); ?></span>
										<span id="r_gs_texthelper_fieldname"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_NAME'); ?></span>
										<span id="r_gs_texthelper_fieldtype"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_TYPE'); ?></span>
										<span id="r_gs_texthelper_inputtype"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_INPUT_TYPE'); ?></span>
										<span id="r_gs_texthelper_required"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_INPUT_REQUIRED'); ?></span>
										<span id="r_gs_texthelper_colsize"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_COLUMN_SIZE'); ?></span>
									</li>
								</ul>
								<div class="r-gs-edit-form-header">
									<a href="javascript:;" class="btn btn-primary" id="r_gs_open_modal" data-modal="r_gs_modal_add_fields" ><?php echo Text::_('COM_GUESTSUPPORT_FORMS_ADD_NEW_FIELD'); ?></a>
								</div>
							</div>
						</div>
					</div>
					<div class="r-gs-spacer"></div>
					<h3><?php echo Text::_('COM_GUESTSUPPORT_FORMS_SUBMIT_BUTTON_OPTIONS'); ?></h3>
					<?php echo $this->xmlfields->renderFieldset('submit_button'); ?>

					<div class="r-gs-spacer"></div>
					<h3><?php echo Text::_('COM_GUESTSUPPORT_FORMS_INPUT_FIELDS_CSS_CLASS'); ?></h3>
					<?php echo $this->xmlfields->renderField('input_class'); ?>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>

				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'file_uploads', Text::_('COM_GUESTSUPPORT_FORM_TAB_FILE_UPLOADS')); ?>
					<div class="r-gs-form-tab-content">
						<?php echo $this->xmlfields->renderFieldset('file_upload'); ?>
					</div>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>

				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'features', Text::_('COM_GUESTSUPPORT_FORM_TAB_SECURITY_AND_FEATURES')); ?>
					<div class="r-gs-form-tab-content">
						<ul id="r_gs_form_additional" class="r-gs-form-fields">
							<li>
								<?php echo $this->xmlfields->renderField('recaptcha'); ?>
								<?php
									if ( !$config->recaptcha_site_key || !$config->recaptcha_secret_key )
									{
										echo '<p class="r-gs-color-red">' . Text::sprintf('COM_GUESTSUPPORT_FORMS_ENABLE_RECAPTCHA_HELP', '<a href="' . Route::_('index.php?option=com_guestsupport&view=settings') . '" target="_blank">', '</a>') . '</p>';
									}
								?>
							</li>
							<li>
								<?php echo $this->xmlfields->renderField('verify_email'); ?>
								<span class="r-gs-color-red"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FEATURES'); ?></span>
							</li>
							<li>
								<?php 
									echo $this->xmlfields->renderField('suggest_docs');
									echo $this->xmlfields->renderField('suggest_docs_cats');
								?>
								<span class="r-gs-color-red"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_VERSION_REQUIRED_FEATURES'); ?></span>
							</li>
						</ul>
					</div>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>

				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'ticket_view', Text::_('COM_GUESTSUPPORT_FORM_TAB_TICKET_VIEW_SIDEBAR')); ?>
					<div class="r-gs-form-tab-content">
						<?php echo $this->xmlfields->renderFieldset('ticket_view'); ?>
					</div>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>

				<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

				<input type="hidden" name="task" value="">
				<input type="hidden" name="form_id" value="<?php echo $this->item->id; ?>">
				<?php echo HTMLHelper::_('form.token'); ?>
			</form>
		</div>
	</div>
	<div class="r-gs-fields-contents">
		<?php
			echo GuestsupportHelper::formField( 'text' );
			echo GuestsupportHelper::formField( 'email' );
			echo GuestsupportHelper::formField( 'textarea' );
		?>
	</div>
</div>

<!-- Add field modal content -->
<div id="r_gs_modal_add_fields" class="r-gs-modal r-gs-add-edit-fields-modal">
    <div class="r-gs-modal-wrapper">
        <div class="r-gs-modal-container">
            <div class="r-gs-modal-container-wrapper">
                <div class="r-gs-modal-header">
                    <h3><?php echo Text::_('COM_GUESTSUPPORT_FORMS_ADD_NEW_FIELD_MODAL_TITLE'); ?></h3>
                </div>
                <div class="r-gs-modal-content r-gs-forms-form">
					<div id="r_gs_add_fields_selector" class="r-gs-add-fields-block-selector">
						<label for="field_type"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_FIELD_TYPE'); ?></label>
						<select id="r_gs_add_field_type" class="form-control-success">
							<option value=""><?php echo Text::_('COM_GUESTSUPPORT_FORMS_SELECT_FIELD_TYPE'); ?></option>
							<option value="text"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_TEXTBOX'); ?></option>
							<option value="email"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_EMAIL'); ?></option>
							<option value="number"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_NUMBER'); ?></option>
							<option value="textarea"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_TEXTAREA'); ?></option>
							<option value="select"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_SELECT'); ?></option>
							<option value="radio"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_RADIO'); ?></option>
							<option value="checkbox"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_CHECKBOX'); ?></option>
							<option value="hidden"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_HIDDEN'); ?></option>
							<option value="date"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_DATE'); ?></option>
							<option value="datetime"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_PRO_INFO_DATETIME'); ?></option>
							<option value="content"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_ADDFIELD_INFO_CONTENT'); ?></option>
							<option value="pluginfield"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_ADDFIELD_INFO_PLUGIN_FIELD'); ?></option>
						</select>
					</div>
					<div id="r_gs_modal_main_content" class="r-gs-modal-main-content"></div>
                </div>
                <div class="r-gs-modal-footer">
                    <div style="display:none;" class="r-gs-loader"><span></span><span></span><span></span><span></span><span></span>&nbsp;&nbsp;&nbsp;</div>
                    <a href="javascript:;" id="r_gs_modal_add_fields_submit" class="btn btn-primary"><?php echo Text::_('COM_GUESTSUPPORT_FORMS_ADD_NEW_FIELD'); ?></a>&nbsp;&nbsp;
                    <a href="javascript:;" id="r_gs_modal_close" class="btn btn-cancel"><?php echo Text::_('COM_GUESTSUPPORT_CANCEL'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Add field modal content -->

<!-- Edit field modal content -->
<div id="r_gs_modal_edit_fields" class="r-gs-modal r-gs-add-edit-fields-modal">
    <div class="r-gs-modal-wrapper">
        <div class="r-gs-modal-container">
            <div class="r-gs-modal-container-wrapper">
                <div class="r-gs-modal-header">
                    <h3><?php echo Text::_('COM_GUESTSUPPORT_EDIT_FIELD_TITLE'); ?> : <span id="r_gs_modal_edit_field_title"></span></h3>
                </div>
                <div class="r-gs-modal-content">
                    <div id="r_gs_modal_main_content" class="r-gs-modal-main-content"></div>
                </div>
                <div class="r-gs-modal-footer">
                    <div style="display:none;" class="r-gs-loader"><span></span><span></span><span></span><span></span><span></span>&nbsp;&nbsp;&nbsp;</div>
                    <a href="javascript:;" id="r_gs_modal_edit_fields_submit" class="btn btn-primary" data-group="none" ><?php echo Text::_('COM_GUESTSUPPORT_EDIT_FIELD_UPDATE'); ?></a>&nbsp;&nbsp;
                    <a href="javascript:;" id="r_gs_modal_close" class="btn btn-dark"><?php echo Text::_('COM_GUESTSUPPORT_CANCEL'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Edit field modal content -->

<script>
	jQuery(document).ready(function($) {
		$.rgsAddAgent("<?php echo Uri::base() . 'index.php?option=com_guestsupport&view=ajax'; ?>");
	});
</script>
