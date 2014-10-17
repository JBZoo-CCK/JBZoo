<?php

$jbmoney = $this->app->jbmoney;

$payment = $order->getPayment();

$statusList = $this->app->jbcartstatus->getList(JBCart::STATUS_PAYMENT, true);
$curStatus = $payment->getStatus();

if (!empty($payment)) :?>
    <div class="uk-panel uk-panel-box">
        <h3 class="uk-panel-title">Система оплаты</h3>
        <dl class="uk-description-list-horizontal">

            <dt>Способ оплаты</dt>
            <dd><p><?php echo $payment->getName(); ?></p></dd>

            <dt>Комиссия</dt>
            <dd><p><?php echo $jbmoney->toFormat($payment->getRate(), 'EUR'); ?></p></dd>

            <dt>Итого к оплате</dt>
            <dd><p><?php echo $order->getTotalSum(true); ?></p></dd>

            <dt>Статус</dt>
            <dd><?php echo $this->app->jbhtml->select($statusList, 'order[payment][status]', '', $curStatus); ?></dd>

        </dl>
    </div>
<?php endif;
