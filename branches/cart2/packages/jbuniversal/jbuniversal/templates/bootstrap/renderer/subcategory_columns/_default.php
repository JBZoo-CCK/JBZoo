<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$this->app->jbdebug->mark('layout::subcategory_columns::start');

if ($vars['count']) {

    $i         = 0;
    $bootstrap = $this->app->jbbootstrap;
    $count     = $vars['count'];
    $rowClass  = $bootstrap->getRowClass();
    $colClass  = $bootstrap->columnClass($vars['cols_num']);

    echo '<div class="subcategories subcategory-col-' . $vars['cols_num'] . '">';

    $rowSubcategories = array_chunk($vars['objects'], $vars['cols_num']);

    foreach ($rowSubcategories as $row) {

        echo '<div class="jsHeightFixRow ' . $rowClass . ' subcategory-row-' . $i . '">';

        $j = 0;
        $i++;

        foreach ($row as $subcategory) {

            $classes = array(
                'subcategory-column', $colClass
            );

            $first = ($j == 0) ? $classes[] = 'first' : '';
            $last  = ($j == $count - 1) ? $classes[] = 'last' : '';
            $j++;

            $isLast = $j % $vars['cols_num'] == 0 && $vars['cols_order'] == 0;

            if ($isLast) {
                $classes[] = 'last';
            }

            echo '<div class="' . implode(' ', $classes) . '">' .
                 '  <div class="subcategory-box well">' . $subcategory . '</div>' .
                 '</div>';
        }

        echo '</div>';
    }

    echo '<hr /></div>';
}

$this->app->jbdebug->mark('layout::subcategory_columns::finish');