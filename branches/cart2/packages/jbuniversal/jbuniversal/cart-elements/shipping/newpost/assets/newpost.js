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

    $.fn.JBCartShippingnewpost = function (options) {

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
            };

            if ($this.hasClass('shipping-init')) {
                return global;
            }

            var globCities = {},
                globWarehouses = {},
                proccessing = false;

            function getCitySelect() {
                return $('.jsNewPostSenderCity #shippingrecipientcity', $this);
            }

            function getRegion() {
                return $.trim($('#shippingregions option:selected', $this)
                    .val()
                    .toLowerCase());
            }

            function getCity() {
                return $.trim($('.jsNewPostSenderCity #shippingrecipientcity option:selected', $this)
                    .val()
                    .toLowerCase());
            }

            function setCities(cities) {

                var region = getRegion();

                if (typeof cities == 'object' && Object.keys(cities).length > 0) {
                    globCities[region] = cities;
                }
            }

            function setWarehouses(warehouses) {

                var city = getCity();

                if (Object.keys(warehouses).length > 0) {
                    globWarehouses[city] = warehouses;
                }
            }

            function getCities(region) {

                if (typeof region == 'undefined') {
                    region = getRegion();
                }

                var city = {};
                if (globCities.hasOwnProperty(region) === true) {
                    return globCities[region];
                }

                return city;
            }

            function getWarhouses(city) {

                if (typeof city == 'undefined') {
                    city = getCity();
                }

                var warehouses = {};
                if (globWarehouses.hasOwnProperty(city) === true) {
                    return globWarehouses[city];
                }

                return warehouses;
            }

            function clearCities() {
                $('#shippingrecipientcity option', $this).not(':first').remove();
            }

            function clearWarehouses() {
                $('#shippingstreet option', $this).not(':first').remove();
            }

            $this.changePostType = function (type) {

                if (!type) {
                    type = parseInt($('#shippingdeliverytype_id option:selected', $this).val(), 10);
                }

                if (type === settings.toDoors) {
                    $this.showBlockDoors();

                } else if (type === settings.toWrn) {
                    $this.showBlockWarehouse();

                }
            };

            $this.showBlockDoors = function () {

                $('.jsAreaWarehouse', $this).slideUp(function () {
                    $('input, select', $(this)).attr('disabled', 'disabled');
                });

                $('.jsAreaDoors', $this)
                    .slideDown()
                    .find('input, select')
                    .removeAttr('disabled');
            };

            $this.showBlockWarehouse = function () {

                $('.jsAreaDoors', $this).slideUp(function () {
                    $('input, select', $(this)).attr('disabled', 'disabled');
                });

                $('.jsAreaWarehouse', $this)
                    .slideDown()
                    .find('input, select')
                    .removeAttr('disabled');
            };

            $this.addLoading = function () {
                var $select = getCitySelect(),
                    $wrhSelect = $('.jsNewPostWareehouse #shippingstreet', $this);

                $select.addClass('loading');
                $wrhSelect.addClass('loading');
            };

            $this.removeLoading = function () {
                var $select = getCitySelect(),
                    $wrhSelect = $('.jsNewPostWareehouse #shippingstreet', $this);

                $select.removeClass('loading');
                $wrhSelect.removeClass('loading');
            };

            $this.setCities = function (region, callback) {

                proccessing = true;
                if (typeof region == 'undefined') {
                    region = getRegion();
                }

                if (Object.keys(getCities(region)).length > 0 && callback) {
                    callback();
                    proccessing = false;
                    return false;
                }
                $this.addLoading();

                JBZoo.ajax({
                    'url'     : settings.getCitiesUrl,
                    'data'    : {
                        "args": {
                            'region': region
                        }
                    },
                    'dataType': 'json',
                    'success' : function (cities) {
                        setCities(cities.cities);

                        if (callback) {
                            callback();
                        }

                        $this.removeLoading();
                        proccessing = false;

                    },
                    'error'   : function (error) {
                        $this.removeLoading();
                        proccessing = false;
                    }
                });
            };

            $this.addCities = function (region) {

                if (typeof region == 'undefined') {
                    region = getRegion();
                }

                var cities = getCities(region);
                var $select = getCitySelect();

                clearCities();
                if (typeof cities != 'undefined') {
                    $.each(cities, function (key, value) {
                        $select.append($("<option/>", {
                            value: key,
                            text : value
                        }));
                    });
                }

                $select
                    .trigger('liszt:updated')
                    .trigger("chosen:updated");
            };

            $this.setWarehouses = function (city, callback) {

                if (!city) {
                    city = getCity();
                }

                if (Object.keys(getWarhouses(city)).length > 0 && callback) {
                    callback();
                    proccessing = false;
                    return false;
                }

                var $select = $('.jsNewPostWareehouse #shippingstreet', $this);
                $select.addClass('loading');

                JBZoo.ajax({
                    'url'     : settings.getWarehousesUrl,
                    'data'    : {
                        "args": {
                            'city': city
                        }
                    },
                    'dataType': 'json',
                    'success' : function (warehouses) {
                        setWarehouses(warehouses.warehouses);

                        if (callback) {
                            callback();
                        }

                        $select.removeClass('loading');
                        proccessing = false;
                    },
                    'error'   : function (error) {
                        $select.removeClass('loading');
                        proccessing = false;
                    }
                });
            };

            $this.addWarehouses = function (city) {

                if (!city) {
                    city = getCity();
                }

                var warehouses = getWarhouses(city),
                    $select = $('.jsNewPostWareehouse #shippingstreet', $this);

                clearWarehouses();
                $.each(warehouses, function (key, value) {
                    $select.append($("<option/>", {
                        value: key,
                        text : value
                    }));
                });

                $select
                    .trigger('liszt:updated')
                    .trigger("chosen:updated");
            };

            $('#shippingdeliverytype_id', $this).on('change', function () {

                $this.changePostType();
            });

            $('.jsCalculate select', $this).on('change', function () {

                global.getPrice();
            });

            $('#shippingregions', $this).on('change', function () {

                clearWarehouses();
                $this.setCities(getRegion(), $this['addCities']);
            });

            $('#shippingrecipientcity', $this).on('change', function () {

                $this.setWarehouses(getCity(), $this['addWarehouses']);
            });

            $this.changePostType();
            $this.addClass('shipping-init');
        });

        return global;
    };

})(jQuery, window, document);