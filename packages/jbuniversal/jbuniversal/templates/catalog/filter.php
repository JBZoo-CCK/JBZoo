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


$this->app->jbdebug->mark('template::filter::start');

$this->app->jblayout->setView($this);
$this->app->jbassets->tabs();
if (!$this->app->jbcache->start()) {
    $this->app->jbwrapper->start();

    ?><h1 class="title"><?php echo JText::_('JBZOO_SEARCH_RESULT'); ?></h1><?php

    if ($this->items) {

        echo '<p>' . JText::_('JBZOO_FILTER_TOTAL_RESULT') . ': ' . $this->itemsCount . '</p>';

        // items
        echo $this->app->jblayout->render('items', $this->items);

        // pagination render
        echo $this->app->jblayout->render('pagination', $this->pagination, array('link' => $this->pagination_link));

    } else {
        echo $this->app->jbjoomla->renderPosition('jbzoo_price_filter');
        ?><p><?php echo JText::_('JBZOO_FILTER_ITEMS_NOT_FOUND'); ?></p><?php

    }

    $this->app->jbwrapper->end();
    $this->app->jbcache->stop();
}

$this->app->jbdebug->mark('template::filter::finish');
