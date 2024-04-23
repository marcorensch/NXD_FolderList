<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.nxdfolderlist
 *
 * @copyright   (C) 2024 NXD | nx-designs Marco Rensch <https://www.nx-designs.ch>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use NXD\Plugin\Fields\NXDFolderList\Extension\NXDFolderList;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.3.0
     */
    public function register(Container $container): void
    {
        FormHelper::addFieldPrefix('NXD\\Plugin\\Fields\\NXDFolderList\\Field');

        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $dispatcher = $container->get(DispatcherInterface::class);
                $plugin     = new NXDFolderList(
                    $dispatcher,
                    (array) PluginHelper::getPlugin('fields', 'nxdfolderlist')
                );
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
