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

if (!$this->isCore() && empty($html)) {

    $html = $this->getJBPrice()->renderWarning();
    $link = $this->app->jbrouter->admin(array(
        'controller' => 'jbcart',
        'task'       => 'price',
        'element'    => $this->element_id
    ));

    $link = '<a target="_blank" href="' . $link . '">' . JText::_('JBZOO_ELEMENT_PRICE_ADD_OPTIONS') . '</a>';
    $html = JText::sprintf('JBZOO_ELEMENT_PRICE_NO_OPTIONS', $link);
}

$type   = $this->getElementType();
$isCore = ($this->isCore() ? 'core' : 'simple');
$attr   = array(
    'class' => array(
        'jbprice-element',
        'jsElement',
        'js' . ucfirst($type),
        'js' . ucfirst($isCore)
    )
);

echo '<div ' . $this->_attrs($attr) . '>' . $html . '</div>';