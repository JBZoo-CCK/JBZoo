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
