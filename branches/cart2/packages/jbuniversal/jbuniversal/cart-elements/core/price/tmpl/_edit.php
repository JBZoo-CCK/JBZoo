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

if (($html === '' || $html === null) && (!$this->isCore())) {
    $html = $this->getJBPrice()->renderWarning('_warning.php', JText::_('JBZOO_PRICE_EDIT_ERROR_ADD_OPTIONS'));
}

$type   = $this->getElementType();
$isCore = ($this->isCore() ? 'core' : 'simple');
$attr   = array(
    'class' => array(
        'jbprice-element',
        'jsElement',
        'js' . JString::ucfirst($type),
        'js' . JString::ucfirst($isCore)
    )
);

echo '<div ' . $this->_attrs($attr) . '>' . $html . '</div>';