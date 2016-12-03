<?php

namespace DojoGrid;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

/**
 *
 */
class Module implements
    ConfigProviderInterface,
    InitProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @inheritdoc
     */
    public function init(ModuleManagerInterface $manager)
    {
        $manager->loadModule('Dext');
    }
}