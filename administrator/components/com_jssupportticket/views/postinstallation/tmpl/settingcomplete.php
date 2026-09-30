<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
 ^
 + Project: 	JS Tickets
 ^ 
*/

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

?>
<div id="js-tk-admin-wrapper" class="jsst-screen jsst-postinstallation-settingcomplete">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsst-main-wrapper" class="post-installation">
            <?php
$jsstWizardTitle = 'JS Support Ticket Settings';
include_once('components/com_jssupportticket/views/partials/wizardheader.php');
?>
            <div class="post-installtion-content-wrapper">
                <div class="post-installtion-content-header">
                    <ul class="update-header-img step-1">
                        <li class="header-parts first-part">
                            <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepone" title="link" class="tab_icon">
                                <img class="start" src="components/com_jssupportticket/include/images/postinstallation/general-settings.png" />
                                <span class="text"><?php echo Text::_('General'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts second-part">
                           <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=steptwo" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/ticket.png" />
                                <span class="text"><?php echo Text::_('Ticket Setting'); ?></span>
                            </a>
                        </li>
                        <li class="header-parts forth-part active">
                            <a href="index.php?option=com_jssupportticket&c=postinstallation&layout=settingcomplete" title="link" class="tab_icon">
                               <img class="start" src="components/com_jssupportticket/include/images/postinstallation/complete.png" />
                                <span class="text"><?php echo Text::_('Complete'); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="post-installtion-content_wrapper_right">
                    <div class="jsst-config-topheading">
                        <span class="heading-post-ins jsst-configurations-heading"><?php echo Text::_('Setting Complete');?></span>
                        <span class="heading-post-ins jsst-config-steps"><?php echo Text::_('Step 4 of 4');?></span>
                    </div>
                    <div class="post-installtion-content">
                        <form id="jslearnmanager-form-ins" method="post" action="#">
                            <div class="jsst_setting_complete_heading"><h1 class="Jsst_heading"><?php echo Text::_('Setting Completed'); ?></h1></div>
                            <div class="jsst_img_wrp">
                                <img src="components/com_jssupportticket/include/images/postinstallation/complete-setting.png" alt="<?php echo htmlspecialchars(Text::_('Setting Logo'), ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars(Text::_('Setting Logo'), ENT_QUOTES, 'UTF-8'); ?>"> 
                            </div>
                            <div class="jsst_text_below_img">
                                <?php echo Text::_('Setting you have applied has been save successfully');?>
                            </div>
                            <div class="pic-button-part">
                                <a class="next-step finish" href="index.php?option=com_jssupportticket">
                                    <?php echo Text::_('Finish'); ?>
                                </a>
                                <a class="back" href="index.php?option=com_jssupportticket&c=postinstallation&layout=stepthree"> 
                                   <img src="components/com_jssupportticket/include/images/postinstallation/back-arrow.png">
                                    <?php echo Text::_('Back'); ?>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
