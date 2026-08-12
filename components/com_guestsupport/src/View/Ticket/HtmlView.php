<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Site\View\Ticket;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
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
	protected $ticket;
	protected $subject;
	protected $formFields;
	protected $customFields;
	protected $otherTickets;
	protected $userProfile;
	protected $form;
	protected $params;
	protected $state;
	protected $deleted;
	protected $ispro;
	protected $is_agent;
	protected $encrypted_agent_id;
	protected $agent_url_param;
	protected $filter;
	protected $config;
	protected $ticket_url;
	protected $guest;
	protected $sidebar_options;
	protected $display_form_name = false;

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
		$user = $this->getCurrentUser();

		// Import Guestsupport plugins
        PluginHelper::importPlugin('guestsupport');

        // Get some data from the models
		$model 				= $this->getModel();
        $this->ticket		= $model->getTicket();
		$this->subject		= $model->getSubject();

		// Show warning message when the ticket is replying by an Administrator
		// but the Administrator isn't the assigned Agent.
		$administrator = $user->authorise('core.admin');
		$agent_user_id = $model->getAgentIdByDepartmentId();

		if ( $administrator && (int) $user->id !== $agent_user_id )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_ADMINISTRATOR_REPLYING_AS_AGENT'), 'info');
		}

		// Get deleted message from session if one exist
		$this->deleted = $session->get('com_guestsupport.ticketdeleted', '');
		// Empty deleted message data on session
		$session->set('com_guestsupport.ticketdeleted', '');

		if ( $this->deleted === true )
		{
			echo '<h4>' . Text::_('COM_GUESTSUPPORT_TICKET_DELETE_SUCCESSFULLY') . '</h4>';
			return;
		}

		// Early return if ticket not found
		if ( empty( $this->ticket ) || empty( $this->subject ) )
		{
			echo '<h4>' . Text::_('COM_GUESTSUPPORT_TICKET_NOT_FOUND') . '</h4>';
			return;
		}

        $this->otherTickets	= $model->getOthertickets();
        $this->userProfile	= $model->getUserProfile();
        $this->form			= $model->getForm();
		$this->state		= $model->getState();
		$this->params		= $this->state->get('params');
		$this->ispro		= GuestsupportHelper::isPro();
		$this->formFields	= array();
		$this->is_agent		= GuestsupportHelper::isAgent();
		$this->encrypted_agent_id = $this->is_agent ? GuestsupportHelper::encrypt( $this->is_agent ) : '';
		$this->agent_url_param = $this->is_agent && $this->encrypted_agent_id ? '&agent=' . $this->encrypted_agent_id : '';
		$this->ticket_url = 'index.php?option=com_guestsupport&id=' . $this->escape( $this->subject->ticket_id ) . '&auth=' . $this->escape( $this->subject->ticket_token ) . $this->agent_url_param;

		if ( !empty( $this->form ) && !empty( $this->form->params ) )
		{
			$this->sidebar_options = unserialize( $this->form->params );
		}
		else
		{
			$this->sidebar_options = [
				'top_text' => '',
				'display_ticket_id' => 'short',
				'display_ticket_status' => 1,
				'display_email' => 1,
				'display_form_name' => 'agents',
				'display_department' => 1,
				'display_ticket_creation_date' => 1,
				'ticket_creation_date_format' => 'M n, Y h:iA',
				'display_last_updated_date' => 1,
				'last_updated_date_format' => 'M n, Y h:iA',
				'display_custom_fields' => 1,
				'display_other_tickets' => 'both',
				'noof_other_tickets' => 10,
				'bottom_text' => ''
			];
		}

        $pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx', ''), ENT_COMPAT, 'UTF-8');
		$this->pageclass_sfx = $pageclass_sfx ? ' ' . $pageclass_sfx : '';

		// Whether display the form name or not
		$params_display_form_name = $this->sidebar_options['display_form_name'];
		if ( $params_display_form_name === 'both' || ( $params_display_form_name === 'agents' && $this->is_agent ) || ( $params_display_form_name === 'users' && !$this->is_agent ) )
		{
			$this->display_form_name = true;
		}

		// Form fields
		$this->filter = InputFilter::getInstance();
		$this->guest = $user->get('guest');

		// Custom fields
		$this->customFields = '';

        $this->_prepareDocument();

        parent::display($tpl);
    }

	/**
	 * Custom fields
	 */
	protected function displayCustomFields( $custom_fields )
	{
		return '';
	}

	/**
	 * Ticket and reply attachments
	 */
	protected function displayAttachments( $attachments, $message_user_type )
	{
		if ( empty( $attachments ) )
		{
			return '';
		}

		$view_attachments = GuestsupportHelper::getConfig( 'view_attachments', 'browser' );

		$html = '<ul>';
		foreach ( $attachments as $file ) {
			$fileurl = JPATH_SITE . '/images/com_guestsupport/attachments/' . $file->file_name_enc;

			$html .= '<li class="r-gs-grid r-gs-vcenter">';
			$html .= '<svg class="r-gs-ticket-attachment-icon" width="16px" height="16px"><use href="#ticket_attachment"></use></svg>';

			if ( file_exists( $fileurl ) )
			{
				$extension = pathinfo( $file->file_name_raw, PATHINFO_EXTENSION );
				if ( $view_attachments === 'browser' && in_array( $extension, [ 'jpg', 'jpeg', 'png', 'gif', 'pdf' ] ) )
				{
					$real_url = GuestsupportHelper::AttachmentsBaseUrl() . $file->file_name_enc;
					$html .= '<a href="' . $real_url . '" target="_blank">' . $this->filter->clean( $file->file_name_raw, 'STRING' ) . ' (' . GuestsupportHelper::bytesToReadable( $file->file_size ) . ')</a>';
				}
				else
				{
					$html .= '<a href="' . Route::_( $this->ticket_url . '&task=ticket.downloadfile&fileid=' . GuestsupportHelper::encrypt( $file->file_id ), false ) . '">' . $this->filter->clean( $file->file_name_raw, 'STRING' ) . ' (' . GuestsupportHelper::bytesToReadable( $file->file_size ) . ')</a>';
				}
			}
			else
			{
				$html .= '<span>' . $this->filter->clean( $file->file_name_raw, 'STRING' ) . ' (' . Text::_('COM_GUESTSUPPORT_FILE_NOT_FOUND') . ')</span>';
			}
			if ( $this->is_agent )
			{
				$html .= '&nbsp;&nbsp;-&nbsp;<svg class="r-gs-ticket-delete-icon" width="16px" height="16px"><use href="#ticket_file_delete"></use></svg>';
				$html .= '<span><a id="r-gs-delete" class="r-gs-delete-file" href="' . Route::_( $this->ticket_url . '&task=ticket.deletefile&fileid=' . GuestsupportHelper::encrypt( $file->file_id ), false ) . '">' . Text::_('COM_GUESTSUPPORT_DELETE') . '</a></span>';
			}
			elseif ( $message_user_type == 'user' )
			{
				$html .= '&nbsp;&nbsp;-&nbsp;<svg class="r-gs-ticket-delete-icon" width="16px" height="16px"><use href="#ticket_file_delete"></use></svg>';
				$html .= '<span><a id="r-gs-delete" class="r-gs-delete-file" href="' . Route::_( $this->ticket_url . '&task=ticket.deletefile&fileid=' . GuestsupportHelper::encrypt( $file->file_id ), false ) . '">' . Text::_('COM_GUESTSUPPORT_DELETE') . '</a></span>';
			}
			$html .= '</li>';
		}

		$html .= '</ul>';
		return $html;
	}

	/**
	 * Custom other tickets
	 */
	protected function displayOtherTickets()
	{
		if ( empty( $this->otherTickets ) )
		{
			return '';
		}

		$display = $this->sidebar_options['display_other_tickets'];
		if ( $display === 'none' )
		{
			return '';
		}

		$show_to_both = $display === 'both';
		$show_to_users = $display === 'users' || $show_to_both;
		$show_to_agents = $display === 'agents' || $show_to_both;

		if ( ( $show_to_users && !$this->is_agent ) || ( $this->is_agent && $show_to_agents ) )
		{
			$html = '<div class="r-gs-ticket-info-others">';
			$html .= '<h4>' . ( $this->is_agent ? Text::_('COM_GUESTSUPPORT_OTHER_TICKETS_BY_USER') : Text::_('COM_GUESTSUPPORT_MY_OTHER_TICKETS') ) . '</h4>';
			$html .= '<ol class="r-gs-ticket-othersbyuser">';

			foreach ( $this->otherTickets as $ticket )
			{
				$html .= '<li><a href="' . Route::_( 'index.php?option=com_guestsupport&view=ticket&id=' . $ticket->ticket_id . '&auth=' . $ticket->ticket_token . $this->agent_url_param, false ) . '">' . $this->filter->clean( $ticket->subject, 'STRING' ) . '</a></li>';
			}

			$html .= '</ol>';
			$html .= '</div>';

			return $html;
		}

		return '';
	}

	/**
	 * Display extra data
	 * Before status
	 */
	protected function displayExtraDataBeforeStatus()
	{
		// Import Guestsupport plugins
        PluginHelper::importPlugin('guestsupport');

		$event = new Event(
            'onGuestsupportTicketBeforeStatus',
            [
				'context' => 'guestsupport.ticket',
            	'ticket'  => $this->subject,
            	'is_agent'  => $this->is_agent
            ]
        );

		// Trigger an event, in case a plugin wishes to display data
		$results = Factory::getApplication()->getDispatcher()->dispatch( 'onGuestsupportTicketBeforeStatus', $event )->getArgument('result', '');
		return !empty( $results ) ? '<div class="r-gs-ticket-info-before-status">' . implode("\n", $results) . '</div>' : '';
	}

	/**
	 * Display extra data
	 * After other tickets
	 */
	protected function displayExtraDataAfterDepartment()
	{
		// Import Guestsupport plugins
        PluginHelper::importPlugin('guestsupport');

		$event = new Event(
            'onGuestsupportTicketAfterDepartment',
            [
				'context' => 'guestsupport.ticket',
            	'ticket'  => $this->subject,
            	'is_agent'  => $this->is_agent
            ]
        );

		// Trigger an event, in case a plugin wishes to display data
		$results = Factory::getApplication()->getDispatcher()->dispatch( 'onGuestsupportTicketAfterDepartment', $event )->getArgument('result', '');
		return !empty( $results ) ? '<div class="r-gs-ticket-info-after-department">' . implode("\n", $results) . '</div>' : '';
	}

	/**
	 * Display extra data
	 * Before other tickets
	 */
	protected function displayExtraDataBeforeOtherTickets()
	{
		// Import Guestsupport plugins
        PluginHelper::importPlugin('guestsupport');

		$event = new Event(
            'onGuestsupportTicketBeforeOtherTickets',
            [
				'context' => 'guestsupport.ticket',
            	'ticket'  => $this->subject,
				'is_agent'  => $this->is_agent
            ]
        );

		// Trigger an event, in case a plugin wishes to display data
		$results = Factory::getApplication()->getDispatcher()->dispatch( 'onGuestsupportTicketBeforeOtherTickets', $event )->getArgument('result', '');
		return !empty( $results ) ? '<div class="r-gs-ticket-info-before-other-tickets">' . implode("\n", $results) . '</div>' : '';
	}

	/**
	 * Display extra data
	 * After other tickets
	 */
	protected function displayExtraDataAfterOtherTickets()
	{
		// Import Guestsupport plugins
        PluginHelper::importPlugin('guestsupport');

		$event = new Event(
            'onGuestsupportTicketAfterOtherTickets',
            [
				'context' => 'guestsupport.ticket',
            	'ticket'  => $this->subject,
				'is_agent'  => $this->is_agent
            ]
        );

		// Trigger an event, in case a plugin wishes to display data
		$results = Factory::getApplication()->getDispatcher()->dispatch( 'onGuestsupportTicketAfterOtherTickets', $event )->getArgument('result', '');
		return !empty( $results ) ? '<div class="r-gs-ticket-info-after-other-tickets">' . implode("\n", $results) . '</div>' : '';
	}

	/**
	 * Prepares the document.
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app    = Factory::getApplication();
		$title = $this->subject->subject;

		if ($title) {
			$this->setDocumentTitle($title);
			// $this->document->setTitle($title);
		}

		$this->document->setMetaData('robots', 'noindex, nofollow');

		// For Breadcrumbs
		$pathway = $app->getPathWay();
        $pathway->addItem($title, Route::_( 'index.php?option=com_guestsupport&view=tickets', false ));

		// Load specific styles for this
		$wa = $this->document->getWebAssetManager();
		$wa->useStyle('com_guestsupport.styles')
			->useScript('com_guestsupport.scripts')
			->useScript('com_guestsupport.editor-scripts');

		// Send translated texts to function
		$fileLimit = $this->form->upload_filelimit;
		$totalSize = $this->form->upload_filesize;
		$fileTypes = str_replace(',', ', ', $this->form->allowed_filetypes);
		$text_onefile = Text::_('COM_GUESTSUPPORT_ONE_FILE');
		$text_sizelimitexceded = Text::_('COM_GUESTSUPPORT_SIZE_LIMIT_EXCEDED');
		$text_uploadfile = Text::_('COM_GUESTSUPPORT_FORM_CHOOSE_A_FILE');
		$text_remove = Text::_('COM_GUESTSUPPORT_REMOVE');
		$text_filelimitexceded = Text::_('COM_GUESTSUPPORT_FILE_LIMIT_EXCEDED');
		$text_invalidextension = Text::_('COM_GUESTSUPPORT_INVALID_EXTENSIONS');

		$this->config = GuestsupportHelper::config();
		$scroll_to_last = '';
		if ( $this->config->scroll_to_last == 'yes' )
		{
			$scroll_to_last = '$(window).scrollTop($("#r-gs-ticket-message-last").offset().top);$("#system-message-container").appendTo("#r_gs_system_msg");';
		}

		$script = '
			jQuery(document).ready(function($){
				$.rgsCheckFileUpload(' . $this->filter->clean($fileLimit, 'INT') . ', ' . $this->filter->clean($totalSize, 'INT') . ', "' . $this->filter->clean($fileTypes, 'STRING') . '", "' . $this->filter->clean($text_onefile, 'STRING') . '", "' . $this->filter->clean($text_sizelimitexceded, 'STRING') . '", "' . $this->filter->clean($text_filelimitexceded, 'STRING') . '", "' . $this->filter->clean($text_uploadfile, 'STRING') . '", "' . $this->filter->clean($text_invalidextension, 'STRING') . '");
				$.rgsAddFileUploadBlock(' . $this->filter->clean($fileLimit, 'INT') . ', "' . $this->filter->clean($text_uploadfile, 'STRING') . '", "' . $this->filter->clean($text_remove, 'STRING') . '", "' . $this->filter->clean($text_filelimitexceded, 'STRING') . '");
				$(document).on("click", "#r_gs_system_msg .joomla-alert--close", function() {
					$("#r_gs_system_msg > #system-message-container").remove();
				});
				' . $scroll_to_last . '
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
				quilljs_textarea("#r-gs-edit-message", {
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
