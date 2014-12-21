<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$view = $this->getView();
$this->app->jbassets->basket();

$this->app->jbassets->less('jbassets:less/cart/cart.less');
$this->app->jbassets->less('jbassets:less/cart-btn.less');
?>


<?php if (count($view->items) == 0) : ?>
    <p><?php echo JText::_('JBZOO_CART_ITEMS_NOT_FOUND'); ?></p>

<?php else: ?>

    <?php
    $isFormEmpty = empty($view->shipping)
        && empty($view->payment)
        && empty($view->shippingFields);
    //&& !$view->formRenderer->checkPosition('fields');
    ?>

    <form action="<?php echo $this->app->jbrouter->cartOrderCreate(); ?>" class="jbcart" method="post" name="jbcartForm"
          accept-charset="utf-8" enctype="multipart/form-data">

        <?php echo $this->partial('basket', 'table'); ?>

        <?php if (!$isFormEmpty) : ?>

            <p class="jbcart-title jbcart-title-main"><?php echo JText::_('JBZOO_CART_CREATE_ORDER_TITLE'); ?></p>
            <?php echo $this->partial('basket', 'form'); ?>
            <?php echo $this->partial('basket', 'shipping'); ?>
            <?php echo $this->partial('basket', 'payment'); ?>
            <?php echo $this->partial('basket', 'buttons'); ?>

        <?php else : ?>

            <?php echo $this->partial('basket', 'buttons'); ?>

        <?php endif; ?>

        <input type="hidden" name="option" value="com_zoo" />
        <input type="hidden" name="controller" value="basket" />
        <input type="hidden" name="task" value="index" />
        <input type="hidden" name="Itemid" value="<?php echo $view->Itemid; ?>" />
        <?php echo $this->app->html->_('form.token'); ?>
    </form>

<?php endif;