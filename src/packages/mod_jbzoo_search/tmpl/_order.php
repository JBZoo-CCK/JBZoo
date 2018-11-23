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

$orderList = $modHelper->getOrderList();
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
