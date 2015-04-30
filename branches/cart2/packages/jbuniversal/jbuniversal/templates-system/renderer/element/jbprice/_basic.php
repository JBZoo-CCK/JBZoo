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

$name = JText::_($element->getName());
$type = JString::strtolower($element->getElementType());
$desc = JString::trim($element->getDescription());

$unique  = $element->htmlId(true);
$jbPrice = $element->getJBPrice();

// init vars
$name = (isset($params['altlabel'])) ? $params['altlabel'] : $name;
$desc = (!empty($desc) ? $desc : JText::_('JBZOO_ELEMENT_PRICE_' . $type . '_DESC'));

// create label
$label = '<label class="hasTip row-field" title="' . $desc . '" for="' . $unique . '">' . JString::ucfirst($name) . '</label>';

//create attributes for main div
$attributes = array(
    'class' => array(
        'jbprice-row',
        'jbpriceadv-row',
        'basic-' . $type . '-wrap',
        'jbprice-' . $jbPrice->getElementType(),
        'clearfix'
    )
);

// render element
echo '<div ' . $this->app->jbhtml->buildAttrs($attributes) . '>'
    . $label
    . $element->edit($params)
    . '</div>';
