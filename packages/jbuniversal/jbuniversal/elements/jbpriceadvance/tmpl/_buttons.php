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

?>

<?php if ($mode != 'none') : ?>
    <!--noindex-->
    <div class="jbprice-buttons">

        <?php if ($mode == 'normal') : ?>
            <a rel="nofollow" href="#add-to-cart" class="jsAddToCart jbzoo-button green"
               title="<?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART'); ?>"><?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART'); ?></a>

        <?php elseif ($mode == 'popup') : ?>
            <?php $this->app->jbassets->fancybox();?>
            <a rel="nofollow" href="#add-to-cart-modal" class="jsAddToCartModal jbzoo-button green"
               title="<?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART_MODAL'); ?>"><?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART'); ?></a>

        <?php elseif ($mode == 'oneclick') : ?>
            <a rel="nofollow" href="#add-to-cart-goto" class="jsAddToCartGoto jbzoo-button green"
               title="<?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART_GOTO'); ?>"><?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART_GOTO'); ?></a>

        <?php endif; ?>

        <a rel="nofollow" href="#remove-from-cart" class="jsRemoveFromCart jbzoo-button remove-button"
           title="<?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?>"><?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?></a>

    </div>
    <!--/noindex-->
<?php endif; ?>
