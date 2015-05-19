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
 * Class JBCartElementNotification
 */
abstract class JBCartElementNotification extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_NOTIFICATION;

    /**
     * @type JBOrderMacrosHelper
     */
    protected $_macros = null;

    /**
     * Class constructor
     * @param App    $app
     * @param string $type
     * @param string $group
     */
    public function __construct($app, $type, $group)
    {
        parent::__construct($app, $type, $group);

        $this->_macros = $this->app->jbordermacros;
    }

    /**
     * Messages only for existing orders!
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        $order = $this->getOrder();

        if (!$order || !$order->id) {
            return false;
        }

        return true;
    }

    /**
     * Launch notification
     * @return void
     */
    abstract function notify();

}

/**
 * Class JBCartElementNotificationException
 */
class JBCartElementNotificationException extends JBCartElementException
{
}
