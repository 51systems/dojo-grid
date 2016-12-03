<?php

namespace PHPSTORM_META {
    override(\Zend\View\Renderer\PhpRenderer::plugin(0), map([
        'dojoGrid' => \DojoGrid\View\Helper\Grid::class,
    ]));
}