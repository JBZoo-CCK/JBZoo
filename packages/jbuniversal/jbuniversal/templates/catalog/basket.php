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


$this->app->jbdebug->mark('template::basket::start');
$this->app->jblayout->setView($this);
$this->app->document->setTitle(JText::_('JBZOO_CART_ITEMS'));
$this->app->jbwrapper->start();

$user = JFactory::getUser();

?><h1 class="title"><?php echo JText::_('JBZOO_CART_ITEMS'); ?></h1><?php

if (!empty($this->items)) {

    // items
    echo $this->app->jblayout->render('basket', $this->items);

    if ((int)$this->params->get('global.jbzoo_cart_config.auth', 0) && !$user->id) {
        $this->app->jbnotify->warning(JText::_('JBZOO_CART_NOT_AUTH'));

    } else if (!empty($this->items)) {
        echo $this->app->jblayout->render('basketform', $this->items);

    }


} else {
    echo '<p>' . JText::_('JBZOO_CART_ITEMS_NOT_FOUND') . '</p>';
}

$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::basket::finish');
