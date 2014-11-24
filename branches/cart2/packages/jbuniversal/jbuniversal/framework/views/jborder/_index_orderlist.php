<?php


$order    = $this->filter->get('filter_order', 'id');
$orderDir = $this->filter->get('filter_order_Dir', 'desc');
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
        $orderOrder = $this->app->jbrouter->admin(array('task' => 'edit', 'cid[]' => $order->id));
        ?>
        <tr class="odd">
            <td class="checkbox">
                <input type="checkbox" name="cid[]" value="<?php echo $order->id; ?>">
            </td>

            <td>
                <a href="<?php echo $orderOrder; ?>">№<?php echo $order->getName(); ?></a>
                <?php
                echo JText::_('JBZOO_BY');
                if ($user = $order->getAuthor()) {
                    $href = $this->app->component->users->link(array('task' => 'user.edit', 'layout' => 'edit', 'view' => 'user', 'id' => $user->id));
                    echo '<i><a href="' . $href . '">' . $user->name . '</a></i>';
                } else {
                    echo '<i>' . JText::_('JBZOO_ANONYM') . '</i>';
                }
                ?>
            </td>

            <td><?php echo $this->app->html->_('date', $order->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></td>

            <td><?php
                if ($order->modified) {
                    echo $this->app->html->_('date', $order->modified, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());
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
