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


$view = $this->getView();
$this->app->jbassets->basket();
$this->app->jbassets->initJBPrice();
?>
<table class="jbbasket-table jsJBZooBasket">
    <thead>
    <tr>
        <th>#</th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_SKU'); ?></th>
        <th></th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?></th>
        <th style="min-width: 70px;"><?php echo JText::_('JBZOO_CART_ITEM_PRICE'); ?></th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_QUANTITY'); ?></th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_SUBTOTAL'); ?></th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    <?php
    $i = 0;
    $summa = 0;
    $count = 0;

    $currencyConvert = $view->appParams->get('global.jbzoo_cart_config.currency');
    $imageElementId = $view->appParams->get('global.jbzoo_cart_config.element-image');

    foreach ($view->basketItems as $hash => $basketItem) {

        $item = $basketItem['item'];

        $basketItem['price'] = $this->app->jbmoney->convert($basketItem['currency'], $currencyConvert, $basketItem['price']);

        $count += $basketItem['quantity'];

        $subtotal = $basketItem['quantity'] * $basketItem['price'];
        $summa += $subtotal;

        $image = $this->app->jbitem->renderImageFromItem($item, $imageElementId, true);

        echo '<tr class="row-' . $hash . '" data-itemId="' . $item->id . '" data-hash="' . $hash . '">';
        echo '<td>' . ++$i . '</td>';
        echo '<td>' . $basketItem['sku'] . '</td>';
        echo '<td>' . $image . '</td>';

        echo '<td>';
        echo '<a href="' . $this->app->route->item($item) . '" title="' . $item->name . '">' . $item->name . '</a>';

        if (isset($basketItem['priceParams']) && !empty($basketItem['priceParams'])) {
            foreach ($basketItem['priceParams'] as $key => $value) {
                if (!empty($value)) {
                    echo '<div><strong>' . $key . ':</strong> ' . $value . '</div>';
                }
            }
        }

        if (!empty($basketItem['priceDesc'])) {
            echo '<br/><span class="price-description">' . $basketItem['priceDesc'] . '</span>';
        }

        echo '</td>';

        if ($basketItem['price']) {
            echo '<td class="jsPricevalue" price="' . $basketItem['price'] . '">'
                . $this->app->jbmoney->toFormat($basketItem['price'], $currencyConvert)
                . ' </td>';
        } else {
            echo '<td> - </td>';
        }

        echo '<td><input type="text" class="jsQuantity input-quantity" value="' . $basketItem['quantity'] . '" /></td>';

        if ($basketItem['price']) {
            echo '<td class="jsSubtotal">' . $this->app->jbmoney->toFormat($subtotal, $currencyConvert) . '</td>';
        } else {
            echo '<td> - </td>';
        }

        echo '<td><input type="button" class="jbbutton jsDelete" itemid="' . $item->id . '" value="' . JText::_('JBZOO_CART_DELETE') . '" /></td>';
        echo "</tr>\n";
    }
    ?>
    </tbody>

    <tfoot>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><strong><?php echo JText::_('JBZOO_CART_TOTAL'); ?>:</strong></td>
        <td class="jsTotalCount"><?php echo $count; ?></td>
        <td class="jsTotalPrice"><?php echo $this->app->jbmoney->toFormat($summa, $currencyConvert); ?></td>
        <td>
            <input type="button" class="jbbutton jsDeleteAll" value="<?php echo JText::_('JBZOO_CART_REMOVE_ALL'); ?>"/>
        </td>
    </tr>
    </tfoot>
</table>

<script type="text/javascript">
    jQuery(function ($) {
        $('.jbzoo .jsJBZooBasket').JBZooBasket({
            'clearConfirm': "<?php echo JText::_('JBZOO_CART_CLEAR_CONFIRM');?>",
            'quantityUrl': "<?php echo $this->app->jbrouter->basketQuantity($view->appId);?>",
            'deleteUrl': "<?php echo $this->app->jbrouter->basketDelete($view->appId);?>",
            'clearUrl': "<?php echo $this->app->jbrouter->basketClear($view->appId);?>"
        });
    });
</script>
