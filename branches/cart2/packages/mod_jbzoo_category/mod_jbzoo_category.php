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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


// load config
require_once dirname(__FILE__) . '/helper.php';

$categoryHelper = new JBZooCategoryHelper($params);
$zoo            = App::getInstance('zoo');

$zoo->jbdebug->mark('mod_jbzoo_category::start-' . $module->id);

$zoo->jbassets->setAppCSS();
$zoo->jbassets->setAppJS();

$categories = $categoryHelper->getCategories();

// renderer module
$renderer = $zoo->renderer->create('item')->addPath(array(
    $zoo->path->path('component.site:'),
    dirname(__FILE__)
));

// render module
include(JModuleHelper::getLayoutPath('mod_jbzoo_category', $params->get('layout', 'default')));

$zoo->jbdebug->mark('mod_jbzoo_category::finish-' . $module->id);
