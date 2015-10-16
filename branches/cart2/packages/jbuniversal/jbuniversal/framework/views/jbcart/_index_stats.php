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


if ($this->count) : ?>
    <h2><?php echo JText::_('JBZOO_ADMIN_INDEX_SHOP_STATS'); ?></h2>
    <p>
        <?php echo JText::_('JBZOO_ADMIN_INDEX_ORDERS_SUM'); ?>: <?php echo $this->summ->html(); ?><br />
        <?php echo JText::_('JBZOO_ADMIN_INDEX_ORDERS_COUNT'); ?>: <?php echo $this->count; ?>
    </p>
<?php endif; ?>
