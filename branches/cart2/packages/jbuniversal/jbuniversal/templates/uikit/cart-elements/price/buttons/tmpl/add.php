<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 * @coder       Kalistratov Sergey <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');?>

<div class="jbprice-buttons jsPriceButtons <?php echo $inCart; ?>">
    <a rel="nofollow" href="#add-to-cart" class="jsAddToCart uk-button uk-button-success add-button"
       title="<?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART'); ?>">
        <i class="uk-icon-shopping-cart"></i>
        <?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART'); ?>
    </a>

    <a rel="nofollow" href="#remove-from-cart" class="jsRemoveFromCart uk-button uk-button-danger uk-button-small remove-button"
       title="<?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?>">
        <i class="uk-icon-trash-o"></i>
        <?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART_VARIANT'); ?>
    </a>

    <a rel="nofollow" href="#remove-from-cart" class="jsRemoveFromCart uk-button uk-button-danger uk-button-small remove-button"
       title="<?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?>">
        <i class="uk-icon-trash-o"></i>
        <?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?>
    </a>
</div>