<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$items = $order->getItems();

$payment  = $order->getPayment();
$shipping = $order->getShipping();

$orderModifiers = $order->getModifiers(JBCart::MODIFIER_ORDER);
$itemModifiers  = $order->getModifiers(JBCart::MODIFIER_ITEM);

$summa      = $order->val();
$baseCur    = $summa->cur();
$emptyRow   = '<tr class="empty-row"><td colspan="50"></td></tr>';
$totalCount = 0;

?>
<h2><?php echo JText::_('JBZOO_ORDER_ITEMS_LIST'); ?></h2>

<table class="uk-table uk-table-condensed jborder-details-table">
    <thead>
    <tr>
        <th class="col-image"><?php echo JText::_('JBZOO_ORDER_ITEM_IMAGE'); ?></th>
        <th class="col-name"><?php echo JText::_('JBZOO_ORDER_ITEM_NAME'); ?></th>
        <th class="col-item"><?php echo JText::_('JBZOO_ORDER_PRICE_PER_ITEM'); ?></th>
        <th class="col-quantity"><?php echo JText::_('JBZOO_ORDER_ITEM_QUANTITY'); ?></th>
        <th class="col-total"><?php echo JText::_('JBZOO_ORDER_ITEM_TOTAL'); ?></th>
    </tr>
    </thead>

    <tbody>
    <?php
    foreach ($items as $key => $row) :

        $item      = $row->get('item');
        $priceItem = $order->val($row->get('total'));
        $quantity  = (float)$row->get('quantity', 1);
        $totalItem = $priceItem->multiply($quantity, true);
        $discount  = $order->val($row->find('elements._discount'));
        $margin    = $order->val($row->find('elements._margin'));
        $rowspan   = count($itemModifiers) + 1;
        $totalCount += $quantity;
        ?>
        <tr class="item-row">
            <td rowspan="<?php echo $rowspan; ?>">
                <?php
                if ($row->find('elements._image')) {
                    $imagePath = $this->app->jbimage->resize($row->find('elements._image'), 90);
                    echo '<img src="' . $imagePath->url . '" class="item-image" />';
                } else {
                    echo '-';
                }
                ?>
            </td>
            <td>
                <p><?php echo JText::_('JBZOO_ORDER_ITEM_id'); ?>: <?php echo $row->get('item_id'); ?></p>
                <?php if ($row->get('sku')) : ?>
                    <p><?php echo JText::_('JBZOO_ORDER_ITEM_SKU'); ?>: <?php echo $row->get('sku'); ?></p>
                <?php endif; ?>

                <p><?php if ($item) {
                        $itemLink = $this->app->jbrouter->adminItem($item);
                        echo '<a href="' . $itemLink . '" target="_blank">' . $row->get('item_name') . '</a>';
                    } else {
                        echo $row->get('item_name');
                    }
                    ?></p>

                <?php if (!empty($row['values'])) : ?>
                    <ul>
                        <?php foreach ($row['values'] as $label => $param) : ?>
                            <li><strong><?php echo $label; ?>:</strong> <?php echo $param; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php
                if ($desc = $row->get('price_desc')) {
                    echo '<p><i>' . $desc . '</i></p>';
                } ?>
            </td>
            <td class="item-price4one">
                <?php
                echo '<p>' . $priceItem->html() . '</p>';
                //TODO margin & discount empties
                if (!$margin->isEmpty()) {
                    echo '<p>Наценка:' . $margin->htmlAdv($baseCur, true) . '</p>';
                }
                if (!$discount->isEmpty()) {
                    echo '<p>Скидка:' . $discount->htmlAdv($baseCur, true) . '</p>';
                }
                ?>
            </td>
            <td class="item-quantity"><?php echo $quantity; ?></td>
            <td class="item-total align-right subtotal-money"><?php echo $totalItem->html(); ?></td>
        </tr>

        <?php
        if (!empty($itemModifiers)) {
            $i = 0;
            ?>
            <tr class="item-modifiers">
                <td></td>
                <td colspan="3"><strong>Модификаторы:</strong></td>
            </tr>
            <?php
            foreach ($itemModifiers as $modifier) {
                $i++;
                $totalItem->addModify($modifier);
                ?>
                <tr class="item-modifiers">
                    <td></td>
                    <td><?php echo $modifier->getName(); ?></td>
                    <td class="align-right"><?php echo $modifier->getRate()->htmlAdv($baseCur, true); ?></td>
                    <td class="align-right"><?php echo $totalItem->html(); ?></td>
                </tr>
            <?php
            }
        }

        $summa->add($totalItem);
        ?>
    <?php endforeach; ?>
    </tbody>
    <tfoot>

    <?php if ($shipping || $payment || $orderModifiers) : ?>
        <tr>
            <td class="noborder-btm"></td>
            <td colspan="2"><p><?php echo JText::_('JBZOO_ORDER_SUBTOTAL'); ?></p></td>
            <td class="align-right"><p><?php echo $totalCount; ?></p></td>
            <td class="align-right subtotal-money"><?php echo $summa->html(); ?></td>
        </tr>
        <?php echo $emptyRow; ?>
    <?php endif; ?>

    <?php if ($payment) :
        $summa->addModify($payment);
        ?>
        <tr>
            <td class="noborder-btm"></td>
            <th><p><?php echo JText::_('JBZOO_ORDER_PAYMENT_FEE'); ?></p></th>
            <td><?php echo $payment->getName(); ?></td>
            <td class="align-right"><?php echo $payment->getRate()->htmlAdv($baseCur, true); ?></td>
            <td class="align-right"><?php echo $summa->html(); ?></td>
        </tr>
    <?php endif; ?>

    <?php if ($shipping) :
        $summa->addModify($shipping);
        ?>
        <tr>
            <td class="noborder-btm"></td>
            <th><p><?php echo JText::_('JBZOO_ORDER_SHIPPING_FEE'); ?></p></th>
            <td><?php echo $shipping->getName(); ?></td>
            <td class="align-right"><?php echo $shipping->getRate()->htmlAdv($baseCur, true); ?></td>
            <td class="align-right"><?php echo $summa->html(); ?></td>
        </tr>
    <?php endif; ?>

    <?php if ($shipping || $payment) : ?>
        <tr>
            <td style="border-bottom: none;"></td>
            <td colspan="3"><p><?php echo JText::_('JBZOO_ORDER_SUBTOTAL'); ?></p></td>
            <td class="align-right subtotal-money"><?php echo $summa->html(); ?></td>
        </tr>
        <?php echo $emptyRow; ?>
    <?php endif; ?>

    <?php if (!empty($orderModifiers)) {
        $i = 0;
        foreach ($orderModifiers as $modifier) {
            $i++;
            $summa->addModify($modifier);
            ?>
            <?php if ($i == 1) { ?>
                <tr>
                    <td rowspan="<?php echo count($orderModifiers); ?>" class="noborder-btm"></td>
                    <td rowspan="<?php echo count($orderModifiers); ?>">
                        <strong><?php echo JText::_('JBZOO_ORDER_MODIFIERS_OTHER'); ?></strong><br>
                        <i>(<?php echo JText::_('JBZOO_ORDER_MODIFIERS_OTHER_ELEMENTS'); ?>)</i>
                    </td>
                    <td><?php echo $modifier->getName(); ?></td>
                    <td class="align-right"><?php echo $modifier->getRate()->htmlAdv($baseCur, true); ?></td>
                    <td class="align-right"><?php echo $summa->html(); ?></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td><?php echo $modifier->getName(); ?></td>
                    <td class="align-right"><?php echo $modifier->getRate()->htmlAdv($baseCur, true); ?></td>
                    <td class="align-right"><?php echo $summa->html(); ?></td>
                </tr>
            <?php
            }
        }
    }
    ?>

    <tr>
        <td colspan="2" class="noborder-btm"></td>
        <td class="total-name" colspan="2"><?php echo JText::_('JBZOO_ORDER_TOTALPRICE'); ?></td>
        <td class="total-value"><?php echo $summa->html(); ?></td>
    </tr>
    </tfoot>
</table>
