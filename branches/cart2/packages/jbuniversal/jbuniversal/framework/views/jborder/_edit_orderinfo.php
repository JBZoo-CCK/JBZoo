<?php


?>

<div class="uk-panel">
    <h2 class="uk-panel-title">Информация о заказе от покупателя</h2>

    <dl class="uk-description-list-horizontal">
        <?php echo $this->orderFieldRender->renderAdminEdit(array('order' => $order)); ?>
    </dl>

</div>