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

    echo '<div class="items maincatjbzoo flexcatdiv items-col-' . $vars['cols_num'] . '">';

    foreach ($rowItems as $row) {
        echo '<article class="row ' . $rowClass . 'item-row-' . $i . '">';
    
        $j = 0;
        $i++;
    
        foreach ($row as $item) {

    
            $classes = array(
                'col', $colClass, 'item-column'
            );
    
            // Добавляем классы для первого и последнего элемента в строке
            if ($j == 0) {
                $classes[] = 'first';
            }
            if ($j == count($row) - 1) {
                $classes[] = 'last';
            }
    
            // Проверяем, является ли элемент последним в строке
            $isLast = ($j + 1) % $vars['cols_num'] == 0;
    
            if ($isLast && $vars['cols_order'] == 0) {
                $classes[] = 'last';
            }
            
            echo $item; 
    
            $j++;
        }
    
        echo '</article>';
    }
    

    echo '</div>';
}

$this->app->jbdebug->mark('layout::item_columns::finish');