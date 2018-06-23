<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
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
