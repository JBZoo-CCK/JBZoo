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

    if (!empty($view->items)) {
    $i = 0;
    $summa = 0;
    $count = 0;

    $currency = $view->config->get('default_currency', 'EUR');

    foreach ($view->items as $id => $data) {
        if (empty($data)) {
            continue;
        }
        $variant = null;

        if (strpos($id, '-')) {
            list($itemId, $variant) = explode('-', $id);
        }

        $item   = $this->app->table->item->get($data['item_id']);
        $option = !empty($variant) || $variant == '0' ? '#' . ($variant + 1) : JText::_('JBZOO_CART_ITEM_VARIANT_BASIC');
        $option = JText::sprintf('JBZOO_CART_ITEM_VARIANT_NO', $option);

        $price = $this->app->jbmoney->convert($data['currency'], $currency, $data['price']);

        $count += $data['quantity'];
        $subtotal = $data['quantity'] * $price;
        $summa += $subtotal;

        $image = ''; //$this->app->jbitem->renderImageFromItem($item, $imageElementId, true);

        echo '<tr class="row-' . $id . '" data-itemId="' . $data['item_id'] . '" data-key="' . $id . '">';
        echo '<td>' . ++$i . '</td>';
        echo '<td>' . $data['sku'] . '</td>';
        echo '<td>' . $image . '</td>';

        echo '<td>';
        echo '<a href="' . $this->app->route->item($item) . '" title="' . $data['name'] . '">' . $data['name'] . '</a><br/><i>(' . $option . ')</i>';
        unset($item);
        if (isset($data['priceParams']) && !empty($data['priceParams'])) {
            foreach ($data['priceParams'] as $key => $value) {
                if (!empty($value)) {
                    echo '<div><strong>' . $key . ':</strong> ' . $value . '</div>';
                }
            }
        }

        echo '</td>';

        if ($data['price']) {
            echo '<td class="jsPricevalue" price="' . $price . '">'
                . $this->app->jbmoney->toFormat($price, $currency)
                . '</td>';
        } else {
            echo '<td> - </td>';
        }

        echo '<td><input type="text" class="jsQuantity input-quantity" value="' . $data['quantity'] . '" /></td>';

        if ($price) {
            echo '<td class="jsSubtotal basket-table-subtotal">
            <span class="basket-table-value jsValue">
            ' . $this->app->jbmoney->toFormat($subtotal, $currency) . '
            </span></td>';
        } else {
            echo '<td> - </td>';
        }

        echo '<td><input type="button" class="jbbutton jsDelete" itemid="' . $id . '" value="' . JText::_('JBZOO_CART_DELETE') . '" /></td>';
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
        <td class="jsTotalCount">
            <span class="jsValue"><?php echo $count; ?></span>
        </td>
        <td class="jsTotalPrice">
            <span class="jsValue"><?php echo $this->app->jbmoney->toFormat($summa, $currency); ?></span>
        </td>
        <td>
            <input type="button" class="jbbutton jsDeleteAll"
                   value="<?php echo JText::_('JBZOO_CART_REMOVE_ALL'); ?>"/>
        </td>
    </tr>
    </tfoot>
    <?php } ?>
</table>

<script type="text/javascript">
    jQuery(function ($) {
        $('.jbzoo .jsJBZooBasket').JBZooBasket({
            'clearConfirm': "<?php echo JText::_('JBZOO_CART_CLEAR_CONFIRM');?>",
            'quantityUrl': "<?php echo $this->app->jbrouter->basketQuantity();?>",
            'deleteUrl': "<?php echo $this->app->jbrouter->basketDelete();?>",
            'clearUrl': "<?php echo $this->app->jbrouter->basketClear();?>"
        });
    });
</script>
