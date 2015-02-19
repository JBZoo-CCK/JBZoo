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

$itemsHtml = $order->renderItems();
$items     = $order->getItems();
$orderUrl  = $order->getUrl();
$created   = $this->app->html->_('date', $order->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());
?>

<table class="jbclientarea-order">
    <?php foreach ($items as $item) :
        $itemHtml = $itemsHtml[$item->get('key')];
        ?>
        <tr class="item-row">
            <td rowspan="<?php echo $rowspan; ?>"><?php echo $itemHtml['image']; ?></td>
            <td>
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
            <tr>
                <td><?php echo $modifier->getName(); ?></td>
                <td class="align-right"><?php echo $rate->html(); ?></td>
            </tr>
        <?php
        }
    }
    ?>

    <?php if ($shipping = $order->getShipping()) : ?>
        <tr>
            <td><?php echo $shipping->getName(); ?></td>
            <td class="align-right"><?php echo $shipping->getRate()->html(); ?></td>
        </tr>
    <?php endif; ?>

    <?php if ($payment = $order->getPayment()) : ?>
        <tr>
            <td><?php echo $payment->getName(); ?></td>
            <td class="align-right"><?php echo $payment->getRate()->html(); ?></td>
        </tr>
    <?php endif; ?>

    <tr>
        <td>Итого:</td>
        <td class="align-right"><?php echo $order->getTotalSum()->html(); ?></td>
    </tr>

</table>


