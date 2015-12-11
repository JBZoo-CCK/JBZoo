<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$this->app->jbassets->less('jbassets:less/cart/buttons.less');

$isCreate  = (int)$view->config->get('tmpl_button_create', 1);
$isPayment = (int)$view->config->get('tmpl_button_payment', 1) && $view->payment;

?>

<div class="jbcart-buttons clearfix">

    <?php if ($isCreate) : ?>
        <input type="submit" name="create" value="<?php echo JText::_('JBZOO_CART_SUBMIT'); ?>"
               class="jbbutton green big" />
    <?php endif; ?>

    <?php if ($isPayment) : ?>
        <input type="submit" name="create-pay" value="<?php echo JText::_('JBZOO_CART_SUBMIT_AND_PAY'); ?>"
               class="jbbutton green big" />
    <?php endif; ?>

</div>
