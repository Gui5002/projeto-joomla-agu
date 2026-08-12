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

$jinput = Factory::getApplication()->input;
$c = $jinput->get('c');
$layout = $jinput->get('layout');
$tf = $jinput->get('tf');
$ff = $jinput->get('ff');
?>
<div id="jsstadmin-logo">
    <a id="js-tk-top-lefticon" title="JS Help Desk System" class="jsst-anchor" href="javascript:void(0)">
        <img alt="JS Help Desk System" src="components/com_jssupportticket/include/images/logo.png">
    </a>
    <img id="jsstadmin-menu-toggle" src="components/com_jssupportticket/include/images/c_p/left-icons/menu.png">
</div>
<div id="js-tk-links"  data-widget="tree">
    <div class="treeview js-divlink <?php if($c=='' || $c == 'jssupportticket'  && $layout != 'themes' || $c == 'systemerror' || $c == 'proinstaller') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/dashboard.png"/>
            <span class="text js-parent <?php if($c == 'jssupportticket' && $layout != 'themes' || ($c == 'proinstaller' && ($layout == 'step1' || $layout == 'step2'))) echo 'lastshown'; ?>"><?php echo Text::_('Home'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            
                <a class="js-child <?php if($c == 'jssupportticket' && ($layout == 'controlpanel' || $layout == '')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel"><span class="text"><?php echo Text::_('Control Panel'); ?></span></a>
            
                <a class="js-child <?php if($c == 'proinstaller' && ($layout == 'step1' || $layout == 'step2')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=proinstaller&layout=step1"><span class="text"><?php echo Text::_('Update'); ?></span></a>
            
                <a class="js-child <?php if($c == 'jssupportticket' && ($layout == 'aboutus' || $layout == '')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=aboutus"><span class="text"><?php echo Text::_('About Us'); ?></span></a>
            
                <a class="js-child <?php if($c == 'jssupportticket' && ($layout == 'translation' || $layout == '')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=translation"><span class="text"><?php echo Text::_('Translation'); ?></span></a>

        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'ticket'  || $c == 'userfields' &&( $layout == 'export') || $layout == 'export' || $ff == '1' ) echo 'active'; ?> ">
        <a href="index.php?option=com_jssupportticket&c=ticket&layout=tickets" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/tickets.png"/>
            <span class="text js-parent <?php if($c == 'ticket'  || ($c=='reports' && $layout=='export') || ($c == 'userfields' && $layout=='fieldsordering' && $ff == '1')) echo 'lastshown'; ?>"><?php echo Text::_('Tickets'); ?></span>
        </a>
        <div class="treeview-menu js-innerlink">
            <a class="js-child <?php if($c == 'ticket' && ($layout == 'tickets')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets"><span class="text"><?php echo Text::_('Tickets'); ?></span></a>
            <a class="js-child <?php if($c == 'ticket' && ($layout == 'formticket')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=ticket&layout=formticket"><span class="text"><?php echo Text::_('Create Ticket'); ?></span></a>
            <a class="js-child <?php if($ff == '1') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering&ff=1"><span class="text"><?php echo Text::_('Fields'); ?></span></a>
            <a class="js-child disable-child" href="javascript:void(0)"><span class="text"><?php echo Text::_('Export'); ?></span><img src="components/com_jssupportticket/include/images/c_p/pro-icon.png"></a>
        </div>
    </div>
    <div class="disabled-menu js-divlink">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/staff.png"/>
            <span class="text js-parent"><?php echo Text::_('Staff members'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title=""><?php echo Text::_('Pro Version'); ?></a>
        </a>
    </div>
    <div class="treeview js-divlink <?php if($c == 'config') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=config&layout=config" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/settings.png"/>
            <span class="text js-parent <?php if($c == 'config' || $layout == 'themes') echo 'lastshown'; ?>"><?php echo Text::_('Configurations'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'config' && ($layout == 'config')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=config&layout=config"><span class="text"><?php echo Text::_('Configurations'); ?></span></a>
            <a class="js-child disable-child" href="javascript:void(0)"><span class="text"><?php echo Text::_('Themes'); ?></span><img src="components/com_jssupportticket/include/images/c_p/pro-icon.png"></a>
        </div>
    </div>
    <div class="treeview js-divlink <?php if($c == 'gdpr') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=gdpr&layout=gdprfields" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/lock.png"/>
            <span class="text js-parent <?php if($c == 'gdpr' || $layout == 'erasedatarequests') echo 'lastshown'; ?>"><?php echo Text::_('GDPR'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'gdpr' && $layout == 'erasedatarequests') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=gdpr&layout=erasedatarequests"><span class="text"><?php echo Text::_('Erase Data Requests'); ?></span></a>
        </div>
    </div>
    <div class="disabled-menu js-divlink">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/category.png"/>
            <span class="text js-parent"><?php echo Text::_('Categories'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title=""><?php echo Text::_('Pro Version'); ?></a>
        </a>  
    </div>
    <div class="disabled-menu js-divlink">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/kb.png"/>
            <span class="text js-parent"><?php echo Text::_('Knowledge Base'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title="View Pro Version"><?php echo Text::_('Pro Version'); ?></a>
        </a>
    </div>
    <div class="disabled-menu js-divlink">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/download.png"/>
            <span class="text js-parent"><?php echo Text::_('Downloads'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title="View Pro Version"><?php echo Text::_('Pro Version'); ?></a>
        </a>
    </div>
    <div class="disabled-menu js-divlink">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/announcements.png"/>
            <span class="text js-parent"><?php echo Text::_('Announcements'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title=""><?php echo Text::_('Pro Version'); ?></a>
        </a>
    </div>
    <div class="disabled-menu js-divlink">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/faq.png"/>
            <span class="text js-parent"><?php echo Text::_('FAQs'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title=""><?php echo Text::_('Pro Version'); ?></a>
        </a>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'department' && ($layout == 'departments' || $layout == 'formdepartment')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=department&layout=departments" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/department.png"/>
            <span class="text js-parent <?php if($c == 'department') echo 'lastshown'; ?>"><?php echo Text::_('Departments'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'department' && $layout == 'departments') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=department&layout=departments"><span class="text"><?php echo Text::_('Departments'); ?></span></a>
            <a class="js-child <?php if($c == 'department' && $layout == 'formdepartment') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=department&layout=formdepartment"><span class="text"><?php echo Text::_('Add Department'); ?></span></a>
        </div>
    </div>
    <div class="disabled-menu js-divlink">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/help-topic.png"/>
            <span class="text js-parent"><?php echo Text::_('Help Topics'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title=""><?php echo Text::_('Pro Version'); ?></a>
        </a>
    </div>
    <div class="disabled-menu js-divlink">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/premade-messages.png"/>
            <span class="text js-parent"><?php echo Text::_('Premade'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title=""><?php echo Text::_('Pro Version'); ?></a> 
        </a>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'priority' && ($layout == 'priorities' || $layout == 'formpriority')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=priority&layout=priorities" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/priorities.png"/>
            <span class="text js-parent <?php if($c == 'priority') echo 'lastshown'; ?>"><?php echo Text::_('Priorities'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'priority' && $layout == 'priorities') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=priority&layout=priorities"><span class="text"><?php echo Text::_('Priorities'); ?></span></a>
            <a class="js-child <?php if($c == 'priority' && $layout == 'formpriority') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=priority&layout=formpriority"><span class="text"><?php echo Text::_('Add Priority'); ?></span></a>
        </div>
    </div>
    <div class="disabled-menu js-divlink">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/role.png"/>
            <span class="text js-parent"><?php echo Text::_('Roles'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title=""><?php echo Text::_('Pro Version'); ?></a>
        </a>
    </div>
    <div class="js-divlink disabled-menu">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/feedback.png"/>
            <span class="text js-parent"><?php echo Text::_('Feedback'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title="View Pro Version"><?php echo Text::_('Pro Version'); ?></a>  
        </a>
    </div>
    <div class="js-divlink treeview <?php if($c == 'email' && ($layout == 'emails' || $layout == 'formemail')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=email&layout=emails" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/system-email.png"/>
            <span class="text js-parent <?php if($c == 'email') echo 'lastshown'; ?>"><?php echo Text::_('System Emails'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'email' && $layout == 'emails') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=email&layout=emails"><span class="text"><?php echo Text::_('Emails'); ?></span></a>
            <a class="js-child <?php if($c == 'email' && $layout == 'formemail') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=email&layout=formemail"><span class="text"><?php echo Text::_('Add Email'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'jssupportticket' && ($layout == 'translation')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=translation" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/language-icon.png"/>
            <span class="text js-parent <?php if($c == 'jssupportticket') echo 'lastshown'; ?>"><?php echo Text::_('Translation'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'jssupportticket' && $layout == 'translation') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=translation"><span class="text"><?php echo Text::_('Translation'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink disabled-menu">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/mails.png"/>
            <span class="text js-parent"><?php echo Text::_('Mail'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title=""><?php echo Text::_('Pro Version'); ?></a> 
        </a>
    </div>
    <div class="js-divlink disabled-menu ">
        <a href="javascript:void(0)" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/menu-grey/ban.png"/>
            <span class="text js-parent"><?php echo Text::_('Banned Emails'); ?></span>
            <a class="pro-btn" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=proversion" title=""><?php echo Text::_('Pro Version'); ?></a> 
        </a>
    </div>
    <div class="js-divlink treeview <?php if($c == 'emailtemplate') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=tk-ew-ad" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/email-templates.png"/>
            <span class="text js-parent <?php if($c == 'emailtemplate') echo 'lastshown'; ?>"><?php echo Text::_('Email Templates'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($tf == 'tk-ew-ad') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=tk-ew-ad"><span class="text"><?php echo Text::_('New Ticket Admin Alert'); ?></span></a>
            <a class="js-child <?php if($tf == 'ew-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=ew-tk"><span class="text"><?php echo Text::_('New Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'sntk-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=sntk-tk"><span class="text"><?php echo Text::_('Staff Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'rs-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=rs-tk"><span class="text"><?php echo Text::_('Reassign Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'cl-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=cl-tk"><span class="text"><?php echo Text::_('Close Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'dl-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=dl-tk"><span class="text"><?php echo Text::_('Delete Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'mo-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=mo-tk"><span class="text"><?php echo Text::_('Mark Overdue'); ?></span></a>
            <a class="js-child <?php if($tf == 'be-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=be-tk"><span class="text"><?php echo Text::_('Ban email'); ?></span></a>
            <a class="js-child <?php if($tf == 'dt-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=dt-tk"><span class="text"><?php echo Text::_('Department Transfer'); ?></span></a>
            <a class="js-child <?php if($tf == 'ebct-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=ebct-tk"><span class="text"><?php echo Text::_('Ban Email and Close Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'ube-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=ube-tk"><span class="text"><?php echo Text::_('Unban Email'); ?></span></a>
            <a class="js-child <?php if($tf == 'rsp-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=rsp-tk"><span class="text"><?php echo Text::_('Response Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'rpy-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=rpy-tk"><span class="text"><?php echo Text::_('Reply Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'be-trtk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=be-trtk"><span class="text"><?php echo Text::_('Ban email try to create ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'd-us-da') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=d-us-da"><span class="text"><?php echo Text::_('Erase User Data'); ?></span></a>
            <a class="js-child <?php if($tf == 'd-us-da-ad') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=d-us-da-ad"><span class="text"><?php echo Text::_('Erase User Data for admin'); ?></span></a>
            <a class="js-child <?php if($tf == 'u-da-de') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=u-da-de"><span class="text"><?php echo Text::_('User data deleted'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'systemerrors') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=systemerrors&layout=systemerrors" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/system-error.png"/>
            <span class="text js-parent <?php if($c == 'systemerrors') echo 'lastshown'; ?>"><?php echo Text::_('System Errors'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'systemerrors' && $layout == 'systemerrors') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=systemerrors&layout=systemerrors"><span class="text"><?php echo Text::_('System Errors'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'reports' && ($layout == 'overallreports')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=reports&layout=reports" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/report.png"/>
            <span class="text js-parent <?php if($c == 'reports') echo 'lastshown'; ?>"><?php echo Text::_('Reports'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'reports' && $layout == 'overallreports') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=reports&layout=overallreports"><span class="text"><?php echo Text::_('Overall Statistics'); ?></span></a>
            <a class="js-child disable-child" href="javascript:void(0)"><span class="text"><?php echo Text::_('Staff Reports'); ?></span><img src="components/com_jssupportticket/include/images/c_p/pro-icon.png"></a>
            <a class="js-child disable-child" href="javascript:void(0)"><span class="text"><?php echo Text::_('Department Reports'); ?></span><img src="components/com_jssupportticket/include/images/c_p/pro-icon.png"></a>
            <a class="js-child disable-child" href="javascript:void(0)"><span class="text"><?php echo Text::_('User Reports'); ?></span><img src="components/com_jssupportticket/include/images/c_p/pro-icon.png"></a>
        </div>
    </div>
</div>

<script type="text/javascript">
    var cookielist = document.cookie.split(';');
    for (var i=0; i<cookielist.length; i++) {
        if (cookielist[i].trim() == "jsst_collapse_admin_menu=1") {
            jQuery("#js-tk-admin-wrapper").addClass("menu-collasped-active");
            jQuery("#js-tk-copyright").addClass("menu-collasped-active-footer");
            break;
        }
    }
    jQuery(document).ready(function(){
        jQuery("img#js-admin-responsive-menu-link").click(function(e){
            e.preventDefault();
            if(jQuery("div#js-tk-leftmenu").css('display') == 'none'){
                jQuery("div#js-tk-leftmenu").show();
                jQuery("div#js-tk-leftmenu").find('.js-parent,a.js-parent2').show();
                jQuery('.js-parent.lastshown').next().find('a.js-child').css('display','block');
                jQuery('.js-parent.lastshown').find('img.arrow').attr("src","components/com_jssupportticket/include/images/c_p/arrow2.png");
                jQuery('.js-parent.lastshown').find('span').css('color','#ffffff');
            }else{
                jQuery("div#js-tk-leftmenu").hide();
            }
        });
        jQuery("img#jsstadmin-menu-toggle").click(function () {
            if(jQuery("div#js-tk-admin-wrapper").hasClass("menu-collasped-active")){
                jQuery('div#js-tk-admin-wrapper').removeClass('menu-collasped-active');
                jQuery('div#js-tk-copyright').removeClass('menu-collasped-active-footer');
                jQuery('.js-parent ').css('display','none');
                jQuery('a.js-child').css({'display':'none'});
                document.cookie = 'jsst_collapse_admin_menu=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
            }else{
                jQuery("div#js-tk-leftmenu").show();
                jQuery("div#js-tk-admin-wrapper").addClass('menu-collasped-active');
                jQuery("div#js-tk-copyright").addClass('menu-collasped-active-footer');
                jQuery('.js-parent ').css('display','inline-block');
                document.cookie = 'jsst_collapse_admin_menu=1; expires=Sat, 01 Jan 2050 00:00:00 UTC; path=/';
            }
        });
    });
</script>
