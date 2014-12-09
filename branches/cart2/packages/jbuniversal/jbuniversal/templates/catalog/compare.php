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


$this->app->jbdebug->mark('template::compare::start');
$this->app->jblayout->setView($this);
$this->app->document->setTitle(JText::_('JBZOO_COMPARE_ITEMS'));
$this->app->jbwrapper->start();

?><h1 class="title"><?php echo JText::_('JBZOO_COMPARE_ITEMS'); ?></h1><?php

if (!empty($this->items)) {
    // items
    echo '<div class="jbcompare-wrapper rborder">';
    echo $this->app->jblayout->render('compare', $this->items);
    echo '</div>';

} else {
    echo '<p>' . JText::_('JBZOO_COMPARE_ITEMS_NOT_FOUND') . '</p>';
}

$this->app->jbwrapper->end();
$this->app->jbdebug->mark('template::compare::finish');
