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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;    

$id = Factory::getApplication()->input->getVar('id');
$isguest=$this->user->getIsGuest();
$layout=$this->layoutname;
$config_prefix = 'user';
$commonpath="index.php?option=com_jssupportticket";
$showhearderbottom = false;
$obj=[];
$array[]=array('text'=> Text::_('Dashboard'));?>
<?php
     if ($layout != null) {
        switch ($layout) {
            /*Control Panel*/
            case 'controlpanel':
                $array[] = array('text' => Text::_('Dashboard'));
            break;
            /*User Persmissions*/
            case 'userpermissions':
                $array[] = array('text' => Text::_('Staff Permissions'));
                break;
            /*Tickets*/
            case 'formticket':
                $text = ($id) ? Text::_('Edit Ticket') : Text::_('Create Ticket');
                $array[] = array('text' => $text);
                break;
            case 'mytickets':
            case 'myticketsstaff':
                $array[] = array('text' => Text::_('My Tickets'));
                break;
            case 'ticketdetail':
                $array[] = array('text' => Text::_('Ticket Details'));
            break;
            case 'ticketstatus':
                $array[] = array('text' => Text::_('Ticket Status'));
            break;
            /*Staffs*/
            case 'formstaff':
                $text = ($id) ? Text::_('Edit Staff Member') : Text::_('Add Staff');
                $array[] = array('text' => $text);
                break;
            case 'staff':
                $array[] = array('text' => Text::_('Staff Members'));
                break;
            case 'staffprofile':
                $array[] = array('text' => Text::_('My Profile'));
                break;
            /*Roles*/
            case 'formrole':
                $text = ($id) ? Text::_('Edit Role') : Text::_('Add Role');
                $array[] = array('text' => $text);
                break;
            case 'roles':
                $array[] = array('text' => Text::_('Roles'));
                break;
            /*Roles Permission*/
            case 'rolepermissions':
                $array[] = array('text' => Text::_('Role Permissions'));
            break;
            /*Reports*/
            case 'departmentreports':
                $array[] = array('text' => Text::_('Department Reports'));
                break;
            case 'staffreports':
                $array[] = array('text' => Text::_('Staff Reports'));
                break;
            case 'staffdetailreport':
                $array[] = array('text' => Text::_('Staff Report Details'));
                break;
            /*Mail*/
            case 'formmessage':
                $array[] = array('text' => Text::_('Send Message'));
                break;
            case 'inbox':
                $array[] = array('text' => Text::_('Inbox'));
                break;
            case 'outbox':
                $array[] = array('text' => Text::_('Outbox'));
                break;
            case 'message':
                $array[] = array('text' => Text::_('Messages'));
                break;
            /*Knowledgebase*/
            case 'formcategory':
                $text = ($id) ? Text::_('Edit Category') : Text::_('Add Category');
                $array[] = array('text' => $text);
                break;
            case 'formarticle':
                $text = ($id) ? Text::_('Edit Knowledge Base Article') : Text::_('Add Knowledge Base Article');
                $array[] = array('text' => $text);
                break;
            case 'categories':
                $array[] = array('text' => Text::_('Categories'));
                break;
            case 'articles':
                $array[] = array('text' => Text::_('Knowledge Base'));
                break;
            case 'userarticles':
            case 'usercatarticles':
                $array[] = array('text' => Text::_('Knowledge Base'));
                break;
            case 'usercatarticledetails':
                $array[] = array('text' => Text::_('Knowledge Base Details'));
                break;
            /*Faqs*/
            case 'formfaq':
                $text = ($id) ? Text::_('Edit Faq') : Text::_('Add Faq');
                $array[] = array('text' => $text);
                break;
            case 'faqs':
                $array[] = array('text' => Text::_('FAQs'));
                break;
            case 'userfaqs':
                $array[] = array('text' => Text::_('FAQs'));
                break;
            case 'userfaqdetail':
                $array[] = array('text' => Text::_('FAQ Details'));
                break;
            /*Download*/
            case 'formdownload':
                $text = ($id) ? Text::_('Edit Download') : Text::_('Add Download');
                $array[] = array('text' => $text);
                break;
            case 'downloads':
                $array[] = array('text' => Text::_('Downloads'));
                break;
            case 'userdownloads':
                $array[] = array('text' => Text::_('Downloads'));
                break;
            /*Department*/
            case 'formdepartment':
                $text = ($id) ? Text::_('Edit Department') : Text::_('Add Department');
                $array[] = array('text' => $text);
                break;
            case 'departments':
                $array[] = array('text' => Text::_('Departments'));
                break;
            /*FeedBack*/
            case 'feedbacks':
                $array[] = array('text' => Text::_('Feedback'));
                break;
            case 'formfeedback':
                $array[] = array('text' => Text::_('Add FeedBack'));
                break;
            /*Visitor Message Layout*/
            case 'visitorsuccessmessage':
                $array[] = array('text' => Text::_('Visitor Message'));
            break;
            /*Announcement*/
            case 'formannouncement':
                $text = ($id) ? Text::_('Edit Announcement') : Text::_('Add Announcement');
                $array[] = array('text' => $text);
            break;
            case 'announcements':
                $array[] = array('text' => Text::_('Announcements'));
            break;
            case 'userannouncements':
                $array[] = array('text' => Text::_('Announcements'));
            break;
            case 'userannouncementdetail':
                $array[] = array('text' => Text::_('Announcement Details'));
            break;
            case 'adderasedatarequest':
                $array[] = array('text' => Text::_('Erase Data Request'));
            break;
        }
    }
