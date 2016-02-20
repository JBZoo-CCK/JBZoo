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

