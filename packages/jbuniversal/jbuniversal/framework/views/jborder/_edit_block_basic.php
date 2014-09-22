<?php

$user = JText::_('JBZOO_ANONYM');
if ($juser = $order->getAuthor()) {
    $href = $this->app->component->users->link(array('task' => 'user.edit', 'layout' => 'edit', 'view' => 'user', 'id' => $juser->id));
    $user = '<a href="' . $href . '">' . $juser->name . '</a>';
}

?>
<div class="uk-panel uk-panel-box">
    <h3 class="uk-panel-title">Основное</h3>

    <dl class="uk-description-list-horizontal">

        <dt>Статус</dt>
        <dd>
            <p>
                <select style="width: 180px;">
                    <option value="">Ожидает доставки</option>
                </select></p>

        </dd>

        <dt>Номер заказа</dt>
        <dd><p class="uk-badge uk-badge-notification"><?php echo $order->getName(); ?></p></dd>

        <dt>Пользователь</dt>
        <dd><p><?php echo $user; ?></p></dd>

        <dt>Дата создания</dt>
        <dd><p><?php echo $created; ?></p></dd>

        <dt>Модифицирован</dt>
        <dd><p><?php echo $modified; ?></p></dd>

        <dt>Заметки</dt>
        <dd>
            <textarea cols="100" rows="5" style="resize: vertical;" placeholder="Только для администратора"><?php echo $order->comment; ?></textarea>
        </dd>
    </dl>
</div>