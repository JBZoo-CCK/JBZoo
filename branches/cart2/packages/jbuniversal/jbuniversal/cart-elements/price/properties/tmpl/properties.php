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

echo
'<div class="jbprice-height jsPriceHeight">', JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_HEIGHT_UNIT'), ': ', $height, '</div>',
'<div class="jbprice-length jsPriceLength">', JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_LENGTH_UNIT'), ': ', $length, '</div>',
'<div class="jbprice-width jsPriceWidth">', JText::_('JBZOO_ELEMENT_PRICE_PROPERTIES_WIDTH_UNIT'), ': ', $width, '</div>';
