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

$input_attr = array(
    'class' => 'jbcolor-input ',
    'style' => 'width:' . $width . 'px; height:' . $height . 'px;'
);

$label_attr = array(
    'class' => 'jbcolor-label hasTip radio',
    'style' => 'width:' . $width . 'px; height:' . $height . 'px;'
);

$div_attr = array(
    'style' => 'width:' . $width . 'px; height:' . $height . 'px;'
);

echo $this->_jbhtml->colors('radio', $colorItems, $this->getRenderName('value'), $this->getValue(), $input_attr, $label_attr, $div_attr);
