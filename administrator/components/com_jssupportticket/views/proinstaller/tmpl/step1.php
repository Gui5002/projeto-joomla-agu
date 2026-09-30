<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 * Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Project:     JS Tickets
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

$joomlaMajor = (int) getJSTicketPHPFunctionsClass()->jsticket_explode('.', JVERSION)[0];
$minimumPhpVersion = $joomlaMajor >= 6 ? '8.3.0' : ($joomlaMajor >= 5 ? '8.1.0' : '7.2.5');
$phpOk       = version_compare(PHP_VERSION, $minimumPhpVersion, '>=');
$curlExtOk   = isset($this->result['curlexist']) && $this->result['curlexist'] == 1;
$curlSslOk   = isset($this->result['curlssl']) && $this->result['curlssl'] == 1;
$curlOk      = $curlExtOk && $curlSslOk;
$zipOk       = isset($this->result['ziplib']) && $this->result['ziplib'] == 1;
$adminDirOk  = isset($this->result['admin_dir']) && $this->result['admin_dir'] >= 755;
$siteDirOk   = isset($this->result['site_dir']) && $this->result['site_dir'] >= 755;
$tmpDirOk    = isset($this->result['tmp_dir']) && $this->result['tmp_dir'] >= 755;
$createOk    = isset($this->result['create_table']) && $this->result['create_table'] == 1;
$insertOk    = isset($this->result['insert_record']) && $this->result['insert_record'] == 1;
$updateOk    = isset($this->result['update_record']) && $this->result['update_record'] == 1;
$deleteOk    = isset($this->result['delete_record']) && $this->result['delete_record'] == 1;
$dropOk      = isset($this->result['drop_table']) && $this->result['drop_table'] == 1;
$fileOk      = isset($this->result['file_downloaded']) && $this->result['file_downloaded'] == 1;
$permissionsOk = $adminDirOk && $siteDirOk && $tmpDirOk;
$databaseOk = $createOk && $insertOk && $updateOk && $deleteOk && $dropOk;
$canStart = $phpOk && $curlOk && $zipOk && $permissionsOk && $databaseOk && $fileOk;
?>

<script language="Javascript">
    function confirmdelete() {
        if (confirm("<?php echo Text::_('Are you sure to delete'); ?>") == true) {
            return true;
        } else {
            return false;
        }
    }
