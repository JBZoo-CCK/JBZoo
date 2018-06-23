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
