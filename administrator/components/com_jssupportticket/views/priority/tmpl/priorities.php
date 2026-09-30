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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/jsst-admin-workspace-v2.css?v=61');
?>
<script language=Javascript>
    function confirmdelete() {
        if (confirm("<?php echo Text::_('Are you sure to delete'); ?>") == true) {
            return true;
        } else
            return false;
    }
</script>

<div id="js-tk-admin-wrapper" class="jsst-management jsst-management-priority jsst-screen jsst-screen-list">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'Priorities';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('Priorities'), 'link' => null),
);
$jsstAddLink = 'index.php?option='.$this->option.'&c=priority&task=addnewpriority&cid[]=""';
$jsstAddLabel = 'Add Priority';
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm">
            <div id="js-tk-filter">
                <div class="tk-search-value"><input type="text" placeholder="<?php echo Text::_('Title'); ?>" name="filter_priority" id="filter_priority" size="15" value="<?php if (isset($this->searchpriority)) echo $this->searchpriority; ?>" class="text_area"/></div>
                <div class="tk-search-button">
                    <button class="js-form-search" onclick="this.form.submit();"><?php echo Text::_('Search'); ?></button>
                    <button class="js-form-reset" onclick="document.getElementById('filter_priority').value = ''; this.form.submit();"><?php echo Text::_('Reset'); ?></button>
                </div>
            </div>
            <?php
            if (!(empty($this->priority)) && is_array($this->priority)) {  ?>
                    <table id="js-table" class="js-ticket-box-shadow">
                        <thead>
                        <tr>
                            <th class="center"><?php echo Text::_("S.No"); ?></th>
                            <th><?php echo Text::_("Title"); ?></th>
                            <th class="center"><?php echo Text::_("Public"); ?></th>
                            <th class="center"><?php echo Text::_("Default"); ?></th>
                            <th class="center"><?php echo Text::_("Color"); ?></th>
                            <th class="center"><?php echo Text::_("Action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $k = 0;
                            foreach ($this->priority AS $row) {
                                $checked = HTMLHelper::_('grid.id', $i, $row->id);
                                if($row->ispublic == 1) $icon_ispublic = 'good.png'; else $icon_ispublic = 'close.png';
                                if($row->isdefault == 1) $icon_isdefault = 'good.png'; else $icon_isdefault = 'close.png';
                                $editlink = 'index.php?option='.$this->option.'&c=priority&task=addnewpriority&cid[]=' . $row->id;
                                $deletelink = 'index.php?option='.$this->option.'&c=priority&task=deletepriority&cid[]='.$row->id.'&'. Factory::getSession()->getFormToken() .'=1'; ?>
                                <tr>
                                    <td class="center"><?php echo $k + 1 + $this->pagination->limitstart; ?></td>
                                    <td><a href="<?php echo $editlink;?>"><?php echo Text::_($row->priority); ?></a></td>
                                    <td class="center"><img src="components/com_jssupportticket/include/images/<?php echo $icon_ispublic; ?>"></td>
                                    <td class="center"><img src="components/com_jssupportticket/include/images/<?php echo $icon_isdefault; ?>"></td>
                                    <td class="center">
                                        <span class="js-ticket-admin-prirrity-color"  style="background:<?php echo $row->prioritycolour;?>;color:#FFFFFF;"><?php echo getJSTicketPHPFunctionsClass()->jsticket_strtoupper($row->prioritycolour); ?></span></td>
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
            <input type="hidden" name="c" value="priority"/>
            <input type="hidden" name="layout" value="priorities"/>
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
