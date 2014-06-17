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


$zoo = App::getInstance('zoo');
$zoo->jbassets->fancybox();
$zoo->jbassets->initJBPrice();


$htmlCurrency = array();
foreach ($currencyList as $currency) {

    $activeClass = '';
    if ($currency == $activeCur) {
        $activeClass = ' active';
    }

    $htmlCurrency[] = '<span class="price-currency jsPriceCurrency ' . $activeClass . '" currency="' . $currency . '">' . $currency . '</span>';
}

$classes = array(
    'jbprice-wrapper',
    'jbprice-wrapper-' . $count,
    ($isInCart ? 'in-cart' : 'not-in-cart'),
    'jsPrice',
    'jsPrice-' . $this->identifier . '-' . $this->getItem()->id
);

?>
<div class="<?php echo implode(' ', $classes);?>">

    <?php if ($template != 'onlybuttons-popup' && $template != 'onlybuttons-oneclick'): ?>

        <?php if ((int)$params->get('show_sku', 1)) : ?>
            <div class="item-sku">
                <strong><?php echo JText::_('JBZOO_CART_ITEM_SKU');?></strong>:
                <?php echo $this->_getSku();?>
            </div>
        <?php endif;?>

        <?php if (!$nopaidOrder && count($htmlCurrency) > 1) {
            echo '<div class="currency-list">' . implode("\n", $htmlCurrency) . '</div>';
            echo $values;
        } else {
            echo $values;
        } ?>

    <?php endif;?>


    <?php if ($template != 'onlyprice' && $this->_isInStock()) : ?>
    <!--noindex-->
        <?php if ($template == 'oneclick' || $template == 'onlybuttons-oneclick') : ?>
            <a rel="nofollow" href="#order-now" data-href="<?php echo $addToCartUrl;?>" class="jsBayIt add-to-cart"
                title="<?php echo JText::_('JBZOO_CART_BUY_IT');?>"><?php echo JText::_('JBZOO_CART_BUY_IT');?></a>
        <?php else:?>
            <a rel="nofollow" href="#add-to-basket" data-href="<?php echo $modalUrl;?>" class="jsAddToCart add-to-cart"
                title="<?php echo JText::_('JBZOO_CART_ADD');?>"><?php echo JText::_('JBZOO_CART_ADD');?></a>
        <?php endif;?>

        <a rel="nofollow" href="#remove-from-basket" data-href="<?php echo $removeFromCartUrl;?>" class="jsRemoveFromCart remove-from-cart"
           title="<?php echo JText::_('JBZOO_CART_REMOVE');?>"><?php echo JText::_('JBZOO_CART_REMOVE');?></a>
    <!--/noindex-->
    <?php endif;?>


    <?php if (!$this->_isInStock()): ?>
        <p class="not-in-stock"><?php echo JText::_('JBZOO_CART_NOT_IN_STOCK'); ?><p>
    <?php endif;?>

</div>
