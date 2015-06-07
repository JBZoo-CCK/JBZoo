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


$this->app->jbdebug->mark('template::category::start');

$this->app->jblayout->setView($this);
$currentView = $this->app->jbrequest->get('view', 'category');
$currentTask = $this->app->jbrequest->get('task', 'category');

if (isset($this->category)) {
    if ($currentView == 'frontpage' || $currentTask == 'frontpage') {
        $category = $this->application;
    } else {
        $category = $this->category;
    }
}

if (!$this->app->jbcache->start($this->params->get('config.lastmodified'))) {
    $this->app->jbwrapper->start();

    // category render
    if (isset($category)) {
        echo $this->app->jblayout->render($currentView, $category);
    }

    // alphaindex render
    if ($this->params->get('template.show_alpha_index', 0)) {
        echo $this->app->jblayout->render('alphaindex', $this->alpha_index);
    }

    // subcategories render
    if (isset($category)) {
        $categories = $this->category->getChildren();
        if ($this->params->get('template.subcategory_show', 1) && count($categories)) {
            echo $this->app->jblayout->render('subcategories', $categories);
        }
    }

    // category items render
    if ($this->params->get('config.items_show', 1) && count($this->items)) {

        if (isset($category) && $this->params->get('config.show_feed_link', 1) && $currentView == 'category') {
            $link = $this->params->get('config.alternate_feed_link');
            if (!$link && isset($category->application_id)) {
                $link = $this->app->route->feed($category, 'rss');
                $link = JRoute::_($link);

                echo '<a class="rsslink" target="_blank" href="' . $link . '" title="' . JText::_('RSS feed') . '">' .
                    JText::_('RSS feed') . '</a>';

                echo JBZOO_CLR;
            }
        }

        echo $this->app->jblayout->render('items', $this->items);

    } else {
        echo $this->app->jblayout->render('items_empty', $category);
    }

    // pagination render
    if ($this->params->get('template.item_pagination', 1)) {
        echo $this->app->jblayout->render('pagination', $this->pagination, array('link' => $this->pagination_link));
    }

    $this->app->jbwrapper->end();
    $this->app->jbcache->stop();
}

$this->app->jbdebug->mark('template::category::finish');