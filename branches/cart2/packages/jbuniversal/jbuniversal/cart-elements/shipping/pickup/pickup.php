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
 * Class JBCartElementShippingCourier
 */
class JBCartElementShippingPickup extends JBCartElementShipping
{
    /**
     * @param  array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @param array $params
     *
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {
        $shipping  = $this->getPrice();
        $addresses = $this->getAddress();

        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params'    => $params,
                'shipping'  => $shipping,
                'addresses' => $addresses
            ));
        }

        return false;
    }

    /**
     *
     */
    public function ajaxGetPrice($fields = '')
    {
        $price = $this->getPrice();

        $this->app->jbajax->send(array(
            'price'  => $price,
            'symbol' => ''
        ));
    }

    /**
     * Get price form element config
     *
     * @param  array $params
     *
     * @return integer
     */
    public function getPrice($params = array())
    {
        return $this->app->data->create(array(
            'price'  => JText::_('JBZOO_CART_SHIPPING_VALUE_DEFAULT'),
            'symbol' => ' '
        ));
    }

    /**
     * Validates the submitted element
     *
     * @param $value
     * @param $params
     *
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        return array(
            'value' => $this->getPrice()->get('price')
        );
    }

    /**
     * @return mixed|string
     */
    public function getAddress()
    {
        $addresses = $this->config->get('addresses', null);
        if (!empty($addresses)) {
            $address   = explode("\n", $addresses);
            $addresses = implode('<br/>', $address);
        }

        return $addresses;
    }

    /**
     * Get array of parameters to push it into(data-params) element div
     *
     * @param  boolean $encode - Encode array or no
     *
     * @return string|array
     */
    public function getWidgetParams($encode = true)
    {
        $params = array(
            'shippingfields' => implode(':', $this->config->get('shippingfields', array())),
            'getPriceUrl'    => $this->app->jbrouter->elementOrder($this->identifier, 'ajaxGetPrice'),
            'default_price'  => $this->getPrice()->get('price'),
            'symbol'         => $this->getPrice()->get('symbol')
        );

        return $encode ? json_encode($params) : $params;
    }

}
