<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$count = count($items);

if ($count) {

    $appParams = $this->getItem()->getApplication()->params;
    $bootstrap = $this->app->jbbootstrap;

    if ((int)$appParams->get('global.config.column_heightfix', 0)) {
        $this->app->jbassets->heightFix('.item-box');
    }

    $i = 0;
    $rowItems = array_chunk($items, $columns);
    $rowClass = $bootstrap->getRowClass();
    $colClass = $bootstrap->columnClass($columns);

    echo '<div class="items items-col-' . $columns . '">';
        foreach ($rowItems as $row) {
            echo '<div class="' . $rowClass . ' item-row-' . $i . '">';

            $i++;
            $j = 0;

            foreach ($row as $item) {

                $classes = array(
                    'item-column', $colClass
                );

                $first = ($j == 0) ? $classes[] = 'first' : '';
                $last  = ($j == $count - 1) ? $classes[] = 'last' : '';
                $j++;

                $isLast = $j % $columns == 0;

                if ($isLast) {
                    $classes[] = 'last';
                }

                echo '<div class="' . implode(' ', $classes) . '">' .
                     '   <div class="item-box well">' . $item . '</div>' .
                     '</div>';
            }

            echo '</div>';
        }
    echo '</div>';

}
