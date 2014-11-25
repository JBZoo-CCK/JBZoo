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
     * @param  array $params
     *
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'params' => $params
            ));
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
        $params   = $this->app->data->create($params);
        $template = $params->get('template', 'simple');

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'textNo'    => '<span class="not-available">' . JText::_('JBZOO_JBPRICE_NOT_AVAILABLE') . '</span>',
                'textYes'   => '<span class="available">' . JText::_('JBZOO_JBPRICE_AVAILABLE') . '</span>',
                'textOrder' => '<span class="under-order">' . JText::_('JBZOO_JBPRICE_BALANCE_UNDER_ORDER') . '</span>'
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

        if ($inStock == -1) {
            return true;

        } elseif (($inStock == self::NOT_AVAILABLE) || ($inStock == self::UNDER_ORDER)) {
            return false;

        } elseif ($inStock >= $quantity) {
            return true;
        }

        return false;
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

}
