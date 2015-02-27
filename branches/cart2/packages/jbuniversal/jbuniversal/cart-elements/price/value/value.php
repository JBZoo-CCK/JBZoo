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
        $value = $this->get('value', 0);
        if (!isset($value) || empty($value)) {
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
        $prices = $this->getPrices();

        return $prices['total'];
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

        $discount = JBCart::val();
        if ($prices['save'] < 0) {
            $discount = $prices['save'];
        };

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'mode'     => (int)$params->get('only_price_mode', 1),
                'total'    => JBCart::val($prices['total']),
                'price'    => JBCart::val($prices['price']),
                'save'     => JBCart::val($prices['save'])->abs(),
                'discount' => JBCart::val($discount)->abs(),
                'currency' => $this->currency()
            ));
        }

        return null;
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
     * @param array $params
     * @return null
     */
    public function renderAjax($params = array())
    {
        return $this->render($params);
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->js('cart-elements:price/value/assets/js/value.js');

        return parent::loadAssets();
    }

}
