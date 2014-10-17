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
 * Class JBCartElementPriceValue
 */
class JBCartElementPriceValue extends JBCartElementPrice
{
    /**
     * @return mixed|null|string
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'currencyList' => $this->app->jbmoney->getCurrencyList(TRUE)
            ));
        }

        return NULL;
    }

    /**
     * @param array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $params   = $this->app->data->create($params);
        $prices   = $this->getPrices();
        $discount = $this->getElementData('_discount');

        $currencyParams  = $this->getRenderParams('_currency');
        $defaultCurrency = $currencyParams->get('default_currency');

        $value    = $discount->get('value', 0);
        $currency = $discount->get('currency', $defaultCurrency);

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params'   => $params,
                'base'     => $prices,
                'discount' => array(
                    'value'  => $discount->get('value', 0),
                    'format' => $this->app->jbmoney->toFormat($value, $currency),
                )
            ));
        }

        return NULL;
    }

}
