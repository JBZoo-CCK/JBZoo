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


$app = App::getInstance('zoo');

if ($app->jbversion->joomla('3')) {
    echo App::getInstance('zoo')->jbfield->menuitems_j3($name, $value, $control_name, $node, $parent);
} else {
    echo App::getInstance('zoo')->jbfield->menuitems_j25($name, $value, $control_name, $node, $parent);
}
