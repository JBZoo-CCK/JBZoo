<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
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

    $appParams = $this->getItem()->getApplication()->params;
    if ((int)$appParams->get('global.config.column_heightfix', 0)) {
        $this->app->jbassets->heightFix();
    }

    echo '<div class="related-items related-items-col-' . $columns . ' clearfix">';

    $j = 0;
    foreach ($items as $item) {

        $classes = array(
            'rborder',
            'column',
            'width' . intval(100 / $columns)
        );

        $first = ($j == 0) ? $classes[] = 'first' : '';
        $last  = ($j == $count - 1) ? $classes[] = 'last' : '';
        $j++;

        $isLast = $j % $columns == 0;

        if ($isLast) {
            $classes[] = 'last';
        }

        echo '<div class="' . implode(' ', $classes) . '">'
                . '<div class="jb-box">'
                    . $item
                . '</div>'
            . '</div>';
    }

    echo '</div>';
}
