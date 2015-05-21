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

$itemsHtml = $order->renderItems(array(
    'currency'     => $order->getCurrency(),
    'admin_url'    => true,
    'image_width'  => 75,
    'image_height' => 75,
));

if (!empty($items)) :
    foreach ($items as $key => $row) :
        $i    = 0;
        $item = $row->get('item');

        $itemHtml = $itemsHtml[$key];

        $discount  = $order->val();
        $margin    = $order->val();
        $modifiers = $order->getModifiersItemPrice(null, $row);

        $quantity = (float)$row->get('quantity', 1);
        if ((int)$row->get('isOverlay')) {
            $variations = $row->get('variations');
            if (!empty($variations)) {
                ksort($variations);
                $basic = $variations[0];

                $itemValue = $order->val($basic['_value']['value']);
                $itemPrice = $itemValue->getClone();
                foreach ($variations as $j => $variant) {
                    if ($j !== 0) {
                        $itemPrice->add($variant['_value']['value']);
                    }
                }
            }
        } else {
            $discount = $order->val($row->find('elements._discount'));
            $margin   = $order->val($row->find('elements._margin'));

            $itemValue = $order->val($row->find('elements._value'));
            $itemPrice = $itemValue->getClone()->add($margin)->minus($discount);
        }


        $modCount = count($modifiers);
        if ($modCount == 0) {
            $rowspan = 1;
        } else if ($modCount == 1) {
            $rowspan = 3;
        } else {
            $rowspan = $modCount + 2;
        }

        $this->count += $quantity; ?>

        <tr class="item-row">
            <td rowspan="<?php echo $rowspan; ?>"><?php echo $itemHtml['image'];?></td>
            <td>
                <?php echo $itemHtml['name'];?>
                <?php echo $itemHtml['itemid']; ?>
                <?php echo $itemHtml['sku']; ?>
                <?php echo $itemHtml['params'];?><br>
                <?php echo $itemHtml['description'];?>
            </td>
            <td class="item-price4one">
                <?php echo '<p>' . $itemValue->html() . '</p>'; ?>

                <?php if (!$margin->isEmpty()) {
                    echo '<p>' . JText::_('JBZOO_ORDER_ITEM_MARGIN') . ':' . $margin->htmlAdv($currency, true) . '</p>';
                } ?>

                <?php if (!$discount->isEmpty()) {
                    echo '<p>' . JText::_('JBZOO_ORDER_ITEM_DISCOUNT') . ':' . $discount->negative()->htmlAdv($currency, false) . '</p>';
                } ?>
            </td>

            <td class="item-quantity"><?php echo $itemHtml['quantity'];?></td>
            <td class="align-right"><?php echo $itemPrice->multiply($quantity, true)->html(); ?></td>
        </tr>

        <?php
        if (!empty($modifiers)) : ?>
            <tr class="item-modifiers">
                <td></td>
                <td colspan="3">
                    <strong><?php echo JText::_('JBZOO_ORDER_ITEM_MODIFIERS'); ?>:</strong>
                </td>
            </tr>

            <?php foreach ($modifiers as $id => $modifier) :
                $modifier->bindData($row->find('modifiers.' . $id));
                $itemPrice->add($modifier->get('rate'));

                $editHtml = $modifier->edit(array(
                    'currency' => $this->get('currency')
                ));
                ?>
                <tr class="item-modifiers">
                    <td class="align-right"></td>
                    <td class="align-right"><?php echo $modifier->getName(); ?></td>
                    <td class="align-right"><?php echo $editHtml; ?></td>
                    <td class="align-right"><?php echo $itemPrice->html(); ?></td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td colspan="4"></td>
                <td class="item-total align-right subtotal-money">
                    <?php
                    $itemPrice->multiply($quantity);
                    if ($itemPrice->isNegative()) {
                        $itemPrice->setEmpty();
                    }
                    echo $itemPrice->html(); ?>
                </td>
            </tr>
        <?php endif;

        $this->sum->add($itemPrice);

    endforeach;
endif;