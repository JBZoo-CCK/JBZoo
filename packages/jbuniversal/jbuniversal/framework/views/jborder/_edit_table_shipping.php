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

if (!$shipping) {
    return;
}

$this->sum->addModify($shipping); ?>
<tr>
    <td class="noborder-btm"></td>
    <th>
        <p><?php echo JText::_('JBZOO_ORDER_SHIPPING_FEE'); ?></p>
    </th>
    <td>
        <?php echo $shipping->getName(); ?>
        <em>(
            <?php
            if ($shipping->isModify()) {
                echo JText::_('JBZOO_ORDER_SHIPPING_INCLUDED');
            } else {
                echo JText::_('JBZOO_ORDER_SHIPPING_NOT_INCLUDED');
            }
            ?>
            )</em>
        <?php
        if ($shipping->isFree()) {
            $priceCost = $shipping->getOrder()->val($shipping->config->get('limit_for_free'));
            echo '<br />' . JText::sprintf('JBZOO_ORDER_SHIPPING_IF_FREE', $priceCost->html());
        }
        ?>
    </td>
    <td class="align-right">
        <?php echo $shipping->getRate()->htmlAdv($currency, true); ?>
    </td>
    <td class="align-right">
        <?php echo $this->sum->html(); ?>
    </td>
</tr>