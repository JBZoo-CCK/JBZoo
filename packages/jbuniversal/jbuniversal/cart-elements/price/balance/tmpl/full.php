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

$value      = $this->getValue();
$isUseStock = $this->_isUseStock();

?>

<!--noindex-->
<?php if ($value == JBCartElementPriceBalance::COUNT_AVAILABLE_NO) : ?>
    <span class="jbprice-balance-available-no">
        <?php echo JText::_('JBZOO_ELEMENT_PRICE_BALANCE_AVAILABLE_NO'); ?>
    </span>

<?php elseif (!$isUseStock || $value > 0) : ?>
    <span class="jbprice-balance-left">
        <?php echo JText::sprintf('JBZOO_ELEMENT_PRICE_BALANCE_LEFT', $value); ?>
    </span>

<?php elseif ($value == JBCartElementPriceBalance::COUNT_AVAILABLE_YES || $value > 0) : ?>
    <span class="jbprice-balance-available-yes">
        <?php echo JText::_('JBZOO_ELEMENT_PRICE_BALANCE_AVAILABLE_YES'); ?>
    </span>

<?php elseif ($value == JBCartElementPriceBalance::COUNT_REQUEST) : ?>
    <span class="jbprice-balance-request">
        <?php echo JText::_('JBZOO_ELEMENT_PRICE_BALANCE_REQUEST'); ?>
    </span>

<?php endif; ?>
<!--/noindex-->
