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
$showlabel = (int)$params->get('showlabel', 0);

$html = array();
if ($showlabel) {
    $html[] = '<tr><td align="left" valign="top">';
    $html[] = '<h3 style="color: #444444;margin: 0 0 15px 0;font-size: 18px;">';
    $html[] = $element->getName();
    $html[] = '</h3>';
    $html[] = $element->render($params);
    $html[] = '</td></tr>';
} else {
    $html[] = '<tr><td align="left" valign="top">';
    $html[] = $element->render($params);
    $html[] = '</td></tr>';
}

// render result
echo implode(PHP_EOL, $html);
