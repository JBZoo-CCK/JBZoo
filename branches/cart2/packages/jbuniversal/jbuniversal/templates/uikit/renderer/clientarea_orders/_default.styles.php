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

?>

<style type="text/css">
    @media (max-width: 767px) {

        .jbclientarea-table tbody .item-row .item-info:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?>";
        }

        .jbclientarea-table tbody .item-row .item-price4one:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_PRICE'); ?>";
        }

        .jbclientarea-table tbody .item-row .item-quantity:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_QUANTITY'); ?>";
        }

        .jbclientarea-table tbody .item-row .item-total-sum:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_SUBTOTAL'); ?>";
        }

    }
</style>