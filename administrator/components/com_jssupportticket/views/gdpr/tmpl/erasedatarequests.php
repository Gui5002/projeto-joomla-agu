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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
?>

<script language=Javascript>
    function confirmdelete() {
        if (confirm("<?php echo Text::_('Are you sure to delete'); ?>") == true) {
            return true;
        } else
            return false;
    }
    jQuery(document).ready(function(){
        jQuery('a.js-tk-button').tooltip();
    });
</script>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-list">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'Erase Data Requests';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('User').' '.Text::_('Erase Data Requests'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
            <form action="index.php" class="jsstadmin-data-wrp" method="post" name="adminForm" id="adminForm">
                <div id="js-tk-filter">
                    <div class="tk-search-value"><input type="text" name="filter_email" id="filter_email" placeholder="<?php echo Text::_('User Email') ?>" value="<?php if (isset($this->searchemail)) echo $this->searchemail; ?>" class="text_area"/></div>
                    <div class="tk-search-button">
                        <button type="submit" class="jsst-search"><?php echo Text::_('Search'); ?></button>
                        <button type="button" class="jsst-reset" onclick="resetJsForm();this.form.submit();"><?php echo Text::_('Reset'); ?></button>
                    </div>
                </div>
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="c" value="gdpr" />
                <input type="hidden" name="layout" value="erasedatarequests" />
                <?php
            if (!(empty($this->result)) && is_array($this->result)) {  ?>
                    <div class="jsst-gdpr-table-scroll" role="region" aria-label="<?php echo htmlspecialchars(Text::_('Erase Data Requests'), ENT_QUOTES, 'UTF-8'); ?>" tabindex="0">
                    <table id="js-table" class="js-ticket-box-shadow jsst-gdpr-erase-table">
                        <thead>
                        <tr>
                            <th class="center"><?php echo Text::_("S.No"); ?></th>
                            <th><?php echo Text::_("Subject"); ?></th>
                            <th><?php echo Text::_("Message"); ?></th>
                            <th class="center"><?php echo Text::_("Email"); ?></th>
                            <th class="center"><?php echo Text::_("Request Status"); ?></th>
                            <th class="center"><?php echo Text::_("Created"); ?></th>
                            <th class="center"><?php echo Text::_("Action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $k = 0;
                            foreach ($this->result AS $request) { ?>
                                <tr>
                                    <td class="center"><?php echo $k + 1 + $this->pagination->limitstart; ?></td>
                                    <td><?php echo Text::_($request->subject); ?></td>
                                    <td><?php echo Text::_($request->message); ?></td>
                                    <td class="center"><?php echo $request->email; ?></td>
                                    <td class="center">
                                      <?php if($request->status == 1){
                                          echo Text::_('Awaiting response');
                                      }elseif($request->status == 2){
                                        echo Text::_('Erased identifying data');
                                      }else{
                                        echo  Text::_('Deleted');
                                      }?>
                                    </td>
                                    <td class="center"><?php echo date($this->config['date_format'], getJSTicketPHPFunctionsClass()->jsticket_strtotime($request->created)); ?></td>
                                    <td class="center jsst-gdpr-action-cell">
                                        <div class="jsst-gdpr-action-stack">
                                            <a class="js-tk-button jsst-gdpr-action-button" onclick="return confirmdelete()" href="index.php?option=com_jssupportticket&c=gdpr&task=eraseidentifyinguserdata&id=<?php echo $request->uid; ?>&<?php echo Factory::getSession()->getFormToken(); ?>=1" data-toggle="tooltip" title="<?php echo Text::_("All the data belongs to this user will replace with dummy text"); ?>">
                                              <?php echo Text::_('Erase identifying data');?>
                                            </a>
                                            <a class="js-tk-button jsst-gdpr-action-button" onclick="return confirmdelete()" href="index.php?option=com_jssupportticket&c=gdpr&task=deleteuserdata&id=<?php echo $request->uid; ?>&<?php echo Factory::getSession()->getFormToken(); ?>=1" data-toggle="tooltip" title="<?php echo Text::_("All the data belongs to this user will be deleted"); ?>">
                                              <?php echo Text::_('Delete data');?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                                $k++;
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
            </form>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
<script type="text/javascript">
    function resetJsForm(){
        var form = jQuery('form#adminForm');
        form.find("input[type=text], input[type=email], input[type=password], textarea").val("");
    }
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
