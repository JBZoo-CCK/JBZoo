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
     * Check if element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $value = $this->getValue();
        if ($value->isEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * Get elements search data
     * @return mixed
     */
    public function getSearchData()
    {
        $list = $this->getList();

        return $list->getTotal();
    }

    /**
     * @param array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout, array(
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

        $total = $prices['total'];
        $price = $prices['price'];
        $save  = $prices['save'];

        $discount = JBCart::val();

        if ($save->isNegative()) {
            $discount = $save->getClone();
        };

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'mode'     => (int)$params->get('only_price_mode', 1),
                'total'    => $total,
                'price'    => $price,
                'save'     => $save->abs(),
                'discount' => $discount->abs(),
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
        $list  = $this->getList();
        $total = $list->getTotal();
        $price = $list->getPrice();

        if ($this->_jbprice->isOverlay()) {

            $prices = array(
                'total' => $total,
                'price' => $price,
                'save'  => $total->minus($price, true)
            );

        } else {

            $prices = array(
                'total' => $total,
                'price' => $price,
                'save'  => $total->minus($price, true)
            );
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

        return $this->getHelper()->isModifier($value);
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
