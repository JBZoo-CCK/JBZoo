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


if (!empty($view->payment)) {

    $this->app->jbassets->less('jbassets:less/cart/payment.less');
    $this->app->jbassets->js('jbassets:js/cart/payment.js');

    echo $view->paymentRenderer->render('payment.default', array(
        'order' => $view->order
    ));

}
