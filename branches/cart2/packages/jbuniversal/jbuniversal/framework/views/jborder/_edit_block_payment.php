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
    $statusList = $this->app->jbcartstatus->getList(JBCart::STATUS_PAYMENT, true);
    $curStatus  = $payment->getStatus(); ?>
    <div class="uk-panel uk-panel-box">
        <h3 class="uk-panel-title">Система оплаты</h3>
        <dl class="uk-description-list-horizontal">

            <dt>Способ оплаты</dt>
            <dd><p><?php echo $payment->getName(); ?></p></dd>

            <dt>Комиссия</dt>
            <dd><p><?php echo $payment->getRate()->html(); ?></p></dd>

            <dt>Итого к оплате</dt>
            <dd><p><?php echo $order->getTotalSum(true)->html(); ?></p></dd>

            <dt>Статус</dt>
            <dd><?php echo $this->app->jbhtml->select($statusList, 'order[payment][status]', '', $curStatus); ?></dd>

        </dl>
    </div>
<?php endif;
