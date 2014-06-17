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


$this->app->jbdebug->mark('template::favorite::start');
$this->app->jblayout->setView($this);
$this->app->document->setTitle(JText::_('JBZOO_FAVORITE_ITEMS'));
$this->app->jbwrapper->start();

?><h1 class="title"><?php echo JText::_('JBZOO_FAVORITE_ITEMS'); ?></h1><?php

if (!empty($this->items)) {
    // items
    echo $this->app->jblayout->render('favorite', $this->items);
    echo '<p class="jsJBZooFavoriteEmpty" style="display:none;">' . JText::_('JBZOO_FAVORITE_ITEMS_NOT_FOUND') . '</p>';
} else {
    echo '<p>' . JText::_('JBZOO_FAVORITE_ITEMS_NOT_FOUND') . '</p>';
}

$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::favorite::finish');
