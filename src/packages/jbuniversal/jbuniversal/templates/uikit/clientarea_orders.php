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

$this->app->jbdoc->noindex();

$this->app->jbdebug->mark('template::orders::start');
$this->app->jblayout->setView($this);
$this->app->jbwrapper->start();

echo '<h1 class="title">' . JText::_('JBZOO_CLIENTAREA_ORDERS_TITLE') . '</h1>';

// items
if (count($this->orders) > 0) {
    echo $this->app->jblayout->render('clientarea_orders', $this->orders);
} else {
    echo JText::_('JBZOO_CLIENTAREA_ORDERS_EMPTY');
}

$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::orders::finish');
