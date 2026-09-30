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

$yesno = array(
    '0' => array('value' => '1',
        'text' => Text::_('JYES')),
    '1' => array('value' => '0',
        'text' => Text::_('JNO')),);
$med_field_width = 25;
$date_format = array(
    '0' => array('value' => 'd-m-Y', 'text' => Text::_('DD-MM-YYYY')),
    '1' => array('value' => 'm-d-Y', 'text' => Text::_('MM-DD-YYYY')),
    '2' => array('value' => 'Y-m-d', 'text' => Text::_('YYYY-MM-DD')),);

$date_format = HTMLHelper::_('select.genericList', $date_format, 'date_format', 'class="inputbox js-select jsst-postsetting" ' . '', 'value', 'text',$this->result['date_format']);
?>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-postinstallation-stepone">
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
                        <li class="header-parts first-part active">
                            <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepone" title="link" class="tab_icon">
                                <img class="start" src="components/com_jssupportticket/include/images/postinstallation/general-settings.png" />
                                <span class="text"><?php echo Text::_('General Setting'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts second-part">
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
                        <span class="heading-post-ins jsst-configurations-heading"><?php echo Text::_('General Configurations');?></span>
                        <span class="heading-post-ins jsst-config-steps"><?php echo Text::_('Step 1 of 4');?></span>
                    </div>
                    <div class="post-installtion-content">
                        <form id="jssupportticket-form-ins" method="post" action="index.php">
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('Title');?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" class="inputbox jsst-postsetting" name="title" id="title" placeholder="<?php echo Text::_('System Title'); ?>" size="<?php echo $med_field_width; ?>" value="<?php echo isset($this->result) ? $this->result['title'] : ''; ?>" />
                                </div>
                                <div class="desc">
                                    <?php echo Text::_("Enter the site title"); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('Data Directory');?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" class="inputbox jsst-postsetting" name="data_directory" id="directory" placeholder="<?php echo Text::_('Data Directory'); ?>" size="<?php echo $med_field_width; ?>" value="<?php echo isset($this->result) ? $this->result['data_directory'] : ''; ?>" />
                                </div>
                                <div class="desc">
                                    <?php echo Text::_("You need to rename the existing data directory in the file system before changing the data directory name"); echo ': <b>"'.JPATH_SITE.$this->result['data_directory'].'"</b>'; ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('Date Format');?>:  
                                </div>
                                <div class="field"> 
                                    <?php echo $date_format; ?>
                                </div>
                                <div class="desc"><?php echo Text::_('Date format for plugin');?> </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('No. of attachment');?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" name="noofattachment" value="<?php echo $this->result['noofattachment']; ?>" class="inputbox jsst-postsetting" size="<?php echo $med_field_width; ?>" />
                                </div>
                                <div class="desc">
                                    <?php echo Text::_('No. of attachment allowed at a time'); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('File maximum size');?>:  
                                </div>
                                <div class="field"> 
                                    <input type="text" class="inputbox jsst-postsetting" name="filesize" id="size" size="<?php echo $med_field_width; ?>" value="<?php echo $this->result['filesize']; ?>" /> Kb
                                </div>
                                <div class="desc">
                                    <?php echo Text::_("Upload file size in KB's"); ?>
                                </div>
                            </div>
                            <div class="pic-config">
                                <div class="title"> 
                                    <?php echo Text::_('File extension');?>:  
                                </div>
                                <div class="field"> 
                                    <textarea name="fileextension" cols="25" rows="3" class="inputbox js-textarea"><?php echo $this->result['fileextension']; ?></textarea>
                                </div>
                                <div class="desc">
                                    <?php echo Text::_("File extension allowed to attach"); ?>
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
                                <a class="next-step full-width" href="#"  onclick="document.getElementById('jssupportticket-form-ins').submit();" >
                                    <?php echo Text::_('Next'); ?>
                                    <img src="components/com_jssupportticket/include/images/postinstallation/next-arrow.png">
                                </a>
                            </div>
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="c" value="postinstallation" />
                            <input type="hidden" name="layout" value="stepone" />
                            <input type="hidden" name="step" value="1">
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
