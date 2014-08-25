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
 * Class JBCartElementNotification
 */
abstract class JBCartElementNotification extends JBCartElement
{
    protected $_namespace = JBCartOrder::ELEMENT_TYPE_NOTIFICATION;
}

/**
 * Class JBCartElementNotificationException
 */
class JBCartElementNotificationException extends JBCartElementException
{
}
