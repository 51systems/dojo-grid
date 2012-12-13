<?php

namespace DojoGrid\View\Helper;

use \Zend\Filter\FilterChain;
use \DojoGrid\View\Exception\InvalidArgumentException;
use \Zend\Json\Json;
use Dojo\View\Helper\CustomDijit;

/**
* Grid view helper instance
*
* @author Dustin Thomson <dthomson@51systems.com>
 */
class GridContainer extends CustomDijit
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dojoGrid.widget.Grid';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dojoGrid.widget.Grid';

    /**
     * Id of the grid
     * @var string
     */
    protected $_id;

    /**
     * Dijit Parameters
     * @var array[string][string]
     */
    protected $_params;

    /**
     * Html Attributes
     * @var array[string][string]
     */
    protected $_attribs;

    /**
     * The REST endpoint URl
     * @var string
     */
    protected $_endpointUrl;

    /**
     * Contains the columns to display.
     * @var array[string][array]
     */
    protected $_columnLayout = array();

    /**
     * Flag to indicate the editing is enabled in the grid
     * @var bool
     */
    protected $_isEditable;

    /**
     * Plugins to be enabled
     * @var array[string]
     */
    protected $_plugins = array();

    /**
     * If a column name is not specified, formats the column name from the field.
     *
     * @var \Zend\Filter\FilterInterface
     */
    protected $_nameFormatter;

    /**
     * Creates a new grid view helper instance
     *
     * @param string $id Grid ID
     * @param array[string][string] $params Dijit parameters
     * @param array[string][string] $attribs HTML attributes
     */
    public function __construct($id, array $params = array(), array $attribs = array())
    {
        $this->_id = $id;
        $this->_params = $params;
        $this->_attribs = $attribs;

        //Setup the filter change for generating the name of columns
        $filterChain = new FilterChain();
        $filterChain
            ->attach(new \Zend\Filter\StringTrim())
            ->attach(new \Zend\Filter\Word\CamelCaseToSeparator())
            ->attach(new \Zend\Filter\Callback('ucwords'));

        $this->_nameFormatter = $filterChain;
    }

    /**
     * Adds a column to be displayed by the grid
     *
     * @param string $field The name of the field to display
     * @param array $properties
     * @param int $headerRow The row number of the header (Supports multiple-row headers)
     * @return GridContainer Fluid Interface
     */
    public function addColumn($field, array $properties = array(), $headerRow=0)
    {
        $properties['field'] = $field;

        if(!isset($properties['name'])){
            $properties['name'] = $this->_nameFormatter->filter($properties['field']);
        }

        if(isset($properties['readOnly'])) {
            if(isset($properties['editable'])) {
                throw new InvalidArgumentException('readOnly and editable attribute cannot both be set');
            }

            $properties['editable'] = ! (bool) $properties['readOnly'];
        }

        if(isset($properties['readOnlyOnNew'])) {
            if(isset($properties['editableOnNew'])) {
                throw new InvalidArgumentException('readOnlyOnNew and editableOnNew attribute cannot both be set');
            }

            $properties['editableOnNew'] = ! (bool) $properties['readOnlyOnNew'];
        }

        if(isset($properties['type']) && !($properties['type'] instanceof \Zend\Json\Expr)){
            $properties['type'] = new \Zend\Json\Expr($properties['type']);
        }

        if(!isset($this->_columnLayout[$headerRow])){
            $this->_columnLayout[$headerRow] = array();
        }

        $this->_columnLayout[$headerRow][] = $properties;

        return $this;
    }

    /**
     * Sets the query to pre-filter the grid results.
     * The query cannot be changed by the user.
     *
     * Eg: $queryObj = array('make' => 'ford') to return only ford autos.
     *
     * @param array|object $queryObj An associative array or object decribing the query.
     * @return GridContainer Fluid Interface
     */
    public function setQuery($queryObj)
    {
        $this->_params['query'] = $queryObj;

        return $this;
    }

    /**
     * Sets the Rest Endpoint URl
     * @param string $url
     * @return GridContainer Fluid Interface
     */
    public function setRestEndpointUrl($url)
    {
        $this->_endpointUrl = $url;
        return $this;
    }

    /**
     * If flag is set, sets the editable flag. If not, returns the current editable state
     *
     * @param bool $flag
     * @return GridContainer|bool
     */
    public function isEditable($flag=null)
    {
        if($flag === null){
            return $this->_isEditable;
        }

        $this->_isEditable = $flag;

        return $this;
    }

    /**
     * Includes and enables the specified plugin
     * @param string $plugin The name of the plugin
     * @param bool|array $params The plugin parameters
     *
     * @return GridContainer
     */
    public function enablePlugin($plugin, $params = true)
    {
        $this->view->dojo()->requireModule('dojox.grid.enhanced.plugins.' . $plugin);

        switch($plugin){

            case Grid::PLUGIN_DRAG_AND_DROP:
                $plugin = 'dnd';
                break;

            default:
                $plugin[0] = strtolower($plugin[0]);
        }

        $this->_plugins[$plugin] = $params;

        return $this;
    }

    /**
     * Render grid widget as string
     *
     * @return string
     */
    public function __toString()
    {
        $htmlString = '';

        //Setup the store
        $storeId = $this->_id . '_store';
        $htmlString .= $this->view->jsonRestStore($storeId, $this->_endpointUrl);

        //setup the grid

        //merge in the grid structure property
        $params = $this->_params;

        if(isset($params['query']) && !is_string($params['query'])) {
            $params['query'] = Json::encode($params['query'], false, array('enableJsonExprFinder' => true));
        }

        $structure = array(
            'defaultCell' => array(),
            'cells' => $this->_columnLayout,
        );

        foreach($structure['cells'] as &$cell) {
            if(isset($cell['editorParams'])) {
                $cell['editorParams'] = Json::encode($cell['editorParams'], false, array('enableJsonExprFinder' => true));
            }
        }

        if($this->_isEditable){
            //$this->view->dojo()->requireModule('dojox.grid.cells.dijit');
            //$this->view->dojo()->requireModule('dext.widget.grid.NewRowDialog');
            $structure['defaultCell']['editable'] = true;
        }

        $params['plugins'] = Json::encode($this->_plugins, false, array('enableJsonExprFinder' => true));

        $params['structure'] = Json::encode($structure, false, array('enableJsonExprFinder' => true));
        $params['store'] = $storeId;

        $params['columnReordering'] = true;
        $params['rowSelector'] = '20px';
        $params['editable'] = $this->_isEditable;

        $htmlString .= $this->_createLayoutContainer($this->_id, null, $params, $this->_attribs);

        return $htmlString;
    }
}