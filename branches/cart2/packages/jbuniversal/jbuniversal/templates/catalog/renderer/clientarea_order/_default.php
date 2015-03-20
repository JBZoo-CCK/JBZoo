<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$this->app->jbassets->less('jbassets:less/cart/clientarea.less');

$order = $vars['object'];
$view  = $vars['view'];

$itemsHtml = $order->renderItems();
$items     = $order->getItems();

$created  = $this->app->html->_('date', $order->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());
$modified = $this->app->html->_('date', $order->modified, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());

echo $this->partial('clientarea_orders', 'default.styles');

$this->app->document->setTitle(JText::sprintf('JBZOO_CLIENTAREA_ORDERNAME_DATE', $order->getName(), $created));

?>

<div class="jbclientarea">

    <table class="jbclientarea-order-table">
        <thead>
        <tr>
            <th class="jbclientarea-image"></th>
            <th class="jbclientarea-info"><?php echo JText::_('JBZOO_CLIENTAREA_ITEM_NAME'); ?></th>
            <th class="jbclientarea-price4one"><?php echo JText::_('JBZOO_CLIENTAREA_ITEM_PRICE'); ?></th>
            <th class="jbclientarea-quantity"><?php echo JText::_('JBZOO_CLIENTAREA_ITEM_QUANTITY'); ?></th>
            <th class="jbclientarea-totalsum"><?php echo JText::_('JBZOO_CLIENTAREA_ITEM_TOTALSUM'); ?></th>
        </tr>
        </thead>

        <tbody>

        <?php
        $j = 0;

        $itemCount = count($items);
        foreach ($items as $key => $item) :
            $itemHtml = $itemsHtml[$key];
            $first    = ($j == 0) ? ' first' : '';
            $last     = ($j == $itemCount - 1) ? ' last' : '';
            $j++;
            ?>
            <tr class="jbclientarea-item jbclientarea-item-<?php echo $item->item_id . $class . $first . $last; ?>">
                <td class="jbclientarea-item-image"><?php echo $itemHtml['image']; ?></td>
                <td class="jbclientarea-item-info">
                    <?php echo $itemHtml['itemid']; ?>
                    <?php echo $itemHtml['sku']; ?>
                    <?php echo $itemHtml['name']; ?>
                    <?php echo $itemHtml['params']; ?>
                    <?php echo $itemHtml['description']; ?>
                </td>
                <td class="jbclientarea-item-price4one"><?php echo $itemHtml['price4one']; ?></td>
                <td class="jbclientarea-item-quantity"><?php echo $itemHtml['quantity']; ?></td>
                <td class="jbclientarea-item-totalsum"><?php echo $itemHtml['totalsum']; ?></td>
            </tr>
        <?php endforeach; ?>

        <?php
        $modifiers = $order->getModifiersOrderPrice();
        if (!empty($modifiers)) {
            foreach ($modifiers as $modifier) {
                $rate = $order->val($modifier->get('rate'));
                ?>
                <tr class="jbclientarea-modifier">
                    <td class="jbclientarea-emptycell"></td>
                    <td class="jbclientarea-label" colspan="3"><?php echo $modifier->getName(); ?></td>
                    <td class="jbclientarea-value"><?php echo $rate->html(); ?></td>
                </tr>
            <?php
            }
        }
        ?>

        <?php if ($shipping = $order->getShipping()) : ?>
            <tr class="jbclientarea-shipping">
                <td class="jbclientarea-emptycell"></td>
                <td class="jbclientarea-label" colspan="3"><?php echo $shipping->getName(); ?></td>
                <td class="jbclientarea-value"><?php echo $shipping->getRate()->html(); ?></td>
            </tr>
        <?php endif; ?>

        <tr class="jbclientarea-total">
            <td class="jbclientarea-emptycell"></td>
            <td class="jbclientarea-label" colspan="3"><?php echo JText::_('JBZOO_ORDER_ITEM_TOTAL') ?>:</td>
            <td class="jbclientarea-value"><?php echo $order->getTotalSum()->html(); ?></td>
        </tr>
    </table>

</div>

<div class="jbclientarea-basicinfo">
    <h3><?php echo JText::_('JBZOO_CLIENTAREA_ORDERINFO'); ?></h3>
    <dl>
        <dt><?php echo JText::_('JBZOO_CLIENTAREA_STATUS'); ?></dt>
        <dd><p><?php echo $order->getStatus()->getName(); ?></p></dd>

        <dt><?php echo JText::_('JBZOO_CLIENTAREA_ORDERNO'); ?></dt>
        <dd><p><?php echo $order->getName(); ?></p></dd>

        <dt><?php echo JText::_('JBZOO_CLIENTAREA_CREATED'); ?></dt>
        <dd><p><?php echo $created; ?></p></dd>

        <dt><?php echo JText::_('JBZOO_CLIENTAREA_MODIFIED'); ?></dt>
        <dd><p><?php echo $modified; ?></p></dd>
    </dl>
</div>


<?php if ($payment = $order->getPayment()) : ?>
    <div class="jbclientarea-payment">
        <h3><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT'); ?></h3>
        <dl>
            <dt><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT_NAME'); ?></dt>
            <dd><p><?php echo $payment->getName(); ?></p></dd>

            <dt><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT_RATE'); ?></dt>
            <dd><p><?php echo $payment->getRate()->html(); ?></p></dd>

            <dt><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT_SUMMA'); ?></dt>
            <dd><p><?php echo $order->getTotalSum(true)->html(); ?></p></dd>

            <dt><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT_STATUS'); ?></dt>
            <dd><?php echo $payment->getStatus()->getName(); ?></dd>
        </dl>
    </div>
<?php endif; ?>


<?php if ($shipping = $order->getShipping()) : ?>
    <div class="jbclientarea-shipping">
        <h3><?php echo JText::_('JBZOO_CLIENTAREA_SHIPPING'); ?></h3>
        <dl>
            <?php echo $view->shippingRenderer->renderAdminPosition(array('style' => 'order.useredit', 'order' => $order)); ?>

            <dt><?php echo JText::_('JBZOO_CLIENTAREA_SHIPPING_RATE'); ?></dt>
            <dd>
                <p><?php echo $shipping->getRate()->html(); ?></p>
            </dd>

            <dt><?php echo JText::_('JBZOO_CLIENTAREA_SHIPPING_STATUS'); ?></dt>
            <dd>
                <p><?php echo $shipping->getStatus()->getName(); ?></p>
            </dd>

            <?php echo $view->shippingFieldsRenderer->renderAdminPosition(array('style' => 'order.useredit', 'order' => $order)); ?>
        </dl>
    </div>
<?php endif; ?>


<?php
$html = $view->formRenderer->renderAdminPosition(array('style' => 'order.useredit', 'order' => $order));
if (JString::trim(strip_tags($html))) : ?>
    <div class="jbclientarea-formfields">
        <h3><?php echo JText::_('JBZOO_CLIENTAREA_USERINFO'); ?></h3>
        <dl>
            <?php echo $html; ?>
        </dl>
    </div>
<?php endif; ?>
