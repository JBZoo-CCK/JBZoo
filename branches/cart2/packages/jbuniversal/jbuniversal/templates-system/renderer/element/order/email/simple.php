<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
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
$label  = '';
if ($params->get('showlabel')) {

    // check label
    $labelText = $element->getName();

    $label = '<strong>' . $labelText . '</strong>';

}

// render HTML for current element
$render = $element->render($params);

// render result
echo $label . $render;