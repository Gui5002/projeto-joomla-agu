<?php

/**
 * @package		Joomla.Site
 * @subpackage	com_guestsupport
 * @author		RcaTheme.com https://www.rcatheme.com
 * @copyright	(C) 2025 RcaTheme.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Guestsupport\Site\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Guestsupport\Administrator\Helper\GuestsupportHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class TicketController extends BaseController
{
    /**
     * Update ticket status
     */
	public function status()
	{
		$app = $this->app;
		$config = GuestsupportHelper::config();
		$input = GuestsupportHelper::getInput();
		$ticket_id = $input->getString('id', '');
		$ticket_token = $input->getString('auth', '');
		$url_agent_id = $input->getString('agent', '');
		$status = $input->getString('status', '');
		$isagent = GuestsupportHelper::isAgent();

		$ticket_url = Route::_('index.php?option=com_guestsupport&view=ticket&id=' . $ticket_id . '&auth=' . $ticket_token . ($isagent ? '&agent=' . $url_agent_id : ''), false);

		$allowed_statuses = array( 'reopen', 'closed' );
		if ( !in_array( $status, $allowed_statuses ) )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_INVALID_STATUS'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		// Check if re-open allowed
		// Set status based on previous status
		if ( $status == 'reopen' )
		{
			if ( 
				$config->reopen_closed_ticket == 'no' || 
				( $config->reopen_closed_ticket == 'agent' && !$isagent ) || 
				( $config->reopen_closed_ticket == 'user' && $isagent )
			)
			{
				$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_REOPEN_NOT_ALLOWED'), 'error');
				$this->setRedirect($ticket_url);
				return false;
			}

			// Set new status
			$last_user_type = $this->getModel()->lastReplyUserType( $ticket_id, $ticket_token );
			if ( $last_user_type !== false && $last_user_type == 'user' )
			{
				$status = 'open';
			}
			elseif ( $last_user_type !== false && $last_user_type == 'agent' )
			{
				$status = 'pending';
			}
		}

		// Check if the ticket exist
		$check_ticket = $this->getModel()->getSubject( $ticket_id, $ticket_token );
		if ( $check_ticket === false || empty( $check_ticket ) )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_INVALID_TICKET'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		// Update ticket status
		$update = $this->getModel()->updateTicketStatus( $ticket_id, $ticket_token, $status );

		if ( $update === false )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_TICKET_STATUS_UPDATE_FAILED'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_TICKET_STATUS_UPDATE_SUCCESS'), 'success');
		$this->setRedirect($ticket_url);
		return true;
	}

	/**
	 * Delete ticket
	 */
	public function delete()
	{
		$app = $this->app;
		$input = GuestsupportHelper::getInput();
		$config = GuestsupportHelper::config();
		$ticket_id = $input->getString('id', '');
		$ticket_token = $input->getString('auth', '');
		$url_agent_id = $input->getString('agent', '');
		$isagent = GuestsupportHelper::isAgent();

		$ticket_url = Route::_('index.php?option=com_guestsupport&view=ticket&id=' . $ticket_id . '&auth=' . $ticket_token . ($isagent ? '&agent=' . $url_agent_id : ''), false);

		$user = $app->getIdentity();
		$guest = $user->get('guest');
		$admin = false;

		if ( !$guest && $user->authorise('core.admin') )
		{
			$admin = true;
		}

		// Only Agents and Super Administrators can delete tickets
		if ( $admin || ( $isagent && $config->can_delete_tickets == 'yes' ) )
		{
			$delete = $this->getModel()->delete( $ticket_id, $ticket_token );

			if ( $delete !== false )
			{
				// Set ticket deleted status on session
				$session = Factory::getApplication()->getSession();
				$session->set('com_guestsupport.ticketdeleted', true);
				$this->setRedirect($ticket_url);
				return true;
			}
		}
		$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_TICKET_DELETE_NOT_ALLOWED'), 'error');
		$this->setRedirect($ticket_url);
		return false;
	}

	/**
	 * Delete ticket replies
	 */
	public function deletereply()
	{
		$app = $this->app;
		$input = GuestsupportHelper::getInput();
		$config = GuestsupportHelper::config();
		$ticket_id = $input->getString('id', '');
		$ticket_token = $input->getString('auth', '');
		$url_agent_id = $input->getString('agent', '');
		$reply_id = $input->getString('replyid', '');
		$reply_id = GuestsupportHelper::sanitizeBase64( $reply_id );
		$reply_id = GuestsupportHelper::decrypt( $reply_id );
		$isagent = GuestsupportHelper::isAgent();

		$ticket_url = Route::_('index.php?option=com_guestsupport&view=ticket&id=' . $ticket_id . '&auth=' . $ticket_token . ($isagent ? '&agent=' . $url_agent_id : ''), false);

		$user = $app->getIdentity();
		$guest = $user->get('guest');
		$admin = false;

		if ( !$guest && $user->authorise('core.admin') )
		{
			$admin = true;
		}

		// Only Agents and Super Administrators can delete tickets
		if ( $admin || ( $isagent && $config->can_delete_replies == 'yes' ) )
		{
			$delete = $this->getModel()->deletereply( $ticket_id, $ticket_token, $reply_id );

			if ( $delete !== false )
			{
				$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_REPLY_DELETE_SUCCESSFULLY'), 'success');
				$this->setRedirect($ticket_url);
				return true;
			}
		}
		$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_TICKET_DELETE_NOT_ALLOWED'), 'error');
		$this->setRedirect($ticket_url);
		return false;
	}

	/**
	 * Download File
	 */
	public function downloadfile()
	{
		$app = $this->app;
		$input = GuestsupportHelper::getInput();
		$config = GuestsupportHelper::config();
		$ticket_id = $input->getString('id', '');
		$ticket_token = $input->getString('auth', '');
		$url_agent_id = $input->getString('agent', '');
		$file_id = $input->getString('fileid', '');
		$file_id = GuestsupportHelper::sanitizeBase64( $file_id );
		$file_id = GuestsupportHelper::decrypt( $file_id );
		$isagent = GuestsupportHelper::isAgent();

		$ticket_url = Route::_('index.php?option=com_guestsupport&view=ticket&id=' . $ticket_id . '&auth=' . $ticket_token . ($isagent ? '&agent=' . $url_agent_id : ''), false);

		// Get file info
		$fileinfo = $this->getModel()->getFileInfo( $ticket_id, $file_id );

		if ( $fileinfo === false )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_NOT_FOUND'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		$filepath = GuestsupportHelper::uploadDirectory() . '/' . $fileinfo->file_name_enc;

		if ( !file_exists( $filepath ) )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_NOT_FOUND'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		$filter = InputFilter::getInstance();

		// Download file
		@ob_end_clean();
		$filename = $fileinfo->file_name_raw;
		$user_agent = $filter->clean( $_SERVER["HTTP_USER_AGENT"], 'STRING' );
		header("Cache-Control: public, must-revalidate");
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		if (strstr(@$user_agent, "MSIE") == false)
		{
			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
		}
		header("Expires: 0");
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream; charset=utf-8");
		header("Content-Length: " . (string) filesize($filepath));
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header("Content-Transfer-Encoding: binary\n");
		@readfile($filepath);
		exit;
	}

	/**
	 * Delete File
	 */
	public function deletefile()
	{
		$app = $this->app;
		$input = GuestsupportHelper::getInput();
		$config = GuestsupportHelper::config();
		$ticket_id = $input->getString('id', '');
		$ticket_token = $input->getString('auth', '');
		$url_agent_id = $input->getString('agent', '');
		$file_id = $input->getString('fileid', '');
		$file_id = GuestsupportHelper::sanitizeBase64( $file_id );
		$file_id = GuestsupportHelper::decrypt( $file_id );
		$isagent = GuestsupportHelper::isAgent();

		$ticket_url = Route::_('index.php?option=com_guestsupport&view=ticket&id=' . $ticket_id . '&auth=' . $ticket_token . ($isagent ? '&agent=' . $url_agent_id : ''), false);

		// Get file info
		$fileinfo = $this->getModel()->getFileInfo( $ticket_id, $file_id );

		if ( $fileinfo === false )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_NO_FILE_TO_DELETE'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}

		$filepath = GuestsupportHelper::uploadDirectory() . '/' . $fileinfo->file_name_enc;

		$filename = $fileinfo->file_name_enc;

		// Delete file from database
		$request_by = $isagent ? 'agent' : 'user';
		$delete_record = $this->getModel()->deleteFile( $ticket_id, $file_id, $request_by );

		if ( $delete_record === false )
		{
			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_NO_FILE_TO_DELETE'), 'error');
			$this->setRedirect($ticket_url);
			return false;
		}
		else
		{
			// File record deleted from Database, now delete File.
			if ( file_exists( $filepath ) )
			{
				unlink( $filepath );
			}

			$app->enqueueMessage(Text::_('COM_GUESTSUPPORT_FILE_DELETE_SUCCESSFULLY'), 'success');
			$this->setRedirect($ticket_url);
			return true;
		}
	}

	/**
	 * Edit reply through Ajax request
	 */
	public function editreply()
	{
		$app = $this->app;
		$input = GuestsupportHelper::getInput();

		// Get post data
		$input_message = $input->post->getRaw('message', '');
		$message = GuestsupportHelper::sanitizeHtmlEditor( $input_message );
		$ticket_id = $input->post->getString('ticket_id', '');
		$ticket_token = $input->post->getString('ticket_auth', '');
		$message_id = $input->getString('message_id', '');
		$message_id = GuestsupportHelper::sanitizeBase64( $message_id );
		$message_id = GuestsupportHelper::decrypt( $message_id );

		if ( !$message || !$ticket_id || !$ticket_token || !$message_id )
		{
			$result = Text::_('COM_GUESTSUPPORT_EDIT_REPLY_NO_DATA');
			echo json_encode($result);
        	$app->close();
		}

		$config = GuestsupportHelper::config();
		$isagent = GuestsupportHelper::isAgent();
		$canedit = 0;

		if ( $config->can_edit_replies == 'both' || ( $config->can_edit_replies == 'agent' && $isagent ) || ( $config->can_edit_replies == 'user' && !$isagent ) )
        {
            if ( ( $isagent && $config->edit_replies_globally == 'yes' ) || $config->edit_reply_type == 'all' )
            {
                $canedit = 1;
            }
            elseif ( $config->edit_reply_type == 'last' )
            {
				$lastMessage = $this->getModel()->getLastMessage( $ticket_id, $ticket_token );
                if ( $lastMessage !== false && (int) $lastMessage->id === (int) $message_id && ( ( $lastMessage->status == 'pending' && $isagent ) || ( $lastMessage->status == 'open' && !$isagent ) ) )
                {
                    $canedit = 1;
                }
            }
        }
        else
        {
            $canedit = 0;
        }

		if ( $canedit )
        {
            $update = $this->getModel()->editMessage( $ticket_id, $ticket_token, $message_id, $message );
            if ( $update === true )
            {
                $result = 'success';
            }
            else
            {
                $result = Text::_('COM_GUESTSUPPORT_EDIT_REPLY_FAILED');
            }
        }
        else
        {
            $result = Text::_('COM_GUESTSUPPORT_EDIT_REPLY_NOT_ALLOWED');
        }

        // Output a \JSON object
        echo json_encode($result);

        $app->close();
	}
}
