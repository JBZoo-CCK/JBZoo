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

if ($mode == JBCartElementPriceDiscount::SALE_VIEW_TEXT_SIMPLE) :
    echo
    '<span class="jbprice-price">
         <span class="save discount-less">
             <span class="jsSave">'
                , $discount->html($currency),
            '</span>
            <span class="discount">('
            , $discount->percent($price)->html($currency),
            ')</span>
        </span>
    </span>';
endif;
