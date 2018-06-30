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

if ($this->checkPosition(JBCart::DEFAULT_POSITION)) : ?>

    <p class="jbcart-title jbcart-title-main"><?php echo JText::_('JBZOO_CART_CREATE_ORDER_TITLE'); ?></p>

    <div class="jbcart-form clearfix">
        <?php echo $this->renderPosition(JBCart::DEFAULT_POSITION, array('style' => 'order.block')); ?>
    </div>

<?php endif;
