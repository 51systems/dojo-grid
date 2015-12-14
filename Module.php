<?php

namespace DojoGrid;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Renderer\RendererInterface;

/**
 *
 */
class Module implements
    ConfigProviderInterface,
    InitProviderInterface,
    BootstrapListenerInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @param EventInterface|MvcEvent $e
     * @return array|void
     */
    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        /** @var $as \AsseticBundle\Service */
        $as = $serviceManager->get('AsseticService');

        //Register the dext javascript files with assetic
        $assetManager = $as->getAssetManager();

        //Register the javascript assets
        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
        \Dext\AsseticHelper::registerDir($assetManager, $rootDir);

        /** @var $view RendererInterface */
        $view = $serviceManager->get('viewmanager')->getRenderer();

        /**
         * @var $dojo \Dojo\Builder\Configuration
         * Note that this is actually a \Dojo\View\Helper\Dojo object that we proxy to configuration.
         */
        $dojo = $view->plugin('dojo');
        $baseUrl = rtrim($as->getConfiguration()->getBaseUrl() . $as->getConfiguration()->getBasePath(), '/');

        $dojo->getDojoConfig()->registerPackage('dojoGrid', $baseUrl . '/js/dojoGrid');
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