<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Class JBOrderMacrosHelper
 */
class JBOrderMacrosHelper extends AppHelper
{
    /**
     * @type array
     */
    protected $_list = array(
        'time',
        'date',
        'datetime',

        'order_id',
        'order_name',
        'order_status',
        'order_total',
        'order_created_id',
        'order_created_name',
        'order_payment_name',
        'order_payment_stat',
        'order_shipping_name',
        'order_shipping_stat',

        'user_id',
        'user_name',

        'site_name',
        'site_desc',
        'site_url',
        'site_link',

        'shop_name',
        'shop_desc',
        'shop_country',
        'shop_city',
        'shop_address',
        'shop_zip',
    );

    /**
     * Get macros list with description
     */
    public function getList()
    {
        $result = array();

        foreach ($this->_list as $macros) {
            $key   = '{' . $macros . '}';
            $value = JText::_('JBZOO_ORDER_MACROS_' . strtoupper($macros));

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param string      $text
     * @param JBCartOrder $order
     * @return mixed
     */
    public function renderText($text, $order)
    {
        foreach ($this->_list as $macros) {
            $text = $this->_replaceMacros($text, $macros, $order);
        }

        return $text;
    }

    /**
     * @param string      $text
     * @param string      $macros
     * @param JBCartOrder $order
     * @return mixed
     * @throws Exception
     */
    private function _replaceMacros($text, $macros, JBCartOrder $order = null)
    {
        if ($macros == 'date') {
            $date    = new JDate();
            $replace = $date->format(JText::_('DATE_FORMAT_LC3'));

        } else if ($macros == 'time') {
            $date    = new JDate();
            $replace = $date->format('H:i');

        } else if ($macros == 'datetime') {
            $date    = new JDate();
            $replace = $date->format('Y-m-d H:m');

        } else if ($macros == 'order_id' && $order) {
            $replace = $order->getName('short');

        } else if ($macros == 'order_name' && $order) {
            $replace = $order->getName('full');

        } else if ($macros == 'order_status' && $order) {
            $replace = $order->getStatus()->getName();

        } else if ($macros == 'order_total' && $order) {
            $replace = $order->getTotalSum()->text();

        } else if ($macros == 'order_payment_name' && $order) {
            $replace = JText::_('JBZOO_UNDEFINED');
            if ($payment = $order->getPayment()) {
                $replace = $payment->getName();
            }

        } else if ($macros == 'order_payment_stat' && $order) {
            $replace = $order->getPaymentStatus()->getName();

        } else if ($macros == 'order_shipping_name' && $order) {
            $replace = JText::_('JBZOO_UNDEFINED');
            if ($shipping = $order->getShipping()) {
                $replace = $shipping->getName();
            }

        } else if ($macros == 'order_shipping_stat' && $order) {
            $replace = $order->getShippingStatus()->getName();

        } else if ($macros == 'user_id') {
            $replace = (int)JFactory::getUser()->id;

        } else if ($macros == 'user_name') {
            $juser   = JFactory::getUser();
            $replace = ($juser->id > 0) ? $juser->get('name') : JText::_('JBZOO_UNDEFINED');

        } else if ($macros == 'order_created_id') {
            $replace = $order->created_by;

        } else if ($macros == 'order_created_name') {
            $juser   = JFactory::getUser($order->created_by);
            $replace = ($juser->id > 0) ? $juser->name : JText::_('JBZOO_UNDEFINED');

        } else if ($macros == 'site_name') {
            $replace = JFactory::getConfig()->get('sitename', '');

        } else if ($macros == 'site_desc') {
            $replace = JFactory::getConfig()->get('MetaDesc', '');

        } else if ($macros == 'site_url') {
            $replace = JUri::root();

        } else if ($macros == 'site_link') {
            $sitename = JString::trim(JFactory::getConfig()->get('sitename'));
            $replace  = '<a href="' . JUri::root() . '" target="_blank">' . $sitename . '</a>';

        } else if ($macros == 'shop_name') {
            $replace = JBModelConfig::model()->get('shop_name', '', 'cart.config');

        } else if ($macros == 'shop_desc') {
            $replace = JBModelConfig::model()->get('shop_details', '', 'cart.config');

        } else if ($macros == 'shop_country') {
            $replace = JBModelConfig::model()->get('default_shipping_country', '', 'cart.config');

        } else if ($macros == 'shop_city') {
            $replace = JBModelConfig::model()->get('default_shipping_city', '', 'cart.config');

        } else if ($macros == 'shop_address') {
            $replace = JBModelConfig::model()->get('default_shipping_address', '', 'cart.config');

        } else if ($macros == 'shop_zip') {
            $replace = JBModelConfig::model()->get('default_shipping_zip', '', 'cart.config');

        } else {
            throw new Exception('Undefined email macros: "{' . $macros . '}"');
        }

        if (isset($replace)) {
            $replace = JString::trim($replace);
            $macros  = preg_quote('{' . trim($macros) . '}');
            $text    = preg_replace('#' . $macros . '#ius', $replace, $text);
        }

        return $text;
    }

}
