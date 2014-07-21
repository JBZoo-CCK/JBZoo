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
 * Class JBCartElementPriceValue
 */
class JBCartElementPriceValue extends JBCartElementPrice
{
    /**
     * @return mixed|null|string
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'currencyList' => $this->app->jbmoney->getCurrencyList(true)
            ));
        }

        return null;
    }

    /**
     * @param array $params
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $params   = $this->app->data->create($params);
        $prices   = $this->getPrices();
        $discount = $this->getBasic('_discount');

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params'   => $params,
                'base'     => $prices,
                'discount' => array(
                    'value'  => (float)$discount['value'],
                    'format' => $this->app->jbmoney->toFormat($discount['value'], $discount['currency']),
                )
            ));
        }

        return null;
    }

    /**
     * @param null $identifier
     * @param $name
     * @return string
     */
    public function getBasicName($identifier = null, $name)
    {

        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][basic][{$name}]";
    }

    /**
     * @param null $identifier
     * @param $name
     * @param  int $index
     * @return string
     */
    public function getParamName($identifier = null, $name, $index = 0)
    {

        if (empty($identifier)) {
            $identifier = $this->identifier;
        }

        return "elements[{$identifier}][variations][{$index}][{$name}]";
    }

    public function getValue($key, $default = null)
    {
        $data = $this->app->data->create($this->config->get('data'));

        return $data->get($key);
    }

    /**
     * @return array
     */
    public function getPrices()
    {
        $currencyDefault = $this->_jbprice->config->get('default_currency', 'EUR');
        $basicCurrency   = $this->getBasic('currency');

        $jbmoney = $this->app->jbmoney;
        $data    = $this->getBasic('_discount');

        $curr     = $data['currency'];
        $discount = $data['value'];
        $value    = $this->getBasic('_value');

        $basePrice = $jbmoney->calcDiscount($value, $basicCurrency, $discount, $curr);

        $total         = $jbmoney->convert($basicCurrency, $currencyDefault, $basePrice);
        $totalNoFormat = $jbmoney->convert($basicCurrency, $curr, $total);

        $priceNoFormat = $jbmoney->convert($basicCurrency, $curr, $value);
        $price         = $jbmoney->toFormat($priceNoFormat, $curr);

        $saveNoFormat = abs($totalNoFormat - $priceNoFormat);
        $save         = $jbmoney->toFormat($saveNoFormat, $curr);


        $prices = array(
            'totalNoFormat' => $totalNoFormat,
            'priceNoFormat' => $priceNoFormat,
            'saveNoFormat'  => $saveNoFormat,
            'total'         => $total,
            'price'         => $price,
            'save'          => $save
        );

        return $prices;
    }
}
