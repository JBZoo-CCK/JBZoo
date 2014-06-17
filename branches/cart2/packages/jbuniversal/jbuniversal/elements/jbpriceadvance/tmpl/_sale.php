<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


if ($discount['value'] < 0): ?>
    <?php if ($mode == ElementJBPriceAdvance::SALE_VIEW_ICON_SIMPLE) : ?>
        <span class="sale-icon-simple"> </span>
    <?php endif; ?>

    <?php if ($mode == ElementJBPriceAdvance::SALE_VIEW_ICON_VALUE) : ?>
        <span class="sale-icon-empty"><?php echo $discount['format']; ?></span>
    <?php endif; ?>

    <?php if ($mode == ElementJBPriceAdvance::SALE_VIEW_TEXT_SIMPLE) : ?>
        <span class="jsSave save discount-less"><?php echo $base['save']; ?></span>
        (<span class="discount"><?php echo $discount['format']; ?></span>)
    <?php endif; ?>
<?php endif; ?>

