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
