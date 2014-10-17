(function ($) {
    /**
     * JBZoo JBPrice Advance
     * @param options
     * @returns {*|jQuery}
     */
    $.fn.JBZooPriceAdvance = function (options) {

        var options = $.extend({}, {
            'mainHash'         : '',
            'itemId'           : 0,
            'identifier'       : '',
            'relatedImage'     : '',
            'mainImage'        : '',
            'popup'            : 0,
            'prices'           : {},
            'default_variant'  : {},
            'addToCartUrl'     : '',
            'removeFromCartUrl': '',
            'changeVariantUrl' : '',
            'basketUrl'        : '',
            'modalUrl'         : '',
            'isInCart'         : 0
        }, options);

        options.params = $.extend({}, {
            'startValue'     : 1,
            'multipleValue'  : 1,
            'currencyDefault': 'EUR',
            'advFieldText'   : 0,
            'advAllExistShow': 0
        }, options.params);

        return $(this).each(function () {

            var $obj = $(this);

            if ($obj.hasClass('jbprice-adv-inited')) {
                return $obj;
            }

            $obj.addClass('jbprice-adv-inited');

            var AjaxProcess = false,
                currency = options.params.currencyDefault,
                prices = {},
                $paramIMG = $('.jsImageRelated', $obj),
                JBImage = $obj.parents('.jbzoo').find('.' + $paramIMG.data('element')),
                JBImageTemplate = JBImage.data('template');

            if (!options.prices.image) {
                options.prices.image = JBImage.attr('src');
            }
            prices[options.mainHash] = options.prices;

            options.relatedImage = $paramIMG.data('element');

            if ($('.jsCurrencyList', $obj).length !== 0) {
                currency = $('.jsCurrencyList', $obj).data('default');

            }

            function getPrices(newCurrency) {

                var hash = getCurrentHash(),
                    values = getValues();

                if (typeof prices[hash] != 'undefined') {

                    AjaxProcess = false;
                    toggle(prices, newCurrency);

                } else {
                    JBZoo.ajax({
                        'url'    : options.changeVariantUrl,
                        'data'   : {
                            'args': {
                                'values': values
                            }
                        },
                        'success': function (data) {

                            AjaxProcess = false;

                            if (typeof data != 'undefined') {
                                prices[hash] = data;
                            } else {
                                prices[hash] = prices[options.mainHash];
                            }
                            toggle(prices, newCurrency);
                        },
                        'error'  : function (data) {
                            AjaxProcess = false;

                            if (data.result == false) {
                                prices[hash] = prices[options.mainHash];
                            }

                            toggle(prices, newCurrency);
                        }
                    });
                }
            }

            function togglePrices(newCurrency) {

                if (AjaxProcess) {
                    return false;
                }
                AjaxProcess = true;

                getPrices(newCurrency);
                currency = newCurrency;
            }

            function toggle(prices, newCurrency) {

                var hash = getCurrentHash();
                newCurrency = newCurrency.toLowerCase();
                var values = prices[options.mainHash][newCurrency],
                    description = '';

                //TODO optimize code
                if (typeof prices[hash] != 'undefined') {

                    values = prices[hash][newCurrency];
                    description = $.trim(prices[hash].description);

                    if (options.params.advAllExistShow == 0) {
                        $('.jbprice-buttons', $obj).removeClass('disabled');
                    }
                } else {
                    values = prices[options.mainHash][newCurrency];

                    if (options.params.advAllExistShow == 0) {
                        $('.jbprice-buttons', $obj).addClass('disabled');
                    }

                }

                if (typeof values != 'undefined') {

                    $('.not-paid-box', $obj).show();
                    if (values.totalNoFormat == 0) {
                        $('.not-paid-box', $obj).hide();
                    }

                    $('.jsPrice', $obj).html('&nbsp;' + values.price + '&nbsp;');
                    $('.jbcurrency-' + newCurrency.toLowerCase(), $obj).addClass('active');

                    $('.jsDescription', $obj).replaceWith(description);

                    if (prices[hash].sku != null) {
                        $('.jbprice-sku', $obj).replaceWith(prices[hash].sku);
                    }

                    if (prices[hash].balance != null) {
                        $('.jsJBPriceBalance', $obj).replaceWith(prices[hash].balance);
                    }

                    if(prices[hash].value != null) {
                        $('.jbPriceElementValue', $obj).replaceWith(prices[hash].value);
                    }

                    $('.jsSave', $obj).text(values.save);
                    $('.jsTotal', $obj).text(values.total);
                    if (typeof prices[hash] != 'undefined') {

                        if (prices[hash].image) {
                            var item = $obj.parents('.jbzoo');
                            var $relatedImg = $('.' + options.relatedImage, item);

                            if (prices[hash].image != $relatedImg.attr('src')) {

                                $relatedImg.fadeOut('10', function () {
                                    $(this).attr('src', prices[hash].image).fadeIn();
                                });
                            }

                            if (prices[hash].pop_up) {
                                $relatedImg.attr('href', prices[hash].pop_up).fadeIn('600');
                            }
                        }
                    }
                }

            }

            function bindDataIndex() {
                $('.jbpriceParams', $obj).each(function (n) {
                    n++;
                    var $param = $(this);
                    $param.attr('data-index', n);
                })
            }

            /**
             * Add item to cart
             * @param callback
             */
            function addToCart(callback) {

                var count = options.params.startValue;
                if ($('.jsCount', $obj).length) {
                    count = $('.jsCount', $obj).val();
                }

                JBZoo.ajax({
                    'url'    : options.addToCartUrl,
                    'data'   : {
                        "args": {
                            'quantity': count,
                            'values'  : getValues()
                        }
                    },
                    'success': function (data) {

                        $('.jbzoo-button', $obj).removeClass('loading').removeAttr('disabled');
                        if ($.isFunction(callback)) {
                            callback(data);
                        }
                    },
                    'error'  : function (data) {
                        $('.jbzoo-button', $obj).removeClass('loading').removeAttr('disabled');
                        if (data.message) {
                            alert(data.message);
                        }
                    }
                });
            }

            function removeFromCart() {
                JBZoo.ajax({
                    'url'    : options.removeFromCartUrl,
                    'data'   : {
                        'args': {
                            'values': getValues()
                        }
                    },
                    'success': function (data) {
                        $obj.removeClass('in-cart').addClass('not-in-cart');
                        $.fn.JBZooPriceReloadBasket();
                    }
                });
            }

            /**
             * Build hash to string
             * @param hash
             * @returns {string}
             */
            function buildHash(hash) {
                var result = [];

                for (var key in hash) {
                    var val = hash[key];
                    result.push(key + val);
                }

                return result.join('_');
            }

            function buildValue(value) {
                var result = [];

                for (var key in value) {
                    var val = value[key];
                    result.push(val);
                }

                return result.join('-');
            }

            function getCurrentValues() {

                var data = {};

                $('.jbpriceParams', $obj).each(function (n, row) {
                    var $param = $(row),
                        type = $param.data('type'),
                        index = $param.data('index');

                    if (type == 'radio') {
                        var radio = $('input[type="radio"]:checked', $param);


                        if ($.trim(radio.val()).length) {
                            data['p' + index + '-'] = $.trim(radio.val());
                        }

                    }

                    if (type == 'select') {
                        var select = $('select.jsParam', $param);

                        if ($.trim(select.val()).length) {
                            data['p' + index + '-'] = $.trim(select.val());
                        }
                    }

                    if (type == 'checkbox') {
                        var checkbox = {};

                        $('input[type="checkbox"]:checked', $param).each(function (n) {
                            var $checkbox = $(this);

                            if ($.trim($checkbox.val()).length) {
                                checkbox[n] = $checkbox.val();
                            }
                        });

                        if (Object.keys(checkbox).length) {
                            data['p' + index + '-'] = $.trim(buildValue(checkbox));
                        }
                    }

                });

                return data;
            }

            function getValues() {
                var data = {};

                var i = 0;
                $('.jbpriceParams', $obj).each(function (n, row) {
                    var $param = $(row),
                        type = $param.data('type');

                    if (type == 'radio') {
                        var radio = $('input[type="radio"]:checked', $param);

                        if (radio.is(':checked') && radio.val().length > 0) {
                            var value = {};

                            if (!$param.hasClass('jbprice-param-colors')) {
                                value['value'] = radio.val();
                                data[radio.data('identifier')] = value;
                            } else {
                                value['value'] = radio.val();
                                data[radio.data('identifier')] = value;
                                i++;
                            }
                        }
                    }

                    if (type == 'select') {
                        var select = $('select.jsParam', $param),
                            value = {};

                        if ($.trim(select.val()).length) {
                            value['value'] = $.trim(select.val());
                            data[select.data('identifier')] = value;
                        }
                    }

                    if (type == 'checkbox') {
                        var checkbox = [];
                        $('input[type="checkbox"]', $param).each(function () {
                            var $checkbox = $(this);

                            if ($checkbox.is(':checked') && $checkbox.val().length > 0) {
                                checkbox.push($checkbox.val());
                            }

                            if (checkbox.length) {
                                data[$checkbox.data('identifier')] = checkbox;
                            }
                        });
                    }

                });

                return data;
            }

            /**
             * Get current hash for price
             * @returns {string}
             */
            function getCurrentHash() {

                var newHash = getCurrentValues(),
                    result = buildHash(newHash);

                if (result == (['p1-', 'p2-', 'p3-', 'd-']).join('_') ||
                    result == (['p1-', 'p2-', 'p3-']).join('_')) {
                    return options.mainHash;
                }

                result = options.mainHash + '-' + buildHash(newHash);
                if (buildHash(newHash).length == 0) {
                    result = options.mainHash;
                }

                return result;
            }

            var quantity = $('.jbprice-quantity', $obj);

            $('.jsCount', $obj).JBZooQuantity({
                "step"    : parseFloat(quantity.data('step')),
                "default" : parseFloat(quantity.data('default')),
                "decimals": parseFloat(quantity.data('decimals')),
                "min"     : parseFloat(quantity.data('min')),
                "scroll"  : true
            });
            // currency list
            $(".jsPriceCurrency", $obj).on('click', function () {
                var $cur = $(this),
                    $parent = $(this).parent(),
                    currency = $cur.data('currency');

                $parent.addClass('jbprice-lock');

                $(".jsPriceValue", $obj).removeClass('active');
                $(".jbprice-" + currency, $obj).addClass('active');
                $(".jsPriceCurrency", $obj).removeClass('active');
                $cur.addClass("active");
                togglePrices(currency);
            });

            $('.jsParam', $obj).on('change', function () {
                togglePrices(currency);
            });

            $('.jsParamDesc', $obj).on('change', function () {
                togglePrices(currency);
            });

            $('.jbpriceParams input', $obj).on('change', function () {
                togglePrices(currency);
            });

            $(".jsAddToCart", $obj).click(function () {

                var button = $(this);
                button.addClass('loading').attr('disabled', 'disabled');
                addToCart(function (data) {
                    if (data) {
                        $.fn.JBZooPriceToggle(options.identifier, options.itemId);
                    }
                });
                return false;
            });

            $(".jsRemoveFromCart", $obj).click(function () {
                removeFromCart();
                return false;
            });

            $(".jsAddToCartModal", $obj).click(function () {
                var $link = $(this),
                    href = options.modalUrl,
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
                    'width'     : 400,
                    'fitToView' : true,
                    'autoHeight': true,
                    'autoResize': true,

                    'iframe' : {
                        'scrolling': 'no',

                        'preload': true
                    },
                    'helpers': {
                        'overlay': {
                            'locked': false,

                            'css': {
                                'background': 'rgba(119, 119, 119, 0.4)'
                            }
                        }
                    }
                });

                return false;
            });

            $('.jsCartModal .jsAddToCart').unbind().click(function () {
                addToCart(function (data) {
                    if (data) {
                        parent.jQuery.fn.JBZooPriceToggle(options.identifier, options.itemId);

                        if (typeof parent.jQuery.fancybox != 'undefined') {
                            parent.jQuery.fancybox.close();
                        }

                    }
                });

                return false;
            });

            $('.jsAddToCartOne', $obj).click(function () {
                addToCart(function (data) {
                    if (data) {
                        $.fn.JBZooPriceToggle(options.identifier, options.itemId);
                        $.fn.JBZooPriceReloadBasket();
                    }
                });

                return false;
            });

            $('.jsAddToCartGoto', $obj).click(function () {

                var button = $(this);
                button.addClass('loading').attr('disabled', 'disabled');

                addToCart(function (data) {
                    if (data) {
                        if (options.basketUrl) {
                            parent.location.href = options.basketUrl;
                        }

                    }
                });

                return false;
            });

            // init
            (function () {
                $obj.addClass(options.isInCart ? 'in-cart' : 'not-in-cart');
                bindDataIndex();
                $(".jbcurrency-" + currency, $obj).addClass('active');

                if (options.default_variant.length > 0) {
                    prices[getCurrentHash()] = options.default_variant;
                }

                togglePrices(currency);

            }());
        });
    };

})(jQuery);