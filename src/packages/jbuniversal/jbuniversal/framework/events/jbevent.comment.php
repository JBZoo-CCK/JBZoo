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