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

    $.fn.JBCartShippingemspost = function (options) {

        var settings = $.extend({
                'super'  : {},
                'toDoors': 3,
                'toWrn'  : 4
            }, options, $(this).data('settings')),
            global = $(this);

        $(this).each(function () {

            var $this = $(this);

            global.price = settings.default_price;
            global.symbol = settings.symbol;
            global.getPrice = function (to) {

                if (typeof to == 'undefined' || to.length === 0) {
                    to = $('select option:selected', $this).val();
                }

                var result = {to: to};
                global.addClass('loading');
                JBZoo.ajax({
                    'url'     : settings.getPriceUrl,
                    'data'    : {
                        "args": {
                            'to': JSON.stringify(result)
                        }
                    },
                    'dataType': 'json',
                    'success' : function (price) {

                        settings.super.setPrice(price);
                        global.price = price.price;
                        $('.shipping-element .field-label .value .jsValue', $this).html(price.price);
                        $('.shipping-element .field-label .value .jsCurrency', $this).html(price.symbol);
                        global.removeClass('loading');
                    },
                    'error'   : function (error) {
                        global.removeClass('loading');
                    }
                });
            };

            if ($this.hasClass('shipping-init')) {
                return global;
            }

            $('#shippingto', $this).on('change', function () {

                var value = $(this).val();
                $('#shippingcountryto', $this).attr('disabled', 'disabled');

                if (value.length === 0) {
                    $('#shippingcountryto', $this).removeAttr('disabled');
                }

                $('#shippingcountryto', $this)
                    .trigger('liszt:updated')
                    .trigger("chosen:updated");
                global.getPrice(value);
            });

            $('#shippingcountryto', $this).on('change', function () {

                var value = $(this).val();
                $('#shippingto', $this).attr('disabled', 'disabled');

                if (value.length === 0) {
                    $('#shippingto', $this).removeAttr('disabled');
                }

                $('#shippingto', $this)
                    .trigger('liszt:updated')
                    .trigger("chosen:updated");
                global.getPrice(value);
            });

            $this.addClass('shipping-init');
        });

        return global;
    };
})(jQuery, window, document);