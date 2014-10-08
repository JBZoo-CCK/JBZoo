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
    $sum = 0;
    $count = 0;

    $default = $view->config->get('default_currency', 'EUR');
    $jbHTML = $this->app->jbhtml;
    $jbMoney = $this->app->jbmoney;

    foreach ($view->items as $id => $data) {

        extract($data, EXTR_PREFIX_ALL, '_');

        $item = $this->app->table->item->get($__item_id);

        $image = null;
        $price = $jbMoney->convert($__currency, $default, $__price);
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

        echo '<td>' . ++$i . '</td>';
        echo '<td><span>' . $__sku . '</span></td>';
        echo '<td>' . $image . '</td>';

        echo '<td>';
        echo '<a href="' . $href . '" title="' . $__name . '">' . $__name . '</a>';
        unset($item);
        if (isset($__priceParams) && !empty($__priceParams)) {
            foreach ($__priceParams as $key => $value) {
                if (!empty($value)) {
                    echo '<div><strong>' . $key . ':</strong> ' . $value . '</div>';
                }
            }
        }

        echo '</td>';

        if ($__price) {
            echo '<td class="jsPricevalue" price="' . $price . '">'
                 . $jbMoney->toFormat($price, $default)
                 . '</td>';
        } else {
            echo '<td> - </td>';
        }

        echo '<td><input type="text" class="jsQuantity input-quantity" value="' . $__quantity . '" /></td>';

        if ($price) {
            echo '<td class="jsSubtotal basket-table-subtotal">
            <span
             data-total="' . $jbMoney->format($price) . '"
             data-noformat="' . $subtotal . '"
             class="basket-table-value jsValue">
            ' . $jbMoney->format($subtotal) . '
            </span>
            <span class="jsCurrency">
            ' . strtoupper($default) . '
            </span></td>';
        } else {
            echo '<td> - </td>';
        }

        echo '<td><input type="button" class="jbbutton jsDelete" itemid="' . $id . '" value="'
             . JText::_('JBZOO_CART_DELETE') . '" /></td>';
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
            <span class="jsValue">
                <?php echo $jbMoney->format($sum); ?>
            </span>
            <span class="jsCurrency">
                <?php echo strtoupper($default); ?>
            </span>
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
            'quantityUrl' : "<?php echo $this->app->jbrouter->basketQuantity();?>",
            'deleteUrl'   : "<?php echo $this->app->jbrouter->basketDelete();?>",
            'clearUrl'    : "<?php echo $this->app->jbrouter->basketClear();?>"
        });
    });
</script>
