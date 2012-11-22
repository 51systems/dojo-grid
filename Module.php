<?php

namespace DojoGrid;

/**
 *
 */
class Module
{

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
}