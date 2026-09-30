<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Same value stepone.php uses. It was referenced further down for the
// auto-close field but never defined here, so that input rendered size=""
// and the read raised a notice on the wizard's second screen.
$med_field_width = 25;

$yesno = array(
    '0' => array('value' => '1',
        'text' => Text::_('JYES')),
    '1' => array('value' => '0',
        'text' => Text::_('JNO')),);
$ticketidsequence = array(
    '0' => array('value' => '1',
        'text' => Text::_('Random')),
    '1' => array('value' => '2',
        'text' => Text::_('Sequential')),);
$owncaptchaoparend = array(
    array('value' => '2', 'text' => '2'),
    array('value' => '3', 'text' => '3')
);
?>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-postinstallation-steptwo">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsst-main-wrapper" class="post-installation">
            <?php
$jsstWizardTitle = 'JS Support Ticket Configurations';
include_once('components/com_jssupportticket/views/partials/wizardheader.php');
?>
            <div class="post-installtion-content-wrapper">
                <div class="post-installtion-content-header">
                    <ul class="update-header-img step-1">
                        <li class="header-parts first-part">
                            <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepone" title="link" class="tab_icon">
                                <img class="start" src="components/com_jssupportticket/include/images/postinstallation/general-settings.png" />
                                <span class="text"><?php echo Text::_('General Setting'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts second-part active">
                           <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=steptwo" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/ticket.png" />
                                <span class="text"><?php echo Text::_('Ticket Setting'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts forth-part">
                            <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=settingcomplete" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/complete.png" />
                                <span class="text"><?php echo Text::_('Complete'); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="post-installtion-content_wrapper_right">
                    <div class="jsst-config-topheading">
                        <span class="heading-post-ins jsst-configurations-heading"><?php echo Text::_('Ticket Configurations');?></span>
                        <span class="heading-post-ins jsst-config-steps"><?php echo Text::_('Step 2 of 4');?></span>
                    </div>
                    <div class="post-installtion-content">
                        <form id="jssupportticket-form-ins" method="post" action="index.php">
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('Ticketid sequence'); ?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo HTMLHelper::_('select.genericList', $ticketidsequence, 'ticketid_sequence', 'class="inputbox jsst-postsetting" ' . '', 'value', 'text', $this->result['ticketid_sequence']); ?>
                                </div>
                                <div class="desc">
                                    <?php echo Text::_('Set ticketid sequential or random'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('Reopen ticket within days'); ?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" name="ticket_reopen_within_days" value="<?php echo $this->result['ticket_reopen_within_days']; ?>" class="inputbox jsst-postsetting" />
                                </div>
                                <div class="desc">
                                    <?php echo Text::_('Ticket can be reopen within given number of days'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('Ticket auto close');?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" class="inputbox jsst-postsetting" name="ticket_auto_close_indays" id="autoclose" size="<?php echo $med_field_width; ?>" value="<?php echo $this->result['ticket_auto_close_indays']; ?>" />
                                </div>
                                <div class="desc">
                                    <?php echo Text::_("Ticket auto close if user not respond within given days"); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('Show count on my tickets');?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo HTMLHelper::_('select.genericList', $yesno, 'show_count_tickets', 'class="inputbox js-select jsst-postsetting" ' . '', 'value', 'text', $this->result['show_count_tickets']); ?>
                                </div>
                            </div>
                            <div class="pic-button-part">
                                <a class="next-step" href="index.php?option=com_jssupportticket&c=postinstallation&layout=settingcomplete"  onclick="document.getElementById('jssupportticket-form-ins').submit();" >
                                    <?php echo Text::_('Next'); ?>
                                    <img src="components/com_jssupportticket/include/images/postinstallation/next-arrow-2.png">
                                </a>
                                <a class="back" href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepone"> 
                                   <img src="components/com_jssupportticket/include/images/postinstallation/back-arrow.png">
                                    <?php echo Text::_('Back'); ?>
                                </a>
                            </div>
                            
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="c" value="postinstallation" />
                            <input type="hidden" name="layout" value="settingcomplete" />
                            <input type="hidden" name="step" value="3">
                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <?php echo HTMLHelper::_( 'form.token' ); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
