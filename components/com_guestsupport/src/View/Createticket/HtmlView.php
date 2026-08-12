<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Site\View\Createticket;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\Event;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class HtmlView extends BaseHtmlView
{
	protected $pageclass_sfx;
	protected $form;
	protected $params;
	protected $state;
	protected $page_title;
	protected $ticketcreated;
	protected $recaptcha_enabled;
	protected $config;
	protected $filter;
	protected $confirmation_message;
	protected $ispro;
	protected $formFields;
	protected $verify_email;
	protected $verify_email_html;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     *
     * @since   3.1
     */
    public function display($tpl = null)
    {
        $app    = Factory::getApplication();
        $params = $app->getParams();
		$session = Factory::getApplication()->getSession();

		// Import Guestsupport plugins
        PluginHelper::importPlugin('guestsupport');

		// Get ticket created message from session if one exist
		$this->ticketcreated = $session->get('com_guestsupport.ticketcreated', '');
		// Empty ticket created message data on session
		$session->set('com_guestsupport.ticketcreated', '');

		if ( $this->ticketcreated === true )
		{
			$this->confirmation_message = $session->get('com_guestsupport.new_ticket_confirmation_message', '');
			$session->set('com_guestsupport.new_ticket_confirmation_message', '');
			parent::display('success');
			return;
		}

        // Get some data from the models
		$model 				= $this->getModel();
        $this->form			= $model->getForm();
		$this->state		= $model->getState();
		$this->params		= $this->state->get('params');
		$this->ispro		= GuestsupportHelper::isPro();
		$this->config		= GuestsupportHelper::config();
		$this->filter		= InputFilter::getInstance();

        $pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx', ''), ENT_COMPAT, 'UTF-8');
		$this->pageclass_sfx = $pageclass_sfx ? ' ' . $pageclass_sfx : '';

		// Page title
		$menu = Factory::getApplication()->getMenu()->getActive();
		if ( $this->params->get('show_page_heading') && trim( $this->params->get('page_heading') ) )
		{
			$this->page_title = $this->params->get('page_heading');
		}
		else
		{
			$this->page_title = $menu->title;
		}

		if ( empty( $this->form ) )
		{
			throw new \Exception(Text::_('COM_GUESTSUPPORT_ERROR_FORM_NOT_FOUND'), 404);
		}

		// Form fields
		$user = $this->getCurrentUser();
		$guest = $user->get('guest');

		$name = '';
		$email = '';
		if ( !$guest )
		{
			$name = $user->name;
			$email = $user->email;
		}
		$this->verify_email = 0;
		if ( $this->form->verify_email == 1 && $guest )
		{
			$this->verify_email = 1;
		}

		$this->recaptcha_enabled = 0;
		if ( $this->form->recaptcha == 1 && $this->config->recaptcha_secret_key && $this->config->recaptcha_site_key )
		{
			$this->recaptcha_enabled = 1;
		}

		$html = '';
		$ve_html = '';
		$departments = $model->getDepartmentsForThisForm();

		$prefillData = $app->getUserState('com_guestsupport.createticket.data', []);
		if ( empty( $prefillData ) )
		{
			// This will be sanitized after submitting the ticket.
			$prefillData = $_REQUEST;
		}

		if ( !empty( $this->form ) )
		{
			$formFields = unserialize( $this->form->form_fields );

			if ( !empty( $formFields ) )
			{
				foreach ( $formFields as $field ) {
					if ( !in_array( $field['name'], ['name', 'email', 'subject', 'department', 'message'] ) )
					{
						die();
					}
					if ( $field['name'] === 'department' )
					{
						continue;
					}

					$html .= '<div id="r-gs-block-' . $this->escape( $field['inputtype'] ) . '" class="r-gs-block r-gs-size-' . $this->escape( $field['colsize'] ) . ' r-gs-field-' . $this->escape( $field['name'] ) . ( $field['name'] === 'message' ? ' ' . $this->escape( $this->form->input_class ) : '' ) . '">';
					$html .= '<div class="r-gs-field' . ( $field['name'] == 'subject' && $this->form->suggest_docs == 1 ? ' has-r-gs-suggest-kb' : '' ) . '">';
					$placeholder = isset( $field['placeholder'] ) && !empty( trim( $field['placeholder'] ) ) ? Text::_( $this->filter->clean($field['placeholder'], 'STRING') ) : '';

					$value = $prefillData[$field['name']] ?? '';
					if ( !$value )
					{
						if ( $field['name'] == 'name' )
						{
							$value = $name;
						}
						elseif ( $field['name'] == 'email' )
						{
							$value = $email;
						}
					}

					$html .= '<label for="r-gs-field-' . $this->escape( $field['name'] ) . '">' . Text::_( $this->filter->clean($field['label'], 'STRING') ) . '</label>';
					if ( $field['inputtype'] == 'text' )
					{
						$html .= '<input type="text" name="' . $this->escape( $field['name'] ) . '" id="r-gs-field-' . $this->escape( $field['name'] ) . '" class="' . $this->escape( $this->form->input_class ) . ( $field['name'] == 'name' && $name ? ' disabled' : '' ) . '" value="' . $this->escape( $value ) . '" placeholder="' . $placeholder . '"' . ( $field['name'] == 'name' ? ' autocomplete="name"' : '' ) . ( $field['required'] == 1 ? ' required' : '' ) . '>';
						if ( $field['name'] == 'subject' && $this->form->suggest_docs == 1 )
						{
							$html .= '<div id="r_gs_suggest_kb" class="r-gs-suggest-kb"></div>';
						}
					}
					elseif ( $field['inputtype'] == 'email' )
					{
						$html .= '<input type="email" name="' . $this->escape( $field['name'] ) . '" id="r-gs-field-' . $this->escape( $field['name'] ) . '" class="' . $this->escape( $this->form->input_class ) . ( $field['name'] == 'email' && $email ? ' disabled' : '' ) . '" value="' . $this->escape( $value ) . '" placeholder="' . $placeholder . '"' . ( $field['name'] == 'email' ? ' autocomplete="email"' : '' ) . ( $field['required'] == 1 ? ' required' : '' ) . '>';
					}
					elseif ( $field['inputtype'] == 'textarea' && $field['name'] == 'message' )
					{
						$html .= '<textarea name="' . $this->escape( $field['name'] ) . '" id="r-gs-field-' . $this->escape( $field['name'] ) . '" class="' . $this->escape( $this->form->input_class ) . '" rows="5" cols="40" placeholder="' .$placeholder . '">' . $value . '</textarea>';
					}
					if ( isset( $field['description'] ) && $field['description'] )
					{
						$html .= '<div class="r-gs-field-desc">' . GuestsupportHelper::sanitizeHtml( nl2br( $field['description'] ) ) . '</div>';
					}
					$html .= '</div>';
					$html .= '</div>';
				}
			}
		}

		// Get data again from the plugin event,
		// so we can use the updated data if any.
		$this->formFields = $html;

		$this->verify_email_html = '';

        $this->_prepareDocument();

        parent::display($tpl);
    }

	/**
	 * Prepares the document.
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$wa = $this->document->getWebAssetManager();
		$wa->useStyle('com_guestsupport.styles')
			->useScript('com_guestsupport.scripts')
			->useScript('com_guestsupport.editor-scripts');

		if ( $this->recaptcha_enabled ) {
			$wa->registerAndUseScript('com_guestsupport.recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . $this->config->recaptcha_site_key, [], [], []);
		}

		// Prepare texts
		$fileLimit = $this->form->upload_filelimit;
		$totalSize = $this->form->upload_filesize;
		$fileTypes = str_replace(',', ', ', $this->form->allowed_filetypes);
		$text_onefile = Text::_('COM_GUESTSUPPORT_ONE_FILE');
		$text_sizelimitexceded = Text::_('COM_GUESTSUPPORT_SIZE_LIMIT_EXCEDED');
		$text_uploadfile = Text::_('COM_GUESTSUPPORT_FORM_CHOOSE_A_FILE');
		$text_remove = Text::_('COM_GUESTSUPPORT_REMOVE');
		$text_filelimitexceded = Text::_('COM_GUESTSUPPORT_FILE_LIMIT_EXCEDED');
		$text_invalidextension = Text::_('COM_GUESTSUPPORT_INVALID_EXTENSIONS');
		$EmailErrorMsg = Text::_('COM_GUESTSUPPORT_ENTER_VALID_EMAIL');
		$OtpLengthMsg = Text::_('COM_GUESTSUPPORT_OTP_LENGTH');
		$otpSentMsg = Text::_('COM_GUESTSUPPORT_OTP_SENT');
		$ticketSubmitBtnText = Text::_( $this->filter->clean($this->form->submit_text, 'STRING') );

		$script = 'jQuery(document).ready(function($){' . 
			( ( !$this->ispro && $this->recaptcha_enabled ) || ( $this->ispro && !$this->verify_email && $this->recaptcha_enabled ) ? '
				$("form#r_gs_submit_ticket_form").submit(function(event) {
					event.preventDefault();
					grecaptcha.ready(function() {
						grecaptcha.execute("' . $this->escape( $this->config->recaptcha_site_key ) . '", {action: "rc_r_gs_create_ticket"}).then(function(token) {
							$("form#r_gs_submit_ticket_form").prepend(\'<input type="hidden" name="_recaptcha_token" value="\' + token + \'">\');
							$("form#r_gs_submit_ticket_form").prepend(\'<input type="hidden" name="_recaptcha_action" value="rc_r_gs_create_ticket">\');
							$("form#r_gs_submit_ticket_form").unbind("submit").submit();
						});
					});
				});
			' : '' ) .
			($this->form->fileupload ? '
				$.rgsCheckFileUpload(' . $this->filter->clean($fileLimit, 'INT') . ', ' . $this->filter->clean($totalSize, 'INT') . ', "' . $this->filter->clean($fileTypes, 'STRING') . '", "' . $this->filter->clean($text_onefile, 'STRING') . '", "' . $this->filter->clean($text_sizelimitexceded, 'STRING') . '", "' . $this->filter->clean($text_filelimitexceded, 'STRING') . '", "' . $this->filter->clean($text_uploadfile, 'STRING') . '", "' . $this->filter->clean($text_invalidextension, 'STRING') . '");
				$.rgsAddFileUploadBlock(' . $this->filter->clean($fileLimit, 'INT') . ', "' . $this->filter->clean($text_uploadfile, 'STRING') . '", "' . $this->filter->clean($text_remove, 'STRING') . '", "' . $this->filter->clean($text_filelimitexceded, 'STRING') . '");
			' : '') . 
			( $this->verify_email ? '$.rgsVerifyEmail("' . $this->escape( $this->config->recaptcha_site_key ) . '", "' . $this->filter->clean($EmailErrorMsg, 'STRING') . '", "' . $this->filter->clean($OtpLengthMsg, 'STRING') . '", "' . $this->filter->clean($otpSentMsg, 'STRING') . '", "' . $this->filter->clean($ticketSubmitBtnText, 'STRING') . '");' : '' ) . 
			'
			quilljs_textarea("#r-gs-field-message", {
				modules: {
					toolbar: [
						["bold", "italic", "underline"],
						[{ "list": "bullet" }, { "list": "ordered" }, "link"],
						["blockquote", "code-block", "code"]
					]
				}, 
				theme: "snow",
			});
		});
		';

		$wa->addInlineScript($script, ['position' => 'after'], [], ['com_guestsupport.editor-scripts']);
	}
}
