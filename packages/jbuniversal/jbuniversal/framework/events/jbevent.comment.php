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
 * Class JBEventComment
 */
class JBEventComment extends JBEvent
{
    /**
     * On comment init
     *
     * @param AppEvent $event
     */
    public static function init($event)
    {
    }

    /**
     * On after comment saved
     *
     * @param AppEvent $event
     */
    public static function saved($event)
    {
    }

    /**
     * On after comment deleted
     *
     * @param AppEvent $event
     */
    public static function deleted($event)
    {
    }

    /**
     * On after comment state changed
     *
     * @param AppEvent $event
     */
    public static function stateChanged($event)
    {
    }
}