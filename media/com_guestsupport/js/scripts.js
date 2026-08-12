/*!
 * @package     Guest Support
 * @author      RcaTheme.com <support@rcatheme.com>
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link        https://rcatheme.com
 * @copyright   2022 RcaTheme.com
 */

jQuery(document).ready(function($) {
	if ( $('textarea#r-gs-ticketbyagent-message').length ) {
		quilljs_textarea("#r-gs-ticketbyagent-message", {
			modules: {
				toolbar: [
					["bold", "italic", "underline"],
					[{ "list": "bullet" }, { "list": "ordered" }, "link"],
					["blockquote", "code-block", "code"]
				]
			}, 
			theme: "snow",
		});
	}

	$(document).on('click', '#r-gs-delete', function() {
		return confirm("Are you sure? You can't undo this action.");
	});
	// Modals
	$('div.r-gs-modal').appendTo('body');
	$(document).on('click', '#r_gs_open_modal', function() {
		var $this = $(this);
		var $thisContent = $this.attr("data-modal");

		// Edit ticket message
		if ( $thisContent == 'r_gs_edit_reply_modal' ) {
			$block = $this.closest('div.r-gs-ticket-message');
			$block.addClass('editing-message');
			$('.r-gs-editreply-field .ql-editor').html( $block.find( 'div.r-gs-ticket-message-contentblock' ).html().trim() );
		}
		else if ( $thisContent == 'r_gs_modal_create_ticket_by_agent' ) {
			$('div#r_gs_create_ticket_by_agent div#r_gs_field_notice').hide().html("");
		}
		$('#' + $thisContent).addClass('r-gs-modal-active');
		$('body').addClass('r-gs-modal-active');
	});
	$(document).on('click', '#r_gs_modal_close', function() {
		rgsModalClose();
	});

	function rgsModalClose() {
		$('div.r-gs-modal').removeClass('r-gs-modal-active');
		$('body').removeClass('r-gs-modal-active');
		$('div.r-gs-ticket-message').removeClass('editing-message');
	}

	// Edit message
	$(document).on('click', 'button#r_gs_submit_editreply', function() {
		var $this = $(this),
			$message = $('textarea#r-gs-edit-message').val(),
			$block = $('div.r-gs-ticket-message.editing-message a#r_gs_open_modal'),
			$base_url = $('input#r-gs-baseurl').val(),
			$ticket_id = $block.attr('data-ticketid'),
			$ticket_auth = $block.attr('data-ticketauth'),
			$agent = $block.attr('data-agent'),
			$message_id = $block.attr('data-messageid');
		var post_vars = new Array();
		post_vars.push({name:'message', value: $message});
		post_vars.push({name:'ticket_id', value: $ticket_id});
		post_vars.push({name:'ticket_auth', value: $ticket_auth});
		post_vars.push({name:'agent', value: $agent});
		post_vars.push({name:'message_id', value: $message_id});
		var data = jQuery.post( $base_url + 'index.php?option=com_guestsupport&task=ticket.editreply', post_vars );

		data.done(function(reply_data) {
			var response_json = jQuery.parseJSON(reply_data);
			if ( response_json == 'success' ) {
				// Create a temporary element
				let tempElement = document.createElement('div');
				tempElement.innerHTML = $message;

				// Find and remove the select element
				let selectElement = tempElement.querySelector('select');
				if (selectElement) {
					selectElement.parentNode.removeChild(selectElement);
				}

				$('div.editing-message .r-gs-ticket-message-contentblock').html( tempElement.innerHTML );
				$("div.r-gs-editreply-notice").html("");
				$("div.r-gs-editreply-notice-success").show();
				setTimeout(function() {
					$("div.r-gs-editreply-notice-success").hide();
					rgsModalClose();
				}, 1500);
			} else {
				$("div.r-gs-editreply-notice").html(response_json);
			}
		});
	});

	/**
	 * Pro features
	 */

	// Knowledge base search
	var resultarea = $('div#r_gs_suggest_kb');
	var searchloader = '<div class="r-gs-loader"><span></span><span></span><span></span><span></span><span></span></div>';
	var rgsTypingInterval = 800;

	var rgsTypingTimer;
	var $rgsSearchInput = $('div.has-r-gs-suggest-kb input#r-gs-field-subject');
	$rgsSearchInput.on('keyup', function () {
		if ($('div.has-r-gs-suggest-kb').find('div.r-gs-loader').length < 1) {
			resultarea.html(searchloader);
		}
		clearTimeout(rgsTypingTimer);
		rgsTypingTimer = setTimeout(rgsDoneTyping, rgsTypingInterval);
	});
	$rgsSearchInput.on('keydown', function () {
		clearTimeout(rgsTypingTimer);
	});
	$rgsSearchInput.on('blur', function () {
		rgsDoneTyping();
	});
	function rgsDoneTyping () {
		var searchterm = $rgsSearchInput.val(),
			formId = $('input#form_id').val(),
			base_url = $('input#r_gs_baseurl').val();
		if (searchterm != '' && searchterm.length > 1) {
			var post_vars = new Array();
			post_vars.push({name:'form_id', value: formId});
			post_vars.push({name:'searchterm', value: searchterm});
			var data = jQuery.post( base_url + 'index.php?option=com_guestsupport&task=createticket.searchkb', post_vars );

			data.done(function(reply_data) {
				var response_json = jQuery.parseJSON(reply_data);
				resultarea.html(response_json);
			});
		} else {
			resultarea.html('');
		}
	}
	// Create ticket by Agent
	$(document).on('click', 'button#guest_support_submit_ticket_by_agent', function(event) {
		// event.stopPropagation();
		var $this = $(this);
		var $loader = '<div class="r-gs-loader"><span></span><span></span><span></span><span></span><span></span></div>',
			$resultarea = $('div#r_gs_create_ticket_by_agent div#r_gs_field_errormsg');
		$resultarea.show().html($loader);
		var $block = $('div#r_gs_create_ticket_by_agent'),
			$userid = $block.find('input#r_gs_dropdown_search_main').val(),
			$subject = $block.find('input#subject').val(),
			$department = $block.find('select#department').val(),
			$message = $('textarea#r-gs-ticketbyagent-message').val(),
			$base_url = $block.find('input#r_gs_baseurl').val();

		var post_vars = new Array();
		post_vars.push({name:'userid', value: $userid});
		post_vars.push({name:'subject', value: $subject});
		post_vars.push({name:'department', value: $department});
		post_vars.push({name:'message', value: $message});
		var data = jQuery.post( $base_url + 'index.php?option=com_guestsupport&task=createticket.ticketbyagent', post_vars );

		data.done(function(reply_data) {
			var response_json = jQuery.parseJSON(reply_data),
				$code = response_json['code'],
				$message = response_json['message'];

			if ( $code == 'success' ) {
				$this.hide();
				let closeButton = $this.next();
				closeButton.html(closeButton.attr('data-text-close'));
				$resultarea.hide().html("");
				$block.hide();
				$('div#r_gs_submit_ticket_by_agent_successmsg').show();
				$('div#r_gs_submit_ticket_by_agent_successmsg').html($message);
			} else {
				$resultarea.html($message);
			}
		});
	});

	$(document).on('click', 'div#r_gs_modal_create_ticket_by_agent a#r_gs_modal_close', function(event) {
		var $block = $('div#r_gs_create_ticket_by_agent');
		$block.find('input#subject').val("");
		$block.find('textarea#r-gs-ticketbyagent-message').val("");
		$block.find('.ql-editor').html("");
		$block.show();
		$(this).html($(this).attr('data-text-cancel'));
		$('button#guest_support_submit_ticket_by_agent').show();
		$('div#r_gs_submit_ticket_by_agent_successmsg').hide();
		$('div#r_gs_submit_ticket_by_agent_successmsg').html("");
	});

	// Profile pictuer
	$(document).on('change', 'input#r_gs_fp_image', function(e) {
		var $input	 = $(this),
			$filename	 = $input.val(),
			$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

		var $extension = $filename.replace(/^.*\./, '');
		if ($extension == $filename) {
			$extension = '';
		} else {
			$extension = $extension.toLowerCase();
		}

		// Check file extension, size, number of files etc.
		if ($.inArray($extension, $allowed_extensions) == -1) {
			$(this).val('');
			alert("Invalid image format.");
		} else if ( this.files[0].size > 1000000 ) {
			$(this).val('');
			alert("Maximum filesize limit is 1 MB.");
		} else if (this.files && this.files.length > 1) {
			$(this).val('');
			alert("Upload only one image.");
		} else {
			var $fileUrl = URL.createObjectURL(this.files[0]);
			$('img#r_gs_fp_img_main').attr('src', $fileUrl);
			$('img#r_gs_fp_img_main').onload = function() {
				URL.revokeObjectURL($fileUrl) // free memory
			}
		}
	});
});

