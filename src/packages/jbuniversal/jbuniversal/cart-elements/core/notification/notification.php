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
