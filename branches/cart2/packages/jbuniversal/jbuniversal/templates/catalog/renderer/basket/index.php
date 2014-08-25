<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$view = $this->getView();
$this->app->jbassets->basket();
$this->app->jbassets->initJBPrice();
$actionUrl = $this->app->jbrouter->cartOrderCreate($view->application->id, null);
?>


<form action="<?php echo $actionUrl; ?>" method="post" name="jbcartForm" accept-charset="utf-8"
      enctype="multipart/form-data">

    <h2>Список товаров в корзине</h2>

    <?php echo $this->partial('basket', 'table'); ?>

    <h2>Форма заказа</h2>

    <div><?php echo $this->partial('basket', 'form'); ?></div>

    <h2>Доставка</h2>

    <div><?php echo $this->partial('basket', 'shipping'); ?></div>

    <h2>Оплата</h2>

    <div><?php echo $this->partial('basket', 'payment'); ?></div>

    <input type="submit" />

    <input type="hidden" name="option" value="com_zoo" />
    <input type="hidden" name="controller" value="basket" />
    <input type="hidden" name="task" value="index" />
    <input type="hidden" name="Itemid" value="<?php echo $view->Itemid; ?>" />
    <?php echo $this->app->html->_('form.token'); ?>

</form>
