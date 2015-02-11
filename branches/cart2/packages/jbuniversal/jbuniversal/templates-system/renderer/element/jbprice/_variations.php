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

$name  = JText::_($element->config->get('name'));
$class = 'variant-param';

$isRequired = (int)$element->config->get('required', 0);
$isCore     = $element->isCore();

$attention = '<span class="attention jsMessage"></span>';
$type      = JString::strtolower($element->getElementType());

$class .= ($isCore ? ' core' : ' simple') . '-param';
$class .= ' variant-' . $type . '-wrap';

// init vars
$label = $required = $required_class = '';

// create required mark
if ($isRequired) {
    $required = '<span class="hasTip jbrequired-note" title="Param is required">*</span>';
    $class .= ' jbparam-required';
}

// create label
//if (isset($params['showlabel']) && $params['showlabel']) {
$name  = (isset($params['altlabel'])) ? $params['altlabel'] : $element->config->get('name');
$label = '<strong class="label row-field"><span class="hasTip jbparam-label" title="'
    . $name . '">'
    . ucfirst($name) . '</span>'
    . $required . '</strong>';
//}

// render element
$element_html =
    '<div class="field jsElementData">'
    . $element->edit($params) .
    '</div>';

//create attributes for main div
$attributes = array(
    'class' => $class
);

// render element
echo '<div ' . $this->app->jbhtml->buildAttrs($attributes) . '>'
    . $label
    . $attention
    . $element_html
    . '</div>';
