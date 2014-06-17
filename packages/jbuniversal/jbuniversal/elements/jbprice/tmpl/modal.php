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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title><?php echo JText::_('JBZOO_CART_ADD_TO_CART');?></title>
    <link rel="stylesheet" href="<?php echo $this->app->path->url('jbassets:css/jbzoo.css');?>" type="text/css"/>
    <script src="<?php echo $this->app->path->url('libraries:jquery/jquery.js');?>" type="text/javascript"></script>
    <script type="text/javascript">
        jQuery(function ($) {

            $('.jsCartModal .jsAddToCartButton').click(function () {

                var ajaxUrl = "<?php echo $addToCartUrl;?>";
                var requestParams = {
                    "args":{
                        'quantity':parseInt($('.jsQuantity').val(), 10),
                        'indexPrice':parseInt(jQuery('.jsPriceIndex:checked').val(), 10)
                    }
                };

                $.post(ajaxUrl, requestParams, function (data) {
                    parent.jQuery.fn.JBZooPriceToggle("<?php echo $this->identifier;?>", <?php echo (int)$this->getItem()->id;?>);
                    parent.jQuery.fancybox.close();
                }, "json");
            });

            $('.jsCartModal .jsAddToCartButtonGoto').click(function () {

                var ajaxUrl = "<?php echo $addToCartUrl;?>";
                var requestParams = {
                    "args":{
                        'quantity':parseInt($('.jsQuantity').val(), 10),
                        'indexPrice':parseInt(jQuery('.jsPriceIndex:checked').val(), 10)
                    }
                };

                $.post(ajaxUrl, requestParams, function (data) {
                    parent.location.href = "<?php echo $basketUrl;?>";
                }, "json");
            });

            $('.jsAddQuantity').click(function () {
                var quantity = parseInt($('.jsQuantity').val(), 10);
                quantity++;
                $('.jsQuantity').val(quantity);
                return false;
            });

            $('.jsRemoveQuantity').click(function () {
                var quantity = parseInt($('.jsQuantity').val(), 10);
                quantity--;
                if (quantity <= 0) {
                    quantity = 1;
                }
                $('.jsQuantity').val(quantity);
                return false;
            });

            $('.jsGoto').click(function () {
                var url = $(this).attr('href');
                parent.location.href = url;
                return false;
            });
        });
    </script>

</head>
<body class="jbcart-modal-body">
<div class="jbzoo">

    <div class="jbcart-modal-window jsCartModal">

        <h1>
            <a class="jsGoto"
               href="<?php echo $this->app->route->item($this->getItem());?>"><?php echo $this->getItem()->name;?></a>
        </h1>

        <p class="sku">
            <strong><?php echo JText::_('JBZOO_CART_ITEM_SKU');?></strong>:
            <?php echo $this->_getSku();?>
        </p>

        <div class="row">
            <?php
            if (!empty($values)) {

                if (count($values) > 1) {
                    echo '<strong>' . JText::_('JBZOO_CART_SELECT') . '</strong>';
                }

                foreach ($values as $key => $price) {

                    $value = $this->app->jbmoney->toFormat($price['value'], $currency);

                    echo '<div class="price-row"><label>';

                    echo '<input name="index" class="jsPriceIndex" type="radio" value="' . $key . '" ' . ($key == 0 ? 'checked = "checked"' : '') . ' /> ';

                    if (!($price['value'] == 0 && (int)$this->config->get('basket-nopaid', 0))) {
                        echo '<span class="price-value">' . $value . '</span><br/>';
                    }

                    if (isset($price['description']) && !empty($price['description'])) {
                        echo '<span class="price-description">' . $price['description'] . '</span>';
                    }

                    echo '</label></div>';
                }

            }
            ?>
            <div class="clear"></div>
        </div>

        <div class="row">
            <label for="jbzooprice-quantity">
                <span class="text-quantity"><?php echo JText::_('JBZOO_CART_QUANTITY');?></span>
                <a href="#minus" class="jsRemoveQuantity change-quantity-btn">-</a>
                <input type="text" id="jbzooprice-quantity" class="jsQuantity" value="1"/>
                <a href="#plus" class="jsAddQuantity change-quantity-btn">+</a>
            </label>

            <div class="clear"></div>
        </div>

        <div class="row row-center">
            <input type="button" value="<?php echo JText::_('JBZOO_CART_ADD_TO_CART');?>"
                   class="jsAddToCartButton add-to-cart-button"/>

            <?php if ($basketUrl) : ?>
            <input type="button" value="<?php echo JText::_('JBZOO_CART_ADD_TO_CART_GOTO_BASKET');?>"
                   class="jsAddToCartButtonGoto jbbuttom"/>
            <?php endif;?>

            <div class="clear"></div>
        </div>
    </div>
</div>
</body>
</html>
