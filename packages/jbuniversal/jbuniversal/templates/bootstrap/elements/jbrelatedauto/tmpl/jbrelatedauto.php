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
