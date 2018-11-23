<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class JBCartElementPaymentYandexKassaEmail
 */
class JBCartElementPaymentYandexKassaEmail extends JBCartElementPayment
{
    /**
     * Payment uri
     * @var string
     */
    private $_uri = 'https://money.yandex.ru/eshop.xml';

    /**
     * Redirect to payment form action
     * @return null|string
     */
    public function getRedirectUrl()
    {
        $summa = $this->getOrderSumm()->val($this->config->get('currency', 'rub'));
        $summa = number_format($summa, 2, '.', '');
        $order = JBModelOrder::model()->getById($this->getOrder()->id);
        $getTotalSum = JBModelOrder::model()->getById($order->id)->getTotalSum()->val();
        $getStatus = $order->getStatus();
        $getShippingStatus = $order->getShippingStatus();
        $getPaymentStatus = $order->getPaymentStatus();
        $orderShip = $order->getShippingFields();
        $getShipping = $order->getShipping();
        $fields = $order->getFields();

        $UserTel = $fields->get($this->config->get('cps_phone'))->value;
        $UserEmail = $fields->get($this->config->get('cps_email'))->value;
        $taxSystem = $this->config->get('taxSystem');
        $tax = $this->config->get('tax');
        $cartItems = $order->getItems(false);

        foreach ($cartItems as $cartItem) {
            $itemPrice = $order->val($cartItem->get('total'));
            $specialcharsname = addslashes(($cartItem->get('item_name')));
            $itemId = $cartItem->item_id;
            $itemName = $cartItem->item_name;
            $itemQuantity = $cartItem->quantity;
            $queryitems .= "{\"quantity\":{$itemQuantity},\"price\":{\"amount\":{$itemPrice->val()}},\"tax\":\"{$tax}\",\"text\":\"{$specialcharsname}\"},";
        }

        $queryitems =  substr($queryitems, 0, -1);
            
        $query = array(
            'shopId'         => JString::trim($this->config->get('shopId')),
            'scid'           => JString::trim($this->config->get('scid')),
            'sum'            => $summa,
            'customerNumber' => 'UserID ' . JFactory::getUser()->id,
            'orderNumber'    => $this->getOrder()->id,
            'cps_phone'    => $UserTel,
            'cps_email'    => $UserEmail,
            'ym_merchant_receipt'    => '{"customerContact":"'.$UserEmail.'","taxSystem":'.$taxSystem.',"items":['.$queryitems.']}',
        );

        $paymentType = $this->config->get('paymentType', 'AC');
        if ($paymentType && $paymentType != 'none') {
            $query['paymentType'] = $paymentType;
        }

        return $this->_uri . '?' . $this->_jbrouter->query($query);
    }

    /**
     * Set payment rate
     * @return JBCartValue
     */
    public function getRate()
    {
        return $this->_order->val($this->config->get('rate', '3.5%'));
    }

}
