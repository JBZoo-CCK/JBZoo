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
    /**
     * Check if element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $value = $this->get('value', '');

        return ($value !== '') || ((int)$params->get('empty_show', 1) && $value === '0');    }

    /**
     * Get elements search data
     * @return mixed
     */
    public function getSearchData()
    {
        $prices = $this->getPrices();
        $total  = JBCart::val($prices['total']);

        return $total->convert('eur');
    }

    /**
     * @param array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderEditLayout($layout, array(
                'value' => $this->get('value', '')
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
        $prices   = $this->getPrices();
        $discount = JBCart::val();
        if ($prices['save'] < 0) {
            $discount = $prices['save'];
        }

        if ($layout = $this->getLayout($params->get('layout', 'full_div') . '.php')) {
            return $this->renderLayout($layout, array(
                'total'    => JBCart::val($prices['total']),
                'price'    => JBCart::val($prices['price']),
                'save'     => JBCart::val($prices['save'])->abs(),
                'discount' => JBCart::val($discount)->abs(),
                'currency' => $this->currency(),
                'message'  => JString::trim($params->get('empty_text', ''))
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
     * Set data through data array.
     * @param  array  $data
     * @param  string $key
     * @return $this
     */
    public function bindData($data = array(), $key = 'value')
    {
        if (is_string($data)) {
            $data = array($key => $data);
        }

        foreach ($data as $key => $value) {
            if ($this->isBasic()) {
                $value = $this->clearSymbols($value);
            }
            $this->set($key, $value);
        }

        return $this;
    }
}
