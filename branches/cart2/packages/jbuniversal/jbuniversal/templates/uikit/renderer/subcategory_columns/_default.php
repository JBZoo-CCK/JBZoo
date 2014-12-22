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


$this->app->jbdebug->mark('layout::subcategory_columns::start');

if ($vars['count']) {
    $i = 0;
    $count = $vars['count'];

    echo '<div class="subcategories subcategory-col-' . $vars['cols_num'] . ' uk-article-divider">';

    $rowSubcategories = array_chunk($vars['objects'], $vars['cols_num']);

    foreach ($rowSubcategories as $row) {

        echo '<div class="uk-grid subcategory-row-' . $i . '" data-uk-grid-margin>';

        $j = 0;
        $i++;

        foreach ($row as $subcategory) {

            $first = ($j == 0) ? ' first' : '';
            $last  = ($j == $count - 1) ? ' last' : '';
            $j++;

            $isLast = $j % $vars['cols_num'] == 0 && $vars['cols_order'] == 0;

            if ($isLast) {
                $last .= ' last';
            }

            echo '<div class="subcategory-column uk-width-medium-1-' . $vars['cols_num'] . $first . $last . '">' .
                    '<div class="uk-panel uk-panel-box">' .
                        $subcategory .
                    '</div>' .
                '</div>';
        }

        echo '</div>';
    }

    echo '<hr /></div>';
}

$this->app->jbdebug->mark('layout::subcategory_columns::finish');