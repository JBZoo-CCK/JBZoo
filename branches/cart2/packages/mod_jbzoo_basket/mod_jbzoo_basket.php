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


// load helper
require_once dirname(__FILE__) . '/helper.php';

$zoo = App::getInstance('zoo');
$zoo->jbdebug->mark($module->module . '::start-' . $module->id);

// init & render module
$modHelper = new JBModuleHelperBasket($params, $module);
echo $modHelper->render(true);

$zoo->jbdebug->mark($module->module . '::finish-' . $module->id);
