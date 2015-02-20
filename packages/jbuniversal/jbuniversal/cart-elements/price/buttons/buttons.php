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
                'params'        => $params,
                'inCart'        => ((int)$interface['isInCart'] ? 'in-cart' : null),
                'inCartVariant' => ((int)$interface['isInCartVariant'] ? 'in-cart-variant' : null)
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
        $params  = $this->getRenderParams();
        $options = $this->get('_options');

        $item_id    = $options->get('item_id');
        $element_id = $options->get('element_id');
        $_interface = $this->_interfaceParams();

        return array(
            'key'              => $_interface['key'],
            'basket'           => $_interface['basket'],
            'isInCart'         => $_interface['isInCart'],
            'isInCartVariant'  => $_interface['isInCartVariant'],
            'add'              => $this->app->jbrouter->element($element_id, $item_id, 'ajaxAddToCart', array(
                'template' => $options->get('template')
            )),
            'remove'           => $this->app->jbrouter->element($element_id, $item_id, 'ajaxRemoveFromCart'),
            'modal'            => $this->app->jbrouter->element($element_id, $item_id, 'ajaxModalWindow'),
            'canRemoveVariant' => (int)$params->get('remove_variant', 0)
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
        self::addToStorage(array('cart-elements:price/buttons/assets/js/buttons.js'));

        return parent::loadAssets();
    }

    /**
     * Get session key and check if variant is in cart
     * @return array
     */
    protected function _interfaceParams()
    {
        $cart    = JBCart::getInstance();
        $options = $this->get('_options');
        $key     = $options->get('session_key');

        return array(
            'key'             => $key,
            'basket'          => $this->getBasketUrl(),
            'isInCart'        => (int)$cart->inCart($options->get('item_id'), $options->get('element_id')),
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
