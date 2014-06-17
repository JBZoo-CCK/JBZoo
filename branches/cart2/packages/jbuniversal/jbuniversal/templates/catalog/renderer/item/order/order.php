<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$this->app->jbassets->jqueryAccordion();

?>

<div class="basket-info jsBasketInfo jsAccordion">

    <?php if ($this->checkPosition('billing')) : ?>
        <h3 class="toggler"><?php echo JText::_('JBZOO_CART_BILLING'); ?></h3>
        <div>
            <div class="tab-body content wk-content clearfix">
                <?php echo $this->renderPosition('billing', array('style' => 'order.block')); ?>
            </div>
        </div>
    <?php endif; ?>


    <?php if ($this->checkPosition('shipping')) : ?>
        <h3 class="toggler"><?php echo JText::_('JBZOO_CART_SHIPPING'); ?></h3>
        <div>
            <div class="tab-body content wk-content clearfix">
                <?php echo $this->renderPosition('shipping', array('style' => 'order.block')); ?>
            </div>
        </div>
    <?php endif; ?>


    <?php if ($this->checkPosition('payment')) : ?>
        <h3 class="toggler"><?php echo JText::_('JBZOO_CART_PAYMENT'); ?></h3>
        <div>
            <div class="tab-body content wk-content clearfix">
                <?php echo $this->renderPosition('payment', array('style' => 'order.block')); ?>
            </div>
        </div>
    <?php endif; ?>


    <?php if ($this->checkPosition('other')) : ?>
        <h3 class="toggler"><?php echo JText::_('JBZOO_CART_OTHER'); ?></h3>
        <div>
            <div class="tab-body content wk-content clearfix">
                <?php echo $this->renderPosition('other', array('style' => 'order.block')); ?>
            </div>
        </div>
    <?php endif; ?>

</div>
