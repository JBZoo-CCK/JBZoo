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
 * Class JBCartElementHook
 */
abstract class JBCartElementHook extends JBCartElement
{
    protected $_namespace = JBCart::ELEMENT_TYPE_HOOK;

    /**
     * @type AppEvent|null
     */
    protected $_event = null;

    /**
     * @param array $params
     * @return bool
     */
    public function hasValue($params = array())
    {
        return true;
    }

    /**
     * @param AppEvent $event
     */
    public function setEvent(AppEvent $event)
    {
        $this->_event = $event;
    }

    /**
     * @return AppEvent|null
     */
    public function getEvent()
    {
        return $this->_event;
    }

    /**
     * Execute element
     * @param $params
     * @return
     */
    abstract function notify($params = array());

}

/**
 * Class JBCartElementHookException
 */
class JBCartElementHookException extends JBCartElementException
{
}
