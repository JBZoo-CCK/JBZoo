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

$this->app->jbdoc->noindex();

$this->app->jbdebug->mark('template::tag::start');

$this->app->jblayout->setView($this);

if (!$this->app->jbcache->start($this->tag)) {
    $this->app->jbwrapper->start();

    ?><h1 class="title"><?php echo JText::_('JBZOO_ARTICLES_TAGGED_WITH') . ': ' . $this->tag; ?></h1><?php

    // items
    if (count($this->items) > 0) {
        echo $this->app->jblayout->render('items', $this->items);
    }

    // pagination render
    echo $this->app->jblayout->render('pagination', $this->pagination, array('link' => $this->pagination_link));

    $this->app->jbwrapper->end();
    $this->app->jbcache->stop();
}

$this->app->jbdebug->mark('template::tag::finish');