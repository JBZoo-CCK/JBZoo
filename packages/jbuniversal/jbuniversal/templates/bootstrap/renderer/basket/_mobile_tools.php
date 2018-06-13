<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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