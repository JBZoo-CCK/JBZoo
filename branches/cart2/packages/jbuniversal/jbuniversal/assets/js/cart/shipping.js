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

    $.fn.JBCartShipping = function (settings) {

        var options = $.extend({}, {
            'no_value_message': 'Free'
        }, settings);

        var byDefault = 'default',
            plugins = [],
            $this = $(this),
            create = false;

        $this.getParams = function () {

            var params = {};

            for (var name in plugins) {

                var plg = plugins[name];

                if (Object.keys(plg).length > 0) {

                    var input = $('.jsInputShippingService', plg),
                        identifier = input.val(),
                        options = $('.jsCalculate', plg),
                        values = {};

                    $('input, select', options).each(function () {

                        var field = $(this);

                        if (typeof field.attr('name') != 'undefined') {

                            var nameOf = field.attr('name'),
                                value = $.trim(field.val());

                            if (value.length > 0) {
                                nameOf = nameOf.replace(/shipping(?:[\[])(\w+)(?:[\]])/, "$1");

                                values[nameOf] = field.val();
                            }
                        }
                    });

                    if (Object.keys(values).length > 0) {
                        params[identifier] = values;
                    }
                }
            }

            return $.param({'shipping': params});
        };

        $this.recount = function () {

            for (var name in plugins) {
                plugins[name].getPrice();
            }

            return $this.getPrice();
        };

        $this.createPlugins = function () {

            if (create === false) {
                $('.jsShippingElement', $this).each(function () {

                    if ($(this).length > 0) {
                        $('select', $(this)).chosen();
                        $this.createPlugin($(this));
                        create = true;
                    }
                });
            }

            return plugins;
        };

        $this.getPrice = function () {

            for (var name in plugins) {

                var plg = plugins[name];

                if ($('.jsInputShippingService:checked', plg).length > 0) {

                }
            }
        };

        $this.setPrices = function (plgs) {

            if (Object.keys(plgs).length > 0) {
                for (var id in plgs) {
                    var input = $('.jsInputShippingService[value="' + id + '"]', $('.jbzoo .shipping-list')),
                        type = input.parents('.jsShippingElement').data('type'),
                        label = input.next();

                    if (input.is(':checked')) {
                        $this.setPrice(plgs[id]);
                    }

                    return; // TODO FIX ME
                    plugins[type].price = plgs[id].price;
                    $('.shipping-info .value .jsValue', label).html(plgs[id].price);
                    $('.shipping-info .value .jsCurrency', label).html(plgs[id].symbol);
                }
            }
        };

        $this.setPrice = function (price) {

            if (price == 'undefined') {
                price = options.no_value_message;
            }

            $('.jsShippingPrice .jsValue', $this.parents('.jbzoo')).html(price.price);
            $('.jsShippingPrice .jsCurrency', $this.parents('.jbzoo')).html(price.symbol);
        };

        var toggleShipFields = function (shipFields) {

            var shippingBlock = $this.parents('.jbzoo').find('.shippingfileds-list');
            shippingBlock.addClass('loading');

            shippingBlock.show();
            if (shipFields.indexOf(':') > 0) {

                var fields = shipFields.split(':'),
                    classes = '.element-' + fields.join(', .element-');

                $(classes, shippingBlock).fadeIn()
                    .find('input, select, textarea')
                    .removeAttr('disabled');

                $('div.element:not(' + classes + ')', shippingBlock)
                    .fadeOut(function () {

                        $(this)
                            .find('input, select')
                            .attr('disabled', 'disabled');
                    });

            } else if (shipFields.length > 0) {

                $('.element-' + shipFields, shippingBlock)
                    .fadeIn()
                    .find('input, select, textarea')
                    .removeAttr('disabled');

                $('div.element', shippingBlock).not('.element-' + shipFields)
                    .fadeOut(function () {

                        $(this)
                            .find('input, select, textarea')
                            .attr('disabled', 'disabled');
                    });

            } else {

                shippingBlock.hide();
            }

            setTimeout(function () {
                shippingBlock.removeClass('loading');
            }, 500);
        };

        $('.jsInputShippingService', $this).on('change', function () {

            var $element = $(this).parents('.jsShippingElement');
            var plg = plugins[$element.data('type')];

            $this.setPrice({'price': plg.price, 'symbol': plg.symbol});

            $element.addClass('active');
            $element.siblings('.element').removeClass('active');

            $this.toggleShipFields($element);
            $this.hide();
            $this.show($element);

        });

        $this.hide = function () {

            $('.jsMoreOptions', $this).slideUp('fast', function () {
                //$('input, select', $(this)).attr('disabled', 'disabled');
            });

            /*$('.jsMoreOptions', $this).animate({
             opacity: 0
             }, 1000, function () {

             });*/
        };

        $this.show = function ($element) {

            /* $('.jsMoreOptions', $element).animate({
             opacity: 1
             }, 1000, function () {

             });*/

            $('.jsMoreOptions', $element).slideDown('fast');
            $('.jsMoreOptions input, .jsMoreOptions select', $element).removeAttr('disabled');
        };

        $this.toggleShipFields = function ($element) {

            var settings = $element.data('settings');

            if (settings) {
                var shipFields = settings.shippingfields;
            }

            if (typeof shipFields != 'undefined') {
                toggleShipFields(shipFields);
            }

        };

        $this.createPlugin = function ($element) {

            var name = $element.data('type'),
                plugin = null;
            if (typeof name == 'undefined') {
                name = byDefault;
            }

            var plugName = $.trim('JBCartShipping' + name.toLowerCase());

            if (typeof plugins[$element.data('type')] != 'undefined' && plugins[$element.data('type')].length !== 0) {
                return plugins[$element.data('type')];
            }

            if ($.isFunction($.fn[plugName])) {

                plugin = $element[plugName]({
                    super: $this
                });

                plugins[$element.data('type')] = plugin;
            } else {

                plugName = $.trim('JBCartShipping' + byDefault);
                plugin = $element[plugName]({
                    super: $this
                });

                plugins[$element.data('type')] = plugin;
            }

            return plugin;
        };

        var $element =
                $('.jsInputShippingService:checked', $this).parents('.jsShippingElement');

        $element.addClass('active');
        $element.siblings('.element').removeClass('active');

        $this.createPlugins();
        if ($element.length > 0) {
            $this.toggleShipFields($element);
        }
        $this.hide();
        $this.show($element);

        return $this;
    };

})(jQuery, window, document);