<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBPriceRenderer
 */
class JBPriceRenderer extends PositionRenderer
{
    /**
     * @var int
     */
    protected $variant = 0;
    /**
     * @var JBCartVariant
     */
    protected $_variant = null;

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
     * @var ElementJBPricePlain || ElementJBPriceCalc
     */
    protected $_jbprice;

    /**
     * @var string
     */
    protected $_priceLayout;

    /**
     * @param App  $app
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
        foreach ($this->getConfigPosition($position) as $key => $data) {

            if (($element = $this->_variant->getElement($key)) ||
                ($element = $this->_jbprice->getElement($key, $this->variant))
            ) {

                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $key;

                if ($element->canAccess() && $element->hasValue($this->app->data->create($data))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $layout
     * @param array  $args
     *
     * @return string
     */
    public function render($layout, $args = array())
    {
        $this->variant  = isset($args['variant']) ? $args['variant'] : null;
        $this->_variant = isset($args['_variant']) ? $args['_variant'] : null;

        $this->_jbprice     = isset($args['price']) ? $args['price'] : null;
        $this->_priceLayout = isset($args['_layout']) ? $args['_layout'] : null;

        unset($args['_variant']);
        unset($args['price']);

        $result = '';
        $result .= parent::render('jbprice.' . $layout, $args);

        return $result;
    }

    /**
     * @param array $args
     *
     * @return string
     */
    public function renderEditPositions($args = array())
    {
        // init vars
        $elements = array();
        $output   = array();

        // get style
        $style  = $this->_variant->isBasic() ? 'basic' : 'variations';
        $config = $this->_getConfig();

        if (!empty($config)) {
            foreach ($config as $key => $data) {

                if (($element = $this->_variant->getElement($key)) ||
                    ($element = $this->_jbprice->getElement($key, $this->variant))
                ) {

                    if ($this->_variant->isBasic() && $element->getMetaData('core') != 'true') {
                        continue;
                    }

                    $data['_price_layout'] = $this->_priceLayout;

                    $data['_layout'] = $this->_layout;
                    $data['_index']  = $key;

                    $params = array_merge($data, $args);

                    $elements[] = compact('element', 'params');
                }
            }
        }

        if (!empty($elements)) {

            foreach ($elements as $i => $data) {
                $params = array_merge(array('first' => ($i == 0),
                                            'last'  => ($i == count($elements) - 1)), $data['params']);

                $output[$i] = parent::render('element.jbprice.' . $style, array(
                    'element' => $data['element'],
                    'params'  => $this->app->data->create($params)
                ));
            }
        }

        return implode("\n", $output);
    }

    /**
     * @param string $position
     * @param array  $args
     *
     * @return string|void
     */
    public function renderPosition($position = null, $args = array())
    {
        // init vars
        $elements = array();
        $output   = array();

        // get style
        $style = isset($args['style']) ? 'jbprice.' . $args['style'] : 'jbprice.default';

        // store layout
        $layout = $this->_layout;
        foreach ($this->getConfigPosition($position) as $key => $data) {

            if (($element = $this->_variant->getElement($key)) ||
                ($element = $this->_jbprice->getElement($key, $this->variant))
            ) {

                if (!$element->canAccess()) {
                    continue;
                }

                $data['_price_layout'] = $this->_priceLayout;

                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $key;

                // set params
                $params = array_merge($data, $args);

                if ($element->hasValue($this->app->data->create($params))) {
                    $elements[] = compact('element', 'params');
                }
            }
        }

        foreach ($elements as $i => $data) {
            $params = array_merge(array('first' => ($i == 0), 'last' => ($i == count($elements) - 1)), $data['params']);

            $data['element']->loadAssets();
            $output[$i] = parent::render('element.' . $style, array(
                'element' => $data['element'],
                'params'  => $this->app->data->create($params)
            ));
        }

        $this->_layout = $layout;

        return implode("\n", $output);
    }

    /**
     * @param $position
     *
     * @return mixed
     */
    public function getConfigPosition($position)
    {
        $config   = $this->_getPositions();
        $position = $config->get($this->_jbprice->identifier . '.' . $this->_layout . '.' . $position);

        return !empty($position) ? $position : array();
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

    /**
     * @return JSONData
     */
    public function _getPositions()
    {
        $layouts = $this->_jbconfig->getGroup('cart.' . JBCart::CONFIG_PRICE_TMPL);

        return $layouts;
    }

    /**
     * @return mixed
     */
    protected function _getConfig()
    {
        $params = $this->_jbconfig->getGroup('cart.' . JBCart::CONFIG_PRICE . '.' . $this->_jbprice->identifier);

        return $params->get(JBCart::DEFAULT_POSITION);
    }

}
