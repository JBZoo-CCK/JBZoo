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

if ($this->count) : ?>
    <h2><?php echo JText::_('JBZOO_ADMIN_INDEX_SHOP_STATS'); ?></h2>
    <p>
        <?php echo JText::_('JBZOO_ADMIN_INDEX_ORDERS_SUM'); ?>: <?php echo $this->summ->html(); ?><br />
        <?php echo JText::_('JBZOO_ADMIN_INDEX_ORDERS_COUNT'); ?>: <?php echo $this->count; ?>
    </p>
<?php endif; ?>
