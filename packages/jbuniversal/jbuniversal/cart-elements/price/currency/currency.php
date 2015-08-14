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
 * Class JBCartElementPriceCurrency
 */
class JBCartElementPriceCurrency extends JBCartElementPrice
{
    /**
     * Check if element has value
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $list = $this->_getRates($params);

        return count($list) >= 2;
    }

    /**
     * Get elements search data
     * @return null
     */
    public function getSearchData()
    {
        if ($element = $this->getElement('_value')) {
            return $element->getValue()->cur();
        }

        return false;
    }

    /**
     * @param array $params
     * @return mixed|null|string
     */
    public function edit($params = array())
    {
        return null;
    }

    /**
     * @param array $params
     * @return array|mixed|null|string
     */
    public function render($params = array())
    {
        if (!$this->hasValue($params)) {
            return null;
        }

        $rates   = $this->_getRates($params);
        $default = $params->get('currency_default', JBCartValue::DEFAULT_CODE);

        if ($layout = $this->getLayout()) {
            return self::renderLayout($layout, array(
                'rates'       => $rates,
                'default'     => $default,
                'showDefault' => in_array(JBCartValue::DEFAULT_CODE, $rates, true)
            ));
        }

        return null;
    }

    /**
     * @param $params
     * @return array
     */
    protected function _getRates($params)
    {
        $list     = (array)$params->get('currency_list', array());
        $fullList = (array)$this->app->jbmoney->getData();

        if (in_array('all', $list, true)) {
            return $fullList;
        }

        return array_intersect_key($fullList, array_flip($list));
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
        return JBCart::val()->cur();
    }

    /**
     * Get JBPrice class
     * @return string
     */
    public function parentSelector()
    {
        return '.' . $this->hash;
    }

    /**
     * Get params for widget
     * @param array $params
     * @return array
     */
    public function interfaceParams($params = array())
    {
        return array(
            'default' => $params->get('currency_default'),
            'target'  => '.jsCurrencyToggle'
        );
    }

    /**
     * @return $this
     */
    public function loadAssets()
    {
        // for cache mode
        $this->js(array(
            'jbassets:js/widget/money.js',
            'jbassets:js/widget/currencytoggle.js'
        ));

        $this->less('jbassets:less/widget/currencytoggle.less');

        return parent::loadAssets();
    }

}
