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

$action = 'https://merchant.roboxchange.com/Index.aspx';
if ($isDebug) {
    $action = 'http://test.robokassa.ru/Index.aspx';
}

?>
    <p style="height:36px;"><!--noindex--><a href="http://robokassa.ru/" target="_blank" rel="nofollow"><img
                src="media/zoo/applications/jbuniversal/assets/img/payments/robokassa.png"></a><!--/noindex--></p>
    <form action="<?php echo $action; ?>" method=POST>
        <input type="hidden" name="MrchLogin" value="<?php echo $data->get('login'); ?>">
        <input type="hidden" name="OutSum" value="<?php echo $data->get('summ'); ?>">
        <input type="hidden" name="InvId" value="<?php echo $data->get('orderId'); ?>">
        <input type="hidden" name="Desc" value="Order #<?php echo $data->get('orderId'); ?> from <?php echo JUri::getInstance()->getHost(); ?>">
        <input type="hidden" name="SignatureValue" value="<?php echo $data->get('hash'); ?>">

        <input type="submit" style="display:inline-block;" class="add-to-cart"
               value="<?php echo JText::_('JBZOO_PAYMENT_BUTTON'); ?>"/>
    </form>

<?php if ($isDebug) : ?>
    <strong style="color:red;"><?php echo JText::_('JBZOO_ROBOX_DEBUG_MODE'); ?></strong>
<?php endif; ?>