<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


// create label
$labelText = ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');
$label     = '<h3>' . $labelText . '</h3>';

// create class attribute
$classes = array_filter(array(
    'tab-body',
    isset($params['jbzoo_filter_render']) ? 'element-' . $params['jbzoo_filter_render'] : '',
    ($params['first']) ? 'first' : '',
    ($params['last']) ? 'last' : '',
));

echo $label . '<div class="' . implode(' ', $classes) . '">' . $elementHTML . '</div>';
