<?php
/**
 * @package     Bluecoder.TagFilter
 *
 * @copyright   Copyright © 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseDriver;

/**
 * Load the installer
 */
class Pkg_simple_tag_filterInstallerScript extends InstallerScript
{

    /**
     * The minimum PHP version required to install this extension
     *
     * @var   string
     */
    protected $minimumPhp = '7.4.0';

    /**
     * The minimum Joomla! version required to install this extension
     *
     * @var   string
     */
    protected $minimumJoomla = '5.1.0';

    protected $allowDowngrades = true;

    /**
     * Cache the extension objects from the database.
     *
     * @var array
     */
    protected $databaseExtension;

    protected $previousEdition;

    /**
     * @var bool
     */
    protected $isPro;

    /**
     * These files will be removed in the FREE version.
     *
     * @var string[]
     */
    protected array $proFiles = [
        'modules/mod_simple_tag_filter/tmpl/_dropdown.php',
    ];

    /**
     * The messages that going to print.
     *
     * @var array
     */
    protected $printed_messages = [];

    /**
     * A list of extensions (modules, plugins) to enable after installation. Each item has four values, in this order:
     * type (plugin, module, ...), name (of the extension), client (0=site, 1=admin), group (for plugins), edition (pro, free).
     *
     * @var array
     */
    protected array $extensionsToEnableOnInstall = [
        ['plugin', 'simpletagfilter', 0, 'system' , 'free']
    ];

    protected array $extensionsToEnableOnUpgrade = [];

    /**
     * Add messages to be displayed after installation, e.g. new features
     *
     * @var array
     */
    protected array $messages = [];

    /**
     * Preflight routine executed before installation and update
     *
     * @param        $type    string    type of change (install, update or discover_install)
     * @since        1.0.0
     */
    public function preflight($type, $parent)
    {

        if (!parent::preflight($type, $parent))
        {
            return false;
        }


        if ($type == 'update') {
            $milestone_versions = array_keys($this->messages);
            $this->printed_messages = [];
            $oldRelease = $this->getParam('version');
            [$type, $elementName, $client, $group, $edition] = $this->extensionsToEnableOnUpgrade[0];
            $this->previousEdition = $this->extensionsToEnableOnUpgrade && !empty($this->getInstalledExtension($elementName)) ? 'PRO' : 'FREE';
            foreach ($milestone_versions as $m_v) {
                if (version_compare($oldRelease, $m_v) == -1) {
                    $this->printed_messages[] = $this->messages[$m_v];
                }
            }
        }

        return true;
    }

