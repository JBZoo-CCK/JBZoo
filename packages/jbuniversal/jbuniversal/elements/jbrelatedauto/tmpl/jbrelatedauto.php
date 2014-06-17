<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$count = count($items);

if ($count) {

    echo '<div class="related-items related-items-col-' . $columns . '">';

    $j = 0;
    foreach ($items as $item) {

        $first = ($j == 0) ? ' first' : '';
        $last  = ($j == $count - 1) ? ' last' : '';
        $j++;

        $isLast = $j % $columns == 0;

        if ($isLast) {
            $last .= ' last';
        }

        echo '<div class="rborder column width' . intval(100 / $columns) . $first . $last . '">' . $item . '</div>';

        if ($isLast) {
            echo '<div class="clear clr"></div>';
        }
    }

    echo '<div class="clear clr"></div>';
    echo '</div>';

}
