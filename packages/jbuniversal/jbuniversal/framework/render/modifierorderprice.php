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
 * Class ModifierOrderPriceRenderer
 */
class ModifierOrderPriceRenderer extends PositionRenderer
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

        $this->_jbconfig = JBModelConfig::model();
    }

    /**
     * @param string $position
     * @return bool
     */
    public function checkPosition($position)
    {
        foreach ($this->_getConfigPosition($position) as $index => $data) {

            if ($element = $this->_order->getModifierOrderPriceElement($data['identifier'])) {

                $data['_layout']   = $this->_layout;
                $data['_position'] = $position;
                $data['_index']    = $index;

                if ($element->canAccess()) {
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
        $style    = isset($args['style']) ? $args['style'] : 'order.modifier';
        $layout   = $this->_layout;

        // render elements
        foreach ($this->_getConfigPosition($position) as $index => $data) {

            if ($element = $this->_order->getModifierOrderPriceElement($data['identifier'])) {

                if (!$element->canAccess()) {
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
     * @param $position
     * @return mixed
     */
    protected function _getConfigPosition($position)
    {
        return $this->_jbconfig->get($position, array(), 'cart.' . JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE);
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
            $this->app->jbevent->fire($this->_order, 'modifier:beforedisplay', array(
                'render' => &$render,
                'html'   => &$result
            ));
        }

        // render layout
        if ($render) {
            $result .= parent::render($layout, $args);

            // trigger afterdisplay event
            if ($this->_order) {
                $this->app->jbevent->fire($this->_order, 'modifier:afterdisplay', array(
                    'html' => &$result
                ));
            }
        }

        return $result;
    }

    /**
     * @return JSONData
     */
    public function getLayoutParams()
    {
        return $this->_jbconfig->get(JBCart::DEFAULT_POSITION, array(), 'cart.' . JBCart::ELEMENT_TYPE_MODIFIER_ORDER_PRICE);
    }

}
