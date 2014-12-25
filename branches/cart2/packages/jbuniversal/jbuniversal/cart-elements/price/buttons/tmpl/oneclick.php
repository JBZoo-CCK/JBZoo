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
defined('_JEXEC') or die('Restricted access'); ?>

<div class="jbprice-buttons jsPriceButtons <?php echo $inCart . ' ' . $inCartVariant; ?> ?>">
    <a rel="nofollow" href="#add-to-cart-goto" class="jsAddToCart jsAddToCartGoTo jbbutton green"
       title="<?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART_GOTO'); ?>"><?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART_GOTO'); ?></a>

    <a rel="nofollow" href="#remove-from-cart" class="jsRemoveFromCart jsRemoveElement jbbutton small remove-button"
       title="<?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?>"><?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?></a>
</div>