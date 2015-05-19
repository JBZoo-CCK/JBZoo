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
 * Class JBEventBasket
 */
class JBEventBasket extends JBEvent
{
    /**
     * @param AppEvent $event
     */
    public static function beforeSave($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function saved($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function afterSave($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function updated($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function orderStatus($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function shippingStatus($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function paymentStatus($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function paymentCallback($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function paymentSuccess($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function paymentFail($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function addItem($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function updateItem($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function changeQuantity($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function removeItem($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function removeVariant($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function removeItems($event)
    {
        self::app()->jbevent->fireElements($event);
    }

    /**
     * @param AppEvent $event
     */
    public static function recount($event)
    {
        self::app()->jbevent->fireElements($event);
    }

}
