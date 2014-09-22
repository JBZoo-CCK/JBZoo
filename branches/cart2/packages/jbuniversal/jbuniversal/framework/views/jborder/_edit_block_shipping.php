<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$jbmoney = $this->app->jbmoney;
$element = $order->getShipping();

?>
<div class="uk-panel uk-panel-box">
    <h3 class="uk-panel-title">Сервис доставки</h3>
    <dl class="uk-description-list-horizontal">

        <dt>Способ доставки</dt>
        <dd><p><?php echo $element->getName(); ?></p></dd>

        <dt>Цена доставки</dt>
        <dd><p><?php echo $jbmoney->toFormat($element->get('value', 0), $element->currency()); ?></p></dd>

        <dt>Статус</dt>
        <dd><select style="width: 180px;">
                <option>В процессе</option>
            </select></dd>

        <h3>Дополнительно</h3>

        <?php echo $this->shipRender->renderAdminEdit(array('order' => $order)); ?>

        <?php echo $this->shipFieldsRender->renderAdminEdit(array('order' => $order)); ?>

    </dl>
</div>