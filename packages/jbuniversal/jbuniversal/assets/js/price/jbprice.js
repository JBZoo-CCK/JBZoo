/**
 * JBZoo App is universal Joomla CCK, application for YooTheme Zoo component
 *
 * @package     jbzoo
 * @version     2.x Pro
 * @author      JBZoo App http://jbzoo.com
 * @copyright   Copyright (C) JBZoo.com,  All rights reserved.
 * @license     http://jbzoo.com/license-pro.php JBZoo Licence
 */

;
(function ($, window, document, undefined) {

    /**
     * JBZoo Price (depricated!)
     * @depricated
     * @param options
     * @returns {*}
     * @constructor
     */
    $.fn.JBZooPrice = function (options) {

        var $this = $(this);

        return $this.each(function (n, obj) {

            var $obj = $(obj);

            $(".jsPriceCurrency", $obj).click(function () {
                var $cur = $(this),
                    currency = $cur.attr("currency");

                $(".jsPriceValue", $obj).removeClass("active");
                $(".price-currency-" + currency, $obj).addClass("active");

                $(".jsPriceCurrency", $obj).removeClass("active");
                $cur.addClass("active");
            });

            $(".jsAddToCart", $obj).click(function () {
                var $link = $(this),
                    href = $link.data('href'),
                    params = 'format=raw&tmpl=component';

                // force added params (sef bug)
                if (href.indexOf('?') == -1) {
                    href += '?' + params;
                } else {
                    href += '&' + params;
                }

                $.fancybox({
                    'type'      : 'iframe',
                    'href'      : href,
                    'width'     : 360,
                    'autoHeight': true,
                    'autoResize': true,
                    'fitToView' : true,
                    'iframe'    : {
                        'scrolling': 'no',
                        'preload'  : true
                    },
                    'helpers'   : {
                        'overlay': {
                            'locked': false,
                            'css'   : {
                                'background': 'rgba(119, 119, 119, 0.4)'
                            }
                        }
                    }
                });

                return false;
            });

            // order in one click
            $('.jsBayIt', $obj).click(function () {

                var $link = $(this),
                    indexPrice = 0;

                if ($('.jbprice-row input:checked', $obj).length) {
                    indexPrice = $('.jbprice-row input:checked', $obj).val();
                }

                JBZoo.ajax({
                    'url'    : $link.data('href'),
                    'data'   : {
                        "args": {
                            'quantity'  : $('.jsQuantity').val(),
                            'indexPrice': indexPrice
                        }
                    },
                    'success': function (data) {
                        if (data.result) {
                            window.location.href = data.basketUrl;
                        }
                    }
                });

                return false;
            });

            $(".jsRemoveFromCart", $obj).click(function () {
                var $link = $(this);

                JBZoo.ajax({
                    'url'    : $link.data("href"),
                    'success': function (data) {
                        $obj.removeClass('in-cart').addClass('not-in-cart');
                        $.fn.JBZooPriceReloadBasket();
                        $('.jsJBZooCartModule').JBZooCartModule().JBZooCartModule('reload');
                    }
                });

                return false;
            });
        });
    };

})(jQuery, window, document);