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

echo $filterHelper->createRenderer()->render('item.' . $filterHelper->getItemLayout(), array(
    'params'      => $params,
    'type'        => $filterHelper->getType(),
    'layout'      => $filterHelper->getItemLayout(),
    'application' => $this->app->table->application->get($filterHelper->getAppId()),
));
