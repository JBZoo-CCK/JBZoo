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

if (!$payment) {
    return;
}

$this->sum->addModify($payment); ?>
<tr>
    <td class="noborder-btm"></td>
    <th>
        <p><?php echo JText::_('JBZOO_ORDER_PAYMENT_FEE'); ?></p>
    </th>
    <td>
        <?php echo $payment->getName(); ?>
        <em>(
            <?php
            if ($payment->isModify()) {
                echo JText::_('JBZOO_ORDER_PAYMENT_INCLUDED');
            } else {
                echo JText::_('JBZOO_ORDER_PAYMENT_NOT_INCLUDED');
            }
            ?>
        )</em>
    </td>
    <td class="align-right">
        <?php echo $payment->getRate()->htmlAdv($currency, true); ?>
    </td>
    <td class="align-right">
        <?php echo $this->sum->html(); ?>
    </td>
</tr>