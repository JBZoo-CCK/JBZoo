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
$data = $vars['object'];
?>

<p style="height:36px;">
    <strong><?php echo $data->get('title'); ?></strong>
</p>

<form name="payment" action="<?php echo JRoute::_('index.php'); ?>" method="get">
    <input type="hidden" name="option" value="com_zoo"/>
    <input type="hidden" name="controller" value="payment"/>
    <input type="hidden" name="task" value="paymentManual"/>
    <input type="hidden" name="app_id" value="<?php echo $view->appId; ?>"/>
    <input type="hidden" name="order_id" value="<?php echo $view->orderId; ?>"/>
    <input type="hidden" name="Itemid" value="<?php echo (int)$this->app->jbrequest->get('Itemid'); ?>"/>

    <input type="submit" style="display:inline-block;" class="add-to-cart"
           value="<?php echo JText::_('JBZOO_PAYMENT_MANUAL'); ?>"/>
</form>
