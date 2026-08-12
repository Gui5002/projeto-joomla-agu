/*!
 * @package     Guest Support
 * @author      RcaTheme.com <support@rcatheme.com>
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link        https://rcatheme.com
 * @copyright   2025 RcaTheme.com
 */

// Add Agent
(function($){
	$.rgsAddAgent = function($url) {
		// Get users to select as agent
        var resultarea = $('div.r-gs-dropdown-search-menu'),
		$text = $('div.r-gs-dropdown-search-text').html(),
		$input = $('input#r_gs_dropdown_search_box'),
		rgsTypingInterval = 500,
    	rgsTypingTimer;

		// Change Agent id on click
		$(document).on('click', '.r-gs-dropdown-search-menu > .item:not(.message)', function(event) {
			event.stopPropagation();
			var $this = $(this),
				$selected_id = $this.attr('data-value'),
				$selected_text = $this.html();
			$text = $selected_text;
			$('input#r_gs_dropdown_search_main').val($selected_id);
			$('div.r-gs-dropdown-search-text').html($text);
			$input.val($text);
			resultarea.hide(50);
		});
		$(document).on("click", function() {
			$input.val($text);
			resultarea.hide(50);
		});

		$('input#r_gs_dropdown_search_box').on("click", function(event) {
			event.stopPropagation();
		});

		$input.on('focus', function (event) {
			$input.val('');
			resultarea.show(50);
		});
		$input.on('keyup', function (event) {
			clearTimeout(rgsTypingTimer);
			rgsTypingTimer = setTimeout(rgsDoneTyping, rgsTypingInterval);
		});
		$input.on('keydown', function () {
			clearTimeout(rgsTypingTimer);
		});
		function rgsDoneTyping () {
			resultarea.html('');
			var searchterm = $input.val();
			if (searchterm != '' && searchterm.length > 1) {
				var post_vars = new Array();
				post_vars.push({name:'request', value: 'usersbyemail'});
				post_vars.push({name:'searchterm', value: searchterm});
				var data = jQuery.post($url, post_vars);

				data.done(function(reply_data) {
					var response_json = jQuery.parseJSON(reply_data);
					resultarea.html(response_json);
				});
			} else {
				resultarea.html('');
			}
		}
    };
})(jQuery);