// Verify email using OTP
(function($){
	$.rgsVerifyEmail = function($reCaptchaKey, $EmailErrorMsg, $OtpLengthMsg, $otpSentMsg, $ticketSubmitBtnText) {
		// Create or verify OTP and attach reCaptcha response
		$('form#r_gs_submit_ticket_form').submit(function(event) {
			event.preventDefault();
			var $rgsLoader = '<div class="r-gs-loader"><span></span><span></span><span></span><span></span><span></span></div>',
				$this = $(this),
				$email = $('input#r-gs-field-email').val(),
				$base_url = $('input#r_gs_baseurl').val(),
				$resultarea = $('div#r_gs_verify_notice'),
				$resultareaMsg = $('span#r_gs_verify_notice_msg');
			$resultarea.removeClass('success');

			if ( !$email )
			{
				$resultarea.show();
				$resultareaMsg.html($EmailErrorMsg);
			}

			$resultarea.show();
			$resultareaMsg.html($rgsLoader);

			if ( $this.hasClass('r-gs-verify-email') ) {
				// Verify OTP
				var $otp = $('input#r-gs-field-otp').val(),
					post_vars = new Array();

				if ( $.isNumeric($otp) && $otp.length === 6 ) {
					post_vars.push({name:'email', value: $email});
					post_vars.push({name:'otp', value: $otp});
					var data = jQuery.post( $base_url + 'index.php?option=com_guestsupport&task=createticket.verifyotp', post_vars );

					data.done(function(reply_data) {
						var response_json = jQuery.parseJSON(reply_data);
						if ( response_json == 'verified' ) {
							// OTP verified, now check if we need attach reCaptcha
							// Submit the form
							if ( $this.hasClass('r-gs-verify-captcha') ) {
								grecaptcha.ready(function() {
									grecaptcha.execute($reCaptchaKey, {action: 'rc_r_gs_create_ticket'}).then(function(token) {
										$('form#r_gs_submit_ticket_form').prepend('<input type="hidden" name="_recaptcha_token" value="' + token + '">');
										$('form#r_gs_submit_ticket_form').prepend('<input type="hidden" name="_recaptcha_action" value="rc_r_gs_create_ticket">');
										$('form#r_gs_submit_ticket_form').unbind('submit').submit();
									});
								});
							} else {
								// No need to attach reCaptcha, submit form
								$('form#r_gs_submit_ticket_form').unbind('submit').submit();
							}
						} else {
							setTimeout( function() {
								$resultarea.show();
								$resultareaMsg.html(response_json);
							}, 1000);
						}
					});
				} else {
					setTimeout( function() {
						$resultarea.show();
						$resultareaMsg.html($OtpLengthMsg);
					}, 1000);
				}
			} else {
				// Create OTP
				var post_vars = new Array();
				post_vars.push({name:'email', value: $email});
				var data = jQuery.post( $base_url + 'index.php?option=com_guestsupport&task=createticket.createotp', post_vars );

				data.done(function(reply_data) {
					var response_json = jQuery.parseJSON(reply_data);
					if ( response_json == 'success' ) {
						$this.addClass('r-gs-verify-email');
						setTimeout( function() {
							$('div#r-gs-block-otp').show();
							$resultarea.addClass('success');
							$resultarea.show();
							$resultareaMsg.html($otpSentMsg);
							$('button#guest_support_submit_ticket').html($ticketSubmitBtnText);
						}, 1000);
					} else {
						setTimeout( function() {
							$resultarea.show();
							$resultareaMsg.html(response_json);
						}, 1000);
					}
				});
			}
		});
	};
})(jQuery);

