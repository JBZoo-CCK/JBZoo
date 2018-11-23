<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
