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

        $discount = $order->val($row->find('elements._discount'));
        $margin   = $order->val($row->find('elements._margin'));

        $quantity = (float)$row->get('quantity', 1);

        $item_value = $order->val($row->find('elements._value'));
        $item_price = $item_value->getClone()->add($margin)->minus($discount);

        $rowspan = count($modifiers) + 2;
        $this->count += $quantity; ?>

        <tr class="item-row">

            <td rowspan="<?php echo $rowspan; ?>">
                <?php if ($row->find('elements._image')) :
                    $imagePath = $this->app->jbimage->resize($row->find('elements._image'), 90);
                    echo '<img src="' . $imagePath->url . '" class="item-image" />';
                else :
                    echo '-';
                endif; ?>
            </td>

            <td>
                <p>
                    <?php echo JText::_('JBZOO_ORDER_ITEM_id') . ':' . $row->get('item_id'); ?>
                </p>

                <?php if ($row->get('sku')) :
                    echo '<p>' . JText::_('JBZOO_ORDER_ITEM_SKU') . ':' . $row->get('sku') . '</p>';
                endif; ?>

                <p>
                    <?php if ($item) :
                        $itemLink = $this->app->jbrouter->adminItem($item);
                        echo '<a href="' . $itemLink . '" target="_blank">' . $row->get('item_name') . '</a>';
                    else :
                        echo $row->get('item_name');
                    endif; ?>
                </p>

                <?php if (!empty($row['values'])) : ?>
                    <ul>
                        <?php foreach ($row['values'] as $label => $param) :
                            echo '<li><strong>' . $label . ':</strong> ' . $param . '</li>';
                        endforeach; ?>
                    </ul>
                <?php endif;

                if ($row->get('description')) :
                    echo '<p><i>' . $row->get('description') . '</i></p>';
                endif; ?>
            </td>

            <td class="item-price4one">
                <?php
                echo '<p>' . $item_value->html() . '</p>';
                if (!$margin->isEmpty()) {
                    echo '<p>' . JText::_('JBZOO_ORDER_ITEM_MARGIN') . ':' . $margin->htmlAdv($currency, true) . '</p>';
                }
                if (!$discount->isEmpty()) {
                    echo '<p>' . JText::_('JBZOO_ORDER_ITEM_DISCOUNT') . ':' . $discount->negative()
                            ->htmlAdv($currency, false) . '</p>';
                } ?>
            </td>

            <td class="item-quantity">
                <?php echo $quantity; ?>
            </td>
            <td class="align-right">
                <?php echo $item_price->multiply($quantity)->html(); ?>
            </td>
        </tr>

        <?php if (!empty($modifiers)) : ?>
        <tr class="item-modifiers">
            <td></td>
            <td colspan="3">
                <strong>
                    <?php echo JText::_('JBZOO_ORDER_ITEM_MODIFIERS'); ?>:
                </strong>
            </td>
        </tr>
        <?php foreach ($modifiers as $id => $modifier) :
            $modified = $row->find('modifiers.' . $id);
            $modifier->bindData(array(
                'rate'     => $modified,
                'currency' => $currency
            )); ?>
            <tr class="item-modifiers">
                <?php echo $modifier->edit($item_price); ?>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4"></td>
            <td class="item-total align-right subtotal-money">
                <?php
                $item_total = $item_price->multiply($quantity, true);
                if ($item_total->isNegative()) :
                    $item_total->setEmpty();
                endif;
                echo $item_total->html(); ?>
            </td>
        </tr>
    <?php endif;
        $this->sum->add($item_total);
    endforeach;
endif;