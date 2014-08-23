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

$jbhtml = $this->app->jbhtml;
$value  = $this->getValue('_description');
$attrs  = $jbhtml->buildAttrs(array(
    'size'        => '60',
    'rows'        => '5',
    'style'       => 'resize: none;',
    'class'       => 'description',
    'maxlength'   => '255',
    'placeholder' => JText::_('JBZOO_JBPRICE_BASIC_DESCRIPTION')
));

echo '<textarea name="' . $this->getControlName('_description') . '"
                ' . $attrs . '
                >' . $value . '</textarea>';
