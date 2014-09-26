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
 * Class JBCartElementShippingNewPost
 */
class JBCartElementShippingNewPost extends JBCartElementShipping
{
    /**
     * Headers for service
     * @var array
     */
    public $headers = array("Content-Type: text/xml");

    /**
     * @var string
     */
    protected $_url = 'http://orders.novaposhta.ua/xml.php?';

    const NEWPOST_CURRENCY = 'UAH';

    const NEWPOST_DELIVERY_TO_DOORS = 3;
    const NEWPOST_DELIVERY_TO_WARE  = 4;

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

        $this->registerCallback('ajaxGetCities');
        $this->registerCallback('ajaxGetWarehouses');
        $this->registerCallback('ajaxGetPrice');
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
        $shipping = $this->getRate();

        return $sum + $shipping;
    }

    /**
     * @return int
     */
    public function getRate()
    {
        return $this->get('value', 0);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
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

        return false;
    }

    /**
     * @param array $params
     *
     * @return mixed|string
     */
    public function renderSubmission($params = array())
    {
        if ($layout = $this->getLayout('submission.php')) {
            return self::renderLayout($layout, array(
                'params' => $params
            ));
        }

        return false;
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
        $params = $value->getArrayCopy();
        $params = $this->mergeParams($params);
        $price  = $this->_getPrice($params);

        $type = (int)$value->get('deliverytype_id') == self::NEWPOST_DELIVERY_TO_DOORS ?
            JText::_('JBZOO_ORDER_SHIPPING_NEWPOST_TO_DOORS') :
            JText::_('JBZOO_ORDER_SHIPPING_NEWPOST_TO_WARE');

        return array(
            'value'  => $price,
            'fields' => array(
                'type'      => $type,
                'recipient' => $this->app->validator->create('string')->clean($value->get('recipientcity'))
            ),
            'params' => $params
        );
    }

    /**
     * @return array
     */
    public function getRegions()
    {
        $data   = $this->_getCitiesRegionsFromPost();
        $states = $this->_getDefaultValue();

        if (!empty($data)) {
            foreach ($data->result->cities->city as $city) {
                if (!isset($states[$this->clean($city->areaNameUkr)])) {
                    $states[$this->clean($city->areaNameUkr)] = array();
                }
                $states[$this->clean($city->areaNameUkr)] = trim($city->areaNameUkr);
            }
        }

        return $states;
    }

    /**
     * @param  null|string $region
     *
     * @return array
     */
    public function getCities($region = null)
    {
        $data = $this->_getCitiesRegionsFromPost();

        $cities = $this->_getDefaultValue();
        $region = $this->clean($region);

        if (!empty($data)) {
            foreach ($data->result->cities->city as $city) {

                if (isset($region) &&
                    $this->clean($city->areaNameUkr) == $region
                ) {
                    $cities[$region][trim($city->nameUkr)] = JString::trim($city->nameUkr);

                } elseif (!isset($region)) {
                    $cities[$this->clean($city->areaNameUkr)][] = JString::trim($city->nameUkr);

                }
            }
        }

        return $cities;
    }

    /**
     * @param  null $city
     *
     * @return array|bool
     */
    public function getWarehouses($city = null)
    {
        if (empty($city)) {
            return $this->_getDefaultValue();
        }

        $wrn  = array();
        $data = $this->_getWarehousesFromPost($city);

        foreach ($data->result->whs->warenhouse as $warehouse) {
            $wrn[$this->clean($warehouse->address)] = trim($warehouse->address);
        }

        return $wrn;
    }

    /**
     * @return array
     */
    public function getDefaultParams()
    {
        $params = array(
            'sendercity'      => $this->_getDefaultCity(),
            'recipientcity'   => '',
            'mass'            => 1,
            'height'          => 1,
            'width'           => 1,
            'depth'           => 1,
            'publicprice'     => 0,
            'deliverytype_id' => '',
            'date'            => date('Y-m-d H:i:s'),
            'floor_count'     => '',
            'street'          => ''
        );

        return $params;
    }

    /**
     * @param  $data
     *
     * @return array|bool
     */
    public function callService($data)
    {
        $responseData = $this->_callService($this->_url, JBCartElementShipping::HTTP_POST, $data, $this->headers);

        return $responseData;
    }

    /**
     * Decoding the result of API call
     *
     * @param $responseBody
     *
     * @return array
     */
    public function processingData($responseBody)
    {
        return simplexml_load_string($responseBody);
    }

    /**
     * Make request to service and get results
     *
     * @param  string $url    - Shipping service url.
     * @param  string $method - POST, GET.
     * @param  array  $data   - Data for POST $method
     * @param  array  $headers
     *
     * @return bool|array
     */
    protected function _callService($url, $method = self::HTTP_GET, $data = array(), array $headers = array())
    {
        $group = $this->getElementGroup() . '_' . $this->getElementType();

        //using cache to avoid a ban from API
        if (!$responseData = simplexml_load_string($this->app->jbcache->get($data, $group, true))) {

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
                    $responseData = false;
                }

            } catch (Exception $e) {
                $responseData = false;
            }


            if ($responseData) {
                $this->app->jbcache->set($data, $responseData->asXML(), $group, true);
            }
        }

        return $responseData;
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
            'getCitiesUrl'     => $this->app->jbrouter->elementOrder($this->identifier, 'ajaxGetCities'),
            'getWarehousesUrl' => $this->app->jbrouter->elementOrder($this->identifier, 'ajaxGetWarehouses'),
            'getPriceUrl'      => $this->app->jbrouter->elementOrder($this->identifier, 'ajaxGetPrice'),
            'shippingfields'   => implode(':', $this->config->get('shippingfields', array()))
        );

        return $encode ? json_encode($params) : $params;
    }

    /**
     * @param $region
     */
    public function ajaxGetCities($region)
    {
        $cities = $this->getCities($region);

        if (!empty($cities)) {
            $this->app->jbajax->send(array(
                    'cities' => $cities[$region]
                )
            );
        }
    }

    /**
     * @param $city
     */
    public function ajaxGetWarehouses($city)
    {
        $warehouses = $this->getWarehouses($city);

        if (empty($warehouses)) {
            $this->app->jbajax->send(array(
                    'warehouses' => $warehouses
                ),
                false
            );
        }

        $this->app->jbajax->send(array(
                'warehouses' => $warehouses
            )
        );
    }

    /**
     * @param array $fields
     */
    public function ajaxGetPrice($fields = array())
    {
        $params = json_decode($fields, true);
        $params = $this->mergeParams($params);
        $price  = $this->_getPrice($params);

        $this->app->jbajax->send(array(
            'price' => $this->_jbmoney->toFormat($price)
        ));
    }

    /**
     * City location of the store
     * @return string
     */
    protected function _getDefaultCity()
    {
        return 'Киев';
    }

    /**
     * Send request to novaposhta and get cities
     * @return SimpleXMLElement|string
     */
    protected function _getCitiesRegionsFromPost()
    {
        $api_key = $this->config->get('api_key');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <file>
        <auth>' . $api_key . '</auth>
        <city/>
        </file>';

        $xml = $this->callService($xml);

        return $xml;
    }

    /**
     * @param  $city
     *
     * @return SimpleXMLElement|string
     */
    protected function _getWarehousesFromPost($city)
    {
        $api_key = $this->config->get('api_key');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <file>
        <auth>' . $api_key . '</auth>
        <warenhouse/>
        <filter>' . $city . '</filter>
        </file>';

        $xml = $this->callService($xml);

        return $xml;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    protected function _getPrice($params = array())
    {
        $api_key       = $this->config->get('api_key');
        $publicPrice   = $params['publicprice'];
        $recipientCity = $params['recipientcity'];
        $senderCity    = $params['sendercity'];
        $deliveryId    = $params['deliverytype_id'];
        $mass          = $params['mass'];
        $date          = $params['date'];
        $depth         = $params['depth'];
        $width         = $params['width'];
        $height        = $params['height'];
        $floorCount    = $params['floor_count'];

        $publicPrice = $this->_jbmoney->convert($this->currency(), self::NEWPOST_CURRENCY, $publicPrice);
        $price       = 0;

        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <file>
        <auth>' . $api_key . '</auth>
        <countPrice>
            <senderCity>' . $senderCity . '</senderCity>
            <recipientCity>' . $recipientCity . '</recipientCity>
            <mass>' . $mass . '</mass>
            <height>' . $height . '</height>
            <width>' . $width . '</width>
            <depth>' . $depth . '</depth>
            <publicPrice>' . $publicPrice . '</publicPrice>
            <deliveryType_id>' . $deliveryId . '</deliveryType_id>
            <floor_count>' . $floorCount . '</floor_count>
            <date>' . $date . '</date>
            </countPrice>
        </file>';

        $xml = $this->callService($xml);

        if (!empty($xml)) {
            $price = $this->_jbmoney->convert(self::NEWPOST_CURRENCY, $this->currency(), (float)$xml->cost);
        }

        return $price;
    }

    /**
     * @param  JBCartOrder $order
     *
     * @return array
     */
    protected function _getParamsFromOrder(JBCartOrder $order)
    {
        $fields = $order->getShippingFields();
        $params = $this->getDefaultParams();

        foreach ($fields as $identifier => $field) {

            if (JString::strlen($identifier) != 36) {
                $identifier = JString::str_ireplace('_', '', $identifier, 1);

                if ($identifier == 'city') {
                    $value = JString::strtoupper(JString::trim($field->get('value')));

                    $params['recipientCity'] = $value;
                }
            }
        }

        return $params;
    }

}
