<?php

// add check admin position

$html   = $this->orderFieldRender->renderAdminEdit(array('order' => $order));
$isShow = JString::trim(strip_tags($html));

?>

<?php if ($isShow) : ?>
    <div class="uk-panel">
        <h2>Информация от пользователя</h2>
        <dl class="uk-description-list-horizontal"><?php echo $html; ?></dl>
    </div>
<?php endif; ?>
