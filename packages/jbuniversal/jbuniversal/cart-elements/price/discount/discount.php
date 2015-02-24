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
        $value = $this->getValue();

        return (int)$value->isEmpty();
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function edit($params = array())
    {
        $layout = 'edit';
        if ($this->options('isOverlay', 0)) {
            $layout = 'disabled';
        }

        if ($layout = $this->getLayout($layout . '.php')) {
            return self::renderEditLayout($layout, array(
                'discount' => $this->getValue(),
                'message'  => JText::sprintf('JBZOO_JBPRICE_CALC_PARAM_CANT_USE', '<strong>' . $this->getElementType() . '</strong>')
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
        $prices = $this->getPrices();
        if (!$prices['save'] < 0) {
            $prices['save'] = 0;
        }

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'discount' => JBCart::val($prices['save'])->positive(),
                'mode'     => $params->get('sale_show', self::SALE_VIEW_ICON_VALUE),
                'currency' => $this->currency()
            ));
        }

        return null;
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

}
