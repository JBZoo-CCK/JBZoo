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
 * Class JBCartElementShippingRussianPost
 */
class JBCartElementShippingRussianPost extends JBCartElementShipping
{
    /**
     * @var string
     */
    public $_url = 'http://www.russianpost.ru/autotarif/Autotarif.aspx?';

    const RUSSIANPOST_CURRENCY = 'RUB';

    /**
     * Class constructor
     *
     * @param App $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->registerCallback('ajaxGetPrice');
    }

    /**
     * @param float $sum
     * @param string $currency
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
     * @param array $params
     *
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @return array
     */
    public function getDefaultParams()
    {
        $params = array(
            'viewpost'        => '',
            'countrycode'     => 643,
            'typePost'        => '',
            'viewpostName'    => '',
            'countrycodename' => '',
            'typepostname'    => '',
            'weight'          => $this->getBasketWeight(),
            'value1'          => 0,
            'postofficeid'    => 0
        );

        return $params;
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
     * @param  $value
     * @param  $params
     *
     * @return array
     */
    public function validateSubmission($value, $params)
    {
        $params = $value->getArrayCopy();
        $params = $this->mergeParams($params);
        $price  = $this->getPrice($params);

        return array(
            'value'  => $price->get('price'),
            'fields' => array(
                'viewpost' => $this->getViewPostName($params['viewpost']),
                'typepost' => $this->getTypePostName($params['typepost']),
                'zip'      => $params['postofficeid']
            ),
            'params' => $params
        );
    }

    /**
     * @return int
     */
    public function getRate()
    {
        return $this->get('value', 0);
    }

    /**
     * Get name of view post by id
     *
     * @param  $id
     *
     * @return string
     */
    public function getViewPostName($id)
    {
        $types = array(
            ''   => '-&nbsp;' . JText::_('JBZOO_DELIVERY_RUSSIANPOST_VIEW') . '&nbsp;-',
            '23' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_PARCEL'),
            '18' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_CARD'),
            '13' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_LETTER'),
            '26' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_RICH_PARCEL'),
            '36' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_RICH_PACKAGE'),
            '16' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_RICH_LETTER')
        );

        return !empty($types[$id]) ? $types[$id] : '-None-';
    }

    /**
     * Get name of type post by id
     *
     * @param  $id
     *
     * @return string
     */
    public function getTypePostName($id)
    {
        $typePost = array(
            ''  => '-&nbsp;' . JText::_('JBZOO_DELIVERY_RUSSIANPOST_TYPE') . '&nbsp;-',
            '1' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_GROUND'),
            '2' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_AIR'),
            '3' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_COMBINE'),
            '4' => JText::_('JBZOO_DELIVERY_RUSSIANPOST_FAST')
        );

        return !empty($typePost[$id]) ? $typePost[$id] : '-None-';
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
            'getPriceUrl'    => $this->app->jbrouter->elementOrder($this->identifier, 'ajaxGetPrice'),
            'shippingfields' => implode(':', $this->config->get('shippingfields', array())),
            'default_price'  => $this->default_price,
            'symbol'         => $this->_symbol
        );

        return $encode ? json_encode($params) : $params;
    }

    /**
     * @return array
     */
    public function getCountries()
    {
        $cUrl = 'http://www.russianpost.ru/autotarif/Selautotarif.aspx';
        $c    = file_get_contents($cUrl);
        preg_match('/<select>(.*?)<\/select>/si', $c, $res);

        $countries = $this->app->country->getIsoToNameMapping();
        $result    = $this->_getDefaultValue();

        foreach ($countries as $key => $country) {

            $value = JString::strtolower($this->app->country->isoToName($key));
            if ($value == 'russian federation') {
                $country = 'Russia';
                $value   = JText::_('Россия');
            }

            $result[JString::strtolower($country)]['value'] = JString::strtolower(JText::_($value));
            $result[JString::strtolower($country)]['code']  = $key;
        }

        return $result;
    }

    /**
     * Get country code by country
     *
     * @param  string $country
     *
     * @return mixed
     */
    public function countryCode($country)
    {
        $countries = $this->getCountries();
        $country   = JString::trim(JString::strtolower($country));

        if (!isset($countries[$country])) {

            foreach ($countries as $region) {

                $value = JString::trim(JString::strtolower($region['value']));
                if ($country == $value) {
                    return $region['code'];
                }
            }
        }

        return isset($countries[$country]['code']) ? $countries[$country]['code'] : null;
    }

    /**
     * @param string $fields
     */
    public function ajaxGetPrice($fields = '')
    {
        $params = json_decode($fields, true);
        $price  = $this->getPrice($params);

        $this->app->jbajax->send(array(
            'price'  => $price->get('price'),
            'symbol' => $price->get('symbol')
        ));
    }

    /**
     * Make request and get price form service
     *
     * @param  array $params
     *
     * @return int
     */
    public function getPrice($params = array())
    {
        $params = $this->mergeParams($params);
        $url    = $this->getServicePath($params);
        $data   = $this->_callService($url);

        preg_match('/<span id="TarifValue">([0-9\,\-]+)<\/span>/i', $data, $result);
        preg_match('/<span id="lblErrStr">(.*)<\/span>/i', $data, $matches);
        list($unset, $value) = $result;
        list($span, $error) = $matches;
        $price = $this->_jbmoney->convert(self::RUSSIANPOST_CURRENCY, $this->currency(), $value);

        return $this->app->data->create(array(
            'price'  => $this->_jbmoney->format($price),
            'symbol' => $this->_symbol
        ));
    }

}
