<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
 * Class JBEventBasket
 */
class JBEventBasket extends JBEvent
{
    /**
     * On after order saved
     * @param AppEvent $event
     * @throws \JBCartOrderException
     * @return bool
     */
    public static function saved($event)
    {
        $app = self::app();
        $app->jbeventmanager->fireListeners();

        $order = $event->getSubject();
        $items = (array)$order->getItems();

        if (!empty($items)) {
            foreach ($items as $orderData) {
                /** @type Item $item */
                $item = $app->table->item->get($orderData->get('item_id'));
                /**@type ElementJBPrice $element */
                if ($element = $item->getElement($orderData->get('element_id'))) {
                    if ($element instanceof ElementJBPrice) {
                        try {
                            $element->setTemplate($orderData->get('template'));

                            // Create variant object
                            $list = $element->getList($orderData->get('variations'), array(
                                'template' => $orderData->get('template')
                            ));
                            $variant = $list->current();
                            /** @type JBCartElementPriceBalance $balance */
                            if (!$balance = $variant->get('_balance')) {
                                continue;
                            }

                            if ($balance->reduce((float)$orderData->get('quantity'))) {
                                $element->bindVariant($variant);
                                $app->table->item->save($item);
                            }

                        } catch (JBCartOrderException $e) {
                            $app->jbnotify->warning(JText::_($e->getMessage()));
                        }
                    }
                }

                if ($item && is_a($element, 'ElementJBAdvert') && $elements = $item->getElementsByType('jbadvert')) {
                    foreach ($elements as $element) {
                        if (method_exists($element, 'modify')) {
                            $element->modify();
                        }
                    }
                }
            }
        }
    }

    /**
     * On before order save
     * @param AppEvent $event
     */
    public static function beforeSave($event)
    {
    }

    /**
     * On order status changed
     * @param AppEvent $event
     */
    public static function orderStatus($event)
    {
        $app    = self::app();
        $order  = $event->getSubject();
        $params = $event->getParameters();

        $cartConf = JBModelConfig::model()->getGroup('cart.' . JBCart::CONFIG_STATUS_EVENTS);
        $elements = $cartConf->get(JBCart::STATUS_ORDER . '__' . $params['newStatus'], array());

        foreach ($elements as $config) {
            $element = $app->jbcartelement->create($config['type'], $config['group'], $config);
            $element->setOrder($order);

            if (method_exists($element, 'notify')) {
                $element->notify($order, $params);
            }
        }
    }

    /**
     * On payment status changed
     * @param AppEvent $event
     */
    public static function paymentStatus($event)
    {
        $app    = self::app();
        $order  = $event->getSubject();
        $params = $event->getParameters();

        $cartConf = JBModelConfig::model()->getGroup('cart.' . JBCart::CONFIG_STATUS_EVENTS);
        $elements = $cartConf->get(JBCart::STATUS_PAYMENT . '__' . $params['newStatus'], array());

        foreach ($elements as $config) {
            $element = $app->jbcartelement->create($config['type'], $config['group'], $config);
            $element->setOrder($order);

            if (method_exists($element, 'notify')) {
                $element->notify($order, $params);
            }
        }
    }

    /**
     * On shipping status changed
     * @param AppEvent $event
     */
    public static function shippingStatus($event)
    {
        $app    = self::app();
        $order  = $event->getSubject();
        $params = $event->getParameters();

        $cartConf = JBModelConfig::model()->getGroup('cart.' . JBCart::CONFIG_STATUS_EVENTS);
        $elements = $cartConf->get(JBCart::STATUS_SHIPPING . '__' . $params['newStatus'], array());

        foreach ($elements as $config) {
            $element = $app->jbcartelement->create($config['type'], $config['group'], $config);
            $element->setOrder($order);

            if (method_exists($element, 'notify')) {
                $element->notify($order, $params);
            }
        }
    }


}