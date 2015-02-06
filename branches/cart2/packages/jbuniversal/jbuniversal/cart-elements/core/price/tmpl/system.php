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

$type   = $this->getElementType();
$isCore = ($this->isCore() ? 'core' : 'simple');

$classes = array(
    'jbprice-param-' . $type . ' jbprice-' . $isCore . '-param jbprice-param',
    'jsElement jsPriceElement js' . ucfirst($type) . ' js' . ucfirst($isCore)
);

$attr = array(
    'data-identifier' => $this->identifier
);

echo
    '<div class="' . implode(' ', $classes) . '"' . $this->app->jbhtml->buildAttrs($attr) . '>'
    . $html .
    '</div>';