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
 * @since 2.2
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
        return (!$this->isEmpty()) || ((int)$params->get('empty_show', 0));
    }

    /**
     * Get elements search data
     * @return mixed
     */
    public function getSearchData()
    {
        $prices = $this->getPrices();

        return $prices['total']->val(JBModelConfig::model()->getCurrency());
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
        if(!$this->hasValue($params)) {
            return $this->renderWrapper();
        }
        $prices   = $this->getPrices();
        $discount = JBCart::val();
        if ($prices['save']->isNegative()) {
            $discount->set($prices['save']->val(), $prices['save']->cur());
        }

        $total   = $prices['total'];
        $message = JText::_(JString::trim($params->get('empty_text', '')));

        $layout = $params->get('layout', 'full-div');
        if ($total->isEmpty() && !empty($message)) {
            $layout = 'empty';
        }

        if ($layout = $this->getLayout($layout . '.php')) {
            return $this->renderLayout($layout, array(
                'total'    => $total,
                'price'    => $prices['price'],
                'save'     => $prices['save']->abs(true),
                'discount' => $discount->abs(),
                'currency' => $this->currency(),
                'message'  => $message
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
        if (!is_array($data)) {
            $data = array($key => (string)$data);
        }

        foreach ($data as $key => $value) {
            if ($this->isBasic()) {
                $value = $this->clearSymbols($value);
            }
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function isEmpty()
    {
        $prices = $this->getPrices();

        return $prices['total']->isEmpty();
    }
}
