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

?>
<div class="uk-panel uk-panel-box">

    <h3 class="uk-panel-title">
        <?php echo JText::_('JBZOO_ORDER_SHIPPING_TITLE'); ?>
    </h3>
    <dl class="uk-description-list-horizontal">
        <?php echo $this->shipRender->renderAdminEdit(array('order' => $order)); ?>

        <h3>
            <?php echo JText::_('JBZOO_ORDER_SHIPPINGFIELD_TITLE'); ?>
        </h3>

<?php echo $this->shipFieldsRender->renderAdminEdit(array('order' => $order)); ?>

    </dl>
</div>