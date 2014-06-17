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


if (!empty($elementHTML)) {

    // create label
    $label = '';
    if (isset($params['showlabel']) && $params['showlabel']) {
        $label .= '<h3>';
        $label .= ($params['altlabel']) ? $params['altlabel'] : $element->getConfig()->get('name');
        $label .= '</h3>';
    }

    // create class attribute
    $classes = array_filter(array(
        'props-element',
        ($params['first']) ? 'first' : '',
        ($params['last']) ? 'last' : '',
    ));

    echo $label . '<div class="tab-body ' . implode(' ', $classes) . '">' . $elementHTML . '</div>';
}
