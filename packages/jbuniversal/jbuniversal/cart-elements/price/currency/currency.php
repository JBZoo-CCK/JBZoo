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
        $list = $params->get('currency_list', array());
        if (!empty($list)) {
            return true;
        }

        return false;
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
        $template = $params->get('template', 'currency');

        $list    = (array)$params->get('currency_list', array());
        $default = $params->get('currency_default', 'EUR');

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'list'    => $list,
                'default' => $default,
                'rates'   => array_intersect_key((array)$this->_jbmoney->getData(), array_flip($list))
            ));
        }

        return null;
    }

    /**
     * Get elements value
     * @param string $key
     * @param null   $default
     * @return mixed|null
     */
    public function getValue($key = 'value', $default = null)
    {
        return JBCart::val()->cur();
    }

    /**
     * Get JBPrice class
     * @return string
     */
    public function parentSelector()
    {
        return '.' . $this->options('hash');
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
        $this->js(array(
            'jbassets:js/widget/money.js',
            'jbassets:js/widget/currencytoggle.js',
        ));

        $this->less('jbassets:less/widget/currencytoggle.less');

        return parent::loadAssets();
    }
}
