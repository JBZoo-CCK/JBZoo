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

$helper = App::getInstance('zoo')->jbfield;

if ($helper->isGlobal($node, $parent, $name)) {
    echo $helper->boolGlobal($name, $value, $control_name, $node, $parent);
} else {
    echo $helper->bool($name, $value, $control_name, $node, $parent);
}
