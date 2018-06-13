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

if (!defined('PROJECT_ROOT')) { // for PHPUnit process isolation
    !defined('PROJECT_ROOT') && define('PROJECT_ROOT', realpath('.'));
    !defined('PROJECT_SRC') && define('PROJECT_SRC', PROJECT_ROOT . DS . 'src');
    !defined('PROJECT_TESTS') && define('PROJECT_TESTS', PROJECT_ROOT . DS . 'tests');
}

// Main autoload
if ($autoload = realpath('./vendor/autoload.php')) {
    require_once $autoload;
} else {
    echo 'Please execute "make update" in project root directory';
    exit(1);
}
