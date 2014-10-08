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
 * Class JBCartElementShipping
 */
abstract class JBCartElementShipping extends JBCartElement
{
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

    const HTTP_POST = 'post';
    const HTTP_GET  = 'get';

    /**
     * Class constructor
     *
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_jbmoney    = $this->app->jbmoney;
        $this->_cartConfig = $this->_getCartConfig();
    }

    /**
     * @param float       $sum
     * @param string      $currency
     * @param JBCartOrder $order
     *
     * @return float
     */
    public function modify($sum, $currency, JBCartOrder $order)
    {
        return $sum;
    }

    /**
     * Render shipping in order
     *
     * @param  array
     *
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

        return FALSE;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        $shipping = JBModelConfig::model()->get('default_shipping', NULL, 'cart.config');

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

        return FALSE;
    }

    /**
     * @param      $name
     * @param bool $array
     *
     * @return string|void
     */
    public function getControlName($name, $array = FALSE)
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
     *
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
     *
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
     *
     * @return int
     */
    public function getBasketWeight()
    {
        $cart = JBcart::getInstance();

        $weight = $cart->getWeight();

        return $weight;
    }

    /**
     * Get the properties of all items in basket.
     *
     * @return array
     */
    public function getBasketProperties()
    {
        $prop  = array();
        $items = $this->getBasketItems();

        foreach ($items as $item) {
            if (!empty($item['params'])) {

                $prop['height'] = $prop['width'] = $prop['length'] = 0;

                $params = $this->app->data->create($item['params']);
                $prop['height'] += $params->get('height', 1);
                $prop['width'] += $params->get('width', 1);
                $prop['length'] += $params->get('length', 1);
            }
        }

        return $prop;
    }

    /**
     * Get all items in basket
     *
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
     *
     * @param  array $params
     *
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
     *
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
     *
     * @param  boolean $encode - Encode array or no
     *
     * @return string|array
     */
    public function getWidgetParams($encode = TRUE)
    {
        $params = array(
            'shippingfields' => implode(':', $this->config->get('shippingfields', array()))
        );

        return $encode ? json_encode($params) : $params;
    }

    /**
     * Cleans data
     *
     * @param  string         $data
     * @param  string|boolean $charlist
     *
     * @return string mixed
     */
    public function clean($data, $charlist = FALSE)
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
     *
     * @param $responseBody
     *
     * @return mixed
     */
    public function processingData($responseBody)
    {
        return $responseBody;
    }

    /**
     * Make request to service and get results
     *
     * @param  string $url    - Shipping service url.
     * @param  string $method - POST, GET.
     * @param  array  $data   - Data for POST $method
     *
     * @return bool|array
     */
    protected function _callService($url, $method = self::HTTP_GET, $data = array())
    {
        $group = $this->getElementGroup() . '_' . $this->getElementType();

        //using cache to avoid a ban from API
        if (!($responseData = $this->app->jbcache->get($url, $group, TRUE))) {

            $jhttp = JHttpFactory::getHttp();

            try {
                if ($method == self::HTTP_GET) {
                    $response = $jhttp->get($url);

                } else if ($method == self::HTTP_POST) {
                    $response = $jhttp->post($url, $data);
                }

                if ($response->code == 200) {
                    $responseData = $this->processingData($response->body);
                } else {
                    $responseData = FALSE;
                }

            } catch (Exception $e) {
                $responseData = FALSE;
            }

            if ($responseData) {
                $this->app->jbcache->set($url, $responseData, $group, TRUE);
            }
        }

        return $responseData;
    }

    /**
     * City location of the store
     * @return string
     */
    protected function _getDefaultCity()
    {
        //$city = $this->_cartConfig->get('city');

        return JString::strtolower('Москва');
    }

    /**
     * Country location of the store
     * @return string
     */
    protected function _getDefaultCountry()
    {
        //$country = $this->_cartConfig->get('country');

        return JString::strtolower('Украина');
    }

    /**
     * Get default value for select
     * @return array
     */
    protected function _getDefaultValue()
    {
        return array('' => '-None-');
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
     *
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
     *
     * @return mixed|string
     */
    private function _clean($str, $charlist = FALSE)
    {
        $str = JString::trim($str, $charlist);
        $str = JString::strtolower($str);

        return $str;
    }

    /**
     * Change shipping status and fire event
     *
     * @param $newStatus
     */
    public function setStatus($newStatus)
    {
        $oldStatus = $this->getStatus();
        if ($oldStatus && $oldStatus != $newStatus) {
            $this->app->event->dispatcher->notify($this->app->event->create($this->getOrder(), 'basket:shippingStatus',
                compact('oldStatus', 'newStatus')));
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
