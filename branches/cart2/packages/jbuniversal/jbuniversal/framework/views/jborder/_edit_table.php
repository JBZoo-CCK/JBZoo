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

$items = $order->getItems();
$jbmoney = $this->app->jbmoney;

$payment = $order->getPayment();
$shipping = $order->getShipping();
$currency = $order->getCurrency();

$modifiers = $order->getModifiers(JBCart::MODIFIER_ORDER);
$modifiersCount = count($modifiers);

?>

<h2><?php echo JText::_('JBZOO_ORDER_ITEMS_LIST'); ?></h2>

<table class="uk-table uk-table-striped uk-table-condensed">
    <thead>
    <tr>
        <th style="width:30px"><?php echo JText::_('JBZOO_ORDER_ITEM_SKU'); ?></th>
        <th style="width:90px"><?php echo JText::_('JBZOO_ORDER_ITEM_IMAGE'); ?></th>
        <th><?php echo JText::_('JBZOO_ORDER_ITEM_NAME'); ?></th>
        <th style="width:150px;text-align: right;"><?php echo JText::_('JBZOO_ORDER_PRICE_PER_PIECE'); ?></th>
        <th style="width:120px;text-align: center;"><?php echo JText::_('JBZOO_ORDER_ITEM_QUANTITY'); ?></th>
        <th style="width:150px;text-align: center;"><?php echo JText::_('JBZOO_ORDER_ITEM_COST'); ?></th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($items as $key => $row) :
        $item = $row->get('item');

        $quantity  = $row->get('quantity');
        $price     = $row->get('price');
        $priceItem = $jbmoney->convert($row->get('currency'), $currency, $price);
        $total     = $priceItem * $quantity;

        if ($item) {
            $itemLink = $this->app->jbrouter->adminItem($item);
        }

        ?>
        <tr>
            <td><?php echo $key + 1; ?></td>
            <td>
                <?php
                if ($row['image']) {
                    echo '<img src="' . JUri::root() . $row['image'] . '" />';
                } else {
                    echo '-';
                }
                ?>
            </td>
            <td>
                <?php echo $item ? '<a href="' . $itemLink . '" target="_blank">' . $row->get('name') . '</a>' : $row->get('name'); ?>

                <?php foreach ($row['priceParams'] as $label => $param) : ?>
                    <div>

                        <strong>
                            <?php echo $label; ?>
                            :
                        </strong>

                        <?php echo $param; ?>
                        <br/>

                    </div>
                <?php endforeach;

                if ($desc = $row->get('price_desc')) {
                    echo '<br><i>' . $desc . '</i>';
                } ?>
            </td>
            <td style="text-align: right;"><?php echo $jbmoney->toFormat($priceItem, $currency); ?></td>
            <td style="text-align: center;"><?php echo $quantity; ?></td>
            <td style="text-align: right;"><strong><?php echo $jbmoney->toFormat($total, $currency); ?></strong></td>
        </tr>
    <?php endforeach; ?>
    </tbody>

    <tfoot>
    <tr>
        <td colspan="2" style="border-bottom: none;"></td>
        <td colspan="3"><p>Промежуточный итог</p></td>
        <td style="text-align: right;font-size: 18px;"><em><?php echo $order->getTotalForItems(true); ?></em></td>
    </tr>

    <?php if ($payment) : ?>
        <tr>
            <td colspan="2" style="border-bottom: none;"></td>
            <th><p>Комиссия платежной системы</p></th>
            <td colspan="2"><?php echo $payment->getName(); ?></td>
            <td style="text-align: right;">
                <strong><?php echo $jbmoney->toFormat($payment->getRate(), $currency); ?></strong></td>
        </tr>
    <?php endif; ?>

    <?php if ($shipping) : ?>
        <tr>
            <td colspan="2" style="border-bottom: none;"></td>
            <th><p>Цена доставки</p></th>
            <td colspan="2"><?php echo $shipping->getName(); ?></td>
            <td style="text-align: right;">
                <strong><?php echo $jbmoney->toFormat($shipping->getRate(), $currency); ?></strong></td>
        </tr>
    <?php endif; ?>

    <?php if ($shipping || $payment) : ?>
        <tr>
            <td colspan="2" style="border-bottom: none;"></td>
            <td colspan="3"><p>Промежуточный итог</p></td>
            <td style="text-align: right; font-size: 18px;"><em><?php echo $order->getTotalForSevices(true); ?></em>
            </td>
        </tr>
    <?php endif; ?>

    <?php if (!empty($modifiers)) {
        $i = 0;
        foreach ($modifiers as $modifier) {
            $i++;

            $name = $modifier->getName();
            $rate = $modifier->getRate();

            ?>
            <?php if ($i == 1) { ?>

                <tr>
                    <td rowspan="<?php echo $modifiersCount; ?>" colspan="2" style="border-bottom: none;"></td>
                    <td rowspan="<?php echo $modifiersCount; ?>">
                        <strong>Прочее</strong> <br><i>(Элементы модификаторов цены)</i></td>
                    <td colspan="2"><?php echo $name; ?></td>
                    <td style="text-align: right;"><?php echo $rate; ?></td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td colspan="2"><?php echo $name; ?></td>
                    <td style="text-align: right;"><?php echo $rate; ?></td>
                </tr>
            <?php
            }
        }
    }
    ?>

    <tr>
        <td colspan="3" style="border-bottom: none;"></td>
        <td colspan="2" style="border-bottom: none;padding-top:24px;"><strong>Итого к оплате</strong></td>
        <td style="font-size: 24px;color:#a00;text-align:right;border-bottom: none;padding-top:24px;">
            <strong><?php echo $order->getTotalSum(true); ?></strong>
        </td>
    </tr>
    </tfoot>
</table>