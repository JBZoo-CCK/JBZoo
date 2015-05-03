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


$config   = $this->config;
$shipping = $order->getShipping();

if (!$this->config->get('shipping', 1)) {
    return;
}

?>

<?php if ($shipping && !$shipping->getRate()->isEmpty()) : ?>
    <tr>
        <td colspan="2" style="border-bottom: none;"></td>

        <td <?php echo $this->getStyles(); ?>>
            <p><strong><?php echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_SHIPPING_RATE'); ?></strong></p>
        </td>

        <td colspan="2" <?php echo $this->getStyles(); ?>>
            <?php echo $shipping->getName(); ?>
            <em>(
                <?php
                if ($shipping->isModify()) {
                    echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_SHIPPING_RATE_INCLUDED');
                } else {
                    echo JText::_('JBZOO_ELEMENT_EMAIL_ITEMS_SHIPPING_RATE_NOT_INCLUDED');
                }
                ?>
            )</em>
        </td>

        <td colspan="1" <?php echo $this->getStyles(array('border-bottom' => '1px solid #dddddd')); ?>>
            <strong><?php echo $shipping->getRate()->html($this->_getCurrency()); ?></strong>
        </td>
    </tr>
<?php endif;
