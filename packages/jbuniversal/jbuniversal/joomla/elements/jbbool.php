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


$helper = App::getInstance('zoo')->jbfield;

if ($helper->isGlobal($node, $parent, $name)) {
    echo $helper->boolGlobal($name, $value, $control_name, $node, $parent);
} else {
    echo $helper->bool($name, $value, $control_name, $node, $parent);
}
