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

// check label
$label = (int)$params->get('showlabel') ? '<h4>' . $element->getName() . '</h4> ' : null;

// render HTML for current element
$render = $element->render($params);

// render result
if (!is_null($render)) {
    echo $label . $render;
}
