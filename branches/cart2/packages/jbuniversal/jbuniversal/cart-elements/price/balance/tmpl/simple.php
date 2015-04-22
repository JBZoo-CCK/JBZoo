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

$value      = $this->getValue();
$isUseStock = $this->_isUseStock();

?>

<!--noindex-->
<?php if ($value == JBCartElementPriceBalance::COUNT_AVAILABLE_NO) : ?>
    <span class="jbprice-balance-available-no">
        <?php echo JText::_('JBZOO_ELEMENT_PRICE_BALANCE_AVAILABLE_NO'); ?>
    </span>

<?php elseif (!$isUseStock) : ?>
    <span class="jbprice-balance-available-yes">
        <?php echo JText::_('JBZOO_ELEMENT_PRICE_BALANCE_AVAILABLE_YES'); ?>
    </span>

<?php elseif ($value == JBCartElementPriceBalance::COUNT_REQUEST) : ?>
    <span class="jbprice-balance-request">
        <?php echo JText::_('JBZOO_ELEMENT_PRICE_BALANCE_REQUEST'); ?>
    </span>

<?php else : ?>
    <span class="jbprice-balance-available-yes">
        <?php echo JText::_('JBZOO_ELEMENT_PRICE_BALANCE_AVAILABLE_YES'); ?>
    </span>

<?php endif; ?>
<!--/noindex-->
