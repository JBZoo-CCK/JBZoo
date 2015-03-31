<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Vitaliy Yanovskiy <joejoker@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$count   = count($items);
$columns = (int)$params->get('item_cols', 1);
$border  = (int)$params->get('display_border', 1) ? 'rborder' : '';
$zoo     = App::getInstance('zoo');

if ($count) {

    echo '<div id="' . $unique . '" class="jbzoo yoo-zoo">';
    echo '<div class="module-items jbzoo-rborder module-items-col-' . $columns . '">';

    if ($params->get('delete') && $params->get('mode') == 'viewed') {
        echo '<a href="index.php?option=com_zoo&controller=viewed&task=clear&format=raw" class="jsRecentlyViewedClear recently-viewed-clear">' . JText::_('JBZOO_MODITEM_DELETE') . '</a>';
    }

    if ($columns) {

        $j = 0;

        foreach ($items as $item) {

            $app_id = $item->application_id;
            $first  = ($j == 0) ? ' first' : '';
            $last   = ($j == $count - 1) ? ' last' : '';
            $j++;

            $isLast = $j % $columns == 0;

            if ($isLast) {
                $last .= ' last';
            }

            echo '<div class="' . $border . ' column width' . intval(100 / $columns) . $first . $last . '">';
            echo $renderer->render('item.' . $params->get('item_layout', 'default'),
                array(
                    'item'   => $item,
                    'params' => $params
                )
            );

            echo '</div>';

            if ($isLast) {
                echo '<div class="clear clr"></div>';
            }
        }

    } else {

        foreach ($items as $item) {
            $app_id = $item->application_id;
            echo $renderer->render('item.' . $params->get('item_layout', 'default'),
                array(
                    'item'   => $item,
                    'params' => $params
                )
            );
        }
    }

    echo '</div></div>';

    if ($params->get('delete') && $params->get('mode') == 'viewed') {

        $zoo->jbassets->js('mod_jbzoo_item:assets/js/viewed.js');
        echo $zoo->jbassets->widget('#' . $unique, 'JBZooViewed', array(
            'message'   => JText::_('JBZOO_MODITEM_RECENTLY_VIEWED_DELETE_HISTORY'),
            'url_clear' => $zoo->link(array('controller' => 'viewed', 'task' => 'clear', 'app_id' => $app_id), true, 2),
        ), true);
    }

    if ($params->get('column_heightfix')) {
        echo $zoo->jbassets->widget('.module-items', 'JBZooHeightFix', array(), true);
    }
}


