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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/circle.css');
$document->addScript('components/com_jssupportticket/include/js/circle.js');
?>
<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        jQuery('a[href="#"]').click(function(e){
            e.preventDefault();
        });
        jQuery("div#js-ticket-main-black-background,span#js-ticket-popup-close-button").click(function () {
            jQuery("div#js-ticket-main-popup").slideUp();
            setTimeout(function () {
                jQuery("div#js-ticket-main-black-background").hide();
            }, 600);

        });
    });
</script>
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE.'/views/header.php';
    $document = Factory::getDocument();
    $document->addStyleSheet(Uri::root().'components/com_jssupportticket/include/css/inc.css/ticket-myticket.css', 'text/css');

    $language = Factory::getLanguage();
    $document->addStyleSheet(Uri::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(Uri::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    $c_count = $this->config['controlpanel_column_count'];
    if($c_count < 1 || $c_count > 12){
        $c_count = 3;
    }else{
        $c_count = ceil(12/$c_count);
    } ?>
        <div id="jsst-wrapper-top">
            <?php if($this->config['cur_location'] == 1){ ?>
                <div id="jsst-wrapper-top-left">
                    <div id="jsst-breadcrunbs">
                        <ul>
                            <li>
                                <?php echo Text::_('Dashboard'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
    </div>
    <div class="js-cp-left" >
        <div id="js-dash-menu-link-wrp">
            <!-- Dashboard Links -->
            <div class="js-section-heading"><?php echo Text::_('Dashboard Links'); ?></div>
            <div class="js-menu-links-wrp">
                <div class="js-ticket-menu-links-row">
                    <?php if($this->config['cplink_openticket_user'] == 1){ ?>
                        <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid=<?php echo $this->Itemid; ?>">
                            <div class="js-ticket-dash-menu-icon">
                                <img class="js-ticket-dash-menu-img" src="<?php echo Uri::root() ?>components/com_jssupportticket/include/images/dashboard-icon/add-ticket-icon.png" />
                            </div>
                            <span class="js-ticket-dash-menu-text"><?php echo Text::_('Submit Ticket'); ?></span>
                        </a>
                    <?php } ?>
                    <?php if($this->config['cplink_myticket_user'] == 1){ ?>
                        <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>">
                            <div class="js-ticket-dash-menu-icon">
                                <img class="js-ticket-dash-menu-img" src="<?php echo Uri::root() ?>components/com_jssupportticket/include/images/dashboard-icon/tickets.png" />
                            </div>
                            <span class="js-ticket-dash-menu-text"><?php echo Text::_('My Tickets'); ?></span>
                        </a>
                    <?php } ?>
                    <?php if($this->config['cplink_checkstatus_user'] == 1){ ?>
                        <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=ticket&layout=ticketstatus&Itemid=<?php echo $this->Itemid; ?>">
                            <div class="js-ticket-dash-menu-icon">
                                <img class="js-ticket-dash-menu-img" src="<?php echo Uri::root() ?>components/com_jssupportticket/include/images/dashboard-icon/report.png" />
                            </div>
                            <span class="js-ticket-dash-menu-text"><?php echo Text::_('Ticket Status'); ?></span>
                        </a>
                    <?php } ?>
                </div>
                <div class="js-ticket-menu-links-row">
                    <?php if($this->config['cplink_userdata_user'] == 1){ ?>
                        <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=gdpr&layout=adderasedatarequest&Itemid=<?php echo $this->Itemid; ?>">
                            <div class="js-ticket-dash-menu-icon">
                                <img class="js-ticket-dash-menu-img" src="<?php echo Uri::root() ?>components/com_jssupportticket/include/images/dashboard-icon/user-data.png" />
                            </div>
                            <span class="js-ticket-dash-menu-text"><?php echo Text::_('User Data'); ?></span>
                        </a>
                    <?php } ?>
                    <?php $redirect = Route::_(Uri::root()."index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=" . $this->Itemid , false);
                    $redirect = '&amp;return=' . getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($redirect);
                    if(!$this->userticketstats){ ?>
                        <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="<?php echo 'index.php?option=com_users&view=login' . $redirect; ?>">
                            <div class="js-ticket-dash-menu-icon">
                                <img class="js-ticket-dash-menu-img" src="<?php echo Uri::root() ?>components/com_jssupportticket/include/images/dashboard-icon/login.png" />
                            </div>
                            <span class="js-ticket-dash-menu-text"><?php echo Text::_('Log In'); ?></span>
                        </a>
                    <?php }else{ ?>
                        <?php $link = "index.php?option=com_jssupportticket&c=jssupportticket&task=logout&return=".$redirect."&Itemid=" . $this->Itemid;?>
                        <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="<?php echo $link; ?>">
                            <div class="js-ticket-dash-menu-icon">
                                <img class="js-ticket-dash-menu-img" src="<?php echo Uri::root() ?>components/com_jssupportticket/include/images/dashboard-icon/logout.png" />
                            </div>
                            <span class="js-ticket-dash-menu-text"><?php echo Text::_('Log Out'); ?></span>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="js-cp-right">
        <!-- if user loged in -->
        <?php if(isset($this->userticketstats) && $this->userticketstats){
            $openticket = 0;
            $closedticket = 0;
            $answeredticket = 0;
            if($this->userticketstats['allticket'] > 0){
                $openticket = getJSTicketPHPFunctionsClass()->jsticket_round($this->userticketstats['openticket'] / $this->userticketstats['allticket'] * 100);
                $closedticket = getJSTicketPHPFunctionsClass()->jsticket_round($this->userticketstats['closedticket'] / $this->userticketstats['allticket'] * 100);
                $answeredticket = getJSTicketPHPFunctionsClass()->jsticket_round($this->userticketstats['answeredticket'] / $this->userticketstats['allticket'] * 100);
            }
            if($this->userticketstats['allticket'] != 0){
                $allticket = 100;
            }else{
                $allticket = 0;
            }
            ?>
            <div class="js-ticket-count">
                <div class="js-ticket-link js-ticket-open">
                    <a class="js-ticket-link" href="#" data-tab-number="1" title="<?php echo Text::_("Open Ticket"); ?>">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $openticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-link-text">
                            <?php echo Text::_("Open") . " ( " . $this->userticketstats['openticket'] . " )"; ?>
                        </div>
                    </a>
                </div>
                <div class="js-ticket-link js-ticket-close">
                    <a class="js-ticket-link" href="#" data-tab-number="2" title="<?php echo ("closed ticket"); ?>">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $closedticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-link-text">
                            <?php echo Text::_("Closed") . " ( " . $this->userticketstats['closedticket'] . " )"; ?>
                        </div>
                    </a>
                </div>
                <div class="js-ticket-link js-ticket-answer">
                    <a class="js-ticket-link" href="#" data-tab-number="3" title="<?php  echo Text::_("answered ticket"); ?>">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $answeredticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-link-text js-ticket-brown">
                            <?php echo Text::_("Answered") . " ( " . $this->userticketstats['answeredticket'] . " )"; ?>
                        </div>
                    </a>
                </div>
                <div class="js-ticket-link js-ticket-allticket">
                    <a class="js-ticket-link" href="#" data-tab-number="4" title="<?php echo Text::_("All ticket"); ?>">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $allticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-link-text">
                            <?php echo Text::_("All Tickets") . " ( " . $this->userticketstats['allticket'] . " )"; ?>
                        </div>
                    </a>
                </div>
            </div>
        <?php }else{ ?>
            <!-- if user not loged in -->
            <div class="js-support-ticket-cont">
                <div class="js-support-ticket-box">
                    <img src="<?php echo Uri::root() ?>components/com_jssupportticket/include/images/dashboard-icon/add-ticket.png" alt="Create Ticket">
                    <div class="js-support-ticket-title">
                        <?php echo Text::_("Submit Ticket"); ?>
                    </div>
                    <div class="js-support-ticket-desc">
                        <?php echo Text::_("New").' '.Text::_("Ticket"); ?>
                    </div>
                    <a href="index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid=<?php echo $this->Itemid; ?>" class="js-support-ticket-btn">
                        <?php echo Text::_("Submit Ticket"); ?>
                    </a>
                </div>
                <div class="js-support-ticket-box">
                    <img src="<?php echo Uri::root() ?>components/com_jssupportticket/include/images/dashboard-icon/my-tickets.png" alt="my ticket">
                    <div class="js-support-ticket-title">
                        <?php echo Text::_("My Tickets"); ?>
                    </div>
                    <div class="js-support-ticket-desc">
                        <?php echo Text::_("View all the created tickets"); ?>
                    </div>
                    <a href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>" class="js-support-ticket-btn">
                        <?php echo Text::_("My Tickets"); ?>
                    </a>
                </div>
                <div class="js-support-ticket-box">
                    <img src="<?php echo Uri::root() ?>components/com_jssupportticket/include/images/dashboard-icon/ticket-status.png" alt="Ticket Status" />
                    <div class="js-support-ticket-title">
                        <?php echo Text::_("Ticket Status"); ?>
                    </div>
                    <div class="js-support-ticket-desc">
                        <?php echo Text::_("Check Status"); ?>
                    </div>
                    <a href="index.php?option=com_jssupportticket&c=ticket&layout=ticketstatus&Itemid=<?php echo $this->Itemid; ?>" class="js-support-ticket-btn">
                        <?php echo Text::_("View").' '.Text::_("Status"); ?>
                    </a>
                </div>
            </div>
        <?php } ?>
        <!-- latest user tickets -->
        <?php if (!empty($this->latest_tickets)) { ?>
            <div class="js-ticket-latest-ticket-wrapper">
                <div class="js-ticket-haeder">
                    <div class="js-ticket-header-txt"><?php echo Text::_("Latest Tickets"); ?></div>
                    <a class="js-ticket-header-link" href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>"><?php echo Text::_("View All Tickets"); ?></a>
                </div>
                <div class="js-ticket-latest-tickets-wrp">
                    <?php foreach ($this->latest_tickets AS $ticket):?>
                        <div class="js-ticket-row">
                            <div class="js-ticket-first-left">
                                <div class="js-ticket-user-img-wrp">
                                    <img alt="<?php echo Text::_("User image"); ?>" src="components/com_jssupportticket/include/images/user.png" class="avatar avatar-96 photo" height="96" width="96">
                                </div>
                                <div class="js-ticket-ticket-subject">
                                    <div class="js-ticket-data-row">
                                        <?php echo Text::_($ticket->name); ?>
                                    </div>
                                    <div class="js-ticket-data-row name">
                                        <?php $link = 'index.php?option=' . $this->option . '&c=ticket&layout=ticketdetail&id='.$ticket->ticketid.'&Itemid='.$this->Itemid; ?>
                                        <a class="js-ticket-data-link" href="<?php echo $link; ?>"><?php echo Text::_($ticket->subject); ?></a>
                                    </div>
                                    <div class="js-ticket-data-row">
                                        <span class="js-ticket-title"><?php echo Text::_("Department"); ?> : </span>
                                        <?php echo Text::_($ticket->departmentname); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="js-ticket-second-left">
                                <?php if ($ticket->status == 0) { ?>
                                    <span class="js-ticket-status" style="color: #9ACC00;"><?php echo Text::_('New'); ?></span>
                                <?php } elseif ($ticket->status == 1) { ?>
                                    <span class="js-ticket-status" style="color: orange;"><?php echo Text::_('Waiting reply'); ?></span>
                                <?php } elseif ($ticket->status == 2) { ?>
                                    <span class="js-ticket-status" style="color: #FF7F50;"><?php echo Text::_('In progress'); ?></span>
                                <?php } elseif ($ticket->status == 3) { ?>
                                    <span class="js-ticket-status" style="color: #507DE4;"><?php echo Text::_('Replied'); ?></span>
                                <?php } elseif ($ticket->status == 4) { ?>
                                    <span class="js-ticket-status" style="color: #CB5355;"><?php echo Text::_('Close'); ?></span>
                                <?php } elseif ($ticket->status == 5){ ?>
                                    <span class="js-ticket-status" style="color: #ee1e22;"><?php echo Text::_('Close due to Merge'); ?></span>
                                <?php } ?>
                                </span>
                            </div>
                            <div class="js-ticket-third-left"><?php echo HTMLHelper::_('date',$ticket->created,"d F, Y"); ?></div>
                            <div class="js-ticket-fourth-left">
                                <span class="js-tk-priorty" style="background:<?php echo $ticket->prioritycolour; ?>;color:#fff;"><?php echo Text::_($ticket->priority); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php } ?>
        <div id="js-tk-copyright">
            <div class="js-tk-copyright-logo-wrapper">
                <img src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a>
            </div>
            <div class="js-tk-copyright-desc-wrapper">
                &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="http://www.burujsolutions.com">Buruj Solutions</a>
            </div>
        </div>
    </div>
    <?php
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>
