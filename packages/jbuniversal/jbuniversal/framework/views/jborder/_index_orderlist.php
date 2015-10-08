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


$order    = $this->filter->get('order', 'id');
$orderDir = $this->filter->get('order_dir', 'desc');
?>
<table class="list stripe order-list">

    <thead>
    <tr>
        <th style="width: 25px;" class="checkbox">
            <input type="checkbox" class="check-all">
        </th>
        <th style="min-width: 120px;"><?php echo $this->app->html->_('grid.sort', 'JBZOO_ADMIN_NAME', 'id', $orderDir, $order); ?></th>
        <th style="width: 120px;"><?php echo $this->app->html->_('grid.sort', 'JBZOO_ADMIN_CREATED', 'created', $orderDir, $order); ?></th>
        <th style="width: 120px;"><?php echo $this->app->html->_('grid.sort', 'JBZOO_ADMIN_MODIFIED', 'modified', $orderDir, $order); ?></th>
        <th style="width: 180px;"><?php echo $this->app->html->_('grid.sort', 'JBZOO_ADMIN_STATUS', 'status', $orderDir, $order); ?></th>
        <th style="width: 150px;"><?php echo JText::_('JBZOO_ADMIN_PAYMENT'); ?></th>
        <th style="width: 150px;"><?php echo JText::_('JBZOO_ADMIN_SHIPPING'); ?></th>
        <th style="width: 120px;"><?php echo $this->app->html->_('grid.sort', 'JBZOO_ADMIN_TOTAL', 'total', $orderDir, $order); ?></th>
        <th style="width: 20%;"><?php echo JText::_('JBZOO_ADMIN_ORDER_COMMENT'); ?></th>
    </tr>
    </thead>

    <tfoot>
    <tr>
        <td colspan="9">
            <?php echo $this->pagination->getListFooter(); ?>
        </td>
    </tr>
    </tfoot>

    <tbody>

    <?php foreach ($orderList as $order) :
        ?>
        <tr class="odd">
            <td class="checkbox">
                <input type="checkbox" name="cid[]" value="<?php echo $order->id; ?>">
            </td>

            <td>
                <a href="<?php echo $order->getUrl(); ?>">№<?php echo $order->getName(); ?></a>
                <?php
                echo JText::_('JBZOO_BY') . ' ';
                if ($user = $order->getAuthor()) {
                    $href = $this->app->component->users->link(array('task' => 'user.edit', 'layout' => 'edit', 'view' => 'user', 'id' => $user->id));
                    echo '<i><a href="' . $href . '">' . $user->name . '</a></i>';
                } else {
                    echo '<i>' . JText::_('JBZOO_ANONYM') . '</i>';
                }
                ?>
            </td>

            <td><?php echo $this->app->jbdate->toHuman($order->created); ?></td>

            <td><?php
                if ($order->modified) {
                    echo $this->app->jbdate->toHuman($order->modified);
                } else {
                    echo '-';
                }
                ?></td>

            <td><?php echo $order->getStatus()->getName(); ?></td>

            <td>
                <?php
                $payment = $order->getPayment();
                if ($payment) {
                    echo $payment->getName() . '<br><i>' . $payment->getStatus()->getName() . '</i>';
                } else {
                    echo '-';
                }
                ?>
            </td>

            <td>
                <?php
                $shipping = $order->getShipping();
                if ($shipping) {
                    echo $shipping->getName() . '<br><i>' . $shipping->getStatus()->getName() . '</i>';
                } else {
                    echo '-';
                }
                ?>
            </td>

            <td><?php echo $order->getTotalSum(true); ?></td>

            <td><?php echo ($order->comment) ? $order->comment : ' - '; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
