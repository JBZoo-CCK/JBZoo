<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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

$zoo = App::getInstance('zoo');

$zoo->jbdebug->mark($module->module . '::start-' . $module->id);

// init & render module
$modHelper = new JBModuleHelperCategory($params, $module);
echo $modHelper->render(false);

$zoo->jbdebug->mark($module->module . '::finish-' . $module->id);
