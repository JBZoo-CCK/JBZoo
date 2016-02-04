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
 * Class JBCartElementPriceBalance
 */
class JBCartElementPriceBalance extends JBCartElementPrice
{
    const COUNT_AVAILABLE_NO  = 0;
    const COUNT_AVAILABLE_YES = -1;
    const COUNT_REQUEST       = -2;

    const AVAILABLE    = 1;
    const NO_AVAILABLE = 0;

    /**
     * Check if element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * Get elements search data
     * @return mixed
     */
    public function getSearchData()
    {
        $value = $this->getValue();

        if ($value == self::COUNT_REQUEST) {
            return self::COUNT_REQUEST;
        }

        if ($value == self::COUNT_AVAILABLE_YES || $value > 0) {
            return self::AVAILABLE;
        }

        return self::NO_AVAILABLE;
    }

    /**
     * @param array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            $this->app->jbassets->js('cart-elements:price/balance/assets/js/edit.js');

            return self::renderEditLayout($layout);
        }

        return null;
    }

    /**
     * Check if item in stock
     * @param $quantity
     * @return bool
     */
    public function inStock($quantity)
    {
        if (!$this->_isUseStock()) {
            return true;
        }

        $quantity = $this->app->jbvars->number($quantity);
        $inStock  = $this->getValue();

        if ($inStock == self::COUNT_AVAILABLE_YES) {
            return true;

        } elseif (($inStock == self::COUNT_AVAILABLE_NO) || ($inStock == self::COUNT_REQUEST)) {
            return false;

        } elseif ($inStock >= $quantity) {
            return true;
        }

        return false;
    }

    /**
     * Reduce balance from element after order saved
     * @param int $quantity How much items was ordered
     * @return bool
     */
    public function reduce($quantity)
    {
        $quantity = $this->app->jbvars->number($quantity);

        $value = $this->getValue();
        if (!$this->_isUseStock() || $value == self::COUNT_AVAILABLE_YES) {
            return true;
        }

        if ($value >= $quantity) {
            $value -= $quantity;
            $this->bindData(array('value' => $value));

            return true;
        }

        return false;
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
        if (!is_array($data) && $data !== '' && $data !== null) {
            $data = array($key => $this->app->jbvars->number($data));
        }

        foreach ($data as $key => $value) {
            $this->set($key, $this->app->jbvars->number($value));
        }

        return $this;
    }

    /**
     * @return int
     */
    protected function _isUseStock()
    {
        return (int)$this->config->get('usestock', 1);
    }

    /**
     * @return array
     */
    protected function _getList()
    {
        return array(
            self::COUNT_AVAILABLE_NO  => JText::_('JBZOO_ELEMENT_PRICE_BALANCE_EDIT_AVAILABLE_NO'),
            self::COUNT_AVAILABLE_YES => JText::_('JBZOO_ELEMENT_PRICE_BALANCE_EDIT_UNLIMITED'),
            self::COUNT_REQUEST       => JText::_('JBZOO_ELEMENT_PRICE_BALANCE_EDIT_REQUEST'),
        );
    }
}
