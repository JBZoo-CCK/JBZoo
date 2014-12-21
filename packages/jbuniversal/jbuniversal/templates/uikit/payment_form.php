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


$this->app->jbdebug->mark('template::payment_form::start');
$this->app->jblayout->setView($this);
$this->app->document->setTitle(JText::_('JBZOO_PAYMENT_FORM_PAGE_TITLE'));
$this->app->jbwrapper->start();

$user = JFactory::getUser();

?><h1 class="title"><?php echo JText::_('JBZOO_PAYMENT_FORM_PAGE_TITLE'); ?></h1><?php


echo $this->app->jblayout->render('payment_form');


$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::payment_form::finish');
