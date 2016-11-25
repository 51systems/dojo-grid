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
    InitProviderInterface,
    ViewHelperProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                // the array key here is the name you will call the view helper by in your view scripts
                'dojoGrid' => function($sm) {
                    return new \DojoGrid\View\Helper\Grid();
                },
            ),
        );
    }

    /**
     * @inheritdoc
     */
    public function init(ModuleManagerInterface $manager)
    {
        $manager->loadModule('Dext');
    }
}