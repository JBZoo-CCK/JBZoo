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
     * Displaying nn a modal window or not
     * @type bool
     */
    public $isModal;

    /**
     * Constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->isModal = (bool)$this->app->jbrequest->is('modal', '1');
    }

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
        $interface = $this->_interfaceParams($params);

        $add      = (int)$params->get('template_add', 1);
        $goto     = (int)$params->get('template_goto', 1);
        $popUp    = (int)$params->get('template_popup', 0);
        $oneClick = (int)$params->get('template_oneclick', 0);

        if (($add || $goto || $popUp || $oneClick) &&
            $layout = $this->getLayout('buttons.php')
        ) {
            return $this->renderLayout($layout, array(
                'add'           => $add,
                'popup'         => $popUp,
                'oneClick'      => $oneClick,
                'goto'          => $goto,
                'isModal'       => $this->isModal,
                'basketUrl'     => $interface['basket'],
                'inCart'        => ((int)$interface['isInCart'] ? 'in-cart' : null),
                'addLabel'      => JText::_($this->config->get('add_label', 'JBZOO_JBPRICE_BUTTONS_ADD_TO_CART')),
                'popupLabel'    => JText::_($this->config->get('popup_label', 'JBZOO_JBPRICE_BUTTONS_ADD_TO_CART_POPUP')),
                'oneClickLabel' => JText::_($this->config->get('oneclick_label', 'JBZOO_JBPRICE_BUTTONS_ADD_TO_CART_GOTO')),
                'goToLabel'     => JText::_($this->config->get('goto_label', 'JBZOO_JBPRICE_BUTTONS_ADD_TO_CART_GOTO')),
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
        $_interface = $this->_interfaceParams($params);

        return array(
            'item_id'    => $_interface['item_id'],
            'element_id' => $_interface['element_id'],
            'key'        => $_interface['key'],
            'hash'       => $this->hash,
            'basket'     => $_interface['basket'],
            'isModal'    => $_interface['isModal'],
            'isInCart'   => $_interface['isInCart'],
            'add'        => $this->app->jbrouter->element($_interface['element_id'], $_interface['item_id'], 'ajaxAddToCart', array(
                'template' => $this->template
            )),
            'modal'      => $this->app->jbrouter->element($_interface['element_id'], $_interface['item_id'], 'ajaxModalWindow', array(
                    'template' => $params->get('template_modal', 'default'),
                    'layout'   => $this->getJBPrice()->layout()
                )) . '&tmpl=component&modal=1',
            'remove'     => $this->app->jbrouter->element($_interface['element_id'], $_interface['item_id'], 'ajaxRemoveFromCart'),
        );
    }

    /**
     * Returns data when variant changes
     * @param array $params
     * @return null
     */
    public function renderAjax($params = array())
    {
        return $this->_interfaceParams($params);
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
     * @param AppData|array $params
     * @return array
     */
    protected function _interfaceParams($params = array())
    {
        $cart = JBCart::getInstance();
        $key  = $this->_jbprice->getList()->getSessionKey();

        return array(
            'item_id'    => $this->item_id,
            'element_id' => $this->element_id,
            'key'        => $key,
            'basket'     => $this->getBasketUrl(),
            'isModal'    => $this->isModal,
            'isInCart'   => (int)$cart->inCart($this->item_id, $this->element_id),
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
