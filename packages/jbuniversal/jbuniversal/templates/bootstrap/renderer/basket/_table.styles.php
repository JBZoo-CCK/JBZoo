<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<style type="text/css">
    @media (max-width: 767px) {

        .jbcart-table tbody .jbcart-row .jbcart-name:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_NAME'); ?>";
        }

        .jbcart-table tbody .jbcart-row .jbcart-price:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_PRICE'); ?>";
        }

        .jbcart-table tbody .jbcart-row .jbcart-quantity:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_QUANTITY'); ?>";
        }

        .jbcart-table tbody .jbcart-row .jbcart-subtotal:before {
            content: "<?php echo JText::_('JBZOO_CART_ITEM_SUBTOTAL'); ?>";
        }

    }
</style>