// File upload check
(function($){
	$.rgsCheckFileUpload = function($fileLimit, $totalSize, $fileTypes, $text_onefile, $text_sizelimitexceded, $text_filelimitexceded, $text_uploadfile, $text_invalidextension) {
		var $totalUploadSize = $totalSize * 1000;

		$(document).on('change', 'input.r-gs-field-fileupload', function(e) {
		// $("input.r-gs-field-fileupload").on('change', function(e) {
			var $input	 = $(this),
			$label	 = $input.next('label'),
			$labelVal = $label.html(),
			$parentDiv = $input.closest('div.r-gs-field-file'),
			$uploadedSize = 0,
			$uploadedFiles = 0,
			$fileExts = $fileTypes.replace(/\s+/g, '');
			$allowed_extensions = $fileExts.split(',');

			$("input.r-gs-field-fileupload").each(function() {
				for (var i = 0; i < this.files.length; i++) {
					$uploadedSize += this.files[i].size;
					$uploadedFiles += 1;
				}
			});

			var $filename = $(this).val();
			var $extension = $filename.replace(/^.*\./, '');
			if ($extension == $filename) {
				$extension = '';
			} else {
				$extension = $extension.toLowerCase();
			}

			var fileName = '';

			// Check file extension, size, number of files etc.
			if ($.inArray($extension, $allowed_extensions) == -1) {
				$(this).val('');
				alert($text_invalidextension + " " + $fileTypes);
			} else if ( $uploadedFiles > $fileLimit ) {
				$(this).val('');
				alert($text_filelimitexceded);
			} else if (this.files && this.files.length > 1) {
				$(this).val('');
				alert($text_onefile);
			} else if (this.files && $uploadedSize > $totalUploadSize) {
				$(this).val('');
				alert($text_sizelimitexceded);
			} else {
				if( this.files && this.files.length > 1 )
					fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
				else if( e.target.value )
					fileName = e.target.value.split( '\\' ).pop();

				if(fileName) {
					$label.find('span').html(fileName);
					$parentDiv.addClass('has-file');
				} else {
					$label.html($labelVal);
					$parentDiv.removeClass('has-file');
				}
			}
		});
		$("input.r-gs-field-fileupload").on( 'focus', function(){ $(this).closest('div.r-gs-field-file').addClass( 'has-focus' ); });
		$("input.r-gs-field-fileupload").on( 'blur', function(){ $(this).closest('div.r-gs-field-file').addClass( 'has-focus' ); });

		// Remove uploaded file
		$(document).on('click', 'div.r-gs-field-file.has-file span.r-gs-fileupload-remove', function() {
			var $theInput = $(this).closest('.r-gs-field-file').children('input.r-gs-field-fileupload');
			var $theLabel = $(this).closest('.r-gs-field-file').children('.r-gs-label-fileupload');
			var $theDiv = $(this).closest('.r-gs-field-file');
			$theInput.val('');
			if ($theInput.val() == '') {
				$theLabel.find('span').html($text_uploadfile);
				$theDiv.removeClass('has-file');
			}
		});
	};
})(jQuery);

