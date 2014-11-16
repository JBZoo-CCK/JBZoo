<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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
 * Class JBCartElementShipping
 */
abstract class JBCartElementShipping extends JBCartElement
{
    /**
     * Default price
     * @var float|mixed
     */
    public $default_price = 0.00;

    /**
     * Path to shipping service
     * @var
     */
    protected $_url;

    /**
     * @var string
     */
    protected $_namespace = JBCart::ELEMENT_TYPE_SHIPPING;
    /**
     * @var JBMoneyHelper
     */
    protected $_jbmoney;

    /**
     * Default cart options
     * @var JSONData
     */
    protected $_cartConfig;

    /**
     * Currency symbol
     * @var string
     */
    protected $_symbol = null;

    const HTTP_POST = 'post';
    const HTTP_GET  = 'get';

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_cartConfig = $this->_getCartConfig();

        $this->_jbmoney = $this->app->jbmoney;
        //$this->_symbol  = $this->_jbmoney->getSymbol($this->currency());

        $this->default_price = $this->_jbmoney->format(0);

        $this->registerCallback('ajaxGetPrice');
    }

    /**
     * Get price form element config
     * @param  array $params
     * @return integer
     */
    abstract function getPrice($params = array());

    /**
     * Convert price to shipping service currency
     * before/after send call service.
     * @param $price
     * @param $from
     * @param $to
     * @return integer
     */
    public function convert($price, $from, $to)
    {
        $value = $this->_jbmoney->convert($from, $to, $price);

        if (JString::strlen($price) === 0 || $price === 0) {
            $value = JText::_('JBZOO_CART_SHIPPING_VALUE_DEFAULT');
        }

        return $value;
    }

    /**
     * @param $price
     * @return float
     */
    public function clear($price)
    {
        return $this->_jbmoney->clearValue($price);
    }

    /**
     * @param float       $sum
     * @param string      $currency
     * @param JBCartOrder $order
     * @return float
     */
    public function modify($sum, $currency, JBCartOrder $order)
    {
        return $sum;
    }

    /**
     * Render shipping in order
     * @param  array
     * @return bool|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'value'  => $this->get('value'),
                'fields' => $this->get('fields', array())
            ));
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        $shipping = JBModelConfig::model()->get('default_shipping', null, 'cart.config');

        return $this->identifier == $shipping;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $default = JBCart::getInstance()->getDefaultStatus(JBCart::STATUS_SHIPPING);

        return $this->get('status', $default->getCode());
    }

    /**
     * @return bool|string
     */
    public function renderFields()
    {
        if ($layout = $this->getLayout('fields.php')) {
            return self::renderLayout($layout);
        }

        return false;
    }

    /**
     * @param      $name
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
     * @param  array $params
     * @return string
     */
    public function mergeParams($params = array())
    {
        $defaultParams = $this->getDefaultParams();

        if (!empty($params)) {

            $defaultParams = array_change_key_case($defaultParams, CASE_LOWER);
            $params        = array_change_key_case($params, CASE_LOWER);

            foreach ($defaultParams as $key => $param) {

                if (!empty($params[$key])) {
                    $defaultParams[$key] = JString::strtolower($params[$key]);
                }
            }
        }

        return $defaultParams;
    }

    /**
     * Default params to Call Service.
     * @return array
     */
    public function getDefaultParams()
    {
        $prop   = $this->getBasketProperties();
        $params = array(
            'city'   => $this->_getDefaultCity(),
            'weight' => $this->getBasketWeight(),
            'height' => $prop['height'],
            'width'  => $prop['width'],
            'depth'  => $prop['length'],
            'date'   => date('Y-m-d H:i:s'),
        );

        return $params;
    }

    /**
     * Get the weight of all items in basket.
     * @return int
     */
    public function getBasketWeight()
    {
        $cart = JBcart::getInstance();

        $weight = $cart->getWeight();

        return $weight;
    }

    /**
     * Get the properties(width, height, length) of all items in basket.
     * @return array
     */
    public function getBasketProperties()
    {
        $cart = JBcart::getInstance();

        $properties = $cart->getProperties();

        return $properties;
    }

    /**
     * Get price for all items in basket
     * @param string $currency
     * @return int|mixed
     */
    public function getBasketValue($currency = 'EUR')
    {
        $cart   = JBcart::getInstance();
        $items  = $cart->getItems();
        $result = 0;

        foreach ($items as $item) {

            $value = $item['quantity'] * $item['price'];

            $result += $this->_jbmoney->convert($item['currency'], $currency, $value);
        }

        return (float)$result;
    }

    /**
     * Get all items in basket
     * @return mixed
     */
    public function getBasketItems()
    {
        $cart  = JBCart::getInstance();
        $items = $cart->getItems();

        return $items;
    }

    /**
     * Make path to service api from params
     * @param  array $params
     * @return string
     */
    public function getServicePath($params = array())
    {
        $result = $this->_url;

        if (empty($params)) {
            return $result;
        }

        foreach ($params as $key => $value) {
            $result .= $key . '=' . $value . '&';
        }

        return $result;
    }

    /**
     * Try to get currency from order or cart config
     * @return mixed
     */
    public function currency()
    {
        $currency = $this->_cartConfig->get('default_currency', 'EUR');
        if (isset($this->_order->id)) {
            $currency = $this->_order->getCurrency();
        }

        return $currency;
    }

    /**
     * Save data in the order.
     * Data comes from method - validateSubmission.
     * return array
     */
    public function getOrderData()
    {
        $data = parent::getOrderData();

        $data->set('status', $this->getStatus());
        $data->set('name', $this->getName());
        $data->set('rate', $this->getRate());

        return $data;
    }

    /**
     * Get array of parameters to push it into(data-params) element div
     * @param  boolean $encode - Encode array or no
     * @return string|array
     */
    public function getWidgetParams($encode = true)
    {
        $params = array(
            'shippingfields' => implode(':', $this->config->get('shippingfields', array())),
            'getPriceUrl'    => $this->app->jbrouter->elementOrder($this->identifier, 'ajaxGetPrice'),
            'default_price'  => $this->config->get('cost', '-'),
            'symbol'         => $this->_symbol
        );

        return $encode ? json_encode($params) : $params;
    }

    /**
     *
     */
    public function ajaxGetPrice($fields = '')
    {
        $price = $this->getPrice();
        $this->app->jbajax->send(array(
            'price' => $this->_jbmoney->toFormat($price)
        ));
    }

    /**
     * Cleans data
     * @param  string         $data
     * @param  string|boolean $charlist
     * @return string mixed
     */
    public function clean($data, $charlist = false)
    {
        if (!is_array($data)) {
            return $this->_clean($data, $charlist);
        }

        foreach ($data as $key => $value) {
            $data[$this->_clean($key, $charlist)] = $this->_clean($value, $charlist);
        }

        return $data;
    }

    /**
     * Decoding the result of API call
     * @param $responseBody
     * @return mixed
     */
    public function processingData($responseBody)
    {
        return $responseBody;
    }

    /**
     * Make request to service and get results
     * @param  string $url    - Shipping service url.
     * @param  string $method - POST, GET.
     * @param  array  $data   - Data for POST $method
     * @return bool|array
     */
    protected function _callService($url, $method = 'get', $data = array())
    {
        $response = $this->app->jbhttp->request($url, $data, array(
            'method'    => $method,
            'cache'     => 1,
            'cache_ttl' => 60,
            'cache_id'  => $this->getElementGroup() . '_' . $this->getElementType(),
        ));

        $responseData = $this->processingData($response);

        return $responseData;
    }

    /**
     * City location of the store
     * @return string
     */
    protected function _getDefaultCity()
    {
        $city = $this->_cartConfig->get('default_shipping_city');
        $city = JString::trim($city);

        return JString::strtolower($city);
    }

    /**
     * Country location of the store
     * @return string
     */
    protected function _getDefaultCountry()
    {
        $country = $this->_cartConfig->get('default_shipping_country');
        $country = JString::trim($country);

        return JString::strtolower($country);
    }

    /**
     * Get default value for select
     * @param string $type - Can be language constant or just a string.
     * @return array
     */
    protected function _getDefaultValue($type = 'JBZOO_NONE')
    {
        return array('' => '-&nbsp;' . JText::_($type) . '&nbsp;-');
    }

    /**
     * @return JSONData
     */
    protected function _getCartConfig()
    {
        $config = JBModelConfig::model();

        return $config->getGroup('cart.config');
    }

    /**
     * @param  JBCartOrder $order
     * @return array
     */
    protected function _getParamsFromOrder(JBCartOrder $order)
    {
        $fields = $order->getShippingFields();
        $params = array();

        foreach ($fields as $identifier => $field) {
            $params[$identifier] = $field->get('value');
        }

        return $params;
    }

    /**
     * @param  string      $str
     * @param  bool|string $charlist
     * @return mixed|string
     */
    private function _clean($str, $charlist = false)
    {
        $str = JString::trim($str, $charlist);
        $str = JString::strtolower($str);

        return $str;
    }

    /**
     * Change shipping status and fire event
     * @param $newStatus
     */
    public function setStatus($newStatus)
    {
        $oldStatus = (string)$this->getStatus();
        $newStatus = (string)$newStatus;

        $isChanged = $oldStatus // is not first set on order creating
            && $oldStatus != JBCartStatusHelper::UNDEFINED // old is not empty
            && $oldStatus != $newStatus; // is really changed

        if ($isChanged) {

            $this->app->event->dispatcher->notify($this->app->event->create(
                $this->getOrder(),
                'basket:shippingStatus',
                array(
                    'oldStatus' => $oldStatus,
                    'newStatus' => $newStatus,
                )
            ));

        }

        $this->set('status', $newStatus);
    }

}

/**
 * Class JBCartElementShippingException
 */
class JBCartElementShippingException extends JBCartElementException
{
}
