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
 * Class JBCartElementPriceButtons
 */
class JBCartElementPriceButtons extends JBCartElementPrice
{
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
     * @param array $params
     *
     * @return bool
     */
    public function hasFilterValue($params = array())
    {
        return false;
    }

    /**
     * @param  array $params
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
        $template  = $params->get('template', 'add');
        $interface = $this->_interfaceParams();

        if ($layout = $this->getLayout($template . '.php')) {
            return self::renderLayout($layout, array(
                'params' => $params,
                'inCart' => ((int)$interface['isInCart'] ? 'in-cart' : null)
            ));
        }

        return null;
    }

    /**
     * Get params for widget
     * @return array
     */
    public function interfaceParams()
    {
        $jbPrice = $this->_jbprice;

        $item   = $jbPrice->getItem();
        $params = $this->_interfaceParams();

        return array(
            'key'      => $params['key'],
            'isInCart' => $params['isInCart'],
            'add'      => $this->app->jbrouter->element($jbPrice->identifier, $item->id, 'ajaxAddToCart', array(
                'template' => $jbPrice->getTemplate()
            )),
            'remove'   => $this->app->jbrouter->element($jbPrice->identifier, $item->id, 'ajaxRemoveFromCart'),
            'modal'    => $this->app->jbrouter->element($jbPrice->identifier, $item->id, 'ajaxModalWindow'),
        );
    }

    /**
     * Returns data when variant changes
     * @return null
     */
    public function renderAjax()
    {
        return $this->_interfaceParams();
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->app->jbassets->js('cart-elements:price/buttons/assets/js/buttons.js');
        return parent::loadAssets();
    }

    /**
     * Get session key and check if variant is in cart
     * @return array
     */
    protected function _interfaceParams()
    {
        $cart = JBCart::getInstance();
        $key  = $this->getList()->getSessionKey();

        return array(
            'key'      => $key,
            'isInCart' => (int)$cart->inCart($key)
        );
    }
}
