<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
        $tooltip     = $tooltipText ?
            '<span class="jbtooltip" data-uk-tooltip="{pos:\'top-left\'}" title="' . $tooltipText . '">' .
                '<i class="uk-icon-question-circle uk-text-primary"></i>' .
            '</span>' : '';
    }

    // check label
    $labelText = ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');

    $label = '<' . $params['labelTag'] . ' class="element-label"> '
        . $labelText . '&nbsp;'
        . $tooltip
        . '</' . $params['labelTag'] . '>';

}

// collect html classes
$classes = array_filter(array(
    'index-' . (int)$params['_index'],
    $params['class'],
    'element-' . $element->identifier,
    'element-' . $element->getElementType(),
    $params['first'] ? 'first' : '',
    $params['last'] ? 'last' : '',
));

// add clear after html
$clear = $params['clear'] ? '<div class="clear clr clearfix"></div>' : '';

// render HTML for  current element
$render = $element->render($params);

// wrapping the element HTML
if ($params['wrapperTag']) {
    $render = '<' . $params['wrapperTag'] . '>' . $render . '</' . $params['wrapperTag'] . '>';
}

// render result
echo '<' . $params['tag'] . ' class="' . implode(' ', $classes) . '">', $label,
    ' ' . $render, '</' . $params['tag'] . '>', PHP_EOL . $clear;
