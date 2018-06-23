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

$curList = $modHelper->getCurrencyList();
?>

<?php if (!empty($curList['list'])) : ?>
    <div class="jbzoo jsNoCurrencyToggle">

        <?php if (count($curList['list']) > 1) : ?>

            <p><?php echo $curList['orig']->html(); ?><?php echo JText::_('JBZOO_MODULE_CURRENCY_OTHERS'); ?></p>

            <table class="jbcurrency-list no-border">
                <?php foreach ($curList['list'] as $code => $currency) : ?>
                    <tr class="jbcurrency-<?php echo $code; ?>">
                        <td class="jbcurrency-from"><?php echo $currency['name']; ?></td>
                        <td class="jbcurrency-devider">. . .</td>
                        <td class="jbcurrency-to"><?php echo $currency['to']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php else: ?>

            <table class="jbcurrency-list no-border">
                <?php foreach ($curList['list'] as $code => $currency) : ?>
                    <tr class="jbcurrency-<?php echo $code; ?>">
                        <td class="jbcurrency-from"><?php echo $currency['from']; ?></td>
                        <td class="jbcurrency-devider">. . .</td>
                        <td class="jbcurrency-to"><?php echo $currency['to']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php endif; ?>
    </div>
<?php endif; ?>
