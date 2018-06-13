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

    <p class="jbcart-title"><?php echo JText::_('JBZOO_CART_SHIPPING_TITLE'); ?></p>

    <div class="jsJBCartShipping">
        <?php echo $this->renderPosition(JBCart::DEFAULT_POSITION, array('style' => 'order.shipping')); ?>
    </div>

    <?php echo $this->app->jbassets->widget('.jsJBCartShipping', 'JBZooShipping', array(
        'fields_assign' => $this->app->jbshipping->getConfigAssign(),
        'url_shipping'  => $this->app->jbrouter->basketShipping(),
    ), true); ?>

<?php endif;
