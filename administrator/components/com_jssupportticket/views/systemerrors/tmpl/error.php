<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Project:    JS Tickets
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
$document = Factory::getDocument();
$errorText = isset($this->error->error) ? (string) $this->error->error : '';
?>
<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-list">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'System Error Detail';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label' => 'System Errors', 'link' => 'index.php?option=com_jssupportticket&c=systemerrors&layout=systemerrors'),
    array('label_raw' => Text::_('Error Detail'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <div id="jsstadmin-data-wrp" class="js-ticket-pagination-shadow">
            <div class="jsst-error-detail-card">
                <div class="jsst-error-detail-head">
                    <strong><?php echo Text::_('Captured Error'); ?></strong>
                    <a href="index.php?option=com_jssupportticket&c=systemerrors&layout=systemerrors"><?php echo Text::_('Back to System Errors'); ?></a>
                </div>
                <div class="jsst-error-meta">
                    <div class="jsst-error-meta-item">
                        <strong><?php echo Text::_('From'); ?></strong>
                        <span><?php if (!empty($this->error->staffname)) echo htmlspecialchars($this->error->staffname, ENT_QUOTES, 'UTF-8'); else echo Text::_('User'); ?></span>
                    </div>
                    <div class="jsst-error-meta-item">
                        <strong><?php echo Text::_('Date'); ?></strong>
                        <span><?php echo HTMLHelper::_('date', $this->error->created, $this->config['date_format']); ?></span>
                    </div>
                    <div class="jsst-error-meta-item">
                        <strong><?php echo Text::_('Viewed'); ?></strong>
                        <span><?php if ($this->error->isview == 1) echo Text::_('JYES'); else echo Text::_('JNO'); ?></span>
                    </div>
                </div>
                <div class="jsst-error-raw">
                    <strong><?php echo Text::_('Technical Detail'); ?></strong>
                    <pre class="jsst-error-pre"><?php echo htmlspecialchars($errorText, ENT_QUOTES, 'UTF-8'); ?></pre>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
