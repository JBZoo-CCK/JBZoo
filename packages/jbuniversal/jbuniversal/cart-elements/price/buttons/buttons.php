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
        $add_html  = $popup_html = $oneClick_html = $goto_html = '';
        $interface = $this->_interfaceParams();

        $tpl_add      = (int)$params->get('template_add', 1);
        $tpl_popup    = (int)$params->get('template_popup', 0);
        $tpl_oneClick = (int)$params->get('template_oneclick', 0);
        $tpl_goto     = (int)$params->get('template_goto', 1);

        $_params = array(
            'addLabel'      => JText::_($this->config->get('add_label', 'JBZOO_JBPRICE_BUTTONS_ADD_TO_CART')),
            'popupLabel'    => JText::_($this->config->get('popup_label', 'JBZOO_JBPRICE_BUTTONS_ADD_TO_CART_POPUP')),
            'oneClickLabel' => JText::_($this->config->get('oneclick_label', 'JBZOO_JBPRICE_BUTTONS_ADD_TO_CART_GOTO')),
            'goToLabel'     => JText::_($this->config->get('goto_label', 'JBZOO_JBPRICE_BUTTONS_ADD_TO_CART_GOTO')),
            'basketUrl'     => $interface['basket'],
            'inCart'        => ((int)$interface['isInCart'] ? 'in-cart' : null),
            'inCartVariant' => ((int)$interface['isInCartVariant'] ? 'in-cart-variant' : null)
        );

        // Render simple add template
        if($tpl_add && $add_layout = $this->getLayout('add.php'))
        {
            $add_html = self::renderLayout($add_layout, $_params);
        }

        // Render popup template
        if($tpl_popup && $popup_layout = $this->getLayout('popup.php'))
        {
            $popup_html = self::renderLayout($popup_layout, $_params);
        }

        // Render oneclick template
        if($tpl_oneClick && $oneClick_layout = $this->getLayout('oneclick.php'))
        {
            $oneClick_html = self::renderLayout($oneClick_layout, $_params);
        }

        // Render goto template
        if($tpl_goto && $goto_layout = $this->getLayout('goto.php'))
        {
            $goto_html = self::renderLayout($goto_layout, $_params);
        }

        if (($add_html || $popup_html || $oneClick_html || $goto_html) &&
            $layout = parent::getLayout('buttons.php')
        ) {
            return $this->renderLayout($layout, array(
                'add_html'      => $add_html,
                'popup_html'    => $popup_html,
                'oneClick_html' => $oneClick_html,
                'goto_html'     => $goto_html,
                'inCart'        => $_params['inCart'],
                'removeLabel'   => JText::_($this->config->get('remove_label', 'JBZOO_JBPRICE_REMOVE_FROM_CART'))
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
     * Renders the element using template layout file
     * @param string $__layout layouts template file
     * @param array  $__args   layouts template file args
     * @return string
     */
    protected function renderLayout($__layout, $__args = array())
    {
        // init vars
        if (is_array($__args)) {
            foreach ($__args as $__var => $__value) {
                $$__var = $__value;
            }
        }

        // render layout
        $__html = '';
        ob_start();
        include($__layout);
        $__html = ob_get_contents();
        ob_end_clean();

        return $__html;
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
