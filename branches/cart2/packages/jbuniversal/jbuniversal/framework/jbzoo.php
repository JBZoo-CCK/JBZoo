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

if (version_compare(PHP_VERSION, '7.0', '>=')) {
    require_once __DIR__ . '/jbzoo_7.x.php'; // For latest PHP version and ioncube loader v6.x+
} else {
    require_once __DIR__ . '/jbzoo_5.x.php'; // legacy versions of PHP...
}
