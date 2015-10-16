<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!empty($payment)) :

    /** @var JBCartStatusHelper $jbstatus */
    $jbstatus   = $this->app->jbcartstatus;
    $statusList = $jbstatus->getList(JBCart::STATUS_PAYMENT, true, true, $order);
    $curStatus  = $payment->getStatus();
    ?>

    <div class="uk-panel uk-panel-box">
        <h3 class="uk-panel-title"><?php echo JText::_('JBZOO_ORDER_PAYMENT_TITLE'); ?></h3>

        <?php echo $this->paymentRender->renderAdminEdit(array('order' => $order)); ?>

        <dl class="uk-description-list-horizontal">

            <dt><?php echo JText::_('JBZOO_ORDER_PAYMENT_METHOD'); ?></dt>
            <dd><p><?php echo $payment->getName(); ?></p></dd>

            <dt><?php echo JText::_('JBZOO_ORDER_PAYMENT_TAX'); ?></dt>
            <dd><p><?php echo $payment->getRate()->html(); ?></p></dd>

            <dt><?php echo JText::_('JBZOO_ORDER_PAYMENT_TOTAL'); ?></dt>
            <dd><p><?php echo $order->getTotalSum(true)->html(); ?></p></dd>

            <dt><?php echo JText::_('JBZOO_ORDER_PAYMENT_STATUS'); ?></dt>
            <dd><?php echo $this->app->jbhtml->select($statusList, 'order[payment][status]', '', $curStatus); ?></dd>

        </dl>
    </div>
<?php endif;
