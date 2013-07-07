<?php

namespace DojoGrid;

use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\Mvc\MvcEvent;

/**
 *
 */
class Module implements DependencyIndicatorInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        /** @var $as \AsseticBundle\Service */
        $as = $serviceManager->get('AsseticService');

        //Register the dext javascript files with assetic
        $assetManager = $as->getAssetManager();

        //Register the javascript assets
        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
        \Dext\AsseticHelper::registerDir($assetManager, $rootDir);

        /** @var $view Zend\View\RendererInterface */
        $view = $serviceManager->get('viewmanager')->getRenderer();

        /**
         * @var $dojo \Dojo\View\Helper\Configuration
         * Note that this is actually a \Dojo\View\Helper\Dojo object that we proxy to configuration.
         */
        $dojo = $view->plugin('dojo');
        $baseUrl = rtrim($as->getConfiguration()->getBaseUrl() . $as->getConfiguration()->getBasePath(), '/');

        $dojo->registerPackagePath('dojoGrid', $baseUrl . '/js/dojoGrid');
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
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies()
    {
        return array('Dojo', 'Dext');
    }
}