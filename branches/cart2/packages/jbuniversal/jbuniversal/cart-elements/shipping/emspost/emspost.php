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
 * Class JBCartElementShippingEmsPost
 */
class JBCartElementShippingEmsPost extends JBCartElementShipping
{
    const CURRENCY  = 'rub';
    const CACHE_TTL = 1440;

    /**
     * Url to make request
     * @var string
     */
    protected $_url = 'http://emspost.ru/api/rest';

    /**
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->js('cart-elements:shipping/emspost/assets/js/emspost.js');
        $this->app->jbassets->chosen();
    }

    /**
     * @param  string $locType - cities, regions, countries, russia
     * @return string
     */
    protected function _getLocations($locType)
    {
        $locations = $this->_apiRequest(array(
            'method' => 'ems.get.locations',
            'plain'  => 'true',
            'type'   => $locType,
        ));

        $result = array('' => '-&nbsp;' . JText::_('JBZOO_SHIPPING_EMSPOST_' . $locType) . '&nbsp;-');

        if (!$locations) {
            return $result;
        }

        $jbvars = $this->app->jbvars;
        foreach ($locations['locations'] as $location) {

            $value = $jbvars->lower($location['value']);
            $name  = JString::ucfirst($jbvars->lower($location['name']));

            $result[$value] = $name;
        }

        if ($locType != 'russia') {
            asort($result);
        }

        return $result;
    }

    /**
     * @return JBCartValue
     */
    public function getRate()
    {
        $summ = $this->_order->val(0, self::CURRENCY);

        if ($location = $this->_getLocation($this->data())) {
            $response = $this->_apiRequest(array(
                'method' => 'ems.calculate',
                'weight' => $this->_getWeight(),
                'from'   => $this->_getDefaultCity(),
                'to'     => $location,
            ));

            if ($response) {
                $summ->set($response['price'], self::CURRENCY);
            }
        }

        return $summ;
    }

    /**
     * @param JSONData $data
     * @return string
     */
    protected function _getLocation($data)
    {
        $location = null;
        if ($data->get('cities')) {
            $location = $data->get('cities');

        } else if ($data->get('countries')) {
            $location = $data->get('countries');

        } else if ($data->get('regions')) {
            $location = $data->get('regions');

        } else if ($data->get('russia')) {
            $location = $data->get('russia');
        }

        return $location;
    }

    /**
     * @return string
     */
    protected function _getDefaultCity()
    {
        $jbvars = $this->app->jbvars;

        $defaultCity = $jbvars->lower(parent::_getDefaultCity());
        $cityList    = $this->_getLocations('cities');

        foreach ($cityList as $code => $city) {
            $city = $jbvars->lower($city);
            if ($defaultCity == $city) {
                return $code;
            }
        }
    }

    /**
     * @param $options
     * @return null
     */
    protected function _apiRequest($options)
    {
        $options['plain'] = 'true'; // forced options for resolving bug with spaces

        $response = $this->app->jbhttp->request($this->_url, $options, array(
            'cache'     => 1,
            'cache_ttl' => self::CACHE_TTL,
        ));

        $locations = json_decode($response, true);
        if (isset($locations['rsp']) && $locations['rsp']['stat'] == 'ok') {
            return $locations['rsp'];
        }

        return null;
    }

    /**
     * Validates the submitted element
     * @param $value
     * @param $params
     * @return array
     * @throws JBCartElementShippingException
     */
    public function validateSubmission($value, $params)
    {
        $location = $this->_getLocation($value);
        if (!$location) {
            throw new JBCartElementShippingException('empty location');
        }

        // for calculate rate
        $this->bindData($value);

        $rate = $this->getRate();
        $value->set('rate', $rate->data(true));

        return $value;
    }

    /**
     * @return float
     */
    protected function _getWeight()
    {
        $resp = $this->_apiRequest(array(
            'method' => 'ems.get.max.weight'
        ));

        $max = (float)$resp['max_weight'];
        $cur = $this->_order->getTotalWeight();
        if ($cur <= $max) {
            return $cur;
        }

        return $max;
    }

}
