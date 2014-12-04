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
$params = array(); ?>

<table class="jbbasket-table jsJBZooCart">
    <thead>
    <tr>
        <th></th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?></th>
        <th><?php echo JText::_('JBZOO_CART_ITEM_PRICE'); ?></th>
        <th style="width:150px;"><?php echo JText::_('JBZOO_CART_ITEM_QUANTITY'); ?></th>
        <th style="width:180px;"><?php echo JText::_('JBZOO_CART_ITEM_SUBTOTAL'); ?></th>
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

    <?php if (!empty($view->items)) :

    $i     = 0;
    $total = JBCart::val();
    $count = 0;
    $image = null;

    $string  = $this->app->jbstring;
    $jbHTML  = $this->app->jbhtml;
    $jbMoney = $this->app->jbmoney;
    $default = $view->config->get('default_currency', 'EUR');

    foreach ($view->items as $id => $data) {

        $data = $this->app->data->create($data);

        //dump($data);

        $encode   = base64_encode($id);
        $quantity = $data['quantity'];
        $price    = JBCart::val($data['total']);

        $item = $this->app->table->item->get($data['item_id']);
        $href = $this->app->route->item($item);

        $count += $quantity;
        $total->add($price->multiply($quantity, true));

        if ($data->find('elements._image')) {

            $url = $this->app->jbimage->resize($data->find('elements._image'), 75, 75);

            $image = '<a ' . $jbHTML->buildAttrs(array(
                    'class' => 'jbimage-link',
                    'title' => $item->name,
                    'href'  => $href
                )) . '><img  ' . $jbHTML->buildAttrs(array(
                    'class'  => 'jbimage',
                    'src'    => $url->url,
                    'alt'    => $item->name,
                    'title'  => $item->name,
                    'width'  => 75,
                    'height' => 75
                )) . ' /></a>';
        }?>

        <tr class="row-<?php echo $encode; ?> jbbasket-item-row" data-item_id="<?php echo $data['item_id']; ?>"
            data-key="<?php echo $encode; ?>">

            <td class="jbbasket-item-image">
                <?php echo $image; ?>
            </td>
            <td class="jbbasket-item-name">
                <a href="<?php echo $href; ?>" title="<?php echo $data['item_name']; ?>">
                    <?php echo $data['item_name']; ?>
                </a>

                <?php if ($data->find('elements._sku')) : ?>

                    <div class="jbbasket-item-param">
                        <span class="jbbasket-item-sku">
                            <?php echo JText::_('JBZOO_CART_ITEM_SKU'); ?>:
                            <?php echo $data->find('elements._sku'); ?>
                        </span>
                    </div>

                <?php endif;
                if (!empty($data['values'])) {
                    foreach ($data['values'] as $key => $value) {
                        if (JString::strlen($value) !== 0) : ?>
                            <div class="jbbasket-item-param">
                                <span class="jbbasket-param-key">
                                    <?php echo $key; ?>:
                                </span>
                                <?php echo $value; ?>
                            </div>
                        <?php endif;
                    }
                } ?>
            </td>

            <td class="jsValue jbbasket-item-price">
                <?php echo $price->html(); ?>
            </td>

            <td class="jbbasket-item-quantity">
                <?php
                $params = array();
                if (isset($data->params['_quantity'])) {
                    $params = $data->params['_quantity'];
                }
                echo $this->app->jbhtml->quantity($quantity, $params);
                ?>
            </td>

            <td class="jsSubtotal jbbasket-subtotal">
            <span class="jbbasket-table-value jsValue">
                <?php echo $price->multiply($quantity, true)->html(); ?>
                <a class="item-delete jbbutton orange jsDelete"
                   item_id="<?php echo $data['item_id']; ?>">x</a>
            </span>
            </td>
        </tr>

        <?php if (!empty($data['params'])) {
            foreach ($data['params'] as $key => $param) {
                if (!empty($param)) {
                    $params[$encode][$key] = $param;
                }
            }
        }
    } ?>

    <tfoot>
    <tr class="null">
        <td class="null jsNull"></td>
        <td class="null jsNull"></td>
        <td class="null jsNull"></td>
        <td class="null jsNull"></td>
        <td class="null jsNull">
            <a class="jsDeleteAll item-delete-all jbbutton orange"><?php echo JText::_('JBZOO_CART_EMPTY'); ?></a>
        </td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td style="width:240px;">
            В корзине
            <span class="jsTotalCount jbitems-total-count">
                <span class="jsValue"><?php echo $count; ?></span>
            </span>
            <span data-word="товар" class="morphology jsMorphology">
                <?php echo $string->declension($count, 'товар', 'товара', 'товаров'); ?>
            </span>

            на сумму:
            <div class="jsTotalPrice">
                <span class="jsValue jbtotal-price">
                    <?php echo $total->html(); ?>
                </span>
            </div>
        </td>

        <td>
            <?php if ($view->shipping) : ?>
                <span class="jbasket-label">Доставка: </span>
                <span class="shipping-price jsShippingPrice">
                        <span class="jsValue shipping-total">
                            Бесплатно
                        </span>
                        <span class="jsCurrency shipping-currency">

                        </span>
                </span>
            <?php endif; ?>
        </td>

        <td class="jsTotalPrice jbbasket-total-price">
            <span class="jbasket-label">Итого к оплате:</span>
            <span class="jsValue jbtotal-price"><?php echo $total->html(); ?></span>
        </td>

    </tr>

    </tfoot>
    <?php endif; ?>
</table>


<?php
$params = array(
    'confirm_message' => JText::_('JBZOO_CART_CLEAR_CONFIRM'),
    'url_quantity'    => $this->app->jbrouter->basketQuantity(),
    'url_delete'      => $this->app->jbrouter->basketDelete(),
    'url_clear'       => $this->app->jbrouter->basketClear(),
    'params'          => (object)$params
);
?>
<script type="text/javascript">
    jQuery(function ($) {
        $(".jbzoo .jsJBZooCart").JBZooCart(<?php echo json_encode($params);?>);
    });
</script>
