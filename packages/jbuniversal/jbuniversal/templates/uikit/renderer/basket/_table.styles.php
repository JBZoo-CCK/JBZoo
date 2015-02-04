<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Kalistratov Sergey <kalistratov.s.m@gmail.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$itemName     = JText::_('JBZOO_CART_ITEM_NAME');
$itemPrice    = JText::_('JBZOO_CART_ITEM_PRICE');
$itemQuantity = JText::_('JBZOO_CART_ITEM_QUANTITY');
$itemSubTotal = JText::_('JBZOO_CART_ITEM_SUBTOTAL');

?>


<style type="text/css">
    @media (max-width: 767px) {

        .jbcart-table tbody .jbcart-row .jbcart-name:before {
            content: "<?php echo $itemName; ?>";
        }

        .jbcart-table tbody .jbcart-row .jbcart-price:before {
            content: "<?php echo $itemPrice; ?>";
        }

        .jbcart-table tbody .jbcart-row .jbcart-quantity:before {
            content: "<?php echo $itemQuantity; ?>";
        }

        .jbcart-table tbody .jbcart-row .jbcart-subtotal:before {
            content: "<?php echo $itemSubTotal; ?>";
        }

    }
</style>