jQuery(document).ready(function($) {
	$(document).on('click', '#r-gs-delete', function() {
		return confirm("Are you sure? You can't undo this action.");
	});
	// Modals
	$('div.r-gs-modal').appendTo('body');
	$(document).on('click', '#r_gs_open_modal', function() {
		var $this = $(this);
		var $thisContent = $this.attr("data-modal");

		if ( $thisContent == 'r_gs_modal_create_ticket_by_agent' ) {
            $('div#r_gs_create_ticket_by_agent div#r_gs_field_errormsg').hide().html("");
        }

		$('#' + $thisContent).addClass('r-gs-modal-active');
		$('body').addClass('r-gs-modal-active');
	});
	$(document).on('click', '#r_gs_modal_close', function() {
		rgsModalClose();
		/* $('div.r-gs-modal').removeClass('r-gs-modal-active');
		$('body').removeClass('r-gs-modal-active');
		$('div#r_gs_modal_main_content').html(""); */
	});

	function rgsModalClose() {
		$('div.r-gs-modal').removeClass('r-gs-modal-active');
		$('body').removeClass('r-gs-modal-active');
		$('div#r_gs_modal_main_content').html("");
	}

	// Add fields
	var $wtPreloader = '<div class="r-gs-loader"><span></span><span></span><span></span><span></span><span></span></div>';

	// Edit fields
	$(document).on('click', '#r_gs_edit_field', function() {
		var $this = $(this),
			$value = $this.attr("field-type"),
			$editType = $this.attr("field-edit"),
			$thisList = $this.closest('li'),
			$thisListId = $this.closest('li').attr('id'),

			$inputName = $thisList.find('input#r_gs_field_list_name').val(),
			$inputLabel = $thisList.find('input#r_gs_field_list_label').val(),
			$inputPlaceholder = $thisList.find('input#r_gs_field_list_placeholder').val(),
			$inputRequired = $thisList.find('input#r_gs_field_list_required').val(),
			$inputDescription = $thisList.find('textarea#r_gs_field_list_description').val(),
			$inputErrorMessage = $thisList.find('textarea#r_gs_field_list_error_message').val(),
			$inputColsize = $thisList.find('input#r_gs_field_list_colsize').val(),

			$fieldtoform = $thisList.find('input#r_gs_field_list_fieldtoform').val(),
			$showonticketview = $thisList.find('input#r_gs_field_list_showonticketview').val(),
			$addtoemail = $thisList.find('input#r_gs_field_list_addtoemail').val(),
			$login_required = $thisList.find('input#r_gs_field_list_login_required').val(),

			$min = $thisList.find('input#r_gs_field_list_min').val(),
			$max = $thisList.find('input#r_gs_field_list_max').val(),
			$step = $thisList.find('input#r_gs_field_list_step').val(),
			$options = $thisList.find('textarea#r_gs_field_list_options').val(),
			$hidden_field_value = $thisList.find('input#r_gs_field_list_hidden_value').val(),
			$custom_field_content = $thisList.find('textarea#r_gs_field_list_customtext').val();

		$thisList.addClass('r_gs_field_updating');

		if ( $value == 'departments' ) {
			var $showonform = $thisList.find('input#r_gs_field_list_showonform').val();
			var $default_department = $thisList.find('input#r_gs_field_list_default_department').val();
		}

		var $content = '<ul id="r_gs_field_type_' + $value + '" class="r-gs-field-contents r-gs-forms-form">' + $('ul#r_gs_field_type_' + $value).html() + '</ul>';

		// Change modal title
		$('span#r_gs_modal_edit_field_title').html( $inputName );

		// Activate modal
		$('#r_gs_modal_edit_fields').addClass('r-gs-modal-active');
		$('body').addClass('r-gs-modal-active');
		$('div#r_gs_modal_main_content').html($wtPreloader);

		setTimeout(function(){
			$('div#r_gs_modal_main_content').html($content);

			// Delete field name option for core field.
			if ( $editType == 'core' ) {
				$('div#r_gs_modal_main_content li.r_gs_field_list_name').remove();
				$('div#r_gs_modal_main_content li.r_gs_field_list_required').remove();
				$('div#r_gs_modal_main_content li.r_gs_field_list_showonticketview').remove();
				$('div#r_gs_modal_main_content li.r_gs_field_list_fieldtoform').remove();
				$('div#r_gs_modal_main_content li.r_gs_field_list_addtoemail').remove();
				$('div#r_gs_modal_main_content li.r_gs_field_list_login_required').remove();
			} else {
				$('div#r_gs_modal_main_content li.r_gs_field_list_name').hide();
			}
			// Change modal inputs
			$('div#r_gs_modal_main_content input#field_name').val( $inputName );
			$('div#r_gs_modal_main_content input#field_label').val( $inputLabel );
			$('div#r_gs_modal_main_content input#field_placeholder').val( $inputPlaceholder );
			$('div#r_gs_modal_main_content select#field_required').val( $inputRequired ).change();
			$('div#r_gs_modal_main_content textarea#field_description').val( $inputDescription );
			$('div#r_gs_modal_main_content textarea#field_error_message').val( $inputErrorMessage );
			$('div#r_gs_modal_main_content select#r_gs_field_list_colsize').val( $inputColsize ).change();

			$('div#r_gs_modal_main_content select#r_gs_field_list_fieldtoform').val( $fieldtoform ).change();
			$('div#r_gs_modal_main_content select#r_gs_field_list_showonticketview').val( $showonticketview ).change();
			$('div#r_gs_modal_main_content select#r_gs_field_list_addtoemail').val( $addtoemail ).change();
			$('div#r_gs_modal_main_content select#r_gs_field_list_login_required').val( $login_required ).change();

			$('div#r_gs_modal_main_content input#field_min').val( $min );
			$('div#r_gs_modal_main_content input#field_max').val( $max );
			$('div#r_gs_modal_main_content input#field_step').val( $step );
			$('div#r_gs_modal_main_content textarea#field_options').val( $options );
			$('div#r_gs_modal_main_content input#field_value').val( $hidden_field_value );
			$('div#r_gs_modal_main_content textarea#field_content').val( $custom_field_content );

			if ( $value == 'departments' ) {
				if ( $showonform == '1' ) {
					$('div#r_gs_modal_main_content input#showonform_departments_1').attr('checked', 'checked');
					$('li.r_gs_field_list_default_department > div.list-departments').hide();
				} else if ( $showonform == '0' ) {
					$('div#r_gs_modal_main_content input#showonform_departments_0').attr('checked', 'checked');
					$('li.r_gs_field_list_default_department > div.list-departments').show();
				}
				$('div#r_gs_modal_main_content select#field_default_department').val( $default_department ).change();
			}

			// Change data-field att on edit button
			$('div#r_gs_modal_edit_fields a#r_gs_modal_edit_fields_submit').attr('data-group', $thisListId);
		}, 500);
	});

	// Process update
	$(document).on('click', '#r_gs_modal_edit_fields_submit', function() {
		var $this = $(this);
		var $dataGroup = $this.attr('data-group');

		// Get the field group list id
		var $thisList = $this.closest('div.r-gs-modal-container-wrapper');

		// Show pre-loader
		$thisList.find('.r-gs-modal-footer div.r-gs-loader').show();

		// Get inputs
		var $inputName = $thisList.find('.r-gs-modal-content input#field_name').val(),
			$inputLabel = $thisList.find('.r-gs-modal-content input#field_label').val(),
			$inputPlaceholder = $thisList.find('.r-gs-modal-content input#field_placeholder').val(),
			$inputRequired = $thisList.find('.r-gs-modal-content select#field_required').val(),
			$inputDescription = $thisList.find('.r-gs-modal-content textarea#field_description').val(),
			$inputErrorMessage = $thisList.find('.r-gs-modal-content textarea#field_error_message').val(),
			$min = $thisList.find('.r-gs-modal-content input#field_min').val(),
			$max = $thisList.find('.r-gs-modal-content input#field_max').val(),
			$step = $thisList.find('.r-gs-modal-content input#field_step').val(),
			$options = $thisList.find('.r-gs-modal-content textarea#field_options').val(),
			$hidden_field_value = $thisList.find('.r-gs-modal-content input#field_value').val(),
			$custom_field_content = $thisList.find('.r-gs-modal-content textarea#field_content').val(),
			$colSize = $thisList.find('.r-gs-modal-content select#r_gs_field_list_colsize').val(),
			$fieldtoform = $thisList.find('.r-gs-modal-content select#r_gs_field_list_fieldtoform').val(),
			$showonticketview = $thisList.find('.r-gs-modal-content select#r_gs_field_list_showonticketview').val(),
			$addtoemail = $thisList.find('.r-gs-modal-content select#r_gs_field_list_addtoemail').val(),
			$login_required = $thisList.find('.r-gs-modal-content select#r_gs_field_list_login_required').val(),
			$inputType = $thisList.find('.r-gs-modal-content input#r_gs_this_field_input_type').val();

		if ( $options ) {
			var $optionsArray = $options.split(/\r?\n/);
		}

		// Departments
		var $deptShowonform = $thisList.find('.r-gs-modal-content input[name="showonform_departments"]:checked').val(),
			$deptDefaultDepartment = $thisList.find('.r-gs-modal-content select#field_default_department').val(),
			$deptShowonformYes = $('span#r_gs_texthelper_yes').html(),
			$deptShowonformNo = $('span#r_gs_texthelper_no').html();

		$('li#' + $dataGroup + ' input#r_gs_field_list_showonform').val( $deptShowonform );
		$('li#' + $dataGroup + ' select#r_gs_field_list_departments').val( $deptDefaultDepartment ).change();
		$('li#' + $dataGroup + ' input#r_gs_field_list_default_department').val( $deptDefaultDepartment );
		if ( $deptShowonform == '0' ) {
			$('li#' + $dataGroup + ' span#r_gs_form_field_label_showonform').html( $deptShowonformNo );
		} else {
			$('li#' + $dataGroup + ' span#r_gs_form_field_label_showonform').html( $deptShowonformYes );
		}

		// Update fields of this group
		$('li#' + $dataGroup + ' label').html($inputLabel);
		$('li#' + $dataGroup + ' input#r_gs_field_list_visible').attr('placeholder', $inputPlaceholder );
		$('li#' + $dataGroup + ' textarea#r_gs_field_list_visible').attr('placeholder', $inputPlaceholder );
		$('li#' + $dataGroup + ' input#r_gs_field_list_label').val( $inputLabel );
		$('li#' + $dataGroup + ' input#r_gs_field_list_placeholder').val( $inputPlaceholder );
		$('li#' + $dataGroup + ' textarea#r_gs_field_list_description').val( $inputDescription );
		$('li#' + $dataGroup + ' textarea#r_gs_field_list_error_message').val( $inputErrorMessage );

		$('li#' + $dataGroup + ' input#r_gs_field_list_min').val( $min );
		$('li#' + $dataGroup + ' input#r_gs_field_list_max').val( $max );
		$('li#' + $dataGroup + ' input#r_gs_field_list_step').val( $step );
		$('li#' + $dataGroup + ' input#r_gs_field_list_hidden_value').val( $hidden_field_value );
		$('li#' + $dataGroup + ' textarea#r_gs_field_list_options').val( $options );
		$('li#' + $dataGroup + ' textarea#r_gs_field_list_customtext').val( $custom_field_content );
		$('li#' + $dataGroup + ' div.r_gs_field_list_visible_customcontent').html( $custom_field_content );
		$('li#' + $dataGroup + ' div.r_gs_field_list_visible_pluginfield').html( 'Replace <code>' + $inputPlaceholder + '</code> with your desired input field using the plugin event. Please refer to the documentation for further details.' );

		if ( $inputType == 'select' ) {
			var $html_select = $('li#' + $dataGroup + ' select#r_gs_field_list_visible');
			$html_select.empty(); // remove old options
			if ( $options && $optionsArray.length !== 0 ) {
				$.each($optionsArray, function( index, value ) {
					$html_select.append($("<option></option>").attr("value", index).text(value));
				});
			}
		} else if ( $inputType == 'radio' ) {
			$html_radio = '';
			if ( $options && $optionsArray.length !== 0 ) {
				$.each($optionsArray, function( index, value ) {
					$html_radio += '<li><input name="demo_checkbox_' + $inputName + '" type="radio" id="' + $inputName + '_' + index + '" value="' + index + '"><label for="' + $inputName + '_' + index + '" class="r-gs-field-checkbox-label"> ' + value + '</label></li>';
				});
			}
			$('li#' + $dataGroup + ' ul.r-gs-field-checkbox').html($html_radio);
		} else if ( $inputType == 'checkbox' ) {
			$html_checkbox = '';
			if ( $options && $optionsArray.length !== 0 ) {
				$.each($optionsArray, function( index, value ) {
					$html_checkbox += '<li><input name="demo_checkbox_' + $inputName + '" type="checkbox" id="' + $inputName + '_' + index + '" value="' + index + '"><label for="' + $inputName + '_' + index + '" class="r-gs-field-checkbox-label"> ' + value + '</label></li>';
				});
			}
			$('li#' + $dataGroup + ' ul.r-gs-field-checkbox').html($html_checkbox);
		}

		if ($inputName) {
			$('li#' + $dataGroup + ' input#r_gs_field_list_name').val( $inputName );
			$('li#' + $dataGroup + ' span#r_gs_form_field_label_name').html( $inputName );
		}
		if ($inputRequired) {
			var $inputRequiredText = $thisList.find('.r-gs-modal-content select#field_required option:selected').text();
			$('li#' + $dataGroup + ' input#r_gs_field_list_required').val( $inputRequired );
			$('li#' + $dataGroup + ' span#r_gs_form_field_label_required').html( $inputRequiredText );
		}

		/** Column Size */
		if ( $inputType != 'hidden' ) {
			$('li#' + $dataGroup).removeClass('r-gs-size-100').removeClass('r-gs-size-50').removeClass('r-gs-size-33-3');
			$('li#' + $dataGroup).addClass('r-gs-size-' + $colSize);
			$('li#' + $dataGroup + ' input#r_gs_field_list_colsize').val( $colSize );
			$('li#' + $dataGroup + ' span#r_gs_form_field_label_colsize').html( $colSize + '%' );
		}

		$('li#' + $dataGroup + ' input#r_gs_field_list_fieldtoform').val( $fieldtoform );
		$('li#' + $dataGroup + ' input#r_gs_field_list_showonticketview').val( $showonticketview );
		$('li#' + $dataGroup + ' input#r_gs_field_list_addtoemail').val( $addtoemail );
		$('li#' + $dataGroup + ' input#r_gs_field_list_login_required').val( $login_required );
		/** End Column Size */
		$('li#' + $dataGroup).removeClass('r_gs_field_updating');

		setTimeout(function() {
			$thisList.find('.r-gs-modal-footer div.r-gs-loader').hide();
			rgsModalClose();
		}, 500);
	});
	$(document).on('click', '.r-gs-add-edit-fields-modal a#r_gs_modal_close', function() {
		$('div.r-gs-add-edit-fields-modal div#r_gs_modal_main_content').html('');
		$('div.r-gs-add-edit-fields-modal select#r_gs_add_field_type').val('').change();
	});

	// Add/change Agent
	// $.rgsAddAgent();

	// Show hide SMTP options
	$(document).on('change', 'select#r_gs_select_mailer', function(event) {
        var $thisval = $(this).val();
		if ( $thisval == 'joomla' ) {
			$('div#r_gs_settings_smtp_credentials').hide();
		}
		else
		{
			$('div#r_gs_settings_smtp_credentials').show();
		}
	});

	// Create plugin field placeholder
	$(document).on('input', 'input.r_gs_field_pluginfield_name', function(event) {
		$('input.r_gs_field_pluginfield_placeholder').val('{guest_support_field_' + $(this).val() + '}');
	});
});
