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

if ((int)$view->application->params->get('global.config.column_heightfix', 0)) {
    $this->app->jbassets->heightFix('.uk-panel');
}

$this->app->jbassets->less(array(
    'jbassets:less/general/cart.less',
));

$this->app->jbassets->widget('.jbzoo .jsJBZooCart', 'JBZoo.Cart', array(
    'text_remove_all'  => JText::_('JBZOO_CART_CLEANUP'),
    'text_remove_item' => JText::_('JBZOO_CART_REMOVE_ITEM'),
    'url_shipping'     => $this->app->jbrouter->basketShipping(),
    'url_quantity'     => $this->app->jbrouter->basketQuantity(),
    'url_delete'       => $this->app->jbrouter->basketDelete(),
    'url_clear'        => $this->app->jbrouter->basketClear(),
    'items'            => $view->order->getItems(false),
));
?>

<?php if (count($view->items) == 0) : ?>
    <p><?php echo JText::_('JBZOO_CART_ITEMS_NOT_FOUND'); ?></p>
<?php else: ?>

    <?php
    $isFormEmpty = empty($view->payment)
        && empty($view->shipping)
        && empty($view->shippingFields)
        && !$view->formRenderer->checkPosition(JBCart::DEFAULT_POSITION);
    ?>

    <form action="<?php echo $this->app->jbrouter->cartOrderCreate(); ?>" class="jbcart jsJBZooCart uk-form"
          method="post" name="jbcartForm" accept-charset="utf-8" enctype="multipart/form-data">

        <?php echo $this->partial('basket', 'validators'); ?>

        <?php echo $this->partial('basket', 'table'); ?>

        <?php if (!$isFormEmpty) : ?>
            <?php echo $this->partial('basket', 'form'); ?>
            <?php echo $this->partial('basket', 'shipping'); ?>
            <?php echo $this->partial('basket', 'payment'); ?>
        <?php endif; ?>

        <?php echo $this->partial('basket', 'buttons'); ?>
        <?php echo $this->partial('basket', 'mobile_tools'); ?>

        <input type="hidden" name="option" value="com_zoo" />
        <input type="hidden" name="controller" value="basket" />
        <input type="hidden" name="task" value="index" />
        <input type="hidden" name="Itemid" value="<?php echo $view->Itemid; ?>" />
        <?php echo $this->app->html->_('form.token'); ?>
    </form>

<?php endif;
