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
        return (!$this->isEmpty() || (int)$params->get('empty_show', 0));
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
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout, array(
                'value'   => $this->get('value', ''),
                'message' => JText::sprintf('JBZOO_JBPRICE_CALC_PARAM_CANT_USE', '<strong>' . $this->getElementType() . '</strong>')
            ));
        }

        return null;
    }

    /**
     * Renders the element
     * TODO add protected method for preparing $discount variable
     * @param JSONData|array $params
     * @return mixed|string
     */
    public function render($params = array())
    {
        if(!$this->hasValue($params)) {
            return $this->renderWrapper();
        }

        $prices = $this->getPrices();
        $message = JString::trim($params->get('empty_text', ''));
        $layout  = $params->get('layout', 'icon-text');
        if ((int)$params->get('percent_show', 1)) {
            $price    = $prices['price'];
            $save     = $prices['save']->abs();
            $discount = $save->percent($price);
        } else {
            $discount = $prices['save'];
        }

        if ((int)$params->get('percent_round', 1)) {
            $discount->setFormat(array(
                'round_value'  => 0,
                'num_decimals' => 0,
                'round_type'   => 'classic'
            ));
        }

        if ((int)$params->get('is_negative', 1)) {
            $discount->negative();
        } else {
            $discount->positive();
        }

        if ($discount->isEmpty() && !empty($message)) {
            $layout = 'empty';
        }

        if ($layout = $this->getLayout($layout . '.php')) {
            return $this->renderLayout($layout, array(
                'discount' => $discount,
                'currency' => $this->currency(),
                'message'  => $message
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
        $value = $this->clearSymbols($value, array('-', '+'));

        if ($toString) {
            return $value;
        }

        return JBCart::val($value);
    }

    /**
     * @return bool
     */
    protected function isEmpty()
    {
        $prices = $this->getPrices();

        return $prices['save']->isEmpty();
    }

}
