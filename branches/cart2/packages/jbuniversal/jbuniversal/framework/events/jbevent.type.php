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
 * Class JBEventType
 */
class JBEventType extends JBEvent
{
    /**
     * On type before save
     * @param AppEvent $event
     */
    public static function beforeSave($event)
    {
        $itemType = $event->getSubject();
        self::app()->jbtables->checkTypeBeforeSave($itemType);
    }

    /**
     * On type after save
     * @param AppEvent $event
     */
    public static function afterSave($event)
    {
        $itemType = $event->getSubject();
        self::app()->jbtables->checkTypeAfterSave($itemType);
    }

    /**
     * On type copied
     * @param AppEvent $event
     */
    public static function copied($event)
    {

    }

    /**
     * On type deleted
     * @param AppEvent $event
     */
    public static function deleted($event)
    {

    }

    /**
     * On type edit display
     * @param AppEvent $event
     */
    public static function editDisplay($event)
    {

    }

    /**
     * On type assign elements
     * @param AppEvent $event
     */
    public static function assignElements($event)
    {

    }

    /**
     * On type core config
     * @param AppEvent $event
     */
    public static function coreConfig($event)
    {

    }

}