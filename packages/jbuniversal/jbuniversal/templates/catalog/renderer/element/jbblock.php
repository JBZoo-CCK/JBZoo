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

// default params
$params = array_merge(array(
    'first'      => 0,
    'last'       => 0,
    'showlabel'  => 0,
    'altlabel'   => '',
    'element'    => '',
    'style'      => 'jbblock',
    'tag'        => 'div',
    'labelTag'   => 'strong',
    'wrapperTag' => '',
    'tooltip'    => 0,
    'clear'      => 0,
    'class'      => '',
    '_layout'    => '',
    '_position'  => '',
    '_index'     => '',
), $params);

// create label
$label = '';
if ($params['showlabel']) {

    // add tooltip
    $tooltip = '';

    if ($params['tooltip']) {
        $tooltipText = $this->app->jbstring->clean($element->config->get('description'));
        $tooltip     = $tooltipText ? ' <span class="jbtooltip" title="' . $tooltipText . '"></span>' : '';

        $this->app->jbassets->initTooltip();
    }

    // check label
    $labelText = ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');

    $label = '<' . $params['labelTag'] . ' class="element-label"> '
        . $labelText
        . $tooltip
        . '</' . $params['labelTag'] . '>';

}

// collect html classes
$attrs = array(
    'class' => array(
        $params['class'],
        'index-' . (int)$params['_index'],
        'element-' . $element->identifier,
        'element-' . $element->getElementType(),
        $params['first'] ? 'first' : '',
        $params['last'] ? 'last' : '',
    )
);

// add clear after html
$clear = $params['clear'] ? JBZOO_CLR : '';

// render HTML for  current element
$render = $element->render($params);

// wrapping the element HTML
if ($params['wrapperTag']) {
    $render = '<' . $params['wrapperTag'] . '>' . $render . '</' . $params['wrapperTag'] . '>';
}

// render result
echo '<' . $params['tag'] . ' ' . $this->app->jbhtml->buildAttrs($attrs) . '>'
    . $label
    . ' '
    . $render
    . '</' . $params['tag'] . '>'
    . PHP_EOL
    . $clear;