?>
<?php if (isset($array)) {
    foreach ($array AS $obj);
} ?>
<?php if($this->config['show_header'] == 1){ ?>
    <div id="jsst-header-main-wrapper">
        <div id="jsst-header">
            <?php /*
            <div id="jsst-header-heading" class="" >
                <a class="js-ticket-header-links"><?php echo $obj['text']; ?></a>
            </div> */ ?>
            <div id="jsst-tabs-wrp" class="" >
                <?php if($this->config['tplink_home_'.$config_prefix] == 1){?>
                    <span class="jsst-header-tab">
                        <a class="js-cp-menu-link <?php if($layout=='controlpanel') echo ' selected'; ?> " href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>">
                            <?php echo Text::_('Dashboard'); ?>
                        </a>
                    </span>
                <?php } ?>
                <?php if($this->config['tplink_ticket_'.$config_prefix] == 1){?>
                    <span class="jsst-header-tab">
                        <a class="js-cp-menu-link <?php if($layout=='formticket') echo ' selected'; ?> " href="index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid=<?php echo $this->Itemid; ?>" >
                            <?php echo Text::_('Submit Ticket'); ?>
                        </a>
                    </span>
                <?php } ?>
                <?php  if($this->config['tplink_ticket_'.$config_prefix] == 1){ ?>
                    <span class="jsst-header-tab">
                        <?php
                            $link = "index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=".$this->Itemid;
                        ?>
                        <a class="js-cp-menu-link <?php if($layout=='mytickets' || $layout=='myticketsstaff') echo ' selected'; ?>" href="<?php echo $link; ?>">
                            <?php echo Text::_('My Tickets'); ?>
                        </a>
                    </span>
                <?php } ?>
                <?php  $redirect = Route::_("index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=" . $this->Itemid , false);
                $redirect = '&amp;return=' . getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($redirect);
                if($isguest){ ?>
                    <span class="jsst-header-tab jsst-header-tab-right">
                        <a class="js-cp-menu-link" href="<?php echo 'index.php?option=com_users&view=login' . $redirect; ?>">
                            <?php echo Text::_('Log In'); ?>
                        </a>
                    </span>
                <?php }else{
                    $link = "index.php?option=com_jssupportticket&c=jssupportticket&task=logout&return=".$redirect."&Itemid=" . $this->Itemid; ?>
                    <span class="jsst-header-tab jsst-header-tab-right">
                        <a class="js-cp-menu-link" href="<?php echo $link; ?>">
                            <?php echo Text::_('Log Out'); ?>
                        </a>
                    </span>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

