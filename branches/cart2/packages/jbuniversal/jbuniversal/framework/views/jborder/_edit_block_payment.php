<?php

$jbmoney = $this->app->jbmoney;

$payment = $order->getPayment();

if (!empty($payment)) :?>
    <div class="uk-panel uk-panel-box">
        <h3 class="uk-panel-title">Система оплаты</h3>
        <dl class="uk-description-list-horizontal">

            <dt>Способ оплаты</dt>
            <dd><p><?php echo $payment->getName(); ?></p></dd>

            <dt>Комиссия</dt>
            <dd><p><?php echo $jbmoney->toFormat($payment->getRate(), '%'); ?></p></dd>

            <dt>Итого к оплате</dt>
            <dd><p><?php echo $order->getTotalSum(true); ?></p></dd>

            <dt>Статус</dt>
            <dd><select style="width: 180px;">
                    <option><?php echo $payment->getStatus(); ?></option>
                </select></dd>

            <h3>Дополнительно</h3>
            <dt>Тип платильщика</dt>
            <dd>Юр. лицо</dd>

            <dt>ОГРН</dt>
            <dd>12345678</dd>

            <dt>БИК</dt>
            <dd>123456</dd>

            <dt>Расчетный счет</dt>
            <dd>1234567890</dd>
        </dl>
    </div>
<?php endif;
