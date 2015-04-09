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
 * Class JBCartElementPriceDiscount
 */
class JBCartElementPriceDiscount extends JBCartElementPrice
{
    /**
     * Check if element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return (!$this->getValue()->isEmpty()) || ((int)$params->get('show_empty', 1));
    }

    /**
     * Get elements search data
     * @return mixed
     */
    public function getSearchData()
    {
        return (int)(!JBCart::val($this->get('value', 0))->isEmpty());
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function edit($params = array())
    {
        $layout = $this->isOverlay ? 'disabled' : 'edit';
        if ($layout = $this->getLayout($layout . '.php')) {
            return self::renderEditLayout($layout, array(
                'value'   => $this->get('value', ''),
                'message' => JText::sprintf('JBZOO_JBPRICE_CALC_PARAM_CANT_USE', '<strong>' . $this->getElementType() . '</strong>')
            ));
        }

        return null;
    }

    /**
     * Renders the element
     * @param JSONData|array $params
     * @return mixed|string
     */
    public function render($params = array())
    {
        $prices = $this->getPrices();

        $message = JString::trim($params->get('empty_text', ''));

        if ((int)$params->get('percent_show', 1)) {
            $price    = JBCart::val($prices['price']);
            $save     = JBCart::val($prices['save'])->abs();
            $discount = $save->percent($price);
        } else {
            $discount = JBCart::val($prices['save']);
        }

        if ((int)$params->get('percent_round', 1)) {
            $discount->setFormat(array(
                'round_value'  => 0,
                'num_decimals' => 0,
                'round_type'   => 'classic',
            ));
        }

        $discount->negative();

        $layout = $params->get('layout', 'icon-text');
        if ($discount->isEmpty() && !empty($message)) {
            $layout = 'empty';
        }

        if ($layout = $this->getLayout($layout . '.php')) {
            return self::renderLayout($layout, array(
                'discount' => $discount->positive(),
                'currency' => $this->currency(),
                'message'  => $message,
            ));
        }

        return null;
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
     * Get elements value
     * @param string $key      Array key.
     * @param mixed  $default  Default value if data is empty.
     * @param bool   $toString A string representation of the value.
     * @return mixed|string
     */
    public function getValue($toString = false, $key = 'value', $default = null)
    {
        $value = parent::getValue($toString, $key, $default);

        if ($this->isBasic()) {
            $value = $this->clearSymbols($value);
        }

        if ($toString) {
            return $value;
        }

        return JBCart::val($value);
    }

}
