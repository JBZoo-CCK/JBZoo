<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 * @coder       Oganov Alexander <t_tapakm@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once dirname(__FILE__) . '/helper.php';

$itemHelper = new JBZooItemHelper();
$zoo        = App::getInstance('zoo');
$unique     = $zoo->jbstring->getId('module-items-');
$itemHelper->loadType($params);

$zoo->jbdebug->mark('mod_jbzoo_item::start');
$items = $itemHelper->getItems();

// renderer module
$renderer = $zoo->renderer->create('item')->addPath(array(
    $zoo->path->path('component.site:'),
    dirname(__FILE__)
));

// render module
include(JModuleHelper::getLayoutPath('mod_jbzoo_item', $params->get('layout', 'default')));

$zoo->jbdebug->mark('mod_jbzoo_item::finish');
