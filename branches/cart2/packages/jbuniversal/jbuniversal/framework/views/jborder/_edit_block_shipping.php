<?php

$jbmoney = $this->app->jbmoney;

?>
<div class="uk-panel uk-panel-box">
    <h3 class="uk-panel-title">Сервис доставки</h3>
    <dl class="uk-description-list-horizontal">

        <dt>Способ доставки</dt>
        <dd><p><?php echo $order->getShipping()->getName(); ?></p></dd>

        <dt>Цена доставки</dt>
        <dd><p><?php echo $jbmoney->toFormat(5, 'eur'); ?></p></dd>

        <dt>Статус</dt>
        <dd><select style="width: 180px;">
                <option>В процессе</option>
            </select></dd>

        <h3>Дополнительно</h3>
        <dt>Страна</dt>
        <dd>Россия</dd>

        <dt>Город</dt>
        <dd>Москва</dd>

        <dt>Адрес</dt>
        <dd>Кремль, палата №6</dd>

        <dt>Получатель</dt>
        <dd>На усмотрение курьера</dd>
    </dl>
</div>