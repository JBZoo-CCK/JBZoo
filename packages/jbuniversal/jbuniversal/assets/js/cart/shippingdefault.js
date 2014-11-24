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

    $.fn.JBCartShippingdefault = function (options) {

        var settings = $.extend({
                'super': {}
            }, options, $(this).data('settings')),
            global = $(this);

        $(this).each(function () {

            var $this = $(this);

            global.price = settings.default_price;
            global.symbol = settings.symbol;

            global.getPrice = function () {

                var $fields = $('.jsCalculate input:not(input:disabled), ' +
                    '.jsCalculate select:not(select:disabled)', $this),
                    result = {};

                $fields.each(function () {

                    var $field = $(this), value = $.trim($field.val()), id = $field.attr('id');
                    if (value.length > 0) {
                        id = id.replace('shipping', '');
                        result[id] = value;
                    }
                });

                if (Object.keys(result).length > 0) {
                    global.addClass('loading');
                    JBZoo.ajax({
                        'url'     : settings.getPriceUrl,
                        'data'    : {
                            "args": {
                                'fields': JSON.stringify(result)
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
                }

            };

            if ($this.hasClass('shipping-init')) {
                return global;
            }

            $('.jsCalculate select, .jsCalculate input', $this).on('change', function () {

                global.getPrice($(this).val());
            });

            $this.addClass('shipping-init');
        });

        return global;
    };
})(jQuery, window, document);