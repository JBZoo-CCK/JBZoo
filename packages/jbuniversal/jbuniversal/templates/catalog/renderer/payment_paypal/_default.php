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

$isDebug = (int)$data->get('debug', 0);
$action = 'https://www.paypal.com/cgi-bin/webscr';
if ($isDebug) {
    $action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
}

$fields = array(
    'cmd'           => '_xclick',
    'no_shipping'   => '1',
    'rm'            => '2',
    'business'      => $data->get('email'),
    'item_number'   => $data->get('orderId'),
    'amount'        => $data->get('summ'),
    'currency_code' => $data->get('currency'),
    'return'        => $data->get('url_success'),
    'cancel_return' => $data->get('url_fail'),
    'notify_url'    => $data->get('url_callback'),
    'item_name'     => 'Order #' . $data->get('orderId') . ' from ' . JUri::getInstance()->getHost(),
);

?>
    <!--noindex-->
    <a href="http://paypal.com/" target="_blank" rel="nofollow">
        <img src="media/zoo/applications/jbuniversal/assets/img/payments/paypal.png">
    </a>
    <!--/noindex-->
    <form action="<?php echo $action; ?>" method=POST>

        <?php foreach ($fields as $name => $field) : ?>
            <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $field; ?>">
        <?php endforeach; ?>

        <input type="submit" style="display:inline-block;" class="add-to-cart"
               value="<?php echo JText::_('JBZOO_PAYMENT_BUTTON'); ?>"/>
    </form>

<?php if ($isDebug) : ?>
    <strong style="color:red;"><?php echo JText::_('JBZOO_PAYPAL_DEBUG_MODE'); ?></strong>
<?php endif; ?>