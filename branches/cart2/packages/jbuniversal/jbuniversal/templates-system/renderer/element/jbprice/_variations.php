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

$jbPrice = $element->getJBPrice();

$name = JText::_($element->getName());
$type = JString::strtolower($element->getElementType());
$desc = JString::trim($element->getDescription());

$classes = array(
    'variant-param',
    'jbprice-' . $jbPrice->getElementType(),
    ($element->isCore() ? 'core' : 'simple') . '-param',
    'variant-' . $type . '-wrap'
);

$isRequired = (int)$element->config->get('required', 0);

// create required mark
$required = '';
if ($isRequired) {
    $required  = '<span class="hasTip jbrequired-note" title="Param is required">*</span>';
    $classes[] = 'jbparam-required';
}

// create label
$name = (isset($params['altlabel'])) ? $params['altlabel'] : $name;
$desc = (!empty($desc) ? $desc : JText::_('JBZOO_ELEMENT_PRICE_' . $type . '_DESC'));

$label = '<strong class="label row-field">'
    . '<span class="hasTip jbparam-label" title="' . $desc . '">' . JString::ucfirst($name) . '</span>'
    . $required . '</strong>';

//create attributes for main div
$attributes = array(
    'class' => $classes
);

// render element
echo '<div ' . $this->app->jbhtml->buildAttrs($attributes) . '>'
    . $label
    . '<span class="attention jsMessage"></span>'
    . $element->edit($params)
    . '</div>';
