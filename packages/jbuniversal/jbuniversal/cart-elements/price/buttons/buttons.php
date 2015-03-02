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
                'addLabel'      => JText::_($this->config->get('add_label', 'JBZOO_JBPRICE_ADD_TO_CART')),
                'removeLabel'   => JText::_($this->config->get('remove_label', 'JBZOO_JBPRICE_REMOVE_FROM_CART')),
                'inCart'        => ((int)$interface['isInCart'] ? 'in-cart' : null),
                'inCartVariant' => ((int)$interface['isInCartVariant'] ? 'in-cart-variant' : null)
            ));
        }

        return null;
    }

    /**
     * Get params for widget
     * @param array $params
     * @return array
     */
    public function interfaceParams($params = array())
    {
        $_interface = $this->_interfaceParams();
        return array(
            'item_id'         => $_interface['item_id'],
            'element_id'      => $_interface['element_id'],
            'key'             => $_interface['key'],
            'basket'          => $_interface['basket'],
            'isInCart'        => $_interface['isInCart'],
            'isInCartVariant' => $_interface['isInCartVariant'],
            'add'             => $this->app->jbrouter->element($_interface['element_id'], $_interface['item_id'], 'ajaxAddToCart', array(
                'template' => $this->template
            )),
            'remove'          => $this->app->jbrouter->element($_interface['element_id'], $_interface['item_id'], 'ajaxRemoveFromCart'),
            'modal'           => $this->app->jbrouter->element($_interface['element_id'], $_interface['item_id'], 'ajaxModalWindow')
        );
    }

    /**
     * Returns data when variant changes
     * @param array $params
     * @return null
     */
    public function renderAjax($params = array())
    {
        return $this->_interfaceParams();
    }

    /**
     * Load elements css/js assets
     * @return $this
     */
    public function loadAssets()
    {
        $this->js('cart-elements:price/buttons/assets/js/buttons.js');

        return parent::loadAssets();
    }

    /**
     * Get session key and check if variant is in cart
     * @return array
     */
    protected function _interfaceParams()
    {
        $cart = JBCart::getInstance();
        $key  = $this->_jbprice->getList()->session_key;

        return array(
            'item_id'         => $this->item_id,
            'element_id'      => $this->element_id,
            'key'             => $key,
            'basket'          => $this->getBasketUrl(),
            'isInCart'        => (int)$cart->inCart($this->item_id, $this->element_id),
            'isInCartVariant' => (int)$cart->inCartVariant($key),
        );
    }

    /**
     * Get basket url
     * @return null
     */
    protected function getBasketUrl()
    {
        $url  = null;
        $menu = (int)$this->_jbprice->config->get('basket_menuitem');

        $url = $this->app->jbrouter->basket($menu);

        return $url;
    }
}
