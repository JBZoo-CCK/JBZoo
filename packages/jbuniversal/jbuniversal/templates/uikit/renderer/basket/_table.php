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

$this->app->jbassets->less('jbassets:less/cart/table.less');

$string = $this->app->jbstring;
$jbhtml = $this->app->jbhtml;

$order = JBCart::getInstance()->newOrder();

?>

<table class="jbcart-table jsJBZooCartTable">
    <thead>
    <tr>
        <th class="jbcart-col jbcart-col-image"></th>
        <th class="jbcart-col jbcart-col-name"><?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?></th>
        <th class="jbcart-col jbcart-col-price"><?php echo JText::_('JBZOO_CART_ITEM_PRICE'); ?></th>
        <th class="jbcart-col jbcart-col-quantity"><?php echo JText::_('JBZOO_CART_ITEM_QUANTITY'); ?></th>
        <th class="jbcart-col jbcart-col-subtotal"><?php echo JText::_('JBZOO_CART_ITEM_SUBTOTAL'); ?></th>
        <th class="jbcart-col jbcart-col-delete"></th>
    </tr>
    </thead>
    <tbody>

    <tr class="jbcart-row-empty">
        <td colspan="6"></td>
    </tr>

    <?php
    $total = JBCart::val();
    foreach ($view->items as $itemKey => $data) {
        $data = $this->app->data->create($data);
        unset($view->items[$itemKey]['params']);

        $item = $this->app->table->item->get($data->get('item_id'));
        $href = $this->app->route->item($item);

        $price = JBCart::val($data->get('total'));

        $quantity = $data->get('quantity', 1);

        $total->add($price->multiply($quantity, true));

        $image = '';
        if ($data->find('elements._image')) {
            $image = '<a ' . $jbhtml->buildAttrs(array(
                    'class' => 'jbimage-link',
                    'title' => $item->name,
                    'href'  => $href
                )) . '><img  ' . $jbhtml->buildAttrs(array(
                    'class'  => 'jbimage',
                    'src'    => $data->find('elements._image'),
                    'alt'    => $item->name,
                    'title'  => $item->name,
                    'width'  => 75,
                    'height' => 75
                )) . ' /></a>';
        }

        ?>
        <tr class="jbcart-row jsCartTableRow js<?php echo $itemKey; ?>" data-key="<?php echo $itemKey; ?>">
            <td class="jbcart-image"><?php echo $image; ?></td>
            <td class="jbcart-name">
                <?php
                $html = array();

                // name
                $html[] = '<a class="jbcart-url" href="' . $href . '">' . $item->name . '</a>';

                // sku
                if ($data->find('elements._sku')) {
                    $html[] = '<div class="jbcart-sku">';
                    $html[] = '<span class="jbcart-sku-key">' . JText::_('JBZOO_CART_ITEM_SKU') . ':</span>';
                    $html[] = '<span class="jbcart-sku-value uk-badge uk-badge-notification">' . $data->find('elements._sku') . '</span>';
                    $html[] = '</div>';
                }

                // params
                if ($paramValues = $data->get('values', array())) {
                    foreach ($paramValues as $key => $value) {
                        if (!empty($value)) {
                            $html[] = '<div class="jbcart-param">';
                            $html[] = '<span class="jbcart-param-key">' . $key . ':</span>';
                            $html[] = '<span class="jbcart-param-value">' . $value . '</span>';
                            $html[] = '</div>';
                        }
                    }
                }

                // output
                echo implode("\n", $html);
                ?>
            </td>
            <td class="jbcart-price jsPrice"><?php echo $price->html(); ?></td>
            <td class="jbcart-quantity"><?php echo $jbhtml->quantity($quantity, $data->find('params._quantity', array())); ?></td>
            <td class="jbcart-subtotal jsSubtotal jsPrice-<?php echo $itemKey;?>">
                <?php echo $price->multiply($quantity, true)->html(); ?>
            </td>
            <td class="jbcart-delete"><a class="uk-button uk-button-danger uk-button-small round jsDelete"><i class="uk-icon-trash-o"></i></a></td>
        </tr>
    <?php } // endforeach ?>
    </tbody>
    <tfoot>
    <tr class="jbcart-row-total">
        <td colspan="3">
            <div>
                <span class="jbcart-label"><?php echo JText::_('Товаров в корзине'); ?>:</span>
                <span class="jbcart-value jsTotalCount"><?php echo $order->getTotalCount(); ?></span>
            </div>
            <div>
                <span class="jbcart-label"><?php echo JText::_('на сумму'); ?>:</span>
                <span class="jbcart-value jsTotalPrice"><?php echo $order->getTotalForItems()->html(); ?></span>
            </div>
        </td>
        <td>
            <?php if ($view->shipping) : ?>
                <div class="jbcart-label"><?php echo JText::_('Доставка'); ?>:</div>
                <div class="jbcart-value jsShippingPrice"><?php echo $order->getShippingPrice()->html(); ?></div>
            <?php endif; ?>
        </td>
        <td colspan="2">
            <div class="jbcart-label"><?php echo JText::_('Итого к оплате'); ?>:</div>
            <div class="jbcart-value jsTotal"><?php echo $order->getTotalSum()->html(); ?></div>
        </td>
    </tr>
    <tr class="jbcart-row-remove">
        <td colspan="6">
            <a class="jsDeleteAll item-delete-all uk-button uk-button-danger">
                <i class="uk-icon-trash-o"></i>
                <?php echo JText::_('JBZOO_CART_REMOVE_ALL'); ?>
            </a>
        </td>
    </tr>
    </tfoot>
</table>
