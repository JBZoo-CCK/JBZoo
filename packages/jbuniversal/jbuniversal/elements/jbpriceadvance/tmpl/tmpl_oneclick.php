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


$uniqClass = 'jsJBPriceAdvance-' . $this->identifier . '-' . $this->getItem()->id;
$iniqId = uniqid('jbprice-adv-');
?>

<div class="jsJBPriceAdvance jbprice-advance <?php echo $uniqClass; ?>" id="<?php echo $iniqId;?>">

    <?php if ((int)$params->get('oneclick_show_params', 1)) : ?>
        <div class="jbprice-selects">
            <?php echo $selects; ?>
        </div>
    <?php endif; ?>

    <div class="jbprice-buttons <?php echo ($isInCart) ? 'in-cart' : 'not-in-cart'; ?>">
        <!--noindex-->
        <a rel="nofollow" href="#add-to-cart-one" class="jsAddToCartOne jbzoo-button green"
           title="<?php echo JText::_('JBZOO_JBPRICE_ADD_TO_CART_MODAL'); ?>"><?php echo JText::_('JBZOO_JBPRICE_ONE_CLICK'); ?></a>

        <a rel="nofollow" href="#remove-from-cart" class="jsRemoveFromCart jbzoo-button remove-button"
           title="<?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?>"><?php echo JText::_('JBZOO_JBPRICE_REMOVE_FROM_CART'); ?></a>
        <!--/noindex-->
    </div>
</div>

<script type="text/javascript">
    (function ($) {

        $('#<?php echo $iniqId;?>').JBZooPriceAdvance({
            'itemId'           : "<?php echo $this->getItem()->id;?>",
            'identifier'       : "<?php echo $this->identifier;?>",
            'isInCart'         : <?php echo $isInCart;?>,
            'removeFromCartUrl': "<?php echo $removeFromCartUrl; ?>",
            'basketUrl'        : "<?php echo $basketUrl; ?>",
            'addToCartUrl'     : "<?php echo $addToCartUrl; ?>"
        });

    })(jQuery);
</script>
