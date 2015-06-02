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
     * Get params for widget
     * @param array $params
     * @return array
     */
    public function interfaceParams($params = array())
    {
        $interface = $this->_interfaceParams($params);
        $jbrouter  = $this->app->jbrouter;

        return array(
            'hash'            => $this->hash,
            'item_id'         => $interface['item_id'],
            'element_id'      => $interface['element_id'],
            'key'             => $interface['key'],
            'basket'          => $interface['basket'],
            'isModal'         => $interface['isModal'],
            'isInCart'        => $interface['isInCart'],
            'addAlert'        => (int)$this->config->get('add_alert', 0),
            'addAlertText'    => $this->config->get('add_alert_text', JText::_('JBZOO_ELEMENT_PRICE_BUTTONS_ADD_ALERT_TEXT_DEFAULT')),
            'addAlertTimeout' => $this->app->jbvars->number($this->config->get('add_alert_timeout', 3)) * 1000,
            'remove'          => $jbrouter->element($interface['element_id'], $interface['item_id'], 'ajaxRemoveFromCart'),
            'add'             => $jbrouter->element($interface['element_id'], $interface['item_id'], 'ajaxAddToCart'),
            'modal'           => $jbrouter->element($interface['element_id'], $interface['item_id'], 'ajaxModalWindow', array(
                    'template' => $params->get('modal_layout', 'modal'),
                    'layout'   => $this->layout,
                    'hash'     => $this->hash
                )) . '&tmpl=component&modal=1', // Joomla hack
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
     * Get session key and check if variant is in cart
     * @param AppData|array $params
     * @return array
     */
    protected function _interfaceParams($params = array())
    {
        $cart = JBCart::getInstance();
        $key  = $this->getJBPrice()->getList()->getSessionKey();

        return array(
            'key'        => $key,
            'item_id'    => $this->item_id,
            'element_id' => $this->element_id,
            'isModal'    => $this->_isModal(),
            'basket'     => $this->_getBasketUrl(),
            'isInCart'   => (int)$cart->inCart($this->item_id, $this->element_id)
        );
    }

    /**
     * Get basket url
     * @return null
     */
    protected function _getBasketUrl()
    {
        return $this->app->jbrouter->basket();
    }

    /**
     * @return bool
     */
    protected function _isModal()
    {
        return (bool)$this->app->jbrequest->is('modal', '1');
    }
}
