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
    $labelText = $element->config->get('name');

    $label =
        '<td align="left">
        <strong>' . $labelText . '</strong>
        </td>';
}

// render HTML for current element
$render = '<td align="center">' . $element->render($params) . '</td>';

// render result
echo
    '<tr>
    ' . $label . $render . '
    </tr>', "\n";
