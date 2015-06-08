<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 * @coder       Sergey Kalistratov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


if ($link) {
    echo '<a ' . $linkAttrs . '><img ' . $imageAttrs . ' /></a> ' . PHP_EOL;
} else {
    echo '<img ' . $imageAttrs . ' /> ' . PHP_EOL;
}
