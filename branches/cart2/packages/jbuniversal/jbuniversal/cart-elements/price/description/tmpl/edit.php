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

$unique     = $this->htmlId(true);
$attributes = $this->_jbhtml->buildAttrs(array(
    'rows'        => '5',
    'style'       => 'resize: vertical;',
    'class'       => 'jsField description',
    'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_DESCRIPTION_NAME')
));

echo '<textarea name="' . $this->getControlName('value') . '"
                ' . $attributes . '
                >' . $value . '</textarea>';

if (!$this->isBasic()) {
    $this->app->jbassets->widget('.jsDescription .jsField', 'JBZoo.PriceEditElement_descriptionEdit');
}