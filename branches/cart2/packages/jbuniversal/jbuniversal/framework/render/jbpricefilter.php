<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBPriceFilterRenderer
 */
class JBPriceFilterRenderer extends PositionRenderer
{
    /**
     * @var JBCartElementHelper
     */
    protected $_jbcartelement;
    /**
     * @var JBCartPositionHelper
     */
    protected $_jbposition;

    /**
     * @var JBModelConfig
     */
    protected $_jbconfig;

    /**
     * @var ElementJBPriceAdvance
     */
    protected $_jbprice;

    /**
     * Joomla module params
     *
     * @var null
     */
    protected $_moduleParams = null;

    protected $_template = null;
    protected $_application = null;

    /**
     * @param App $app
     * @param null $path
     */
    public function __construct($app, $path = null)
    {
        parent::__construct($app, $path);

        $this->_jbposition  = $app->jbcartposition;
        $this->_cartelement = $app->jbcartelement;
        $this->_jbconfig    = JBModelConfig::model();
    }

    /**
     * @param string $position
     *
     * @return bool|void
     */
    public function checkPosition($position)
    {
        foreach ($this->_getConfigPosition($position) as $key => $data) {
            jbdump::dump($data);
            if ($element = $this->_jbprice->getParam($key)) {
                jbdump::dump($element);
                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $key;

                if ($element->canAccess() && $element->hasFilterValue($this->app->data->create($data))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $layout
     * @param array $args
     *
     * @return string
     */
    public function render($layout, $args = array())
    {
        $this->_jbprice     = $args['price'];
        $this->_template    = $args['template'];
        $this->_application = $args['app_id'];

        unset($args['price']);

        $result = null;
        $result .= parent::render('jbpricefilter.' . $layout, $args);

        return $result;
    }

    /**
     * @param string $position
     * @param array $args
     *
     * @return string|void
     */
    public function renderPosition($position = null, $args = array())
    {
        $output = array();

        // get style
        $style = isset($args['style']) ? $args['style'] : 'default';
        $i     = 0;

        foreach ($this->_getConfigPosition($position) as $key => $data) {

            if ($element = $this->_jbprice->getParam($key)) {

                $i++;

                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $key;

                // set params
                $params = array_merge(
                    array('item_template'       => $this->_template,
                          'item_application_id' => $this->_application,
                          'moduleParams'        => $this->_moduleParams
                    ), $data, $args);

                if (!$element->canAccess() && !$element->hasFilterValue($this->app->data->create($params))) {
                    continue;
                }

                $attrs = array(
                    'id'    => 'filterEl_' . $element->identifier,
                    'class' => array(
                        'element-' . strtolower($element->getElementType())
                    )
                );

                $value       = $this->_getRequest($element->identifier);
                $elementHTML = $this->elementRender($element, $value, $params, $attrs);

                if (empty($elementHTML)) {
                    continue;
                }

                if ($style) {

                    $output[$i] = parent::render('element.jbpricefilter.' . $style, array(
                            'element'     => $element,
                            'params'      => $params,
                            'attrs'       => $attrs,
                            'value'       => $value,
                            'config'      => $element->config,
                            'elementHTML' => $elementHTML
                        )
                    );
                } else {
                    $output[$i] = $elementHTML;
                }
            }
        }

        return implode("\n", $output);
    }

    /**
     * @param $position
     *
     * @return mixed
     */
    public function _getConfigPosition($position)
    {
        $config   = $this->_jbconfig->getGroup('cart.' . JBCart::CONFIG_PRICE_TMPL_FILTER);
        $position = $config->get($this->_jbprice->identifier . '.' . $this->_layout . '.' . $position);

        return isset($position) ? $position : array();
    }

    /**
     * Element render
     *
     * @param string $element
     * @param bool $value
     * @param array $params
     * @param array $attrs
     *
     * @return mixed
     * @throws Exception
     */
    public function elementRender($element, $value = false, $params = array(), $attrs = array())
    {
        $elementType = $element->getElementType();
        $render      = $this->_getRender($params, $elementType);

        $params['jbzoo_original_type']    = $elementType;
        $params['jbzoo_is_original_type'] = ($elementType == $render);

        $renderPaths   = explode('-', $render);
        $className     = 'JBPriceFilterElement';
        $classFilename = 'element';

        $this->app->loader->register($className, 'renderer:/pricefilter/' . $classFilename . '.php');
        foreach ($renderPaths as $renderPath) {

            $className .= $renderPath;
            $classFilename .= '.' . $renderPath;

            $this->app->loader->register($className, 'renderer:/pricefilter/' . $classFilename . '.php');

            if (!class_exists($className)) {
                throw new Exception('Unkown class render "' . $className . '"');
            }
        }

        $render = new $className($element, $value, $params, $attrs);

        if ($render->hasValue()) {
            return $render->html();
        }

        return null;
    }

    /**
     * Mapper elementType to render method
     *
     * @param array $params
     * @param string $elementType
     *
     * @return string
     */
    private function _getRender(array $params, $elementType)
    {
        if (!isset($params['jbzoo_filter_render'])) {
            $params['jbzoo_filter_render'] = '_auto_';
        }

        if ($params['jbzoo_filter_render'] == '_auto_') {

            switch ($elementType) {

                case 'radio':
                    $renderMethod = 'radio';
                    break;

                case 'checkbox':
                    $renderMethod = 'checkbox';
                    break;

                case 'color':
                    $renderMethod = 'jbcolor';
                    break;

                case 'select':
                    $renderMethod = 'select';
                    break;

                case 'country':
                    $renderMethod = 'country-select';
                    break;

                case 'itemcreated':
                case 'itemmodified':
                case 'itempublish_down':
                case 'itempublish_up':
                case 'date':
                    $renderMethod = 'date-range';
                    break;

                case 'itemauthor':
                    $renderMethod = 'author';
                    break;

                case 'itemcategory':
                    $renderMethod = 'category';
                    break;

                case 'itemfrontpage':
                    $renderMethod = 'frontpage';
                    break;

                case 'itemtag':
                    $renderMethod = 'tag-checkbox';
                    break;

                case 'rating':
                    $renderMethod = 'rating-slider';
                    break;

                case 'jbselectcascade':
                    $renderMethod = 'jbselectcascade';
                    break;

                case 'text':
                case 'textarea':
                case 'itemname':
                default :
                    $renderMethod = 'text';
                    break;
            }

        } else {
            $renderMethod = $params['jbzoo_filter_render'];
        }

        return $renderMethod;
    }

    /**
     * Get element request
     *
     * @param $identifier
     *
     * @return null|array|string
     */
    private function _getRequest($identifier)
    {
        $value = $this->app->jbrequest->get($identifier);

        if (!$value) {

            $id = $this->_jbprice->identifier;

            $elements = $this->app->jbrequest->get('e');

            $elements = $this->app->data->create($elements);

            $param = $this->_jbprice->getParam($identifier);

            if ($param->isCore() && $elements->has($id)) {

                return $elements->get($id, array());

            } else {

                return $elements->find($id . '.params.' . $identifier);
            }

        }

        return $value;
    }

    /**
     * Set Joomla module params
     *
     * @param $params
     *
     * @return $this
     */
    public function setModuleParams($params)
    {
        $this->_moduleParams = $params;

        return $this;
    }

    /**
     * @param string $dir
     *
     * @return array
     */
    public function getLayouts($dir)
    {
        // init vars
        $layoutList = array();
        $parts      = explode('.', $dir);
        $path       = implode('/', $parts);

        // parse positions xml
        if ($xml = simplexml_load_file($this->_getPath($path . '/' . $this->_xml_file))) {

            $layouts = $xml->xpath('positions[@layout]');

            foreach ($layouts as $layout) {
                $name = (string)$layout->attributes()->layout;

                $layoutList[$name] = $name;
            }

        }

        return $layoutList;
    }

}
