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
 * Class JBCartElementPriceDiscount
 */
class JBCartElementPriceDiscount extends JBCartElementPrice
{
    const SALE_VIEW_NO          = 0;
    const SALE_VIEW_TEXT        = 1;
    const SALE_VIEW_TEXT_SIMPLE = 2;
    const SALE_VIEW_ICON_SIMPLE = 3;
    const SALE_VIEW_ICON_VALUE  = 4;

    /**
     * Check if element has value
     *
     * @param array $params
     *
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
     * @return mixed|string
     */
    public function edit()
    {
        if ($layout = $this->getLayout('edit.php')) {
            return self::renderLayout($layout, array(
                'discount' => $this->getValue()
            ));
        }

        return null;
    }

    /**
     * @param  array $params
     *
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        $value = $this->getElement('_value');

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'params'   => $params,
                'base'     => $value->getPrices(),
                'discount' => $this->getValue(),
                'mode'     => $params->get('sale_show', self::SALE_VIEW_ICON_VALUE)
            ));
        }

        return null;
    }

    /**
     * Get elements value
     *
     * @param string $key
     * @param null   $default
     *
     * @internal param string $key
     * @return mixed|null
     */
    public function getValue($key = 'value', $default = null)
    {
        $value = parent::getValue($key, $default);

        return JBCart::val($value)->abs();
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
