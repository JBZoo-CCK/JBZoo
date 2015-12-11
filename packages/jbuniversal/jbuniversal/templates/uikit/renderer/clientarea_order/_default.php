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
$tabsId    = uniqid('jbzoo-tabs-');
$created   = $this->app->jbdate->toHuman($order->created);
$modified  = $this->app->jbdate->toHuman($order->modified);

echo $this->partial('clientarea_order', 'default.styles');

$this->app->document->setTitle($order->getName('full'));

$html = $view->formRenderer->renderAdminPosition(array('style' => 'order.useredit', 'order' => $order));

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
            $first = ($j == 0) ? ' first' : '';
            $last  = ($j == $itemCount - 1) ? ' last' : '';
            $j++;
            $rowClass = ($j % 2 == 0) ? 'even' : 'odd';
            ?>
            <tr class="jbclientarea-item jbclientarea-item-<?php echo $item->item_id . $first . $last . ' row-' . $rowClass; ?>">
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

<div class="jbclientarea-tab-headers">

    <ul class="uk-tab" data-uk-tab="{connect:'#<?php echo $tabsId; ?>'}">

        <li class="uk-active">
            <a href="#"><?php echo JText::_('JBZOO_CLIENTAREA_ORDERINFO'); ?></a>
        </li>

        <?php if ($payment = $order->getPayment()) : ?>
            <li>
                <a href="#"><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT'); ?></a>
            </li>
        <?php endif; ?>

        <?php if ($shipping = $order->getShipping()) : ?>
            <li>
                <a href="#"><?php echo JText::_('JBZOO_CLIENTAREA_SHIPPING'); ?></a>
            </li>
        <?php endif; ?>

        <?php if (JString::trim(strip_tags($html))) : ?>
            <li>
                <a href="#"><?php echo JText::_('JBZOO_CLIENTAREA_USERINFO'); ?></a>
            </li>
        <?php endif; ?>

    </ul>

    <ul id="<?php echo $tabsId; ?>" class="uk-switcher uk-margin">

        <li>
            <div class="jbclientarea-basicinfo">
                <h3><?php echo JText::_('JBZOO_CLIENTAREA_ORDERINFO'); ?></h3>
                <dl class="uk-description-list-horizontal">
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
        </li>

        <?php if ($payment = $order->getPayment()) : ?>
            <li>
                <div class="jbclientarea-payment">
                    <h3><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT'); ?></h3>
                    <dl class="uk-description-list-horizontal">
                        <dt><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT_NAME'); ?></dt>
                        <dd><p><?php echo $payment->getName(); ?></p></dd>

                        <dt><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT_RATE'); ?></dt>
                        <dd><p><?php echo $payment->getRate()->html(); ?></p></dd>

                        <dt><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT_SUMMA'); ?></dt>
                        <dd><p><?php echo $order->getTotalSum(true)->html(); ?></p></dd>

                        <dt><?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT_STATUS'); ?></dt>
                        <dd><?php echo $payment->getStatus()->getName(); ?></dd>

                        <?php if (!$payment->isPaid() && ($checkoutUrl = $payment->getRedirectUrl())) : ?>
                            <dd>
                                <a target="_blank" class="uk-button-success uk-button" href="<?php echo $checkoutUrl; ?>">
                                    <?php echo JText::_('JBZOO_CLIENTAREA_PAYMENT_GOTO_CHECKOUT'); ?></a>
                            </dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </li>
        <?php endif; ?>

        <?php if ($shipping = $order->getShipping()) : ?>
            <li>
                <div class="jbclientarea-shipping">
                    <h3><?php echo JText::_('JBZOO_CLIENTAREA_SHIPPING'); ?></h3>
                    <dl class="uk-description-list-horizontal">
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
            </li>
        <?php endif; ?>

        <?php if (JString::trim(strip_tags($html))) : ?>
            <li>
                <div class="jbclientarea-formfields">
                    <h3><?php echo JText::_('JBZOO_CLIENTAREA_USERINFO'); ?></h3>
                    <dl class="uk-description-list-horizontal">
                        <?php echo $html; ?>
                    </dl>
                </div>
            </li>
        <?php endif; ?>

    </ul>

</div>
