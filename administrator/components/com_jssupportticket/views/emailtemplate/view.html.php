<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
 ^
 + Project: 	JS Tickets
 ^
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class JSSupportticketViewEmailtemplate extends JSSupportTicketView
{
	function display($tpl = null)
	{
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        ToolbarHelper::title(Text::_('Tickets'));
        if($layoutName == 'emailtemplate'){
            $templatefor = Factory::getApplication()->input->get('tf');
            switch($templatefor){
                case 'ew-tk' : $text = Text::_('New Ticket'); break;
                case 'sntk-tk' : $text = Text::_('Staff Ticket'); break;
                case 'ew-md' : $text = Text::_('New Department'); break;
                case 'ew-sm' : $text = Text::_('New Staff'); break;
                case 'ew-ht' : $text = Text::_('New Help Topic'); break;
                case 'rs-tk' : $text = Text::_('Reassign Ticket'); break;
                case 'cl-tk' : $text = Text::_('Close Ticket'); break;
                case 'dl-tk' : $text = Text::_('Delete Ticket'); break;
                case 'mo-tk' : $text = Text::_('Mark Overdue'); break;
                case 'be-tk' : $text = Text::_('Ban email'); break;
                case 'be-trtk' : $text = Text::_('Ban email try to create ticket'); break;
                case 'dt-tk' : $text = Text::_('Department Transfer'); break;
                case 'ebct-tk' : $text = Text::_('Ban Email and Close Ticket'); break;
                case 'ube-tk' : $text = Text::_('Unban Email'); break;
                case 'rsp-tk' : $text = Text::_('Response Ticket'); break;
                case 'rpy-tk' : $text = Text::_('Reply Ticket'); break;
                case 'tk-ew-ad' : $text = Text::_('New Ticket Admin Alert'); break;
                case 'lk-tk' : $text = Text::_('Lock Ticket'); break;
                case 'ulk-tk' : $text = Text::_('Unlock ticket'); break;
                case 'minp-tk' : $text = Text::_('In Progress Ticket'); break;
                case 'pc-tk' : $text = Text::_('Ticket Priority Is Changed By'); break;
                case 'ml-ew' : $text = Text::_('New Mail Receviced'); break;
                case 'ml-rp' : $text = Text::_('New Mail Message Recevied'); break;
                case 'fd-bk' : $text = Text::_('Feedback Email to User'); break;
                case 'no-rp' : $text = Text::_('User Reply On Closed Ticket Email'); break;
                case 'd-us-da' : $text = Text::_('Erase user data request for user'); break;
                case 'd-us-da-ad' : $text = Text::_('Erase user data request for admin'); break;
                case 'u-da-de' : $text = Text::_('User data deleted'); break;
            }

            ToolbarHelper::title(Text::_('Email Templates').' <small><small>['.$text.'] </small></small>');
            ToolbarHelper::save('saveemailtemplate','Save email template');
            $template = $this->getJSModel('emailtemplate')->getTemplate($templatefor);
            $ufields = $this->getJSModel('userfields')->getUserfieldsfor(1);
            $this->template = $template;
            $this->ufields = $ufields;
            $this->templatefor = $templatefor;
        }

        parent::display($tpl);
	}
}
?>
