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
    const CACHE_TTL = 1440;
    const URL       = 'http://emspost.ru/api/rest';

    protected $_currency = 'rub';

    /**
     * @return $this
     */
    public function loadAssets()
    {
        parent::loadAssets();
        $this->app->jbassets->chosen();
    }

    /**
     * @param  string $locType - cities, regions, countries, russia
     * @return string
     */
    public static function getLocations($locType)
    {
        $zoo = App::getInstance('zoo');

        $locations = self::apiRequest(array(
            'method' => 'ems.get.locations',
            'plain'  => 'true',
            'type'   => $locType,
        ));

        $result = array('' => '-&nbsp;' . JText::_('JBZOO_ELEMENT_SHIPPING_EMSPOST_' . $locType) . '&nbsp;-');

        if (!$locations) {
            return $result;
        }

        $jbvars = $zoo->jbvars;
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
        $summ = $this->_order->val(0, $this->_currency);

        if ($location = $this->_getLocation($this->data())) {
            $response = $this->apiRequest(array(
                'method' => 'ems.calculate',
                'weight' => $this->_getWeight(),
                'from'   => $this->_getDefaultCity(),
                'to'     => $location,
            ));

            if ($response) {
                $summ->set($response['price'], $this->_currency);
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
        return $this->config->get('from', 'city--moskva');
    }

    /**
     * @return string
     */
    protected function _getDefaultCityName()
    {
        $city = $this->_getDefaultCity();
        return self::getLocationName($city);
    }

    /**
     * @param $alias
     * @return string
     */
    protected function getLocationName($alias)
    {
        $list = self::getLocationList($alias);
        return isset($list[$alias]) ? $list[$alias] : false;
    }

    /**
     * @param $options
     * @return null
     */
    public static function apiRequest($options)
    {
        $options['plain'] = 'true'; // forced options for resolving bug with spaces
        if (isset($options['method']) && $options['method'] == 'ems.calculate') {
            $options['type'] = 'att';
        }

        $response = App::getInstance('zoo')->jbhttp->request(self::URL, $options, array(
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
            throw new JBCartElementShippingException('JBZOO_ELEMENT_SHIPPING_EMSPOST_EMPTY_LOCATION');
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
        $resp = $this->apiRequest(array(
            'method' => 'ems.get.max.weight'
        ));

        $max = (float)$resp['max_weight'];
        $cur = $this->_order->getTotalWeight();

        if ($cur == 0) {
            $cur = 0.1;
        }

        if ($cur <= $max) {
            return $cur;
        }

        return $max;
    }

    /**
     * @param string           $name
     * @param string|array     $value
     * @param string           $controlName
     * @param SimpleXMLElement $node
     * @param SimpleXMLElement $parent
     * @return mixed
     */
    public static function getLocationSelect($name, $value, $controlName, $node, $parent)
    {
        $list = self::getLocationList();
        return App::getInstance('zoo')->jbhtml->select($list, $controlName . '[' . $name . ']', '', $value);
    }


    public static function getLocationList()
    {
        $list = array_merge(
            self::getLocations('cities'),
            self::getLocations('russia'),
            self::getLocations('countries'),
            self::getLocations('regions')
        );

        unset($list['']);
        asort($list);

        return $list;
    }

    /**
     * @return AppParameterForm
     */
    public function getConfigForm()
    {
        return parent::getConfigForm()->addElementPath(dirname(__FILE__));
    }
}
