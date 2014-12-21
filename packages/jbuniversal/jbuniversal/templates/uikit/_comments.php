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


$this->app->jbdebug->mark('template::_comments::start');

echo $this->app->jblayout->render(
    'comments', $comments, array(
        'active_author' => $active_author,
        'comments'      => $comments,
        'captcha'       => $captcha,
        'params'        => $params,
        'item'          => $item,
    )
);

$this->app->jbdebug->mark('template::_comments::finish');