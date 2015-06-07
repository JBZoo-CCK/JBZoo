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
    if ((int)$appParams->get('global.config.column_heightfix', 0)) {
        $this->app->jbassets->heightFix('.uk-panel');
    }

    $i = 0;
    $rowItems = array_chunk($items, $columns);

    echo '<div class="items items-col-' . $columns . ' uk-article-divider">';
        foreach ($rowItems as $row) {
            echo '<div class="uk-grid item-row-' . $i . '" data-uk-grid-margin>';

            $i++;
            $j = 0;

            foreach ($row as $item) {

                $first = ($j == 0) ? ' first' : '';
                $last  = ($j == $count - 1) ? ' last' : '';
                $j++;

                $isLast = $j % $columns == 0;

                if ($isLast) {
                    $last .= ' last';
                }

                echo '<div class="item-column uk-width-medium-1-' . $columns . $first . $last . '">' .
                        '<div class="uk-panel uk-panel-box">' .
                            $item .
                        '</div>' .
                    '</div>';
            }

            echo '</div>';
        }
    echo '</div>';

}
