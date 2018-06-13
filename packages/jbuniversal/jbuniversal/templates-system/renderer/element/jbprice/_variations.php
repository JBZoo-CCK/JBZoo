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
