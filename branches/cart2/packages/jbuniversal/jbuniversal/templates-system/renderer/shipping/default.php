<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
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
