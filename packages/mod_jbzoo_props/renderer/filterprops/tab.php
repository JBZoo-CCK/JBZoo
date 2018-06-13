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

if (!empty($elementHTML)) {

    // create label
    $labelText = ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');
    $label     = '<h3>' . $labelText . '</h3>';

    // create class attribute
    $classes = array_filter(array(
        'props-element',
        ($params['first']) ? 'first' : '',
        ($params['last']) ? 'last' : '',
    ));

    echo $label . '<div class="tab-body ' . implode(' ', $classes) . '">' . $elementHTML . '</div>';
}
