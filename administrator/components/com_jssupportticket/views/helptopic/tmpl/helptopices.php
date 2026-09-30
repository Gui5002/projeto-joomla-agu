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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>

<script language=Javascript>
    function confirmdelete() {
        if (confirm("<?php echo Text::_('Are you sure to delete'); ?>") == true) {
            return true;
        } else
            return false;
    }
</script>
<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-list">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'Help Topics';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('Help Topics'), 'link' => null),
);
$jsstAddLink = 'index.php?option='.$this->option.'&c=helptopic&task=edithelptopic';
$jsstAddLabel = 'Add Help Topic';
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm">
            <div id="js-tk-filter">
                <div class="tk-search-value"><input type="text" name="filter_ht_helptopic" placeholder="<?php echo Text::_('Help Topic'); ?>" id="filter_ht_helptopic" value="<?php if (isset($this->lists['helptopic'])) echo $this->lists['helptopic']; ?>"/></div>
                <div class="tk-search-value"><?php echo $this->lists['status']; ?></div>
                <div class="tk-search-button">
                    <button class="js-form-search" onclick="this.form.submit();"><?php echo Text::_('Search'); ?></button>
                    <button class="js-form-reset" onclick="document.getElementById('filter_ht_helptopic').value = ''; document.getElementById('filter_ht_statusid').value = ''; this.form.submit();"><?php echo Text::_('Reset'); ?></button>
                </div>
            </div>
            <?php
            if (!(empty($this->helptopic)) && is_array($this->helptopic)) {  ?>
                    <table id="js-table" class="js-ticket-box-shadow">
                        <thead>
                        <tr>
                            <th class="center"><?php echo Text::_("S.No"); ?></th>
                            <th><?php echo Text::_("Help Topic"); ?></th>
                            <th><?php echo Text::_("Department"); ?></th>
                            <?php /* <th><?php echo Text::_("Auto Response"); ?></th> */ ?>
                            <th class="center"><?php echo Text::_("Status"); ?></th>
                            <th class="center"><?php echo Text::_("Last Update"); ?></th>
                            <th class="center"><?php echo Text::_("Action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $k = 0;
                            foreach ($this->helptopic AS $row) {
                                $checked = HTMLHelper::_('grid.id', $i, $row->id);
                                if($row->status == 1) $icon_status = 'good.png'; else $icon_status = 'close.png';
                                $editlink = 'index.php?option=' . $this->option .'&c=helptopic&layout=formhelptopic&cid[]=' . $row->id;
                                $deletelink = 'index.php?option='.$this->option.'&c=helptopic&task=removehelptopic&cid[]='.$row->id.'&'. Factory::getSession()->getFormToken() .'=1'; ?>
                                <tr>
                                    <td class="center"><?php echo $k + 1 + $this->pagination->limitstart; ?></td></td>
                                    <td><a href="<?php echo $editlink;?>"><?php echo $row->topic; ?></a></td>
                                    <td><?php echo Text::_($row->departmentname); ?></td>
                                    <?php /* <td><?php if ($row->autoresponce == 1) echo "<font color='green'>" . Text::_('JYES') . "</font>"; else echo "<font color='red'>" . $row->autoresponce . "</font>"; ?></td> */ ?>
                                    <td class="center"><img src="components/com_jssupportticket/include/images/<?php echo $icon_status; ?>"></td>
                                    <td class="center"><?php if ($row->update == '0000-00-00 00:00:00' || $row->update == '' || $row->update == NULL) echo Text::_('Not updated'); else echo HTMLHelper::_('date',$row->update,$this->config['date_format']); ?></td>
                                    <td class="center">
                                        <a class="js-tk-button" href="<?php echo $editlink; ?>">
                                            <img src="components/com_jssupportticket/include/images/edit.png">                     
                                        </a>&nbsp;
                                        <a class="js-tk-button" onclick="return confirmdelete()" href="<?php echo $deletelink; ?>">
                                            <img src="components/com_jssupportticket/include/images/delete.png">
                                        </a>
                                    </td>
                                </tr>
                            <?php
                                $i++;
                                $k++;
                            } ?>
                        </tbody>
                    </table>
                <div class="js-tk-pagination js-ticket-pagination-shadow">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
            <?php 
            }else{
                messagesLayout::getRecordNotFound();
            } ?>
            <input type="hidden" name="option" value="<?php echo $this->option; ?>"/>
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>"/>
            <input type="hidden" name="c" value="helptopic"/>
            <input type="hidden" name="layout" value="helptopices"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
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
