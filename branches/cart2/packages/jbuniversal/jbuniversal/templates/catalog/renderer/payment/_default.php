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
$this->app->jbassets->payment();

echo '<h2>' . JText::_('JBZOO_ORDER_NAME') . ' #' . $view->order->id . '</h2>';

echo $view->orderDetails->render(array(
    'payment-button' => false,
    'payment-info'   => false,
    'template'       => 'table',
));

if ((int)$view->appParams->get('global.jbzoo_cart_config.paypal-enabled', 0)) {
    echo '<div class="width33">';
    echo $this->app->jblayout->render('payment_paypal', $view->payments['paypal']);
    echo '</div>';
}

if ((int)$view->appParams->get('global.jbzoo_cart_config.robox-enabled', 0)) {
    echo '<div class="width33">';
    echo $this->app->jblayout->render('payment_robox', $view->payments['robox']);
    echo '</div>';
}

if ((int)$view->appParams->get('global.jbzoo_cart_config.ikassa-enabled', 0)) {
    echo '<div class="width33">';
    echo $this->app->jblayout->render('payment_ikassa', $view->payments['ikassa']);
    echo '</div>';
}

if ((int)$view->appParams->get('global.jbzoo_cart_config.manual-enabled', 0)) {
    echo '<div class="width33">';
    echo $this->app->jblayout->render('payment_manual', $view->payments['manual']);
    echo '</div>';
}
