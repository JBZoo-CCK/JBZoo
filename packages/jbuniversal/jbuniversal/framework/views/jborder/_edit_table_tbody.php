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

if (!empty($items)) :
    foreach ($items as $key => $row) :
        $i    = 0;
        $item = $row->get('item');

        $discount  = $order->val($row->find('elements._discount'));
        $margin    = $order->val($row->find('elements._margin'));
        $modifiers = $order->getModifiersItemPrice(null, $row);

        $quantity = (float)$row->get('quantity', 1);

        $itemValue = $order->val($row->find('elements._value'));
        $itemPrice = $itemValue->getClone()->add($margin)->minus($discount);

        $rowspan = count($modifiers) + 2;
        $this->count += $quantity; ?>

        <tr class="item-row">

            <td rowspan="<?php echo $rowspan; ?>">
                <?php if ($row->find('elements._image')) {
                    $imagePath = $this->app->jbimage->resize($row->find('elements._image'), 90);
                    echo '<img src="' . $imagePath->url . '" class="item-image" />';
                } else {
                    echo '-';
                }?>
            </td>

            <td>
                <p><?php echo JText::_('JBZOO_ORDER_ITEM_id') . ':' . $row->get('item_id'); ?></p>

                <?php if ($row->get('sku')) {
                    echo '<p>' . JText::_('JBZOO_ORDER_ITEM_SKU') . ':' . $row->get('sku') . '</p>';
                } ?>

                <p><?php if ($item) {
                        $itemLink = $this->app->jbrouter->adminItem($item);
                        echo '<a href="' . $itemLink . '" target="_blank">' . $row->get('item_name') . '</a>';
                    } else {
                        echo $row->get('item_name');
                    }?>
                </p>

                <?php if (!empty($row['values'])) : ?>
                    <ul>
                        <?php foreach ($row['values'] as $label => $param) :
                            echo '<li><strong>' . $label . ':</strong> ' . $param . '</li>';
                        endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($row->get('description')) :
                    echo '<p><i>' . $row->get('description') . '</i></p>';
                endif; ?>
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

            <td class="item-quantity"><?php echo $quantity; ?></td>
            <td class="align-right"><?php echo $itemPrice->multiply($quantity)->html(); ?></td>
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