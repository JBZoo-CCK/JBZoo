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

$items   = $modHelper->getItems();
$count   = count($items);
$columns = (int)$params->get('item_cols', 1);
$border  = (int)$params->get('display_border', 1) ? 'rborder' : 'no-border';

if ($count) {

    echo '<div id="' . $modHelper->getModuleId() . '" class="jbzoo yoo-zoo">';
    echo '<div class="module-items jbzoo-' . $border . ' module-items-col-' . $columns . '">';
    echo $modHelper->renderRemoveButton();

    if ($columns) {

        $j = $i = 0;

        $rowItem = array_chunk($items, $columns);

        echo '<div class="items items-col-' . $columns . ' uk-article-divider">';

        foreach ($rowItem as $row) {
            echo '<div class="uk-grid item-row-' . $i . '" data-uk-grid-margin>';

            foreach ($row as $item) {

                $app_id = $item->application_id;
                $first  = ($j == 0) ? ' first' : '';
                $last   = ($j == $count - 1) ? ' last' : '';
                $j++;

                $isLast = $j % $columns == 0;

                if ($isLast) {
                    $last .= ' last';
                }

                $renderer = $modHelper->createRenderer('item');

                echo '<div class="item-column uk-width-medium-1-' . $columns . $first . $last . '">'
                        . '<div class="uk-panel uk-panel-box">'
                            . $renderer->render('item.' . $modHelper->getItemLayout(), array(
                                'item'   => $item,
                                'params' => $params
                            ))
                        . '</div>'
                    . '</div>';
            }

            $i++;

            echo '</div>';
        }

        echo '</div>';


    } else {

        foreach ($items as $item) {
            $renderer = $modHelper->createRenderer('item');
            echo $renderer->render('item.' . $modHelper->getItemLayout(), array(
                'item'   => $item,
                'params' => $params
            ));
        }
    }

    echo '</div></div>';
}
