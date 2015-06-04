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

$type   = $this->getElementType();
$isCore = ($this->isCore() ? 'core' : 'simple');

$attr = array(
    'data-identifier' => $this->identifier,
    'data-template'   => $this->template,
    'data-index'      => $this->index,
    'data-position'   => $this->position,
    'class'           => array(
        'jbprice-' . $type, // very IMPORTANT class for element templates (DON'T REMOVE!)
        'jsElement',
        'jsPriceElement',
        'js' . ucfirst($type),
        'js' . ucfirst($isCore),
        'jselement'   . strtolower($this->template . $this->position . $this->index)
    )
);

echo '<div ' . $this->_attrs($attr) . '>' . $html . '</div>';