// Add new file input block
(function($){
	$.rgsAddFileUploadBlock = function($uploadLimit, $text_uploadfile, $text_remove, $text_limitexceded) {
		$(document).on('click', 'div.r-gs-field-file-addnew', function() {
			var $currentItem = $('ul#r_gs_fileupload_block li').length;

			if ( $currentItem > $uploadLimit ) {
				alert($text_limitexceded);
				return;
			} else {
				$html = '<li>';
				$html += '<div class="r-gs-field-file">';
				$html += '<input type="file" name="attachments[]" id="attachment_' + $currentItem + '" class="r-gs-field-fileupload" value="" size="25">';
				$html += '<label class="r-gs-label-fileupload r-gs-grid r-gs-vcenter" for="attachment_' + $currentItem + '"><svg class="r-gs-fileupload-icon" width="14px" height="14px"><use href="#ticket_file_upload"></use></svg><span class="r-gs-block r-gs-block-fixed">' + $text_uploadfile + '</span></label>';
				$html += '<span class="r-gs-fileupload-remove"><svg class="r-gs-fileremove-icon" width="14px" height="14px"><use href="#ticket_file_remove"></use></svg><span class="r-gs-block r-gs-block-fixed">' + $text_remove + '</span></span>'; 
				$html += '</div>';
				$html += '</li>';
				$($html).insertBefore('ul#r_gs_fileupload_block li.r-gs-field-block-addnew');
			}

			// Remove add button if maximum allowed blocks already added
			if ( $currentItem == $uploadLimit ) {
				$('ul#r_gs_fileupload_block li.r-gs-field-block-addnew').remove();
			}
		});
	};
})(jQuery);

// Select User
(function($){
	$.rgsSelectUser = function($url) {
		// Get users to select as agent
        var resultarea = $('div.r-gs-dropdown-search-menu'),
		$text = $('div.r-gs-dropdown-search-text').html(),
		$input = $('input#r_gs_dropdown_search_box'),
		rgsTypingInterval = 500,
    	rgsTypingTimer;

		// Change Agent id on click
		$(document).on('click', '.r-gs-dropdown-search-menu > .r-gs-dds-item:not(.message)', function(event) {
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