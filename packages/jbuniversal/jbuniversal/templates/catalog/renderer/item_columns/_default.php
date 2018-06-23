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

    $count = $vars['count'];

    echo '<div class="items clearfix items-col-' . $vars['cols_num'] . '">';

    $j = 0;
    foreach ($vars['objects'] as $object) {

        $classes = array(
            'column',
            'rborder',
            'width' . intval(100 / $vars['cols_num'])
        );

        $first = ($j == 0) ? $classes[] = 'first' : '';
        $last  = ($j == $count - 1) ? $classes[] = 'last' : '';
        $j++;

        $isLast = $j % $vars['cols_num'] == 0 && $vars['cols_order'] == 0;

        if ($isLast) {
            $classes[] = 'last';
        }

        echo '<div class="' . implode(' ', $classes) . '">' . $object . '</div>';

        if ($isLast) {
            echo JBZOO_CLR;
        }

    }

    echo '</div>';
}

$this->app->jbdebug->mark('layout::item_columns::finish');