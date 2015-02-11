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

// Services
echo $this->app->jbhtml->checkbox($data, $this->getControlName('value', true), null, $this->data());

//Buttons
echo '<div class="' . $this->getElementType() . '-button">
        <input type="submit" name="goTo" class="jsAddToCart jsAddToCartGoTo jbbutton green"
        title="' . JText::_('JBZOO_JBADVER_ADD_TO_CART') . '"
        value="' . JText::_('JBZOO_JBADVER_ADD_TO_CART') . '" /></div>';
