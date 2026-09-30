<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.jssupportticketicon
 *
 * @copyright   Copyright (C) 2015 - 2026 Joom Sky. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use JoomSky\Plugin\System\JSSupportTicketIcon\Extension\JSSupportTicketIcon;

return new class () implements ServiceProviderInterface {
    /**
     * Register the plugin service.
     *
     * @param   Container  $container  The dependency injection container.
     *
     * @return  void
     */
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            static function (Container $container): PluginInterface {
                $plugin = new JSSupportTicketIcon(
                    $container->get(DispatcherInterface::class),
                    (array) PluginHelper::getPlugin('system', 'jssupportticketicon')
                );

                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
