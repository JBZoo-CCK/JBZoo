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

$this->app->jbdebug->mark('template::payment_success::start');
$this->app->jblayout->setView($this);
$this->app->document->setTitle(JText::_('JBZOO_PAYMENT_SUCCESS_PAGE_TITLE'));
$this->app->jbwrapper->start();

$user = JFactory::getUser();

?><h1 class="title"><?php echo JText::_('JBZOO_PAYMENT_SUCCESS_PAGE_TITLE'); ?></h1><?php


echo $this->app->jblayout->render('payment_success');


$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::payment_success::finish');
