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

if (PHP_VERSION_ID >= 70000) {
    require_once __DIR__ . '/jbzoo_7.x.php'; // For latest PHP version and ioncube loader v6.x+
} else {
    require_once __DIR__ . '/jbzoo_5.x.php'; // legacy versions of PHP...
}
