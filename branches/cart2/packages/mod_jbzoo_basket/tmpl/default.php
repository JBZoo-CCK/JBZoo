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


$zoo   = App::getInstance('zoo');
$items = $basketHelper->getBasketItems();

echo '<!--noindex-->';
echo '<div class="jbzoo jbzoo-basket-wraper jsJBZooCartModule" id="jbzooCartModule-' . $module->id . '">';

if (!empty($items)) {

    $summa    = $basketHelper->getSumm($items);
    $count    = $basketHelper->getCount($items);
    $countSku = $basketHelper->getCountSku($items);
    $currency = $basketHelper->getCurrency($items);

    if ((int)$params->get('items_show', 1)) {
        echo '<p>' . JText::_('JBZOO_CART_TOTAL_COUNT') . ': <span class="total-items">'
            . $count . ' ' . JText::_('JBZOO_CART_COUNT_ABR') . '</span></p>';
    }

    if ((int)$params->get('lots_show', 1)) {
        echo '<p>' . JText::_('JBZOO_CART_TOTAL_SKU') . ': <span class="total-items">'
            . $countSku . ' ' . JText::_('JBZOO_CART_COUNT_ABR') . '</span></p>';
    }

    if ((int)$params->get('summa_show', 1)) {
        echo '<p>' . JText::_('JBZOO_CART_TOTAL_PRICE') . ': <span class="price-total-value">'
            . $summa . '</span></p>';
    }

    if ((int)$params->get('cancel_show', 1)) {
        echo '<p class="basket-link">'
            . '<a rel="nofollow" class="jsEmptyCart empty-cart" href="#clean-cart">'
            . JText::_('JBZOO_CART_EMPTY')
            . '</a>';
    }

    if ((int)$params->get('link_show', 1)) {
        echo '<a rel="nofollow" class="add-to-cart" href="' . $basketHelper->getBasketUrl() . '">'
            . JText::_('JBZOO_CART_GOTO_BASKET') . '</a>
        </p>';
    }

} else {
    echo '<p>' . JText::_('JBZOO_CART_ITEMS_NOT_FOUND') . '</p>';
}

echo '<div class="clr"></div>';
echo '</div><!--/noindex-->';

?>

<?php
if (!$zoo->jbrequest->isAjax()) :
    $zoo->jbassets->js('jbassets:js/cart/module.js');
    $zoo->jbassets->js('jbassets:js/cart/cart.js');
    ?>
    <script type="text/javascript">
        jQuery(function ($) {
            $('.jsJBZooCartModule').JBZooCartModule(<?php echo $basketHelper->getWidgetParams();?>);
        });
    </script>
<?php endif; ?>
