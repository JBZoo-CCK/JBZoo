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

$params = $this->app->data->create($params);

// create label
$label = '';
if (isset($params['showlabel']) && $params['showlabel']) {
    $labelText = ($params['altlabel']) ? $params['altlabel'] : $element->getName();
    $label     = '<label class="jbfilter-label" for="' . $attrs['id'] . '">' . $labelText . '</label>';
}

// create class attribute
$attrs = array(
    'class' => array(
        'jbfilter-row',
        'jbfilter-jbprice',
        'jbfilter-jbprice-' . ($element->isCore() ? 'core' : 'simple'),
        'jbfilter-' . trim($params->get('jbzoo_filter_render', 'default'), '_'),
    ));

echo '<div ' . $this->app->jbhtml->buildAttrs($attrs) . '>'
    . $label
    . '<div class="jbfilter-element">' . $elementHTML . '</div>'
    . JBZOO_CLR
    . '</div>';

