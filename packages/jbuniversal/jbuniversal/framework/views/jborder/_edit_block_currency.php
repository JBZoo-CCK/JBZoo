<?php

$curList = $order->getCurrencyList();
if (count($curList) <= 2) {
    return false;
}

?>
<div class="uk-panel uk-panel-box basic-info">
    <h3 class="uk-panel-title"><?php echo JText::_('JBZOO_ORDER_CURRENCY_TITLE'); ?></h3>
    <p>Список и курсы валют на момент создания заказа</p>

    <?php
    echo $this->app->jbhtml->currencyToggle($curList, JBCartValue::DEFAULT_CODE, array(
        'target'      => '.jbzoo .uk-grid',
        'showDefault' => true,
    ));
    ?>

</div>