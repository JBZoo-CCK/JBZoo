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
$border  = (int)$params->get('display_border', 1) ? 'rborder' : '';

if ($count) {

    echo '<div id="' . $modHelper->getModuleId() . '" class="jbzoo yoo-zoo">';
    echo '<div class="module-items jbzoo-rborder module-items-col-' . $columns . '">';
    echo $modHelper->renderRemoveButton();

    if ($columns) {
        $j = 0;
        foreach ($items as $item) {

            $first = ($j == 0) ? ' first' : '';
            $last  = ($j == $count - 1) ? ' last' : '';
            $j++;

            $isLast = $j % $columns == 0;

            if ($isLast) {
                $last .= ' last';
            }

            $renderer = $modHelper->createRenderer('item');
            echo '<div class="' . $border . ' column width' . intval(100 / $columns) . $first . $last . '">'
                .   '<div class="jb-box">'
                        . $renderer->render('item.' . $modHelper->getItemLayout(), array(
                            'item'   => $item,
                            'params' => $params
                        ))
                    . '</div>'
                . '</div>';

            if ($isLast) {
                echo JBZOO_CLR;
            }
        }

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


