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

$classes = array(
    'jbcart-shipping',
    $view->shipping && $view->shippingFields ? 'jbcart-shipping-full' : '',
    'clearfix'
);

?>

<?php if (!empty($view->shipping) || !empty($view->shippingFields)) : ?>

    <div class="<?php echo implode(' ', $classes); ?>">

        <?php
        $this->app->jbassets->less('jbassets:less/cart/shipping.less');
        $this->app->jbassets->less('jbassets:less/cart/shippingfield.less');
        $this->app->jbassets->js('jbassets:js/cart/shipping.js');
        $this->app->jbassets->js('jbassets:js/cart/shipping-type.js');
        ?>

        <?php if (!empty($view->shipping)) : ?>
            <div class="jbcart-shipping-col jsShippingWrapper">
                <?php echo $view->shippingRenderer->render('shipping.default', array(
                    'order' => $view->order
                )); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($view->shippingFields)) : ?>
            <div class="jbcart-shippingfield-col jsShippingFieldWrapper">
                <?php echo $view->shippingFieldRenderer->render('shippingfield.default', array(
                    'order' => $view->order
                )); ?>
                <p class="jsShippingFieldEmpty jbcart-shippingfield-empty">
                    <?php echo JText::_('JBZOO_CART_SHIPPINGFIELDS_EMPTY'); ?>
                </p>
            </div>
        <?php endif; ?>

    </div>
<?php endif; ?>
