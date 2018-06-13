<?php

use Symfony\Component\Finder\Finder;

require_once __DIR__ . '/../vendor/autoload.php';

$validHeader = '<?xml version="1.0" encoding="UTF-8" ?>
<!--
    JBZoo Application

    This file is part of the JBZoo CCK package.
    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.

    @package    Application
    @license    GPL-2.0
    @copyright  Copyright (C) JBZoo.com, All rights reserved.
    @link       https://github.com/JBZoo/JBZoo
-->';

$finder = new Finder();
$finder
    ->files()
    ->in(__DIR__ . '/../packages')
    ->name('*.xml');

$rep = preg_quote('-->', null);
$reg = "#(.*?{$rep})#ius";

/** @var \SplFileInfo $file */
foreach ($finder as $file) {
    $code = file_get_contents($file->getPathname());
    $code = preg_replace($reg, $validHeader, $code);
    file_put_contents($file->getPathname(), $code);
    echo $file->getPathname() . PHP_EOL;
}
