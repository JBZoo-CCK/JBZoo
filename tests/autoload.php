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

define('PATH_ROOT', realpath(__DIR__ . '/../'));

// Main autoload
if ($autoload = realpath(PATH_ROOT . '/vendor/autoload.php')) {
    require_once $autoload;
} else {
    echo 'Please execute "make update" in project root directory';
    exit(1);
}
