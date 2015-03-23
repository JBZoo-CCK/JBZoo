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

        .jbclientarea-order-table .jbclientarea-item-info:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?>";
        }

        .jbclientarea-order-table .jbclientarea-date:before {
            content: "<?php echo JText::_('JBZOO_CLIENTAREA_DATE'); ?>";
        }

        .jbclientarea-order-table .jbclientarea-item-price4one:before {
            content: "<?php echo JText::_('JBZOO_CLIENTAREA_PRICE'); ?>";
        }

        .jbclientarea-order-table .jbclientarea-item-quantity:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_QUANTITY'); ?>";
        }

        .jbclientarea-order-table .jbclientarea-item-totalsum:before {
            content: "<?php echo JText::_('JBZOO_CLIENTAREA_STATUS'); ?>";
        }

    }
</style>