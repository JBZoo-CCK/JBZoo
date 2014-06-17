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

if ((int)$data->get('isNew', 0)) {
    $actionURL = 'https://sci.interkassa.com/';
    $fields    = array(
        'ik_co_id' => $data->get('shopid'),
        'ik_pm_no' => $data->get('orderId'),
        'ik_am'    => $data->get('summ'),
        'ik_cur'   => $data->get('currency'),
        'ik_desc'  => 'Order #' . $data->get('orderId') . ' from ' . JUri::getInstance()->getHost(),
        'ik_enc'   => 'utf-8',
        'ik_int'   => 'web',
        'ik_am_t'  => 'invoice',
    );

    // add hash
    ksort($fields, SORT_STRING);
    array_push($fields, $data->get('key'));
    $fields['ik_sign'] = base64_encode(md5(implode(':', $fields), true));

} else {
    $actionURL = 'https://www.interkassa.com/lib/payment.php';
    $fields    = array(
        'ik_shop_id'        => $data->get('shopid'),
        'ik_payment_amount' => $data->get('summ'),
        'ik_payment_id'     => $data->get('orderId'),
        'ik_payment_desc'   => 'Order #' . $data->get('orderId') . ' from ' . JUri::getInstance()->getHost(),
    );
}

?>

<p style="height:36px;">
    <!--noindex-->
    <a href="http://www.interkassa.com/" target="_blank" rel="nofollow"><img
            src="media/zoo/applications/jbuniversal/assets/img/payments/interkassa.png"></a>
    <!--/noindex-->
</p>

<form action="<?php echo $actionURL; ?>"
      name="payment"
      method="post"
      enctype="application/x-www-form-urlencoded"
      enctype="utf-8">
    <?php
    $html = array();
    foreach ($fields as $name => $field) {
        if ($name && $field) {
            $html[] = '<input type="hidden" name="' . $name . '" value="' . $field . '"/>';
        }
    }
    echo implode("\r\t", $html);
    ?>

    <input type="submit" style="display:inline-block;" class="add-to-cart"
           value="<?php echo JText::_('JBZOO_PAYMENT_BUTTON'); ?>"/>
</form>
