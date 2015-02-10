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

// load config
require_once dirname(__FILE__) . DS . 'helper.php';

$zoo = App::getInstance('zoo');

$zoo->jbdebug->mark('mod_jbzoo_currency::start-' . $module->id);

$zoo->jbassets->less('modules:mod_jbzoo_currency/assets/less/styles.less');

$currencyHelper = new JBZooCurrencyModuleHelper($params, $module);

// render module
include(JModuleHelper::getLayoutPath('mod_jbzoo_currency', $params->get('layout', 'switcher-buttons')));

$zoo->jbdebug->mark('mod_jbzoo_currency::finish-' . $module->id);
