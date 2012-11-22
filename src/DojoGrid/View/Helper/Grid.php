<?php

namespace DojoGrid\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Grid extends AbstractHelper
{
    /**
     * Drag and drop plugin.
     * Drag-and-drop support for rows/columns/cells, either within grid or out of grid.
     * @var string
     */
    const PLUGIN_DRAG_AND_DROP = 'DnD';

    /**
     * Nested sorting plugin.
     * Multiple column sorting
     * @var string
     */
    const PLUGIN_NESTED_SORTING = 'NestedSorting';

    /**
     * Indirect selection plugin.
     * Selecting rows with radio button or check box
     * @var string
     */
    const PLUGIN_INDIRECT_SELECTION = 'IndirectSelection';

    /**
     * Filter plugin.
     * Support for defining rules to filter grid content with various data types.
     * @var string
     */
    const PLUGIN_FILTER = 'Filter';

    /**
     * Flag to indicate that the grid stylesheets have been added
     * @var bool
     */
    protected $_styleSheetsAdded = false;

    /**
     * Creates a new grid instance
     *
     * @param string $id Grid ID
     * @param array[string][string] $params Dijit parameters
     * @param array[string][string] $attribs HTML attributes
     * @return GridContainer
     */
    public function grid($id=null, array $params = array(), array $attribs = array())
    {
        /**
         * @var $dojo \Dojo\View\Helper\Configuration
         * Note that this is actually a \Dojo\View\Helper\Dojo object that we proxy to configuration.
         */
        $dojo = $this->view->dojo();
        if(!$this->_styleSheetsAdded){
            $dojoPath = null;

            if($dojo->useLocalPath()){
                $dojoPath = $dojo->getLocalPath();
            }else{
                $dojoPath = $dojo->getCdnDojoPath();
            }

            //strip off the dojo.js from the path
            $pathInfo = pathinfo($dojoPath);
            $dojoPath = $pathInfo['dirname'];

            $dojo->addStylesheet($dojoPath . '/../dojox/grid/resources/Grid.css');
            $dojo->addStylesheet($dojoPath . '/../dojox/grid/resources/claroGrid.css');
            $dojo->addStylesheet($dojoPath . '/../dojox/grid/enhanced/resources/EnhancedGrid.css');

            $this->_styleSheetsAdded = true;
        }

        if(!isset($attribs['height'])){
            $attribs['height'] = '200px';
        }

        $helper =  new GridContainer($id, $params, $attribs);
        $helper->setView($this->view);
        return $helper;
    }
}