</script>
<div id="js-tk-admin-wrapper" class="jsst-screen jsst-proinstaller-step1">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
        $jsstPageTitle = 'JS Support Ticket Pro Installer';
        $jsstBreadcrumb = array(
        array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
        array('label_raw' => Text::_('JS Support Ticket Pro Installer'), 'link' => null),
        );
        include_once('components/com_jssupportticket/views/partials/pageheader.php');
        ?>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow jsst-proinstaller-card">
            <div style="display:none;" id="jsjob_installer_waiting_div"></div>
            <span style="display:none;" id="jsjob_installer_waiting_span"><?php echo Text::_('Please wait installation in progress'); ?></span>
            <div id="jsst-main-wrapper" class="jsst-proinstaller-v2">
                <div id="jsst-lower-wrapper">
                    <div class="jsst_installer_wrapper" id="jsst-installer_id">
                        <div class="jsst-proinstaller-v2__intro">
                            <div class="jsst-proinstaller-v2__logo">
                                <img alt="<?php echo htmlspecialchars(Text::_('JS Support Ticket'), ENT_QUOTES, 'UTF-8'); ?>" src="components/com_jssupportticket/include/images/jsst-support-icon-v26-128.png">
                            </div>
                            <span class="jsst-proinstaller-v2__eyebrow"><?php echo Text::_('Pro Installer'); ?></span>
                            <h2><?php echo Text::_('Activate JS Support Ticket Pro'); ?></h2>
                            <p><?php echo Text::_('Enter your activation key to continue the Pro installation. The installer will keep the same checks and installation flow, with a cleaner admin interface.'); ?></p>
                        </div>

                        <form id="proinstaller_form" action="index.php" method="post" class="jsst-proinstaller-v2__form">
                            <div class="jsst-proinstaller-v2__content" id="jsst_middle">
                                <div class="jsst-proinstaller-v2__activation">
                                    <label for="transactionkey"><?php echo Text::_('Activation Key'); ?></label>
                                    <div class="jsst_form_field_wrp">
                                        <div class="jsst_bg_overlay">
                                            <input type="text" name="transactionkey" id="transactionkey" class="jsst_key_field" value="" placeholder="<?php echo Text::_('Please Insert Your Activation Key'); ?>" />
                                        </div>
                                    </div>
                                    <?php if (isset($this->response)) { ?>
                                        <div id="invalid_activation_key" class="jsst_error_messages">
                                            <span class="jsst_msg"><?php echo $this->response; ?></span>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="jsst-proinstaller-v2__checks" aria-label="<?php echo Text::_('Server Checks'); ?>">
                                    <div class="jsst-proinstaller-v2__checks-head">
                                        <span><?php echo Text::_('Environment Check'); ?></span>
                                        <strong><?php echo $canStart ? Text::_('Ready') : Text::_('Needs Attention'); ?></strong>
                                    </div>
                                    <p class="jsst-proinstaller-v2__check-note"><?php echo Text::_('These checks confirm the server can activate the license, download the Pro package, extract files, write component folders, and update the database.'); ?></p>
                                    <div class="jsst-proinstaller-v2__check-grid">
                                        <div class="jsst-proinstaller-v2__check <?php echo $phpOk ? 'is-ok' : 'is-error'; ?>"><span></span><div class="jsst-proinstaller-v2__check-text"><strong><?php echo Text::_('PHP Version'); ?></strong><small><?php echo Text::_('Required'); ?> <?php echo $minimumPhpVersion; ?>+ · <?php echo Text::_('Current'); ?> <?php echo PHP_VERSION; ?></small></div></div>
                                        <div class="jsst-proinstaller-v2__check <?php echo $curlOk ? 'is-ok' : 'is-error'; ?>"><span></span><div class="jsst-proinstaller-v2__check-text"><strong><?php echo Text::_('cURL + SSL'); ?></strong><small><?php echo Text::_('Activation and package download'); ?></small></div></div>
                                        <div class="jsst-proinstaller-v2__check <?php echo $zipOk ? 'is-ok' : 'is-error'; ?>"><span></span><div class="jsst-proinstaller-v2__check-text"><strong><?php echo Text::_('ZIP / zlib'); ?></strong><small><?php echo Text::_('Extract Pro installer package'); ?></small></div></div>
                                        <div class="jsst-proinstaller-v2__check <?php echo $permissionsOk ? 'is-ok' : 'is-error'; ?>"><span></span><div class="jsst-proinstaller-v2__check-text"><strong><?php echo Text::_('Writable Folders'); ?></strong><small><?php echo Text::_('Admin, site, and tmp folders'); ?></small></div></div>
                                        <div class="jsst-proinstaller-v2__check <?php echo $databaseOk ? 'is-ok' : 'is-error'; ?>"><span></span><div class="jsst-proinstaller-v2__check-text"><strong><?php echo Text::_('Database Rights'); ?></strong><small><?php echo Text::_('Create, insert, update, delete, drop'); ?></small></div></div>
                                        <div class="jsst-proinstaller-v2__check <?php echo $fileOk ? 'is-ok' : 'is-error'; ?>"><span></span><div class="jsst-proinstaller-v2__check-text"><strong><?php echo Text::_('Package Download'); ?></strong><small><?php echo Text::_('Server outbound download test'); ?></small></div></div>
                                    </div>
                                </div>

                                <div class="jsst-proinstaller-v2__messages">
                                    <?php if (!$phpOk) { ?>
                                        <div class="jsst_error_messages"><span class="jsst_msg"><?php echo Text::_('PHP version is lower than the required version for this Joomla installation'); ?></span></div>
                                    <?php } ?>
                                    <?php if (!$curlOk) { ?>
                                        <div class="jsst_error_messages"><span class="jsst_msg"><?php echo Text::_('cURL with SSL support is required for activation and package download'); ?></span></div>
                                    <?php } ?>
                                    <?php if (!$zipOk) { ?>
                                        <div class="jsst_error_messages"><span class="jsst_msg"><?php echo Text::_('ZIP extraction support is required. Enable Joomla Archive, ZipArchive, zlib, or the bundled PclZip fallback'); ?></span></div>
                                    <?php } ?>
                                    <?php if (!$permissionsOk) { ?>
                                        <div class="jsst_error_messages">
                                            <span class="jsst_msg"><?php echo Text::_('Directory permissions error'); ?></span>
                                            <?php if(!$adminDirOk){ ?><span class="jsst_msg">"<?php echo JPATH_ROOT . "/administrator/components/com_jssupportticket"; ?>"&nbsp;<?php echo Text::_('is not writeable'); ?></span><?php } ?>
                                            <?php if(!$siteDirOk){ ?><span class="jsst_msg">"<?php echo JPATH_ROOT . "/components/com_jssupportticket"; ?>"&nbsp;<?php echo Text::_('is not writeable'); ?></span><?php } ?>
                                            <?php if(!$tmpDirOk){ ?><span class="jsst_msg">"<?php echo JPATH_ROOT . "/tmp"; ?>"&nbsp;<?php echo Text::_('is not writeable'); ?></span><?php } ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (!$createOk) { ?><div class="jsst_error_messages"><span class="jsst_msg"><?php echo Text::_('Database create table not allowed'); ?></span></div><?php } ?>
                                    <?php if (!$insertOk) { ?><div class="jsst_error_messages"><span class="jsst_msg"><?php echo Text::_('Database insert record not allowed'); ?></span></div><?php } ?>
                                    <?php if (!$updateOk) { ?><div class="jsst_error_messages"><span class="jsst_msg"><?php echo Text::_('Database update record not allowed'); ?></span></div><?php } ?>
                                    <?php if (!$deleteOk) { ?><div class="jsst_error_messages"><span class="jsst_msg"><?php echo Text::_('Database delete record not allowed'); ?></span></div><?php } ?>
                                    <?php if (!$dropOk) { ?><div class="jsst_error_messages"><span class="jsst_msg"><?php echo Text::_('Database drop table not allowed'); ?></span></div><?php } ?>
                                    <?php if (!$fileOk) { ?><div class="jsst_error_messages"><span class="jsst_msg"><?php echo Text::_('Server could not complete the outbound package download test'); ?></span></div><?php } ?>
                                </div>
                            </div>

                            <?php if ($canStart) { ?>
                                <div class="jsst_bottom jsst-proinstaller-v2__bottom">
                                    <div class="jsst_submit_btn">
                                        <button type="submit" id="startpress" class="jsst_btn" role="submit"><?php echo Text::_('Start'); ?></button>
                                    </div>
                                </div>
                            <?php } ?>

                            <input type="hidden" name="productcode" id="productcode" value="<?php echo isset($this->config['productcode']) ? $this->config['productcode'] : 'jssupportticket'; ?>" />
                            <input type="hidden" name="productversion" id="productversion" value="<?php echo isset($this->config['version']) ? $this->config['version'] : '100'; ?>" />
                            <input type="hidden" name="producttype" id="producttype" value="<?php echo isset($this->config['producttype']) ? $this->config['producttype'] : $this->config['versiontype']; ?>" />
                            <input type="hidden" name="domain" id="domain" value="<?php echo Uri::root(); ?>" />
                            <input type="hidden" name="JVERSION" id="JVERSION" value="<?php echo JVERSION; ?>" />
                            <input type="hidden" name="config_count" id="config_count" value="<?php echo $this->config_count; ?>" />
                            <input type="hidden" name="c" value="proinstaller">
                            <input type="hidden" name="layout" value="step1">
                            <input type="hidden" name="task" value="getversionlist">
                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        </form>
                        <div id="jsst_next_form" style="display: none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
