<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class ShippingRenderer
 */
class ShippingRenderer extends PositionRenderer
{

    /**
     * @var JBCartOrder
     */
    protected $_order = null;

    /**
     * @var JBModelConfig
     */
    protected $_jbconfig = null;

    /**
     * @param App  $app
     * @param null $path
     */
    public function __construct($app, $path = null)
    {
        parent::__construct($app, $path);
        $this->app = $app;

        $this->_jbconfig = JBModelConfig::model();
    }

    /**
     * @param string $position
     * @return bool
     */
    public function checkPosition($position)
    {
        foreach ($this->_getConfigPosition($position) as $index => $data) {
            if ($element = $this->_order->getShippingElement($data['identifier'])) {

                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $index;

                if ($element->canAccess() && $element->hasValue()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $position
     * @param array  $args
     * @return string|void
     */
    public function renderPosition($position, $args = array())
    {
        // init vars
        $elements = array();
        $output   = array();
        $style    = isset($args['style']) ? $args['style'] : 'order.shipping';
        $layout   = $this->_layout;

        // render elements
        foreach ($this->_getConfigPosition($position) as $index => $data) {
            if ($element = $this->_getElement($data['identifier'])) {

                if (!$element->canAccess() || !$element->hasValue()) {
                    continue;
                }

                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $index;

                // set params
                $params = array_merge((array)$data, $args);

                // check value
                $elements[] = compact('element', 'params');
            }
        }

        foreach ($elements as $i => $data) {

            $output[$i] = parent::render('element.' . $style, array(
                'element' => $data['element'],
                'params'  => array_merge(
                    array(
                        'first' => ($i == 0),
                        'last'  => ($i == count($elements) - 1)
                    ),
                    $data['params']
                ),
            ));
        }

        // restore layout
        $this->_layout = $layout;

        return implode(PHP_EOL, $output);
    }

    /**
     * @param string $dir
     * @return array
     */
    public function getLayouts($dir)
    {
        // init vars
        $layoutList = array();
        $parts      = explode('.', $dir);
        $path       = implode('/', $parts);
        $xmlPath    = $this->_getPath($path . '/' . $this->_xml_file);

        // parse positions xml
        if ($xmlPath && $xml = simplexml_load_file($xmlPath)) {

            $layouts = $xml->xpath('positions[@layout]');

            foreach ($layouts as $layout) {

                $name = (string)$layout->attributes()->layout;

                $layoutList[$name] = $name;
            }

        }

        return $layoutList;
    }

    /**
     * @param $position
     * @return mixed
     */
    protected function _getConfigPosition($position)
    {
        return $this->_jbconfig->get($position, array(), 'cart.shipping');
    }

    /**
     * @param string $layout
     * @param array  $args
     * @return string|void
     */
    public function render($layout, $args = array())
    {
        // set order
        $this->_order = isset($args['order']) ? $args['order'] : null;

        // init vars
        $render = true;
        $result = '';

        // trigger beforedisplay event
        if ($this->_order) {
            $this->app->jbevent->fire($this->_order, 'shipping:beforedisplay', array(
                'render' => &$render,
                'html'   => &$result
            ));
        }

        // render layout
        if ($render) {
            $result .= parent::render($layout, $args);

            // trigger afterdisplay event
            if ($this->_order) {
                $this->app->jbevent->fire($this->_order, 'shipping:afterdisplay', array(
                    'html' => &$result
                ));
            }
        }

        return $result;
    }

    /**
     * @param string $layout
     * @return JSONData
     */
    public function getLayoutParams($layout = 'default')
    {
        return $this->_jbconfig->get(JBCart::DEFAULT_POSITION, array(), 'cart.' . JBCart::CONFIG_SHIPPINGS);
    }

    /**
     * @param array $args
     * @return string|void
     */
    public function renderAdminEdit($args = array())
    {
        $style = isset($args['style']) ? $args['style'] : null;

        return $this->render('edit.list', array(
            'order' => $args['order'],
            'style' => $style,
        ));
    }

    /**
     * @param $args
     * @return string
     */
    public function renderAdminPosition($args = array())
    {
        // init vars
        $layout = $this->_layout;
        $style  = isset($args['style']) ? $args['style'] : 'adminedit';

        $this->_order = isset($args['order']) ? $args['order'] : $this->_order;

        if ($shipping = $this->_order->getShipping()) {

            $output = parent::render('element.' . $style, array(
                'element' => $shipping,
                'params'  => $args,
            ));

            // restore layout
            $this->_layout = $layout;

            return $output;
        }
    }

    /**
     * @param $identifier
     * @return JBCartElementShipping
     */
    protected function _getElement($identifier)
    {
        $shippingList = JBCart::getInstance()->getShippingList();

        if ($shipping = $this->_order->getShippingElement($identifier)) {
            if (isset($shippingList[$identifier])) {
                $shipping->bindData($shippingList[$identifier]);
            }
        }

        return $shipping;
    }

}

