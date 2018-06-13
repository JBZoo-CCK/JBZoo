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
