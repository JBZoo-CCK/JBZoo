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

$attributes = array(
    'title' => $label,
    'class' => array(
        'jbcolor', (!$isFile ? 'dye-' . $color : '')
    ),
    'style' => array(
        (!$isFile ? 'background-color: #' . $color . ';' : 'background: url('.$color.') center;'),
        'width:'  . $width  . 'px;',
        'height:' . $height . 'px;',
    )
);

$spanAttrs = $this->app->jbhtml->buildAttrs($attributes);

echo '<label class="jbcolor-label jbcolor-default">
      <span ' . $spanAttrs . '></span></label>';
