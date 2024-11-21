<?php

/**
 * @package     Spsimpleportfolio.Site
 * @subpackage  Finder.Spsimpleportfolio
 *
 * @copyright   Copyright (C) 2023 - 2024 JoomShaper <https://www.joomshaper.com>. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\DI\Container;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\DispatcherInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Extension\PluginInterface;
use JoomShaper\Plugin\Finder\Spsimpleportfolio\Extension\Spsimpleportfolio;

return new class () implements ServiceProviderInterface {

    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $dispatcher = $container->get(DispatcherInterface::class);
                $plugin = new Spsimpleportfolio(
                    $dispatcher,
                    (array) PluginHelper::getPlugin('finder', 'spsimpleportfolio')
                );
                
                $plugin->setApplication(Factory::getApplication());
                $plugin->setDatabase($container->get(DatabaseInterface::class));

                return $plugin;
            }
        );
    }
};