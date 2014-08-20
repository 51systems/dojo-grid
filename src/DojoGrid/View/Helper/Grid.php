<?php

namespace DojoGrid\View\Helper;

use Dojo\View\Exception\RuntimeException;
use Zend\View\Helper\AbstractHelper;

/**
 * Dojo Grid View Helper.
 *
 *
 * @author Dustin Thomson <dthomson@51systems.com>
 */
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
     * The dojo module identifer for the grid.
     */
    const MODULE_KEY = 'dojoGrid';

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
    public function __invoke($id=null, array $params = array(), array $attribs = array())
    {
        /**
         * @var $dojo \Dojo\View\Helper\Configuration
         * Note that this is actually a \Dojo\View\Helper\Dojo object that we proxy to configuration.
         */
        $dojo = $this->view->dojo();
        if (!$this->_styleSheetsAdded) {
            $dojo->addStylesheet(\Dojo\View\Helper\Configuration::DOJO_PATH_TOKEN . '/dojox/grid/resources/Grid.css');
            $dojo->addStylesheet(\Dojo\View\Helper\Configuration::DOJO_PATH_TOKEN . '/dojox/grid/resources/claroGrid.css');
            $dojo->addStylesheet(\Dojo\View\Helper\Configuration::DOJO_PATH_TOKEN . '/dojox/grid/enhanced/resources/EnhancedGrid.css');

            $this->_styleSheetsAdded = true;
        }

        if (!array_key_exists(self::MODULE_KEY, $dojo->getPackagePaths())) {
            throw new RuntimeException("Dojo grid package has not been registered");
        }

        if(!isset($attribs['height'])){
            $attribs['height'] = '15em';
        }

        $helper =  new GridContainer($id, $params, $attribs);
        $helper->setView($this->view);
        return $helper;
    }
}
