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


// load config
require_once dirname(__FILE__) . DS . 'helper.php';

$zoo = App::getInstance('zoo');

$zoo->jbdebug->mark('mod_jbzoo_basket::start-' . $module->id);

$zoo->jbassets->setAppCSS();
$zoo->jbassets->setAppJS();

// render module
include(JModuleHelper::getLayoutPath('mod_jbzoo_basket', $params->get('layout', 'default')));

$zoo->jbdebug->mark('mod_jbzoo_basket::finish-' . $module->id);
