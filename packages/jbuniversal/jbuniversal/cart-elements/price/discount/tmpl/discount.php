<?php
/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 * @coder       Alexander Oganov <t_tapak@yahoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if ($mode == JBCartElementPriceDiscount::SALE_VIEW_ICON_SIMPLE) : ?>
    <span class="sale-icon-simple"> </span>
<?php endif;

if ($mode == JBCartElementPriceDiscount::SALE_VIEW_ICON_VALUE) : ?>
    <span class="sale-icon-empty"><?php echo $discount->html($currency); ?></span>
<?php endif;

if ($mode == JBCartElementPriceDiscount::SALE_VIEW_TEXT_SIMPLE) : ?>
    <span class="jsSave save discount-less"><?php echo $base['save']->html(); ?></span>
    (<span class="discount"><?php echo $discount->html($currency); ?></span>)
<?php endif;
