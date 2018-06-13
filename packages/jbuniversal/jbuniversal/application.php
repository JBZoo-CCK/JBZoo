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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (version_compare(PHP_VERSION, '7.0', '>=')) {
    require_once __DIR__ . '/application_7.x.php'; // For latest PHP version and ioncube loader v6.x+
} else {
    require_once __DIR__ . '/application_5.x.php'; // legacy versions of PHP...
}
