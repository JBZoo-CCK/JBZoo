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


// controll view access
$user = JFactory::getUser();
if (!$user->id || $user->id != $item->created_by) {
    $this->app->jbnotify->error('Access denied');
}

?>

<?php if ($this->checkPosition('items')) : ?>
    <h2><?php echo JText::_('JBZOO_CART_ITEMS'); ?></h2>
    <?php echo $this->renderPosition('items'); ?>
<?php endif; ?>


<h2><?php echo JText::_('JBZOO_CART_ABOUT_USER'); ?></h2>

<div class="basket-info jsBasketInfo jsAccordion">

    <?php if ($this->checkPosition('billing')) : ?>
        <ul class="tab-body">
            <?php echo $this->renderPosition('billing', array('style' => 'email')); ?>
        </ul>
    <?php endif; ?>

    <?php if ($this->checkPosition('shipping')) : ?>
        <h2><?php echo JText::_('JBZOO_CART_SHIPPING'); ?></h2>
        <ul class="tab-body">
            <?php echo $this->renderPosition('shipping', array('style' => 'email')); ?>
        </ul>
    <?php endif; ?>

    <?php if ($this->checkPosition('payment')) : ?>
        <h2><?php echo JText::_('JBZOO_CART_PAYMENT'); ?></h2>
        <ul class="tab-body">
            <?php echo $this->renderPosition('payment', array('style' => 'email')); ?>
        </ul>
    <?php endif; ?>

    <?php if ($this->checkPosition('other')) : ?>
        <h2><?php echo JText::_('JBZOO_CART_OTHER'); ?></h2>
        <ul class="tab-body">
            <?php echo $this->renderPosition('other', array('style' => 'email')); ?>
        </ul>
    <?php endif; ?>

</div>
