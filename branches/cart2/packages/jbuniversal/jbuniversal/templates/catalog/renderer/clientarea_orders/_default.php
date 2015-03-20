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


?>

<div class="myorders">
    <h1><?php echo JText::_('JBZOO_MYORDERS_TITLE'); ?></h1>

    <p><?php echo JText::_('JBZOO_MYORDERS_DESCRIPTION'); ?>:</p>

    <?php foreach ($vars['objects'] as $order) :
        $orderUrl  = $order->getUrl();
        $created   = $this->app->html->_('date', $order->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());
        $orderName = '<a href="' . $orderUrl . '">Заказ #' . $order->id . ' ' . JText::_('JBZOO_BY') . ' ' . $created . '</a>';
        echo $orderName.'<br>';
    endforeach; ?>

</div>

<?php if (0) : ?>

    <div class="jbclientarea jb-orders">
        <?php foreach ($vars['objects'] as $order) :
            $itemsHtml = $order->renderItems();
            $items     = $order->getItems();
            $orderUrl  = $order->getUrl();
            $created   = $this->app->html->_('date', $order->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());
            ?>

            <div class="jbclientarea items order-<?php echo $order->id; ?>">
                <div class="column rborder width100">
                    <div class="jbzoo-item">
                        <h3 class="order-title">
                            <a href="<?php echo $orderUrl;?>">Заказ #<?php echo $order->id;?><?php echo JText::_('JBZOO_BY');?><?php echo $created;?></a>
                        </h3>

                        <table class="jbclientarea-table">
                            <?php
                            $j         = 0;
                            $itemCount = count($items);
                            foreach ($items as $item) :
                                $itemHtml = $itemsHtml[$item->get('key')];
                                $first    = ($j == 0) ? ' first' : '';
                                $last     = ($j == $itemCount - 1) ? ' last' : '';
                                $class    = ($j % 2) ? ' jbrow-even' : ' jbrow-odd';
                                $j++;
                                ?>
                                <tr class="item-row item-<?php echo $item->item_id . $class . $first . $last; ?>">
                                    <td class="item-image"><?php echo $itemHtml['image']; ?></td>
                                    <td class="item-info">
                                        <?php echo $itemHtml['itemid']; ?>
                                        <?php echo $itemHtml['sku']; ?>
                                        <?php echo $itemHtml['name']; ?>
                                        <?php echo $itemHtml['params']; ?>
                                        <?php echo $itemHtml['description']; ?>
                                    </td>
                                    <td class="item-price4one"><?php echo $itemHtml['price4one']; ?></td>
                                    <td class="item-quantity"><?php echo $itemHtml['quantity']; ?></td>
                                    <td class="align-right"><?php echo $itemHtml['totalsum']; ?></td>
                                </tr>
                            <?php endforeach; ?>

                            <?php
                            $modifiers = $order->getModifiersOrderPrice();

                            if (!empty($modifiers)) {
                                $i = 0;
                                foreach ($modifiers as $modifier) {
                                    $i++;
                                    $rate = $order->val($modifier->get('rate'));
                                    ?>
                                    <tr class="jbtable-row jbshipping">
                                        <td class="item-cell-empty"></td>
                                        <td colspan="3" class="item-cell-label"><?php echo $modifier->getName(); ?></td>
                                        <td class="item-cell-value align-right"><?php echo $rate->html(); ?></td>
                                    </tr>
                                <?php
                                }
                            }
                            ?>

                            <?php if ($shipping = $order->getShipping()) : ?>
                                <tr class="jbtable-row jbshipping">
                                    <td class="item-cell-empty"></td>
                                    <td colspan="3" class="item-cell-label"><?php echo $shipping->getName(); ?></td>
                                    <td class="item-cell-value align-right"><?php echo $shipping->getRate()->html(); ?></td>
                                </tr>
                            <?php endif;?>

                            <?php if ($payment = $order->getPayment()) : ?>
                                <tr class="jbtable-row jbpayment">
                                    <td class="item-cell-empty"></td>
                                    <td colspan="3" class="item-cell-label"><?php echo $payment->getName(); ?></td>
                                    <td class="item-cell-value align-right"><?php echo $payment->getRate()->html(); ?></td>
                                </tr>
                            <?php endif;?>

                            <tr class="jbtable-row jbtotal-sum">
                                <td class="item-cell-empty"></td>
                                <td colspan="3"
                                    class="item-cell-label"><?php echo JText::_('JBZOO_ORDER_ITEM_TOTAL') ?>:
                                </td>
                                <td class="item-cell-value align-right"><?php echo $order->getTotalSum()->html();?></td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
<?php endif; ?>
