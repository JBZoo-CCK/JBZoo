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


$this->app->jbdebug->mark('layout::item_columns::start');

if ($vars['count']) {

    $i        = 0;
    $count    = $vars['count'];
    $rowItems = array_chunk($vars['objects'], $vars['cols_num']);

    echo '<div class="items items-col-' . $vars['cols_num'] . ' uk-article-divider">';

    foreach ($rowItems as $row) {
        echo '<div class="jsHeightFixRow uk-grid item-row-' . $i . '" data-uk-grid-margin>';

        $j = 0;
        $i++;

        foreach ($row as $item) {

            $classes = array(
                'item-column',
                'uk-width-medium-1-' . $vars['cols_num']
            );

            $first = ($j == 0) ? $classes[] = 'first' : '';
            $last  = ($j == $count - 1) ? $classes[] = 'last' : '';
            $j++;

            $isLast = $j % $vars['cols_num'] == 0 && $vars['cols_order'] == 0;

            if ($isLast) {
                $classes[] = 'last';
            }

            echo '<div class="' . implode(' ', $classes) . '">'
                . '<div class="uk-panel uk-panel-box">' . $item . '</div>'
                . '</div>';
        }

        echo '</div>';
    }

    echo '</div>';
}

$this->app->jbdebug->mark('layout::item_columns::finish');