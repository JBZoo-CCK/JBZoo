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
 * Class JBCartElementPriceValue
 */
class JBCartElementPriceValue extends JBCartElementPrice
{
    const IDENTIFIER = '_value';

    const PRICE_VIEW_FULL     = 1;
    const PRICE_VIEW_PRICE    = 2;
    const PRICE_VIEW_TOTAL    = 3;
    const PRICE_VIEW_DISCOUNT = 4;
    const PRICE_VIEW_SAVE     = 5;

    /**
     * Get elements search data
     * @return mixed
     */
    public function getSearchData()
    {
        $list = $this->getList();

        return $list->getTotal()->val($this->_jbmoney->getDefaultCur());
    }

    /**
     * @return mixed|null|string
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'value' => $this->getValue()
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
        $prices = $this->getPrices();
        $list   = $this->getList();

        $discount = $list->byDefault()->get('_discount', JBCart::val());
        $margin   = $list->byDefault()->get('_margin', JBCart::val());

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'mode'     => (int)$params->get('only_price_mode', 1),
                'prices'   => $prices,
                'discount' => $discount,
                'margin'   => $margin,
                'currency' => $this->currency()
            ));
        }

        return null;
    }

    /**
     * Get prices for render currency
     * @return array
     */
    public function getPrices()
    {
        $list = $this->getList();

        if ($this->_jbprice->isOverlay()) {

            $total = $list->getTotal();
            $price = $list->getPrice();

            $prices = array(
                'total' => $total,
                'price' => $price,
                'save'  => $total->minus($price, true)->abs()
            );

            return $prices;

        } else {
            
            $prices = array(
                'total' => $list->getTotal(),
                'price' => $list->getPrice(),
                'save'  => $list->getTotal()
                                ->minus($list->getPrice(), true)
                                ->abs()
            );
        }

        return $prices;
    }

    /**
     * Get prices for currencies from currency list
     * @return array
     */
    public function getCurrencyPrices()
    {
        $prices = array();
        $params = $this->getRenderParams('_currency');
        $list   = $params->get('currency_list');

        $price = $this->getPrices();

        if (!empty($list)) {
            foreach ($list as $currency) {

                $prices[$currency] = array(
                    'total' => $price['total']->html($currency),
                    'price' => $price['price']->html($currency),
                    'save'  => $price['total']->minus($price['price'], true)
                                              ->abs()
                                              ->html($currency)
                );
            }
        }

        return $prices;
    }

    /**
     * Check if variant price will modified basic price
     * @return bool
     */
    public function isModifier()
    {
        if ($this->isBasic()) {
            return false;
        }

        $value = $this->get('value', null);
        if (!empty($value) && ($value[0] === '-' || $value[0] === '+' || $value[0] === '%')) {
            return $value[0];
        }

        $value = JBCart::val($value);
        if ($value->isCur('%')) {
            return true;
        }

        return false;
    }

    /**
     * Get elements value
     * @param string $key
     * @param null   $default
     * @return mixed|JBCartValue|null
     */
    public function getValue($key = 'value', $default = null)
    {
        $value = parent::getValue($key, $default);

        return JBCart::val($value);
    }

    /**
     * Returns data when variant changes
     * @return null
     */
    public function renderAjax()
    {
        $params = $this->getRenderParams();

        return $this->render($params);
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->js('cart-elements:price/value/assets/js/value.js');

        return parent::loadAssets();
    }

}
