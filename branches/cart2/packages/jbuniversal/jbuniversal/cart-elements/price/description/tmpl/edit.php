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

$unique = $this->htmlId(true);
$attrs  = $this->_jbhtml->buildAttrs(array(
    'rows'        => '5',
    'style'       => 'resize: vertical;',
    'class'       => 'jsField description',
    'name'        => $this->getControlName('value'),
    'placeholder' => JText::_('JBZOO_ELEMENT_PRICE_DESCRIPTION_PLACEHOLDER')
));

echo '<textarea ' . $attrs . '>' . $value . '</textarea>';

$this->app->jbassets->widget('.jsDescription .jsField', 'JBZoo.PriceEditElement_descriptionEdit');
