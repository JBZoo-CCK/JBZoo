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
 * Class JBCartElementShippingNewPost
 */
class JBCartElementShippingNewPost extends JBCartElementShipping
{
    const CACHE_TTL = 1440;

    const TYPE_DOORS = 3;
    const TYPE_WARE  = 4;

    const DEFAULT_TYPE   = 4;
    const DEFAULT_CITY   = 'Київ';
    const DEFAULT_REGION = 'Київська';

    protected $_currency = 'uah';

    /**
     * @var string
     */
    protected $_url = 'http://orders.novaposhta.ua/xml.php';

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->registerCallback('ajaxLocations');
    }

    /**
     * Check if exists api_key.
     * Without api_key all requests will be unsuccessful.
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $key = $this->config->get('api_key');

        if (!empty($key)) {
            return parent::hasValue($params);
        }

        return parent::hasValue($params);
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        $summ = $this->_order->val(0, $this->_currency);

        $items = $this->_order->getItems();
        foreach ($items as $item) {

            $item = $this->app->data->create($item);

            $quantity  = $item->get('quantity', 1);
            $ItemPrice = $this->_order->val($item->get('total'))->multiply($quantity);

            $data = array(
                'senderCity'      => $this->_getDefaultCity(),
                'recipientCity'   => $this->get('recipientCity', self::DEFAULT_CITY),
                'mass'            => (float)$item->find('elements._weight', 0.1) * $quantity,
                'height'          => (float)$item->find('elements._properties.height', 0.1) * $quantity,
                'width'           => (float)$item->find('elements._properties.width', 0.1) * $quantity,
                'depth'           => (float)$item->find('elements._properties.length', 0.1),
                'publicPrice'     => $ItemPrice->val($this->_currency),
                'deliveryType_id' => $this->get('deliveryType_id', self::DEFAULT_TYPE),
                'loadType_id'     => '1',
                'date'            => date('d.m.Y'),
                'floor_count'     => $this->get('floor_count'),
                'street'          => $this->get('street'),
            );

            $resp = $this->_apiRequest($data, 'countPrice', true);
            if ($resp && isset($resp['cost'])) {
                $resp['cost'] = $this->app->jbvars->money($resp['cost']);
                $summ->add(array($resp['cost'], $this->_currency));
            }

        }

        return $summ;
    }

    /**
     * @return mixed
     */
    protected function _getDefaultCity()
    {
        return $this->config->get('sender_city', 'Київ');
    }

    /**
     * @param array  $data
     * @param string $method
     * @return mixed
     */
    protected function _apiRequest($data, $method = null, $cleanResp = false)
    {
        // build xml body
        $dataXml = array();
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $dataXml[] = "<{$key}/>";
            } else {
                $dataXml[] = "<{$key}>{$value}</{$key}>";
            }
        }

        // render in nice format
        if ($method) {
            $dataXml = implode("\n        ", $dataXml);
            $dataXml = "    <{$method}>\n        {$dataXml}\n    </{$method}>";
        } else {
            $dataXml = implode("\n    ", $dataXml);
        }

        $xml = implode("\n", array(
            '<?xml version="1.0" encoding="utf-8"?>',
            '<file>',
            '    <auth>' . JString::trim($this->config->get('api_key')) . '</auth>',
            $dataXml,
            '</file>'
        ));

        // request
        $response = $this->app->jbhttp->request($this->_url, $xml, array(
            'method'    => 'post',
            'cache'     => true,
            'cache_ttl' => self::CACHE_TTL,
            'headers'   => array(
                'Content-Type' => 'text/xml'
            ),
        ));

        // convert xml to array
        $xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        $res = json_decode(json_encode($xml), true);

        if ($cleanResp) {
            return $res;

        } else {
            if ($res['responseCode'] == 200 && isset($res['result'])) {
                return $res['result'];
            }
        }
    }

    /**
     * @return array
     */
    protected function _getRegionList()
    {
        $resp = $this->_apiRequest(array('city' => null));

        $locations = array('' => '-&nbsp;' . JText::_('JBZOO_ELEMENT_SHIPPING_NEWPOST_REGION') . '&nbsp;-');
        if ($resp && isset($resp['cities']['city'])) {
            foreach ($resp['cities']['city'] as $region) {
                $locations[$region['areaNameUkr']] = $region['areaNameUkr'];
            }
        }

        ksort($locations);
        return $locations;
    }

    /**
     * @param null $region
     * @return array
     */
    protected function _getCityList($region = null)
    {
        $locations = array('' => '-&nbsp;' . JText::_('JBZOO_ELEMENT_SHIPPING_NEWPOST_CITY') . '&nbsp;-');

        if ($region) {
            $resp = $this->_apiRequest(array('city' => null));
            if ($resp && isset($resp['cities']['city'])) {
                foreach ($resp['cities']['city'] as $region) {
                    $locations[$region['nameUkr']] = $region['nameUkr'];
                }
            }
            ksort($locations);
        }

        return $locations;
    }

    /**
     * @param null $city
     * @return array
     */
    protected function _getWarehouseList($city = null)
    {
        $warehouses = array('' => '-&nbsp;' . JText::_('JBZOO_ELEMENT_SHIPPING_NEWPOST_WAREHOUSES') . '&nbsp;-');
        if (empty($city)) {
            return $warehouses;
        }

        $resp = $this->_apiRequest(array('warenhouse' => null, 'filter' => $city));

        if (isset($resp['whs']['warenhouse'])) {
            foreach ($resp['whs']['warenhouse'] as $whs) {
                if (isset($whs['address'])) {
                    $warehouses[$whs['address']] = $whs['address'];
                }
            }
        }

        return $warehouses;
    }

    /**
     * @return array
     */
    protected function _getTypeList()
    {
        return array(
            self::TYPE_DOORS => JText::_('JBZOO_ELEMENT_SHIPPING_NEWPOST_TO_DOORS'),
            self::TYPE_WARE  => JText::_('JBZOO_ELEMENT_SHIPPING_NEWPOST_TO_WAREHOUSE'),
        );
    }

    /**
     * @param $type
     * @param $location
     */
    public function ajaxLocations($type, $location)
    {
        $list = array();

        if ($type == 'cities') {
            $list = $this->_getCityList($location);
        } else if ($type == 'warehouses') {
            $list = $this->_getWarehouseList($location);
        }

        $this->app->jbajax->send(array('list' => $list));
    }

    /**
     * @return string
     */
    protected function _getAjaxLocationsUrl()
    {
        return $this->getAjaxUrl('ajaxLocations');
    }

}
