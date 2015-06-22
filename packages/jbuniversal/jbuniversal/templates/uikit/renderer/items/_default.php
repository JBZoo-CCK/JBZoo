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


if ((int)$this->getView()->application->params->get('global.config.column_heightfix', 0)) {
    $this->app->jbassets->heightFix('.jbzoo-item');
}

$this->app->jbdebug->mark('layout::items::start');

echo $this->columns('item', $vars['objects'], true);

$this->app->jbdebug->mark('layout::items::finish');
