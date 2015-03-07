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
 * Class JBCartElementPriceBalance
 */
class JBCartElementPriceBalance extends JBCartElementPrice
{
    const NOT_AVAILABLE = 0;
    const AVAILABLE     = -1;
    const UNDER_ORDER   = -2;

    /**
     * Check if element has value
     *
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

        return $value;
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
     * @param array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $template = $params->get('template', 'simple');
        $useStock = (int)$this->config->get('balance_mode', 1);

        if ($layout = $this->getLayout($template . '.php')) {
            return $this->renderLayout($layout, array(
                'balance'   => $this->getValue(),
                'textNo'    => '<span class="not-available">' . JText::_('JBZOO_JBPRICE_NOT_AVAILABLE') . '</span>',
                'textYes'   => '<span class="available">' . JText::_('JBZOO_JBPRICE_AVAILABLE') . '</span>',
                'textOrder' => '<span class="under-order">' . JText::_('JBZOO_JBPRICE_BALANCE_UNDER_ORDER') . '</span>',
                'useStock'  => $useStock
            ));
        }

        return null;
    }

    /**
     * Check if item in stock
     *
     * @param $quantity
     *
     * @return bool
     */
    public function inStock($quantity)
    {
        if (!(int)$this->config->get('balance_mode', 1)) {
            return true;
        }

        $quantity = (float)$quantity;
        $inStock  = $this->getValue();

        if ($inStock == self::AVAILABLE) {
            return true;

        } elseif (($inStock == self::NOT_AVAILABLE) || ($inStock == self::UNDER_ORDER)) {
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
        $value = (float)$this->getValue();
        if (!(int)$this->config->get('balance_mode', 1) || $value == self::AVAILABLE) {
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
}
