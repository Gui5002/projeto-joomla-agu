<?php
/**
 * @Copyright Copyright (C) 2009-2011
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
  + Created by:	Ahmad Bilal
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	Jan 11, 2009
  ^
  + Project: 		JS Jobs
 * File Name:	admin-----/views/applications/tmpl/users.php
  ^
 * Description: Template for users view
  ^
 * History:		NONE
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;

use Joomla\CMS\Version;
$version = new Version;
$joomla = $version->getShortVersion();
if (getJSTicketPHPFunctionsClass()->jsticket_substr($joomla, 0, 3) != '1.5') {
    HTMLHelper::_('bootstrap.tooltip');
    HTMLHelper::_('behavior.multiselect');
}
?>

<script type="text/javascript">
    function resetFrom() {
        document.getElementById('title').value = '';
        document.getElementById('categoryid').value = '';
        document.getElementById('type').value = '';
        document.getElementById('jssupportticketform').submit();
    }
    jQuery(document).ready(function () {
        jQuery("a#userpopup").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopupblack").show();
            var f = jQuery(this).attr('data-id');
            var link = "index.php?option=com_jssupportticket&c=userfields&task=getOptionsForFieldEdit&<?php echo Factory::getSession()->getFormToken(); ?>=1";
            jQuery.post(link, { field:f }, function (data) {
                if(data){    
                    var abc = jQuery.parseJSON(data)                
                    jQuery("div#userpopup").html("");
                    jQuery("div#userpopup").html(abc);
                }
            });
            jQuery("div#userpopup").slideDown('slow');
        });
        jQuery("span.close, div#userpopupblack").click(function (e) {
            jQuery("div#userpopup").slideUp('slow', function () {
                jQuery("div#userpopupblack").hide();
            });

        });
    });
    function close_popup(){
        jQuery("div#userpopup").slideUp('slow', function () {
            jQuery("div#userpopupblack").hide();
        });
    }
</script>

<div id="userpopupblack" style="display:none;"></div>
<div id="userpopup" style="display:none;">
</div>


<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo Text::_('Dashboard'); ?></a></li>
                        <li>
                            <?php
                                echo Text::_('Ticket Fields');
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="jsstadmin-wrapper-top-right">
                <div id="jsstadmin-config-btn">
                    <a title="Configuration" href="index.php?option=com_jssupportticket&c=config&layout=config">
                        <img alt="Configuration" src="components/com_jssupportticket/include/images/config.png">
                    </a>
                </div>
                <div id="jsstadmin-vers-txt">
                    <?php echo Text::_('Version').Text::_(' : '); ?>
                    <span class="jsstadmin-ver">
                        <?php $version = str_split($this->version);
                        $version = implode('.', $version);
                        echo $version; ?>
                    </span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php
                echo Text::_('Ticket Fields');
            ?></h1>
            <?php $link = 'index.php?option='.$this->option.'&c=userfields&task=adduserfield&ff='.$_SESSION['ffusr']; ?>
            <a class="tk-heading-addbutton" href="<?php echo $link; ?>">
                <img class="js-heading-addimage" src="components/com_jssupportticket/include/images/plus.png">
                <?php
                    echo Text::_('Add Ticket').' '.Text::_('Field');
                ?>
            </a>
        </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm">
            <?php
            if (!(empty($this->fields)) && is_array($this->fields)) {  ?>
                    <table id="js-table" class="js-ticket-box-shadow">
                        <thead>
                        <tr>
                            <th style="display:none;" class="center"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                            <th class="center"><?php echo Text::_('S.No'); ?></th>
                            <th><?php echo Text::_('Field Title'); ?></th>
                            <th class="center"><?php echo Text::_('Published'); ?></th>
                            <th class="center"><?php echo Text::_('Required'); ?></th>
                            <th class="center"><?php echo Text::_('Ordering'); ?></th>
                            <th class="center"><?php echo Text::_('Action'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $k = 0;
                        $i = 0;
                        $uptask = 'fieldorderingup';
                        $upimg = 'uparrow.png';
                        $downtask = 'fieldorderingdown';
                        $downimg = 'downarrow.png';
                        $n = getJSTicketPHPFunctionsClass()->jsticket_count($this->fields);
                        foreach ($this->fields AS $row) {
                            $checked = HTMLHelper::_('grid.id', $k, $row->id);
                            $pubtask = $row->published ? 'fieldunpublished' : 'fieldpublished';
                            $reqtask = $row->required ? 'fieldnotrequired' : 'fieldrequired';
                            $pubimg = ($row->published == 0) ? 'close.png' : 'good.png';
                            if($row->userfieldtype == 'termsandconditions'){
                                $reqimg = 'good.png';
                            }else{
                                $reqimg = ($row->required == 0) ? 'close.png' : 'good.png';
                            }
                            $alt = $row->published ? Text::_('Published') : Text::_('Unpublished');
                            $reqalt = $row->required ? Text::_('Required') : Text::_('Not required');
                            ?>
                            <tr>
                                <td style="display:none;" class="center"><?php echo $checked; ?></td>
                                <td class="center"><?php echo $k + 1 + $this->pagination->limitstart; ?></td>
                                <td>
                                    <?php 
                                        if ($row->fieldtitle) {
                                            echo Text::_($row->fieldtitle);
                                        }else{
                                            echo "";
                                        }
                                            
                                        if($row->cannotunpublish == 1){
                                            echo '<font style="color:#1C6288;font-size:20px;margin:0px 5px;">*</font>';
                                        }
                                    ?>
                                </td>
                                <td class="center">
                                    <?php 
                                          if(JVERSION < 4){
                                              $token = Factory::getSession()->getFormToken();
                                          }else{
                                              $token = Session::getFormToken();
                                          }
                                        if ($row->cannotunpublish == 1) { ?>
                                            <img src="components/com_jssupportticket/include/images/<?php echo $pubimg; ?>" width="16" height="16" border="0" title="<?php echo Text::_('Can Not Unpublished'); ?>" />
                                        <?php } else { ?>
                                            <?php $status_link = OutputFilter::ampReplace('index.php?option='.$this->option.'&task=userfields.'.$pubtask.'&cid[]='.$row->id.'&'.$token.'=1'); ?>
                                            <a href="<?php echo $status_link;?>">
                                                <img src="components/com_jssupportticket/include/images/<?php echo $pubimg; ?>" width="16" height="16" border="0" title="<?php echo $alt; ?>" />
                                            </a>
                                    <?php } ?>
                                </td>
                                <td class="center">
                                    <?php 
                                        if($row->cannotunpublish == 1 || $row->userfieldtype == 'termsandconditions'){ ?>
                                            <img src="components/com_jssupportticket/include/images/<?php echo $reqimg; ?>" width="16" height="16" border="0" title="<?php echo Text::_('Can Not mark as not required'); ?>" />
                                    <?php }else{ ?>
                                            <?php $status_link = OutputFilter::ampReplace('index.php?option='.$this->option.'&task=userfields.'.$reqtask.'&cid[]='.$row->id.'&'.$token.'=1'); ?>
                                            <a href="<?php echo $status_link;?>">
                                                <img src="components/com_jssupportticket/include/images/<?php echo $reqimg; ?>" width="16" height="16" border="0" title="<?php echo $reqalt; ?>" />
                                            </a>
                                    <?php } ?>
                                </td>
                                <td class="center">
                                    <?php if ($k != 0) { ?>
                                        <a href="index.php?option=com_jssupportticket&c=common&task=userfields.<?php echo $downtask; ?>&cid[]=<?php echo $row->id; ?>&<?php echo $token; ?>=1">
                                            <img src="components/com_jssupportticket/include/images/<?php echo $upimg; ?>" alt="<?php echo Text::_('Order Up');?>" /></a>
                                        </a> 
                                    <?php } else echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        echo $row->ordering; ?>&nbsp;&nbsp; 
                                    <?php if ($k < $n - 1) { ?> 
                                        <a href="index.php?option=com_jssupportticket&c=common&task=userfields.<?php echo $uptask; ?>&cid[]=<?php echo $row->id; ?>&<?php echo $token; ?>=1">
                                            <img src="components/com_jssupportticket/include/images/<?php echo $downimg; ?>" alt="<?php echo Text::_('Order Down');?>" /></a>
                                        </a> 
                                    <?php } ?>
                                </td>
                                <td class="center">
                                    <?php
                                        if($row->isuserfield == 1){
                                            echo '<a class="action-btn" id="userpopup" data-id='.$row->id.'><img src="components/com_jssupportticket/include/images/edit.png" /></a>';
                                            echo '<a class="action-btn" onclick="return confirm(\''.Text::_('Are you sure to delete').'\');" href="index.php?option=com_jssupportticket&c=userfields&task=removeuserfields&cid[]='.$row->id.'&' . Factory::getSession()->getFormToken() .'=1"><img src="components/com_jssupportticket/include/images/delete.png" /></a>';
                                        }else{
                                            echo '<a class="action-btn" id="userpopup" data-id='.$row->id.'><img src="components/com_jssupportticket/include/images/edit.png" /></a>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php
                            $k++;
                        } ?>
                        </tbody>
                    </table>
                <div class="js-row js-tk-pagination js-ticket-pagination-shadow">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
            <?php 
            }else{
                messagesLayout::getRecordNotFound();
            } ?>
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="task" value="view" />
            <input type="hidden" name="c" value="userfields" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="filter_order" value="<?php echo (isset($this->lists) && isset($this->lists['order'])) ? $this->lists['order'] : ''; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo (isset($this->lists) && isset($this->lists['order_Dir'])) ? $this->lists['order_Dir'] : ''; ?>" />
            <input type="hidden" name="layout" value="fieldsordering" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
<script type="text/javascript">
    var headertext = [],
    headers = document.querySelectorAll("#js-table th"),
    tablerows = document.querySelectorAll("#js-table th"),
    tablebody = document.querySelector("#js-table tbody");

    for(var i = 0; i < headers.length; i++) {
      var current = headers[i];
      headertext.push(current.textContent.replace(/\r?\n|\r/,""));
    } 
    for (var i = 0, row; row = tablebody.rows[i]; i++) {
      for (var j = 0, col; col = row.cells[j]; j++) {
        col.setAttribute("data-th", headertext[j]);
      } 
    }
</script>
