<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<table class="jbbasket-table jsJBZooBasket">
    <thead>
    <tr>
        <th></th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?></th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_PRICE'); ?></th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_QUANTITY'); ?></th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_SUBTOTAL'); ?></th>
    </tr>
    </thead>

    <tbody>
    <tr class="null">
        <td class="null jsNull"></td>
        <td class="null jsNull"></td>
        <td class="null jsNull"></td>
        <td class="null jsNull"></td>
        <td class="null jsNull"></td>
    </tr>
    <?php

    if (!empty($view->items)) {

        $i     = 0;
        $sum   = 0;
        $count = 0;

        $string  = $this->app->jbstring;
        $default = $view->config->get('default_currency', 'EUR');
        $jbHTML  = $this->app->jbhtml;
        $jbMoney = $this->app->jbmoney;

        foreach ($view->items as $id => $data) {

            extract($data, EXTR_PREFIX_ALL, '_');

            $item = $this->app->table->item->get($__item_id);

            $image = null;
            $price = $jbMoney->convert($__currency, $default, $__total);
            $href  = $this->app->route->item($item);

            $count += $__quantity;
            $subtotal = $__quantity * $price;
            $sum += $subtotal;

            if (!empty($__image)) {

                $url = $this->app->jbimage->resize($__image, 75, 75);

                $linkAttr = array(
                    'class' => 'jbimage-link',
                    'title' => $item->name,
                    'href'  => $href
                );

                $imgAttr = array(
                    'class'  => 'jbimage',
                    'src'    => $url->url,
                    'alt'    => $item->name,
                    'title'  => $item->name,
                    'width'  => 75,
                    'height' => 75
                );

                $image =
                    '<a ' . $jbHTML->buildAttrs($linkAttr) . '>
                    <img  ' . $jbHTML->buildAttrs($imgAttr) . ' />
                </a>';
            }

            echo '<tr class="row-' . $id . '" data-itemId="' . $__item_id . '" data-key="' . $id . '">';

            echo '<td class="jbbasket-item-image">' . $image . '</td>';

            echo '<td class="jbbasket-item-name">';
            echo '<a href="' . $href . '" title="' . $__name . '">' . $__name . '</a>';

            if ($__sku) {
                echo '<div class="jbbasket-item-param">
                    <span class="jbbasket-item-sku">' . JText::_('JBZOO_CART_ITEM_SKU') . ':
                    </span>
                    ' . $__sku . '
                    </div>';
            }

            unset($item);

            if (isset($__priceParams) && !empty($__priceParams)) {
                foreach ($__priceParams as $key => $value) {
                    if (!empty($value)) {
                        echo '<div class="jbbasket-item-param">
                    <span class="jbbasket-param-key">' . $key . ':</span>
                    ' . $value . '
                    </div>';
                    }
                }
            }

            echo '</td>';

            if ($__price) {
                echo '<td class="jsValue jbbasket-item-price" price="' . $price . '">'
                     . $jbMoney->format($price)
                     . '</td>';
            } else {
                echo '<td> - </td>';
            }

            echo '<td class="jbbasket-item-quantity"><input type="text" class="jsQuantity input-quantity" value="'
                 . $__quantity . '" /></td>';

            if ($price) {
                echo '<td class="jsSubtotal jbbasket-subtotal">

            <span class="jbbasket-table-value jsValue">
            ' . $jbMoney->format($subtotal) . '
            </span>

            <span class="jsCurrency currency-item">
            ' . $jbMoney->getSymbol($default) . '
            </span>
            <a class="item-delete jbbutton-orange jbbutton-base jsDelete" itemid="' . $id . '">x</a>
            </td>';

            } else {
                echo '<td> - </td>';
            }

            echo "</tr>\n";

        }
        ?>

        <tfoot>

        <tr class="null">
            <td class="null jsNull"></td>
            <td class="null jsNull"></td>
            <td class="null jsNull"></td>
            <td class="null jsNull"></td>
            <td class="null jsNull">
                <a class="jsDeleteAll item-delete-all jbbutton-orange jbbutton-base"><?php echo JText::_('JBZOO_CART_EMPTY'); ?></a>
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td>
                В корзине
            <span class="jsTotalCount jbitems-total-count">
                <span class="jsValue">
                <?php echo $count; ?>
                </span>
            </span>

                <span data-word="товар" class="morphology jsMorphology">
                    <?php echo $string->declension($count, 'товар', 'товара', 'товаров'); ?>
                </span>
                на сумму:
                <div class="jsTotalPrice">

                <span class="jsValue jbtotal-price">
                    <?php echo $jbMoney->format($sum); ?>
                </span>

                <span class="jsCurrency jbcurrency">

                    <?php echo $jbMoney->getSymbol(strtoupper($default)); ?>
                </span>
                </div>
            </td>

            <td>
            <span class="jbasket-label">
            Доставка:
            </span>

            <span class="shipping-price jsShippingPrice">
                Бесплатно
            </span>
            </td>

            <td class="jsTotalPrice jbbasket-total-price">

            <span class="jbasket-label">
            Итого:
            </span>

            <span class="jsValue jbtotal-price">
                <?php echo $jbMoney->format($sum); ?>
            </span>

            <span class="jsCurrency jbcurrency">
                <?php echo $jbMoney->getSymbol($default); ?>
            </span>
            </td>

        </tr>

        </tfoot>
    <?php } ?>
</table>

<script type="text/javascript">
    jQuery(function ($) {

        $('.jbzoo .jsJBZooBasket').JBZooBasket({
            'clearConfirm': "<?php echo JText::_('JBZOO_CART_CLEAR_CONFIRM');?>",
            'quantityUrl' : "<?php echo $this->app->jbrouter->basketQuantity();?>",
            'deleteUrl'   : "<?php echo $this->app->jbrouter->basketDelete();?>",
            'clearUrl'    : "<?php echo $this->app->jbrouter->basketClear();?>"
        });
    });
</script>
