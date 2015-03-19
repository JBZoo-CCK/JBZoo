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

// create label
$html  = $this->app->jbhtml;
$label = '';
if (isset($params['showlabel']) && $params['showlabel']) {
    $label = ($params['altlabel']) ? $params['altlabel'] : $element->config->get('name');
}

// render element
echo '<div class="default-style">' .
         $label . $element->render($params) .
     '</div>';