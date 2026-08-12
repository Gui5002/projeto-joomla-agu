<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>

<script language=Javascript>
    function confirmdelete() {
        if (confirm("<?php echo Text::_('Are you sure to delete'); ?>") == true) {
            return true;
        } else
            return false;
    }
</script>
<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="js-tk-heading">
            <h4><img id="js-admin-responsive-menu-link" src="components/com_jssupportticket/include/images/c_p/left-icons/menu.png" /><?php echo Text::_('User Fields'); ?></h4>
            <?php $link = 'index.php?option='.$this->option.'&c=userfields&task=adduserfield&ff=1'; ?>
            <a class="tk-heading-addbutton" href="<?php echo $link; ?>">
                <img class="js-heading-addimage" src="components/com_jssupportticket/include/images/add-btn.png">
                <?php echo Text::_('Add User Field'); ?>
            </a>            
        </div>
        <form action="index.php" method="post" name="adminForm" id="adminForm">
            <div id="js-tk-filter">
                <div class="tk-search-value"><input type="text" placeholder="<?php echo Text::_('Title'); ?>" name="filter_fieldtitle" id="filter_fieldtitle" size="15" value="<?php if (isset($this->filter_fieldtitle)) echo $this->filter_fieldtitle; ?>" class="text_area"/></div>
                <div class="tk-search-button">
                    <button onclick="this.form.submit();"><?php echo Text::_('Search'); ?></button>
                    <button onclick="document.getElementById('filter_fieldtitle').value = ''; this.form.submit();"><?php echo Text::_('Reset'); ?></button>
                </div>
            </div>
            <?php
            if (!(empty($this->items)) && is_array($this->items)) {  ?>
                 <div class="js-col-md-12">
                    <table id="js-table" class="js-ticket-box-shadow">
                        <thead>
                        <tr>
                            <th class="center"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
                            <th><?php echo Text::_("Field Name"); ?></th>
                            <th><?php echo Text::_("Field Title"); ?></th>
                            <th><?php echo Text::_("Field Type"); ?></th>
                            <th><?php echo Text::_("Read only"); ?></th>
                            <th><?php echo Text::_("Action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($this->items AS $row) {
                                $checked = HTMLHelper::_('grid.id', $i, $row->id);
                                if($row->required == 1) $icon_required = 'tick-icon.png'; else $icon_required = 'close-icon.png';
                                if($row->readonly == 1) $icon_readonly = 'tick-icon.png'; else $icon_readonly = 'close-icon.png';
                                $editlink = 'index.php?option='.$this->option.'&c=userfields&task=adduserfield&cid[]=' . $row->id;
                                $deletelink = 'index.php?option='.$this->option.'&c=userfields&task=removeuserfields&cid[]='.$row->id.'&' . Factory::getSession()->getFormToken() . '=1'; ?>
                                <tr>
                                    <td class="center"><?php echo $checked; ?></td>
                                    <td><a href="<?php echo $editlink;?>"><?php echo $row->name; ?></a></td>
                                    <td><?php echo $row->title; ?></td>
                                    <td><?php echo $row->type; ?></td>
                                    <td><img src="components/com_jssupportticket/include/images/<?php echo $icon_readonly; ?>"></td>
                                    <td class="center">
                                        <a class="js-tk-button" href="<?php echo $editlink; ?>">
                                            <img src="components/com_jssupportticket/include/images/edit_small.png">                     
                                        </a>&nbsp;
                                        <a class="js-tk-button" onclick="return confirmdelete()" href="<?php echo $deletelink; ?>">
                                            <img src="components/com_jssupportticket/include/images/deletes.png">
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div class="js-row js-tk-pagination js-ticket-pagination-shadow">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
            <?php 
            }else{
                messagesLayout::getRecordNotFound();
            } ?>
            <input type="hidden" name="option" value="<?php echo $this->option; ?>"/>
            <input type="hidden" name="c" value="userfields"/>
            <input type="hidden" name="layout" value="userfields"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
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
