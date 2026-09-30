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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
$document = Factory::getDocument();
global $mainframe;
?>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-list">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'System Errors';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('System Errors'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <div >
            <?php
            if (!(empty($this->systemerrors)) && is_array($this->systemerrors)) {  ?>
                    <table class="jsstadmin-data-wrp js-ticket-box-shadow" id="js-table">
                        <thead>
                        <tr>
                            <th class="center"><?php echo Text::_("S.No"); ?></th>
                            <th><?php echo Text::_("Name"); ?></th>
                            <th><?php echo Text::_("Error"); ?></th>
                            <th><?php echo Text::_("View"); ?></th>
                            <th><?php echo Text::_("Created"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $k = 0;
                            foreach ($this->systemerrors AS $error) {
                                $checked = HTMLHelper::_('grid.id', $i, $error->id);
                                $editlink = 'index.php?option=' . $this->option . '&c=systemerrors&task=showerror&cid=' . $error->id;
                                if($error->isview == 1) $icon_status = 'tick-icon.png'; else $icon_status = 'close-icon.png'; ?>
                                <tr>
                                    <td class="center"><?php echo $k + 1 + $this->pagination->limitstart; ?></td>
                                    <td><a href="<?php echo $editlink;?>"><?php echo Text::_('User'); ?></a></td>
                                    <td><?php $err = getJSTicketPHPFunctionsClass()->jsticket_substr((string) $error->error, 0, 90) . '...'; if ($error->isview == 0) echo '<b>'; ?> <a href="<?php echo $editlink; ?>"><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></a><?php if ($error->isview == 0) echo '</b>'; ?></td>
                                    <td><?php if ($error->isview == 1) echo Text::_('JYES'); else echo Text::_('JNO'); ?></td>
                                    <td><?php Text::_('Created');echo " : "; ?></span><?php echo $error->created; ?></td>
                                </tr>
                                <?php
                                $i++;
                                $k++;
                            } ?>
                        </tbody>
                    </table>
                <div class="js-pagination-row js-tk-pagination js-ticket-pagination-shadow">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
            <?php 
            }else{ ?>
                <div id="jsstadmin-data-wrp">
                    <?php messagesLayout::getRecordNotFound(); ?>
                
                </div>
            <?php } ?>
        </div>
            <input type="hidden" name="c" value="systemerrors" />
            <input type="hidden" name="layout" value="systemerrors" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
<script type="text/javascript">
    var headertext = [],
    headers = document.querySelectorAll("#js-table th"),
    tablebody = document.querySelector("#js-table tbody");

    if (tablebody) {
      for(var i = 0; i < headers.length; i++) {
        var current = headers[i];
        headertext.push(current.textContent.replace(/\r?\n|\r/,""));
      }
      for (var i = 0, row; row = tablebody.rows[i]; i++) {
        for (var j = 0, col; col = row.cells[j]; j++) {
          col.setAttribute("data-th", headertext[j]);
        }
      }
    }
</script>
