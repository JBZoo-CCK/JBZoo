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

$zoo->jbassets->setAppCss();
$zoo->jbassets->setAppJS();
$zoo->jbassets->initJBPrice();
$zoo->jbassets->basket();

$basketHelper = new JBZooBasketHelper($params);
$basketItems  = $basketHelper->getBasketItems();

echo '<!--noindex--><div class="jbzoo">';
echo '<div class="jbzoo-basket-wraper jsJBZooModuleBasket" appId="' . $basketHelper->getAppId() . '" moduleId="' . $module->id . '">';

if (!empty($basketItems)) {

    $summa     = $basketHelper->getSumm($basketItems);
    $count     = $basketHelper->getCount($basketItems);
    $countSku  = $basketHelper->getCountSku($basketItems);
    $currency  = $basketHelper->getCurrency($basketItems);
    $basketUrl = $basketHelper->getBasketUrl();
    $emptyUrl  = $basketHelper->getBasketEmptyUrl();

    if ((int)$params->get('items_show', 1)) {
        echo '<p>' . JText::_('JBZOO_CART_TOTAL_COUNT') . ': <span class="total-items">' . $count . ' ' . JText::_('JBZOO_CART_COUNT_ABR') . '</span></p>';
    }

    if ((int)$params->get('lots_show', 1)) {
        echo '<p>' . JText::_('JBZOO_CART_TOTAL_SKU') . ': <span class="total-items">' . $countSku . ' ' . JText::_('JBZOO_CART_COUNT_ABR') . '</span></p>';
    }

    if ((int)$params->get('summa_show', 1)) {
        echo '<p>' . JText::_('JBZOO_CART_TOTAL_PRICE') . ': <span class="price-total-value">'
            . $zoo->jbmoney->toFormat($summa, $currency) . '</span></p>';
    }

    if ((int)$params->get('cancel_show', 1)) {
        echo '<p class="basket-link">
            <a rel="nofollow" class="jsEmptyCart empty-cart" style="display:inline-block;" href="' . $emptyUrl . '">'
            . JText::_('JBZOO_CART_EMPTY') . '</a>';
    }

    if ((int)$params->get('link_show', 1)) {
        echo '<a rel="nofollow" class="add-to-cart" style="display:inline-block;" href="' . $basketUrl . '">'
            . JText::_('JBZOO_CART_GOTO_BASKET') . '</a>
        </p>';
    }

} else {
    echo '<p>' . JText::_('JBZOO_CART_ITEMS_NOT_FOUND') . '</p>';
}

echo '<div class="clr"></div>';
echo '</div>';
echo '</div><!--/noindex-->';



