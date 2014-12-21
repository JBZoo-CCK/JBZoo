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


$this->app->jbdebug->mark('template::item::start');

$this->app->jblayout->setView($this);

$this->app->jbwrapper->start();

$layout = $this->app->jbrequest->get('jbquickview');

// render item
if (!$this->app->jbcache->start(array($this->item->modified, $this->item->id))) {

    if ($this->app->jblayout->checkLayout($this->item, $layout)){

        echo $this->app->jblayout->renderItem($this->item, $layout);

    } else {

        echo $this->app->jblayout->renderItem($this->item, 'full');

        // render comments (if no rendered in element)
        if (!defined('JBZOO_COMMENTS_RENDERED_' . $this->item->id)) {
            echo $this->app->comment->renderComments($this, $this->item);
        }
    }

    $this->app->jbcache->stop();
}

$this->app->jbwrapper->end();

$this->app->jbdebug->mark('template::item::finish');
