<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$this->app->jbassets->less('jbassets:less/cart/mobile-tools.less');

$order = JBCart::getInstance()->newOrder();
?>

<div class="jbcart-mobile-tools">
    <div class="jbtool-total-price">
        <span class="jbtool-label"><?php echo JText::_('JBZOO_CART_TOTAL_SUM'); ?>:</span>
        <span class="jbtool-value jsTotal"><?php echo $order->getTotalSum()->html(); ?></span>
    </div>

    <?php echo $this->partial('basket', 'mobile_buttons'); ?>
</div>