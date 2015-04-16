<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$orderList     = $modHelper->getOrderList();
$orderingsHTML = $modHelper->getOrderings();

?>

<?php if ((int)$params->get('order_show', 1) && !empty($orderList)) : ?>
    <div class="jbfilter-row jbfilter-order">
        <label for="jbfilter-id-order" class="jbfilter-label">
            <?php echo JText::_('JBZOO_ORDER'); ?>
        </label>

        <div class="jbfilter-element">
            <?php echo $orderingsHTML; ?>
        </div>
        <?php echo JBZOO_CLR; ?>
    </div>
<?php else :
    echo $orderingsHTML;
endif;
