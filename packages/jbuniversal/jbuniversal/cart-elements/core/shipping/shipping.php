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
 * Class JBCartElementShipping
 */
abstract class JBCartElementShipping extends JBCartElement
{
    protected $_namespace = JBCartOrder::ELEMENT_TYPE_SHIPPING;

    /**
     * @param float $sum
     * @param string $currency
     * @param JBCartOrder $order
     * @return float
     */
    abstract public function modify($sum, $currency, JBCartOrder $order);

    /**
     * @return bool
     */
    public function isDefault()
    {
        $shipping = JBModelConfig::model()->get('cart.config.default_shipping');

        return $this->identifier == $shipping;
    }

    /**
     * @param $name
     * @param bool $array
     * @return string|void
     */
    public function getControlName($name, $array = false)
    {
        return $this->_namespace . '[' . $name . ']';
    }

    /**
     * @return int
     */
    public function getRate()
    {
        return 0;
    }

    /**
     * return array
     */
    public function getOrderData()
    {
        return $this->app->data->create(array(
            'name'   => $this->getName(),
            'rate'   => $this->getRate(),
            'config' => $this->config->getArrayCopy(),
        ));
    }

}

/**
 * Class JBCartElementShippingException
 */
class JBCartElementShippingException extends JBCartElementException
{
}
