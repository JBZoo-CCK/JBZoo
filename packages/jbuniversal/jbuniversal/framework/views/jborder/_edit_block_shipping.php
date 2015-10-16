<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/** @var JBCartStatusHelper $jbstatus */
$jbstatus   = $this->app->jbcartstatus;
$statusList = $jbstatus->getList(JBCart::STATUS_SHIPPING, true, true, $order);

if ($shipping) {
    $curStatus = $shipping->getStatus();
}

?>
<div class="uk-panel uk-panel-box">

    <h3 class="uk-panel-title"><?php echo JText::_('JBZOO_ORDER_SHIPPING_TITLE'); ?></h3>

    <?php echo $this->shipRender->renderAdminEdit(array('order' => $order)); ?>

    <?php if ($shipping) : ?>
        <dl class="uk-description-list-horizontal">
            <dt><?php echo JText::_('JBZOO_ORDER_SHIPPING_BLOCK_PRICE'); ?></dt>
            <dd>
                <p><?php echo $shipping->getRate()->html(); ?></p>
            </dd>

            <dt><?php echo JText::_('JBZOO_ORDER_SHIPPING_BLOCK_STATUS'); ?></dt>
            <dd>
                <p><?php echo $this->app->jbhtml->select($statusList, 'order[shipping][status]', '', $curStatus); ?></p>
            </dd>
        </dl>
    <?php endif; ?>

    <?php echo $this->shipFieldsRender->renderAdminEdit(array('order' => $order)); ?>

</div>