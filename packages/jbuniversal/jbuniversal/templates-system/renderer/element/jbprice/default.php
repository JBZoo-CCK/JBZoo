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

// create label
$label = '';
if ((int)$params->get('showlabel')) {
    $label = ($params['altlabel']) ? $params['altlabel'] : $element->getName();
    $label = '<span class="element-label">' . $label . '</span>';
}

// render element
echo $label . $element->render($params);