    /**
     * Get an extension from the db
     *
     * @param   string  $extensionName
     *
     * @return array
     * @since 1.0.0
     */
    protected function getInstalledExtension(string $extensionName)
    {
        if (!isset($this->databaseExtension[$extensionName])) {
            /** @var DatabaseDriver $db */
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true);
            $query->select([$db->quoteName('client_id'), $db->quoteName('manifest_cache'), $db->quoteName('params')])
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('element') . '=' . $db->quote($extensionName));
            $db->setQuery($query);
            $this->databaseExtension[$extensionName] = $db->loadObject();
        }
        return $this->databaseExtension[$extensionName];
    }

    /**
     * Postflight routine executed after install and update
     *
     * @param        $type    string    type of change (install, update or discover_install)
     *
     * @since        1.0.0
     */
    public function postflight($type, $parent)
    {
        if ($type == 'install') {
            $this->enableExtensions();
        } elseif($type == 'update') {
            $this->enableExtensions('update');
        }

        if ($type == 'install' || $type == 'update') {
            $this->showMessage($type);
        }

        if (($type == 'install' || $type == 'update') && !$this->isPro()) {
            // Remove no pro files
            foreach ($this->proFiles as $file) {
                $fileName = JPATH_ROOT . '/' . $file;
                if (file_exists($fileName)) {
                    unlink($fileName);
                }
            }
        }
    }

    /**
     * Enable modules and plugins after installing them
     */
    private function enableExtensions($type = 'install')
    {
        $extensionsToActivate = [];
        if ($type == 'install') {
            $extensionsToActivate = $this->extensionsToEnableOnInstall;
        } elseif ($type == 'update' && $this->previousEdition != 'PRO') {
            $extensionsToActivate = $this->extensionsToEnableOnUpgrade;
        }

        foreach ($extensionsToActivate as $extension)
        {
            list($type, $name, $client, $group, $edition) = $extension;
            $this->enableExtension($type, $name, $client, $group, $edition);
        }
    }

    /**
     * Enable an extension
     *
     * @param   string   $type    The extension type.
     * @param   string   $name    The name of the extension (the element field).
     * @param   int      $client  The application id (0: Joomla CMS site; 1: Joomla CMS administrator).
     * @param   string   $group   The extension group (for plugins).
     * @param   string   $edition The extension edition ('pro', 'free').
     * @return  bool
     */
    private function enableExtension($type, $name, $client = 1, $group = null, $edition = 'free')
    {
        $extensionEditionIsPro = $this->isPro();

        // Do not enable PRO extensions. Possibly do not exist in the package.
        if (!$extensionEditionIsPro && $edition == 'pro') {
            return false;
        }

        try
        {
            /** @var DatabaseDriver $db */
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true)
                ->update('#__extensions')
                ->set($db->quoteName('enabled') . ' = ' . $db->quote(1))
                ->where('type = ' . $db->quote($type))
                ->where('element = ' . $db->quote($name));
        }
        catch (\Exception $e)
        {
            // Suck it
            return false;
        }


        switch ($type)
        {
            case 'plugin':
                // Plugins have a folder but not a client
                $query->where('folder = ' . $db->quote($group));
                break;

            case 'language':
            case 'module':

            case 'library':
            case 'package':
            case 'component':
            default:
                // Components, packages and libraries don't have a folder or client.
                // Included for completeness.
                break;
        }

        try
        {
            $db->setQuery($query);
            $db->execute();
        }
        catch (\Exception $e)
        {
            // Suck it
        }
        return true;
    }

    /*
     * @return bool
     */
    private function isPro() : bool
    {
        if ($this->isPro === null) {
            $extensionVars = @include JPATH_ROOT . '/modules/mod_simple_tag_filter/env.php';
            $extensionEditionIsPro = is_array($extensionVars) && isset($extensionVars['edition']) && $extensionVars['edition'] == 'PRO' ? true : false;
            $this->isPro = $extensionEditionIsPro;
        }

        return $this->isPro;
    }

    /**
     * Displays post installation messages
     *
     * @param $type
     */
    private function showMessage($type)
    {
        $language = Factory::getApplication()->getLanguage();
        $language->load('mod_simple_tag_filter');
        $language->load('com_config', JPATH_ADMINISTRATOR);

        ?>
        <div class="card p-3">
            <h2 class="card-subtitle" style="font-size: 1.6rem; font-weight:normal; color:#737373; ">
                <img style="height: 2.5rem; margin-inline-end: 0.5rem; vertical-align: bottom;" src="../media/mod_simple_tag_filter/images/logo.svg" alt="logo"/>Simple Tag Filter
            </h2>
            <p class="card-title mt-4 mb-4 ms-4"><?= Text::_('MOD_SIMPLE_TAG_FILTER_XML_DESCRIPTION')?></p>
            <p class="actions">
                <?php if($type == 'install') {
                    // Getting started guide link, in case of installation.
                    ?>
                    <a class="btn btn-primary mx-3 text-light" href="index.php?option=com_modules&view=modules&client_id=0">
                        <?= Text::_('MOD_SIMPLE_TAG_FILTER_OPEN_SITE_MODULES')?></a>
                    <a class="btn btn-light border-dark mx-3" href="https://docs.blue-coder.com/simpletagfilter" target="_blank" rel="noopener">
                        <?= Text::_('MOD_SIMPLE_TAG_FILTER_GETTING_STARTED_GUIDE')?> <span class="text-secondary"><span class="icon-clock"></span> 5' read</span></a>
                <?php }
                elseif($type == 'update') {
                    // Changelog link, in case of update.
                    ?>
                    <a class="btn btn-light border-dark mx-3" href="https://blue-coder.com/changelogs/simpletagfilter?utm_source=joomla&utm_medium=installation&utm_campaign=changelog" target="_blank" rel="noopener">
                        Changelog</a>
                <?php } ?>
            </p>
            <div class="position-absolute d-none d-xl-block" style="bottom:16%; margin-inline-start: 80%;">
                <div style="opacity: 0.5;" class="mb-2">Developed By</div>
                <img src="../media/mod_simple_tag_filter/images/bluecoder_logo_full.svg" alt="Bluecoder logo" style="height: 32px">
            </div>
        </div>
        <?php
        //if update messages
        if (!empty($this->printed_messages)) {
            $language->load('com_messages', JPATH_ADMINISTRATOR);
            ?>
            <div class="clr clearfix"></div>

            <h3><?= Text::_('COM_MESSAGES_READ'); ?></h3>
            <div id="system-message-container">
                <?php
                foreach ($this->printed_messages as $message) {?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php } ?>
            </div>
        <?php }
    }
}