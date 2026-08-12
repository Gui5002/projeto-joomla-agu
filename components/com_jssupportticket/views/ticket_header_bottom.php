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
use Joomla\CMS\Language\Text;
?>

<div id="tk_header_bottom">
    <ul id="tk_header_bottom_menu">
        <?php
        if(!$isstaff){ ?>
                    <li class="tk_header_bottom_menu_link"><a <?php if($layout=='formticket') echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid=<?php echo $this->Itemid; ?>" ><?php echo Text::_('New Ticket'); ?> </a> </li>
            <?php
                if($this->config['tplink_ticket_user'] == 1){ ?>
                    <li class="tk_header_bottom_menu_link"><a <?php if($layout=='mytickets') echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>" ><?php echo Text::_('My Tickets'); ?> </a> </li>
            <?php } ?>
            <?php if($layout=='ticketdetail'){ ?>
                <li class="tk_header_bottom_menu_link">
                    <a <?php if((!$isstaff) && ($layout=='ticket')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id=<?php echo $this->id; ?>&email=<?php echo $this->email; ?>&Itemid=<?php echo $this->Itemid; ?>" ><?php echo Text::_('Ticket detail'); ?> </a> </li>
            <?php } ?>
            <li class="tk_header_bottom_menu_link"><a <?php if((!$isstaff) && ($layout=='login')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=ticket&layout=ticketstatus&Itemid=<?php echo $this->Itemid; ?>" ><?php echo Text::_('Ticket status'); ?> </a> </li>
        <?php }elseif($isstaff){ ?>
            <?php switch ($layout) {
                case 'myticketsstaff':     
                case 'formticketstaff':
                    if($this->config['tplink_ticket_staff'] == 1){ ?>
                        <li class="tk_header_bottom_menu_link">
                            <a <?php if(($isstaff) && ($layout=='formticketstaff')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=ticket&layout=formticketstaff&Itemid=<?php echo $this->Itemid; ?>" >
                                    <?php echo Text::_('Add Ticket'); ?> 
                            </a> 
                        </li>
                        <li class="tk_header_bottom_menu_link">
                            <a <?php if(($isstaff) && ($layout=='myticketsstaff')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=ticket&layout=myticketsstaff&Itemid=<?php echo $this->Itemid; ?>" >
                                    <?php echo Text::_('My Tickets'); ?> 
                            </a> 
                        </li>
                    <?php  } 
                break;
                case 'articles':     
                case 'formarticle':
                    if($this->config['tplink_kb_staff'] == 1){ ?>
                        <li class="tk_header_bottom_menu_link">
                            <a <?php if(($isstaff) && ($layout=='formarticle')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=knowledgebase&layout=formarticle&Itemid=<?php echo $this->Itemid; ?>" >
                                    <?php echo Text::_('Add Knowledge Base'); ?> 
                            </a> 
                        </li>
                        <li class="tk_header_bottom_menu_link">
                            <a <?php if(($isstaff) && ($layout=='articles')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=knowledgebase&layout=articles&Itemid=<?php echo $this->Itemid; ?>" >
                                    <?php echo Text::_('Knowledge Base'); ?> 
                            </a> 
                        </li>
                    <?php }
                break;
                case 'announcements':     
                case 'formannouncement':
                    if($this->config['tplink_announcement_staff'] == 1){ ?>
                        <li class="tk_header_bottom_menu_link">
                            <a <?php if(($isstaff) && ($layout=='formannouncement')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=announcements&layout=formannouncement&Itemid=<?php echo $this->Itemid; ?>" >
                                    <?php echo Text::_('Add Announcement'); ?> 
                            </a> 
                        </li>
                        <li class="tk_header_bottom_menu_link">
                            <a <?php if(($isstaff) && ($layout=='announcements')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=announcements&layout=announcements&Itemid=<?php echo $this->Itemid; ?>" >
                                    <?php echo Text::_('Announcements'); ?> 
                            </a> 
                        </li>
                    <?php }  
                break;
                case 'downloads':     
                case 'formdownload':
                    if($this->config['tplink_download_staff'] == 1){ ?>
                    <li class="tk_header_bottom_menu_link">
                        <a <?php if(($isstaff) && ($layout=='formdownload')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=downloads&layout=formdownload&Itemid=<?php echo $this->Itemid; ?>" >
                                <?php echo Text::_('Add Download'); ?> 
                        </a> 
                    </li>
                    <li class="tk_header_bottom_menu_link">
                        <a <?php if(($isstaff) && ($layout=='downloads')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=downloads&layout=downloads&Itemid=<?php echo $this->Itemid; ?>" >
                                <?php echo Text::_('Downloads'); ?> 
                        </a> 
                    </li>
                    <?php }
                break;
                case 'faqs':     
                case 'formfaq':
                    if($this->config['tplink_faq_staff'] == 1){ ?>
                        <li class="tk_header_bottom_menu_link">
                            <a <?php if(($isstaff) && ($layout=='formfaq')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=faqs&layout=formfaq&Itemid=<?php echo $this->Itemid; ?>" >
                                    <?php echo Text::_('Add FAQ'); ?> 
                            </a> 
                        </li>
                        <li class="tk_header_bottom_menu_link">
                            <a <?php if(($isstaff) && ($layout=='faqs')) echo 'class="selected"'; ?> href="index.php?option=com_jssupportticket&c=faqs&layout=faqs&Itemid=<?php echo $this->Itemid; ?>" >
                                    <?php echo Text::_('FAQs'); ?>
                            </a> 
                        </li>
                    <?php  }
                break;
            }
        } ?>
    </ul>
</div>
</div> <!-- close the main header wraper div  -->
