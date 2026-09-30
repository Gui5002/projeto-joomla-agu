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
use Joomla\CMS\Version;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\OutputFilter;

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
        jQuery("a.jsst-userpopup-trigger").click(function (e) {
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


<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-list">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = ($_SESSION['ffusr'] == 1) ? 'Ticket Fields' : 'Feedback Fields';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label' => $jsstPageTitle, 'link' => null),
);
$jsstAddLink = 'index.php?option='.$this->option.'&c=userfields&task=adduserfield&ff='.$_SESSION['ffusr'];
$jsstAddLabelRaw = ($_SESSION['ffusr'] == 1)
    ? Text::_('Add Ticket').' '.Text::_('Field')
    : Text::_('Add').' '.Text::_('Feedback').' '.Text::_('Field');
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
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
                            <th class="center"><?php echo Text::_('Visitor Published'). ' *'; ?></th>
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
                            $vpubtask = $row->isvisitorpublished ? 'visitorfieldunpublished' : 'visitorfieldpublished';
                            $reqtask = $row->required ? 'fieldnotrequired' : 'fieldrequired';
                            $pubimg = ($row->published == 0) ? 'close.png' : 'good.png';
                            $vpubimg = ($row->isvisitorpublished == 0) ? 'close.png' : 'good.png';
                            if($row->userfieldtype == 'termsandconditions'){
                                $reqimg = 'good.png';
                            }else{
                                $reqimg = ($row->required == 0) ? 'close.png' : 'good.png';
                            }
                            $alt = $row->published ? Text::_('Published') : Text::_('Unpublished');
                            $valt = $row->isvisitorpublished ? Text::_('Published') : Text::_('Unpublished');
                            $reqalt = $row->required ? Text::_('Required') : Text::_('Not required'); ?>
                            <tr>
                                <td style="display:none;" class="center"><?php echo $checked; ?></td>
                                <td class="center"><?php echo $k + 1 + $this->pagination->limitstart; ?></td>
                                <td>
                                    <?php 
                                        if ($row->fieldtitle) {
                                            echo Text::_($row->fieldtitle);
                                        }else{
                                            echo Text::_($row->userfieldtitle);
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
                                        if ($row->cannotunpublish == 1) { ?>
                                            <img src="components/com_jssupportticket/include/images/<?php echo $vpubimg; ?>" width="16" height="16" border="0" title="<?php echo Text::_('Can Not Unpublished'); ?>" />
                                        <?php } else { ?>
                                            <?php $status_link = OutputFilter::ampReplace('index.php?option='.$this->option.'&task=userfields.'.$vpubtask.'&cid[]='.$row->id.'&'.$token.'=1'); ?>
                                            <a href="<?php echo $status_link;?>">
                                                <img src="components/com_jssupportticket/include/images/<?php echo $vpubimg; ?>" width="16" height="16" border="0" title="<?php echo $valt; ?>" />
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
                                            <img src="components/com_jssupportticket/include/images/<?php echo $upimg; ?>" alt="<?php echo Text::_('Order Up');?>" />
                                        </a> 
                                    <?php } else echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        echo $row->ordering; ?>&nbsp;&nbsp; 
                                    <?php if ($k < $n - 1) { ?> 
                                        <a href="index.php?option=com_jssupportticket&c=common&task=userfields.<?php echo $uptask; ?>&cid[]=<?php echo $row->id; ?>&<?php echo $token; ?>=1">
                                            <img src="components/com_jssupportticket/include/images/<?php echo $downimg; ?>" alt="<?php echo Text::_('Order Down');?>" />
                                        </a> 
                                    <?php } ?>
                                </td>
                                <td class="center">
                                    <span class="jsst-field-action-group">
                                    <?php
                                        $edit_label = htmlspecialchars(Text::_('Edit'), ENT_QUOTES, 'UTF-8');
                                        echo '<a href="#" class="action-btn jsst-userpopup-trigger jsst-field-action jsst-field-edit-action" data-id="'.(int) $row->id.'" title="'.$edit_label.'" aria-label="'.$edit_label.'"><img alt="" src="components/com_jssupportticket/include/images/edit.png" /></a>';
                                        if($row->isuserfield == 1){
                                            $delete_label = htmlspecialchars(Text::_('Delete'), ENT_QUOTES, 'UTF-8');
                                            echo '<a class="action-btn jsst-field-action jsst-field-delete-action" title="'.$delete_label.'" aria-label="'.$delete_label.'" onclick="return confirm(\''.Text::_('Are you sure to delete').'\');" href="index.php?option=com_jssupportticket&c=userfields&task=removeuserfields&cid[]='.(int) $row->id.'&' . Factory::getSession()->getFormToken() .'=1"><img alt="" src="components/com_jssupportticket/include/images/delete.png" /></a>';
                                        }
                                    ?>
                                    </span>
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
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
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
