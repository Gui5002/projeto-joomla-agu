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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.tooltip');

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
global $mainframe;
?>
<script src="components/com_jssupportticket/include/js/jquery.js"></script>
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
                        <li><?php echo Text::_('System Errors'); ?></li>
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
                    <span class="jsstadmin-ver"><?php
                        $this->version = $this->getJSModel('config')->getConfigByFor('version'); $version = str_split($this->version['version']);
                        $version = implode('.', $version);
                        echo $version;?></span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo Text::_('System Errors'); ?></h1>      
        </div>
        <div >
            <?php
            if (!(empty($this->systemerrors)) && is_array($this->systemerrors)) {  ?>
                    <table class="jsstadmin-data-wrp" id="js-table" class="js-ticket-box-shadow">
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
                                    <td><a href="<?php echo $editlink;?>"><?php if ($error->staffname != '') echo $error->staffname; else echo Text::_('User'); ?></a></td>
                                    <td><?php $err = getJSTicketPHPFunctionsClass()->jsticket_substr($error->error, 0, 50) . '...'; if ($error->isview == 0) echo '<b>'; ?> <a href="<?php echo $editlink; ?>"><?php echo $err; ?></a><?php if ($error->isview == 0) echo '</b>'; ?></td>
                                    <td><?php if ($error->isview == 1) echo Text::_('JYes'); else echo Text::_('JNo'); ?></td>
                                    <td><?php Text::_('Created');echo " : "; ?></span><?php echo $error->created; ?></td>
                                </tr>
                                <?php
                                $i++;
                                $k++;
                            } ?>
                        </tbody>
                    </table>
                <div class="js-pagination-row js-tk-pagination">
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
