<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// create label
$label = '';
if (isset($params['showlabel']) && $params['showlabel']) {
    $labelText = ($params['altlabel']) ? $params['altlabel'] : $element->getName();
    $label     = '<div class="label param-label"> ' . $labelText . '</div>';
}

// create class attribute
$attrs = array(
    'class' => array(
        'filter-element',
        'filter-element-row',
        'element-' . $params['type'],
        'element-price-param',
        $element->isCore() ? 'core-param' : 'simple-param',
        'jsElement',
        'clear clr'
    ));

echo '<div ' . $this->app->jbhtml->buildAttrs($attrs) . '/>' . $label . $elementHTML . '</div>';

