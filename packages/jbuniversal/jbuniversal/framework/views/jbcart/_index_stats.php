<?php


?>

<?php if ($this->count) : ?>
    <h2>Статистика магазина</h2>
    <p>
        Сумма всех заказов: <?php echo $this->summ->html(); ?><br />
        Всего заказов: <?php echo $this->count; ?>
    </p>
<?php endif; ?>
