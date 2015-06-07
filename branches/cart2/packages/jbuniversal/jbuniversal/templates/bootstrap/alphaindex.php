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

$this->app->jbdebug->mark('template::alphaindex::start');

$this->app->jblayout->setView($this);

$title = JText::_('JBZOO_STARTING_WITH') . ' "' . $this->app->string->strtoupper($this->alpha_char) . '"';

$this->app->document->setTitle($title);

if (!$this->app->jbcache->start($this->alpha_char)) {
    $this->app->jbwrapper->start();

    echo '<h1 class="title">' . $title . '</h1>';

    // alphaindex
    if ($this->params->get('template.show_alpha_index', 0)) {
        echo $this->app->jblayout->render('alphaindex', $this->alpha_index);
    }

    // categories list
    if ($this->params->get('template.subcategories_show', 1) && count($this->selected_categories)) {
        echo '<h2 class="subtitle">' . JText::_('JBZOO_FOUND_AMONG_CATEGORIES') . '</h2>';
        echo $this->app->jblayout->render('subcategories', $this->selected_categories);
    }

    // items list
    if ($this->params->get('config.items_show', 1) && count($this->items)) {
        echo '<h2 class="subtitle">' . JText::_('JBZOO_FOUND_AMONG_ITEMS') . '</h2>';
        echo $this->app->jblayout->render('items', $this->items);
    }

    // pagination render
    echo $this->app->jblayout->render('pagination', $this->pagination, array(
        'url'        => $this->pagination_link,
        'pagination' => $this->pagination
    ));

    $this->app->jbwrapper->end();
    $this->app->jbcache->stop();
}

$this->app->jbdebug->mark('template::alphaindex::finish');
