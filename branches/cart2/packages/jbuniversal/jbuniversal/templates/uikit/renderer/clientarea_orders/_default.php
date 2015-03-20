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

$this->app->document->setTitle(JText::_('JBZOO_CLIENTAREA_ORDERS_TITLE'));

echo $this->partial('clientarea_orders', 'default.styles');
?>

<div class="jbclientarea">

    <p><?php echo JText::_('JBZOO_CLIENTAREA_DESCRIPTION'); ?>:</p>

    <?php if (!empty($vars['objects'])) : ?>

        <table class="jbclientarea-orderlist uk-table uk-table-hover uk-table-striped">
            <thead>
            <tr>
                <th><?php echo JText::_('JBZOO_CLIENTAREA_ID'); ?></th>
                <th><?php echo JText::_('JBZOO_CLIENTAREA_DATE'); ?></th>
                <th><?php echo JText::_('JBZOO_CLIENTAREA_PRICE'); ?></th>
                <th><?php echo JText::_('JBZOO_CLIENTAREA_STATUS'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($vars['objects'] as $order) :
                $created   = $this->app->html->_('date', $order->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset());
                $orderName = '<a href="' . $order->getUrl() . '">' . JText::sprintf('JBZOO_CLIENTAREA_ORDERNAME', $order->getName()) . '</a>';
                ?>

                <tr class="jbclientarea-order jbclientarea-order-<?php echo $order->id; ?>">
                    <td><p class="jbclientarea-name"><?php echo $orderName; ?></p></td>
                    <td><p class="jbclientarea-date"><?php echo $created; ?></p></td>
                    <td><p class="jbclientarea-price"><?php echo $order->getTotalSum(); ?></p></td>
                    <td><p class="jbclientarea-status"><?php echo $order->getStatus()->getName();?></p></td>
                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <p class="jbclientarea-empty"><?php echo JText::_('JBZOO_CLIENTAREA_EMPTY'); ?></p>
    <?php endif; ?>

</div>
