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
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="jbprice-buttons jsPriceButtons <?php echo $inCart; ?>">
    <a rel="nofollow" href="#add-to-cart-goto" class="jsAddToCartGoto uk-button uk-button-success add-button"
       title="<?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART_GOTO'); ?>"><?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART_GOTO'); ?></a>

    <a rel="nofollow" href="#remove-from-cart" class="jsRemoveFromCart uk-button uk-button-danger uk-button-small remove-button remove-button"
       title="<?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?>"><?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?></a>
</div>