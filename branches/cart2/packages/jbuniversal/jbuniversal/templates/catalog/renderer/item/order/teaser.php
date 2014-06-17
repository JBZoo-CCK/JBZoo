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


$itemLink = $this->app->route->item($item);

$orderDetails = JBModelOrder::model()->getDetails($item);
?>
<tr class="table-row item-<?php echo $item->id; ?>">
    <td>
        <p><a href="<?php echo $itemLink; ?>"><?php echo JText::_('JBZOO_ORDER_NAME'); ?> #<?php echo $item->id; ?></p>
    </td>
    <td>
        <p class="date">
            <?php echo $this->renderPosition('date'); ?>
        </p>
    </td>
    <td>
        <p class="price">
            <?php echo $this->renderPosition('price'); ?>
        </p>
    </td>
    <td>
        <p class="payment-status">
            <?php echo $this->renderPosition('payment-status', array('style' => 'jbsimple')); ?>
        </p>
    </td>
</tr>
