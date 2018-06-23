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
