<?php

$user = JText::_('JBZOO_ANONYM');
if ($juser = $order->getAuthor()) {
    $href = $this->app->component->users->link(array('task' => 'user.edit', 'layout' => 'edit', 'view' => 'user', 'id' => $juser->id));
    $user = '<a href="' . $href . '">' . $juser->name . '</a>';
}

?>
<div class="uk-panel uk-panel-box">
    <h3 class="uk-panel-title"><?php echo JText::_('JBZOO_ORDER_MAIN_TITLE'); ?></h3>

    <dl class="uk-description-list-horizontal">

        <dt><?php echo JText::_('JBZOO_ORDER_STATUS'); ?></dt>
        <dd>
            <p>
                <select style="width: 180px;">
                    <option value="">Ожидает доставки</option>
                </select></p>

        </dd>

        <dt><?php echo JText::_('JBZOO_ORDER_NO'); ?></dt>
        <dd><p class="uk-badge uk-badge-notification"><?php echo $order->getName(); ?></p></dd>

        <dt><?php echo JText::_('JBZOO_ORDER_USER'); ?></dt>
        <dd><p><?php echo $user; ?></p></dd>

        <dt><?php echo JText::_('JBZOO_ORDER_CREATED'); ?></dt>
        <dd><p><?php echo $created; ?></p></dd>

        <dt><?php echo JText::_('JBZOO_ORDER_MODIFIED'); ?></dt>
        <dd><p><?php echo $modified; ?></p></dd>

        <dt><?php echo JText::_('JBZOO_ORDER_NOTES'); ?></dt>
        <dd>
            <textarea cols="100" rows="5" style="resize: vertical;"
                      placeholder="<?php echo JText::_('JBZOO_ORDER_NOTES_DESC'); ?>"><?php
                echo $order->comment;
                ?></textarea>
        </dd>
    </dl>
</div>