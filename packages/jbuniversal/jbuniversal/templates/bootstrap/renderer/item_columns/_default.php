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

$this->app->jbdebug->mark('layout::item_columns::start');

if ($vars['count']) {

    $i         = 0;
    $bootstrap = $this->app->jbbootstrap;
    $count     = $vars['count'];
    $rowItems  = array_chunk($vars['objects'], $vars['cols_num']);
    $rowClass  = $bootstrap->getRowClass();
    $colClass  = $bootstrap->columnClass($vars['cols_num']);

    echo '<div class="items items-col-' . $vars['cols_num'] . '">';

    foreach ($rowItems as $row) {
        echo '<div class="' . $rowClass . ' item-row-' . $i . '">';

        $j = 0;
        $i++;

        foreach ($row as $item) {

            $classes = array(
                'item-column', $colClass
            );

            $first = ($j == 0) ? $classes[] = 'first' : '';
            $last  = ($j == $count - 1) ? $classes[] = 'last' : '';
            $j++;

            $isLast = $j % $vars['cols_num'] == 0 && $vars['cols_order'] == 0;

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

$this->app->jbdebug->mark('layout::item_columns::finish');