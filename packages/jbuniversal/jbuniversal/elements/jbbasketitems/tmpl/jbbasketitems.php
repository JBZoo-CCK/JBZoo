<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$imageElementId = $this->app->zoo->getApplication()->getParams()->get('global.jbzoo_cart_config.element-image');

if (!empty($items)) {
    ?>
    <div>
        <table class="jbbasket-table jsJBZooBasket" border="1" cellpadding="3" cellspacing="3">
            <thead>
            <tr>
                <th>#</th>
                <th><?php echo JText::_('JBZOO_CART_ITEM_SKU'); ?></th>
                <th><?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?></th>
                <th><?php echo JText::_('JBZOO_CART_ITEM_PRICE'); ?></th>
                <th><?php echo JText::_('JBZOO_CART_ITEM_QUANTITY'); ?></th>
                <th><?php echo JText::_('JBZOO_CART_ITEM_SUBTOTAL'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i        = 0;
            $summa    = 0;
            $count    = 0;
            $currency = '';
            $html     = array();

            foreach ($basketItems as $basketInfo) {

                $count += $basketInfo['quantity'];

                $item     = isset($items[$basketInfo['itemId']]) ? $items[$basketInfo['itemId']] : null;
                $image    = $this->app->jbitem->renderImageFromItem($item, $imageElementId, true);
                $currency = $basketInfo['currency'];
                $subtotal = $basketInfo['quantity'] * $basketInfo['price'];
                $summa += $subtotal;

                $itemLink = $this->app->jbrouter->adminItem($item);
                if ($this->app->jbenv->isSite()) {
                    $itemLink = $this->app->route->item($item);
                }

                if (strpos($itemLink, JUri::getInstance()->getHost()) === false) {
                    $itemLink = JUri::getInstance()->toString(array('scheme', 'host', 'port')) . $itemLink;
                }


                if (!$item) { // hack for compatibility
                    $item = (object)array(
                        'id'   => 0,
                        'name' => 'Undefined item',
                    );
                }

                $html[] = '<tr class="row-' . $item->id . '" itemId="' . $item->id . '">' . "\n";
                $html[] = '<td>' . ++$i . '</td>';
                $html[] = '<td>' . $basketInfo['sku'] . '</td>';
                $html[] = '<td>';

                if ($image) {
                    $html[] = '<p align="left">' . $image . '</p>';
                }

                $html[] = '<a href="' . $itemLink . '" title="' . $item->name . '">' . $item->name . '</a>';

                if (isset($basketInfo['priceParams']) && !empty($basketInfo['priceParams'])) {
                    foreach ($basketInfo['priceParams'] as $key => $value) {
                        if (!empty($value)) {
                            $html[] = '<div><strong>' . $key . ':</strong> ' . $value . '</div>';
                        }
                    }
                }

                if (!empty($basketInfo['priceDesc'])) {
                    $html[] = '<br/><span class="price-description">' . $basketInfo['priceDesc'] . '</span>';
                }

                $html[] = "</td>\n";
                $html[] = '<td class="jsPricevalue" price="' . $basketInfo['price'] . '">'
                    . $this->app->jbmoney->toFormat($basketInfo['price'], $currency) . '</td>';
                $html[] = '<td>' . $basketInfo['quantity'] . '</td>';
                $html[] = '<td class="jsSubtotal">' . $this->app->jbmoney->toFormat($subtotal, $currency) . '</td>';
                $html[] = "</tr>\n";
            }

            echo implode("\n ", $html);
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td><strong><?php echo JText::_('JBZOO_CART_TOTAL'); ?>:</strong></td>
                <td class="jsTotalCount"><?php echo $count; ?></td>
                <td class="jsTotalPrice"><?php echo $this->app->jbmoney->toFormat($summa, $currency); ?></td>
            </tr>
            </tfoot>
        </table>

        <?php if ($renderMode != 'nopayment') : ?>
            <div class="payment-system">

                <?php if (($params && $params->get('payment-info', true)) || !$params) : ?>

                    <?php $paymentData = $this->getPaymentData(); ?>

                    <?php if ($paymentData && $summa) : ?>
                        <ul>
                            <?php if (isset($paymentData['payment_date'])) : ?>
                                <li>
                                    <strong><?php echo JText::_('JBZOO_CART_REAL_DATE'); ?>:</strong>
                                    <?php echo $paymentData['payment_date']; ?>
                                </li>
                            <?php endif; ?>

                            <?php if (isset($paymentData['payment_system'])) : ?>
                                <li>
                                    <strong><?php echo JText::_('JBZOO_CART_PAYMENT_NAME'); ?>:</strong>
                                    <?php echo $paymentData['payment_system']; ?>
                                </li>
                            <?php endif; ?>

                            <li>
                                <strong><?php echo JText::_('JBZOO_CART_PAYMENT_STATUS'); ?>:</strong>
                                <?php echo '<span class="order-status ' . $this->getOrderStatus(false) . '">' . $this->getOrderStatus(true) . '</span>'; ?>
                            </li>

                            <?php if (isset($paymentData['additional_status'])) : ?>
                                <li>
                                    <strong><?php echo JText::_('JBZOO_CART_PAYMENT_STATUS_REAL'); ?>:</strong>
                                    <?php echo $paymentData['additional_status']; ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php else: ?>
                        <p><?php echo JText::_('JBZOO_CART_PAYMENT_NODATA'); ?></p>
                    <?php endif; ?>

                <?php endif; ?>

                <?php echo $this->getOrderSubForm(); ?>

                <?php if (
                    $this->app->jbenv->isSite() &&
                    in_array($this->getOrderStatus(), array(ElementJBBasketItems::ORDER_STATUS_NOPAID, ElementJBBasketItems::ORDER_STATUS_NODATA)) &&
                    $params->get('payment-button', true) &&
                    $summa > 0
                ) :
                    $appId = $this->app->zoo->getApplication()->id;
                    $href  = $this->app->jbrouter->basketPayment($params->get('basket-menuitem'), $appId, $this->getItem()->id);
                    ?>
                    <p><a style="display:inline-block;" href="<?php echo $href; ?>"
                          class="jsGoto add-to-cart"><?php echo JText::_('JBZOO_PAYMENT_LINKTOFORM'); ?></a></p>

                <?php endif; ?>

            </div>
        <?php endif; ?>


    </div>
    <div class="clear"></div>
    <?php
} else {
    echo '<p>' . JText::_('JBZOO_CART_ITEMS_NOT_FOUND') . '</p>';
}
